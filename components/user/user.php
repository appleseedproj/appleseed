<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   User
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** User Component
 * 
 * User Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  User
 */
class cUser extends cComponent {
	
	private $_Cache;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function Current ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		if ( isset ( $this->_Cache['Current'] ) ) {
			return ( $this->_Cache['Current'] );
		}
		
		$AuthUser = new cUserAuthorization ( );
		
		$AuthUser->LoggedIn();
		
		if ( !$AuthUser->Username ) return ( false );
		
		$this->_Cache['Current'] = $AuthUser;
		
		return ( $AuthUser );
	}
	
	public function Focus ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		if ( isset ( $this->_Cache['Focus'] ) ) {
			return ( $this->_Cache['Focus'] );
		}
		
		list ( $null, $null, $focusUsername, $null ) = explode ('/', $_SERVER['REQUEST_URI'] );
		
		if ( !$focusUsername ) return ( false );
		
		$FocusUser = new cUserAuthorization ( );
		
		if ( !$FocusUser->Focus ( $focusUsername ) ) return ( false );
		
		$this->_Cache['Focus'] = $FocusUser;
		
		return ( $FocusUser );
	}
	
	public function Account ( $pData = null ) {
		$Id = $pData['Id'];
		$Username = $pData['Username'];
		
		$RequestedUser = new cUserAuthorization ( );
		
		if ( $Id ) 
			$RequestedUser->Load ( (int)$Id );
		else
			$RequestedUser->Load ( $Username );
		
		return ( $RequestedUser );
	}
	
	public function AdminMenu ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$return = array ();
		
		$return[] = array ( 'title' =>"Users", 'class' => "users", 'link' => "/admin/users/" );
		
		return ( $return );
	} 
	
	public function AddInvites ( $pData = null ) {
		$this->Load ( 'Invites', null, 'AddInvites', $pData );
	}
	
	public function Link ( $pData = null ) {
		$return['link'] = $this->Load ( 'User', null, 'CreateUserLink', $pData );
		return ( $return );
	}
	
	public function RegisterOptionsArea ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$return = array ();
		
		$return[] = array ( 'title' =>'Account', 'class' => 'account', 'link' => '/profile/(.*)/options/account/' );
		
		return ( $return );
	}
	
	public function GetIcon ( $pData = null ) {
		$objects = $pData['objects'];

		// Disallow component-to-component requests, so we don't exit out.

		if ( !$pData['username'] ) $username = $objects[0];
		if ( !$pData['size'] ) $size = $objects[1];

		if ( !$size ) $size = 't';

		$file = ASD_PATH . '_storage/photos/' . $username . '/' . 'profile.' . $size . '.jpg';
		if ( !file_exists ( $file ) ) {
			// @todo: Create an Error() function for interfaces
			$return['error'] = '404';
			$return['message'] = 'File Not Found';
			return ( $return );
		}

    	$im = @imagecreatefromjpeg( $file );

		header('Content-Type: image/jpeg');

		imagejpeg ( $im );

		// Todo:  Create a Source() function for interfaces
		exit;
	}
	
	/*
	 * Retrieve or generate an authentication token.
	 * Available to authenticated users only.
	 *
	 */
	public function GetToken ( ) {

		$Current = $this->Talk ( 'User', 'Current' );

		if ( !$Current ) {
			// User is not locally authenticated.
			$return['error'] = '403';
			$return['message'] = 'Forbidden';
			return ( $return );
		}

		$Secret = $Current->Secret;

		$Identity = $Current->Account;
		$Origin = ASD_DOMAIN;
		$Destination = ASD_DOMAIN;

		// 1. Check for existing, unexpired token.
      	$tokensModel = new cModel ( "AuthorizationTokens" );

		$Graph = Wob::_( 'Graph' );

		// Create the callback function pointer for saving tokens.
		$fSaveToken = array ( $this, '_SaveToken' );
		$fLoadToken = array ( $this, '_LoadToken' );
		list ( $Token, $Expiration ) = $Graph->Token ( $Identity, $Origin, $Destination, $pSecret, 24 * 60, $fSaveToken, $fLoadToken );

		$Date = Wob::_( 'Date' );

		// Check for Created is > 24h ago.
		$createdStamp = ( time() - ( 60 * 60 * 24 ) ); 
		$createdMysql = $Date->ToMysql ( $createdStamp );

		// Find a corresponding token which is less than 24 hours old.
		$tokensModel->Query ( 'SELECT * FROM #__AuthorizationTokens' );

		$criteria = array (
			'Identity' => $Identity,
			'Origin' => $Origin,
			'Destination' => $Destination,
			'Created' => '>>' . $createdMysql,
		);
		$tokensModel->Retrieve ( $criteria );

		// 2. Create new token.
		if ( $tokensModel->Get ( 'Total' ) == 0 ) {
			$createdStamp = time();
			$expirationStamp = ( time() + ( 60 * 60 * 24 ) ); 

			$createdMysql = $Date->ToMysql ( $createdStamp );
			$expirationMysql = $Date->ToMysql ( $expirationStamp );
			$Expiration = $Date->ToGraph ( $expirationStamp );

			# 1P =  hmac_sha512 ( Identity + Origin + Destination + Expiration, Secret );
			$String = $Identity . $Origin . $Destination . $Expiration;
			$Token = hash_hmac ( 'sha512', $String, $Secret );

			$tokensModel->Set ( 'Identity', $Identity );
			$tokensModel->Set ( 'Origin', $Origin );
			$tokensModel->Set ( 'Destination', $Destination );
			$tokensModel->Set ( 'Created', $createdMysql );
			$tokensModel->Set ( 'Token', $Token );
			$tokensModel->Set ( 'Token', $Token );
			$tokensModel->Set ( 'Host', $_SERVER['HTTP_HOST'] );
			$tokensModel->Set ( 'Address', $_SERVER['REMOTE_ADDR'] );
			$tokensModel->Save();
		} else {
			$tokensModel->Fetch();
			$Token = $tokensModel->Get ( 'Token' );
			$Expiration = $Date->ToGraph ( strtotime ( $tokensModel->Get ( 'Created' ) ) + (24 * 60 * 60 ) );
		}

		// 3. Return the token.
		$return = array ( 'account' => $Identity, 
						  'origin' => $Origin,
						  'destination' => $Destination,
						  'token' => $Token,
						  'expiration' => $Expiration );

		return ( $return );
	}

}

