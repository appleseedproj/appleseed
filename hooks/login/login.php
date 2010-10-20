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

/** Login Hook Class
 * 
 * Login Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cLoginHook extends cHook {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function BeginSystemInitialize ( $pData = null ) {
		return ( true );
	}
	
	public function OnLoginLogout ( $pData = null ) {
		
		$this->_Logout();
		
		return ( true );
	}
	
	public function OnSystemRoute ( $pData = null ) {
		
		$foundation = $pData['foundation'];
		
		if ( $foundation == 'login/login.php' ) {
			
			// If we're attempting to login as someone else, then continue with the login foundation.
			if ( $this->GetSys ( "Request" )->Get ( "Task" ) == "Login" ) return ( false );
		
			// Check if the user is already logged in.
			$user = $this->GetSys ( "Components" )->Talk ( 'User', 'Current' );
		
			// If they are logged in, redirect.
			if ( $user->Username ) {
				header('Location: /');
				exit;
			}
		}
			
		return ( false );
	}
	
	private function _Logout ( ) {
		
		// Get the current cookies
      	$loginSession = isset($_COOKIE["gLOGINSESSION"]) ?  $_COOKIE["gLOGINSESSION"] : "";
      	$remoteLoginSession = isset($_COOKIE["gREMOTELOGINSESSION"]) ?  $_COOKIE["gREMOTELOGINSESSION"] : "";
		
		// Delete the local session info database entry.
		if ( $loginSession ) {
      		$sessionModel = new cModel ( "userSessions" );
      		$sessionModel->Delete ( array ( "Identifier" => $loginSession ) );
		}
		
		// Delete the remote session info database entry.
		if ( $remoteLoginSession ) {
      		$sessionModel = new cModel ( "authSessions" );
      		$sessionModel->Delete ( array ( "Identifier" => $remoteLoginSession ) );
		}
		
		// Delete the cookies.
		setcookie ("gLOGINSESSION", "", time() - 3600, "/");
		setcookie ("gREMOTELOGINSESSION", "", time() - 3600, "/");
		
      	$loginSession = isset($_COOKIE["gLOGINSESSION"]) ?  $_COOKIE["gLOGINSESSION"] : "";
      	$remoteLoginSession = isset($_COOKIE["gREMOTELOGINSESSION"]) ?  $_COOKIE["gREMOTELOGINSESSION"] : "";
      	
		return ( true );
	}
	
}