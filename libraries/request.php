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

/** Request Class
 * 
 * Handles POST/GET/REQUEST data.
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cRequest {

        /**
         * Constructor
         *
         * @access  public
         */
        public function __construct ( ) {       
        }
        
        public function Get ( $pVariable, $pDefault = null ) {
        	
        	// Makes all request variable names case insensitive.
        	$variable = strtolower ( rtrim ( ltrim ( $pVariable ) ) );
        	
        	foreach ( $_REQUEST as $key => $value ) {
        		$lowerkey = strtolower ( $key );
        		$request[$lowerkey] = $_REQUEST[$key];
        	}
        	
        	if ( !$request[$variable] ) return ( $pDefault );
        	
        	return ( $request[$variable] );
        	
        }

}
