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

/** Logs Class
 * 
 * Base class for Logging
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cLogs extends cBase {
	
	private $_Log;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		date_default_timezone_set ( 'UTC' );
	}
	
	public function GetLogs ( $pContext ) {
		
		return ( $this->_Log[$pContext] );
	}
	
	public function HandleError ( $pErrorNumber, $pErrorString, $pErrorFile, $pErrorLine ) {
		
		$context = $pErrorFile . ":" . $pErrorLine;
		$this->Add ( "Warnings", $pErrorString, $context );
		
		return ( true );
	}
	
	
	public function Add ( $pContext, $pValue, $pComponentContext ) {
		
		$current = count ( $this->_Log[$pContext] );
		
		$this->_Log[$pContext][$current] = new StdClass();
		
		$this->_Log[$pContext][$current]->Value = $pValue;
		$this->_Log[$pContext][$current]->Context = $pComponentContext;
		$this->_Log[$pContext][$current]->Stamp = time();
		$this->_Log[$pContext][$current]->Timezone = date("T");
		
		return ( $this->_Log[$pContext][$current] );
	}

}
