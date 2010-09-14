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
				$connect->SetCallback ( "CreateRemoteToken", array ( $this, '_CreateLocalToken' ) );
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
					
					$this->GetSys ( "Session" )->Context ( "login.login.2.login" );
					$this->GetSys ( "Session" )->Set ( "Message", $verified->error );
					$this->GetSys ( "Session" )->Set ( "Identity", $verified->username . '@' . $verified->domain );
					$this->GetSys ( "Session" )->Set ( "Error", true );
					
					$redirect = 'http://' . $_SERVER['HTTP_HOST'] . '/login/remote/';
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
	
	public function _CheckLocalToken ( $pUsername, $pTarget, $pToken ) {
		
		if (!class_exists ( 'cQuickSocial' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicksocial.php' );
				
		$social = new cQuickSocial ();
		
		// Verify if the specified token exists in the database.
		$model = new cModel ("LocalTokens");
		$model->Structure();
	
		$query = '
			SELECT Token 
				FROM #__LocalTokens
				WHERE Username = ?
				AND Target = ?
				AND Token = ?
				AND Stamp > DATE_SUB(NOW(), INTERVAL 24 HOUR)
		';
		
		$model->Query ( $query, array ( $pUsername, $pTarget, $pToken ) );
		
		$model->Fetch();
		$token = $model->Get ( "Token" );
		
		if ( $token ) return ( $token );
		
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
	
	public function _CheckRemoteToken ( $pUsername, $pSource, $pToken ) {
		
		if (!class_exists ( 'cQuickSocial' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicksocial.php' );
				
		$social = new cQuickSocial ();
		
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
			
			$return = $_SERVER['HTTP_HOST'] . $return;
			
			$data = array ( "username" => $username, "domain" => $domain, "return" => $return );
			
			$this->OnLoginAuthenticate ( $data );
			exit;
			
		} elseif ( ( isset ( $_REQUEST['_bounce'] ) ) && ( $bounce = $_REQUEST['_bounce'] ) and ( $_REQUEST['_social'] != "true" ) ) {
			// @note This is for legacy support, the old system turns all links to POST forms. 
			// @note We may or may not keep that for the new system.
			list ( $username, $domain ) = explode ( '@', $bounce, 2 );
			
			$return = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			
			$data = array ( "username" => $username, "domain" => $domain, "return" => $return );
			
			$this->OnLoginAuthenticate ( $data );
			exit;
		}
		
		return ( false );
	}
}