/** User Authorization Class
 * 
 * Returned by Current function
 * 
 * @package     Appleseed.Components
 * @subpackage  User
 */
class cUserAuthorization extends cBase {
	
	public $Username;
	public $Fullname;
	public $Domain;
	public $Remote;
	
	public function LoggedIn () {
		
      	$loginSession = isset($_COOKIE["gLOGINSESSION"]) ?  $_COOKIE["gLOGINSESSION"] : "";
      	$remoteLoginSession = isset($_COOKIE["gREMOTELOGINSESSION"]) ?  $_COOKIE["gREMOTELOGINSESSION"] : "";

      	if ( $loginSession ) {
      		$this->_LocalLoggedIn ( $loginSession );
      	} else if ( $remoteLoginSession ) {
      		$this->_RemoteLoggedIn ( $remoteLoginSession );
      	} else {
      		return ( false );
      	}
      	
      	return ( true );
	}
	
	private function _LocalLoggedIn ( $pSession ) {
		
      	// Load the session information.
      	$sessionModel = new cModel ( "UserSessions" );
      	$sessionModel->Retrieve ( array ( "Identifier" => $pSession ) );
      	$sessionModel->Fetch();
      	
      	if ( !$sessionModel->Get ( "Account_FK" ) ) return ( false );
      	
      	// Load the user account information.
      	$UserAccounts = new cModel ( "UserAccounts" );
      	$UserAccounts->Retrieve ( array ( "Account_PK" => $sessionModel->Get ( "Account_FK" ) ) );
      	$UserAccounts->Fetch();
      	
      	if ( !$UserAccounts->Get ( "Username" ) ) return ( false );
      	
      	$this->Username = $UserAccounts->Get ( "Username" );
      	$this->Secret = $UserAccounts->Get ( "Secret" );
      	
      	// Load the user profile information.
      	$UserProfile = new cModel ( "UserProfile" );
      	$UserProfile->Retrieve ( array ( "Account_FK" => $sessionModel->Get ( "Account_FK" ) ) );
      	$UserProfile->Fetch();
      	
      	$this->Fullname = $UserProfile->Get ( "Fullname" );
      	$this->Domain = $_SERVER['HTTP_HOST'];
      	
      	$this->Remote = false;
      	
      	$this->Account = $this->Username . '@' . $this->Domain;
      	
      	return ( true );
	}
	
