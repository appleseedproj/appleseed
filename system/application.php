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

require_once ( ASD_PATH . DS . 'system' . DS . 'router.php' );

SETGLOBAL("zApp");

/** Application Class
 * 
 * Appleseed Application class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cApplication extends cBase {
	
	var $Path;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
        
	/**
	 * Initialize the application
	 *
	 * @access  public
	 */
	public function Initialize ( ) {
		require_once ( ASD_PATH . DS . 'system' . DS . 'configuration.php' );
		
		$this->_LoadLibraries ();
            
		$this->Conf = new cConf ();
		$this->Language = new cLanguage ();
		$this->Router = new cRouter ( );
		
		// Load site path.
		$this->Path ();
		
		// Load site configuration.
		$this->Config = $this->Conf->Load ("configurations");
		
		return ( true );
	} 
	
	/**
	 * Load the system libraries.
	 *
	 * @access  public
	 */
	private function _LoadLibraries ( ) {
		require_once ( ASD_PATH . DS . 'libraries' . DS . 'language.php' );
	}
	
	public function Path () {
		
		if (!isset ($this->Path)) {
			$this->Path = $_SERVER['DOCUMENT_ROOT'];
		}
		
		return ($this->Path);
	}
	
}

/**
 * Global variable declaration function
 * 
 * Allows PHP global variables to be declared global only once.
 * 
 */
function SETGLOBAL($pVar) { global $_G; $_G[]="$pVar"; }
define ("GLOBALS", 'global $_G; foreach ($_G as $g => $glob) { global $$glob; }');

/**
 * Scan directory for other directories
 * 
 */
function scandirs ($pPath) {
	$results = scandir($pPath);

	foreach ($results as $result) {
		if ($result === '.' or $result === '..') continue;

		if (is_dir($pPath . '/' . $result)) {
			$dirs[] = $result;
		}
	}
	
	return ($dirs);
}