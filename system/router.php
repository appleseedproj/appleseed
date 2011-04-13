<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Router Class
 * 
 * Routes the application to the appropriate foundation
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cRouter extends cBase {
	
	protected $_Route;
	protected $_Foundation;
	protected $_Request;
	protected $_Base;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {
		parent::__construct();
	}

	/**
	 * Routing entry point
	 *
	 * @access  public
	 */
	public function Route ( ) {
		eval ( GLOBALS );
			
		$this->GetSys ( "Event" )->Trigger ( "Begin", "System", "Route" );
		
		$Foundation = $this->GetSys ( "Foundation" );
		$FoundationConfig = $Foundation->Get ( "Config" );
		
		$routes = $FoundationConfig->GetConfiguration ( "routes" );
		
		$request = strtolower ( ltrim ( rtrim ( $_SERVER['REQUEST_URI'], '/' ), '/' ) );

		// If GET data is in the request, then remove it.
		if ( strstr ( $request, '?' ) ) {
			list ( $request, $null ) = explode ( '?', $request );
		}
		
		list ( $admin, $null ) = explode ( '/', $request, 2);
		
		if ( $admin == "admin" ) {
			// Load admin strings into cache.
			$this->GetSys ( "Language" )->Load ('_system/admin.lang');
		}
		
		foreach ( $routes as $r => $route ) {
			$r = strtolower ( ltrim ( rtrim ( $r, '/' ), '/' ) );

			if ( (!$route) and (!$r) ) continue;
			
			$pattern = '/^' . addcslashes ($r, '/') . '$/';

			if ( preg_match ( $pattern, $request, $routed ) ) {

				$restrictions = $FoundationConfig->GetConfiguration ( "restrictions" );
				
				if ( !$this->_CheckRestrictions ( $restrictions ) ) return ( false );
				
				// See if we're matching variables in the url and store them in cRequest 
				if ( preg_match ( '/\?/', $route ) ) {
					list ( $finalDestination, $variables ) = explode ( '?', $route, 2);
					$pairs = explode ( '&', $variables );
					
					preg_match ( $pattern, $request, $matches );
					
					foreach ( $pairs as $p => $pair ) {
						list ( $key, $value) = explode ( '=', $pair, 2 );
						
						$value_pattern = '/\$' . ($p+1) . '/';
						$value = preg_replace ( $value_pattern, $matches[$p+1], $value );
						
						// If the key isn't being set by a form, then pull from the routed variables.
						if ( !$zApp->GetSys ( 'Request' )->Get ( $key ) )
							$zApp->GetSys ( "Request" )->Set ( $key, $value );
					}
					
				} else {
					$finalDestination = $route;
				}
				
				// Get information about the route, pattern, and request and store it
				unset ( $routed[0] );
				
				$routedVars = implode ( '\/', $routed );
				
				$base = '/' . $request;
				foreach ( $routed as $routedVar ) {
					$routedVarPattern = '/' . $routedVar . '$/';
					$base = preg_replace ( $routedVarPattern, '', $base );
				}
				
				// Put leading and trailing slashes on the base url
				$baseFirstChar = $base[0];
				$baseLastChar = $base[strlen($base)-1];
				
				if ( $baseFirstChar != '/' ) $base = '/' . $base;
				if ( $baseLastChar != '/' ) $base = $base . '/';
				
				// Put leading and trailing slashes on the request url
				$requestFirstChar = $request[0];
				$requestLastChar = $request[strlen($request)-1];
				
				if ( $requestFirstChar != '/' ) $request = '/' . $request;
				if ( $requestLastChar != '/' ) $request = $request . '/';
				
				// Route is the regular expression used to route, defined in foundation configuration
				$this->_Route = $pattern;
				
				// Request is the requested uri
				$this->_Request = $request;
				
				// Base is the requested uri without the pattern matched variables.
				$this->_Base = $base;
				
				$data = array ( "foundation" => $finalDestination );
				$modified = $this->GetSys ( "Event" )->Trigger ( "On", "System", "Route", $data );
				if ( $modified ) $finalDestination = $modified;
				
				$this->_Foundation = $finalDestination;
		
				$Foundation->Load ( $finalDestination );
				
				$this->GetSys ( "Event" )->Trigger ( "End", "System", "Route" );
		
				return ( true );
			}
		}

		$Foundation->Load ( 'common/404.php' );
				
		$this->GetSys ( "Event" )->Trigger ( "End", "System", "Route" );
		
		return ( true );
	}
	
	private function _CheckRestrictions ( $pRestrictions ) {
		
		$request = ltrim ( rtrim ( $_SERVER['REQUEST_URI'], '/' ), '/' );
		
		foreach ( $pRestrictions as $r => $restriction ) {
			$r = ltrim ( rtrim ( $r, '/' ), '/' );
			$pattern = '/^' . addcslashes ($r, '/') . '$/';
			if ( preg_match ( $pattern, $request ) ) {
				$data = array ( "restriction" => $restriction );
				$return = $this->GetSys ( "Event" )->Trigger ( "On", "System", "Restricted", $data );
				return ( $return );
			}
		}
		
		return ( true );
	}
	
	public function Redirect ( $pLocation ) {
		header ( 'Location:' . $pLocation );
		exit;
	}

}
