<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Newsfeed
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Newsfeed Component Newsfeed Controller
 * 
 * Newsfeed Component Newsfeed Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Newsfeed
 */
class cNewsfeedNewsfeedController extends cController {
	
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
		
		$this->Model = $this->GetModel ( 'Incoming' );
		
		$this->View = $this->GetView ( $pView );
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		$this->Model->Incoming ( $this->_Focus->Id );
		
		$li = $this->View->Find ( '.list .item', 0);
		
		$row = $this->View->Copy ( '.list' )->Find ( '.item', 0 );
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		if ( $this->Model->Get ( 'Total' ) == 0 ) $li->outertext = '';
		
		while ( $this->Model->Fetch() ) {
			$Type = $this->Model->Get ( 'Type' );
			$Identifier = $this->Model->Get ( 'Identifier' );
			
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			
			$row->Find ( '.stamp', 0 )->innertext = $this->GetSys ( 'Date' )->Format ( $this->Model->Get ( 'Updated' ) );
			$row->Find ( '.comment', 0 )->innertext = str_replace ( "\n", "<br />", $this->Model->Get ( 'Comment' ) );
			$row->Find ( '.actionowner-link', 0 )->rel = $this->Model->Get ( 'ActionOwner' );
			$row->Find ( '.actionowner-link', 0 )->innertext = $this->Model->Get ( 'ActionOwner' );
			$row->Find ( '.remove', 0 )->innertext = '';
			
			list ( $username, $domain ) = explode ( '@', $this->Model->Get ( 'ActionOwner' ) );
			$data = array ( 'username' => $username, 'domain' => $domain, 'width' => 64, 'height' => 64 );
			$row->Find ( '.actionowner-icon', 0 )->src = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Icon', $data );
			
			$data = array ( 'account' => $this->Model->Get ( 'ActionOwner' ), 'source' => ASD_DOMAIN );
			$OwnerLink = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'User', 'Link', $data );
			$row->Find ( '.actionowner-link', 0 )->href = $OwnerLink;
			$row->Find ( '.actionowner-icon-link', 0 )->href = $OwnerLink;
			
			$row->Find ( '[name=Context]', 0 )->value = $this->Get ( 'Context' );
			$row->Find ( '[name=Identifier]', 0 )->value = $Identifier;
			
		    $li->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		$this->View->Reload();
	}
	
	public function Notify ( $pView = null, $pData = array ( ) ) {
		
		$OwnerId = $pData['OwnerId'];
		$Action = $pData['Action'];
		$ActionOwner = $pData['ActionOwner'];
		$ActionLink = $pData['ActionLink'];
		$SubjectOwner = $pData['SubjectOwner'];
		$Context = $pData['Context'];
		$ContextOwner = $pData['ContextOwner'];
		$ContextLink = $pData['ContextLink'];
		$Icon = $pData['Icon'];
		$Comment = $pData['Comment'];
		$Description = $pData['Description'];
		$Identifier = $pData['Identifier'];
		
		$Friends = $pData['Friends'];
		
		$Incoming = $this->GetModel ( 'Incoming' );
		$Outgoing = $this->GetModel ( 'Outgoing' );
		
		$Incoming->Queue ( $OwnerId, $Action, $ActionOwner, $ActionLink, $SubjectOwner, $Context, $ContextOwner, $ContextLink, $Icon, $Comment, $Description, $Identifier );
		
		foreach ( $Friends as $f => $friend ) {
			$Outgoing->Queue ( $OwnerId, $friend, $Action, $ActionOwner, $ActionLink, $SubjectOwner, $Context, $ContextOwner, $ContextLink, $Icon, $Comment, $Description, $Identifier );
		}
		
		return ( true );
	}
	
}
