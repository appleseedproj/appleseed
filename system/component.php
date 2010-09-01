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
	
	protected $_Context;
	
	protected $_Controller;
	protected $_View;
	protected $_Instance = 1;
	
	protected $_API = false;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {
		
		parent::__construct();
	}
	
	/**
	 * Load a component
	 *
	 * @access  public
	 * @param string $pController Which controller to use
	 * @param string $pView Which view to load
	 * @param string $pView Which controller task to execute
	 * @param array $pData Extended controller data.
	 */
	public function Load ( $pController = null, $pView = null, $pTask = null, $pData = array ( ) ) {
		eval ( GLOBALS );
		
		// If no controller is specified, use the default.
		if ( !$pController ) $pController = $this->Component;
		
		// Switch to aliased controller if using the default
		if ( strtolower ( $pController ) == strtolower ( $this->_Alias ) ) $pController = strtolower ( $this->_Component );
		
		$controller = ltrim ( rtrim ( strtolower ( $pController ) ) );
		
		if ( !$pView ) $pView = $this->_Component;
		$view = ltrim ( rtrim ( strtolower ( $pView ) ) );
		
		if ( !$this->_LoadController( $controller ) ) return ( false );
		
		if ( !$pTask ) $pTask = 'display';
		
		$controllername = ucwords ( strtolower ( ltrim ( rtrim ( $controller ) ) ) );
		
		$taskname = ucwords ( strtolower ( ltrim ( rtrim ( $pTask ) ) ) );
		
		$this->Set ( "Controller", $controller );
		$this->Set ( "View", $view );
		
		$this->Controllers->$controllername->Set ( "Component", $this->Get ( "Component" ) ) ;
		$this->Controllers->$controllername->Set ( "Alias", $this->Get ( "Alias" ) ) ;
		
		$this->Controllers->$controllername->Set ( "Instance", $this->_Instance );
		
		$context = $this->CreateContext( $this->_Controller, $this->_View );
		
		$this->Controllers->$controllername->Set ( "Context", $context);
		
		$this->Controllers->$controllername->Set ( "Config", $this->_Config );
		
		// Store the current session context
		$oldSessionContext = $this->GetSys ( "Session" )->Context();
		
		// Set the current session context
		$this->GetSys ( "Session" )->Context ( $this->Get ( "Context" ) );
		
		$this->GetSys ( "Event" )->Trigger ( "Begin", $this->_Component, $pTask ); 
		
		$this->Controllers->$controllername->$taskname ( $pView, $pData);
		
		$this->GetSys ( "Event" )->Trigger ( "End", $this->_Component, $pTask ); 
		
		// Restore the stored session context (for embedded component calls)
		$this->GetSys ( "Session" )->Context ( $oldSessionContext );
		
		return ( true );
	}
	
	public function AddToInstance ( ) {
		$this->_Instance++;
		
		return ( true );
	}
	
	/**
	 * Create the context string of the current instance of the component.
	 * 
	 * Context: <component>.<controller>.<INSTANCE#>.<view>
	 *
	 * @access  public
	 * @param string $pController Which controller is being used.
	 */
	public function CreateContext ( $pController ) {
		
		$contextData = array ( $pController, $this->_Component, $this->_Instance, $this->_View );
		
		$context = join ( '.', $contextData );
		
		$this->_Context = $context;
		
		return ( $context );
	}
	
	/**
	 * Loads the specified controller
	 *
	 * @access  private
	 * @param string $pController Which controller to use
	 */
	private function _LoadController ( $pController = null ) {
		eval ( GLOBALS );
		
		// If no controller is specified, use the default.
		if ( !$pController ) $pController = $this->Component;
		
		// Switch to aliased controller if using the default
		if ( strtolower ( $pController ) == strtolower ( $this->_Alias ) ) $pController = strtolower ( $this->_Component );
		
		$filename = $zApp->GetPath() . DS . 'components' . DS . $this->_Component . DS . 'controllers' . DS . $pController . '.php';
		
		$controllername = ucwords ( $pController );
		$componentname = ucwords ( $this->_Component );
		
		$class = 'c' . $componentname . $controllername . 'Controller';
		
		if ( !is_file ( $filename ) ) {
			echo __("Controller Not Found", array ( 'name' => $pController ) );
			return ( false );
		}
		
		if ( !class_exists ( $class ) ) include ( $filename );
		
		if ( !class_exists ( $class ) ) {
			echo __("Controller Not Found", array ( 'name' => $class ) );
			return ( false );
		}
		
		$this->Controllers->$controllername = new $class();
		
		return ( true );
	}
	
}
