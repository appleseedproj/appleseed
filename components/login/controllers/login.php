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
		
		$this->_Current = $this->Talk ( 'User', 'Current' );
		$Force = $pData['force'];
		
		if ( ( !$Force ) && ( $this->_Current ) ) return ( true );
		
		$referer = $_SERVER['HTTP_REFERER']; 
		$host = ASD_DOMAIN;
		
		$noredirect = $pData['noredirect'];
		
		$config = $this->Get ( 'Config' );
		$invites = $config['invites'];
		
		if ( !$this->Login = $this->GetView ( $pView ) ) return ( false );
		
		$remote = $this->GetSys ( 'Request' )->Get ( 'Remote' );
		
		if ( $remote ) {
			$this->_PrepareMessages ( 'remote' );
		} else if ( $this->GetSys ( "Request" )->Get ( "Task") == "join" )  {
			$this->_PrepareMessages ( 'join' );
		} else {
			$this->_PrepareMessages ( 'local' );
		}
		
		if ( $invites != "true" ) {
			$this->Login->Find ( "[id=invite-code-requirement]", 0 )->outertext = "";
		}
		
		// Set the context for all of the forms.
		$hiddenContexts = $this->Login->Find( "input[name=Context]" );
		foreach ( $hiddenContexts as $hiddenContext ) {
			// @note This is for the login component on the frontpage
			// @todo Find a better way to find and modify contexts
			if ( $this->_Context == 'login.login.1.login') $this->_Context = 'login.login.(.*).login';
			if ( $this->_Context == 'login.login.2.remote') $this->_Context = 'login.login.(.*).remote';
			$hiddenContext->value = $this->_Context;
		}
		
		
		if ( $noredirect ) {
			$this->Login->Find ( "[name=Redirect]", 0 )->value = '';
		} else if ( $redirect = $this->GetSys ( "Request" )->Get ( "Redirect" ) ) {
			$redirect = base64_encode ( $redirect );
			$this->Login->Find ( "[name=Redirect]", 0 )->value = $redirect; 
		} else {
			if ( preg_match ( "/http:\/\/$host/", $referer ) ) {
				list ( $null, $redirect ) = explode ( $host, $referer );
				$redirect = base64_encode ( $redirect );
				$this->Login->Find ( "[name=Redirect]", 0 )->value = $redirect;
			}
		}
			
		
		$this->GetSys ( 'Session' )->Context ( "login.login.(\d+)." . $pView );	
		$sessionData['Identity'] = $this->GetSys ( 'Session' )->Get( 'Identity' );
		$this->GetSys ( 'Session' )->Destroy ( 'Identity' );
		
		$defaults = array_merge ( array ( "Remember" => "checked" ), $sessionData );

		$this->Login->Synchronize( $defaults );
			
		$this->Login->Display();
		
		return ( true );
	}
	
	function Remote () {
		
		$identity = $this->GetSys ( "Request" )->Get ( "Identity" );
		
		list ( $username, $domain ) = explode ( '@', $identity );
		
		$this->Login = $this->GetView ( "remote" );
		
		if ( ( !$username ) or ( !$domain ) ) {
			
			$this->Login->Find ( "[id=remote-login-message]", 0 )->innertext = __( 'Invalid ID' );
			$this->Login->Find ( "[id=remote-login-message]", 0 )->class = 'error';
			
			$this->Login->Synchronize ( );
		
			$this->_PrepareMessages ( "remote" );
		
			$this->Login->Display ();
			
			return ( true );
		}
		
		$data = array ( "username" => $username, "domain" => $domain );
		
		if ( $redirect = $this->GetSys ( "Request" )->Get ( "Redirect" ) ) {
			$data['return'] = 'http://' . ASD_DOMAIN . base64_decode ( $redirect );
		}
			
		$result = $this->GetSys ( "Event" )->Trigger ( "On", "Login", "Authenticate", $data );
		
		if ( $result->error ) {
		
			$this->Login->Find ( "[id=remote-login-message]", 0 )->innertext = $result->error;
			$this->Login->Find ( "[id=remote-login-message]", 0 )->class = 'error';
			
			$this->Login->Synchronize ( );
			
			$this->_PrepareMessages ( "remote" );
		
			$this->Login->Display ();
			
			return ( true );
		}
		
		return ( true );
	}
	
	private function _PrepareMessages ( $pScope ) {
		
		$id = $pScope . "-login-message";
		
		$this->GetSys ( "Session" )->Context ( $this->Get ( "Context" ) );
		$message = $this->GetSys ( "Session" )->Get ( "Message" );
		$error = $this->GetSys ( "Session" )->Get ( "Error" );
		
		if ( $message ) {
			$this->Login->Find ( "[id=$id]", 0)->innertext = $message;
			if ( $error ) 
				$this->Login->Find ( "[id=$id]", 0)->class = "error";
			else
				$this->Login->Find ( "[id=$id]", 0)->class = "message";
			
			$this->GetSys ( "Session" )->Destroy ( "Message" );
			$this->GetSys ( "Session" )->Destroy ( "Error" );
		}
		
		return ( true );
	}	

	function Login () {
		
		$username = $this->GetSys ( "Request" )->Get ( "Username" );
		$password = $this->GetSys ( "Request" )->Get ( "Pass" );
		$remember = $this->GetSys ( "Request" )->Get ( "Remember" );
		
		$loginModel = new cModel ( "userAuthorization" );
		
		$criteria = array ( "Username" => $username );
		
		$loginModel->Retrieve ( $criteria );
		
		$loginModel->Fetch();
		
		$salt = $this->GetSys ( "Crypt" )->Salt ( $loginModel->Get ( "Pass" ) );
		
		$newpass = $this->GetSys ( "Crypt" )->Encrypt ( $password, $salt );
		
		if ( $loginModel->Get ( "Pass" ) == $newpass ) {
			
			$this->_SetLogin ( $loginModel->Get ( "uID"), $remember );
			
			if ( $redirect = $this->GetSys ( "Request" )->Get ( "Redirect" ) ) {
				$redirect = base64_decode ( $redirect );
				header('Location: ' . $redirect);
			} else {
				header('Location: /');
			}
			
			exit;
		} else {
			$this->Login = $this->GetView ( "login" );
			
			$this->Login->Find ( "[id=local-login-message]", 0 )->innertext = __( 'Invalid Login' );
			$this->Login->Find ( "[id=local-login-message]", 0 )->class = 'error';
			
			$this->_SetContexts();
			
			$this->Login->Synchronize();
			
			$this->Login->Display();
			
			return ( true );
		}
	}
	
	private function _SetContexts ( ) {
		// Set the context for all of the forms.
		$hiddenContexts = $this->Login->Find( "input[name=Context]" );
		foreach ( $hiddenContexts as $hiddenContext ) {
			// @todo Find a better way to find and modify contexts
			$hiddenContext->value = $this->_Context;
		}
	}
	
	private function _SetLogin ( $pUserID, $pRemember = false ) {
		
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
		
		// If "Remember" is selected, set a faux-permanent cookie (1 year).
		if ( $pRemember ) 
			$time = time()+60*60*24*365;
		else
			$time = 0;
		
		// Set the cookie
      	if ( !setcookie ("gLOGINSESSION", $identifier, $time, '/') ) {
      		// @todo Set error that we couldn't set the cookie.
      		
      		return ( false );
      	};
		
		// Update the userInformation table
		$infoModel = new cModel ( "userInformation" );
		
		return ( true );
	}
	
	function Join ( ) {
		
		$session = $this->GetSys ( "Session" );
		$session->Context ( "login.login.(\d+).join" );
		
		$config = $this->Get ( "Config" );
		$invites = $config['invites'];
		
		$fullname = ltrim ( rtrim ( $this->GetSys ( "Request" )->Get ( "Fullname" ) ) );
		$username = strtolower ( ltrim ( rtrim ($this->GetSys ( "Request" )->Get ( "Username" ) ) ) );
		$email = ltrim ( rtrim ( $this->GetSys ( "Request" )->Get ( "Email" ) ) );
		$password = $this->GetSys ( "Request" )->Get ( "Pass" );
		$invite = $this->GetSys ( "Request" )->Get ( "Invite" );
		$confirm = $this->GetSys ( "Request" )->Get ( "Confirm" );
		
		$error = false;
		if ( ( !$fullname ) or ( !$username ) or ( !$email ) or ( !$password ) or ( !$confirm ) ) {
			$session->Set ( "Message", "Fields were missing" );
			$session->Set ( "Error", 1 );
			$error = true;
		}
		
		if ( $password != $confirm ) {
			$session->Set ( "Message", "Passwords Do Not Match" );
			$session->Set ( "Error", 1 );
			$error = true;
		}
			
		if ( strlen ( $password ) < 5 ) {
			$session->Set ( "Message", "Username Is Too Short" );
			$session->Set ( "Error", 1 );
			$error = true;
		}
			
		if ( strlen ( $password ) < 6 ) {
			$session->Set ( "Message", "Password Is Too Short" );
			$session->Set ( "Error", 1 );
			$error = true;
		}
			
			
		if ( !preg_match ('/^[a-zA-Z0-9.]+$/', $username ) ) {
			$session->Set ( "Message", "Invalid Characters In Username" );
			$session->Set ( "Error", 1 );
			$error = true;
		}
		
		if ( $invites == "true" ) {
			$userInvites = new cModel ( "userInvites" );
			
			$userInvites->Retrieve ( array ( "Recipient" => $email, "Value" => $invite ) );
			if ( $userInvites->Get ( "Total" ) == 0 ) {
				$session->Set ( "Message", "Invalid Invite Code" );
				$session->Set ( "Error", 1 );
				$error = true;
			}
			
		}
			
		if ( $error ) {
			if ( !$this->Login = $this->GetView ( "login" ) ) return ( false );
		
			$this->_SetContexts();
			$this->_PrepareMessages ( 'join' );
			$this->Login->Synchronize();
			$this->Login->Display();
			
			return ( true );
		}
		
		$newpass = $this->GetSys ( "Crypt" )->Encrypt ( $password );
		
		$userAuth = new cModel ( 'userAuthorization' );
		$userProfile = new cModel ( 'userProfile' );
		
		// Check for existing username.
		$userAuth->Retrieve ( array ( "Username" => $username ) );
		$userAuth->Fetch();
		
		if ( $userAuth->Get ( "Total" ) > 0 ) {
			$session->Set ( "Message", "Username Is Taken" );
			$session->Set ( "Error", 1 );
			if ( !$this->Login = $this->GetView ( "login" ) ) return ( false );
		
			$this->_SetContexts();
			$this->_PrepareMessages ( 'join' );
			$this->Login->Synchronize();
			$this->Login->Display();
			
			return ( true );
		}
		
		// Check for existing email.
		$userProfile->Retrieve ( array ( "Email" => $email ) );
		$userProfile->Fetch();
		
		if ( $userProfile->Get ( "Total" ) > 0 ) {
			$session->Set ( "Message", "Email address in use" );
			$session->Set ( "Error", 1 );
			if ( !$this->Login = $this->GetView ( "login" ) ) return ( false );
		
			$this->_SetContexts();
			$this->_PrepareMessages ( 'join' );
			$this->Login->Synchronize();
			$this->Login->Display();
			
			return ( true );
		}
		
		$userAuth->Set ( 'Username', $username);	
		$userAuth->Set ( 'Pass', $newpass);	
		$userAuth->Set ( 'Verification', 0);	
		$userAuth->Set ( 'Standing', 0);	
		
		if ( !$userAuth->Save() ) {
			$session->Set ( "Message", "An error occurred" );
			$session->Set ( "Error", 1 );
			if ( !$this->Login = $this->GetView ( "login" ) ) return ( false );
		
			$this->_SetContexts();
			$this->_PrepareMessages ( 'join' );
			$this->Login->Synchronize();
			$this->Login->Display();
			
			return ( true );
		}
		
		$userProfile->Set ( "userAuth_uID", $userAuth->Get ( "uID" ) );
		$userProfile->Set ( "Fullname", $fullname );
		$userProfile->Set ( "Email", $email );
		
		$userProfile->Save();
		
		if ( !$this->Login = $this->GetView ( 'success' ) ) return ( false );
			
		$session->Set ( 'Message', 'Your Account Has Been Created' );
		$session->Set ( 'Error', 0 );
		
		if ( $invites == "true" ) {
			$userInvites->Fetch();
			$userInvites->Set ( "Active", "0" );
			$userInvites->Set ( "Stamp", NOW() );
			$userInvites->Save( array ( "Recipient" => $email, "Value" => $invite ) );
		}
		
		$this->_PrepareMessages ( 'local' );
		$this->Login->Synchronize();
		
		$this->Login->Display();
		
		return ( true );
	}
	
	function Forgot ( $pView = null, $pData = null ) {
		
		$username = ltrim ( rtrim ($this->GetSys ( "Request" )->Get ( "Username" ) ) );
		
		if ( !$username ) {
			
			$this->GetSys ( "Session" )->Context ( "login.login.(\d+).login" );
			$this->GetSys ( "Session" )->Set ( "Message", "Invalid Username" );
			$this->GetSys ( "Session" )->Set ( "Error", 1 );
			
			return ( $this->Display ( $pView, $pData ) );
		}
		
		$this->Mailer = $this->GetSys ( "Mailer" );
		
		$newpassword = $this->_GeneratePassword ('##XX#XX!');
      
		$userAuth = new cModel ( 'userAuthorization' );
		
		$userAuth->Retrieve ( array ( "Username" => $username ) );
		if (! $userAuth->Fetch() ) {
			$this->GetSys ( "Session" )->Context ( "login.login.(\d+).login" );
			$this->GetSys ( "Session" )->Set ( "Message", __( "Username Not Found", array ( "username" => $username ) ) );
			$this->GetSys ( "Session" )->Set ( "Error", 1 );
			return ( $this->Display ( $pView, $pData ) );
		}
		
		$userProfile = new cModel ( 'userProfile' );
		$userProfile->Retrieve ( $userAuth->Get ( "uID" ) );
		$userProfile->Fetch();
      
		$newpass = $this->GetSys ( "Crypt" )->Encrypt ( $newpassword );
		
		$to = $userProfile->Get ( "Email" ); 
		$toName = $userProfile->Get ( "Fullname" );

		$subject = __( "Password Reset Subject", array ( "domain" => ASD_DOMAIN ) );

		$body = __("Password Reset Body", array ( "fullname" => $toName, "password" => $newpassword ) );

		$from = __("Password Reset From", array ( "domain" => ASD_DOMAIN ) );
		$fromName = __( "Password Reset From Name" );

		$headers = "From: $from" . "\r\n" .
                 "Reply-To: $from" . "\r\n" .
                 "X-Mailer: PHP/" . phpversion();

		if ( !$this->Mailer->Send ( $from, $fromName, $to, $toName, $subject, $body ) ) {
			// Couldn't send out the message, so error without resetting the pw.
			$this->GetSys ( "Session" )->Context ( "login.login.(\d+).login" );
			$this->GetSys ( "Session" )->Set ( "Message", "Error Sending Message" );
			$this->GetSys ( "Session" )->Set ( "Error", 1 );
		} else { 
			// Reset the pw.
			$userAuth->Set ( "Pass", $newpass );
			$userAuth->Save();
			$this->GetSys ( "Session" )->Context ( "login.login.(\d+).login" );
			$this->GetSys ( "Session" )->Set ( "Message", __( "Password Has Been Reset", array ( "username" => $username ) ) );
			$this->GetSys ( "Session" )->Set ( "Error", 0 );
		}

		return ( $this->Display ( $pView, $pData ) );
	}

    // Generate a password.
    // Originally from bestcodingpractices.com
    private function _GeneratePassword($mask) {

		// Mask Rules
		// # - digit
		// C - Caps Character (A-Z)
		// c - Small Character (a-z)
		// X - Mixed Case Character (a-zA-Z)
		// ! - Custom Extended Characters
		
		$extended_chars = "!@#$%&*";
		
		$length = strlen($mask);
		
		$pwd = '';
		
		for ($c=0;$c<$length;$c++) {
			$ch = $mask[$c];
			switch ($ch) {
			  case '#':
			 	 $p_char = rand(0,9);
			  break;
			  case 'C':
			 	 $p_char = chr(rand(65,90));
			  break;
			  case 'c':
			 	 $p_char = chr(rand(97,122));
			  break;
			  case 'X':
			 	 do {
			 	 	$p_char = rand(65,122);
			 	 } while ($p_char > 90 && $p_char < 97);
			 	 $p_char = chr($p_char);
			  break;
			  case '!':
			 	 $p_char = $extended_chars[rand(0,strlen($extended_chars)-1)];
			  break;
			} // switch
			$pwd .= $p_char;
		} // for
		
		return $pwd;
	} 
	
}
