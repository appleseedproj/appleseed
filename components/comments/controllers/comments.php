<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Comments
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Comments Component Controller
 * 
 * Comments Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Comments
 */
class cCommentsCommentsController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->_Current = $this->Talk ( 'User', 'Current' );
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$Context = $pData['Context'];
		$Context_FK = $pData['Id'];
		
		if ( ( !$Context ) || ( !$Context_FK ) ) {
			// Display error loading comments
			$this->View = $this->GetView ( 'error' );
			$this->View->Display ( );
			
			return ( true );
		}
		
		$this->View = $this->GetView ( );
		
		$this->Model = $this->GetModel ( );
		
		$this->Comments = $this->Model->Load ( $Context, $Context_FK );
		$count = $this->Model->Get ( 'Total' );
		
		$this->View->Find ( '.title', 0)->innertext = __ ( "Read Comments", array ( 'count' => $count ) );
		
		$ol = $this->View->Find ( '.comments', 0);
		
		$row = $this->View->Copy ( '.comments' )->Find ( 'ol', 0 );
		
		$rowOriginal = $row->outertext;
		
		$ol->innertext = '';
		
		foreach ( $this->Comments as $c => $comment ) {
			if ( $comment['Parent_ID'] ) continue;
			
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
		
			$this->_PrepComment ( $row, $comment );
			
			if ( $Children = $this->_GetChildren ( $comment['Entry_PK'] ) ) {
				$row->Find ( '.nesting', 0 )->innertext = $this->_BuildChildren ( $comment['Entry_PK'], $Children, $rowOriginal );
			}
			
		    $ol->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		$this->View->Find ( 'form[name="comment"] [name="Parent_ID"]', 0 )->value = "";
		$this->View->Find ( 'form[name="comment"] [name="Context"]', 0 )->value = $this->Get ( 'Context' );
		
		$this->View->Display();
		
		return ( true );
	}
	
	public function Delete ( $pView = null, $pData = array ( ) ) {
		
		$this->_Current = $this->Talk ( 'User', 'Current' );
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$Entry_PK = $this->GetSys ( 'Request' )->Get ( 'Entry_PK' );
		
		$this->Model = $this->GetModel ( );
		
		if ( ( $this->Model->Ownership ( $Entry_PK, $this->_Current->Account ) ) or
		     ( $this->_Current->Account == $this->_Focus->Account ) ) {
		     // User is editing their own context, or user owns the comment.
		     $this->Model->Remove ( $Entry_PK );
		} else {
			// Access is denied
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}
		
		$redirect = $this->GetSys ( 'Router' )->Get ( 'Request' );
		header ( 'Location:' . $redirect );
		
		return ( true );
		
	}
	
	public function Reply ( $pView = null, $pData = array ( ) ) {
		
		$Parent_ID = $this->GetSys ( 'Request' )->Get ( 'Parent_ID' );
		
		$this->View = $this->GetView ( 'reply' );
		
		$this->Model = $this->GetModel();
		
		$this->Model->Retrieve ( array ( 'Entry_PK' => $Parent_ID ) );
		$this->Model->Fetch();
		
		$parent = $this->Model->Get ( 'Data' );
		
		$this->_PrepComment ( $this->View, $parent );
		
		$this->View->Find ( 'form[name="comment"] [name="Parent_ID"]', 0 )->value = $Parent_ID;
		
		$this->View->Display();
		
		return ( true );
	}
	
	public function Post ( $pView = null, $pData = array ( ) ) {
		
		$Context = $pData['Context'];
		$Id = $pData['Id'];
		
		$this->_Current = $this->Talk ( 'User', 'Current' );
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$Body = $this->GetSys ( 'Request' )->Get ( 'Body' );
		$Parent_ID = $this->GetSys ( 'Request' )->Get ( 'Parent_ID' );
		
		$this->Model = $this->GetModel();
		
		$this->Model->Store ( $Context, $Id, $Body, $Parent_ID, $this->_Current->Account );
		
		$this->_NotifyPost ($this->_Focus->Email, $this->_Current->Account, $this->_Focus->Account, $Body, $pData );
		
		$redirect = $this->GetSys ( 'Router' )->Get ( 'Request' );
		header ( 'Location:' . $redirect );
		exit;
	}
	
	private function _NotifyPost ( $pEmail, $pSender, $pRecipient, $pComment ) {
		
		$data = array ( 'request' => $pSender, 'source' => ASD_DOMAIN, 'account' => $pRecipient );
		$SenderInfo = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Info', $data );
		
		$SenderFullname = $SenderInfo->fullname;
		$SenderNameParts = explode ( ' ', $SenderInfo->fullname );
		$SenderFirstName = $SenderNameParts[0];
		
		list ( $RecipientUsername, $RecipientDomain ) = explode ( '@', $pRecipient );
		
		$MailSubject = __( 'Someone Commented On Your Context', array ( 'fullname' => $SenderFullname ) );
		$Byline = __( 'Commented On Your Context' );
		$Subject = __( 'Commented On Your Context Subject', array ( 'firstname' => $SenderFirstName ) );
		
		$LinkDescription = __( 'Click Here For Comments' );
		$Link = 'http://' . ASD_DOMAIN . $this->GetSys ( 'Router' )->Get ( 'Request' );
		
		$pComment = strip_tags ( $this->GetSys ( 'Render' )->Format ( $pComment ) );
		
		$Body = __( 'Commented On Your Context Description', array ( 'fullname' => $SenderFullname, 'comment' => $pComment, 'domain' => 'http://' . ASD_DOMAIN, 'link' => $Link ) );
		
		$Message = array ( 'Type' => 'User', 'SenderFullname' => $SenderFullname, 'SenderAccount' => $pSender, 'RecipientEmail' => $pEmail, 'MailSubject' => $MailSubject, 'Byline' => $Byline, 'Subject' => $Subject, 'Body' => $Body, 'LinkDescription' => $LinkDescription, 'Link' => $Link );
		
		$this->GetSys ( 'Components' )->Talk ( 'Postal', 'Send', $Message );
		
		return ( true );
	} 
	
	
	public function Cancel ( $pView = null, $pData = array ( ) ) {
		$redirect = $this->GetSys ( 'Router' )->Get ( 'Request' );
		header ( 'Location:' . $redirect );
		exit;
	}
	
	private function _GetChildren ( $pParent ) {
		
		$children = array ( );
		
		foreach ( $this->Comments as $c => $comment ) {
			if ( $comment['Parent_ID'] == $pParent ) {
				$children[] = $comment;
			}
		}
		
		if ( count ( $children ) == 0 ) return ( false );
		
		return ( $children );
	}
	
	private function _BuildChildren ( $pParent, $pChildren, $pOriginal ) {
		
		$return = '';
		
		foreach ( $pChildren as $c => $child ) {
			if ( $child['Parent_ID'] != $pParent ) continue;
			
			$row = new cHTML ();
			$row->Load ( $pOriginal );
			
			$row->Find ( 'ol', 0 )->class = 'comments nested';
		
			$this->_PrepComment ( $row, $child );
			
			if ( $Children = $this->_GetChildren ( $child['Entry_PK'] ) ) {
				$row->Find ( '.nesting', 0 )->innertext .= $this->_BuildChildren ( $child['Entry_PK'], $Children, $pOriginal );
			}
			
			$return .= $row->outertext;
			
			unset ( $row );
			
		}
		
		return ( $return );
	}
	
	private function _PrepComment ( $pRow, $pItem ) {
		
			if ( $pItem['Status'] != 1 ) {
				$this->_PrepDeletedComment ( $pRow, $pItem );
				return ( true );
			}
		
			list ( $username, $domain ) = explode ( '@', $pItem['Owner'] );
			$data = array ( 'username' => $username, 'domain' => $domain, 'width' => 32, 'height' => 32 );
			$pRow->Find ( '.comment-icon', 0 )->src = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Icon', $data );
			
			$pRow->Find ( '.comment-body', 0 )->innertext = $this->GetSys ( 'Render' )->Format ( $pItem['Body'] );
			$pRow->Find ( '.stamp', 0 )->innertext = $this->GetSys ( 'Date' )->Format ( $pItem['Created'] );
			
			$pRow->Find ( '.comment-user-link', 0 )->rel = $pItem['Owner'];
			$pRow->Find ( '.comment-user-link', 0 )->innertext = $pItem['Owner'];
			
			$data = array ( 'account' => $pItem['Owner'], 'source' => ASD_DOMAIN );
			$OwnerLink = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'User', 'Link', $data );
			$pRow->Find ( '.comment-user-link', 0 )->href = $OwnerLink;
			$pRow->Find ( '.comment-icon-link', 0 )->href = $OwnerLink;
			
			$Contexts = $pRow->Find ( '[name="Context"]' );
			foreach ( $Contexts as $c => $context ) {
				$context->value = $this->Get ( 'Context' );
			}
			$pRow->Find ( 'form[name="reply"] [name="Parent_ID"]', 0 )->value = $pItem['Entry_PK'];
			
			if ( ( $this->_Current->Account == $this->_Focus->Account ) or ( $this->_Current->Account == $pItem['Owner'] ) ) {
				$pRow->Find ( 'form[name="delete"] [name="Entry_PK"]', 0 )->value = $pItem['Entry_PK'];
				$pRow->Find ( 'form[name="delete"]', 0 )->action = $this->GetSys ( 'Router' )->Get ( 'Request' );
			} else if ( !$this->_Current ) {
				$pRow->Find ( '.reply-area', 0 )->outertext = "";
				$pRow->Find ( '.delete-area', 0 )->outertext = "";
			} else {
				$pRow->Find ( '.delete-area', 0 )->outertext = "";
			} 
			
		return ( true );
	}
	
	private function _PrepDeletedComment ( $pRow, $pItem ) {
		$pRow->Find ( '.comment-body', 0 )->innertext = __ ( 'Deleted Comment' );
		$pRow->Find ( '.comment', 0 )->class .= ' deleted ';
		$pRow->Find ( '.delete-area', 0 )->outertext = '';
		$pRow->Find ( '.reply-area', 0 )->outertext = '';
		$pRow->Find ( '.stamp', 0 )->outertext = '';
	}
	
}