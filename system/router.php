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
		
		$request = ltrim ( rtrim ( $_SERVER['REQUEST_URI'], '/' ), '/' );
		
		list ( $admin, $null ) = explode ( '/', $request, 2);
		
		if ( $admin == "admin" ) {
			// Load admin strings into cache.
			$this->GetSys ( "Language" )->Load ('_system/admin.lang');
		}
		
		foreach ( $routes as $r => $route ) {
			$r = ltrim ( rtrim ( $r, '/' ), '/' );
			
			$pattern = '/^' . addcslashes ($r, '/') . '$/';

			if ( preg_match ( $pattern, $request, $routed ) ) {
				
				$restrictions = $FoundationConfig->GetConfiguration ( "restrictions" );
				
				if ( !$this->_CheckRestrictions ( $restrictions ) ) return ( false );
				
				// See if we're matching variables in the url and store them in cRequest 
				if ( preg_match ( '/\?/', $route ) ) {
					list ( $finalDestination, $variables ) = explode ( '?', $route, 2);
					$pairs = split ( '&', $variables );
					
					preg_match ( $pattern, $request, $matches );
					
					foreach ( $pairs as $p => $pair ) {
						list ( $key, $value) = explode ( '=', $pair, 2 );
						
						$value_pattern = '/\$' . ($p+1) . '/';
						$value = preg_replace ( $value_pattern, $matches[$p+1], $value );
						
						$zApp->GetSys ( "Request" )->Set ( $key, $value );
					}
					
				} else {
					$finalDestination = $route;
				}
				
				// Get information about the route, pattern, and request and store it
				unset ( $routed[0] );
				
				$routedVars = implode ( '\/', $routed );
				
				$routedVarsPattern = '/' . $routedVars . '/';
				$base = '/' . preg_replace ( $routedVarsPattern, '', $request );
				
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
		
				$Foundation->Load ( $finalDestination );
				
				return ( true );
			}
		}
		
		$this->Legacy ( );
       	
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

	/**
	 * Legacy Routing
	 *
	 * @access  public
	 * @todo    Move all legacy functionality to the new MVC framework.
	 */
	public function Legacy ( ) {
		eval (GLOBALS);
	
		$routes = explode ( '/', $_SERVER['REQUEST_URI'] );
		
		// Set proper global variables
		cRouter::LegacyPrepGlobals ( );
	
		array_shift ( $routes );
		$target = $routes[0];
		$extension_arr = explode ('.', $routes[count($routes)-1]); 
		$extension = $extension_arr[count($extension_arr)-1];
		
		switch ( $extension ) {
			case 'jpg':
			case 'png':
			case 'gif':
				$location = ASD_PATH . implode ( DS, $routes );
				if ( file_exists ( $location ) ) {
					require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'common' . DS . 'images.php' );
					exit;
				}
			break;
		}
		
		switch ( $target ) {
			case '_admin':
				array_shift ( $routes );
			
				if (!$routes[1]) { $routes[1] = 'main'; }
				
				$path = ASD_PATH . 'legacy' . DS . 'code' . DS . 'admin' . DS . $routes[0] . DS . $routes[1] . '.php' ;
				
				if (is_file ($path) ) {
				  require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'admin' . DS . $routes[0] . DS . $routes[1] . '.php' );
				} else {
				  require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'error' . DS . '404.php' );
				}
				
				exit;
			break;
			case 'profile':
				array_shift ( $routes );
				
				global $gPROFILEREQUEST;
				$gPROFILEREQUEST = join ('/', $routes);
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'user' . DS . 'main.php' );
				exit;
			case 'icon':
				array_shift ( $routes );
				
				global $gICONUSER;
				$gICONUSER = $routes[0];
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'common' . DS . 'icon.php' );
			break;	
			case 'news':
			case 'articles':
				array_shift ( $routes );
				
				global $gARTICLEREQUEST;
				$gARTICLEREQUEST = $routes[0];
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'content' . DS . 'articles.php' );
			break;	
			case 'group':
				array_shift ( $routes );
				
				global $gGROUPREQUEST;
				$gGROUPREQUEST = $routes[0];
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'content' . DS . 'group.php' );
			break;	
			case 'groups':
				array_shift ( $routes );
		
				global $gGROUPSECTION;
				$gGROUPSECTION = $routes[0];
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'content' . DS . 'groups.php' );
			break;	
			case 'join':
				array_shift ( $routes );
		
				global $gVALUE;
				$gVALUE = $routes[0];
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'join.php' );
			break;	
			case 'login':
			    if ( $routes[1] == 'bounce' ) {
					require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'bounce.php' );
					exit;
			    } else {
					array_shift ( $routes );
			
					global $gLOGINREQUEST;
					$gLOGINREQUEST = join ('/', $routes);
					require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'login.php' );
					exit;
			    }
			break;
			case 'asd':
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'asd.php' );
				exit;
			break;
			case 'ajax':
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'ajax.php' );
				exit;
			break;
			case 'maintenance':
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'maintenance.php' );
				exit;
			break;
			case 'logout':
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'logout.php' );
				exit;
			break;
			case 'index.php':
			case '':
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'main.php' );
				exit;
			break;
			default:
				require ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'redirect.php' );
				exit;
			break;
		}

		return ( true );
	}

	/**
	 * Legacy Global Variable Preparation
	 *
	 * @access  public
	 */
	public function LegacyPrepGlobals ( ) {

		SETGLOBAL("ADMINDATA");
		SETGLOBAL("zOLDAPPLE"); SETGLOBAL("zHTML"); SETGLOBAL("zAUTHUSER"); SETGLOBAL("zFOCUSUSER"); SETGLOBAL("zLOCALUSER"); SETGLOBAL("zREMOTEUSER"); SETGLOBAL("zIMAGE");
		SETGLOBAL("zARTICLES"); SETGLOBAL("zCONTENTPAGE"); SETGLOBAL("zJANITOR"); SETGLOBAL("zSERVER"); SETGLOBAL("zXML"); SETGLOBAL("zUPDATE");
		SETGLOBAL("gFOCUSUSERID"); SETGLOBAL("gLOGINREQUEST"); SETGLOBAL("gACTION"); SETGLOBAL("gCOMMENTACTION"); SETGLOBAL("gJOINLOCATION"); SETGLOBAL("gFRAMELOCATION"); 
		SETGLOBAL("gTHEMELOCATION"); SETGLOBAL("gPROFILEACTION"); SETGLOBAL("gPROFILESUBACTION"); SETGLOBAL("gICONUSER"); SETGLOBAL("gACTION"); 
		SETGLOBAL("gPOSTDATA"); SETGLOBAL("gEXTRAPOSTDATA"); SETGLOBAL ("guID"); SETGLOBAL("gSCROLLMAX"); SETGLOBAL("gSCROLLSTEP"); SETGLOBAL("gVIEWDATA");
		SETGLOBAL("gERRORMSG"); SETGLOBAL("gERRORTITLE"); SETGLOBAL("gSETTINGS"); SETGLOBAL("gCONFIRM"); SETGLOBAL("gFOOTNOTE");
		SETGLOBAL("gEXTRAPOSTDATA"); SETGLOBAL("gSITEURL"); SETGLOBAL("gREMEMBER"); SETGLOBAL("gRECIPIENT"); SETGLOBAL("gLOCATION"); SETGLOBAL("gLOCATION");
		SETGLOBAL("gAPPLESEEDVERSION"); SETGLOBAL("gSITEDOMAIN"); SETGLOBAL("gMASSLIST");
		SETGLOBAL("bREFRESHLINE"); SETGLOBAL("bMAINSECTION"); SETGLOBAL("bLOGINBOX"); SETGLOBAL("bJOINBOX"); SETGLOBAL("bINVITEBOX");
		SETGLOBAL("target"); SETGLOBAL("mainlocation"); SETGLOBAL("username");
		
		return ( true );

	}

}
