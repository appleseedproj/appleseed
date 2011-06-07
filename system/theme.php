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
	
	protected $_Config;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
 		// Load language configuration.
 		$this->_Config = new cConf ();
		$this->_Config->Set ( "Data",  $this->_Config->Load ( "themes" ) );
		
		return ( true );
	}
	
	/**
	 * Get an ordered list of the available styles, using inheritance
	 *
	 * @access  public
	 */
	public function GetStyles () {
		eval (GLOBALS);
		
		// Check if we've already loaded the styles.
		if ( isset ( $this->_styles ) ) return ( $this->_styles );
		
		$paths = $this->_Config->GetPath ();

		$extensions = explode ( ' ', $this->_Config->GetConfiguration ( 'extensions', 'css' ) );

		$found = false;
		
		$base = $zApp->GetPath() . DS . 'themes';
		
		$styles = array ();
		
		foreach ( $extensions as $e => $extension ) {
 			foreach ( $paths as $p => $path ) {
 			
 				$directory = $zApp->GetPath() . '/themes/' . $path . '/styles/init/';
 				$location = $path . '/styles/init/';
 			
 				// Directory does not exist, continue;
 				if (!is_dir ($directory)) continue;
 			
 				// Get a list of all css files.
                $Storage = Wob::_('Storage');

                $files = $Storage->Scan ( $directory, $extension );

 				// No styles found, continue
 				if ( count ( $files ) < 1 ) continue;
 			
 				$found = true;
 			
 				foreach ( $files as $f => $file ) {
 					$file = $location . $file;
 					$styles[] = $file;
 				}
 		
 			} 
		}

		$Router = Wob::_( 'Router' );
		$foundation = $Router->Get ('Foundation' );

		foreach ( $extensions as $e => $extension ) {
			$foundation = str_replace ( '.php', '.' . $extension, $foundation );

			// Load the foundation styles
			$foundationStyle = null;
			foreach ( $paths as $p => $path ) {
				$file = ASD_PATH . 'themes/' . $path . '/styles/foundations' . $foundation;
				$url = $path . '/styles/foundations' . $foundation;
				if ( file_exists ( $file ) ) {
					$foundationStyle = $url;
				}
			}
		}

		if ( $foundationStyle )
			$styles[] = $foundationStyle;

 		return ( $styles );
	}
	
	/**
	 * Output the html style declarations
	 *
	 * @access  public
	 */
	public function UseStyles () {
		eval(GLOBALS);
		
		$styles = $this->GetStyles ();
		
		$order = $this->_Config->GetConfiguration ( 'order' );
		$skip = $this->_Config->GetConfiguration ( 'skip' );
		
		// Reorder the styles according to the configuration
		if ( isset ( $order ) ) {
			if ( isset ( $skip ) ) $skip = explode ( ' ', $this->_Config->GetConfiguration ( 'skip' ) );
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
		
		return ( true );
	}

}
