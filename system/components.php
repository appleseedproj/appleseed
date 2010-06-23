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
	
	private $_ComponentCount = 0;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {
		
 		// Load component configurations.
 		$this->_Config = new cConf ();
		$this->_Config->Set ( "Data",  $this->_Config->LoadComponents() );
		
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
		
		
		foreach ( $this->_Config->_Components as $c => $component ) {
			
			$filename = $zApp->GetPath () . DS . 'components' . DS . $component . DS . $component . '.php';
			
			if ( !file_exists ( $filename ) ) {
				unset ( $this->_Config->_Components[$c] );
				continue;
			}
			
			require_once ( $filename );
			
			$componentname = ucwords ( $component );
			
			$class = 'c' . $componentname;
			
			if ( !class_exists ( $class ) ) {
				unset ( $this->_Config->_Components[$c] );
				continue;
			}
			
			$this->$componentname = new $class();
			
			$this->$componentname->Set ( 'Component', $component);
			
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
		
		$component = ltrim ( rtrim ( strtolower ( $pComponent ) ) );
		
		// Skip components which use reserved names
		if ( in_array ( $component, $zApp->Reserved () ) ) {
			$warning = __("Bad Component Name", array ( 'name' => $component ) );
			$zApp->Logs->Add ( $warning, "Warnings" );
			return ( false );
		}
		
		$componentname = ucwords ( $component );
		
		$class = 'c' . $componentname;
		
		if ( !class_exists ( $class ) ) {
			echo __("Component Not Found", array ( 'name' => $class ) );
			return ( false );
		};
		
		$this->$componentname->Set ( "Component", $component );
		
		ob_start ();
		$this->$componentname->Load ( $pController, $pView, $pTask, $pData );
		
		$bdata = ob_get_clean ();
		
		$Buffer = $this->GetSys ( "Buffer" );
		
		$Buffer->AddToCount ( 'component' );
		
		$Buffer->Placeholder ( 'component', $parameters );
		
		$Buffer->Queue ( 'component', $parameters, $bdata );
		
		return ( true );
	}
	
	public function Talk ( $pComponent, $pRequest, $pData = null ) {
		
		$component = ucwords ( ltrim ( rtrim ( $pComponent ) ) );
		$function = ltrim ( rtrim ( $pRequest ) );
		
		if ( in_array ( $function, get_class_methods ( $this->$component ) ) ) {
			return ( $this->$component->$function ( $pData ) );
		}
		
		return ( false );
	}

}
