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

/** Router Class
 * 
 * Routes the application to the appropriate foundation
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cRouter {

        /**
         * Constructor
         *
         * @access  public
         */
        public function __construct ( ) {
        }
        
        /**
         * Routing entry point
         *
         * @access  public
         */
        public function Route ( ) {
			$routes = split ( '/', $_SERVER['REQUEST_URI'] );
			array_shift ( $routes );	
        	cRouter::Legacy ( $routes );
        	
        	return ( true );
        }

        /**
         * Legacy Routing
         *
         * @access  public
         * @todo    Move all legacy functionality to the new MVC framework.
         */
        public function Legacy ( $pRoutes = array() ) {
        	
        	// Set proper global variables
        	cRouter::LegacyPrepGlobals ( );
        	
        	$target = $pRoutes[0];
        	
        	
        	switch ( $target ) {
        		case '_admin':
        			array_shift ( $pRoutes );
        			
        			if (!$pRoutes[1]) { $pRoutes[1] = 'main'; }
        			
        			require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'admin' . DS . $pRoutes[0] . DS . $pRoutes[1] . '.php' );
        			exit;
        		break;
        		case 'profile':
        			array_shift ( $pRoutes );
        			
        			global $gPROFILEREQUEST;
        			$gPROFILEREQUEST = join ('/', $pRoutes);
        			require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'user' . DS . 'main.php' );
        			exit;
        		case 'icon':
        			array_shift ( $pRoutes );
        			
        			global $gICONUSER;
        			$gICONUSER = $pRoutes[0];
        			require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'common' . DS . 'icon.php' );
        		break;	
        		case 'news':
        		case 'articles':
        			array_shift ( $pRoutes );
        			
        			global $gARTICLEREQUEST;
        			$gARTICLEREQUEST = $pRoutes[0];
        			require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'content' . DS . 'articles.php' );
        		break;	
        		case 'group':
        			array_shift ( $pRoutes );
        			
        			global $gGROUPREQUEST;
        			$gGROUPREQUEST = $pRoutes[0];
        			require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'content' . DS . 'group.php' );
        		break;	
        		case 'groups':
        			array_shift ( $pRoutes );
        			
        			global $gGROUPSECTION;
        			$gGROUPSECTION = $pRoutes[0];
        			require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'content' . DS . 'groups.php' );
        		break;	
        		case 'join':
        			array_shift ( $pRoutes );
        			
        			global $gVALUE;
        			$gVALUE = $pRoutes[0];
        			require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'join.php' );
        		break;	
        		case 'login':
        		    if ( $pRoutes[1] == 'bounce' ) {
        				require_once ( ASD_PATH . 'legacy' . DS . 'code' . DS . 'site' . DS . 'bounce.php' );
        				exit;
        		    } else {
        				array_shift ( $pRoutes );
        				
        				global $gLOGINREQUEST;
        				$gLOGINREQUEST = join ('/', $pRoutes);
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
 
			function _G($pVar) { global $_G; $_G[]="$pVar"; }
			define ("_G", 'global $_G; foreach ($_G as $g => $glob) { global $$glob; }');

			_G("ADMINDATA");
			_G("zOLDAPPLE"); _G("zHTML"); _G("zAUTHUSER"); _G("zFOCUSUSER"); _G("zLOCALUSER"); _G("zREMOTEUSER"); _G("zIMAGE");
			_G("zARTICLES"); _G("zCONTENTPAGE"); _G("zJANITOR");
			_G("gFOCUSUSERID"); _G("gLOGINREQUEST"); _G("gACTION"); _G("gCOMMENTACTION"); _G("gJOINLOCATION"); _G("gFRAMELOCATION"); 
			_G("gTHEMELOCATION"); _G("gPROFILEACTION"); _G("gPROFILESUBACTION"); _G("gICONUSER"); _G("gACTION"); _G("gPOSTDATA");
			_G("gEXTRAPOSTDATA"); _G("gSITEURL"); _G("gREMEMBER");
			_G("bREFRESHLINE"); _G("bMAINSECTION"); _G("bLOGINBOX"); _G("bJOINBOX");
			_G("target"); _G("mainlocation"); _G("username");
			
			return ( true );

        }

}
