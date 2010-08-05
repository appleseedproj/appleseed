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
	
	protected $_System = array ();

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
		
		// Return false if nothing is set.
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
	
	public function AddSys ( $pVariable, $pLocation ) {
		
		$variable = ltrim ( rtrim ( $pVariable ) );
		
		$this->_System[$variable] = $pLocation;
		
		return ( true );
	}
	
	public function GetSys ( $pVariable ) {
		eval ( GLOBALS );
		
		$variable = ltrim ( rtrim ( $pVariable ) );
		$class = 'c' . $variable;
		
		if ( !isset ( $zApp->$variable ) ) {
			if ( class_exists ( $class ) ) {
				$zApp->$variable = new $class ();
			} else if ( file_exists ( $zApp->_System[$variable] ) ) {
				require ( $zApp->_System[$variable] );
				if ( class_exists ( $class ) ) {
					$zApp->$variable = new $class ();
				} else {
					echo __("System Object Not Found", array ( 'name' => $variable ) );
					exit;
				}
			} else {
				echo __("System Object Not Found", array ( 'name' => $variable ) );
				exit;
			}
		}
		
		return ( $zApp->$variable );
	}
	
}