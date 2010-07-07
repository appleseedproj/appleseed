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
		
		$configdata = $this->_Config->Get ( "Data" );
		
		// Create the real components
		foreach ( $this->_Config->_Components as $c => $component ) {
			
			$filename = $zApp->GetPath () . DS . 'components' . DS . $component . DS . $component . '.php';
			
			if ( !is_file ( $filename ) ) {
				unset ( $this->_Config->_Components[$c] );
				continue;
			}
			
			require_once ( $filename );
			
			$componentname = ucwords ( strtolower ( $component ) );
			
			$class = 'c' . $componentname;
			
			if ( !class_exists ( $class ) ) {
				unset ( $this->_Config->_Components[$c] );
				continue;
			}
			
			
			$this->$componentname = new $class();
			
			$this->$componentname->Set ("Config", $configdata[$component] );
			
			$this->$componentname->Set ( 'Component', $component);
			
		}
		
		// Create the component aliases
		foreach ( $this->_Config->_Components as $c => $component ) {
			$componentname = ucwords ( strtolower ( $component ) );
			
			// Set an alias or set of aliases to this component.
			if ( isset ( $configdata[$component]['alias'] ) ) {
				$aliases = $configdata[$component]['alias'];
				if ( is_array ( $aliases ) ) {
					foreach ( $aliases as $a => $alias ) {
						$alias = ucwords ( strtolower ( ltrim ( rtrim ( $alias ) ) ) );
						if ( !isset ( $this->$alias ) ) {
							$this->$alias = clone $this->$componentname;
							$this->$alias->Set ( "Component", $component );
							$this->$alias->Set ( "Alias", $alias );
						} else {
							$warning = __("Alias Name Exists", array ( 'name' => $alias ) );
							$zApp->Logs->Add ( $warning, "Warnings" );
						}
					} 
				} else {
					if ( !isset ( $this->$aliases ) ) {
						$aliases = ucwords ( strtolower ( ltrim ( rtrim ( $aliases ) ) ) );
						$this->$aliases = clone $this->$componentname;
						$this->$aliases->Set ( "Component", $component );
						$this->$aliases->Set ( "Alias", $aliases );
					} else {
						$warning = __("Alias Name Exists", array ( 'name' => $aliases ) );
						$zApp->Logs->Add ( $warning, "Warnings" );
					}
				}
			} 
		}
		
		return ( true );
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
	public function Go ( $pComponent, $pController = null, $pView = null, $pTask = null, $pData = null ) {
		eval ( GLOBALS );
		
		// Overwrite the Controller from Request data.
		if ( cRequest::Get ('Controller') ) {
			$pController = cRequest::Get ( 'Controller' );
		} else {
			if ( !$pController ) $pController = $pComponent;
		}
		
		// Overwrite the View from Request data.
		if ( cRequest::Get ('View') ) {
			$pView = cRequest::Get ( 'View' );
		} else {
			if ( !$pView ) $pView = $pComponent;
		}
		
		// Overwrite the Task from Request data.
		if ( cRequest::Get ('Task') ) {
			$pTask = cRequest::Get ( 'Task' );
		} else {
			if ( !$pTask ) $pTask = 'display';
		}
		
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
		
		$componentname = ucwords ( strtolower ( $component ) );
		
		if ( !isset ( $this->$componentname ) ) {
			echo __("Component Not Found", array ( 'name' => $componentname ) );
			return ( false );
		};
		
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
		
		$component = ucwords ( strtolower ( ltrim ( rtrim ( $pComponent ) ) ) );
		$function = ltrim ( rtrim ( $pRequest ) );
		
		if ( in_array ( $function, get_class_methods ( $this->$component ) ) ) {
			return ( $this->$component->$function ( $pData ) );
		}
		
		return ( false );
	}

}
