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
	
	private $_cache;
	
	private $_path;
	
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
		eval ( GLOBALS );
		
		$this->_CheckDependencies ( );
		
		$this->_DisableMagicQuotes ( );
		
		require_once ( ASD_PATH . DS . 'system' . DS . 'database.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'configuration.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'theme.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'foundation.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'component.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'components.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'controller.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'event.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'hook.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'hooks.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'model.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'buffer.php' );
		require_once ( ASD_PATH . DS . 'system' . DS . 'logs.php' );
		
		$this->_LoadLibraries ();
            
		$this->Config = new cConf ();
		
		// Load site configuration.
		$this->Config->Set ( "Data",  $this->Config->Load ( "configurations" ) );

		$this->Database = new cDatabase();
		
		$this->Language = new cLanguage();
		$this->Theme = new cTheme ();
		$this->Logs = new cLogs();
		$this->Buffer = new cBuffer();
		
		$this->Components = new cComponents();
		$this->Foundation = new cFoundation();
		$this->Event = new cEvent();
		$this->Hooks = new cHooks();
		
		$this->Request = new cRequest();
		$this->HTML = new cHTML();
		
		$this->Event->Hooks = $this->Hooks;
		
		// Load global strings into cache.
		$this->Language->Load ('en-US', 'system.global.lang');
        
		$this->Router = new cRouter();
		
		return ( true );
	} 
	
	/**
	 * Load the system libraries.
	 *
	 * @access  private
	 */
	private function _LoadLibraries ( ) {
		require_once ( ASD_PATH . DS . 'libraries' . DS . 'language.php' );
		require_once ( ASD_PATH . DS . 'libraries' . DS . 'request.php' );
		require_once ( ASD_PATH . DS . 'libraries' . DS . 'markup.php' );
	}
	
	/**
	 * Get The Appleseed Installation Path 
	 *
	 * @access  public
	 */
	public function GetPath ( ) {
		
		if (!isset ($this->_path)) {
			$this->_path = $_SERVER['DOCUMENT_ROOT'];
		}
		
		return ($this->_path);
	}
	
	/**
	 * Get The Base Url 
	 *
	 * @access  public
	 */
	public function GetBaseURL ( ) {
		
		if (!isset ($this->_baseurl)) {
			$url = $this->Config->GetConfiguration ( "url" );
			
			if ( !isset ( $url ) ) {
				$url = 'http://' . $_SERVER['SERVER_NAME'];
			}
			
			return ( $url );
		}
		
		return ($this->_baseurl);
	}
	
	/**
	 * Set a Cache value
	 *
	 * @access  public
	 * @param string pContext  Which cache to use
	 * @param string pKey
	 * @param string pValue
	 */
	public function SetCache ( $pContext, $pKey, $pValue ) {
		
		$this->_cache[$pContext][$pKey] = $pValue;
		
		return ( true );
	}
	
	/**
	 * Get a Cache value
	 *
	 * @access  public
	 * @param string pContext  Which cache to use
	 * @param string pKey
	 */
	public function GetCache ( $pContext, $pKey ) {
		
		if ( isset ( $this->_cache[$pContext][$pKey] ) ) return ( $this->_cache[$pContext][$pKey] );
		
		return ( false );
	}
	
	/**
	 * Return a list of reserved system names
	 *
	 * @access  public
	 */
	public function Reserved ( ) {
		$reserved = array ( 
			'router', 
			'foundation', 
			'config', 
			'language', 
			'theme', 
			'buffer', 
		);
		
		return ( $reserved );
	}
	
	/**
	 * Check system dependencies and error on failure.
	 *
	 * @access  private
	 */
	private function _CheckDependencies ( ) {
		
		// @todo sha256
		// @todo PDO
		// @todo Installed in site root directory? (see documentation)
		// @todo Is register_globals turned off?
		// @todo Is the _storage/ directory writable?
		// @todo PHP version 5.0 or higher? (Running 5.3.0RC2)
		// @todo Mysql version 5.0 or higher? (Client is 5.1.32)
		
		return ( true );
	}
	
	/**
	 * Manually strips slashes from magically quoted variables
	 *
	 * @access  private
	 */
	private function _DisableMagicQuotes ( ) {
		
		if ( get_magic_quotes_gpc() ) {
		
			$quoted = array( &$_GET, &$_POST, &$_COOKIE );
			
			foreach ( $quoted as $q => $quote) {
				foreach ( $quote as $key => $value ) {
					if ( !is_array ( $value ) ) {
						$quoted[$q][$key] = stripslashes ( $value );
					} else {
						$quoted[] =& $quoted[$q][$key];
					}
				}
			}
			
			unset($quoted);
		}
		
		return ( true );
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
function scandirs ( $pPath ) {
	$results = scandir ( $pPath );

	foreach ($results as $result) {
		// Skip all hidden files
		if ( $result[0] === '.' ) continue;

		if ( is_dir ( $pPath . '/' . $result ) ) {
			$dirs[] = $result;
		}
	}
	
	return ( $dirs );
}
