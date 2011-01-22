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

/** Janitor Hook Class
 * 
 * Janitor Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cJanitorHook extends cHook {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function EndFooterDisplay ( $pData = null ) {
		$this->_Janitorial();
	    return ( true );
	}
	
	private function _Janitorial ( ) {
		// create both cURL resources
		$curlHandle = curl_init();

		// set URL and other appropriate options
		$url = 'http://' . ASD_DOMAIN . '/janitor/';
		
		$multiHandle = curl_multi_init();  
		
		curl_setopt ( $curlHandle, CURLOPT_URL, $url );
		curl_setopt ( $curlHandle, CURLOPT_HEADER, 0 );
		curl_setopt ( $curlHandle, CURLOPT_TIMEOUT, 0 );
		
	    curl_multi_add_handle ( $multiHandle, $curlHandle );  
	    
	    do {
    		$mrc = curl_multi_exec($multiHandle, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
	    
		curl_multi_remove_handle ( $multiHandle, $curlHandle );
		curl_multi_close ( $multiHandle );
	    
	    return ( true );
	}
	
}
