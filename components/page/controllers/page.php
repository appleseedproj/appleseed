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
		
		$li->outertext = '';
		
		if ( $this->References->Get ( 'Total' ) == 0 ) $li->outertext = '';
		
		$this->_Editor = false;
		if ( $this->_CheckEditor() ) $this->_Editor = true;
		
		while ( $this->References->Fetch() ) {
			$Type = $this->References->Get ( 'Type' );
			$Identifier = $this->References->Get ( 'Identifier' );
			
			if (!$this->_Item = $this->_ReferenceByType ( $Type, $Identifier ) ) continue;
			
			$this->_Item['Comment'] = $this->GetSys ( 'Render' )->Format ( $this->_Item['Comment'] );
			$this->_Item['Comment'] = $this->GetSys ( 'Render' )->LiveLinks ( $this->_Item['Comment'] );
			
			$this->_Item['Identifier'] = $Identifier;
			
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			
			switch ( strtolower ( $Type ) ) {
				case 'link':
					$row->Find ( '.item', 0 )->class .= ' link';
					$row->Find ( '.item', 0 )->innertext = $this->_PrepLink ( );
				break;
				case 'post':
				default:
					$row->Find ( '.item', 0 )->class .= ' post';
					$row->Find ( '.item', 0 )->innertext = $this->_PrepPost ( );
				break;
			}
			
		    $li->outertext .= $row->outertext;
		    unset ( $row );
		}
		
		$this->View->Reload();
	
		return ( true );
	}
	
	private function _PrepLink ( ) {
		
		$row = $this->GetView ( 'page.link' );
		
		$row->Find ( '.stamp', 0 )->innertext = $this->GetSys ( 'Date' )->Format ( $this->References->Get ( 'Stamp' ) );
		$row->Find ( '.content', 0 )->innertext = str_replace ( "\n", "<br />", $this->_Item['Comment'] );
		$row->Find ( '.owner-link', 0 )->rel = $this->_Item['Owner'];
		$row->Find ( '.owner-link', 0 )->innertext = $this->_Item['Owner'];
		if ( !$this->_Editor ) $row->Find ( '.remove', 0 )->innertext = '';
		
		list ( $username, $domain ) = explode ( '@', $this->_Item['Owner'] );
		$data = array ( 'username' => $username, 'domain' => $domain, 'width' => 64, 'height' => 64 );
		$row->Find ( '.owner-icon', 0 )->src = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Icon', $data );
		
		$ownerData = $this->Talk ( 'User', 'Link', array ( 'request' => $this->_Item['Owner'] ) );
		$owner = $ownerData['link'];
		
		$Link = $this->_Item['Link'];
		$Icon = $this->_Item['Thumb'];
		$Title = $this->_Item['Title'];
		$Description = $this->_Item['Description'];
		
		$row->Find ( '.thumb-link', 0 )->href = $Link;
		$row->Find ( '.title-link', 0 )->href = $Link;
		$row->Find ( '.info-link', 0 )->href = $Link;
		$row->Find ( '.info-link', 0 )->innertext = $Link;
		
			if ( $Icon ) 
				$row->Find ( '.thumb', 0 )->src = $Icon;
			else
				$row->Find ( '.thumb', 0 )->outertext = "";
			
			if ( $Description ) 
				$row->Find ( '.description', 0 )->innertext = $Description;
			else
				$row->Find ( '.description', 0 )->outertext = "";
			
			if ( $Title ) 
				$row->Find ( '.title', 0 )->innertext = $Title;
			else
				$row->Find ( '.title', 0 )->outertext = "";
			
		$data = array ( 'account' => $this->_Item['Owner'], 'source' => ASD_DOMAIN );
		$OwnerLink = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'User', 'Link', $data );
		$row->Find ( '.owner-link', 0 )->href = $OwnerLink;
		$row->Find ( '.owner-link', 0 )->outertext = $owner;
		$row->Find ( '.owner-icon-link', 0 )->href = $OwnerLink;
		
		$row->Find ( '[name=Context]', 0 )->value = $this->Get ( 'Context' );
		$row->Find ( '[name=Identifier]', 0 )->value = $this->_Item['Identifier'];
		
		return ( $row->outertext );
	}
	
	private function _PrepPost ( ) {
		
		$row = $this->GetView ( 'page.post' );
		
		$row->Find ( '.stamp', 0 )->innertext = $this->GetSys ( 'Date' )->Format ( $this->References->Get ( 'Stamp' ) );
		$row->Find ( '.content', 0 )->innertext = str_replace ( "\n", "<br />", $this->_Item['Comment'] );
		$row->Find ( '.owner-link', 0 )->rel = $this->_Item['Owner'];
		$row->Find ( '.owner-link', 0 )->innertext = $this->_Item['Owner'];
		if ( !$this->_Editor ) $row->Find ( '.remove', 0 )->innertext = '';
		
		$ownerData = $this->Talk ( 'User', 'Link', array ( 'request' => $this->_Item['Owner'] ) );
		$owner = $ownerData['link'];
		
		list ( $username, $domain ) = explode ( '@', $this->_Item['Owner'] );
		$data = array ( 'username' => $username, 'domain' => $domain, 'width' => 64, 'height' => 64 );
		$row->Find ( '.owner-icon', 0 )->src = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Icon', $data );
		
		$data = array ( 'account' => $this->_Item['Owner'], 'source' => ASD_DOMAIN );
		$OwnerLink = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'User', 'Link', $data );
		$row->Find ( '.owner-link', 0 )->href = $OwnerLink;
		$row->Find ( '.owner-link', 0 )->outertext = $owner;
		$row->Find ( '.owner-icon-link', 0 )->href = $OwnerLink;
		
		$row->Find ( '[name=Context]', 0 )->value = $this->Get ( 'Context' );
		$row->Find ( '[name=Identifier]', 0 )->value = $this->_Item['Identifier'];
		
		return ( $row->outertext );
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
		
		$this->References = $this->GetModel ( 'references' );
		$this->References->Retrieve ( array ( 'Identifier' => $Identifier, 'User_FK' => $UserId ) );
		$this->References->Fetch();
		
		switch ( strtolower ( $this->References->Get ( 'Type' ) ) ) {
			case 'link':
				$this->Model = $this->GetModel ( 'link' ); 
			break;
			case 'post':
				$this->Model = $this->GetModel ( 'post' ); 
			break;
		}
		
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