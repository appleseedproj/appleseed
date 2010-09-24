<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Friends
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Friends Component Mutual Controller
 * 
 * Friends Component Mutual Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Friends
 */
class cFriendsMutualController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$current = $this->Talk ( 'User', 'Current' );
		
		$focus = $this->Talk ( 'User', 'Focus' );
		
		// If user isn't logged in, or we're viewing our own page, then don't display.
		if ( ( $focus->Username == $current->Username ) or ( !$current->Username ) ) {
			return ( true );
		}
		
		$current = $this->Talk ( 'User', 'Current' );
		
		$this->View = $this->GetView ( $pView ); 
		
		$this->View->Find ( '[class=all-mutual-friends-link]', 0 )->href = 'http://' . $focus->Domain . '/profile/' . $focus->Username . '/friends/mutual/';
		
		if ( !$this->_Prep() ) return ( false );
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		$focus = $this->Talk ( 'User', 'Focus' );
		$current = $this->Talk ( 'User', 'Current' );
		
		$tabs =  $this->View->Find ('nav[id=profile-friends-tabs]', 0);
		$tabs->innertext = $this->GetSys ( 'Components' )->Buffer ( 'friends', 'tabs' );
		
		$currentAccount = $current->Username . '@' . $current->Domain;
		$data = array ( "account" => $currentAccount, 'source' => ASD_DOMAIN, 'request' => $currentAccount );
		$currentInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
		$currentInfo->username = $current->Username;
		$currentInfo->domain = $current->Domain;
		$currentInfo->account = $current->Username . '@' . $current->Domain;
		
		$focusAccount = $focus->Username . '@' . $focus->Domain;
		$data = array ( "account" => $focusAccount, 'source' => ASD_DOMAIN, 'request' => $focusAccount );
		$focusInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
		$focusInfo->username = $focus->Username;
		$focusInfo->domain = $focus->Domain;
		$focusInfo->account = $focus->Username . '@' . $focus->Domain;
		
		$mutualFriends = array_intersect ( $currentInfo->friends, $focusInfo->friends );
		
		if ( count ( $mutualFriends ) < 1 ) return ( false );
		
		$li = $this->View->Find ( "[class=friends-mutual-summary-item]", 0);
		
		$row = $this->View->Copy ( "[class=friends-mutual-summary-item]" )->Find ( "[class=friends-mutual-summary-item]", 0 );
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		foreach ( $mutualFriends as $m => $mutualFriend ) {
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
		
			list ( $username, $domain ) = split ( '@', $mutualFriend );
			
			$data = array ( "username" => $username, "domain" => $domain, "width" => 32, "height" => 32 );
			$row->Find ( '[class=friends-icon]', 0 )->src = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Icon", $data );
			
			$row->Find ( '[class=friends-icon-link]', 0 )->href = 'http://' . $domain . '/profile/' . $username . '/';
			
		    $li->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		return ( true );
	}
	
	
}
