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
	
	protected $_Request;
	protected $_Unassigned;
	protected $_URI;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
		foreach ( $_REQUEST as $key => $value ) {
			$lowerkey = strtolower ( $key );
			$this->_Raw[$lowerkey] = $_REQUEST[$key];
			if ( is_array ( $_REQUEST[$key] ) ) {
				$requests = $_REQUEST[$key];
				foreach ( $requests as $r => $request ) {
					/*
					 * @todo If we have nested arrays, this will break it.
					 * @todo On the other hand, why are you using nested arrays in a REQUEST in the first place?
					 *
					 */
					$requests[$r] = htmlentities ( strip_tags ( $request ) );
				}
				$this->_Request[$lowerkey] = $requests;
			} else {
				$this->_Request[$lowerkey] = htmlentities ( strip_tags ( $_REQUEST[$key] ) );
			}
		}
		
		$this->_Unassigned = array ();
		
		return ( true );
	}

	public function Get ( $pVariable = null , $pDefault = null ) {
		
		// Makes all request variable names case insensitive.
		$variable = strtolower ( rtrim ( ltrim ( $pVariable ) ) );
		
		if ( !$this->_URI ) $this->_ParseURI();
		
		if ( $pVariable === null ) return ( $this->_Request );
		
		if ( !$this->_Request[$variable] ) return ( $pDefault );
		
		return ( $this->_Request[$variable] );

	}
	
	protected function _ParseURI ( ) {
		eval ( GLOBALS );
		
		$this->_URI = substr ( $_SERVER['REQUEST_URI'], 1, strlen ( $_SERVER['REQUEST_URI'] ) );
		$pattern = $zApp->GetSys ( "Router" )->Get ( "Route" );
		
		$this->_URI = preg_replace ( '/\/$/', '', $this->_URI );
		preg_match ( $pattern, $this->_URI, $returns );
		
		// Discard the first element, which is the full string.
		unset ( $returns[0] );
		
		foreach ( $returns as $r => $return ) {
		
			$matches = explode ( '/', $return );
		
			foreach ( $matches as $m => $match ) {
				if ( strstr ( $match, ',' ) ) {
					list ( $key, $value ) = explode ( ',', $match, 2 );
					$key = strtolower ( $key );
					$this->_Request[$key] = strip_tags ( $value );
				} else {
					$this->_Unassigned[] = $match;
					$key = count ( $this->_Unassigned ) - 1;
					$this->_Request[$key] = $match;
				}
			}
		} 
	}
	
	public function Set ( $pVariable, $pValue ) {
		
		$variable = strtolower ( rtrim ( ltrim ( $pVariable ) ) );
		
		$this->_Request[$variable] = $pValue;
		
		return ( true );
	}
		
	
}
