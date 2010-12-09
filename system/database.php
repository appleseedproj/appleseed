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
			$this->_DB = new AppleseedPDO("mysql:host=$host;dbname=$db;charset=UTF-8", $un, $pw, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
			mb_internal_encoding( 'UTF-8' );
		}  
		catch(PDOException $e) {  
    		die ( $e->getMessage() );
		}
		
		return ( $this->_DB );
	}
	
	public function Query ( $pQuery ) {
		
	}
	
	public function GetFieldInformation ( $pTablename ) {
		eval ( GLOBALS );
		
		$prefix = $this->GetSys ( "Config" )->GetConfiguration ( "pre" );
		
		$table = $prefix . $pTablename;
		
		if ( !$result = $this->_DB->query ( "DESC $table" ) ) {
			
			$warning = __( "Table Does Not Exist", array ( "name" => $table ) );
			$zApp->GetSys ( "Logs" )->Add ( $warning, "Warnings" );
			
			return ( false );
		}
		
		$fieldinfo = $result->fetchAll ( PDO::FETCH_ASSOC );
		
		return ( $fieldinfo );
	}
	
}

/** Extended PDO Class
 * 
 * Extends the PDO object for Appleseed.
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class AppleseedPDO extends PDO {
	protected $_table_prefix;
	protected $_table_suffix;

	public function __construct($dsn, $user = null, $password = null, $driver_options = array(), $prefix = null, $suffix = null)
	{
		$this->_table_prefix = $prefix;
		$this_table_suffix   = $suffix;
		parent::__construct($dsn, $user, $password, $driver_options);
	}

	public function exec($statement)
	{
		$statement = $this->_tablePrefixSuffix($statement);
		return parent::exec($statement);
	}

	public function prepare($statement, $driver_options = array())
	{
		$statement = $this->_tablePrefixSuffix($statement);
		return parent::prepare($statement, $driver_options);
	}

	public function query($statement)
	{
		$statement = $this->_tablePrefixSuffix($statement);
		$args  = func_get_args();

		if (count($args) > 1) {
			return call_user_func_array(array($this, 'parent::query'), $args);
		} else {
			return parent::query($statement);
		}
	}

	protected function _tablePrefixSuffix($statement)
	{
		return sprintf($statement, $this->_table_prefix, $this->_table_suffix);
	}
}