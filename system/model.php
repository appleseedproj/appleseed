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
		
		$fieldinfo = $Database->GetFieldInformation ( $this->_Tablename );
		
		foreach ( $fieldinfo as $f => $field ) {
			$fieldname = $field['Field'];
			$this->_Fields[$fieldname] = $field;
		}
		
		parent::__construct();
	}
        
}
