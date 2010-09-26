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

require ( ASD_PATH . DS . 'libraries' . DS . 'external' . DS . 'htmLawed-1.1.9.4' . DS . 'htmLawed.php' );

/** Purifier Class
 * 
 * Purifier and access management.
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cPurifier {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function Purify ( $pString ) {
		
		$return = htmLawed ( $pString, array ( "safe" => 1 ) );
		
		return ( $return );
	}

}
