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

/** Buffering Class
 * 
 * Tools for page buffering and manipulation.
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cBuffer extends cBase {
	
	var $_buffer;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function LoadFoundation ( $pFoundation ) {
		eval ( GLOBALS );
		
		ob_start ();
		
		require_once ( $pFoundation );
		
		$buffer = ob_get_contents ();
		ob_end_clean ();
		
		$this->_buffer = $buffer;
		
		return ( true );
	}
	
	public function GetBuffer ( ) {
		return ( $this->_buffer );
	}

}
