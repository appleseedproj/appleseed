<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Login
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Login Component Controller
 * 
 * Login Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Login
 */
class cLoginLoginController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		
		// Check if the user is already logged in.
		$user = $this->Talk ( "User", "Current" );
		
		// If they are logged in, redirect.
		if ( $user->Username ) {
			$pView = "redirect";
			
			parent::Display ( $pView, $pData );
			return ( true );
		}
		
		if ( !$this->Login = $this->GetView ( $pView ) ) return ( false );
		
		$remote = $this->GetSys ( "Request" )->Get ( "Remote" );
		
		if ( $remote ) {
			$this->Login->Find ( "[id=login_remote_button]", 0)->class = "ui-tabs-selected";
		} else {
			$this->Login->Find ( "[id=login_local_button]", 0)->class = "ui-tabs-selected";
		}
		
		// Set the context for all of the forms.
		$hiddenContexts = $this->Login->Find( "input[name=Context]" );
		foreach ( $hiddenContexts as $hiddenContext ) {
			$hiddenContext->value = $this->_Context;
		}
		
		$this->Login->Display();
		
		return ( true );
	}
	
	function Remote () {
		echo "Remote";
		
		return ( $this->Display() );
	}

	function Login () {
		
		$username = $this->GetSys ( "Request" )->Get ( "Username" );
		$password = $this->GetSys ( "Request" )->Get ( "Pass" );
		
		$loginModel = new cModel ( "userAuthorization" );
		
		$criteria = array ( "Username" => $username );
		
		$loginModel->Retrieve ( $criteria );
		
		$loginModel->Fetch();
		
		$salt = substr ( $loginModel->Get ( "Pass" ), 0, 16 );
		
		$sha512 = hash ("sha512", $salt . $password);
      
		$newpass = $salt . $sha512;
		
		if ( $loginModel->Get ( "Pass" ) == $newpass ) {
			
			$this->_SetLogin ( $loginModel->Get ( "uID") );
			
			$this->Display( "redirect" );
			return ( true );
		} else {
			$this->Login = $this->GetView ( "login" );
			
			$this->Login->Find ( "[id=local_login_message]", 0 )->innertext = __( 'Invalid Login' );
			$this->Login->Find ( "[id=local_login_message]", 0 )->class = 'error';
			
			// Set the context for all of the forms.
			$hiddenContexts = $this->Login->Find( "input[name=Context]" );
			foreach ( $hiddenContexts as $hiddenContext ) {
				$hiddenContext->value = $this->_Context;
			}
			
			$this->Login->Synchronize ( );
			
			$this->Login->Display();
			
			return ( false );
		}
		
	}
	
	private function _SetLogin ( $pUserID ) {
		
		$sessionModel = new cModel ( "userSessions" );
		
		// Delete current session id's.
		$criteria = array ( "userAuth_uID" => $pUserID );
		
		$sessionModel->Delete ( $criteria );
		
		// Create a unique session identifier.
        $identifier = md5(uniqid(rand(), true));
        
		// Set the session database information.
		$sessionModel->Set ( "userAuth_uID", $pUserID );
		$sessionModel->Set ( "Identifier", $identifier );
		$sessionModel->Set ( "Stamp", NOW() );
		$sessionModel->Set ( "Address", $_SERVER['REMOTE_ADDR'] );
		$sessionModel->Set ( "Host", $_SERVER['REMOTE_HOST'] );
		
		$sessionModel->Save ();
		
		// Set the cookie
      	if ( !setcookie ("gLOGINSESSION", $identifier, time()+60*60*24*30, '/') ) {
      		// @todo Set error that we couldn't set the cookie.
      		
      		return ( false );
      	};
		
		// Update the userInformation table
		$infoModel = new cModel ( "userInformation" );
		
		return ( true );
	}
	
	private function _PrepareMessage ( $pType = "local" ) {
		
		$markup = & $this->Form;
		
		if ( $pType == 'local' ) {
			$id = 'local_login_message';
		} else {
			$id = 'remote_login_message';
		}
		
		if ( $message =  $this->GetSys ( "Session" )->Get ( "Message" ) ) {
			$markup->Find ( "[id=$id]", 0 )->innertext = $message;
			if ( $error =  $this->GetSys ( "Session" )->Get ( "Error" ) ) {
			$markup->Find ( "[id=$id]", 0 )->innertext = $message;
				$markup->Find ( "[id=$id]", 0 )->class = "error";
			} else {
				$markup->Find ( "[id=$id]", 0 )->class = "message";
			}
			$this->GetSys ( "Session" )->Delete ( "Message ");
			$this->GetSys ( "Session" )->Delete ( "Error ");
		}
		
		return ( true );
	}
	

	function Join () {
		echo "Join";
		
		return ( $this->Display("success") );
	}

}