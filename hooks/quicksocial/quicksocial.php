<?php
/**
 * @version      $Id$
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
			$model = new cModel ( "NodeDiscovery");
		}
		
		$node = new cQuickNode ();
		
		$domain = $pData['domain'];
		
		$node->SetCallback ( "CheckLocalToken", array ( $this, '_CheckLocalToken' ) );
		$node->SetCallback ( "CreateLocalToken", array ( $this, '_CreateLocalToken' ) );
		
		$node = $node->Discover ( $domain );
		
		return ( $node );
	}
	
	public function BeginSystemInitialize ( $pData = null ) {
		
		// Bounce if requested.
		$this->_Bounce ( );
		
		$social = $this->GetSys ( "Request" )->Get ( "_social" );
		
		if ( $social != "true" ) return ( false );
		
		$task = $this->GetSys ( "Request" )->Get ( "_task" );
		
		switch ( $task ) {
			case 'verify':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicksocial.php' );
				 
				$social = new cQuickSocial ();
				$social->SetCallback ( "CheckLocalToken", array ( $this, '_CheckLocalToken' ) );
				
				$social->ReplyToVerify();
				exit;
			break;
			case 'verify.remote':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicksocial.php' );
				 
				$social = new cQuickSocial ();
				$social->SetCallback ( "CheckLocalToken", array ( $this, '_CheckLocalToken' ) );
				
				$social->ReplyToRemoteVerify();
				exit;
			break;
			case 'connect.check':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickconnect.php' );
				
				$connect = new cQuickConnect ();
				$connect->SetCallback ( "CheckLogin", array ( $this, '_CheckLogin' ) );
				$connect->SetCallback ( "CreateLocalToken", array ( $this, '_CreateLocalToken' ) );
				
				$connect->Check();
				exit;
			break;
			case 'connect.return':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickconnect.php' );
				 
				$connect = new cQuickConnect ();
				$connect->SetCallback ( "CreateRemoteToken", array ( $this, '_CreateRemoteToken' ) );
				$connect->SetCallback ( "CheckRemoteToken", array ( $this, '_CheckRemoteToken' ) );
				
				$verified = $connect->Process();
				
				if ( $verified->success == "true" ) {
					$username = $verified->username;
					$domain = $verified->domain;
					$returnTo = $verified->returnTo;
					if ( !strstr ( $returnTo, "://" ) ) $returnTo = "http://" . $returnTo;
					
					$this->_SetRemoteSession( $username, $domain );
					
					header ("Location:$returnTo");
		
					exit;
				} else {
					
					$this->GetSys ( "Session" )->Context ( "login.login.4.remote" );
					$this->GetSys ( "Session" )->Set ( "Message", $verified->error );
					$this->GetSys ( "Session" )->Set ( "Identity", $verified->username . '@' . $verified->domain );
					$this->GetSys ( "Session" )->Set ( "Error", true );
					
					$redirect = 'http://' . ASD_DOMAIN . '/login/remote/';
					header('Location: ' . $redirect);
				}
				
				exit;
			break;
			case 'node.discover':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicknode.php' );
				 
				$node = new cQuickNode ();
				$node->SetCallback ( "CheckRemoteToken", array ( $this, '_CheckRemoteToken' ) );
				$node->SetCallback ( "CreateRemoteToken", array ( $this, '_CreateRemoteToken' ) );
				$node->SetCallback ( "NodeInformation", array ( $this, '_NodeInformation' ) );
				$node->ReplyToDiscover();
				exit;
			break;
			case 'user.icon':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickuser.php' );
				 
				$user = new cQuickUser ();
				$user->SetCallback ( "UserIcon", array ( $this, '_UserIcon' ) );
				$user->ReplyToIcon();
				exit;
			break;
			case 'user.info':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickuser.php' );
				 
				$user = new cQuickUser ();
				$user->SetCallback ( "CheckLocalToken", array ( $this, '_CheckLocalToken' ) );
				$user->SetCallback ( "CheckRemoteToken", array ( $this, '_CheckRemoteToken' ) );
				$user->SetCallback ( "UserInfo", array ( $this, '_UserInfo' ) );
				$user->ReplyToInfo();
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
		
		$node->SetCallback ( "CheckLocalToken", array ( $this, '_CheckLocalToken' ) );
		$node->SetCallback ( "CreateLocalToken", array ( $this, '_CreateLocalToken' ) );
		$node->SetCallback ( "LogNetworkRequest", array ( $this, '_LogNetworkRequest' ) );
		
		$node = $node->Discover ( $domain );
		
		if ( $node->success != "true" ) {
			$return = new stdClass();
			if ( $node->error ) {
				$return->error = $node->error;
			} else {
				$return->error = "Invalid Node";
			}
			
			return ( $return );
		}
		
		if (!class_exists ( 'cQuickConnect' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickconnect.php' );
		
		$connect = new cQuickConnect ();
		
		$username = $pData['username'];
		$domain = $pData['domain'];
		
		// @todo Use HTTP for now, but allow for other methods later.
		$method = "http";
		
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
				$legacy_file = ASD_PATH . "_storage" . DS . "legacy" . DS . "photos" . DS . $username . DS . "profile.jpg";
				
				if ( file_exists ( $legacy_file ) ) {
				
					if ( !is_dir ( $location ) ) rmkdir ( $location );
				
					if ( is_writable ( $location ) ) {
						$icon = imagecreatefromjpeg ( $legacy_file );
						$new_icon = $this->_ResizeAndCrop ( $icon, $width, $height );
						imagejpeg ( $new_icon, $file );
						chmod ( $file, 0777 );
					}
					
				}
			}
			
			$url = 'http://' . ASD_DOMAIN . '/_storage' . '/' . 'photos' . '/' . $username . '/';
			$return = $url . 'profile.' . $size . '.jpg';
		} else {
			$data = array ( '_social' => 'true', '_task' => 'user.icon', '_request' => $username, '_width' => $width, '_height' => $height );
		
			$return = 'http://' . $domain . '/?' . http_build_query ( $data );
		}
		
		return ( $return );
	}
	
	public function CreateFriendAddlink ( $pData ) {
		
		$target = $pData['currentDomain'];
		
		$request = $pData['currentUsername'];
		
		$account = $pData['username'] . '@' . $pData['domain'];
		
		$data = array ( '_social' => 'true', '_task' => 'friend.action', '_action' => 'add', '_request' => $request, '_account' => $account );
		
		$return = 'http://' . $target . '/?' . http_build_query ( $data );
		
		return ( $return );
	}
	
	public function CreateFriendRemovelink ( $pData ) {
		
		$target = $pData['currentDomain'];
		
		$request = $pData['currentUsername'];
		
		$account = $pData['username'] . '@' . $pData['domain'];
		
		$data = array ( '_social' => 'true', '_task' => 'friend.action', '_action' => 'remove', '_request' => $request, '_account' => $account );
		
		$return = 'http://' . $target . '/?' . http_build_query ( $data );
		
		return ( $return );
	}
	
	public function OnUserInfo ( $pData ) {
		
		if (!class_exists ( 'cQuickUser' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quickuser.php' );
		
		$user = new cQuickUser ();
		
		$account = $pData['account'];
		$source = $pData['source'];
		$request = $pData['request'];
		
		list ( $accountUsername, $accountDomain ) = explode ( '@', $account );
		list ( $requestUsername, $requestDomain ) = explode ( '@', $request );
		
		if ( ( $source == $accountDomain ) && ( $source == $requestDomain ) ) {
			
			// Request user is local, and requesting a local user's info, so retrieve it from db
			$userInfo = (object) $this->_UserInfo ( $accountUsername, $request, true );
			
		} else {
			// Requesting a remote user's information
			$user->SetCallback ( "CheckRemoteToken", array ( $this, '_CheckRemoteToken' ) );
			$user->SetCallback ( "LogNetworkRequest", array ( $this, '_LogNetworkRequest' ) );
		
			$userInfo = $user->Info ( $account, $source, $request );
			
			
		}
		
		return ( $userInfo );
	}
	
	public function _LogNetworkRequest ( $pRequest, $pResult ) {
		
		$this->GetSys ( "Logs" )->Add ( "Network", $pResult, $pRequest );
		
		//$this->GetSys ( "Benchmark" )->MemStart ( $context );
		//$this->GetSys ( "Benchmark" )->MemStop ( $context );
		//$this->GetSys ( "Benchmark" )->Start ( $context );
		//$this->GetSys ( "Benchmark" )->Stop ( $context );
	}
	
	public function _NodeInformation ( $pSource = null, $pVerified = false) {
		
		$return = array ();
		
		$return['methods'] = array ( "http" );
		
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
		$model = new cModel ("LocalTokens");
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
		$token = $model->Get ( "Token" );
		
		if ( $pEncrypt ) {
			$salt = $this->GetSys ( "Crypt" )->Salt ( $pToken );
			$token = $this->GetSys ( "Crypt" )->Encrypt ( $token, $salt );
		}
		
		if ( $token == $pToken ) return ( true );
		
		return ( false );
	}
	
	public function _CreateLocalToken ( $pUsername, $pTarget ) {
	
		// Look for an existing token from the last 24 hours.
		$model = new cModel ("LocalTokens");
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
		$token = $model->Get ( "Token" );
		
		if ( $token ) {
			// Return the found token.
			return ( $token );
		} else {
			
			// Create a new token and store it.
			if ( !class_exists ( cQuickSocial ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicksocial.php' );
			$social = new cQuickSocial ();
			$token = $social->Token();
			
       		$model->Set ( "Username", $pUsername );
       		$model->Set ( "Target", $pTarget );
       		$model->Set ( "Token", $token );
       		$model->Set ( "Stamp", NOW() );
       		
       		$model->Save();
       		
       		return ( $token );
		}
	}
	
	public function _CheckRemoteToken ( $pUsername, $pSource, $pEncrypt = false ) {
		
		// Look for an existing token from the last 24 hours.
		$model = new cModel ("RemoteTokens");
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
		$token = $model->Get ( "Token" );
		
		if ( $pEncrypt ) $token = $this->GetSys ( "Crypt" )->Encrypt ( $token );
			
		if ( $token ) return ( $token );
		
		return ( false );
	}
	
	public function _CreateRemoteToken ( $pUsername, $pSource, $pToken ) {
		
		$model = new cModel ("RemoteTokens");
		$model->Structure();

		// Delete old tokens
		
		$query = '
			DELETE FROM #__RemoteTokens
				WHERE Username = ?
				AND Source = ?
		';
			
		$model->Query ( $query, array ( $pUsername, $pSource ) );
		
		// Store the verified token
		$model->Set ( "Username", $pUsername );
		$model->Set ( "Source", $pSource );
		$model->Set ( "Token", $pToken );
		$model->Set ( "Address", $_SERVER['REMOTE_ADDR'] );
		$model->Set ( "Host", $_SERVER['REMOTE_HOST'] );
		$model->Set ( "Stamp", NOW() );
		
		$model->Save();
		
		return ( true );
		
	}
	
	public function _CheckLogin ( $pUsername ) {
		
		$cookie = $_COOKIE['gLOGINSESSION'];
		
		$session = new cModel ( "userSessions" );
		
		// Get the session by the identifier
		$criteria = array ( "Identifier" => $cookie );
		$session->Retrieve ( $criteria );
		$session->Fetch();
		$uID = $session->get ( "userAuth_uID" );
		
		if ( !$uID ) return ( false );
		
		$authorization = new cModel ( "userAuthorization" );
		
		$criteria = array ( "uID" => $uID );
		$authorization->Retrieve ( $criteria );
		$authorization->Fetch();
		$Username = strtolower ( $authorization->get ( "Username" ) );
		$pUsername = strtolower ( $pUsername );
		
		if ( $Username == $pUsername ) return ( true );
		
		return ( false );
	}
	
	private function _SetRemoteSession( $pUsername, $pDomain ) {
		
		$sessionModel = new cModel ( "authSessions" );
		
		// Delete current session id's.
		$criteria = array ( "Username" => $pUsername, "Domain" => $pDomain );
		
		$sessionModel->Delete ( $criteria );
		
		// Create a unique session identifier.
        $identifier = md5(uniqid(rand(), true));
        
		// Set the session database information.
		$sessionModel->Set ( "Username", $pUsername );
		$sessionModel->Set ( "Domain", $pDomain );
		$sessionModel->Set ( "Identifier", $identifier );
		$sessionModel->Set ( "Stamp", NOW() );
		$sessionModel->Set ( "Address", $_SERVER['REMOTE_ADDR'] );
		$sessionModel->Set ( "Host", $_SERVER['REMOTE_HOST'] );
		$sessionModel->Set ( "Fullname", "Bob Barker" );
		
		$sessionModel->Save ();
		
		// Set the cookie
      	if ( !setcookie ("gREMOTELOGINSESSION", $identifier, time()+60*60*24*30, '/') ) {
      		// @todo Set error that we couldn't set the cookie.
      		
      		return ( false );
      	};
		
		// Update the userInformation table
		$infoModel = new cModel ( "userInformation" );
		
		return ( true );
	}
	
	private function _Bounce ( ) {
		
		$URI = $_SERVER['REQUEST_URI'];
		
		if ( preg_match ( "/.*_bounce=(.*)/", $URI, $matches ) ) {
			$bounceRequest = $matches[1];
			
			list ( $bounce, $null ) = explode ( '&', $bounceRequest, 2 );
			list ( $username, $domain ) = explode ( '@', $bounce, 2 );
			
			$return = $_SERVER["REQUEST_URI"];
			
			$return = str_replace ( "?_bounce=" . $bounce, "", $return );
			$return = str_replace ( "&_bounce=" . $bounce, "", $return );
			$return = str_replace ( "_bounce=" . $bounce, "", $return );
			
			$return = ASD_DOMAIN . $return;
			
			$data = array ( "username" => $username, "domain" => $domain, "return" => $return );
			
			$this->OnLoginAuthenticate ( $data );
			exit;
			
		} elseif ( ( isset ( $_REQUEST['_bounce'] ) ) && ( $bounce = $_REQUEST['_bounce'] ) and ( $_REQUEST['_social'] != "true" ) ) {
			// @note This is for legacy support, the old system turns all links to POST forms. 
			// @note We may or may not keep that for the new system.
			list ( $username, $domain ) = explode ( '@', $bounce, 2 );
			
			$return = ASD_DOMAIN . $_SERVER['REQUEST_URI'];
			
			$data = array ( "username" => $username, "domain" => $domain, "return" => $return );
			
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
			
			if ( !file_exists ( $legacy_file ) ) return ( false );
			
			if ( !is_dir ( $location ) ) rmkdir ( $location );
			
			if ( is_writable ( $location ) ) {
				$icon = imagecreatefromjpeg ( $legacy_file );
				$new_icon = $this->_ResizeAndCrop ( $icon, $width, $height );
				$icon = $new_icon;
				imagejpeg ( $new_icon, $file );
				chmod ( $file, 0777 );
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
			$friends = new cModel ('friendInformation');
			$friends->Structure();
		
			$friends->Retrieve ( array ( 'userAuth_uID' => $auth->Get ( 'uID' ) ) );
			$return['friends'] = array ();
			while ( $friends->Fetch() ) {
				$return['friends'][] = $friends->Get ( 'Username' ) . '@' . $friends->Get ( 'Domain' );
			}
		}
		
		// Check if the requesting user is blocked.
		$return['blocked'] = 'false';
		
		// Get this user's location
		$return['location'] = "";
		
		// Get this user's status
		$return['status'] = "";
		
		return ( $return );
	}
	
	private function _ResizeAndCrop ($pResource, $pNewWidth, $pNewHeight ) {
		
		$originalWidth = imagesx ( $pResource  );
		$originalHeight = imagesy ( $pResource  );
		
		if ( $originalHeight == $originalWidth ) {
			// Proportion is the same
			$newwidth = $pNewWidth; $newheight = $pNewHeight;
			$startx = 0; $starty = 0;
			$endx = $pNewWidth; $endy = $pNewHeight;
		} elseif ( $originalHeight > $originalWidth ) {
			// Proportion is vertical
			$newwidth = $pNewWidth;
			$newheight = ( $pNewWidth / $originalWidth ) * $originalHeight;
			$newheight = floor ( $newheight );
			$startx = 0; $starty = floor( ( ( $newheight - $pNewHeight ) / 2 ) );
			$endy = $pNewWidth; $endy = $newheight - ceil ( ( ( $newheight - $pNewHeight ) / 2 ) );
		} else {
			// Proportion is horizontal
			$newwidth = ( $pNewHeight / $originalHeight ) * $originalWidth;
			$newwidth = floor ( $newwidth );
			$newheight = $pNewHeight;
			$startx = floor ( ( ( $newwidth - $pNewWidth ) / 2 ) );  $starty = 0;
			$endx = $newwidth - ceil ( ( ( $newwidth - $pNewWidth ) / 2 ) );  $endy = $pNewHeight;
		} // if
		
		/*
		echo $originalWidth, "<br />";
		echo $originalHeight, "<br /><br />";
		echo $pNewWidth, "<br />";
		echo $pNewHeight, "<br /><br />";
		echo $newwidth, "<br />";
		echo $newheight, "<br /><br />";
		echo $startx, "<br />";
		echo $starty, "<br />";
		echo $endx, "<br />";
		echo $endy, "<br />";
		exit;
		*/
		  
		$src_img = imagecreatetruecolor ( $originalWidth, $originalHeight );
		imagecopy( $src_img, $pResource, 0, 0, 0, 0, $originalWidth, $originalHeight );

		$intermediary = imagecreatetruecolor ( $newwidth, $newheight );
		$result = imagecreatetruecolor ( $pNewWidth, $pNewHeight );

		// Resize image.
		imagecopyresampled ( $intermediary, $src_img, 0, 0, 0, 0, $newwidth, $newheight, $originalWidth, $originalHeight );
		
		// Crop image.
		imagecopy ( $result, $intermediary, 0, 0, $startx, $starty, $pNewWidth, $pNewHeight );

		imagedestroy ( $intermediary );
		
		return ( $result );
	} // ResizeAndCrop
    
}