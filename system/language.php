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
class cLanguage extends cBase {
	
	protected $_Config;
	
	protected $_Stores;
	
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
 	function Load ( $pContextFile, $pLanguage = "en-US" ) {
 		eval(GLOBALS);
 		
 		// Get the list of language packs installed.
		$paths = $zApp->Language->_Config->GetPath ();
		
		// Find out which type of language file we're loading ( "_system", "hooks", "components" )
		list ( $type, $filename ) = explode ( '/', $pContextFile );
		
		// Skip if we're looking for the internal system language files.
		switch ( $type ) {
			case '_system':
			break;
			
			case 'hooks':
				list ( $component, $hook, $extension ) = explode ( '.', $filename );
				$filename = join ( '.', array ( $hook, $extension ) );
 				$locations[] = $zApp->GetPath() . DS . $type . DS . $component . DS . $hook . DS . 'languages' . DS . $pLanguage . DS . $filename;
			break;
			case 'components':
				// Find out which component or hook we're loading.
				list ( $component, $extension ) = explode ( '.', $filename );
		
 				$locations[] = $zApp->GetPath() . DS . $type . DS . $component . DS . 'languages' . DS . $pLanguage . DS . $filename;
			break;
		}
		
 		foreach ( $paths as $p => $path ) {
 			switch ( $type ) {
 				case 'hooks':
 					$locations[] = $zApp->GetPath() . DS . 'languages' . DS . $path . DS . $pLanguage . DS . 'hooks' . DS . $component . DS . $hook . '.lang';
 				break;
 				case '_system':
 				case 'components':
 					$locations[] = $zApp->GetPath() . DS . 'languages' . DS . $path . DS . $pLanguage . DS . $pContextFile;
 				break;
 			}
 		}
 		
 		$store = $type . '.' . $filename;
 		
 		/*
 		 * Store the current language set for restoration once we're done.
 		 * 
 		 * This can get a little complicated.  But this way, if you have two hooks or components right next to each other 
 		 * that use the same phrase (ie, "Upload A Photo"), the language file for one hook or component doesn't stay in 
 		 * memory for when the next hook/component loads.
 		 * 
 		 * If you load a hook or component within a hook or component, however, the child will inherit the parent's language.
 		 * 
 		 */
 		$this->_Stores[$store] = $zApp->StoreCache ( 'Language' );
 		
 		foreach ( $locations as $l => $location ) {
 			
 			// File does not exist, return false. 
 			// _set _system _error
 			if ( !file_exists ( $location ) ) continue;
 			
 			// File can not be parsed, continue through.
 			// _set _system _error
 			if ( !$data = parse_ini_file ( $location ) ) {
 				continue;
 			} 
 			
 			$eventData['store'] = $store;
 			$eventData['data'] = $data;
 			$this->GetSys ( "Event" )->Trigger ( "On", "Load", "Language", $eventData );
 		
 			// Put data into the global cache.
 			foreach ( $data as $key => $value ) {
 	        	$zApp->setCache ( 'Language', $key, $value );	
 			}
 		
 		} 
 		
 		return ( $store );
 	}
 	
 	/**
 	 * @access public
 	 * @param string The untranslated string
 	 * @param array list of variables to sprintf
 	 * @return string
 	 */
 	static function _ ( $pString, $pParams = null ) {
 		eval(GLOBALS);
 		
 		$debug = $zApp->Config->GetConfiguration ( "debug" );

        $key = str_replace ( ' ', '_', $pString );
        $key = strtoupper ( $key );
        
        if ( !$key ) return ( $pString );
 		
        $value = $zApp->GetCache ( 'Language', $key );
        
        if ( $value ) {
        	$return = $value;
        } else {
 		    $return = $pString;
 		    
 		    if ( $debug == "true" ) {
 		    	$return = '<span class="untranslated">' . $return . '</span>';
 		    	if ( $pParams ) {
 		    		$parameters = join ( " | ", array_keys ( $pParams ) );
 		    		$return .= '<span class="untranslatedp">' . $parameters . '</span>';
 		    	}
 		    }
 		    
        }
        
        if ( count ( $pParams ) >= 1 ) {
        	$return = sprintfn ( $return, $pParams );
        	if ( !$return ) {
        		$return = $pString;
        		if ( $debug == "true" ) {
 		    		$return = '<span class="mistranslated">' . $return . '</span>';
 		    		if ( $pParams ) {
 		    			$parameters = join ( " | ", array_keys ( $pParams ) );
 		    			$return .= '<span class="mistranslatedp">' . $parameters . '</span>';
 		    		}
        		}
        	}
        }
        
        return ( $return );
        
 	}
 	
 	function Restore ( $pStore ) {
 		eval ( GLOBALS );
 		
 		$store = $pStore;
 		
 		if ( isset ( $this->_Stores[$store] ) ) {
 			
 			$zApp->RestoreCache ( 'Language', $this->_Stores[$store] );
 			unset ( $this->_Stores[$store] );
 		}
 		
 		return ( true );
 	}
 	
}

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
