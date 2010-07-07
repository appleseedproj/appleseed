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
	protected $_Protected;
	
	protected $_PrimaryKey;
	protected $_ForeignKeys;
	
	protected $_Query;
	protected $_Rows;
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
		} elseif ( $this->_Tablename ) {
			$tablename = $this->_Tablename;
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
			
			if ( $field['Key'] == "PRI" ) $this->_PrimaryKey = $field['Field'];
		}
		
		$this->_Protected = array ();
		
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
		
		/*
		 * REFERENCE:
		 * 
		$criteria = array (
			"Username" => "Search1",
			array (
				"||!=Email" => "Search2",
				"&&~~LastLogin" => "Search3"
			)
		);
		
		$ordering = "Username DESC";
		
			WHERE 
				Username = '%s' 
				OR ( Email NOT EQUAL '%s' AND LastLogin LIKE '%s' ) 
			GROUP BY Username DESC
		
		// ~~ LIKE
		// !~ NOT LIKE
		// != NOT EQUAL
		// =n IS NULL
		// !n NOT NULL
		// >> GREATER THAN
		// << LESS THAN
		 
		*/
		
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
		
		$pk = $this->_PrimaryKey;
		$tbl = $this->_Tablename;
		$pre = $this->_Prefix;
		
		$table = $this->_Prefix . $this->_Tablename;
		
		$sql = 'UPDATE %table$s';
		$replacements["table"] = $table;
		
		$sql .= ' SET ';
		
		foreach ( $this->_Fields as $f => $fields ) {
			$fieldname = $fields['Field'];	
			
			// Don't update the primary key.
			if ($fieldname == $this->_PrimaryKey) continue;
			
			// Skip anything on the protected list.
			if ( in_array ( $fieldname, $this->_Protected ) ) continue;
			
			$internal = "_" . strtolower ( $fieldname );
			$internal_field = $internal . "_field";
			$internal_value = $internal . "_value";
			
			$queries[] = ' %' . $internal_field . '$s = \'%' . $internal_value . '$s\'';
			
			$replacements[$internal_field] = $fieldname;
			$replacements[$internal_value] = $this->Get ( $fieldname );
			
		}
		
		$sql .= implode ( ", ", $queries );
		
		$sql .= ' WHERE %pk$s = \'%criteria$s\' ';
		$replacements["pk"] = $pk;
		
		// Without criteria, we'll use the current primary key value
		if ( $pCriteria ) {
			$replacements["criteria"] = $pCriteria;
		} else {
			$primarykey_value = $this->Get ( $pk );
			
			// We don't want to update everything in the table, too dangerous, so error out.
			if ( !$primarykey_value ) return ( false );
			
			$replacements["criteria"] = $primarykey_value;
		}
		
		// @todo PDO already has named prepared statements.  Switch to those.
		$sql = sprintfn ( $sql, $replacements );
		
		$DBO = $this->GetSys ( "Database" )->Get ( "DB" );
		
		$this->_Handle = $DBO->Prepare ( $sql );
		$this->_Handle->Execute ();
		
		$this->_Query = $sql;
		
		$this->_Rows = $this->_Handle->rowCount();
		$this->_Total = $this->_Handle->rowCount();
		
		// @todo Add query to global list.
		
		return ( true );
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
			return ( $this->_RetrieveWhere ( $pCriteria, $pOrdering ) );
		else 
			return ( $this->_Retrieve ( $pCriteria, $pOrdering  ) );
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
		
		$pk = $this->_PrimaryKey;
		$tbl = $this->_Tablename;
		$pre = $this->_Prefix;
		
		$table = $this->_Prefix . $this->_Tablename;
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM %table$s';
		$replacements["table"] = $table;
		
		// Without criteria, we'll find everything in the table.
		if ( $pCriteria ) {
			$sql .= ' WHERE %pk$s = "%criteria$s" ';
			$replacements["pk"] = $pk;
			$replacements["criteria"] = $pCriteria;
		}
		
		if ( $pOrdering ) {
			$sql .= ' ORDER BY %ordering$s ';
			$replacements["ordering"] = $pOrdering;
		}
		
		$sql = sprintfn ( $sql, $replacements );
		
		$DBO = $this->GetSys ( "Database" )->Get ( "DB" );
		
		$this->_Handle = $DBO->Prepare ( $sql );
		$this->_Handle->Execute ();
		
		$data = & $this->_Handle->Fetch ( PDO::FETCH_OBJ );
		
		if ( !$data ) return ( false );
		
		$this->_Data = (array) $data;
		
		foreach ( $this->_Data as $field => $value ) {
			$internal = "_" . $field;
			$this->$internal = $value;
		}
		
		$this->_Query = $sql;
		
		$this->_CountResults();
		
		// @todo Add query to global list.
		
		return ( true );
	}
	
	protected function _CountResults ( ) {
		
		$DBO = $this->GetSys ( "Database" )->Get ( "DB" );
		
		$result = $DBO->query('SELECT FOUND_ROWS()'); 
		$rowCount = (int) $result->fetchColumn(); 
		
		$this->_Total = $rowCount;
		$this->_Rows = $this->_Handle->rowCount();
		
		return ( true );
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
		 
		$data = & $this->_Handle->Fetch ( PDO::FETCH_OBJ );
		
		if ( !$data ) return ( false );
		
		$this->_Data = (array) $data;
		
		foreach ( $this->_Data as $field => $value ) {
			$internal = "_" . $field;
			$this->$internal = $value;
		}
		
		return ( true );
	}
	
	/**
	 * Synchronize the internal data with the values from $_REQUEST.
	 *
	 * @access public
	 */
	public function Synchronize ( ) {
		
		$requests = cRequest::Get();
		
		foreach ( $this->_Fields as $f => $field ) {
			$fieldname = $field['Field'];
			$fieldname_lower = strtolower ( $fieldname );
			
			if ( $requests[$fieldname_lower] ) {
				$this->Set ( $fieldname, $requests[$fieldname_lower] );
			}
		}
		
		return ( true );
	}
	
	/**
	 * Protect the listed fields from updates or modifications.
	 *
	 * @access public
	 */
	public function Protect ( $pProtected ) {
		
		$protected = (array) $pProtected;
		
		foreach ( $protected as $p => $protect ) {
			$this->_Protected[$protect] = $protect;
		}
		
		return ( true );
	}
	
	/**
	 * Take the listed fields off of the protected list.
	 *
	 * @access public
	 */
	public function Endanger ( $pEndangered ) {
		
		$endangered = (array) $pEndangered;
		
		foreach ( $endangered as $e => $endanger ) {
			unset ( $this->_Protected[$endanger] );
		}
		
		return ( true );
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
