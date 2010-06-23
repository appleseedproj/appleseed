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

/** Hook Class
 * 
 * Base class for Hooks
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cHooks extends cBase {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
 		// Load hook configurations.
 		$this->_Config = new cConf ();
		$this->_Config->_Config = $this->_Config->LoadHooks ();
		
		// Load all hook base classes.
		$this->_Load ();
	}
        
	/**
	 * Loads all hook base classes.
	 *
	 * @access  public
	 */
	public function _Load ( ) {
		eval ( GLOBALS );
		
		foreach ( $this->_Config->_Hooks as $c => $component ) {
			
			foreach ( $component as $h => $hook ) {
			
				$filename = $zApp->GetPath () . DS . 'hooks' . DS . $c . DS . $hook . DS . $hook . '.php';
				
				$hookname = ucwords ( $hook );
			
				$class = 'c' . $hookname . "Hook";
				
				if ( !is_file ( $filename ) ) {
					unset ( $this->_Config->_Hooks[$c] );
					continue;
				}
			
				require_once ( $filename );
				
				if ( !class_exists ( $class ) ) {
					unset ( $this->_Config->_Hooks[$c] );
					continue;
				}
			
				$this->$hookname = new $class;
				
				$this->_Hooks->$hookname = $this->$hookname;
			
			}
		}
		
		$this->_Hooks->_Config = $this->_Config;
		
		return ( true );
	}
	
	public function GetHooks ( ) {
		
		return ( $this->_Hooks );
	}
	

	public function GetHookNames ( ) {
		
		return ( $this->_Config->_Hooks );
	}
	

}
