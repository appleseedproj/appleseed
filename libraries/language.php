<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   Library
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Language Class
 * 
 * Language and internationalisation functionality.
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cLanguage {
	
	protected $_Config;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
 		// Load language configuration.
 		$this->_Config = new cConf ();
		$this->_Config->Set ( "Data",  $this->_Config->Load ( "languages" ) );
		
		return ( true );
	}
 	
 	/*
 	 * @access public
 	 * @param string Which language to load.  Example: en-US
 	 * @param string Which language file to load.
 	 * @return bool True on success, false on error.
 	 */
 	function Load ($pLanguage, $pContextFile) {
 		eval(GLOBALS);
 		
		$paths = $zApp->Language->_Config->GetPath ();
		
		$found = false;
		
 		foreach ( $paths as $p => $path ) {
 			
 			$location = $zApp->GetPath() . DS . 'languages' . DS . $path . DS . $pLanguage . DS . $pContextFile;
 			// File does not exist, return false. 
 			// _set _system _error
 			if (!file_exists ($location)) continue;
 			
 			$found = true;
 		
 			// File can not be parsed, return false.
 			// _set _system _error
 			if (!$data = parse_ini_file ($location)) {
 				return (false);
 			} // if
 		
 			// Put data into the global cache.
 			foreach ($data as $key => $value) {
 	        	$zApp->setCache ( 'Language', $key, $value );	
 			} // foreach
 		
 		} 
 		
 		return (true);
 	} // Load
 	
 	/**
 	 * @access public
 	 * @param string The untranslated string
 	 * @param array list of variables to sprintf
 	 * @return string
 	 */
 	function _ ($pString, $pParams = array()) {
 		
 		eval(GLOBALS);
 		

        $key = str_replace (' ', '_', $pString);
        $key = strtoupper ($key);
        
 		
        $value = $zApp->GetCache ( 'Language', $key );
        
        if ( $value ) {
        	$return = $value;
        } else {
 		    $return = $pString;
        } // if
        
        if (count ($pParams) >= 1) {
        	$return = sprintfn($return, $pParams);
        } // if
        
        return ($return);
        
 	} // _
 	
} // cLanguage

/** Lang Class
 * 
 * Shorthand alias for cLanguage class.
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cLang extends cLanguage {
}

function __($pString, $pParams = array ()) {
    return cLanguage::_ ($pString, $pParams);   
}
