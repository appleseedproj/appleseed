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

require ( ASD_PATH . '/libraries/default/external/Swift-4.0.6/swift_required.php' );

/** Crypt Class
 * 
 * Encryption class.
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cCrypt {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	/**
	 * Create a random identifier string.
	 *
	 * @access  public
	 */
	public function Identifier ( $pLength = 64 ) {
		
		$length = (int) $pLength;
		
		$id = str_shuffle ( md5 ( microtime() ) );
		
		while ( strlen ( $id ) < $length ) {
			$id .= str_shuffle ( md5 ( microtime() ) );
		}
		
		$return = substr($id, 0, $length);
		
		return ( $return );
	}
	
	/**
	 * Encrypt a string using an optional salt.
	 *
	 * @access  public
	 * @param string $pSalt An optional salt to use
	 */
	public function Encrypt ( $pString, $pSalt = false ) {
		
		if ( !$pSalt ) $pSalt = $this->Salt ();
		
		$sha512 = hash ("sha512", $pSalt . $pString);
      
		$encrypted = $pSalt . $sha512;
		
		return ( $encrypted );
	}
	
	/**
	 * Generate a salt for encryption.
	 *
	 * @access public
	 * @param string $pString A string to turn into a salt.
	 */
	public function Salt ( $pString = null ) {
		if ( $pString ) {
			$salt = substr ( $pString, 0, 16 );
		} else {
			$salt = substr ( md5 ( uniqid ( rand(), true ) ), 0, 16 );
		}
		
		return ( $salt );
	}
	
}
