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

/** Configuration Class
 * 
 * Base class for configurations
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cConfiguration extends cBase {
	
	protected $_Data;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
        
	/**
	 * Load a configuration file, using inheritance if necessary
	 *
	 * @access  public
	 * @param string pFilename
	 * @param array pDirectories
	 */
	public function Load ( $pDirectory, $pInheritance = true ) {
		// Global variables
		eval (GLOBALS);

		$location = $zApp->GetPath () . DS . $pDirectory;
		
		// Check for other configuration directories.
		$dirs = scandirs ($location);
		
		// Load all enabled configurations
		foreach ( $dirs as $d => $dir ) {
			$configurations[$dir] = new stdClass ();
			$configurations[$dir]->Directory = $dir;
			$file = $zApp->GetPath () . DS . $pDirectory . DS . $dir . DS . $dir . '.conf';
			
			if ( !$configurations[$dir]->_Data = parse_ini_file ($file) ) {
				// Load failed.  Set a warning and unset value.
				unset ($configurations[$dir]);
				continue;
			}
			
			if ( ( strtolower ( $configurations[$dir]->_Data['enabled'] ) != 'true' ) ) {
				unset ($configurations[$dir]);
				continue;
			}
			
		}
		
		// Set inheritance
		$dirs = $configurations;
		
		// If no configurations were found, error out.
		if ( count ( $dirs ) == 0 ) {
			die ( "No configurations were found or enabled: $pDirectory");
		}
		
		// Count inheritance levels.
		$inheritancecount = 0;
		
		do {
			foreach ( $dirs as $dir => $values ) {
				$inherit = $configurations[$dir]->_Data['inherit'];
				
				$inheritanceflag = false;
				
				if ( isset ( $inherit ) ) {
		
					// If inheriting from self, continue
					if ($configurations[$dir]->Directory == $inherit) {
						$configurations[$dir]->Warnings[] = " Cannot Inherit $inherit From Itself. ";
						continue;
					}
					
					// Check if the inherited parent exists
					if ( !isset ( $configurations[$inherit] ) ) {
						// Set a warning and continue.
						$configurations[$dir]->Warnings[] = " Cannot Inherit Values From '$inherit'.  Does Not Exist Or Is Disabled.";
						continue;
					} 
					
					$inheritancecount++;
					
					// Limit inheritance to three levels deep
					if ($inheritancecount > 3) {
						die ( "Error:  Configuration Inheritance Greater Than 3 Levels.  Please Resolve." );
						
					} 
					
					// Set the values as a child of parent
					$configurations[$inherit]->Child = $configurations[$dir];
					unset ($configurations[$dir]);
					
					$inheritanceflag = true;
				}
			}
			
		} while ( $inheritanceflag );
		
		// Check to see if there's more than one parent left.
		if ( count ( $configurations ) > 1 ) {
			die ( "More Than One Parent Configuration Is Enabled.  Please Resolve." );
		} 
		
		$parent = key ( $configurations );
		
		// Traverse and inherit values
		$final = $this->_Inherit ( $configurations[$parent] );
		
		$config = $final->_Data;
		
		// Internal list of the path to the final configuration
		$config['_path'] = array_reverse ( $this->_path );
		
		// Internal list of variables which were cleared and by which configuration
		if ( count ( $this->_cleared ) > 0 ) $config['_cleared'] = array_reverse ( $this->_cleared );
		
		unset ( $final->Directory );
		unset ( $config['enabled'] );
		
		return ( $config );
	}
	
	/**
	 * Loads a configuration value.
	 *
	 * @access  public
	 * @param array pVariable
	 */
	function GetConfiguration ( $pVariable ) {
		
		if ( !isset ( $this->_Data[$pVariable] ) ) return ( false );
		
		return ( $this->_Data[$pVariable] );
	}
	
	/**
	 * Loads the ordered path to the child configuration
	 *
	 * @access  public
	 */
	function GetPath ( ) {
		return ( $this->_Data['_path'] );
	}
	
	/**
	 * Recursively inherit values
	 *
	 * @access  private
	 * @param object pConfiguration
	 */
	private function _Inherit ( $pConfiguration ) {
		$parent = $pConfiguration;
		$child = $parent->Child;
		
		if ( $clear = $child->_Data['clear'] ) {
			$parent = $this->_Clear ( $parent, $child );
		}
			
		if ( isset ($child ) ) {
			$child = $this->_Inherit ($child);
			
			// Move all parent values to child.
			foreach ( $child->_Data as $key => $value ) {
				if ( is_array ( $parent->_Data[$key] ) ) {
					$parent->_Data[$key] = array_merge ( $parent->_Data[$key], $value );
				} else {
					$parent->_Data[$key] = $value;
				} 
			} 
			
			$this->_path[] = $parent->Directory;
			unset ( $parent->_Data['inherit'] );
			unset ( $parent->Child );
			
			return ( $parent );
		} else {
			$this->_path[] = $parent->Directory;
			return ( $parent );
		} 
	}
	
	/**
	 * Clear parent values as specified by child
	 *
	 * @access  private
	 * @param object pParent
	 * @param object pChild
	 */
	private function _Clear ( $pParent, $pChild ) {
		
		$clearlist = array_filter ( split ( ' ', $pChild->_Data['clear'] ) );
		
		if ( count ( $clearlist ) < 1 ) {
			return ( false );
		}
		
		foreach ( $clearlist as $c => $clear ) {
			// Special variables which cannot be cleared
			if ( in_array ( $clear, array ( "inherit", "clear" ) ) ) continue;
			
			$this->_cleared[] = $clear . ' [by ' . $pChild->Directory . '] ';
			unset ( $pParent->_Data[$clear] );
		} 
		
		return ( $pParent );
	}
	
	/**
	 * Loads all of the component configuration files
	 *
	 * @access  public
	 */
	public function LoadComponents ( ) {
		eval ( GLOBALS );
		
		$Config = $this->GetSys ( "Config" );
		
		$configpaths = $Config->GetPath();
		
		$componentdir = $zApp->GetPath() . DS . 'components';
		
		$components = scandirs ( $componentdir );
		
		$config = array ();
		foreach ( $components as $comp => $component ) {
			$filename = $componentdir . DS . $component . DS . $component . '.conf';
			
			if ( is_file ( $filename ) ) {
				$path[$component][] = $filename;
			}
			
			foreach ( $configpaths as $cpath => $configpath ) {
				$filename = $zApp->GetPath() . DS . 'configurations' . DS . $configpath . DS . 'components' . DS . $component . '.conf';
				if ( is_file ( $filename ) ) {
					$path[$component][] = $filename;
				}
			}
			
			// No configuration files found, continue loop
			if ( !isset ( $path[$component] ) ) continue;
			
			
			$config[$component] = array();
			
			foreach ( $path[$component] as $p => $filename ) {
				$currentvalues = $config[$component];
				$configvalues = parse_ini_file ( $filename );
				
				if ( $configvalues['clearall'] == 'true' ) {
					$currentvalues = array ();
					unset ( $configvalues['clearall'] );
				}
				
				$config[$component] = array_merge ( $currentvalues, $configvalues );
			}
			
			// If the component isn't enabled, then unset the values and continue.
			if ($config[$component]['enabled'] != 'true' ) {
				unset ($config[$component]);
				continue;
			} else {
				$this->_Components[] = $component;
			}
			
		}
		
		return ($config);
		
	}
	/**
	 * Loads all of the hook configuration files
	 *
	 * @access  public
	 */
	public function LoadHooks ( ) {
		eval ( GLOBALS );
		
		$Config = $this->GetSys ( "Config" );
		
		$configpaths = $Config->GetPath();
		
		$componentdir = $zApp->GetPath() . DS . 'hooks';
		
		$components = scandirs ( $componentdir );
		
		$config = array ();
		foreach ( $components as $comp => $component ) {
			$hookdir = $zApp->GetPath() . DS . 'hooks' . DS . $component;
		
			$hooks = scandirs ( $hookdir );
			
			foreach ( $hooks as $h => $hook ) {
				
				$filename = $hookdir . DS . $hook . DS . $hook . '.conf';
			
				if ( is_file ( $filename ) ) {
					$path[$hook][] = $filename;
				}
			
				foreach ( $configpaths as $cpath => $configpath ) {
					$filename = $zApp->GetPath() . DS . 'configurations' . DS . $configpath . DS . 'hooks' . DS . $component . DS . $hook . '.conf';
					if ( is_file ( $filename ) ) {
						$path[$hook][] = $filename;
					}
				}
				
				// No configuration files found, continue loop
				if ( !isset ( $path[$hook] ) ) continue;
				
				
				$config[$component][$hook] = array();
				
				foreach ( $path[$hook] as $p => $filename ) {
					$currentvalues = $config[$component][$hook];
					$configvalues = parse_ini_file ( $filename );
					
					if ( $configvalues['clearall'] == 'true' ) {
						$currentvalues = array ();
						unset ( $configvalues['clearall'] );
					}
					
					$config[$component][$hook] = array_merge ( $currentvalues, $configvalues );
				}
				
				// If the hook isn't enabled, then unset the values and continue.
				if ($config[$component][$hook]['enabled'] != 'true' ) {
					unset ($config[$component][$hook]);
					continue;
				} else {
					$this->_Hooks[$component][] = $hook;
				}
				
			}
			
			if ( count ( $config[$component] ) < 1 ) {
				unset ( $config [$component] );
			}
			
		}
		
		return ($config);
		
	}
	
}

/** Conf Class
 * 
 * Alias class for cConfigurations
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cConf extends cConfiguration { }