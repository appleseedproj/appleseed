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
		
		$Foundation = $this->GetSys ( "Foundation" );
		$FoundationConfig = $Foundation->Get ( "Config" );
		
		$routes = $FoundationConfig->GetConfiguration ( "routes" );
		$request = ltrim ( rtrim ( $_SERVER['REQUEST_URI'], '/' ), '/' );
			
		foreach ( $routes as $r => $route ) {
			$r = ltrim ( rtrim ( $r, '/' ), '/' );
			
			$pattern = '/^' . addcslashes ($r, '/') . '$/';
			
			if ( preg_match ( $pattern, $request ) ) {
				$Foundation->Load ( $route );
				return ( true );
			}
		}
		
		$this->Legacy ( );
       	
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
					require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'common' . DS . 'images.php' );
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
				  require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'admin' . DS . $routes[0] . DS . $routes[1] . '.php' );
				} else {
				  require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'error' . DS . '404.php' );
				}
				
				exit;
			break;
			case 'profile':
				array_shift ( $routes );
				
				global $gPROFILEREQUEST;
				$gPROFILEREQUEST = join ('/', $routes);
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'user' . DS . 'main.php' );
				exit;
			case 'icon':
				array_shift ( $routes );
				
				global $gICONUSER;
				$gICONUSER = $routes[0];
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'common' . DS . 'icon.php' );
			break;	
			case 'news':
			case 'articles':
				array_shift ( $routes );
				
				global $gARTICLEREQUEST;
				$gARTICLEREQUEST = $routes[0];
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'content' . DS . 'articles.php' );
			break;	
			case 'group':
				array_shift ( $routes );
				
				global $gGROUPREQUEST;
				$gGROUPREQUEST = $routes[0];
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'content' . DS . 'group.php' );
			break;	
			case 'groups':
				array_shift ( $routes );
		
				global $gGROUPSECTION;
				$gGROUPSECTION = $routes[0];
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'content' . DS . 'groups.php' );
			break;	
			case 'join':
				array_shift ( $routes );
		
				global $gVALUE;
				$gVALUE = $routes[0];
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'join.php' );
			break;	
			case 'login':
			    if ( $routes[1] == 'bounce' ) {
					require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'bounce.php' );
					exit;
			    } else {
					array_shift ( $routes );
			
					global $gLOGINREQUEST;
					$gLOGINREQUEST = join ('/', $routes);
					require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'login.php' );
					exit;
			    }
			break;
			case 'asd':
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'asd.php' );
				exit;
			break;
			case 'ajax':
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'ajax.php' );
				exit;
			break;
			case 'maintenance':
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'maintenance.php' );
				exit;
			break;
			case 'logout':
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'logout.php' );
				exit;
			break;
			case 'index.php':
			case '':
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'main.php' );
				exit;
			break;
			default:
				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'redirect.php' );
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
		SETGLOBAL("zARTICLES"); SETGLOBAL("zCONTENTPAGE"); SETGLOBAL("zJANITOR"); SETGLOBAL("zSERVER");
		SETGLOBAL("gFOCUSUSERID"); SETGLOBAL("gLOGINREQUEST"); SETGLOBAL("gACTION"); SETGLOBAL("gCOMMENTACTION"); SETGLOBAL("gJOINLOCATION"); SETGLOBAL("gFRAMELOCATION"); 
		SETGLOBAL("gTHEMELOCATION"); SETGLOBAL("gPROFILEACTION"); SETGLOBAL("gPROFILESUBACTION"); SETGLOBAL("gICONUSER"); SETGLOBAL("gACTION"); 
		SETGLOBAL("gPOSTDATA"); SETGLOBAL("gEXTRAPOSTDATA"); SETGLOBAL ("guID"); SETGLOBAL("gSCROLLMAX"); SETGLOBAL("gVIEWDATA");
		SETGLOBAL("gERRORMSG"); SETGLOBAL("gERRORTITLE"); SETGLOBAL("gSETTINGS"); SETGLOBAL("gCONFIRM"); SETGLOBAL("gFOOTNOTE");
		SETGLOBAL("gEXTRAPOSTDATA"); SETGLOBAL("gSITEURL"); SETGLOBAL("gREMEMBER"); SETGLOBAL("gRECIPIENT");
		SETGLOBAL("bREFRESHLINE"); SETGLOBAL("bMAINSECTION"); SETGLOBAL("bLOGINBOX"); SETGLOBAL("bJOINBOX"); SETGLOBAL("bINVITEBOX");
		SETGLOBAL("target"); SETGLOBAL("mainlocation"); SETGLOBAL("username");
		
		return ( true );

	}

}
