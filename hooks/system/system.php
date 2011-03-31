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

/** System Hook Class
 * 
 * System Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cSystemHook extends cHook {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function BeginSystemInitialize ( $pData = null ) {

		$this->_CheckSchemaVersion ( );
		return ( true );
	}

	private function _CheckSchemaVersion ( ) {
		
		$Config = $this->GetSys ( 'Config' );
		
		// Get the schema version required
		$Version = $Config->GetConfiguration ( 'schema_version' );
		
		$Model = new cModel ( 'SchemaVersions' );
		
		// SchemaVersions doesn't exist, we must be upgrading from 0.7.8 and have not updated.
		if ( !$Model->Get ( 'Exists' ) ) {
			echo __ ( 'Table Does Not Exist', array ( 'tablename' => 'SchemaVersions' ) );
			exit;
		}
		
		// Retrieve the latest schema version information.
		$Model->Retrieve ( null, 'Schema_PK DESC', array ( 'start' => 0, 'step' => 1 ) );
		
		// No schema version info was found, so we must be upgrading from 0.7.8.
		if ( $Model->Get ( 'Total' ) == 0 ) {
			echo __ ( 'Schema Version Information Unavailable', array ( 'version' => $Version ) );
			exit;
		}
		
		$Model->Fetch();
		
		$Current = $Model->Get ( 'Version' );
		$Script = $Model->Get ( 'Script' );
		
		// If the current and the expected schema version don't match, give a suggested update scripts to run.
		if ( $Current != $Version ) {
			echo __ ( 'Incorrect Schema Version', array ( 'version' => $Version, 'current' => $Current, 'script' => $Script ) );
			exit;
		}
		
		return ( true );
	}
	
}
