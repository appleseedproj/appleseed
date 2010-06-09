<?php
/**
 * @author Michael Chisari <michael.chisari@gmail.com
 * @version 0.7.4
 * @package system/language
 * @copyright Copyright (C) 2004 - 2010 The Appleseed Project. All rights reserved.
 * @license GNU General Public License
 */
 
 class cLanguage {
 	
 	/*
 	 * @access public
 	 * @param string Which language to load.  Example: en-US
 	 * @param string Which language file to load.
 	 * @return bool True on success, false on error.
 	 */
 	function Load ($pLanguage, $pContextFile) {
 		global $gCache;
 		
 		$location = 'languages/' . $pLanguage . '/' . $pContextFile;
 		
 		// File does not exist, return false. 
 		// _set _system _error
 		if (!file_exists ($location)) return (false);
 		
 		// File can not be parsed, return false.
 		// _set _system _error
 		if (!$data = parse_ini_file ($location)) {
 			return (false);
 		} // if
 		
 		// Put data into the global cache.
 		foreach ($data as $key => $value) {
 	        $gCache['Language'][$key] = $value;	
 		} // foreach
 		
 		return (true);
 	} // Load
 	
 	/**
 	 * @access public
 	 * @param string The untranslated string
 	 * @param array list of variables to sprintf
 	 * @return string
 	 */
 	function _ ($pString, $pParams = array()) {
 		global $gCache;

        $key = str_replace (' ', '_', $pString);
        $key = strtoupper ($key);
        
        if (isset( $gCache['Language'][$key] ) ) {
        	$return = $gCache['Language'][$key];
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

 function __($pString, $pParams = array ()) {
     return cLanguage::_ ($pString, $pParams);   
 }
