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

		$this->_Hooks = new stdClass();
		
		foreach ( $this->_Config->_Hooks as $h => $hook ) {
			
				$filename = $zApp->GetPath () . DS . 'hooks' . DS . $hook . DS . $hook . '.php';
				
				$hookname = ltrim ( rtrim ( $hook ) );
				
				$hookref = $hookname;
			
				$class = 'c' . $hookname . "Hook";
				
				if ( !is_file ( $filename ) ) {
					unset ( $this->_Config->_Hooks[$h] );
					continue;
				}
			
				require ( $filename );
				
				if ( !class_exists ( $class ) ) {
					unset ( $this->_Config->_Hooks[$c] );
					continue;
				}
			
				$this->$hookref = new $class;

				$this->$hookref->Set ( "Config", new cConf() );
				$this->$hookref->Get ( 'Config' )->Set ( 'Data', $this->_Config->_Config[$hookname] );

				$this->$hookref->_Config = $this->_Config->_Config[$hookname];
				
				$this->_Hooks->$hookref = $this->$hookref;
			
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
