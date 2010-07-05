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

/** Model Class
 * 
 * Base class for Models
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cModel extends cBase {
	
	protected $_Prefix;
	protected $_Tablename;
	protected $_Fields;
	
	protected $_PrimaryKey;
	protected $_ForeignKeys;
	
	protected $_Query;
	protected $_Total;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTable = null ) {
		
		$Config = $this->GetSys ( "Config" );
		$Database = $this->GetSys ( "Database" );
		
		$this->_Prefix = $Config->GetConfiguration ( "pre" );
		
		// Check if the tablename was specified.
		if ( $pTable ) {
			$tablename = ucwords ( strtolower ( ltrim ( rtrim ( $pTable ) ) ) );
		} else {
			$tablename = preg_replace ( '/^c/', "", get_class ( $this ) );
			$tablename = preg_replace ( '/Model$/', "", $tablename );
		}
		
		$this->_Tablename = $tablename;
		
		// @todo: replace with $this->Structure
		$fieldinfo = $Database->GetFieldInformation ( $this->_Tablename );
		
		foreach ( $fieldinfo as $f => $field ) {
			$fieldname = $field['Field'];
			$this->_Fields[$fieldname] = $field;
		}
		
		parent::__construct();
	}
	
	public function Structure ( $pTablename ) {
	}
	
	/**
	 * Save a record or set of records.
	 *
	 * @access  public
	 * @var string $pCriteria A single value (for primary key based storage) or an array of criteria.
	 */
	public function Save ( $pCriteria = null ) {
		
		$criteria = array (
			"Username" => "Search1",
			array (
				"||!=Email" => "Search2",
				"&&~~LastLogin" => "Search3"
			)
		);
		
		$ordering = "Username DESC";
		
		/*
			WHERE 
				Username = '%s' 
				OR ( Email NOT EQUAL '%s' AND LastLogin LIKE '%s' ) 
			GROUP BY Username DESC
		*/
		
		// ~~ LIKE
		// !~ NOT LIKE
		// != NOT EQUAL
		// =n IS NULL
		// !n NOT NULL
		// >> GREATER THAN
		// << LESS THAN
		
		if ( is_array ( $pCriteria ) ) 
			return ( $this->_SaveWhere ( $pCriteria ) );
		else 
			return ( $this->_Save ( $pCriteria ) );
	}
	
	/**
	 * Save a record based on complex criteria
	 *
	 * @access  protected
	 * @var string $pCriteria An array of criteria.
	 */
	protected function _SaveWhere ( $pCriteria ) {
	}
	
	/**
	 * Save a record
	 *
	 * @access  public
	 * @var string $pCriteria A single value (for primary key based storage)
	 */
	protected function _Save ( $pCriteria = null ) {
	}
	
	/**
	 * Retrieve a record or set of records.
	 *
	 * @access  public
	 * @var string $pCriteria A single value (for primary key based retrieval) or an array of criteria.
	 * @var string $pOrdering Ordering instructions
	 */
	public function Retrieve ( $pCriteria = null, $pOrdering = null ) {
		if ( is_array ( $pCriteria ) ) 
			return ( $this->_RetrieveWhere ( $pCriteria ) );
		else 
			return ( $this->_Retrieve ( $pCriteria ) );
	}
	
	/**
	 * Retrieve a set of records based on complex criteria.
	 *
	 * @access  public
	 * @var string $pCriteria A single value (for primary key based retrieval) or an array of criteria.
	 * @var string $pOrdering Ordering instructions
	 */
	protected function _RetrieveWhere ( $pCriteria, $pOrdering = null ) {
	}
	
	/**
	 * Retrieve a record
	 *
	 * @access protected
	 * @var string $pCriteria A single value (for primary key based retrieval)
	 * @var string $pOrdering Ordering instructions
	 */
	protected function _Retrieve ( $pCriteria = null, $pOrdering = null ) {
	}
	
	/**
	 * Delete a record or set of records
	 *
	 * @access protected
	 * @var string $pCriteria A single value (for primary key based retrieval) or an array of criteria.
	 */
	public function Delete ( $pCriteria = null ) {
		
		if ( is_array ( $pCriteria ) ) 
			return ( $this->_DeleteWhere ( $pCriteria ) );
		else 
			return ( $this->_Delete ( $pCriteria ) );
	}
	
	/**
	 * Delete a set of records
	 *
	 * @access protected
	 * @var string $pCriteria An array of criteria.
	 */
	protected function _DeleteWhere ( $pCriteria = null ) {
	}
	
	/**
	 * Delete a record
	 *
	 * @access protected
	 * @var string $pCriteria A single value (for primary key based retrieval).
	 */
	protected function _Delete ( $pCriteria = null ) {
	}
	
	/**
	 * Execute a custom query
	 *
	 * @access public
	 * @var string $pQuery The SQL query to execute.
	 */
	public function Query ( $pQuery ) {
	}
	
	/**
	 * Fetch a single row of data from a query.
	 *
	 * @access public
	 */
	public function Fetch ( ) {
		/*
		 * Gretchen: That is so fetch! 
		 * Regina: Gretchen, stop trying to make fetch happen. It's not going to happen.
		 *
		 */
	}
	
	/**
	 * Synchronize the internal data with the values from $_REQUEST.
	 *
	 * @access public
	 */
	public function Synchronize ( ) {
	}
	
	/**
	 * Seek to a position in the sql results
	 *
	 * @access public
	 * @string integer $pPosition Which position to seek to.
	 */
	public function Seek ( $pPosition ) {
	}
	
	/**
	 * Begin a transaction
	 *
	 * @access public
	 */
	public function Begin () {
	}
	
	/**
	 * Commit a transaction
	 *
	 * @access public
	 */
	public function Commit () {
	}
	
	/**
	 * Rollback a transaction
	 *
	 * @access public
	 */
	public function Rollback () {
	}
	
}
