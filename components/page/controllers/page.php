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

/** Page Component Page Controller
 * 
 * Page Component Page Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Page
 */
class cPagePageController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->View = $this->GetView ( $pView ); 
		$this->Model = $this->GetModel (); 
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( !$this->_Current ) {
			$this->View->Find ( '.post', 0 )->outertext = "";
			$this->_Prep();
		} else {
			$this->_Prep();
		}
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		$this->Model->RetrievePagePosts ( $this->_Focus->Id );
		$this->View->Find ( "[name=Context]", 0 )->value = $this->Get ( "Context" );
		
		$privacyData = array ( 'start' => $start, 'step'  => $step, 'total' => $total, 'link' => $link );
		$privacyControls =  $this->View->Find ('.privacy', 0);
		$privacyControls->innertext = $this->GetSys ( "Components" )->Buffer ( "privacy", $pageData ); 
			
		$li = $this->View->Find ( '.list .item', 0);
		
		$row = $this->View->Copy ( '.list' )->Find ( '.item', 0 );
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		while ( $this->Model->Fetch() ) {
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			
			$row->Find ( '.stamp', 0 )->innertext = $this->GetSys ( "Date" )->Format ( $this->Model->Get ( "Stamp" ) );
			$row->Find ( '.content', 0 )->innertext = $this->Model->Get ( "Comment" );
			$row->Find ( '.owner-link', 0 )->rel = $this->Model->Get ( "ActionOwner" );
			$row->Find ( '.owner-link', 0 )->innertext = $this->Model->Get ( "ActionOwner" );
			
			list ( $username, $domain ) = explode ( '@', $this->Model->Get ( "ActionOwner" ) );
			$data = array ( 'username' => $username, 'domain' => $domain, 'width' => 64, 'height' => 64 );
			$row->Find ( '.owner-icon', 0 )->src = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Icon', $data );
			
		    $li->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		$this->View->Reload();
		
		return ( true );
	}
	
	public function Share ( $pView = null, $pData = array ( ) ) {
		
		$this->Model = $this->GetModel (); 
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$Owner = $this->_Current->Account;
		$Comments = $this->GetSys ( "Request" )->Get ( "Comment" );
		$Privacy = $this->GetSys ( "Request" )->Get ( "Privacy" );
		
		$Current = false;
		
		if ( $this->_Focus->Account == $this->_Current->Account ) $Current = true;
		
		$this->Model->Post ( $Comments, $Privacy, $this->_Focus->Id, $Owner, $Current );
		
		$redirect = '/profile/' . $this->_Focus->Username . '/page/';
		header ( 'Location:' . $redirect );
		exit;
	}
	
}