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
        	$return = cLanguage::sprintfn($return, $pParams);
        } // if
        
        return ($return);
        
 	} // _
 	
 	/**
 	 * From: http://php.net/manual/en/function.sprintf.php
     * version of sprintf for cases where named arguments are desired (php syntax)
     *
     * with sprintf: sprintf('second: %2$s ; first: %1$s', '1st', '2nd');
     * with sprintfn: sprintfn('second: %second$s ; first: %first$s', array(
     *  'first' => '1st',
     *  'second'=> '2nd'
     * ));
     *
     * @param string $format sprintf format string, with any number of named arguments
     * @param array $args array of [ 'arg_name' => 'arg value', ... ] replacements to be made
     * @return string|false result of sprintf call, or bool false on error
     */
    function sprintfn ($format, array $args = array()) {
        // map of argument names to their corresponding sprintf numeric argument value
    	$arg_nums = array_slice(array_flip(array_keys(array(0 => 0) + $args)), 1);

        // find the next named argument. each search starts at the end of the previous replacement.
        for ($pos = 0; preg_match('/(?<=%)([a-zA-Z_]\w*)(?=\$)/', $format, $match, PREG_OFFSET_CAPTURE, $pos);) {
            $arg_pos = $match[0][1];
            $arg_len = strlen($match[0][0]);
            $arg_key = $match[1][0];

            // programmer did not supply a value for the named argument found in the format string
            if (! array_key_exists($arg_key, $arg_nums)) {
                user_error("sprintfn(): Missing argument '${arg_key}'", E_USER_WARNING);
                return false;
            }

            // replace the named argument with the corresponding numeric one
            $format = substr_replace($format, $replace = $arg_nums[$arg_key], $arg_pos, $arg_len);
            $pos = $arg_pos + strlen($replace); // skip to end of replacement for next iteration
        }
        return vsprintf($format, array_values($args));
    }
	
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
