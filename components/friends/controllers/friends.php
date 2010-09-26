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
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$this->View = $this->GetView ( $pView ); 
		
		switch ( $pView ) {
			case 'mutual':
				return ( $this->_DisplayMutual() );
			break;
			case 'circle':
				return ( $this->_DisplayCircle() );
			break;
			case 'requests':
				return ( $this->_DisplayRequests() );
			break;
			case 'friends':
			default:
				return ( $this->_DisplayFriends() );
			break;
		}
		
		return ( true );
	}
	
	private function _DisplayFriends ( ) {
		$this->View->Find ( '[class=profile-friends-title]', 0 )->innertext = __( "Friends Title", array ( "fullname" => $this->_Focus->Fullname ) );
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _DisplayMutual ( ) {
		$this->View->Find ( '[class=profile-friends-title]', 0 )->innertext = __( "Mutual Title", array ( "fullname" => $this->_Focus->Fullname ) );
		
		$this->GetSys ( "Request" )->Set ( "Circle", "mutual" );
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _DisplayRequests ( ) {
		$this->View->Find ( '[class=profile-friends-title]', 0 )->innertext = __( "Requests Title", array ( "fullname" => $this->_Focus->Fullname ) );
		
		$this->GetSys ( "Request" )->Set ( "Circle", "requests" );
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _DisplayCircle ( ) {
		
		$circleName = urldecode ( str_replace ( '-', ' ' , $this->GetSys ( "Request" )->Get ( "Circle" ) ) );
		
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( "Foundation" )->Redirect ( "common/403.php" );
			return ( false );
		}
		
		$this->Circles = $this->GetModel ( "Circles" );
		
		if ( !$this->Circles->Load ( $this->_Focus->Id, $circleName ) ) {
			$this->GetSys ( "Foundation" )->Redirect ( "common/404.php" );
			return ( false );
		}
		
		$this->View->Find ( '[class=profile-friends-title]', 0 )->innertext = __( "Circle Title", array ( "circle" => $this->Circles->Get ( "Name" ) ) );
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _CheckAccess ( ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( ( $this->_Focus->Username != $this->_Current->Username ) or ( $this->_Focus->Domain != $this->_Current->Domain ) ) {
			return ( false );
		}
		
		return ( true );
	}
	
	
	private function _CircleToUrl ( $pCircle ) {
		
		$return = strtolower ( urlencode ( utf8_decode ( str_replace ( ' ', '-', $pCircle ) ) ) );
		
		return ( $return );
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
		$currentCircleName = ucwords ( str_replace ( '-', ' ', $currentCircle ) );
		
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
		
		// Remove links
		$this->View->Find ( '[class=profile-friends-circle-add] a', 0)->innertext = ""; 
		$this->View->Find ( '[class=profile-friends-circle-remove] a', 0)->innertext = ""; 
		$this->View->Find ( '[class=profile-friends-circle-edit] a', 0)->innertext = ""; 
		
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
		
		if ( !$this->_Current ) {
			$this->_PrepAnonymous();
		} else if ( $this->_Current->Account == $this->_Focus->Account ) {
			$this->_PrepEditor();
		} else {
			$this->_PrepCurrent();
		}
		
		$tabs =  $this->View->Find ('nav[id=profile-friends-tabs]', 0);
		$tabs->innertext = $this->GetSys ( 'Components' )->Buffer ( 'friends', 'tabs' );
		
		if ( $this->_Current ) {
			$data = array ( "account" => $this->_Current->Account, 'source' => ASD_DOMAIN, 'request' => $this->_Current->Account );
			$currentInfo = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Info", $data );
			$currentInfo->username = $current->Username;
			$currentInfo->domain = $current->Domain;
			$currentInfo->account = $current->Username . '@' . $current->Domain;
		}
		
		$this->Model = $this->GetModel();
		
		list ( $start, $step, $page ) = $this->_PageCalc();
		$this->Model->Retrieve ( array ( "userAuth_uID" => $this->_Focus->uID ), null, array ( "start" => $start, "step" => $step ) );
		
		$friendCount = $this->Model->Get ( "Total" );
		
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
			
			$data = array ( "username" => $username, "domain" => $domain, "currentUsername" => $this->_Current->Username, "currentDomain" => $this->_Current->Domain );
			$row->Find ( "[class=friends-add-friend-link]", 0 )->href = $this->GetSys ( "Event" )->Trigger ( "Create", "Friend", "Addlink", $data );
			$row->Find ( "[class=friends-remove-friend-link]", 0 )->href = $this->GetSys ( "Event" )->Trigger ( "Create", "Friend", "Removelink", $data );
			
			$data = array ( "account" => $account, 'source' => ASD_DOMAIN, 'request' => $this->_Current->Account );
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
			
			if ( !$this->_Current ) {
				$row = $this->_PrepAnonymousRow ( $row );
			} else if ( $this->_Current->Account == $this->_Focus->Account ) {
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
