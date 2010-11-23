<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Page
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Page Component Share Controller
 * 
 * Page Component Share Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Page
 */
class cPageShareController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$friends = $this->Talk ( 'Friends', 'Friends' );
		
		if ( $this->_Current->Account == $this->_Focus->Account ) { 
		} else if ( !$this->_Current ) {
			return ( false );
		} else if ( !in_array ( $this->_Current->Account, $friends ) ) {
			return ( false );
		}
		
		$this->View = $this->GetView ( 'share' );
		
		$privacyData = array ( 'start' => $start, 'step'  => $step, 'total' => $total, 'link' => $link );
		$privacyControls =  $this->View->Find ('.privacy');
		
		foreach ( $privacyControls as $c => $control ) {
			$control->innertext = $this->GetSys ( 'Components' )->Buffer ( 'privacy', $pageData ); 
		}
		
		$Contexts =  $this->View->Find ( '[name=Context]' );
		foreach ( $Contexts as $c => $context ) {
			$context->value = $this->Get ( 'Context' );
		}
		
		$this->View->Display();
		
		return ( true );
	}
	
	public function Share ( $pView = null, $pData = array ( ) ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( !$this->_Current ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}
		
		$Type = $this->GetSys ( 'Request' )->Get ( 'Type' );
		
		if ( !$Type ) $Type = 'Post';
		
		switch ( $Type ) {
			case 'Link':
				$this->_ShareLink();
			break;
			case 'Post':
			default:
				$this->_SharePost();
			break;
		}
		
		return ( $this->Display ( $pView, $pData ) );
	}
	
	private function _SharePost ( ) {
		
		$Action = 'posted';
				
		$this->Model = $this->GetModel ( "Post" ); 
		
		$Owner = $this->_Current->Account;
		$Content = $this->GetSys ( 'Request' )->Get ( 'Content' );
		$Privacy = $this->GetSys ( 'Request' )->Get ( 'Privacy' );
		
		$friends = $this->Talk ( 'Friends', 'Friends' );
		
		$Current = false;
		if ( $this->_Focus->Account == $this->_Current->Account ) $Current = true;
		
		$this->Model->Post ( $Content, $Privacy, $this->_Focus->Id, $Owner, $Current );
		
		$Identifier = $this->Model->Get ( 'Identifier' );
		
		foreach ( $friends as $f => $friend ) {
			if ( $friend == $this->_Current->Account ) continue;
			$Access = $this->Talk ( 'Privacy', 'Check', array ( 'Requesting' => $friend, 'Type' => 'Post', 'Identifier' => $Identifier ) );
			if ( !$Access ) unset ( $friends[$f] );
		}
		
		$notifyData = array ( 'OwnerId' => $this->_Focus->Id, 'Friends' => $friends, 'ActionOwner' => $this->_Current->Account, 'Action' => $Action, 'ContextOwner' => $this->_Focus->Account, 'Context' => 'page', 'Comment' => $Content, 'Identifier' => $Identifier );
		$this->Talk ( 'Newsfeed', 'Notify', $notifyData );
		
		// Don't send an email if we're posting on our own page.
		if ( $this->_Current->Account != $this->_Focus->Account ) {
			$this->_EmailPost ( $Content, $Identifier );
		}
		
		return ( true );
	}
	
	private function _ShareLink ( ) {
		
		$Action = 'posted a link';
		
		$this->Model = $this->GetModel ( "Link" ); 
		
		$Link = $this->GetSys ( 'Request' )->Get ( 'Link' );
		$LinkThumb = $this->GetSys ( 'Request' )->Get ( 'LinkThumb' );
		$LinkTitle = $this->GetSys ( 'Request' )->Get ( 'LinkTitle' );
		$LinkDescription = $this->GetSys ( 'Request' )->Get ( 'LinkDescription' );
		
		$Comment = $this->GetSys ( 'Request' )->Get ( 'LinkContent' );
		
		$Owner = $this->_Current->Account;
		$Privacy = $this->GetSys ( 'Request' )->Get ( 'Privacy' );
		
		$friends = $this->Talk ( 'Friends', 'Friends' );
		
		$this->Model->Link ( $Comment, $Privacy, $this->_Focus->Id, $Owner, $Link, $LinkTitle, $LinkDescription, $LinkThumb );
		
		$Identifier = $this->Model->Get ( 'Identifier' );
		
		foreach ( $friends as $f => $friend ) {
			if ( $friend == $this->_Current->Account ) continue;
			$Access = $this->Talk ( 'Privacy', 'Check', array ( 'Requesting' => $friend, 'Type' => 'Link', 'Identifier' => $Identifier ) );
			if ( !$Access ) unset ( $friends[$f] );
		}
		
		$notifyData = array ( 'OwnerId' => $this->_Focus->Id, 'Friends' => $friends, 'ActionOwner' => $this->_Current->Account, 'Action' => $Action, 'ActionLink' => $Link, 'ContextOwner' => $this->_Focus->Account, 'Context' => 'page', 'Comment' => $Comment, 'Title' => $LinkTitle, 'Icon' => $LinkThumb, 'Description' => $LinkDescription, 'Identifier' => $Identifier );
		$this->Talk ( 'Newsfeed', 'Notify', $notifyData );
		
		// Don't send an email if we're posting on our own page.
		if ( $this->_Current->Account != $this->_Focus->Account ) {
			$this->_EmailLink ( $Comment, $Link, $LinkTitle, $LinkDescription, $Identifier );
		}
		
		return ( true );
	}
	
	private function _EmailPost ( $pContent, $pIdentifier) {
		$data = array ( 'account' => $this->_Current->Account, 'source' => ASD_DOMAIN, 'request' => $this->_Current->Account );
		$CurrentInfo = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Info', $data );
		$SenderFullname = $CurrentInfo->fullname;
		$SenderNameParts = explode ( ' ', $CurrentInfo->fullname );
		$SenderFirstName = $SenderNameParts[0];
		
		$SenderAccount = $this->_Current->Account;
		
		$RecipientEmail = $this->_Focus->Email;
		$MailSubject = __( "Someone Posted On Your Page", array ( "fullname" => $SenderFullname ) );
		$Byline = __( "Posted On Your Page" );
		$Subject = __( "Someone Said", array ( "firstname" => $SenderFirstName ) );
		$Body = $pContent;
		$LinkDescription = __( "Click Here" );
		$Link = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/page/' . $pIdentifier;
		
		$Message = array ( 'Type' => 'User', 'SenderFullname' => $SenderFullname, 'SenderAccount' => $SenderAccount, 'RecipientEmail' => $RecipientEmail, 'MailSubject' => $MailSubject, 'Byline' => $Byline, 'Subject' => $Subject, 'Body' => $Body, 'LinkDescription' => $LinkDescription, 'Link' => $Link );
		
		$this->Talk ( 'Postal', 'Send', $Message );
		
		return ( true );
	} 
	
	private function _EmailLink ( $pComment, $pLink, $pLinkTitle, $pLinkDescription, $pIdentifier ) {
		$data = array ( 'account' => $this->_Current->Account, 'source' => ASD_DOMAIN, 'request' => $this->_Current->Account );
		$CurrentInfo = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Info', $data );
		$SenderFullname = $CurrentInfo->fullname;
		$SenderNameParts = explode ( ' ', $CurrentInfo->fullname );
		$SenderFirstName = $SenderNameParts[0];
		
		$SenderAccount = $this->_Current->Account;
		
		$RecipientEmail = $this->_Focus->Email;
		$MailSubject = __( "Someone Posted A Link On Your Page", array ( "fullname" => $SenderFullname ) );
		$Byline = __( "Posted A Link On Your Page" );
		$Subject = __( "Someone Posted A Link", array ( "firstname" => $SenderFirstName ) );
		$Body = __( "Posted A Link Body", array ( "comment" => $pComment, "link" => $pLink, "title" => $pLinkTitle, "description" => $pLinkDescription  ) );
		$LinkDescription = __( "Click Here" );
		$Link = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/page/' . $pIdentifier;
		
		$Message = array ( 'Type' => 'User', 'SenderFullname' => $SenderFullname, 'SenderAccount' => $SenderAccount, 'RecipientEmail' => $RecipientEmail, 'MailSubject' => $MailSubject, 'Byline' => $Byline, 'Subject' => $Subject, 'Body' => $Body, 'LinkDescription' => $LinkDescription, 'Link' => $Link );
		
		$this->Talk ( 'Postal', 'Send', $Message );
		
		return ( true );
	} 
	
}