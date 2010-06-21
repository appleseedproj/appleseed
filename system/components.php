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
	}
	
	public function Go ( $pComponent, $pController, $pView, $pData = array ( ) ) {
		eval ( GLOBALS );
		
		$component = ltrim ( rtrim ( strtolower ( $pComponent ) ) );
		
		// Skip components which use reserved names
		if ( in_array ( $component, $zApp->Reserved () ) ) {
			echo __("Bad Component Name", array ( 'name' => $component ) );
			return ( false );
		}
		
		$filename = $zApp->GetPath() . DS . 'components' . DS . $component . DS . $component . '.php';
		
		if ( !file_exists ( $filename ) ) {
			echo __("Component Not Found", array ( 'name' => $component ) );
			return ( false );
		};
		
		require_once ( $filename );
		
		$componentname = ucwords ( $component );
		
		$class = 'c' . $componentname;
		
		$zApp->$componentname = new $class;
		
		$zApp->$componentname->_component = $component;
		
		$zApp->$componentname->Load ( $pController, $pView, $pData );
		
		return ( true );
	}

}

/** Component Class
 * 
 * Base class for Component
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cComponent extends cBase {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
		// Load the controller
	}
	
	public function Load ( $pController = null, $pView = null, $pData = array ( ) ) {
		eval ( GLOBALS );
		
		if ( !$this->_LoadController( $pController ) ) return ( false );
		
		if ( !$this->_LoadView( $pView ) ) return ( false );
		
		$controllername = ltrim ( rtrim ( ucwords ( $this->_component ) ) );
		
		echo "<pre>";
		print_r ($this); exit;
		
		$this->Controllers->$controllername->Display ();
		
		return ( true );
	}
	
	private function _LoadController ( $pController = null ) {
		eval ( GLOBALS );
		
		if ( !$pController ) $pController = $this->_component;
		
		$controller = ltrim ( rtrim ( strtolower ( $pController ) ) );
		
		$filename = $zApp->GetPath() . DS . 'components' . DS . $this->_component . DS . 'controllers' . DS . $controller . '.php';
		
		$controllername = ucwords ( $controller );
		
		$class = 'c' . $controllername . 'Controller';
		
		if ( !file_exists ( $filename ) ) {
			echo __("Controller Not Found", array ( 'name' => $controller ) );
			return ( false );
		}
		
		require_once ( $filename );
		
		if ( !class_exists ( $class ) ) {
			echo __("Controller Not Found", array ( 'name' => $class ) );
			return ( false );
		}
		
		$this->Controllers->$controllername = new $class;
		
		return ( true );
	}
	private function _LoadView ( $pView = null ) {
		eval ( GLOBALS );
		
		if ( !$pView ) $pView = $this->_component;
		
		$view = ltrim ( rtrim ( strtolower ( $pView ) ) );
		
		$filename = $zApp->GetPath() . DS . 'components' . DS . $this->_component . DS . 'views' . DS . $view . '.php';
		
		$viewname = ucwords ( $view );
		
		$class = 'c' . $viewname . 'View';
		
		if ( !file_exists ( $filename ) ) {
			echo __("View Not Found", array ( 'name' => $view ) );
			return ( false );
		}
		
		require_once ( $filename );
		
		if ( !class_exists ( $class ) ) {
			echo __("View Not Found", array ( 'name' => $class ) );
			return ( false );
		}
		
		$this->Views->$viewname = new $class;
		
		return ( true );
	}

}
