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

/** Theme Class
 * 
 * Base class for Themes
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cTheme extends cBase {
	
	var $Config;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
 		// Load language configuration.
 		$this->Config = new cConf ();
		$this->Config->Config = $this->Config->Load ("themes");
		
		return ( true );
	}
	
	public function GetStyles () {
		eval (GLOBALS);
		
		// Check if we've already loaded the styles.
		if ( isset ( $this->_styles ) ) return ( $this->_styles );
		
		$paths = $this->Config->GetPath ();
		
		$found = false;
		
		$base = $zApp->GetPath() . DS . 'themes';
		
		$styles = array ();
		
 		foreach ( $paths as $p => $path ) {
 			
 			$location = $zApp->GetPath() . DS . 'themes' . DS . $path . DS . 'style';
 			
 			// Directory does not exist, continue;
 			if (!file_exists ($location)) continue;
 			
 			// Get a list of all css files.
 			$files = glob($location . DS . '*.css');
 			
 			// No styles found, continue
 			if ( count ( $files ) < 1 ) continue;
 			
 			$found = true;
 			
 			foreach ( $files as $f => $file ) {
 				$file = str_replace ($base . DS, '', $file);
 				$styles[] = $file;
 			}
 		
 		} 
 		
 		return ( $styles );
		
	}
	
	public function UseStyles () {
		eval(GLOBALS);
		
		$styles = $this->GetStyles ();
		
		$order = $this->Config->GetConfiguration ( 'order' );
		$skip = $this->Config->GetConfiguration ( 'skip' );
		
		// Reorder the styles according to the configuration
		if ( isset ( $order ) ) {
			if ( isset ( $skip ) ) $skip = split ( ' ', $this->Config->GetConfiguration ( 'skip' ) );
			foreach ( $order as $o => $ostyle ) {
				foreach ( $styles as $s => $style ) {
					if ( strstr ( $style, $ostyle ) ) {
						if ( in_array ( $ostyle, $skip ) ) continue;
						$orderedstyles[] = $style;
					}
				}
			}
		}
		
		$styles = $orderedstyles;
			
		$url = $zApp->GetBaseURL () . '/themes/';
		
		foreach ( $styles as $s => $style ) {
			$location = $url . $style;
    		echo '<link rel="stylesheet" href="' . $location . '" />' . "\n";
		}
	}

}
