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
	
	protected $_Toolbox;
	protected $_RedBean;
	protected $_DB;
	protected $_Writer;
	protected $_Tree;
	protected $_Assoc;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {
		
		$Config = $this->GetSys ( "Config" );
		
		$un = $Config->GetConfiguration ('un');
		$pw = $Config->GetConfiguration ('pw');
		$host = $Config->GetConfiguration ('host');
		$db = $Config->GetConfiguration ('db');
		$type = $Config->GetConfiguration ('type');
		
		$mode = $Config->GetConfiguration ('mode');
		
		//Assemble a database connection string (DSN)
		$connect = "$type:host=$host;dbname=$db";
		
		// Check: extension_loaded ( "pdo_mysql");
		
		// If mode is development, database will be modified by functions.
		if ( $mode == "development" ) {
			$this->_Toolbox = RedBean_Setup::kickstartDev($connect, $un, $pw);
		} else {
			$this->_Toolbox = RedBean_Setup::kickstartFrozen($connect, $un, $pw);
		}
		
		$this->_DB = $this->_Toolbox->getDatabaseAdapter();
		$this->_Writer = $this->_Toolbox->getWriter();
		$this->_Assoc = new RedBean_AssociationManager( $this->_Toolbox );
		$this->_Tree = new RedBean_TreeManager( $this->_Toolbox );
		
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
