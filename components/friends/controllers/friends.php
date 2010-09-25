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
		
		$this->_Prep();
		
		switch ( $pView ) {
			case 'mutual':
				$this->_DisplayMutual();
			break;
			case 'circle':
				$this->_DisplayCircle();
			break;
			case 'requests':
				$this->_DisplayRequests();
			break;
			case 'friends':
			default:
				$this->_DisplayFriends();
			break;
		}
		
		if ( !$current ) {
			$this->_PrepAnonymous();
		} else if ( $current->Account == $focus->Account ) {
			$this->_PrepEditor();
		} else {
			$this->_PrepCurrent();
		}
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _DisplayFriends ( ) {
	}
	
	private function _DisplayMutual ( ) {
		$this->GetSys ( "Request" )->Set ( "Circle", "mutual" );
	}
	
	private function _DisplayRequests ( ) {
		$this->GetSys ( "Request" )->Set ( "Circle", "requests" );
	}
	
	private function _DisplayCircle ( ) {
	}
	
	private function _PrepEditor ( ) {
		
		$focus = $this->Talk ( 'User', 'Focus' );
		$current = $this->Talk ( 'User', 'Current' );
		
		// Set the "Add Circle" link
		$this->View->Find ( '[class=profile-friends-circle-add] a', 0)->href = '/profile/' . $current->Username . '/friends/circles/add/';
		
		$currentCircle = urldecode ( strtolower ( $this->GetSys ( "Request" )->Get ( "Circle" ) ) );
		
		// Set the "Edit Circle" link
		$this->View->Find ( '[class=profile-friends-circle-edit] a', 0)->href = '/profile/' . $current->Username . '/friends/circles/edit/' . $currentCircle;
		
		// Set the "Remove Circle" link
		$currentCircleName = ucwords ( $currentCircle );
		
		$this->View->Find ( '[class=profile-friends-circle-remove] a', 0)->innertext = __( "Remove This Circle", array ( "circle" => $currentCircleName ) );
		$this->View->Find ( '[class=profile-friends-circle-remove] a', 0)->href = '/profile/' . $current->Username . '/friends/circles/remove/' . $currentCircle;
		
		return ( true );
	}
	
	private function _PrepAnonymous ( ) {
		// Remove "edit circles" link
		$this->View->Find ( '[id=profile-friends-circles-edit] a', 0)->outertext = " ";
		
		// Remove "Add Circle" link
		$this->View->Find ( '[class=profile-friends-circle-add] a', 0)->innertext = ""; 
		
		return ( true );
	}
	
	private function _PrepCurrent ( ) {
		
		$editor = false;
		
		// Remove "Add Circle" link
		$this->View->Find ( '[class=profile-friends-circle-add] a', 0)->innertext = ""; 
		
		if ( $editor ) {
			$this->View->Find ( '[id=profile-friends-circles-edit] a', 0)->href = '/profile/' . $current->Username . '/friends/circles/edit/';
		} 
		
		return ( true );
	}
	
	private function _PrepAnonymousRow ( $pRow ) {
		// Remove "add as friend" 
		$pRow->Find ( "[class=friends-add-friend]", 0 )->innertext = "";
		
		// Remove "remove from friends"
		$pRow->Find ( "[class=friends-remove-friend]", 0 )->innertext = "";
		
		// Remove "add circles" dropdown
		$pRow->Find ( "[class=friends-circle-editor]", 0 )->innertext = "";
		
		// Remove "Mutual Friends" count
		$pRow->Find ( "[class=friends-mutual-count]", 0 )->innertext = "";
		
		return ( $pRow );
	}
	
	private function _PrepEditorRow ( $pRow ) {
		
		// Remove "add as friend" 
		$pRow->Find ( "[class=friends-add-friend]", 0 )->innertext = "";
		
		// Prep "add circles" dropdown
		// $pRow->Find ( "[class=friends-circle-editor]", 0 )->innertext = "";
		
		// Remove "Mutual Friends" count
		$pRow->Find ( "[class=friends-mutual-count]", 0 )->innertext = "";
		
		return ( $pRow );
	}
	
	private function _PrepCurrentRow ( $pRow, $pCurrentUserInfo, $pUserInfo ) {
		
		// Remove "add circles" dropdown
		$pRow->Find ( "[class=friends-circle-editor]", 0 )->innertext = "";
		
		if ( in_array ( $pCurrentUserInfo->account, $pUserInfo->friends ) ) {
			// Remove "add as friend" if already friends
			$pRow->Find ( "[class=friends-add-friend]", 0 )->innertext = "";
		} else if ( $pCurrentUserInfo->account == $pUserInfo->account ) {
			// Remove both since we're looking at our own account.
			$pRow->Find ( "[class=friends-remove-friend]", 0 )->innertext = "";
			$pRow->Find ( "[class=friends-add-friend]", 0 )->innertext = "";
		} else {
			// Remove "remove from friends" if not friends
			$pRow->Find ( "[class=friends-remove-friend]", 0 )->innertext = "";
		}
		
		return ( $pRow );
	}
	
	private function _Prep ( ) {
		$focus = $this->Talk ( 'User', 'Focus' );
		$current = $this->Talk ( 'User', 'Current' );
		
		$tabs =  $this->View->Find ('nav[id=profile-friends-tabs]', 0);
		$tabs->innertext = $this->GetSys ( 'Components' )->Buffer ( 'friends', 'tabs' );
		
		if ( $current ) {
			$currentAccount = $current->Username . '@' . $current->Domain;
			$data = array ( "account" => $currentAccount, 'source' => ASD_DOMAIN, 'request' => $currentAccount );
			$currentInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
			$currentInfo->username = $current->Username;
			$currentInfo->domain = $current->Domain;
			$currentInfo->account = $current->Username . '@' . $current->Domain;
		}
		
		$currentAccount = $current->Username . '@' . $current->Domain;
		
		$this->Model = $this->GetModel();
		
		list ( $start, $step, $page ) = $this->_PageCalc();
		$this->Model->Retrieve ( array ( "userAuth_uID" => $focus->uID ), null, array ( "start" => $start, "step" => $step ) );
		
		$friendCount = $this->Model->Get ( "Total" );
		
		$this->View->Find ( '[class=profile-friends-owner]', 0 )->innertext = __( "Friends Of User", array ( "fullname" => $focus->Fullname ) );
		$this->View->Find ( '[class=profile-friends-count]', 0 )->innertext = __( "Number Of Friends", array ( "count" => $friendCount ) );
		
		$li = $this->View->Find ( "ul[class=friends-list] li", 0);
		
		$row = $this->View->Copy ( "[class=friends-list]" )->Find ( "li", 0 );
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		while ( $this->Model->Fetch() ) {
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
		
			$username = $this->Model->Get ( "Username" );
			$domain = $this->Model->Get ( "Domain" );
			$account = $username . '@' . $domain;
			
			$data = array ( "username" => $username, "domain" => $domain, "width" => 64, "height" => 64 );
			$row->Find ( '[class=friends-icon]', 0 )->src = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Icon", $data );
			
			$data = array ( "username" => $username, "domain" => $domain, "currentUsername" => $current->Username, "currentDomain" => $current->Domain );
			$row->Find ( "[class=friends-add-friend-link]", 0 )->href = $this->GetSys ( "Event" )->Trigger ( "Create", "Friend", "Addlink", $data );
			$row->Find ( "[class=friends-remove-friend-link]", 0 )->href = $this->GetSys ( "Event" )->Trigger ( "Create", "Friend", "Removelink", $data );
			
			$data = array ( "account" => $account, 'source' => ASD_DOMAIN, 'request' => $currentAccount );
			$userInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
			$userInfo->username = $username;
			$userInfo->domain = $domain;
			$userInfo->account = $username . '@' . $domain;
			
			$row->Find ( '[class=friends-location]', 0 )->innertext = $userInfo->location;
			
			$row->Find ( '[class=friends-status]', 0 )->innertext = $userInfo->status;
			
			$row->Find ( '[class=friends-identity]', 0 )->href = 'http://' . $domain . '/profile/' . $username . '/';
			$row->Find ( '[class=friends-identity]', 0 )->innertext = $username . '@' . $domain;
			$row->Find ( '[class=friends-fullname-link]', 0 )->innertext = $userInfo->fullname;
			$row->Find ( '[class=friends-fullname-link]', 0 )->href = 'http://' . $domain . '/profile/' . $username . '/';
			$row->Find ( '[class=friends-icon-link]', 0 )->href = 'http://' . $domain . '/profile/' . $username . '/';
			
			$mutualFriendsCount = count ( array_intersect ( $currentInfo->friends, $userInfo->friends ) );
			
			if ( $mutualFriendsCount == 1 ) {
				$row->Find ( '[class=friends-mutual-count]', 0 )->innertext = __( "Mutual Friend Count", array ( "count" => $mutualFriendsCount ) );
			} else if ( $mutualFriendsCount > 1 ) {
				$row->Find ( '[class=friends-mutual-count]', 0 )->innertext = __( "Mutual Friends Count", array ( "count" => $mutualFriendsCount ) );
			} else {
				$row->Find ( '[class=friends-mutual-count]', 0 )->innertext = "";
			}
			
			if ( !$current ) {
				$row = $this->_PrepAnonymousRow ( $row );
			} else if ( $current->Account == $focus->Account ) {
				$row = $this->_PrepEditorRow ( $row );
			} else {
				$row = $this->_PrepCurrentRow ( $row, $currentInfo, $userInfo );
			}
			
		    $li->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		$link = $this->GetSys ( "Router" )->Get ( "Base" ) . '(.*)';
		$pageData = array ( 'start' => $start, 'step'  => $step, 'total' => $friendCount, 'link' => $link );
		$pageControl =  $this->View->Find ('nav[class=pagination]', 0);
		$pageControl->innertext = $this->GetSys ( 'Components' )->Buffer ( 'pagination', $pageData ); 
		
		$this->_PrepMessage();
		
		$this->View->Reload();
		
		return ( true );
	}
	
	private function _PageCalc ( ) {
		
		$page = $this->GetSys ( "Request" )->Get ( "Page");
		
		if ( !$page ) $page = 1;
		
		$step = 10;
		
		// Calculate the starting point in the list.
		$start = ( $page - 1 ) * $step;
		
		$return = array ( $start, $step, $page );
		
		return ( $return );
	}
	
	private function _PrepMessage ( ) {
		
		$markup = $this->View;
		
		$session = $this->GetSys ( "Session" );
		$session->Context ( $this->Get ( "Context" ) );
		
		if ( $message =  $session->Get ( "Message" ) ) {
			$markup->Find ( "[id=friends-message]", 0 )->innertext = $message;
			if ( $error =  $session->Get ( "Error" ) ) {
				$markup->Find ( "[id=friends-message]", 0 )->class = "error";
			} else {
				$markup->Find ( "[id=friends-message]", 0 )->class = "message";
			}
			$session->Delete ( "Message ");
			$session->Delete ( "Error ");
		}
		
		return ( true );
	}
	
}
