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

/** Database Class
 * 
 * Base class for Database connections
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cDatabase extends cBase {
	
	protected $_DB;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {
		
		// Check: extension_loaded ( "pdo_mysql");
		
		$this->_DB = $this->_Connect();
		
	}
	
	private function _Connect ( ) {
		$Config = $this->GetSys ( "Config" );
		
		$un = $Config->GetConfiguration ('un');
		$pw = $Config->GetConfiguration ('pw');
		$host = $Config->GetConfiguration ('host');
		$db = $Config->GetConfiguration ('db');
		$type = $Config->GetConfiguration ('type');
		
		$mode = $Config->GetConfiguration ('mode');
		
		try {  
			$DBH = new PDO("mysql:host=$host;dbname=$db", $un, $pw);  
		}  
		catch(PDOException $e) {  
    		die ( $e->getMessage() );
		}
	}
	
	public function Query ( $pQuery ) {
		
	}
	
	public function GetFieldInformation ( $pTablename ) {
		
		$prefix = $this->GetSys ( "Config" )->GetConfiguration ( "pre" );
		
		$table = $prefix . $pTablename;
		
		$fieldinfo = $this->_DB->get ( "DESC $table" );
		
		return ( $fieldinfo );
	}
	
}
