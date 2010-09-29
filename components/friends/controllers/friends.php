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
		
		$this->Model = $this->GetModel();
		$this->Circles = $this->GetModel ( 'Circles' );
		
		list ( $this->_PageStart, $this->_PageStep, $this->_Page ) = $this->_PageCalc();
		
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
		$this->View->Find ( '[class=profile-friends-title]', 0 )->innertext = __( 'Friends Title', array ( 'fullname' => $this->_Focus->Fullname ) );
		
		$this->Model->RetrieveFriends ( $this->_Focus->uID, array ( 'start' => $this->_PageStart, 'step' => $this->_PageStep ) );
		
		$this->View->Find ( '[class=profile-friends-count]', 0 )->innertext = __( 'Number Of Friends', array ( 'count' => $this->Model->Get ( 'Total' ) ) );
		
		$this->_PageLink = $this->GetSys ( 'Router' )->Get ( 'Base' ) . '(.*)';
			
		$this->_Prep();
		
		$this->View->Find ( '[class=profile-friends-circle-edit] a', 0)->outertext = ' ';
		$this->View->Find ( '[class=profile-friends-circle-remove] a', 0)->innertext = ''; 
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _DisplayMutual ( ) {
		
		if ( !$this->_Current ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/404.php' );
			return ( false );
		}
		
		$this->View->Find ( '[class=profile-friends-title]', 0 )->innertext = __( 'Mutual Title', array ( 'fullname' => $this->_Focus->Fullname ) );
		
		$data = array ( 'account' => $this->_Current->Account, 'source' => ASD_DOMAIN, 'request' => $this->_Current->Account );
		$this->_CurrentInfo = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Info', $data );
		
		$this->Model->RetrieveMutual ( $this->_Focus->uID, $this->_CurrentInfo->friends, array ( 'start' => $this->_PageStart, 'step' => $this->_PageStep ) );
		
		$this->View->Find ( '[class=profile-friends-count]', 0 )->innertext = __( 'Number Of Mutual Friends', array ( 'count' => $this->Model->Get ( 'Total' ) ) );
		
		$this->GetSys ( 'Request' )->Set ( 'Circle', 'mutual' );
		
		$this->_PageLink = $this->GetSys ( 'Router' )->Get ( 'Base' ) . '(.*)';
			
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _DisplayRequests ( ) {
		
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}
		
		$this->View->Find ( '[class=profile-friends-title]', 0 )->innertext = __( 'Requests Title', array ( 'fullname' => $this->_Focus->Fullname ) );
		
		$this->Model->RetrieveRequests ( $this->_Focus->uID, array ( 'start' => $this->_PageStart, 'step' => $this->_PageStep ) );
		
		$this->View->Find ( '[class=profile-friends-count]', 0 )->innertext = __( 'Number Of Friend Requests', array ( 'count' => $this->Model->Get ( 'Total' ) ) );
		
		$this->GetSys ( 'Request' )->Set ( 'Circle', 'requests' );
		
		$this->_PageLink = $this->GetSys ( 'Router' )->Get ( 'Base' ) . '(.*)';
			
		$this->_Prep();
		
		$this->View->Find ( '[class=profile-friends-circle-edit] a', 0)->innertext = '';
		$this->View->Find ( '[class=profile-friends-circle-remove] a', 0)->innertext = ''; 
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _DisplayCircle ( ) {
		
		$circleName = urldecode ( str_replace ( '-', ' ' , $this->GetSys ( 'Request' )->Get ( 'Circle' ) ) );
		
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}
		
		if ( !$this->Circles->Load ( $this->_Focus->Id, $circleName ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/404.php' );
			return ( false );
		}
		
		$this->_ViewingCircle = $this->Circles->Get ( 'Name' );
		
		$this->Model->RetrieveCircle ( $this->_Focus->uID, $this->Circles->Get ( 'tID' ), array ( 'start' => $this->_PageStart, 'step' => $this->_PageStep ) );
		
		$this->View->Find ( '[class=profile-friends-count]', 0 )->innertext = __( 'Number Of Friends In Circle', array ( 'count' => $this->Model->Get ( 'Total' ), 'circle' => $this->Circles->Get ( 'Name' ) ) );
		
		$circleName = $this->Circles->Get ( 'Name' );
		
		$page = $this->GetSys ( 'Request' )->Get ( 'Page' );
		
		if ( !$page )
			$this->_PageLink = $this->GetSys ( 'Router' )->Get ( 'Base' ) . $circleName . '/(.*)';
		else
			$this->_PageLink = $this->GetSys ( 'Router' )->Get ( 'Base' ) . '(.*)';
		
		$this->View->Find ( '[class=profile-friends-title]', 0 )->innertext = __( 'Circle Title', array ( 'circle' => $circleName ) );
		
		$this->_Prep();
		
		// Prepare forms.
		$friendsList = $this->Model->Friends ( $this->_Focus->uID );
		$friendsInCircle = $this->Model->FriendsInCircle ( $this->_Focus->uID, $this->Circles->Get ( 'tID' ) );
		$friendsOutCircle = array_diff (  $friendsList, $friendsInCircle );
		
		if ( count ( $friendsOutCircle ) > 0 ) {
			$this->View->Find ( '[class=friend-in-circle-list]', 0 )->innertext = '<option disabled="disabled">Add Friend To Circle</option>';
			foreach ( $friendsOutCircle as $f => $friend ) {
				$this->View->Find ( '[class=friend-in-circle-list]', 0 )->innertext .= '<option>' . $friend . '</option>';
			}
		}
	
		if ( count ( $friendsInCircle ) > 0 ) {
			$this->View->Find ( '[class=friend-in-circle-list]', 0 )->innertext .= '<option disabled="disabled">Remove Friend From Circle</option>';
			foreach ( $friendsInCircle as $f => $friend ) {
				$this->View->Find ( '[class=friend-in-circle-list]', 0 )->innertext .= '<option>' . $friend . '</option>';
			}
		}
		
		if ( count ( $friendsList ) == 0 ) {
			$this->View->Find ( '[class=profile-friends-circle-add-friend]', 0 )->innertext = '';
		}
		
		$this->View->Find ( '[class=friend-in-circle-edit]', 0 )->action = '/profile/' . $this->_Focus->Username . '/friends/circles/';
		$this->View->Find ( '[class=friend-in-circle-edit] [name=Circle]', 0 )->value = $circleName;
		
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
		
		// Set the 'Add Circle' link
		$this->View->Find ( '[class=profile-friends-circle-add] a', 0)->href = '/profile/' . $current->Username . '/friends/circles/add/';
		
		$currentCircle = urldecode ( strtolower ( $this->GetSys ( 'Request' )->Get ( 'Circle' ) ) );
		
		// Set the 'Edit Circle' link
		$this->View->Find ( '[class=profile-friends-circle-edit] a', 0)->href = '/profile/' . $current->Username . '/friends/circles/edit/' . $currentCircle;
		
		// Set the 'Remove Circle' link
		$currentCircleName = ucwords ( str_replace ( '-', ' ', $currentCircle ) );
		
		$this->View->Find ( '[class=profile-friends-circle-remove] a', 0)->innertext = __( 'Remove This Circle', array ( 'circle' => $currentCircleName ) );
		$this->View->Find ( '[class=profile-friends-circle-remove] a', 0)->href = '/profile/' . $current->Username . '/friends/circles/remove/' . $currentCircle;
		
		return ( true );
	}
	
	private function _PrepAnonymous ( ) {
		
		// Remove links
		$this->View->Find ( '[id=profile-friends-circles-edit] a', 0)->outertext = ' ';
		$this->View->Find ( '[class=profile-friends-circle-add] a', 0)->innertext = ''; 
		
		return ( true );
	}
	
	private function _PrepCurrent ( ) {
		
		// Remove links
		$this->View->Find ( '[class=profile-friends-circle-add] a', 0)->innertext = ''; 
		$this->View->Find ( '[class=profile-friends-circle-remove] a', 0)->innertext = ''; 
		$this->View->Find ( '[class=profile-friends-circle-edit] a', 0)->innertext = ''; 
		
		return ( true );
	}
	
	private function _PrepAnonymousRow ( $pRow ) {
		// Remove 'add as friend' 
		$pRow->Find ( '[class=friends-add-friend]', 0 )->innertext = '';
		
		// Remove 'remove from friends'
		$pRow->Find ( '[class=friends-remove-friend]', 0 )->innertext = '';
		
		// Remove 'add circles' dropdown
		$pRow->Find ( '[class=friends-circle-editor]', 0 )->innertext = '';
		
		// Remove 'Mutual Friends' count
		$pRow->Find ( '[class=friends-mutual-count]', 0 )->innertext = '';
		
		return ( $pRow );
	}
	
	private function _PrepEditorRow ( $pRow ) {
		
		// Remove 'add as friend' 
		$pRow->Find ( '[class=friends-add-friend]', 0 )->innertext = '';
		
		// Prep 'add circles' dropdown
		// $pRow->Find ( '[class=friends-circle-editor]', 0 )->innertext = '';
		
		// Remove 'Mutual Friends' count
		$pRow->Find ( '[class=friends-mutual-count]', 0 )->innertext = '';
		
		// Prepare Circle Add/Remove
		$circlesList = $this->Circles->Circles ( $this->_Focus->uID );
		
		if ( count ( $circlesList ) == 0 ) {
			$pRow->Find ( '[class=friend-circle-edit-list]', 0)->outertext = '';
		} else {
		
			foreach ( $circlesList as $c => $circ ) {
				$AllCircles[] = $circ['name'];
			}
			
			$account = $this->Model->Get ( 'Username' ) . '@' . $this->Model->Get ( 'Domain' );
			$MemberOfCircles = $this->Circles->CirclesByMember ( $this->_Focus->uID, $account );
			$NotMemberOfCircles = array_diff (  $AllCircles, $MemberOfCircles );
			
			if ( count ( $NotMemberOfCircles ) > 0 ) {
				$pRow->Find ( '[class=friend-circle-edit-list]', 0 )->innertext = '<option disabled="disabled">Add To Circle</option>';
				foreach ( $NotMemberOfCircles as $c => $circ ) {
					$pRow->Find ( '[class=friend-circle-edit-list]', 0 )->innertext .= '<option>' . $circ . '</option>';
				}
			}
		
			if ( count ( $MemberOfCircles ) > 0 ) {
				$pRow->Find ( '[class=friend-circle-edit-list]', 0 )->innertext .= '<option disabled="disabled">Remove Circle</option>';
				foreach ( $MemberOfCircles as $c => $circ ) {
					$pRow->Find ( '[class=friend-circle-edit-list]', 0 )->innertext .= '<option>' . $circ . '</option>';
				}
			}
			
			$pRow->Find ( '[class=friend-circle-edit]', 0 )->action = '/profile/' . $this->_Focus->Username . '/friends/circles/';
			$pRow->Find ( '[class=friend-circle-edit] [name=Friend]', 0 )->value = $this->Model->Get ( 'Username' ) . '@' . $this->Model->Get ( 'Domain' );
			
			if ( $currentCircle = $circleName = $this->Circles->Get ( 'Name' ) ) 
				$pRow->Find ( '[class=friend-circle-edit] [name=Viewing]', 0 )->value = $this->_ViewingCircle;
				
			// Let the circles editor know that we're looking at requests.
			if ( $this->Model->Get ( 'Verification' ) == 2 ) {
				$pRow->Find ( '[class=friend-circle-edit] [name=Viewing]', 0 )->value = 'requests';
				$pRow->Find ( '[class=friends-approve-friend-link]', 0 )->href = '/profile/' . $this->_Focus->Username . '/friends/approve/' . $this->Model->Get ( 'Username' ) . '@' . $this->Model->Get ( 'Domain' );
			}
		
		}
		
		return ( $pRow );
	}
	
	private function _PrepCurrentRow ( $pRow, $pCurrentUserInfo, $pUserInfo ) {
		
		// Remove 'add circles' dropdown
		$pRow->Find ( '[class=friends-circle-editor]', 0 )->innertext = '';
		
		if ( in_array ( $pCurrentUserInfo->account, $pUserInfo->friends ) ) {
			// Remove 'add as friend' if already friends
			$pRow->Find ( '[class=friends-add-friend]', 0 )->innertext = '';
		} else if ( $pCurrentUserInfo->account == $pUserInfo->account ) {
			// Remove both since we're looking at our own account.
			$pRow->Find ( '[class=friends-remove-friend]', 0 )->innertext = '';
			$pRow->Find ( '[class=friends-add-friend]', 0 )->innertext = '';
		} else {
			// Remove 'remove from friends' if not friends
			$pRow->Find ( '[class=friends-remove-friend]', 0 )->innertext = '';
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
			$data = array ( 'account' => $this->_Current->Account, 'source' => ASD_DOMAIN, 'request' => $this->_Current->Account );
			$currentInfo = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Info', $data );
			$currentInfo->username = $this->_Current->Username;
			$currentInfo->domain = $this->_Current->Domain;
			$currentInfo->account = $this->_Current->Username . '@' . $this->_Current->Domain;
		}
		
		$friendCount = $this->Model->Get ( 'Total' );
		
		$li = $this->View->Find ( 'ul[class=friends-list] li', 0);
		
		$row = $this->View->Copy ( '[class=friends-list]' )->Find ( 'li', 0 );
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		while ( $this->Model->Fetch() ) {
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
		
			$username = $this->Model->Get ( 'Username' );
			$domain = $this->Model->Get ( 'Domain' );
			$request = $username . '@' . $domain;
			
			$data = array ( 'username' => $username, 'domain' => $domain, 'width' => 64, 'height' => 64 );
			$row->Find ( '[class=friends-icon]', 0 )->src = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Icon', $data );
			
			$data = array ( 'account' => $this->_Current->Account, 'request' => $request, );
			$row->Find ( '[class=friends-add-friend-link]', 0 )->href = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'Friend', 'Addlink', $data );
			$row->Find ( '[class=friends-remove-friend-link]', 0 )->href = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'Friend', 'Removelink', $data );
			
			$data = array ( 'account' => $this->_Current->Account, 'source' => ASD_DOMAIN, 'request' => $request);
			$userInfo = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Info', $data );
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
				$row->Find ( '[class=friends-mutual-count]', 0 )->innertext = __( 'Mutual Friend Count', array ( 'count' => $mutualFriendsCount ) );
			} else if ( $mutualFriendsCount > 1 ) {
				$row->Find ( '[class=friends-mutual-count]', 0 )->innertext = __( 'Mutual Friends Count', array ( 'count' => $mutualFriendsCount ) );
			} else {
				$row->Find ( '[class=friends-mutual-count]', 0 )->innertext = '';
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
		
		$pageData = array ( 'start' => $this->_PageStart, 'step'  => $this->_PageStep, 'total' => $friendCount, 'link' => $this->_PageLink );
		$pageControl =  $this->View->Find ('nav[class=pagination]', 0);
		$pageControl->innertext = $this->GetSys ( 'Components' )->Buffer ( 'pagination', $pageData ); 
		
		$this->_PrepMessage();
		
		$this->View->Reload();
		
		return ( true );
	}
	
	private function _PageCalc ( ) {
		
		$page = $this->GetSys ( 'Request' )->Get ( 'Page');
		
		if ( !$page ) $page = 1;
		
		$step = 10;
		
		// Calculate the starting point in the list.
		$start = ( $page - 1 ) * $step;
		
		$return = array ( $start, $step, $page );
		
		return ( $return );
	}
	
	private function _PrepMessage ( ) {
		
		$markup = $this->View;
		
		$session = $this->GetSys ( 'Session' );
		$session->Context ( $this->Get ( 'Context' ) );
		
		if ( $message =  $session->Get ( 'Message' ) ) {
			$markup->Find ( '[id=friends-message]', 0 )->innertext = $message;
			if ( $error =  $session->Get ( 'Error' ) ) {
				$markup->Find ( '[id=friends-message]', 0 )->class = 'error';
			} else {
				$markup->Find ( '[id=friends-message]', 0 )->class = 'message';
			}
			$session->Delete ( 'Message');
			$session->Delete ( 'Error ');
		}
		
		return ( true );
	}
	
	public function Add ( ) {
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$model = $this->GetModel();
		
		$session = $this->GetSys ( "Session" );
		$request = $this->GetSys ( 'Request' )->Get ( 'Request' );
		
		$data = array ( 'account' => $this->_Current->Account, 'source' => ASD_DOMAIN, 'request' => $request );
		$this->_RequestInfo = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Info', $data );
		
		// 0. Check for a pending request.
		if ( $model->CheckPending ( $this->_Focus->uID, $request ) ) {
			$session->Context ( 'friends.friends.(\d+).(mutual|friends|requests|circles|circle)' );
			$session->Set ( "Message", __( "Pending request already exists", array ( 'account' => $request, 'fullname' => $this->_RequestInfo->fullname ) ) );
		
			$redirect = '/profile/' . $this->_Focus->Username . '/friends';
			header ( 'Location:' . $redirect );
			exit;
		}
		
		// 1. Send out the request
		$data = array ( 'account' => $this->_Current->Account, 'request' => $request );
		$result = $this->GetSys ( 'Event' )->Trigger ( 'On', 'Friend', 'Add', $data );
		
		if ( $result->success != 'true' ) {
			$session->Context ( 'friends.friends.(\d+).(mutual|friends|requests|circles|circle)' );
			$session->Set ( "Message", __( "Could not send request", array ( 'account' => $request, 'fullname' => $this->_RequestInfo->fullname ) ) );
		
			$redirect = '/profile/' . $this->_Focus->Username . '/friends';
			header ( 'Location:' . $redirect );
			exit;
		}
		
		// 2. Create a pending record in the database.
		$model->SavePending ( $this->_Focus->uID, $request );
		
		// 3. Redirect to friends.
		$session->Context ( 'friends.friends.(\d+).(mutual|friends|requests|circles|circle)' );
		$session->Set ( "Message", __( "Friend Request Sent", array ( 'account' => $request, 'fullname' => $this->_RequestInfo->fullname ) ) );
		$redirect = '/profile/' . $this->_Focus->Username . '/friends';
		header ( 'Location:' . $redirect );
		
		return ( true );
	}
	
	public function Remove ( ) {
		
		return ( true );
	}
	
	public function Approve ( ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$model = $this->GetModel();
		
		$session = $this->GetSys ( "Session" );
		$request = $this->GetSys ( 'Request' )->Get ( 'Request' );
		
		$data = array ( 'account' => $this->_Current->Account, 'source' => ASD_DOMAIN, 'request' => $request );
		$this->_RequestInfo = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Info', $data );
		
		// 1. Check if a request record exists for this user.
		if ( !$model->CheckRequest ( $this->_Focus->uID, $request ) ) {
			$session->Context ( 'friends.friends.(\d+).(mutual|friends|requests|circles|circle)' );
			$session->Set ( "Message", __( "No Friend Request Found", array ( 'account' => $request, 'fullname' => $this->_RequestInfo->fullname ) ) );
			$session->Set ( "Error", true );
		
			$redirect = '/profile/' . $this->_Focus->Username . '/friends/requests/';
			header ( 'Location:' . $redirect );
			exit;
		}
		
		// 2. Send out the approval
		$data = array ( 'account' => $this->_Current->Account, 'request' => $request );
		$result = $this->GetSys ( 'Event' )->Trigger ( 'On', 'Friend', 'Approve', $data );
		
		if ( $result->success != 'true' ) {
			$session->Context ( 'friends.friends.(\d+).(mutual|friends|requests|circles|circle)' );
			$session->Set ( "Message", __( "Could not approve request", array ( 'account' => $request, 'fullname' => $this->_RequestInfo->fullname ) ) );
			$session->Set ( "Error", true );
		
			$redirect = '/profile/' . $this->_Focus->Username . '/friends/requests/';
			header ( 'Location:' . $redirect );
			exit;
		}
		
		// 3. Update the flag in the database.
		$model->SaveApproved ( $this->_Focus->uID, $request );
		
		// 4. Redirect to friends.
		$session->Context ( 'friends.friends.(\d+).(mutual|friends|requests|circles|circle)' );
		$session->Set ( "Message", __( "Friend Request Approved", array ( 'account' => $request, 'fullname' => $this->_RequestInfo->fullname ) ) );
		$redirect = '/profile/' . $this->_Focus->Username . '/friends';
		header ( 'Location:' . $redirect );
		
		return ( true );
	}
	
}
