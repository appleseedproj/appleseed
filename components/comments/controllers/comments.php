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
	
	function Display ( $pView = null, $pData = array ( ) ) {
		
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
		
		$this->View->Display();
		
		return ( true );
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
			
			if ( ( $this->_Current->Account == $this->_Focus->Account ) or ( $this->_Current->Account == $pItem['Owner'] ) ) {
				$pRow->Find ( '.delete', 0 )->href = "";
			} else {
				$pRow->Find ( '.delete-area', 0 )->outertext = "";
			} 
			
		return ( true );
	}
	
	private function _PrepDeletedComment ( $pRow, $pItem ) {
		$pRow->Find ( '.comment-body', 0 )->innertext = __ ( 'Deleted Comment' );
		$pRow->Find ( '.comments', 0 )->class .= ' deleted ';
		$pRow->Find ( '.delete-area', 0 )->outertext = '';
		$pRow->Find ( '.reply-area', 0 )->outertext = '';
		$pRow->Find ( '.stamp', 0 )->outertext = '';
	}
	
}