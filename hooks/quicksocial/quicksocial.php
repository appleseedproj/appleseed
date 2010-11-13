<?php
/**
 * @version      $pId$p
 * @package      Appleseed.Framework
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Example Hook Class
 * 
 * Example Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cQuicksocialHook extends cHook {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function SystemNodeDiscovery ( $pData = array() ) {
		
		if (!class_exists ( 'cQuickNode' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicknode.php' );
		
		// Set caching on by default.
		$cache = $pData['cache'] ? isset ( $pData['cache'] ) : true;
		
		if ( $cache ) {
			$model = new cModel ( 'NodeDiscovery');
		}
		
		$node = new cQuickNode ();
		
		$domain = $pData['domain'];
		
		$node->SetCallback ( 'CheckLocalToken', array ( $this, '_CheckLocalToken' ) );
		$node->SetCallback ( 'CreateLocalToken', array ( $this, '_CreateLocalToken' ) );
		
		$node = $node->Discover ( $domain );
		
		return ( $node );
	}
	
	public function EndSystemInitialize ( $pData = null ) {
		
		// Bounce if requested.
		$this->_Bounce ( );
		
		$social = $this->GetSys ( 'Request' )->Get ( '_social' );
		
		if ( $social != 'true' ) return ( false );
		
		$task = $this->GetSys ( 'Request' )->Get ( '_task' );
		
		switch ( $task ) {
			case 'verify':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicksocial.php' );
				 
				$social = new cQuickSocial ();
				$social->SetCallback ( 'CheckLocalToken', array ( $this, '_CheckLocalToken' ) );
				
				$social->ReplyToVerify();
				exit;
			break;
			case 'verify.remote':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicksocial.php' );
				 
				$social = new cQuickSocial ();
				$social->SetCallback ( 'CheckLocalToken', array ( $this, '_CheckLocalToken' ) );
				
				$social->ReplyToRemoteVerify();
				exit;
			break;
			case 'connect.check':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickconnect.php' );
				
				$connect = new cQuickConnect ();
				$connect->SetCallback ( 'CheckLogin', array ( $this, '_CheckLogin' ) );
				$connect->SetCallback ( 'CreateLocalToken', array ( $this, '_CreateLocalToken' ) );
				
				$connect->Check();
				exit;
			break;
			case 'connect.return':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickconnect.php' );
				 
				$connect = new cQuickConnect ();
				$connect->SetCallback ( 'CreateRemoteToken', array ( $this, '_CreateRemoteToken' ) );
				$connect->SetCallback ( 'CheckRemoteToken', array ( $this, '_CheckRemoteToken' ) );
				
				$verified = $connect->Process();
				
				if ( $verified->success == 'true' ) {
					$username = $verified->username;
					$domain = $verified->domain;
					$returnTo = $verified->returnTo;
					if ( !strstr ( $returnTo, '://' ) ) $returnTo = 'http://' . $returnTo;
					
					$this->_SetRemoteSession( $username, $domain );
					
					header ('Location:' . $returnTo);
		
					exit;
				} else {
					
					$this->GetSys ( 'Session' )->Context ( 'login.login.4.remote' );
					$this->GetSys ( 'Session' )->Set ( 'Message', $verified->error );
					$this->GetSys ( 'Session' )->Set ( 'Identity', $verified->username . '@' . $verified->domain );
					$this->GetSys ( 'Session' )->Set ( 'Error', true );
					
					$redirect = 'http://' . ASD_DOMAIN . '/login/remote/';
					header('Location: ' . $redirect);
				}
				
				exit;
			break;
			case 'node.discover':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicknode.php' );
				 
				$node = new cQuickNode ();
				$node->SetCallback ( 'CheckRemoteToken', array ( $this, '_CheckRemoteToken' ) );
				$node->SetCallback ( 'CreateRemoteToken', array ( $this, '_CreateRemoteToken' ) );
				$node->SetCallback ( 'NodeInformation', array ( $this, '_NodeInformation' ) );
				$node->ReplyToDiscover();
				exit;
			break;
			case 'redirect':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickredirect.php' );
				
				$redirect = new cQuickRedirect ();
				$redirect->SetCallback ( 'Redirect', array ( $this, '_Redirect' ) );
				$redirect->Redirect();
				exit;
			break;
			case 'user.icon':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickuser.php' );
				 
				$user = new cQuickUser ();
				$user->SetCallback ( 'UserIcon', array ( $this, '_UserIcon' ) );
				$user->ReplyToIcon();
				exit;
			break;
			case 'user.info':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickuser.php' );
				 
				$user = new cQuickUser ();
				$user->SetCallback ( 'CheckLocalToken', array ( $this, '_CheckLocalToken' ) );
				$user->SetCallback ( 'CheckRemoteToken', array ( $this, '_CheckRemoteToken' ) );
				$user->SetCallback ( 'UserInfo', array ( $this, '_UserInfo' ) );
				$user->ReplyToInfo();
				exit;
			break;
			case 'friend.add':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickfriend.php' );
				
				$friend = new cQuickFriend ();
				$friend->SetCallback ( 'CheckLocalToken', array ( $this, '_CheckLocalToken' ) );
				$friend->SetCallback ( 'CheckRemoteToken', array ( $this, '_CheckRemoteToken' ) );
				$friend->SetCallback ( 'FriendAdd', array ( $this, '_FriendAdd' ) );
				$friend->ReplyToFriend();
				exit;
			break;
			case 'friend.approve':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickfriend.php' );
				
				$friend = new cQuickFriend ();
				$friend->SetCallback ( 'CheckLocalToken', array ( $this, '_CheckLocalToken' ) );
				$friend->SetCallback ( 'CheckRemoteToken', array ( $this, '_CheckRemoteToken' ) );
				$friend->SetCallback ( 'FriendApprove', array ( $this, '_FriendApprove' ) );
				$friend->ReplyToApprove();
				exit;
			break;
			case 'friend.remove':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickfriend.php' );
				
				$friend = new cQuickFriend ();
				$friend->SetCallback ( 'CheckLocalToken', array ( $this, '_CheckLocalToken' ) );
				$friend->SetCallback ( 'CheckRemoteToken', array ( $this, '_CheckRemoteToken' ) );
				$friend->SetCallback ( 'FriendRemove', array ( $this, '_FriendRemove' ) );
				$friend->ReplyToRemove();
				exit;
			break;
			case 'feed.synchronize':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickfeed.php' );
				
				$notify = new cQuickFeed ();
				$notify->SetCallback ( 'CheckLocalToken', array ( $this, '_CheckLocalToken' ) );
				$notify->SetCallback ( 'CheckRemoteToken', array ( $this, '_CheckRemoteToken' ) );
				$notify->SetCallback ( 'FeedSynchronize', array ( $this, '_FeedSynchronize' ) );
				$notify->ReplyToSynchronize();
				exit;
			break;
			case '':
			default:
				exit;
			break;
		}
		
	}
	
	public function OnLoginAuthenticate ( $pData ) {
		
		if (!class_exists ( 'cQuickNode' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicknode.php' );
		
		$node = new cQuickNode ();
		
		$domain = $pData['domain'];
		
		$node->SetCallback ( 'CheckLocalToken', array ( $this, '_CheckLocalToken' ) );
		$node->SetCallback ( 'CreateLocalToken', array ( $this, '_CreateLocalToken' ) );
		$node->SetCallback ( 'LogNetworkRequest', array ( $this, '_LogNetworkRequest' ) );
		
		$node = $node->Discover ( $domain );
		
		if ( $node->success != 'true' ) {
			$return = new stdClass();
			if ( $node->error ) {
				$return->error = $node->error;
			} else {
				$return->error = 'Invalid Node';
			}
			
			return ( $return );
		}
		
		if (!class_exists ( 'cQuickConnect' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickconnect.php' );
		
		$connect = new cQuickConnect ();
		
		$username = $pData['username'];
		$domain = $pData['domain'];
		
		// @todo Use HTTP for now, but allow for other methods later.
		$method = 'http';
		
		$returnTo = $pData['return'];
		
		$connect->Redirect ( $domain, $username, $method, $returnTo );
		exit;
	}
	
	public function OnUserIcon ( $pData ) {
		
		$domain = $pData['domain'];
		$username = $pData['username'];
		
		$width = $pData['width'];
		$height = $pData['height'];
		
		$width = $this->_FindClosestValue ( $width, array ( 32, 64, 128 ) );
		$height = $width;
				
		// Get the filename size identifier.
		switch ( $width ) {
			case 128:
				$size = 'm';
			break;
			case 64:
				$size = 's';
			break;
			case 32:
			default:
				$size = 't';
			break;
		}
		
		if ( $domain == ASD_DOMAIN ) {
			$location = ASD_PATH . '_storage' . DS . 'photos' . DS . $username . DS;
			$file = $location . 'profile.' . $size . '.jpg';
			
			/* 
			 * @todo Remove this eventually once new photo system is used.
			 * 
			 */
			if ( !file_exists ( $file ) ) {
				$legacy_file = ASD_PATH . '_storage' . DS . 'legacy' . DS . 'photos' . DS . $username . DS . 'profile.jpg';
				
				if ( file_exists ( $legacy_file ) ) {
				
					if ( !is_dir ( $location ) ) rmkdir ( $location );
				
					if ( is_writable ( $location ) ) {
						$icon = imagecreatefromjpeg ( $legacy_file );
						$new_icon = $this->GetSys ( 'Image' )->ResizeAndCrop ( $icon, $width, $height );
						imagejpeg ( $new_icon, $file );
						chmod ( $file, 0777 );
					}
					
				} else {
					// @todo Replace this with the current theme.
					$url = 'http://' . ASD_DOMAIN . '/themes/default/images/noicon.gif';
				}
			}
			
			if (!$url ) $url = 'http://' . ASD_DOMAIN . '/_storage' . '/' . 'photos' . '/' . $username . '/' . 'profile.' . $size . '.jpg';;
			$return = $url;
		} else {
			$data = array ( '_social' => 'true', '_task' => 'user.icon', '_request' => $username, '_width' => $width, '_height' => $height );
		
			$return = 'http://' . $domain . '/?' . http_build_query ( $data );
		}
		
		return ( $return );
	}
	
	public function OnFriendAdd ( $pData ) {
		
		if (!class_exists ( 'cQuickFriend' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickfriend.php' );
		
		$friend = new cQuickFriend ();
		
		$friend->SetCallback ( 'CreateLocalToken', array ( $this, '_CreateLocalToken' ) );
		$friend->SetCallback ( 'LogNetworkRequest', array ( $this, '_LogNetworkRequest' ) );
		
		$account = $pData['account'];
		$request = $pData['request'];
		
		$result = $friend->Friend ( $account, $request );
		
		return ( $result );
	}
	
	public function OnFriendRemove ( $pData ) {
		
		if (!class_exists ( 'cQuickFriend' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickfriend.php' );
		
		$friend = new cQuickFriend ();
		
		$friend->SetCallback ( 'CreateLocalToken', array ( $this, '_CreateLocalToken' ) );
		$friend->SetCallback ( 'LogNetworkRequest', array ( $this, '_LogNetworkRequest' ) );
		
		$account = $pData['account'];
		$request = $pData['request'];
		
		$result = $friend->Remove ( $account, $request );
		
		return ( $result );
	}
	
	public function OnFriendApprove ( $pData ) {
		
		if (!class_exists ( 'cQuickFriend' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickfriend.php' );
		
		$friend = new cQuickFriend ();
		
		$friend->SetCallback ( 'CreateLocalToken', array ( $this, '_CreateLocalToken' ) );
		$friend->SetCallback ( 'LogNetworkRequest', array ( $this, '_LogNetworkRequest' ) );
		
		$account = $pData['account'];
		$request = $pData['request'];
		
		$result = $friend->Approve ( $account, $request );
		
		return ( $result );
	}
	
	public function CreateMessagesSendlink ( $pData ) {
		
		$request = $pData['request'];
		$account = $pData['account'];
		
		list ( $requestUsername, $requestDomain ) = explode ( '@', $request );
		
		list ( $accountUsername, $accountDomain ) = explode ( '@', $account );
		
		$source = ASD_DOMAIN;
		
		if ( $source == $accountDomain ) {
			//$return = 'http://' . $source . '/profile/' . $requestUsername . '/messages/' . $request;
			$return = '/profile/' . $accountUsername . '/messages/?gACTION=SEND_MESSAGE&gRECIPIENTNAME=' . $requestUsername . '&gRECIPIENTDOMAIN=' . $requestDomain;
		} else {
			$data = array ( '_social' => 'true', '_task' => 'redirect', '_action' => 'messages.compose', '_account' => $account, '_request' => $request, '_source' => $source );
		
			$return = 'http://' . $accountDomain . '/?' . http_build_query ( $data );
		}
		
		return ( $return );
	}
	
	public function CreateFriendAddlink ( $pData ) {
		
		$request = $pData['request'];
		$account = $pData['account'];
		
		list ( $requestUsername, $requestDomain ) = explode ( '@', $request );
		list ( $accountUsername, $accountDomain ) = explode ( '@', $account );
		
		$source = ASD_DOMAIN;
		
		if ( $source == $accountDomain ) {
			$return = 'http://' . $source . '/profile/' . $accountUsername . '/friends/add/' . $request;
		} else {
			$data = array ( '_social' => 'true', '_task' => 'redirect', '_action' => 'friend.add', '_account' => $account, '_request' => $request, '_source' => $source );
		
			$return = 'http://' . $accountDomain . '/?' . http_build_query ( $data );
		}
		
		return ( $return );
	}
	
	public function CreateFriendRemovelink ( $pData ) {
		
		$request = $pData['request'];
		$account = $pData['account'];
		
		list ( $requestUsername, $requestDomain ) = explode ( '@', $request );
		
		list ( $accountUsername, $accountDomain ) = explode ( '@', $account );
		
		$source = ASD_DOMAIN;
		
		if ( $source == $accountDomain ) {
			$return = 'http://' . $source . '/profile/' . $accountUsername . '/friends/remove/' . $request;
		} else {
			$data = array ( '_social' => 'true', '_task' => 'redirect', '_action' => 'friend.remove', '_account' => $account, '_request' => $request, '_source' => $source );
		
			$return = 'http://' . $accountDomain . '/?' . http_build_query ( $data );
		}
		
		return ( $return );
	}
	
	public function CreateUserLink ( $pData ) {
		
		$account = $pData['account'];
		
		list ( $accountUsername, $accountDomain ) = explode ( '@', $account );
		
		$source = ASD_DOMAIN;
		
		if ( $source == $accountDomain ) {
			$return = 'http://' . $source . '/profile/' . $accountUsername;
		} else {
			$data = array ( '_social' => 'true', '_task' => 'redirect', '_action' => 'profile', '_account' => $account, '_source' => $source );
		
			$return = 'http://' . $accountDomain . '/?' . http_build_query ( $data );
		}
		
		return ( $return );
	}
	
	public function OnUserInfo ( $pData ) {
		
		if (!class_exists ( 'cQuickUser' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickuser.php' );
		
		$user = new cQuickUser ();
		
		$account = $pData['account'];
		$source = $pData['source'];
		$request = $pData['request'];
		
		if ( !$request ) return ( false );
		
		list ( $accountUsername, $accountDomain ) = explode ( '@', $account );
		list ( $requestUsername, $requestDomain ) = explode ( '@', $request );
		
		if ( ( $source == $accountDomain ) && ( $source == $requestDomain ) ) {
			
			// Request user is local, and requesting a local user's info, so retrieve it from db
			$userInfo = (object) $this->_UserInfo ( $requestUsername, $request, true );
			
		} else {
			// Requesting a remote user's information
			$user->SetCallback ( 'CheckRemoteToken', array ( $this, '_CheckRemoteToken' ) );
			$user->SetCallback ( 'LogNetworkRequest', array ( $this, '_LogNetworkRequest' ) );
		
			$userInfo = $user->Info ( $account, $source, $request );
			
			
		}
		
		return ( $userInfo );
	}
	
	public function _LogNetworkRequest ( $pRequest, $pResult ) {
		
		$this->GetSys ( 'Logs' )->Add ( 'Network', $pResult, $pRequest );
		
		//$this->GetSys ( 'Benchmark' )->MemStart ( $context );
		//$this->GetSys ( 'Benchmark' )->MemStop ( $context );
		//$this->GetSys ( 'Benchmark' )->Start ( $context );
		//$this->GetSys ( 'Benchmark' )->Stop ( $context );
	}
	
	public function _NodeInformation ( $pSource = null, $pVerified = false) {
		
		$return = array ();
		
		$return['methods'] = array ( 'http' );
		
		$return['tasks'] = array (
			'node.discover',
			'verify',
			'connect.return',
			'connect.check'
		);
		
		$return['trusted'] = array ( );
		
		if ( $pVerified ) {
		}
		
		return ( $return );
	}
	
	public function _CheckLocalToken ( $pUsername, $pTarget, $pToken, $pEncrypt = false ) {
		
		// Verify if the specified token exists in the database.
		$model = new cModel ('LocalTokens');
		$model->Structure();
		
		$query = '
			SELECT Token 
				FROM #__LocalTokens
				WHERE Username = ?
				AND Target = ?
				AND Stamp > DATE_SUB(NOW(), INTERVAL 24 HOUR)
		';
		
		$model->Query ( $query, array ( $pUsername, $pTarget, $pToken ) );
		
		$model->Fetch();
		$token = $model->Get ( 'Token' );
		
		if ( $pEncrypt ) {
			$salt = $this->GetSys ( 'Crypt' )->Salt ( $pToken );
			$token = $this->GetSys ( 'Crypt' )->Encrypt ( $token, $salt );
		}
		
		if ( $token == $pToken ) return ( true );
		
		return ( false );
	}
	
	public function _CreateLocalToken ( $pUsername, $pTarget ) {
	
		// Look for an existing token from the last 24 hours.
		$model = new cModel ('LocalTokens');
		$model->Structure();
	
		$query = '
			SELECT Token 
				FROM #__LocalTokens
				WHERE Username = ?
				And Target = ?
				AND Stamp > DATE_SUB(NOW(), INTERVAL 24 HOUR)
		';
		
		$model->Query ( $query, array ( $pUsername, $pTarget ) );
		
		$model->Fetch();
		$token = $model->Get ( 'Token' );
		
		if ( $token ) {
			// Return the found token.
			return ( $token );
		} else {
			
			// Create a new token and store it.
			if ( !class_exists ( cQuickSocial ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicksocial.php' );
			$social = new cQuickSocial ();
			$token = $social->Token();
			
       		$model->Set ( 'Username', $pUsername );
       		$model->Set ( 'Target', $pTarget );
       		$model->Set ( 'Token', $token );
       		$model->Set ( 'Stamp', NOW() );
       		
       		$model->Save();
       		
       		return ( $token );
		}
	}
	
	public function _CheckRemoteToken ( $pUsername, $pSource, $pEncrypt = false ) {
		
		// Look for an existing token from the last 24 hours.
		$model = new cModel ('RemoteTokens');
		$model->Structure();
	
		$query = '
			SELECT Token 
				FROM #__RemoteTokens
				WHERE Username = ?
				AND Source = ?
				AND Stamp > DATE_SUB(NOW(), INTERVAL 24 HOUR)
		';
			
		$model->Query ( $query, array ( $pUsername, $pSource ) );
		
		$model->Fetch();
		$token = $model->Get ( 'Token' );
		
		if ( $pEncrypt ) $token = $this->GetSys ( 'Crypt' )->Encrypt ( $token );
			
		if ( $token ) return ( $token );
		
		return ( false );
	}
	
	public function _CreateRemoteToken ( $pUsername, $pSource, $pToken ) {
		
		$model = new cModel ('RemoteTokens');
		$model->Structure();

		// Delete old tokens
		
		$query = '
			DELETE FROM #__RemoteTokens
				WHERE Username = ?
				AND Source = ?
		';
			
		$model->Query ( $query, array ( $pUsername, $pSource ) );
		
		// Store the verified token
		$model->Set ( 'Username', $pUsername );
		$model->Set ( 'Source', $pSource );
		$model->Set ( 'Token', $pToken );
		$model->Set ( 'Address', $_SERVER['REMOTE_ADDR'] );
		$model->Set ( 'Host', $_SERVER['REMOTE_HOST'] );
		$model->Set ( 'Stamp', NOW() );
		
		$model->Save();
		
		return ( true );
		
	}
	
	public function _CheckLogin ( $pUsername ) {
		
		$cookie = $_COOKIE['gLOGINSESSION'];
		
		$session = new cModel ( 'userSessions' );
		
		// Get the session by the identifier
		$criteria = array ( 'Identifier' => $cookie );
		$session->Retrieve ( $criteria );
		$session->Fetch();
		$uID = $session->get ( 'userAuth_uID' );
		
		if ( !$uID ) return ( false );
		
		$authorization = new cModel ( 'userAuthorization' );
		
		$criteria = array ( 'uID' => $uID );
		$authorization->Retrieve ( $criteria );
		$authorization->Fetch();
		$Username = strtolower ( $authorization->get ( 'Username' ) );
		$pUsername = strtolower ( $pUsername );
		
		if ( $Username == $pUsername ) return ( true );
		
		return ( false );
	}
	
	private function _SetRemoteSession( $pUsername, $pDomain ) {
		
		$sessionModel = new cModel ( 'authSessions' );
		
		// Delete current session id's.
		$criteria = array ( 'Username' => $pUsername, 'Domain' => $pDomain );
		
		$sessionModel->Delete ( $criteria );
		
		// Create a unique session identifier.
        $identifier = md5(uniqid(rand(), true));
        
		// Set the session database information.
		$sessionModel->Set ( 'Username', $pUsername );
		$sessionModel->Set ( 'Domain', $pDomain );
		$sessionModel->Set ( 'Identifier', $identifier );
		$sessionModel->Set ( 'Stamp', NOW() );
		$sessionModel->Set ( 'Address', $_SERVER['REMOTE_ADDR'] );
		$sessionModel->Set ( 'Host', $_SERVER['REMOTE_HOST'] );
		$sessionModel->Set ( 'Fullname', '' );
		
		$sessionModel->Save ();
		
		// Set the cookie
      	if ( !setcookie ('gREMOTELOGINSESSION', $identifier, time()+60*60*24*30, '/') ) {
      		// @todo Set error that we couldn't set the cookie.
      		
      		return ( false );
      	};
		
		// Update the userInformation table
		$infoModel = new cModel ( 'userInformation' );
		
		return ( true );
	}
	
	private function _Bounce ( ) {
		
		$URI = $_SERVER['REQUEST_URI'];
		
		if ( preg_match ( "/.*_bounce=(.*)/", $URI, $matches ) ) {
			$bounceRequest = $matches[1];
			
			list ( $bounce, $null ) = explode ( '&', $bounceRequest, 2 );
			list ( $username, $domain ) = explode ( '@', $bounce, 2 );
			
			$return = $_SERVER['REQUEST_URI'];
			
			$return = str_replace ( '?_bounce=' . $bounce, '', $return );
			$return = str_replace ( '&_bounce=' . $bounce, '', $return );
			$return = str_replace ( '_bounce=' . $bounce, '', $return );
			
			$return = ASD_DOMAIN . $return;
			
			$data = array ( 'username' => $username, 'domain' => $domain, 'return' => $return );
			
			$this->OnLoginAuthenticate ( $data );
			exit;
			
		} elseif ( ( isset ( $_REQUEST['_bounce'] ) ) && ( $bounce = $_REQUEST['_bounce'] ) and ( $_REQUEST['_social'] != 'true' ) ) {
			// @note This is for legacy support, the old system turns all links to POST forms. 
			// @note We may or may not keep that for the new system.
			list ( $username, $domain ) = explode ( '@', $bounce, 2 );
			
			$return = ASD_DOMAIN . $_SERVER['REQUEST_URI'];
			
			$data = array ( 'username' => $username, 'domain' => $domain, 'return' => $return );
			
			$this->OnLoginAuthenticate ( $data );
			exit;
		}
		
		return ( false );
	}
	
	private function _FindClosestValue ( $pNeedle, $pHaystack ) {
		
		$diffs = array ();
		
		foreach ( $pHaystack as $value) {
			$diff = $pNeedle - $value;
			if ( $diff < 0 ) $diff *= -1;
			
			$diffs[$value] = $diff;
		}
		
		$value_diff = min ( $diffs );
		$diffs = array_flip ( $diffs );
		$value = $diffs[$value_diff];
		
		return ( $value );
	}
	
	public function _UserIcon ( $pUsername, $pWidth = 128, $pHeight = 128 ) {
		
		// Find closest resolution values
		$width = $this->_FindClosestValue ( $pWidth, array ( 32, 64, 128 ) );
		$height = $width;
		
		// Get the filename size identifier.
		switch ( $width ) {
			case 128:
				$size = 'm';
			break;
			case 64:
				$size = 's';
			break;
			case 32:
			default:
				$size = 't';
			break;
		}
		
		// Check for new icons.
		$location = ASD_PATH . '_storage' . DS . 'photos' . DS . $pUsername . DS;
		$file = $location . 'profile.' . $size . '.jpg';
		
		if ( file_exists ( $file ) ) {
			$icon = imagecreatefromjpeg ( $file );
		} else {
			/* 
			 * @todo Remove this eventually once new photo system is used.
			 * 
			 */
			$legacy_file = ASD_PATH . '_storage' . DS . 'legacy' . DS . 'photos' . DS . $pUsername . DS . 'profile.jpg';
			
			if ( !file_exists ( $legacy_file ) ) {
				header ('Content-type: image/gif');
				$icon = imagecreatefromgif ( ASD_PATH . 'themes/default/images/noicon.gif' );
				imagegif ( $icon ) or die ( "Couldn't" );
				imagedestroy ( $icon );
				return ( true );
			} else {
				if ( !is_dir ( $location ) ) rmkdir ( $location );
			
				if ( is_writable ( $location ) ) {
					$icon = imagecreatefromjpeg ( $legacy_file );
					$new_icon = $this->GetSys ( 'Image' )->ResizeAndCrop ( $icon, $width, $height );
					$icon = $new_icon;
					imagejpeg ( $new_icon, $file );
					chmod ( $file, 0777 );
				} else {
					header ('Content-type: image/gif');
					$icon = imagecreatefromgif ( ASD_PATH . 'themes/default/images/noicon.gif' );
					imagegif ( $icon );
					imagedestroy ( $icon );
					return ( true );
				}
			}
		}
		
		// Use the legacy icon
		
		header ('Content-type: image/jpeg');
		
		imagejpeg ( $icon );
		
		imagedestroy ( $icon );
		
		return ( true );
	}
	
	public function _UserInfo ( $pAccount, $pRequest, $pVerified = false ) {
		
		$auth = new cModel ('userAuthorization');
		$auth->Structure();
		
		$auth->Retrieve ( array ( 'Username' => $pAccount ) );
		$auth->Fetch();
		
		if ( !$auth->Get ( 'Username' ) ) return ( false );
		
		$profile = new cModel ('userProfile');
		$profile->Structure();
		
		$profile->Retrieve ( array ( 'userAuth_uID' => $auth->Get ( 'uID' ) ) );
		$profile->Fetch();
		
		// Get the user's full name or alias
		if ( $profile->Get ( 'Alias' ) ) {
			$return['fullname'] = $profile->Get ( 'Alias' );
		} else {
			$return['fullname'] = $profile->Get ( 'Fullname' );
		}
		
		// Check whether the user is currently online only if verified
		if ( $pVerified ) {
			$info = new cModel ('userInformation');
			$info->Structure();
		
			$info->Retrieve ( array ( 'userAuth_uID' => $auth->Get ( 'uID' ) ) );
			$info->Fetch();
		
			$currently = strtotime ('now');
			$online = strtotime ( $info->Get ( 'OnlineStamp' ) );
		
			$difference = $currently - $online;
      
			if ($difference < 180) 
				$return['online'] = 'true';
			else
				$return['online'] = 'false';
		}
		
		// Get the user's friends list for verified users only.
		if ( $pVerified ) {
			$friends = new cModel ('FriendInformation');
			$friends->Structure();
		
			$friends->Retrieve ( array ( 'Owner_FK' => $auth->Get ( 'uID' ), 'Verification' => '1' ) );
			$return['friends'] = array ();
			while ( $friends->Fetch() ) {
				$return['friends'][] = $friends->Get ( 'Username' ) . '@' . $friends->Get ( 'Domain' );
			}
		}
		
		// Check if the requesting user is blocked.
		$return['blocked'] = 'false';
		
		// Get this user's location
		$return['location'] = '';
		
		// Get this user's status
		$return['status'] = '';
		
		return ( $return );
	}
	
	public function _Redirect ( $pAction, $pAccount, $pRequest = null, $pSource = null ) {
		
		list ( $accountUsername, $accountDomain ) = explode ( '@', $pAccount );
		list ( $requestUsername, $requestDomain ) = explode ( '@', $pRequest );
		
		switch ( $pAction ) {
			case 'friend.add':
				$redirect = '/profile/' . $accountUsername . '/friends/add/' . $pRequest;
			break;
			case 'friend.remove':
				$redirect = '/profile/' . $accountUsername . '/friends/remove/' . $pRequest;
			break;
			case 'messages.compose':
				// New
				// $redirect = '/profile/' . $focus . '/messages/compose/' . $pRequest;
				
				// Legacy
				$redirect = '/profile/' . $accountUsername . '/messages/?gACTION=SEND_MESSAGE&gRECIPIENTNAME=' . $requestUsername . '&gRECIPIENTDOMAIN=' . $requestDomain;
			break;
			case 'messages':
			break;
			case 'approval':
			break;
			case 'notifications':
			break;
			case 'profile':
				$redirect = '/profile/' . $accountUsername . '/';
			break;
			default:
				$redirect = '/';
			break;
		}
		
		header ( 'Location:' . $redirect );
		exit;
	}
	
	public function _FriendAdd ( $pAccount, $pRequest ) {
		
		$userModel = new cModel ( 'userAuthorization' );
		$userModel->Structure();
		
		$profileModel = new cModel ( 'userProfile' );
		$profileModel->Structure();
		
		$friendModel = new cModel ( 'FriendInformation' );
		$friendModel->Structure();
		
		list ( $requestUsername, $requestDomain ) = explode ( '@', $pRequest );
		list ( $accountUsername, $accountDomain ) = explode ( '@', $pAccount );
		
		$userModel->Retrieve ( array ( 'Username' => $requestUsername ) );
		$userModel->Fetch();
		
		$requestUsername_uID = $userModel->Get ( 'uID' );
		
		if ( !$requestUsername_uID ) return ( false );
		
		$friendModel->Retrieve ( array ( 'Owner_FK' => $requestUsername_uID, 'Username' => $accountUsername, 'Domain' => $accountDomain ) );
		
		$profileModel->Retrieve ( array ( 'userAuth_uID' => $requestUsername_uID ) );
		$profileModel->Fetch();
		
		$data = array ( 'Email' => $profileModel->Get ( 'Email' ), 'Recipient' => $pRequest, 'Sender' => $pAccount );
		$this->GetSys ( 'Components' )->Talk ( 'Friends', 'NotifyAdd', $data );
		
		if ( $friendModel->Get ( 'Total' ) == 0 ) {
			// No record found, so create one.
			$friendModel->Protect ( 'Friend_PK' );
			$friendModel->Set ( 'Owner_FK', $requestUsername_uID );
			$friendModel->Set ( 'Username', $accountUsername );
			$friendModel->Set ( 'Domain', $accountDomain );
			$friendModel->Set ( 'Verification', 2 );
			$friendModel->Set ( 'Created', NOW() );
			$friendModel->Set ( 'Updated', NOW() );
			$friendModel->Save();
			
			return ( true );
		} else {
			// Record already exists, so just return true;
			return ( true );
		}
	}
	
	public function _FriendApprove ( $pAccount, $pRequest ) {
		
		$userModel = new cModel ( 'userAuthorization' );
		$userModel->Structure();
		
		$friendModel = new cModel ( 'FriendInformation' );
		$friendModel->Structure();
		
		list ( $requestUsername, $requestDomain ) = explode ( '@', $pRequest );
		list ( $accountUsername, $accountDomain ) = explode ( '@', $pAccount );
		
		$profileModel = new cModel ( 'userProfile' );
		$profileModel->Structure();
		
		$userModel->Retrieve ( array ( 'Username' => $requestUsername ) );
		$userModel->Fetch();
		
		$requestUsername_uID = $userModel->Get ( 'uID' );
		
		if ( !$requestUsername_uID ) return ( false );
		
		$friendModel->Retrieve ( array ( 'Owner_FK' => $requestUsername_uID, 'Username' => $accountUsername, 'Domain' => $accountDomain, 'Verification' => '()' . '1,3' ) );
		
		$profileModel->Retrieve ( array ( 'userAuth_uID' => $requestUsername_uID ) );
		$profileModel->Fetch();
		
		if ( $friendModel->Get ( 'Total' ) > 0 ) {
			// Record found, so approve it.
			$friendModel->Fetch();
			$friendModel->Set ( 'Owner_FK', $requestUsername_uID );
			$friendModel->Set ( 'Username', $accountUsername );
			$friendModel->Set ( 'Domain', $accountDomain );
			$friendModel->Set ( 'Verification', 1 );
			$friendModel->Set ( 'Created', NOW() );
			$friendModel->Set ( 'Updated', NOW() );
			$friendModel->Save();
			
			$request = $requestUsername . '@' . ASD_DOMAIN;
			$friends = $this->GetSys ( 'Components' )->Talk ( 'Friends', 'Friends', array ( 'Target' => $requestUsername_uID ) );
			
			$notifyData = array ( 'OwnerId' => $userModel->Get ( 'uID' ), 'Friends' => $friends, 'ActionOwner' => $pRequest, 'Action' => 'friended', 'SubjectOwner' => $pAccount, 'Context' => 'friends' );
			$this->GetSys ( 'Components' )->Talk ( 'Newsfeed', 'Notify', $notifyData );
			
			$data = array ( 'Email' => $profileModel->Get ( 'Email' ), 'Recipient' => $pRequest, 'Sender' => $pAccount );
			$this->GetSys ( 'Components' )->Talk ( 'Friends', 'NotifyApprove', $data );
		
			return ( true );
		} else {
			// Record doesn't exist, so return false;
			return ( false );
		}
	}
	
	public function _FriendRemove ( $pAccount, $pRequest ) {
		
		$userModel = new cModel ( 'userAuthorization' );
		$userModel->Structure();
		
		$friendModel = new cModel ( 'FriendInformation' );
		$friendModel->Structure();
		
		list ( $requestUsername, $requestDomain ) = explode ( '@', $pRequest );
		list ( $accountUsername, $accountDomain ) = explode ( '@', $pAccount );
		
		$userModel->Retrieve ( array ( 'Username' => $requestUsername ) );
		$userModel->Fetch();
		
		$requestUsername_uID = $userModel->Get ( 'uID' );
		
		if ( !$requestUsername_uID ) return ( false );
		
		$friendModel->Retrieve ( array ( 'Owner_FK' => $requestUsername_uID, 'Username' => $accountUsername, 'Domain' => $accountDomain ) );
		
		if ( $friendModel->Get ( 'Total' ) > 0 ) {
			// Record found, so approve it.
			$friendModel->Fetch();
			$friendModel->Delete();
			
			return ( true );
		} else {
			// Record doesn't exist, so just return true;
			return ( true );
		}
	}
	
	public function OnFeedSynchronize ( $pData = array ( ) ) {
		
		if (!class_exists ( 'cQuickFeed' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickfeed.php' );
		
		$feed = new cQuickFeed ();
		
		$feed->SetCallback ( 'CreateLocalToken', array ( $this, '_CreateLocalToken' ) );
		$feed->SetCallback ( 'LogNetworkRequest', array ( $this, '_LogNetworkRequest' ) );
		
		$Account = $pData['Account'];
		$Recipient = $pData['Recipient'];
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
		$Created = $pData['Created'];
		$Updated = $pData['Updated'];
		
		list ( $username, $domain ) = explode ( '@', $Recipient );
			
		$result = $feed->Synchronize ( $domain, $Account, $Recipient, $Action, $ActionOwner, $ActionLink, $SubjectOwner, $Context, $ContextOwner, $ContextLink, $Icon, $Comment, $Description, $Identifier, $Created, $Updated );
		
		if ( $result->success == 'true' ) return ( true );
		
		return ( false );
	}
	
	public function _FeedSynchronize ( $pRecipient, $pAction, $pActionOwner, $pActionLink, $pSubjectOwner, $pContext, $pContextOwner, $pContextLink, $pIcon, $pComment, $pDescription, $pIdentifier, $pCreated, $pUpdated ) {
		
		list ( $recipientUsername, $null ) = explode ( '@', $pRecipient );
		
		$RecipientAccount = $this->GetSys ( 'Components' )->Talk ( 'User', 'Account', array ( 'Username' => $recipientUsername ) );
		
		$OwnerId = $RecipientAccount->Id;
		$data['OwnerId'] = $RecipientAccount->Id;
		$data['Action'] = $pAction;
		$data['ActionOwner'] = $pActionOwner;
		$data['ActionLink'] = $pActionLink;
		$data['SubjectOwner'] = $pSubjectOwner;
		$data['Context'] = $pContext;
		$data['ContextOwner'] = $pContextOwner;
		$data['ContextLink'] = $pContextLink;
		$data['Icon'] = $pIcon;
		$data['Comment'] = $pComment;
		$data['Description'] = $pDescription;
		$data['Identifier'] = $pIdentifier;
		$data['Created'] = $pCreated;
		$data['Updated'] = $pUpdated;
		
		$result = $this->GetSys ( 'Components' )->Talk ( 'Newsfeed', 'AddToIncoming', $data );
		
		return ( $result );
	}
}