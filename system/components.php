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
	
	var $_ComponentCount = 0;

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
	
	/**
	 * Loads all component base classes.
	 *
	 * @access  public
	 */
	public function _Load ( ) {
		eval ( GLOBALS );
		
		
		foreach ( $this->Config->_Components as $c => $component ) {
			
			$filename = $zApp->GetPath () . DS . 'components' . DS . $component . DS . $component . '.php';
			
			if ( !file_exists ( $filename ) ) {
				unset ( $this->Config->_Components[$c] );
				continue;
			}
			
			require_once ( $filename );
			
			$componentname = ucwords ( $component );
			
			$class = 'c' . $componentname;
			
			if ( !class_exists ( $class ) ) {
				unset ( $this->Config->_Components[$c] );
				continue;
			}
			
			$this->$componentname = new $class;
			
		}
		
		return ( true );
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
	public function Go ( $pComponent, $pController = null, $pView = null, $pTask = null, $pData = null ) {
		eval ( GLOBALS );
		
		if (!$pController) $pController = $pComponent;
		if (!$pView) $pView = $pComponent;
		if (!$pTask) $pTask = "Display";
		
		$parameters = array ( 'component' => $pComponent);
		if ( $pController ) $parameters['controller'] = $pController;
		if ( $pView ) $parameters['view'] = $pView;
		if ( $pTask ) $parameters['task'] = $pTask;
		if ( $pData ) $parameters['data'] = $pData;
		
		$this->Buffer->AddToCount ( 'component' );
		
		$this->Buffer->Placeholder ( 'component', $parameters );
		
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
		
		$this->$componentname->_Component = $component;
		
		$this->$componentname->_BufferCounter = & $this->Buffer->_Count['component'];
		
		ob_start ();
		$this->$componentname->Load ( $pController, $pView, $pTask, $pData );
		
		$buffer = ob_get_clean ();
		
		$this->Buffer->Queue ( 'component', $parameters, $buffer );
		
		return ( true );
	}

}
