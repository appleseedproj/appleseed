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

require ( ASD_PATH . DS . 'system' . DS . 'router.php' );

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
		
		require ( ASD_PATH . DS . 'system' . DS . 'benchmark.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'database.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'client.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'configuration.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'theme.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'foundation.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'component.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'components.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'controller.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'language.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'event.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'hook.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'hooks.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'model.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'buffer.php' );
		require ( ASD_PATH . DS . 'system' . DS . 'logs.php' );
		
		$this->Logs = new cLogs();
		
		set_error_handler ( array ( $this->Logs, "HandleError" ) );
		
		$this->_LoadLibraries ();
            
		$this->Config = new cConf ();
		
		// Load site configuration.
		$this->Config->Set ( "Data",  $this->Config->Load ( "configurations" ) );

		$this->Database = new cDatabase();
		
		$this->Theme = new cTheme ();
		$this->Client = new cClient ();
		$this->Benchmark = new cBenchmark();
		$this->Buffer = new cBuffer();
		
		$this->Components = new cComponents();
		$this->Foundation = new cFoundation();
		$this->Event = new cEvent();
		$this->Hooks = new cHooks();
		
		$this->Event->Hooks = $this->Hooks;
		
		$this->Event->Trigger ( "Begin", "System", "Initialize" );
		
		$this->Language = new cLanguage();
		
		// Load global strings into cache.
		$this->Language->Load ('_system/global.lang');
        
		$this->Event->Trigger ( "End", "System", "Initialize" );
		
		$this->Router = new cRouter();
		
		return ( true );
	} 
	
	/**
	 * Load the system libraries.
	 *
	 * @access  private
	 */
	private function _LoadLibraries ( ) {
		
		// Dynamically loaded library classes.	
		$this->AddSys ( "Session",  ASD_PATH . DS . 'libraries' . DS . 'session.php' );
		$this->AddSys ( "Communication",  ASD_PATH . DS . 'libraries' . DS . 'communication.php' );
		$this->AddSys ( "Image",  ASD_PATH . DS . 'libraries' . DS . 'image.php' );
		$this->AddSys ( "Storage",  ASD_PATH . DS . 'libraries' . DS . 'storage.php' );
		$this->AddSys ( "Validation",  ASD_PATH . DS . 'libraries' . DS . 'validation.php' );
		$this->AddSys ( "Request",  ASD_PATH . DS . 'libraries' . DS . 'request.php' );
		$this->AddSys ( "HTML",  ASD_PATH . DS . 'libraries' . DS . 'markup.php' );
		$this->AddSys ( "Textup",  ASD_PATH . DS . 'libraries' . DS . 'markup.php' );
		$this->AddSys ( "Purifier",  ASD_PATH . DS . 'libraries' . DS . 'purifier.php' );
		$this->AddSys ( "Mailer",  ASD_PATH . DS . 'libraries' . DS . 'mailer.php' );
		$this->AddSys ( "Crypt",  ASD_PATH . DS . 'libraries' . DS . 'crypt.php' );
		$this->AddSys ( "Date",  ASD_PATH . DS . 'libraries' . DS . 'date.php' );
		$this->AddSys ( "Render",  ASD_PATH . DS . 'libraries' . DS . 'render.php' );
		
		return ( true );
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
				$url = 'http://' . ASD_DOMAIN;
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
	public function GetCache ( $pContext, $pKey = null ) {
		
		if (!$pKey) return ( $this->_cache[$pContext] );
		
		if ( isset ( $this->_cache[$pContext][$pKey] ) ) return ( $this->_cache[$pContext][$pKey] );
		
		return ( false );
	}
	
	/**
	 * Store Cache values
	 *
	 * @access  public
	 * @param string pContext  Which cache to use
	 */
	public function StoreCache ( $pContext ) {
		
		if ( isset ( $this->_cache[$pContext] ) ) return ( $this->_cache[$pContext] );
		
		return ( false );
	}
	
	/**
	 * Restore Cache values
	 *
	 * @access  public
	 * @param string pContext  Which cache to use
	 */
	public function RestoreCache ( $pContext, $pCache ) {
		
		$this->_cache[$pContext] = $pCache;
		
		return ( true );
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
			'client', 
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

/**
 * Returns the current time in a MYSQL format
 */
function NOW() {
	
	$now = date ("Y-m-d H:i:s", time ( ) );
	
	return ( $now );
}

/*
 * Adapted from: http://php.net/manual/en/function.rmdir.php
 * 
 */
function rrmdir($dir) { 
	if ( is_dir ( $dir ) ) { 
		$objects = scandir( $dir ); 
		foreach ( $objects as $object ) { 
			if ( $object != "." && $object != ".." ) { 
				if ( filetype ( $dir . "/" . $object ) == "dir" )  {
					if ( !rrmdir ( $dir . "/" . $object ) ) return ( false );
				} else {
					if ( !unlink ( $dir."/".$object ) ) return ( false );
				}
			} 
		} 
		reset ( $objects ); 
		if ( !rmdir ( $dir ) ) return ( false );
	} 
	
	return ( true );
} 

/*
 * Adapted from: http://php.net/manual/en/function.rmkdir.php
 * 
 */
function rmkdir ( $path, $mode = 0777 ) {
	$path = rtrim ( preg_replace ( array ( "/\\\\/", "/\/{2,}/" ), "/", $path ), "/" );
	$e = explode ( "/", ltrim ( $path, "/" ) );
	if ( substr ( $path, 0, 1 ) == "/" ) {
		$e[0] = "/".$e[0];
	}
	$c = count ( $e );
	$cp = $e[0];
	for ( $i = 1; $i < $c; $i++ ) {
		if ( !is_dir ( $cp ) && !@mkdir ( $cp, $mode ) ) {
			return false;
		}
		$cp .= "/".$e[$i];
	}
	return @mkdir ( $path, $mode );
}

/*
 * Factory class
 *
 */
class Wob {

	/*
	 * System Object Retrieval
     */
	public static function _ ( $pObject ) {
		global $zApp;

		return ( $zApp->GetSys ( $pObject) );
	}

}
