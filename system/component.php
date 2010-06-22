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

/** Component Class
 * 
 * Base class for Components
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cComponent extends cBase {
	
	var $Controllers;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		// Load the controller
	}
	
	/**
	 * Load a component
	 *
	 * @access  public
	 * @var string $pController Which controller to use
	 * @var string $pView Which view to load
	 * @var string $pView Which controller task to execute
	 * @var array $pData Extended controller data.
	 */
	public function Load ( $pController = null, $pView = null, $pTask = null, $pData = array ( ) ) {
		eval ( GLOBALS );
		
		if ( !$pController ) $pController = $this->_Component;
		$controller = ltrim ( rtrim ( strtolower ( $pController ) ) );
		
		if ( !$pView ) $pView = $this->_Component;
		$view = ltrim ( rtrim ( strtolower ( $pView ) ) );
		
		if ( !$this->_LoadController( $controller ) ) return ( false );
		
		if ( !$pTask ) $pTask = 'display';
		
		$taskname = ltrim ( rtrim ( ucwords ( $pTask ) ) );
		
		$controllername = ltrim ( rtrim ( ucwords ( $controller ) ) );
		
		$this->Controllers->$controllername->_BufferCounter = & $this->_BufferCounter;
		
		$this->Controllers->$controllername->_Controller = $controller;
		$this->Controllers->$controllername->_Component = &$this->_Component;
		
		$this->Controllers->$controllername->$taskname ( $pView, $pData);
		
		return ( true );
	}
	
	/**
	 * Loads the specified controller
	 *
	 * @access  public
	 * @var string $pController Which controller to use
	 */
	private function _LoadController ( $pController = null ) {
		eval ( GLOBALS );
		
		$filename = $zApp->GetPath() . DS . 'components' . DS . $this->_Component . DS . 'controllers' . DS . $pController . '.php';
		
		$controllername = ucwords ( $pController );
		
		$class = 'c' . $controllername . 'Controller';
		
		if ( !file_exists ( $filename ) ) {
			echo __("Controller Not Found", array ( 'name' => $pController ) );
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
	
}
