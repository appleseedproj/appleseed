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
		
		echo "<pre>";
		if ( $current ) {
			$currentAccount = $current->Username . '@' . $current->Domain;
			$data = array ( "account" => $currentAccount, 'source' => ASD_DOMAIN, 'request' => $currentAccount );
			$currentInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
			
			print_r ( $currentInfo );
			echo "<hr />";
		}
		
		$account = 'frank@30rock.appleseed';
		$data = array ( "account" => $account, 'source' => ASD_DOMAIN, 'request' => $currentAccount );
		$friendInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
		
		print_r ( $friendInfo );
		echo "<hr />";
		
		$remoteAccount = 'admin@glee.appleseed';
		$data = array ( "account" => $remoteAccount, 'source' => ASD_DOMAIN, 'request' => $currentAccount );
		$remoteInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
		
		print_r ( $remoteInfo ); exit;
		
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
		
		$this->Model = $this->GetModel();
		
		$friendCount = $this->Model->CountFriends ( $focus->uID );
		
		$this->View->Find ( '[class=profile-friends-owner]', 0 )->innertext = __( "Friends Of User", array ( "fullname" => $focus->Fullname ) );
		$this->View->Find ( '[class=profile-friends-count]', 0 )->innertext = __( "Number Of Friends", array ( "count" => $friendCount ) );
		
		$pageData = array ( 'start' => 0, 'step'  => 10, 'total' => 10, 'link' => $link );
		$pageControl =  $this->View->Find ('nav[class=pagination]', 0);
		$pageControl->innertext = $this->GetSys ( 'Components' )->Buffer ( 'pagination', $pageData ); 
		
		return ( true );
	}
	
}
