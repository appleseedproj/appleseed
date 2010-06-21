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

/** Components Class
 * 
 * Component Management
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cComponents extends cBase {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
 		// Load component configurations.
 		$this->Config = new cConf ();
		$this->Config->Config = $this->Config->LoadComponents ();
		
		// Load all component base classes.
		$this->_Load ();
		
	}
	
	public function _Load ( ) {
		eval ( GLOBALS );
		
		
		foreach ( $this->Config->_components as $c => $component ) {
			
			$filename = $zApp->GetPath () . DS . 'components' . DS . $component . DS . $component . '.php';
			
			if ( !file_exists ( $filename ) ) {
				unset ( $this->Config->_components[$c] );
				continue;
			}
			
			require_once ( $filename );
			
			$componentname = ucwords ( $component );
			
			$class = 'c' . $componentname;
			
			if ( !class_exists ( $class ) ) {
				unset ( $this->Config->_components[$c] );
				continue;
			}
			
			$this->$componentname = new $class;
			
		}
		
		return ( true );
	}
	
	public function Go ( $pComponent, $pController, $pView, $pData = array ( ) ) {
		eval ( GLOBALS );
		
		$component = ltrim ( rtrim ( strtolower ( $pComponent ) ) );
		
		// Skip components which use reserved names
		if ( in_array ( $component, $zApp->Reserved () ) ) {
			echo __("Bad Component Name", array ( 'name' => $component ) );
			return ( false );
		}
		
		$componentname = ucwords ( $component );
		
		$class = 'c' . $componentname;
		
		if ( !class_exists ( $class ) ) {
			echo __("Component Not Found", array ( 'name' => $class ) );
			return ( false );
		};
		
		$this->$componentname->_component = $component;
		
		$this->$componentname->Load ( $pController, $pView, $pData );
		
		return ( true );
	}

}
