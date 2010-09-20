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

/** Friends Component Friends Controller
 * 
 * Friends Component Friends Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Friends
 */
class cFriendsFriendsController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$focus = $this->Talk ( 'User', 'Focus' );
		
		$current = $this->Talk ( 'User', 'Current' );
		
		$this->View = $this->GetView ( $pView ); 
		
		$this->_PrepFocus();
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _PrepFocus ( ) {
		
		$focus = $this->Talk ( 'User', 'Focus' );
		$current = $this->Talk ( 'User', 'Current' );
		
		$editor = false;
		if ( ( $focus->Username == $current->Username ) and ( $focus->Domain == $current->Domain ) ) {
			$editor = true;
		}
		
		if ( $editor ) {
			$this->View->Find ( '[id=profile-friends-circles-edit] a', 0)->href = '/profile/' . $current->Username . '/friends/circles/edit/';
		} else {
			$this->View->Find ( '[id=profile-friends-circles-edit] a', 0)->outertext = " ";
		}
		
	}
	
	private function _Prep ( ) {
		$focus = $this->Talk ( 'User', 'Focus' );
		$current = $this->Talk ( 'User', 'Current' );
		
		if ( $current ) {
			$currentAccount = $current->Username . '@' . $current->Domain;
			$data = array ( "account" => $currentAccount, 'source' => ASD_DOMAIN, 'request' => $currentAccount );
			$currentInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
		}
		
		$currentAccount = $current->Username . '@' . $current->Domain;
		
		$this->Model = $this->GetModel();
		
		$this->Model->Retrieve ( array ( "userAuth_uID" => $focus->uID ) );
		
		$friendCount = $this->Model->Get ( "Total" );
		
		$this->View->Find ( '[class=profile-friends-owner]', 0 )->innertext = __( "Friends Of User", array ( "fullname" => $focus->Fullname ) );
		$this->View->Find ( '[class=profile-friends-count]', 0 )->innertext = __( "Number Of Friends", array ( "count" => $friendCount ) );
		
		while ( $this->Model->Fetch() ) {
			$username = $this->Model->Get ( "Username" );
			$domain = $this->Model->Get ( "Domain" );
			$account = $username . '@' . $domain;
			
			$data = array ( "username" => $username, "domain" => $domain, "width" => 64, "height" => 64 );
			$this->View->Find ( '[class=friends-icon]', 0 )->src = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Icon", $data );
			
			if ( ( $current ) and ( $focus->Username == $current->Username ) and ( $focus->Domain == $current->Domain ) ) {
				$this->View->Find ( '[class=friends-add-friend]', 0 )->outertext = "";
			} else {
				$this->View->Find ( '[class=friends-remove-friend]', 0 )->outertext = "";
			}
			
			$data = array ( "account" => $account, 'source' => ASD_DOMAIN, 'request' => $currentAccount );
			$userInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
			
			$this->View->Find ( '[class=friends-identity]', 0 )->href = 'http://' . $domain . '/profile/' . $username . '/';
			$this->View->Find ( '[class=friends-identity]', 0 )->innertext = $username . '@' . $domain;
			$this->View->Find ( '[class=friends-fullname]', 0 )->innertext = $userInfo->fullname;
			
			$mutualFriendsCount = count ( array_intersect ( $currentInfo->friends, $userInfo->friends ) );
			
			if ( $mutualFriendsCount == 1 ) {
				$this->View->Find ( '[class=friends-mutual-count]', 0 )->innertext = __( "Mutual Friend Count", array ( "count" => $mutualFriendsCount ) );
			} else if ( $mutualFriendsCount > 1 ) {
				$this->View->Find ( '[class=friends-mutual-count]', 0 )->innertext = __( "Mutual Friends Count", array ( "count" => $mutualFriendsCount ) );
			}
			
		}
		
		$pageData = array ( 'start' => 0, 'step'  => 2, 'total' => $friendCount, 'link' => $link );
		$pageControl =  $this->View->Find ('nav[class=pagination]', 0);
		$pageControl->innertext = $this->GetSys ( 'Components' )->Buffer ( 'pagination', $pageData ); 
		
		return ( true );
	}
	
}
