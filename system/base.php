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
	
	/**
	 * Return a private variable
	 *
	 * @access  public
	 */
	public function Get ( $pVariable ) {
		$variable = '_' . ltrim ( rtrim ( $pVariable ) );
		
		if ( !isset ( $this->$variable ) ) return ( false );
		
		return ( $this->$variable );
	}
        
	/**
	 * Set a private variable
	 *
	 * @access  public
	 */
	public function Set ( $pVariable, $pValue ) {
		
		$variable = '_' . ltrim ( rtrim ( $pVariable ) );
		
		$this->$variable = $pValue;
		
		return ( true );
	}
	
	public function GetSys ( $pVariable ) {
		eval ( GLOBALS );
		
		$variable = ltrim ( rtrim ( $pVariable ) );
		
		if ( !isset ( $zApp->$variable ) ) return ( false );
		
		return ( $zApp->$variable );
	}
	
}