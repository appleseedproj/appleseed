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

require ( ASD_PATH . DS . 'libraries' . DS . 'external' . DS . 'Swift-4.0.6' . DS . 'swift_required.php' );

/** Crypt Class
 * 
 * Encryptiong class.
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
	
	public function Encrypt ( $pString, $pSalt = false ) {
		
		if ( !$pSalt ) $pSalt = $this->Salt ();
		
		$sha512 = hash ("sha512", $pSalt . $pString);
      
		$encrypted = $pSalt . $sha512;
		
		return ( $encrypted );
	}
	
	public function Salt ( $pString = null ) {
		if ( $pString ) {
			$salt = substr ( $pString, 0, 16 );
		} else {
			$salt = substr(md5(uniqid(rand(), true)), 0, 16);
		}
		
		return ( $salt );
	}
	
}
