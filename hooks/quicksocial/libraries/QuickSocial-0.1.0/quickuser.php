<?php
/**
 * @version      $Id$
 * @package      QuickSocial.Library
 * @subpackage   QuickUser
 * @copyright    Copyright (C) 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org/quicksocial/
 * @license      GNU Lesser General Public License (LGPL) version 3.0
 */

if ( !class_exists ( "cQuickSocial" ) ) require ( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'quicksocial.php' );

/** QuickUser Class
 * 
 * User information retrieval
 * 
 * @package     QuickSocial.Framework
 * @subpackage  QuickUser
 */
class cQuickUser extends cQuickSocial {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function Icon ( ) {
	}
	
	public function ReplyToIcon ( ) {
		
		$fUserIcon = $this->GetCallBack ( "UserIcon" );
		
		if ( !is_callable ( $fUserIcon ) ) $this->_Error ( "Invalid Callback: UserIcon" );
		
		$account = $_GET['_account'];
		$request = $_GET['_request'];
		
		$width = $_GET['_width'];
		$height = $_GET['_height'];
		
		@call_user_func ( $fUserIcon, $request, $width, $height );
		
		return ( true );
	}
	
	public function Info ( $pAccount, $pSource, $pRequest ) {
		
		list ( $accountUsername, $accountDomain ) = explode ( '@', $pAccount );
		list ( $requestUsername, $requestDomain ) = explode ( '@', $pRequest );
		
		$fCheckRemoteToken = $this->GetCallBack ( "CheckRemoteToken" );
		
		if ( !is_callable ( $fCheckRemoteToken ) ) {
		    trigger_error("Invalid Callback: CheckRemoteToken", E_USER_WARNING);
			return ( false );
		}
		
		$encrypt = false;
		if ( ( $requestDomain ) and ( $accountDomain != $requestDomain ) ) $encrypt = true;
		
		$token = @call_user_func ( $fCheckRemoteToken, $requestUsername, $requestDomain, $encrypt );
		
		echo $pAccount, "<br />";
		echo $pRequest, "<br />";
		
		echo $accountDomain, "<br/>";
		
		echo $token, "<br /><br />";
		
		$data = array (
			"_social" => "true",
			"_task" => "user.info",
			"_account" => $pAccount,
			"_source" => $pSource,
			"_request" => $pRequest,
			"_token" => $token
		);
		
		$result = $this->_Communicate ( $accountDomain, $data );
		
		return ( $result );
	}
	
	public function ReplyToInfo ( ) {
		$social = $_GET['_social'];
		$task = $_GET['_task'];
		
		if ( $social != "true" ) return ( false );
		if ( $task != "user.info" ) return ( false );
		
		$request = $_GET['_request'];
		$account = $_GET['_account'];
		$source = $_GET['_source'];
		$token = $_GET['_token'];
		
		list ( $requestUsername, $requestDomain ) = explode ( '@', $request );
		list ( $accountUsername, $accountDomain ) = explode ( '@', $account );
		
		if ( ($accountDomain) and ( $accountDomain != QUICKSOCIAL_DOMAIN ) ) $this->_Error ( "Unknown User" );
		
		$fCheckLocalToken = $this->GetCallBack ( "CheckLocalToken" );
		
		if ( !is_callable ( $fCheckLocalToken ) ) {
		    trigger_error("Invalid Callback: CheckLocalToken", E_USER_WARNING);
			return ( false );
		}
		
		$verified = false;
		if ( ( $source ) && ( $token ) ) {
			/*
			echo"<pre>";
			echo "requestUsername: \t\t", $requestUsername, "<br />";
			echo "requestDomain: \t\t\t", $requestDomain, "<br />";
			echo "accountUsername: \t\t\t", $accountUsername, "<br />";
			echo "accountDomain: \t\t\t", $accountDomain, "<br />";
			echo "ASD_DOMAIN: \t\t\t", ASD_DOMAIN, "<br />";
			echo "source: \t\t\t", $source, "<hr />";
			*/
			if ( $accountDomain == $requestDomain ) {
				$verified = @call_user_func ( $fCheckLocalToken, $requestUsername, $source, $token );
			} else {
				$verified = $this->RemoteVerify ( $requestUsername, $requestDomain, $source, $token );
			}
			
		}
		
		$fUserInfo = $this->GetCallback ( "UserInfo" );
		
		if ( !is_callable ( $fUserInfo ) ) $this->_Error ( "Invalid Callback: UserInfo" );
		
		$data = @call_user_func ( $fUserInfo, $accountUsername, $request, $verified );
		
		if ( !$data ) $this->_Error ( "Unknown User" );
		
		if ( !is_array ( $data ) ) $this->_Error ( "Invalid Callback Return" );
		
		$data['success'] = "true";
		$data['error'] = "";
		
		echo json_encode ( $data );
		exit;
	}

}
