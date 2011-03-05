<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   Library
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Communication Class
 * 
 * Handles basic communication
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cCommunication {

	 /**
	  * Constructor
	  *
	  * @access  public
	  */
	 public function __construct ( ) {       
	 }
	 
	 public function Retrieve ( $pLocation ) {
	 	
	 	$pLocation = str_replace ( ' ', '%20', $pLocation );
	 	
		$curl_handle = curl_init();
		curl_setopt ( $curl_handle, CURLOPT_URL, $pLocation);
		curl_setopt ( $curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt ( $curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ( $curl_handle, CURLOPT_FOLLOWLOCATION, true);
		
		$buffer = curl_exec($curl_handle);
		
		curl_close($curl_handle);
		
		return ( $buffer );
	}
}
