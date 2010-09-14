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
		
		if ( isset ( $this->_Cache['Current'] ) ) {
			return ( $this->_Cache['Current'] );
		}
		
		$AuthUser = new cUserAuthorization ( );
		
		$AuthUser->LoggedIn();
		
		$this->_Cache['Current'] = $AuthUser;
		
		return ( $AuthUser );
	}
	
	public function Focus ( $pData = null ) {
		
		if ( isset ( $this->_Cache['Focus'] ) ) {
			return ( $this->_Cache['Focus'] );
		}
		
		list ( $null, $null, $focusUsername, $null ) = explode ('/', $_SERVER['REQUEST_URI'] );
		
		if ( !$focusUsername ) return ( false );
		
		$FocusUser = new cUserAuthorization ( );
		
		$FocusUser->Focus ( $focusUsername );
		
		$this->_Cache['Focus'] = $FocusUser;
		
		return ( $FocusUser );
	}
	
	public function AdminMenu ( $pData = null ) {
		
		$return = array ();
		
		$return[] = array ( 'title' =>"Users", 'class' => "users", 'link' => "/admin/users/" );
		
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
      	$sessionModel = new cModel ( "userSessions" );
      	$sessionModel->Retrieve ( array ( "Identifier" => $pSession ) );
      	$sessionModel->Fetch();
      	
      	if ( !$sessionModel->Get ( "userAuth_uID" ) ) return ( false );
      	
      	// Load the user account information.
      	$userModel = new cModel ( "userAuthorization" );
      	$userModel->Retrieve ( array ( "uID" => $sessionModel->Get ( "userAuth_uID" ) ) );
      	$userModel->Fetch();
      	
      	if ( !$userModel->Get ( "Username" ) ) return ( false );
      	
      	$this->Username = $userModel->Get ( "Username" );
      	
      	// Load the user profile information.
      	$profileModel = new cModel ( "userProfile" );
      	$profileModel->Retrieve ( array ( "userAuth_uID" => $sessionModel->Get ( "userAuth_uID" ) ) );
      	$profileModel->Fetch();
      	
      	$this->Fullname = $profileModel->Get ( "Fullname" );
      	$this->Domain = $_SERVER['HTTP_HOST'];
      	
      	$this->Remote = false;
      	
      	return ( true );
	}
	
	private function _RemoteLoggedIn ( $pSession ) {
      	// Load the session information.
      	$sessionModel = new cModel ( "authSessions" );
      	$sessionModel->Retrieve ( array ( "Identifier" => $pSession ) );
      	$sessionModel->Fetch();
      	
      	if ( !$sessionModel->Get ( "Username" ) ) return ( false );
      	
      	$this->Username = $sessionModel->Get ( "Username" );
      	$this->Domain = $sessionModel->Get ( "Domain" );
      	$this->Fullname = $sessionModel->Get ( "Fullname" );
      	$this->Remote = true;
      	
      	return ( true );
	}
	
	public function Focus ( $pUsername ) {
		
      	// Load the user account information.
      	$userModel = new cModel ( "userAuthorization" );
      	$userModel->Retrieve ( array ( "Username" => $pUsername ) );
      	$userModel->Fetch();
      	
      	if ( !$userModel->Get ( "Username" ) ) return ( false );
      	
      	$this->Username = $userModel->Get ( "Username" );
      	
      	// Load the user profile information.
      	$profileModel = new cModel ( "userProfile" );
      	$profileModel->Retrieve ( array ( "userAuth_uID" => $userModel->Get ( "uID" ) ) );
      	$profileModel->Fetch();
      	
      	$this->Fullname = $profileModel->Get ( "Fullname" );
      	$this->Domain = $_SERVER['HTTP_HOST'];
      	
      	$this->Remote = false;
      	
      	return ( true );
	}
	
	
}
