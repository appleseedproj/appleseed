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
			$tablename = $pTable;
		} elseif ( $this->_Tablename ) {
			$tablename = $this->_Tablename;
		} else {
			$tablename = preg_replace ( '/^c/', "", get_class ( $this ) );
			$tablename = preg_replace ( '/Model$/', "", $tablename );
		}
		
		$this->_Tablename = $tablename;
		
		// Pull the table structure into the class.
		$this->Structure ();
		
		$this->_Protected = array ();
		
		parent::__construct();
	}
	
	public function Structure ( $pTablename = null) {
		
		$Database = $this->GetSys ( "Database" );
		
		if ( !$pTablename ) $pTablename = $this->_Tablename;
		
		$fieldinfo = $Database->GetFieldInformation ( $pTablename );
		
		foreach ( $fieldinfo as $f => $field ) {
			$fieldname = $field['Field'];
			$this->_Fields[$fieldname] = $field;
			
			if ( $field['Key'] == "PRI" ) $this->_PrimaryKey = $field['Field'];
		}
		
		return ( true );
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
		// () IN
		// !( IN
		 
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
		
		$pk = $this->_PrimaryKey;
		$tbl = $this->_Tablename;
		$pre = $this->_Prefix;
		
		$table = $this->_Prefix . $this->_Tablename;
		
		$sql = 'UPDATE %table$s';
		$replacements["table"] = $table;
		
		$sql .= ' SET ';
		
		$prepared = array ();
		foreach ( $this->_Fields as $f => $fields ) {
			$fieldname = $fields['Field'];	
			
			// Don't update the primary key.
			if ($fieldname == $this->_PrimaryKey) continue;
			
			// Skip anything on the protected list.
			if ( in_array ( $fieldname, $this->_Protected ) ) continue;
			
			$internal = "_" . strtolower ( $fieldname );
			$internal_field = $internal . "_field";
			$internal_value = $internal . "_value";
			
			$queries[] = ' %' . $internal_field . '$s = ?';
			$prepared[] = $this->Get ( $fieldname );
			
			$replacements[$internal_field] = $fieldname;
			
		}
		
		list ( $where, $criteriaPrepared ) = $this->_BuildCriteria ( $pCriteria ); 
		
		$prepared = array_merge ( $prepared, $criteriaPrepared );
		
		$sql .= implode ( ", ", $queries );
		
		$sql .= " WHERE " . $where;
		
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
		
		$sql = sprintfn ( $sql, $replacements );
		
		$DBO = $this->GetSys ( "Database" )->Get ( "DB" );
		
		$this->Query ( $sql, $prepared );
		
		$this->_Rows = $this->_Handle->rowCount();
		$this->_Total = $this->_Handle->rowCount();
		
		// @todo Add query to global list.
		
		return ( true );
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
		
		$primarykey_value = $this->Get ( $pk );
		
		if ( $primarykey_value ) {
		
			$replacements["pk"] = $pk;
			$replacements["criteria"] = $primarykey_value;
			$replacements["table"] = $table;
		
			$prepared = array ();
		
			// Check for an existing record.
			$sql = 'SELECT * FROM %table$s WHERE %pk$s = \'%criteria$s\' ';
		
			$sql = sprintfn ( $sql, $replacements );
		
			$this->Query ( $sql, $prepared );
		
			$resultCount = $this->Get ( "Rows" );
		} else {
			$resultCount = 0;
		}
		
		$usePrimary = false;
		if ( isset ( $primarykey_value ) ) $usePrimary = true;
		
		if ( ( !$pCriteria ) and ( !$this->Get ( $pk ) ) ) {
			return ( $this->_SaveNew ( $usePrimary ) );
		} else if ( $resultCount == 0 ) {
			$this->Set ( $pk, $primarykey_value );
			return ( $this->_SaveNew ( $usePrimary ) );
		}
		
		$sql = 'UPDATE %table$s';
		
		$sql .= ' SET ';
		
		$prepared = array ();
		foreach ( $this->_Fields as $f => $fields ) {
			$fieldname = $fields['Field'];	
			
			// Don't update the primary key.
			if ($fieldname == $this->_PrimaryKey) continue;
			
			// Skip anything on the protected list.
			if ( in_array ( $fieldname, $this->_Protected ) ) continue;
			
			$internal = "_" . strtolower ( $fieldname );
			$internal_field = $internal . "_field";
			$internal_value = $internal . "_value";
			
			$queries[] = '`%' . $internal_field . '$s` = ?';
			$prepared[] = $this->Get ( $fieldname );
			
			$replacements[$internal_field] = $fieldname;
			
		}
		
		$sql .= implode ( ", ", $queries );
		
		$sql .= ' WHERE %pk$s = \'%criteria$s\' ';
		
		// Without criteria, we'll use the current primary key value
		if ( $pCriteria ) {
			$replacements["criteria"] = $pCriteria;
		} else {
			// We don't want to update everything in the table, too dangerous, so error out.
			if ( !$primarykey_value ) return ( false );
			
			$replacements["criteria"] = $primarykey_value;
		}
		
		$sql = sprintfn ( $sql, $replacements );
		
		$this->Query ( $sql, $prepared );
		
		$this->_Rows = $this->_Handle->rowCount();
		$this->_Total = $this->_Handle->rowCount();
		
		return ( true );
	}
	
	/**
	 * Save a new record
	 *
	 * @access  public
	 */
	protected function _SaveNew ( $pUsePrimary = false ) {
		
		$pk = $this->_PrimaryKey;
		$tbl = $this->_Tablename;
		$pre = $this->_Prefix;
		
		$table = $this->_Prefix . $this->_Tablename;
		
		$sql = 'INSERT INTO %table$s';
		$replacements["table"] = $table;
		
		$prepared = array ();
		foreach ( $this->_Fields as $f => $fields ) {
			$fieldname = $fields['Field'];	
			
			// Don't update the primary key.
			if ( ( !$pUsePrimary ) && ($fieldname == $this->_PrimaryKey ) ) continue;
			
			// Skip anything on the protected list.
			if ( in_array ( $fieldname, $this->_Protected ) ) continue;
			
			$internal = "_" . strtolower ( $fieldname );
			$internal_field = $internal . "_field";
			$internal_value = $internal . "_value";
			
			$fieldnames[] = '`%' . $internal_field . '$s`';
			$prepared[] = $this->Get ( $fieldname );
			
			$replacements[$internal_field] = $fieldname;
		}
		
		$sql .= ' ( ' . join ( ', ', $fieldnames ) . ' ) ';
		
		$sql .= ' VALUES ';
		
		$prepared = array ();
		foreach ( $this->_Fields as $f => $fields ) {
			$fieldname = $fields['Field'];	
			
			// Don't update the primary key.
			if ( ( !$pUsePrimary ) && ($fieldname == $this->_PrimaryKey ) ) continue;
			
			// Skip anything on the protected list.
			if ( in_array ( $fieldname, $this->_Protected ) ) continue;
			
			$internal = "_" . strtolower ( $fieldname );
			$internal_field = $internal . "_field";
			$internal_value = $internal . "_value";
			
			$values[] = '?';
			$prepared[] = $this->Get ( $fieldname );
			
			$replacements[$internal_field] = $fieldname;
			
		}
		
		$sql .= ' ( ' . implode ( ', ', $values ) . ' ) ';
		
		$sql = sprintfn ( $sql, $replacements );
		
		$DBO = $this->GetSys ( 'Database' )->Get ( 'DB' );
		
		$this->Query ( $sql, $prepared );
		
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
	public function Retrieve ( $pCriteria = null, $pOrdering = null, $pLimit = null ) {
		if ( is_array ( $pCriteria ) ) 
			return ( $this->_RetrieveWhere ( $pCriteria, $pOrdering, $pLimit ) );
		else 
			return ( $this->_Retrieve ( $pCriteria, $pOrdering, $pLimit ) );
	}
	
	/**
	 * Retrieve a set of records based on complex criteria.
	 *
	 * @access  public
	 * @var string $pCriteria A single value (for primary key based retrieval) or an array of criteria.
	 * @var string $pOrdering Ordering instructions
	 */
	protected function _RetrieveWhere ( $pCriteria, $pOrdering = null, $pLimit = null ) {
		
		$pk = $this->_PrimaryKey;
		$tbl = $this->_Tablename;
		$pre = $this->_Prefix;
		
		$table = $this->_Prefix . $this->_Tablename;
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM %table$s';
		$replacements['table'] = $table;
		
		list ( $where, $prepared ) = $this->_BuildCriteria ( $pCriteria ); 
		
		$sql .= " WHERE " . $where;
		
		if ( $pOrdering ) {
			$sql .= ' ORDER BY %ordering$s ';
			$replacements['ordering'] = $pOrdering;
		}
		
		if ( $pLimit ) {
			$replacements['start'] = (int) $pLimit['start'] ? $pLimit['start'] : 0;
			$replacements['step'] = (int) $pLimit['step'] ? $pLimit['step'] : 20;
			
			$sql .= ' LIMIT %start$s, %step$s ';
			
		}
		
		// Replace tablenames, fieldnames, ordering, limits, etc.
		$sql = sprintfn ( $sql, $replacements );
		
		// Execute query with prepared statements.
		$this->Query ( $sql, $prepared );
		
		// @todo Add query to global list.
		
	}
	
	/**
	 * Construct a complex set of criteria based on a multi-dimensional array.
	 *
	 * @access  public
	 * @var array $pCriteria An array from which to construct the criteria.
	 */
	protected function _BuildCriteria ( array $pCriteria ) {
		
		$this->_Prepared = array ();
		
		$DBO = $this->GetSys ( "Database" )->Get ( "DB" );
		
		$statements = array ();
		
		foreach ( $pCriteria as $c => $criteria ) {
			
			$operand = null;
			$comparison = null;
			$comparison_string = null;
			
			if ( $this->_IsMasslist ( $criteria ) ) {
				$directive = '()';
			} elseif ( is_array ( $criteria ) ) {
				$comparison = 'AND';
				$compare = substr ( $c, 0, 2 );
				
				if ( $compare == '||' ) $comparison = 'OR';
				if ( count ( $statements ) == 0 ) $comparison = null;
				list ( $inner_statement, $null ) = $this->_BuildCriteria ( $criteria ); 
				$statements[] = $comparison . ' (' . $inner_statement . ')';
				continue;
			} else {
				// The first two characters of the value determine the operand.
				$directive = substr ( $criteria, 0, 2 );
			}
			
			switch ( $directive ) {
				case '~~':
					$operand = "LIKE";
				break;
				case '!~':
					$operand = "NOT LIKE";
				break;
				case '!=':
					$operand = "NOT EQUAL";
				break;
				case '=n':
					$operand = "IS NULL";
					$criteria = null;
				break;
				case '!n':
					$operand = "IS NOT NULL";
					$criteria = null;
				break;
				case '>>':
					$operand = ">";
				break;
				case '<<':
					$operand = "<";
				break;
				case '>=':
					$operand = ">=";
				break;
				case '<=':
					$operand = "<=";
				break;
				case '()':
					$operand = "IN";
				break;
				case '!(':
					$operand = "NOT IN";
				break;
			}
			
			if ( $operand ) {
				// Strip the operand information from the criteria.
				if ( !is_array ( $criteria ) ) $criteria = substr ( $criteria, 2, strlen ( $criteria ) );
			} else {
				// Default to equal comparison
				$operand = "=";
			}
			
			$compare = substr ( $c, 0, 2 );
			
			switch ( $compare ) {
				case '||':
					$comparison = 'OR';
				break;
			}
			
			if ( $comparison ) {
				// Strip the operand information from the criteria.
				$c = substr ( $c, 2, strlen ( $c ) );
			} else {
				// Default to equal comparison
				$comparison = "AND";
			}
			
			if ( count ( $statements ) == 0 ) $comparison = null;
			if ( $comparison ) $comparison_string = $comparison . ' ' ;
			
			switch ( $operand ) {
				case 'IS NULL':
				case 'IS NOT NULL':
					// No criteria used for NULL and NOT NULL
					$statements[] = $comparison_string . $c . ' ' . $operand . ' ';
				break;
				case 'IN':
					$elements = explode ( ',', $criteria );
					$statements[] = $comparison_string . $c . ' ' . $operand . ' (';
					foreach ( $elements as $e => $element ) {
						$in[] = '?';
						$this->_Prepared[] = ltrim ( rtrim ( $element ) );
					} 
					$statements[count($statements)-1] .= implode ( ', ', $in) . ')';
				break;
				default:
					$statements[] = $comparison_string . $c . ' ' . $operand . ' ' . '?';
					$this->_Prepared[] = $criteria;
				break;
			}
			
		}
		
		$where = implode ( " ", $statements );
		
		return ( array ( $where, $this->_Prepared ) );
	}
	
	protected function _IsMasslist ( $pCriteria ) {
		
		if ( !is_array ( $pCriteria ) ) return ( false );
		
		foreach ( $pCriteria as $c => $criteria ) {
			if ( $criteria == 'on' ) continue;
			return ( false );
		}
		
		return ( true );
	}
	
	/**
	 * Retrieve a record
	 *
	 * @access protected
	 * @var string $pCriteria A single value (for primary key based retrieval)
	 * @var string $pOrdering Ordering instructions
	 */
	protected function _Retrieve ( $pCriteria = null, $pOrdering = null, $pLimit = null ) {
		
		$pk = $this->_PrimaryKey;
		$tbl = $this->_Tablename;
		$pre = $this->_Prefix;
		
		$table = $this->_Prefix . $this->_Tablename;
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM %table$s';
		$replacements['table'] = $table;
		
		// Without criteria, we'll find everything in the table.
		if ( $pCriteria ) {
			$sql .= ' WHERE %pk$s = ? ';
			$replacements['pk'] = $pk;
			$prepared[] = $pCriteria;
		}
		
		if ( $pOrdering ) {
			$sql .= ' ORDER BY %ordering$s ';
			$replacements['ordering'] = $pOrdering;
		}
		
		if ( $pLimit ) {
			$replacements['start'] = (int) $pLimit['start'] ? $pLimit['start'] : 0;
			$replacements['step'] = (int) $pLimit['step'] ? $pLimit['step'] : 20;
			
			$sql .= ' LIMIT %start$s, %step$s ';
			
		}
		
		// Replace tablenames, fieldnames, ordering, limits, etc.
		$sql = sprintfn ( $sql, $replacements );
		
		// Execute query with prepared statements.
		$this->Query ( $sql, $prepared );
		
		// @todo Add query to global list.
		
		return ( true );
	}
	
	protected function _CountResults ( ) {
		
		$DBO = $this->GetSys ( "Database" )->Get ( "DB" );
		
		$result = $DBO->query('SELECT FOUND_ROWS()'); 
		$rowCount = (int) $result->fetchColumn(); 
		
		$this->_Total = $rowCount;
		$this->_Rows = $this->_Handle->rowCount();
		
		if ( !$this->_Total ) $this->_Total = $this->_Rows;
		
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
		
		$pk = $this->_PrimaryKey;
		$tbl = $this->_Tablename;
		$pre = $this->_Prefix;
		
		$table = $this->_Prefix . $this->_Tablename;
		
		$sql = 'DELETE FROM %table$s';
		$replacements['table'] = $table;
		
		list ( $where, $prepared ) = $this->_BuildCriteria ( $pCriteria ); 
		
		$sql .= " WHERE " . $where;
		
		// Replace tablenames, fieldnames, ordering, limits, etc.
		$sql = sprintfn ( $sql, $replacements );
		
		// Execute query with prepared statements.
		$this->Query ( $sql, $prepared );
		
		// @todo Add query to global list.
		
	}
	
	/**
	 * Delete a record
	 *
	 * @access protected
	 * @var string $pCriteria A single value (for primary key based retrieval).
	 */
	protected function _Delete ( $pCriteria = null ) {
		
		$pk = $this->_PrimaryKey;
		$tbl = $this->_Tablename;
		$pre = $this->_Prefix;
		
		$primarykey_value = $this->Get ( $pk );
		
		$table = $this->_Prefix . $this->_Tablename;
		
		$sql = 'DELETE FROM %table$s';
		$replacements['table'] = $table;
		
		$sql .= ' WHERE %pk$s = ? ';
		
		// Without criteria, we'll find everything in the table.
		if ( $pCriteria ) {
			$replacements['pk'] = $pk;
			$prepared[] = $pCriteria;
		} else {
			// We don't want to update everything in the table, too dangerous, so error out.
			if ( !$primarykey_value ) return ( false );
			
			$replacements['pk'] = $pk;
			$prepared[] = $primarykey_value;
		}
		
		// Replace tablenames, fieldnames, ordering, limits, etc.
		$sql = sprintfn ( $sql, $replacements );
		
		// Execute query with prepared statements.
		$this->Query ( $sql, $prepared );
		
		// @todo Add query to global list.
		
		return ( true );
	}
	
	/**
	 * Execute a custom query
	 *
	 * @access public
	 * @var string $pQuery The SQL query to execute.
	 */
	public function Query ( $pQuery, $pPrepared = null ) {
		
		$DBO = $this->GetSys ( "Database" )->Get ( "DB" );
		
		$query = $this->_Prepare ( $pQuery, $pPrepared );
		
		$this->_Handle = $DBO->Prepare ( $query );
		
		$this->_Handle->Execute ();
		
		$this->_Query = $this->_Handle->queryString;
		
		$this->_CountResults();
		
		if ( $DBO->lastInsertId() ) {
			$this->Set ( $this->_PrimaryKey, $DBO->lastInsertId() );
		}
		
		// Add query to logs
		$contextArray = array ( get_class ( $this ), $this->_Tablename );
		$context = implode ( '.', $contextArray );
		
		$this->GetSys ( "Logs" )->Add ( "Queries", ( $this->_Handle->queryString ), $context );
		
		return ( true );
	}
	
	/**
	 * Prepare a string for execution
	 * 
	 * This is necessary because PDO doesn't have a method for retrieving post-prepared statements.
	 * For debugging and optimization purposes, we want to be able to keep a list of executed sql statements.
	 *
	 * @access public
	 * @var string $pQuery The SQL query to execute.
	 * @var array $pPrepared Associative array list of prepared values.
	 */
	protected function _Prepare ( $pQuery, $pPrepared = null ) {
		
		$query = $pQuery;
		
		$DBO = $this->GetSys ( "Database" )->Get ( "DB" );
		
		// Start by replacing #_ with the system table prefix
		if ( preg_match ( '/#__/', $query ) ) {
			$query = preg_replace ( '/#__/', $this->_Prefix, $query );
		}
		
		if ( !$pPrepared ) return ( $query );
		
		// Quote each value, to prevent injection.
		foreach ( $pPrepared as $p => $prepare ) {
			$prepared[$p] = $DBO->quote ( utf8_encode ( $prepare ) );
		}
		
		// Replace all the %variable$s references first
		$query = sprintfn ( $query, $prepared );
		
		if (strstr ( $query, 'INSERT' ) ) {
			echo '<pre>';
			echo "Query: ", $query;
			echo "</pre>";
		}
		
		// Now replace all ? placeholders with a unique identifier
		$pcount= 0;
		while ( preg_match ( '/\?/', $query ) ) {
			$query = preg_replace ( '/\?/', '@@##AA@@##' , $query, 1);
		}
		
		// Now replace all unique placeholders with the value.
		$pcount = 0;
		while ( preg_match ( '/@@##AA@@##/', $query ) ) {
			$query = preg_replace ( '/@@##AA@@##/', $prepared[$pcount++], $query, 1);
		}
		
		preg_match ( '/\:(\w+)/', $query, $results); 
		
		foreach ( $results as $r => $result ) {
			list ( $null, $match ) = explode ( ':', $result );
			if ( !isset ( $prepared[$match] ) ) continue;
			$query = preg_replace ( '/:(\w+)/', $prepared[$match], $query, 1);
		}
		
		return ( $query );
	}
	
	/**
	 * Fetch a single row of data into an object from a query.
	 *
	 * @access public
	 */
	public function Fetch ( ) {
		/*
		 * Gretchen: That is so fetch! 
		 * Regina: Gretchen, stop trying to make fetch happen. It's not going to happen.
		 *
		 */
		 
		$data = $this->_Handle->Fetch ( PDO::FETCH_OBJ );
		
		foreach ( $data as $d => $dat ) {
			$data->$d = utf8_decode ( $dat );
		}
		
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
		eval ( GLOBALS );
		
		$requests = $zApp->Request->Get();
		
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
	 * Validate the form values against the database rules.
	 *
	 * @access public
	 */
	public function Validate ( $pData ) {
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
