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

/** Controller Class
 * 
 * Base class for controllers
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cController extends cBase {
	
	protected $_Models;
	
	protected $_Instance = 0;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {
		
		parent::__construct();
	}

	/**
	 * Displays a view
	 *
	 * @access  public
	 * @param string $pView Which view file to load
	 * @param array $pData Extended data array
	 */
	public function Display ( $pView = null, $pData = null) {
		eval ( GLOBALS );
		
		if ( !$this->Form = $this->GetView ( $pView ) ) return ( false );
		
		$this->Form->Display();
		
		return ( true );
	}
	
	
	/**
	 * Loads a view
	 *
	 * @access  public
	 * @param string $pView Which view file to load
	 */
	public function LoadView ( $pView ) {
		eval ( GLOBALS );
		
		// In case it was accidentally specified, remove the php extension from view name.
		$pView = str_replace ( '.php', '', $pView );
		
		$viewpath = $this->_GetViewPath ( $pView ) ;
		
		if ( $viewpath ) {
			ob_start ();
			include ( $viewpath );
			return ( ob_get_clean () );
		}
		
		return ( false );
	}
	
	public function GetView ( $pView ) {
		eval ( GLOBALS );
		
		$form = clone $this->GetSys ( "HTML" );
		
		$form->Load ( $this->LoadView ( $pView ), $pView );
		
		return ( $form );
	}
	
	/**
	 * Determines the view path, using inheritence
	 *
	 * @access  private
	 * @param string $pView Which view file to load
	 */
	private function _GetViewPath ( $pView = null ) {
		eval ( GLOBALS );
		
		// If no view is specified, use the default.
		if ( !$pView ) $pView = $this->_Component;
		
		// Switch to aliased controller if using the default
		if ( strtolower ( $pView ) == strtolower ( $this->_Alias ) ) $pView = strtolower ( $this->_Component );
		
		$Theme = $this->GetSys ( "Theme" );
		$ThemeConfig = $Theme->Get ( "Config" );
		$themepath = $ThemeConfig->GetPath();
		
		$filename = $zApp->GetPath() . DS . 'components' . DS . $this->_Component . DS . 'views' . DS . $pView . '.php';
		if ( is_file ( $filename ) ) $return = $filename;
		
		foreach ( $themepath as $t => $theme ) {
			$filename = $zApp->GetPath() . DS . 'themes' . DS . $theme . DS . 'views' . DS . $this->_Component . DS . $pView . '.php';
			if ( is_file ( $filename ) ) $return = $filename;
		}
		
		if ( !$return ) {
			echo __("View Not Found", array ( 'name' => $pView ) );
			return ( false );
		}
		
		return ($return);
	}
	
	/**
	 * Gets the buffer counter of the current component.
	 *
	 * @access  public
	 */
	public function GetBufferCounter ( ) {
		
		return ( $this->_BufferCounter );
	}
	
	/**
	 * Creates and returns the specified model
	 *
	 * @access  public
	 * @param string $pSuffix Suffix to specify an additional model
	 */
	public function GetModel ( $pSuffix = null, $pTable = null ) {
		eval ( GLOBALS );
		
		// We cannot use a suffix which is the same as the default model name (ie, component name).
		if ( strtolower ( $pSuffix ) == $this->_Component ) {
			$warning = __('Model suffix and default model name ("%name$s") cannot be the same', array ( 'name' => $pSuffix ) );
			$zApp->GetSys ( "Logs" )->Add ( $warning, "Warnings" );
			$pSuffix = null;
		}
		
		$model = ucwords ( strtolower ( $this->_Component ) ) . ucwords ( strtolower ( $pSuffix ) );
		
		// If model has already been created, return it.
		if ( isset ( $this->_Models->$model ) ) return ( $this->_Models->$model );
		
		$class = 'c' . ucwords ( strtolower ( $this->_Component ) ) . ucwords ( strtolower ( $pSuffix ) ) . 'Model';
		
		// If class is already available, just create it and return it.
		if ( class_exists ( $class ) ) {
			$this->_Models->$model = new $class ( $pTable );
			return ( $this->_Models->$model );
		}
		
		if ( $pSuffix ) {
			$file = strtolower ( $pSuffix ). '.php';
		} else {
			$file = $this->_Component . '.php';
		}
		
		$filename = $zApp->GetPath() . DS . 'components' . DS . $this->_Component . DS . 'models' . DS . $file;
		
		if ( !is_file ( $filename ) ) {
			echo __("Model Not Found", array ( 'name' => $model ) );
		}
		
		require ( $filename );
		
		$this->_Models->$model = new $class ( $pTable );
		
		return ( $this->_Models->$model );
		
	}
	
	/**
	 * Calls a controller task with begin & after event triggers.
	 *
	 * @access  public
	 * @param string $pTask Task to execute
	 */
	function Go ( $pTask = "display" ) {
		
		$this->GetSys ( "Event" )->Trigger ( "Begin", $this->_Component, $pTask ); 
		
		$this->$pTask ();
		
		$this->GetSys ( "Event" )->Trigger ( "End", $this->_Component, $pTask ); 
		
		return ( true );
	}
	
	/**
	 * Shorthand for event trigger method.  Determines the event and component origins automatically.
	 *
	 * @access  public
	 * @param string $pEvent Which event to trigger
	 * @param array $pEvent Extra data to pass to event hook
	 */
	function EventTrigger ( string $pEvent, array $pData = null) {
		
		$backtrace = debug_backtrace();
		
		$function = $backtrace[1]['function'];
		$class = $backtrace[1]['class'];
		
		$component = preg_replace ( "/^c/", "", $class );
		$component = preg_replace ( "/Controller$/", "", $component );
		
		$event = ltrim ( rtrim ( $pEvent ) ); 
		
		$return = $this->GetSys ( "Event" )->Trigger ( $pEvent, $component, $function, $pData );
		
		return ( $return );
	}
	
	/**
	 * Shortcut to cComponents Talk method
	 *
	 * @access  public
	 * @param string $pEvent Which event to trigger
	 * @param array $pEvent Extra data to pass to event hook
	 */
	public function Talk ( $pComponent, $pRequest, $pData = null ) {
		
		$return = $this->GetSys ( "Components" )->Talk ( $pComponent, $pRequest, $pData );
		
		return ( $return );
	}

}
