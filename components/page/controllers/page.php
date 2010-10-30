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
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		$Identifier = $this->GetSys ( 'Request' )->Get ( 'Identifier' );
		
		$this->References->RetrieveReferences ( $this->_Focus->Id, $Identifier );
		
		$li = $this->View->Find ( '.list .item', 0);
		
		$row = $this->View->Copy ( '.list' )->Find ( '.item', 0 );
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		if ( $this->References->Get ( 'Total' ) == 0 ) $li->outertext = '';
		
		$Editor = false;
		if ( $this->_CheckEditor() ) $Editor = true;
		
		while ( $this->References->Fetch() ) {
			$Type = $this->References->Get ( 'Type' );
			$Identifier = $this->References->Get ( 'Identifier' );
			
			if (!$Item = $this->_ReferenceByType ( $Type, $Identifier ) ) continue;
			
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			
			$row->Find ( '.stamp', 0 )->innertext = $this->GetSys ( 'Date' )->Format ( $this->References->Get ( 'Stamp' ) );
			$row->Find ( '.content', 0 )->innertext = str_replace ( "\n", "<br />", $Item['Comment'] );
			$row->Find ( '.owner-link', 0 )->rel = $Item['Owner'];
			$row->Find ( '.owner-link', 0 )->innertext = $Item['Owner'];
			if ( !$Editor ) $row->Find ( '.remove', 0 )->innertext = '';
			
			list ( $username, $domain ) = explode ( '@', $Item['Owner'] );
			$data = array ( 'username' => $username, 'domain' => $domain, 'width' => 64, 'height' => 64 );
			$row->Find ( '.owner-icon', 0 )->src = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Icon', $data );
			
			$data = array ( 'account' => $Item['Owner'], 'source' => ASD_DOMAIN );
			$OwnerLink = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'User', 'Link', $data );
			$row->Find ( '.owner-link', 0 )->href = $OwnerLink;
			$row->Find ( '.owner-icon-link', 0 )->href = $OwnerLink;
			
			$row->Find ( '[name=Context]', 0 )->value = $this->Get ( 'Context' );
			$row->Find ( '[name=Identifier]', 0 )->value = $Identifier;
			
		    $li->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		$this->View->Reload();
		
		return ( true );
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
		
		$redirect = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/page/';
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