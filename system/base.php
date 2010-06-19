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

/** Base Class
 * 
 * Base class for all Appleseed classes.  Provides get and set functions.
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cBase {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function Get ( $pVariable ) {
		
		if ( !isset ( $this->$pVariable ) ) return ( false );
		
		return ( $this->$pVariable );
	}
        
	public function Set ( $pVariable, $pValue ) {
		
		$this->$pVariable = $pValue;
		
		return ( true );
	}
        
}