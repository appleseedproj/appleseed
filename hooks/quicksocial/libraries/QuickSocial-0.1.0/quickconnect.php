<?php
/**
 * @version      $Id$
 * @package      QuickSocial.Library
 * @subpackage   QuickConnect
 * @copyright    Copyright (C) 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org/quicksocial/
 * @license      GNU Lesser General Public License (LGPL) version 3.0
 */

 require ( __DIR__ . DIRECTORY_SEPARATOR . 'quicksocial.php' );

/** QuickConnect Class
 * 
 * User remote login and connection class
 * 
 * @package     QuickSocial.Framework
 * @subpackage  QuickConnect
 */
class cQuickConnect extends cQuickSocial {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function Redirect ( $pTarget, $pUser, $pMethod = "http" ) {
		
		$request['_social'] = "true";
		$request['_task'] = "connect.check";
		
		$request['_username'] = $pUser;
		$request['_source'] = $_SERVER['HTTP_HOST'];
		
		$request['_method'] = $pMethod;
		
		switch ( $pMethod ) {
			case 'https':
				$http = 'https://';
			break;
			default:
				$http = 'http://';
			break;
		}
		
		$redirect = $http . $pTarget . '/?' . http_build_query ( $request );
		
		header('Location: ' . $redirect);
		
		exit;
	}
	
	public function Check ( $fCheckLogin, $fStoreIdentifier ) {
		
		$social = $_GET['_social'];
		$task = $_GET['_task'];
		
		if ( $social != "true" ) return ( false );
		if ( $task != "connect.check" ) return ( false );
		
		$source = $_GET['_source'];
		$username = $_GET['_username'];
		
		$method = $_GET['_method'];
		
		switch ( $method ) {
			case 'https':
				$http = 'https://';
			break;
			default:
				$http = 'http://';
			break;
		}
		
		// If either of the callbacks don't exist, redirect back with an error.
		if ( ( !is_callable ( $fCheckLogin ) ) OR ( !is_callable ( $fStoreIdentifier ) ) ) {
		
			$request['_success'] = "false";
			$request['_error'] = "Invalid Callback";
		
			$redirect = $http . $source . '/?' . http_build_query ( $request );
		
			header('Location: ' . $redirect);
			exit;
		}
		
		// 1. Check login
		$loggedIn = @call_user_func ( $fCheckLogin, $username );
		
		// 2. Store identifier
		if ( $loggedIn ) {
			$identifier = $this->_Token ( $username );
			$stored = @call_user_func ( $fStoreIdentifier, $username, $source, $identifier );
			
			if ( !$stored ) {
				$request['_identifier'] = $identifier;
				$request['_success'] = "false";
				$request['_error'] = "Identifier Not Stored";
			} else {
				$request['_identifier'] = null;
				$request['_success'] = "true";
				$request['_error'] = "";
			}
		}
		
		$request['_social'] = "true";
		$request['_task'] = "connect.return";
		
		$request['_account'] = $username;
		$request['_source'] = $_SERVER['HTTP_HOST'];
		
		$redirect = $source . '/?' . http_build_query ( $request );
		
		// 3. Redirect back
		header('Location: ' . $redirect);
		exit;
	}
	
	public function Process ( $fStoreIdentifierToken ) {
		
		$success = $_GET['_success'];
		
		if ( $success != "true" ) return ( false );
					
		$social = $_GET['_social'];
		$task = $_GET['_task'];
		
		if ( !is_callable ( $fStoreIdentifierToken ) ) return ( false );
		
		if ( $social != "true" ) return ( false );
		if ( $task != "connect.return" ) return ( false );
		
		$source = $_GET['_source'];
		$username = $_GET['_username'];
		
		$identifier = $_GET['_username'];
		
		$verification = $this->Verify( $username, $source, $identifier );
		
		$stored = @call_user_func ( $fStoreIdentifierToken, $username, $source, $identifier );
		
		if ( $verification->success == "true" ) return ( true );
		
		return ( false );
	}
	
	public function Verify ( $pUsername, $pTarget, $pIdentifier ) {
		
		$source = $_SERVER['HTTP_HOST'];
		
		$data = array (
			"_social" => "true",
			"_task" => "connect.verify",
			"_identifier" => $pIdentifier,
			"_username" => $pUsername,
			"_source" => $source
		);
		
		$result = $this->_Communicate ( $pTarget, $data );
		
		return ( $result );
	}
	
	public function ReplyToVerify ( $fVerifyReply ) {
		$social = $_GET['_social'];
		$task = $_GET['_task'];
		
		if ( $social != "true" ) return ( false );
		if ( $task != "connect.verify" ) return ( false );
		
		$source = $_GET['_source'];
		$username = $_GET['_username'];
		$identifier = $_GET['_identifier'];
		
		if ( !is_callable ( $fVerifyReply ) ) $this->_Error ( "Invalid Callback" );
		
		$verified = @call_user_func ( $fVerifyReply, $username, $source, $identifier );
		
		if ( $verified ) {
			$data['success'] = "true";
			$data['error'] = "";
		} else {
			$this->_Error ( "Invalid Identifier" );
		}
		
		echo json_encode ( $data );
		exit;
	}	
	

}
