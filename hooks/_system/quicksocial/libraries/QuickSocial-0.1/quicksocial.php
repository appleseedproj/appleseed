<?php
/**
 * @version      $Id$
 * @package      QuickSocial.Library
 * @subpackage   QuickSocial
 * @copyright    Copyright (C) 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org/quicksocial/
 * @license      GNU Lesser General Public License (LGPL) version 3.0
 */
 
 define ( "QUICKSOCIAL_VERSION", "0.1.0" );

/** QuickSocial Class
 * 
 * Base class for QuickSocial subclasses.
 * 
 * @package     QuickSocial.Framework
 * @subpackage  QuickSocial
 */
class cQuickSocial {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	/*
	 * Send a token verification request.
	 * 
	 */
	public function Verify ( $pAccount, $pToken ) {
		
		$source = $_SERVER['HTTP_HOST'];
		
		if ( strstr ( $pAccount, '@' ) ) {
			list ( $username, $domain ) = explode ( '@', $pAccount );
		} else {
			$username = null;
			$domain = $pAccount;
		}
		
		$data = array (
			"_social" => "true",
			"_task" => "verify",
			"_token" => $pToken,
			"_username" => $username,
			"_source" => $source
		);
		
		$result = $this->_Communicate ( $domain, $data );
		
		return ( $result );
	}
	
	/*
	 * Reply to a verification request.
	 * 
	 * @param string $pVerifyToken A callback function to verify token.
	 * 
	 */
	public function ReplyToVerify ( $fVerifyReply ) {
		$social = $_GET['_social'];
		$task = $_GET['_task'];
		
		if ( $social != "true" ) return ( false );
		if ( $task != "verify" ) return ( false );
		
		$source = $_GET['_source'];
		$username = $_GET['_username'];
		$token = $_GET['_token'];
		
		if ( !is_callable ( $fVerifyReply ) ) $this->_Error ( "Invalid Callback" );
		
		$verified = @call_user_func ( $fVerifyReply, $username, $source, $token );
		
		if ( $verified ) {
			$data['success'] = "true";
			$data['error'] = "";
		} else {
			$this->_Error ( "Invalid Token" );
		}
		
		echo json_encode ( $data );
		exit;
		
	}	
	
	protected function _Error ( $pError ) {
		
		$return = array (
			"success" => "false",
			"error" => $pError
		);
		
		echo json_encode ( $return );
		exit;
	}
	
	// Single requests
	protected function _Communicate ( $pTarget, $pData, $pMethod = 'http' ) {
		
		switch ( $pMethod ) {
			case 'https':
				$http = 'https://';
			break;
			default:
				$http = 'http://';
			break;
		}
		
		// Send the data
		
		$url = $http . $pTarget;
		
		$url .= '/?' . http_build_query ($pData );
		
		$curl = curl_init();
		
	    $options = array(
	    	CURLOPT_URL				=> $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_VERBOSE			=> true,
			CURLOPT_USERAGENT      => "Appleseed QuickSocial API v0.1",
			CURLOPT_AUTOREFERER    => true,
			CURLOPT_CONNECTTIMEOUT => 1,
			CURLOPT_TIMEOUT        => 1,      
			CURLOPT_MAXREDIRS      => 10,       
		);
	   	curl_setopt_array( $curl, $options );
		
		// Retrieve the result
		$curl_response = curl_exec ( $curl ) ;
		
		curl_close($curl);
		
		// Decode the result
		$result = json_decode ( $curl_response );
		
		return ( $result );
	}

	// Concurrent requests
	protected function _Queue ( $pTarget, $pData, $pMethod = 'http' ) {
	}
	
	protected function _Token ( $pReference ) {
		
		$token =  md5 ( uniqid( md5 ( $pReference ), true ) );
		
		return ( $token );
	}
}