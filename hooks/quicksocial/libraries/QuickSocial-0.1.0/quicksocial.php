<?php
/**
 * @version      $Id$
 * @package      QuickSocial.Library
 * @subpackage   QuickSocial
 * @copyright    Copyright (C) 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org/quicksocial/
 * @license      GNU Lesser General Public License (LGPL) version 3.0
 */
 
 define ( 'QUICKSOCIAL_VERSION', 'QS/0.1.1' );
 define ( 'QUICKSOCIAL_DOMAIN', $_SERVER['HTTP_HOST'] );

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
		
		// @todo Further sanitizing of data may be necessary.
		foreach ( $_GET as $g => $get ) {
			if ( is_array ( $get ) ) {
				foreach ( $get as $gg => $gget ) {
					$this->_GET[$g][$gg] = strip_tags ( $gget );
				}
			} else {
				$this->_GET[$g] = strip_tags ( $get );
			}
		}
		foreach ( $_POST as $p => $post ) {
			if ( is_array ( $post ) ) {
				foreach ( $post as $pp => $ppost ) {
					$this->_POST[$p][$pp] = strip_tags ( $ppost );
				}
			} else {
				$this->_POST[$p] = strip_tags ( $post );
			}
		}
	}
	
	/*
	 * Send a token verification request.
	 * 
	 */
	public function Verify ( $pUsername, $pTarget, $pToken ) {
		
		$fCheckRemoteToken = $this->GetCallback ( 'CheckRemoteToken' );
		
		if ( !is_callable ( $fCheckRemoteToken ) ) $this->_Error ( 'Invalid Callback: CheckRemoteToken' );
		
		$token = @call_user_func ( $fCheckRemoteToken, $pUsername, $pTarget );
		
		if ( ( !$token ) or ( $token != $pToken ) ) {
			$source = QUICKSOCIAL_DOMAIN;
			
			// Do a verification on this token.
			$data = array (
				'_social' => 'true',
				'_task' => 'verify',
				'_token' => $pToken,
				'_username' => $pUsername,
				'_source' => $source
			);
			
			
			$result = $this->_Communicate ( $pTarget, $data );
			
			if ( $result->success == 'true' ) {
				$fCreateRemoteToken = $this->GetCallback ( 'CreateRemoteToken' );
				
				if ( is_callable ( $fCreateRemoteToken ) ) {
					$token = @call_user_func ( $fCreateRemoteToken, $pUsername, $pTarget, $pToken );
				}
				
				$result->username = $pUsername;
				$result->domain = $pTarget;
				
				return ( $result );
			}
			
		} else if ( $token == $pToken ) {
			$return = new stdClass(); 
			$return->success = 'true';
			$return->error = '';
			
			return ( $return );
		} else {
			return ( false );
		}
		
		return ( false );
	}
	
	/*
	 * Send a remote token verification request.
	 * 
	 */
	public function RemoteVerify ( $pUsername, $pDomain, $pSource, $pToken ) {
		
		$source = $pSource;
		
		// Do a verification on this token.
		$data = array (
			'_social' => 'true',
			'_task' => 'verify.remote',
			'_token' => $pToken,
			'_username' => $pUsername,
			'_source' => $source
		);
		
		$result = $this->_Communicate ( $pDomain, $data );
		
		if ( $result->success == 'true' ) return ( true );
		
		return ( false );
	}
	
	/*
	 * Reply to a verification request.
	 * 
	 */
	public function ReplyToVerify ( ) {
		$social = $this->_GET['_social'];
		$task = $this->_GET['_task'];
		
		if ( $social != 'true' ) return ( false );
		if ( $task != 'verify' ) return ( false );
		
		$source = $this->_GET['_source'];
		$username = $this->_GET['_username'];
		$token = $this->_GET['_token'];
		
		$fCheckLocalToken = $this->GetCallback ( 'CheckLocalToken' );
		
		if ( !is_callable ( $fCheckLocalToken ) ) $this->_Error ( 'Invalid Callback: CheckLocalToken' );
		
		$verified = @call_user_func ( $fCheckLocalToken, $username, $source, $token );
		
		if ( $verified ) {
			$data['success'] = 'true';
			$data['error'] = '';
		} else {
			$this->_Error ( 'Invalid Token' );
		}
		
		echo json_encode ( $data );
		exit;
		
	}	
	
	/*
	 * Reply to a remote verification request.
	 * 
	 */
	public function ReplyToRemoteVerify ( ) {
		$social = $this->_GET['_social'];
		$task = $this->_GET['_task'];
		
		if ( $social != 'true' ) return ( false );
		if ( $task != 'verify.remote' ) return ( false );
		
		$source = $this->_GET['_source'];
		$username = $this->_GET['_username'];
		$token = $this->_GET['_token'];
		
		$fCheckLocalToken = $this->GetCallback ( 'CheckLocalToken' );
		
		if ( !is_callable ( $fCheckLocalToken ) ) $this->_Error ( 'Invalid Callback: CheckLocalToken' );
		
		$verified = @call_user_func ( $fCheckLocalToken, $username, $source, $token, true );
		
		if ( $verified ) {
			$data['success'] = 'true';
			$data['error'] = '';
		} else {
			$this->_Error ( 'Invalid Token' );
		}
		
		echo json_encode ( $data );
		exit;
		
	}
	
	public function Blocked ( ) {
		
		$fCheckBlocked = $this->GetCallback ( 'CheckBlocked' );
		
		if ( !is_callable ( $fCheckBlocked ) ) $this->_Error ( 'Invalid Callback: CheckBlocked' );
		
		$source = $this->_GET['_source'];
		
		$result = @call_user_func ( $fCheckBlocked, $source );
		
		if ( !$result ) $this->_Error ( 'Blocked' );
		
		return ( true );
	}
	
	protected function _Error ( $pError ) {
		
		$return = array (
			'success' => 'false',
			'error' => $pError
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
			CURLOPT_ENCODING       => '',
			CURLOPT_VERBOSE			=> true,
			CURLOPT_USERAGENT      => 'Appleseed QuickSocial API v0.1',
			CURLOPT_AUTOREFERER    => true,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT        => 20,      
			CURLOPT_MAXREDIRS      => 10,       
		);
	   	curl_setopt_array( $curl, $options );
		
		// Retrieve the result
		$curl_response = curl_exec ( $curl ) ;
		
		curl_close($curl);
		
		// Optionally log the request and result if callback exists.
		$fLogNetworkRequest = $this->GetCallback ( 'LogNetworkRequest' );
		
		if ( is_callable ( $fLogNetworkRequest ) ) {
			@call_user_func ( $fLogNetworkRequest, $url, $curl_response );
		}
		
		// Decode the result
		$result = json_decode ( $curl_response );
		
		return ( $result );
	}

	// Concurrent requests
	protected function _Queue ( $pTarget, $pData, $pMethod = 'http' ) {
	}
	
	// Generate a random 64 byte token
	public function Token ( ) {
		
		$token = substr(md5(uniqid(rand(), true)), 0, 128);
		$token .= substr(md5(uniqid(rand(), true)), 0, 128);
		
		return ( $token );
	}
	
	public function SetCallback ( $pName, $pFunction ) {
		
		$this->_Callbacks[$pName] = $pFunction;
		
		return ( true );
	}
	
	public function GetCallback ( $pName ) {
		
		$callback = $this->_Callbacks[$pName];
		
		if ( !$callback ) return ( false );
		
		return ( $callback );
	}
	
}
