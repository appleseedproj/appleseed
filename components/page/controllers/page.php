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
	
	var $Type = array();
	
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
		$this->References = $this->GetModel ( 'References' ); 
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$friends = $this->Talk ( 'Friends', 'Friends' );
		
		if ( !$this->_Current ) {
			$this->View->Find ( '.post', 0 )->outertext = '';
			$this->_Prep();
		} else if ( $this->_Current->Account == $this->_Focus->Account ) { 
			$this->_Prep();
		} else if ( !in_array ( $this->_Current->Account, $friends ) ) {
			$this->View->Find ( '.post', 0 )->outertext = '';
			$this->_Prep();
		} else {
			$this->_Prep();
		}
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		$this->References->RetrieveReferences ( $this->_Focus->Id );
		$this->View->Find ( '[name=Context]', 0 )->value = $this->Get ( 'Context' );
		
		$privacyData = array ( 'start' => $start, 'step'  => $step, 'total' => $total, 'link' => $link );
		$privacyControls =  $this->View->Find ('.privacy', 0);
		$privacyControls->innertext = $this->GetSys ( 'Components' )->Buffer ( 'privacy', $pageData ); 
			
		$li = $this->View->Find ( '.list .item', 0);
		
		$row = $this->View->Copy ( '.list' )->Find ( '.item', 0 );
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		$Editor = false;
		if ( $this->_CheckEditor() ) $Editor = true;
		
		while ( $this->References->Fetch() ) {
			$Type = $this->References->Get ( 'Type' );
			$Identifier = $this->References->Get ( 'Identifier' );
			
			if (!$Item = $this->_ReferenceByType ( $Type, $Identifier ) ) continue;
			
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			
			$row->Find ( '.stamp', 0 )->innertext = $this->GetSys ( 'Date' )->Format ( $this->References->Get ( 'Stamp' ) );
			$row->Find ( '.content', 0 )->innertext = $Item['Comment'];
			$row->Find ( '.owner-link', 0 )->rel = $Item['Owner'];
			$row->Find ( '.owner-link', 0 )->innertext = $Item['Owner'];
			if ( !$Editor ) $row->Find ( '.delete', 0 )->innertext = '';
			
			list ( $username, $domain ) = explode ( '@', $Item['Owner'] );
			$data = array ( 'username' => $username, 'domain' => $domain, 'width' => 64, 'height' => 64 );
			$row->Find ( '.owner-icon', 0 )->src = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Icon', $data );
			
			$row->Find ( '[name=Context]', 0 )->value = $this->Get ( 'Context' );
			$row->Find ( '[name=Identifier]', 0 )->value = $Identifier;
			
			$row->Find ( '.delete', 0 )->action = $this->GetSys ( "Router" )->Get ( "Base" );
			
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
		
		if ( !$this->_Current ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}
		
		$Owner = $this->_Current->Account;
		$Content = $this->GetSys ( 'Request' )->Get ( 'Content' );
		$Privacy = $this->GetSys ( 'Request' )->Get ( 'Privacy' );
		
		$Current = false;
		if ( $this->_Focus->Account == $this->_Current->Account ) $Current = true;
		
		$this->Model->Post ( $Content, $Privacy, $this->_Focus->Id, $Owner, $Current );
		
		$redirect = $this->GetSys ( "Router" )->Get ( "Request" );
		header ( 'Location:' . $redirect );
		exit;
	}
	
	public function _CheckEditor () {
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( $this->_Focus->Account == $this->_Current->Account ) return ( true );
		
		return ( false );
	}
	
	public function Remove ( $pData = null ) {
		
		if ( !$this->_CheckEditor () ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$Identifier = $this->GetSys ( 'Request' )->Get ( 'Identifier' );
		$UserId = $this->_Focus->Id;
		
		$this->Model = $this->GetModel (); 
		
		$this->Model->Remove ( $Identifier, $this->_Focus->Id );
		
		$redirect = $this->GetSys ( "Router" )->Get ( "Base" );
		header ( 'Location:' . $redirect );
		exit;
	}
	
	private function _ReferenceByType ( $pType, $pIdentifier ) {
		if ( !$this->Types ) $this->Types = $this->_ReferenceTypes ( );
		
		$pType = strtolower ( $pType );
		
		$pointer = $this->Types[$pType];
		$data = array ( 'Identifier' => $pIdentifier, 'Account' => $this->_Current->Account );
		$return = $this->GetSys ( 'Components' )->Talk ( $pointer->Component, $pointer->Function, $data );
		
		return ( $return );
	}
	
	private function _ReferenceTypes ( ) {
		
		$components = $this->GetSys ( 'Components' );
		$componentList = $components->Get ( 'Config' )->Get ( 'Components' ); 
		
		foreach ( $componentList as $c => $component ) {
			if ( !$types = $components->Talk ( $component, 'RegisterPageType' ) ) continue;
			if ( !is_array ( $types ) ) continue;
			$this->Types = array_merge ( (array)$this->Types, $types );
		}
		
		$this->Types = array_change_key_case ( $this->Types, CASE_LOWER );
		
		return ( $this->Types );
	}
	
}