	private function _RemoteLoggedIn ( $pSession ) {
      	// Load the session information.
      	$sessionModel = new cModel ( "RemoteSessions" );
      	$sessionModel->Retrieve ( array ( "Identifier" => $pSession ) );
      	$sessionModel->Fetch();
      	
      	if ( !$sessionModel->Get ( "Username" ) ) return ( false );
      	
      	$this->Username = $sessionModel->Get ( "Username" );
      	$this->Domain = $sessionModel->Get ( "Domain" );
      	$this->Fullname = $sessionModel->Get ( "Fullname" );
      	$this->Remote = true;
      	
      	$this->Account = $this->Username . '@' . $this->Domain;
      	
      	return ( true );
	}
	
	public function Focus ( $pUsername ) {
		
      	// Load the user account information.
      	$UserAccounts = new cModel ( "UserAccounts" );
      	$UserAccounts->Retrieve ( array ( "Username" => $pUsername ) );
      	$UserAccounts->Fetch();
      	
      	if ( !$UserAccounts->Get ( "Username" ) ) return ( false );
      	
      	$this->Username = $UserAccounts->Get ( "Username" );
      	$this->Id = $UserAccounts->Get ( "Account_PK" );
      	
      	// Load the user profile information.
      	$UserProfile = new cModel ( "UserProfile" );
      	$UserProfile->Retrieve ( array ( "Account_FK" => $UserAccounts->Get ( "Account_PK" ) ) );
      	$UserProfile->Fetch();
      	
      	$this->Account_PK = $UserProfile->Get ( "Account_FK" );
      	
      	if ( $UserProfile->Get ( "Alias" ) )
      		$this->Fullname = $UserProfile->Get ( "Alias" );
      	else
      		$this->Fullname = $UserProfile->Get ( "Fullname" );
      		
      	$this->Description = $UserProfile->Get ( "Description" );
      	$this->Domain = $_SERVER['HTTP_HOST'];
      	
      	$this->Account = $this->Username . '@' . $this->Domain;
      	
      	$this->Email = $UserAccounts->Get ( 'Email' );
      	
      	$this->Remote = false;
      	
      	return ( true );
	}
	
	public function Load ( $pUser ) {
		
      	$UserAccounts = new cModel ( "UserAccounts" );
      	$UserAccounts->Structure();
      	
      	if ( is_int ( $pUser ) ) {
      		$UserAccounts->Retrieve ( array ( 'Account_PK' => $pUser ) );
      	} else {
      		$UserAccounts->Retrieve ( array ( 'Username' => $pUser ) );
      	}
      	$UserAccounts->Fetch();
      	
      	if ( !$UserAccounts->Get ( "Username" ) ) return ( false );
      	
      	$this->Username = $UserAccounts->Get ( "Username" );
      	$this->Id = $UserAccounts->Get ( "Account_PK" );
      	
      	// Load the user profile information.
      	$UserProfile = new cModel ( "UserProfile" );
      	$UserProfile->Retrieve ( array ( "Account_FK" => $UserAccounts->Get ( "Account_PK" ) ) );
      	$UserProfile->Fetch();
      	
      	$this->Account_PK = $UserProfile->Get ( "Account_FK" );
      	
      	if ( $UserProfile->Get ( "Alias" ) )
      		$this->Fullname = $UserProfile->Get ( "Alias" );
      	else
      		$this->Fullname = $UserProfile->Get ( "Fullname" );
      		
      	$this->Description = $UserProfile->Get ( "Description" );
      	$this->Domain = $_SERVER['HTTP_HOST'];
      	
      	$this->Account = $this->Username . '@' . $this->Domain;
      	
      	$this->Email = $UserAccounts->Get ( 'Email' );
      	
      	$this->Remote = false;
      	
      	return ( true );
	}

}
