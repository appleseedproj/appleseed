<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   Graph
 * @copyright    Copyright (C) 2004 - 2011 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'GRAPHAPI' ) or die( 'Direct Access Denied' );

/** Graph Hook Class
 * 
 * Graph Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cGraphAPI {

	private $_Callbacks;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}

	/*
     * Set callback function
	 *
	 */
	public function SetCallback ( $pCallback, $pFunction ) {
		$this->_Callbacks[$pCallback] = $pFunction;
	}

	/*
     * Get whether callback function is callable
	 *
	 */
	public function IsCallback ( $pCallback ) {
		if ( method_exists ( $this->_Callbacks[$pCallback][0], $this->_Callbacks[$pCallback][1] ) ) {
			return ( true );
		}
		return ( false );
	}

	public function Callback ( $pCallback, $pData ) {
		$Result = call_user_func_array ( $this->_Callbacks[$pCallback], $pData );

		return ( $Result );
	}

	/*
     * Communicate with a REST endpoint
     * 
     * @access	public
	 * @return	array
	 */
	public function Communicate ( $pDomain, $pMethod, $pEndPoint, $pIdentity = null ) {

		// Pull from callback function
		$EntryPoint = $this->_EntryPoint ( $pDomain );

		// Get the preferred domain protocol
		$Protocol = $this->_Protocol ( $pDomain );

		// Remove the leading slash from the end point.
		if ( $pEndPoint[0] == '/' ) {
			$pEndPoint = preg_replace ( '/\//', '', $pEndPoint, 1 );
		}

		// Remove trailing slash on entrypoint.
		if ( $EntryPoint[strlen($EntryPoint)-1] == '/' ) {
			$EntryPoint[strlen($EntryPoint)-1] = '';
		}

		// No leading slash on the entry point means a separate domain.
		if ( $EntryPoint[0] == '/' ) {
			$Entry = $Protocol . $pDomain . $EntryPoint . '/';
		} else {
			$Entry = $Protocol . $EntryPoint . '/';
		}

		$url = $Entry . $pEndPoint;

		echo $url, "<br />";
	}

	/*
     * Get the entry point for a domain
     * 
     * @access	public
	 * @return	array
	 */
	protected function _EntryPoint ( $pDomain ) {
		if ( method_exists ( $this->_Callbacks['GetNodeEntryPoint'][0], $this->_Callbacks['GetNodeEntryPoint'][1] ) ) {
			if ( !$EntryPoint = call_user_func_array ( $this->_Callbacks['GetNodeEntryPoint'], array ( $pDomain ) ) ) {
				// Query the node directly for entry point.
				$target = 'http://' . $pDomain . '/graph';
				$result = json_decode ( $this->_Communicate ( $target ) );
				$EntryPoint = $result->entry;
				$Version = $result->version;
				// Attempt to store the new data for the future.
				if ( $this->IsCallback ( 'UpdateNetworkNode' ) ) {
					$this->Callback ( 'UpdateNetworkNode', array ( $pDomain, $EntryPoint, $Version ) );
				}
			}
		}

		return ( $EntryPoint );
	}

	/*
     * Get the preferred protocol for a domain
     * 
     * @access	public
	 * @return	array
	 */
	protected function _Protocol ( $pDomain ) {
		if ( method_exists ( $this->_Callbacks['GetNodeProtocols'][0], $this->_Callbacks['GetNodeProtocols'][1] ) ) {
			if ( !$Protocols = call_user_func_array ( $this->_Callbacks['GetNodeProtocols'], array ( $pDomain ) ) ) {
				$Protocol = 'http';
			} else {
				// Prioritize https over http
				if ( isset ( $Protocols['https'] ) ) {
					$Protocol = 'https';
				} else {
					$Protocol = 'http';
				}
			}
		} else {
			$Protocol = 'http1';
		}

		$Protocol .= '://';

		return ( $Protocol );
	}

	/*
     * Make a single CURL request
     * 
     * @access	private
	 * @return	string
	 */
	protected function _Communicate ( $pTarget, $pMethod = 'GET', $pData = array(), $pProtocol = 'http://' ) {
		
		// Send the data
		
		$url = $http . $pTarget;
		
		$url .= '/?' . http_build_query ($pData );
		
		$curl = curl_init();
		
	    $options = array(
	    	CURLOPT_URL				=> $url,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_CUSTOMREQUEST	=> $pMethod,
			CURLOPT_HEADER			=> false,
			CURLOPT_FOLLOWLOCATION	=> true,
			CURLOPT_ENCODING		=> '',
			CURLOPT_VERBOSE			=> true,
			CURLOPT_USERAGENT		=> 'Appleseed Social Graph API v0.1',
			CURLOPT_AUTOREFERER		=> true,
			CURLOPT_CONNECTTIMEOUT	=> 10,
			CURLOPT_TIMEOUT			=> 20,      
			CURLOPT_MAXREDIRS		=> 10,       
		);
	   	curl_setopt_array( $curl, $options );
		
		// Retrieve the result
		$result = curl_exec ( $curl ) ;
		
		curl_close($curl);
		
		// Optionally log the request and result if callback exists.
		if ( $this->IsCallback ( 'LogNetworkRequest') ) {
			$this->Callback ( 'LogNetworkRequest', $url, $curl_response );
		}
		
		return ( $result );
	}

}
?>
