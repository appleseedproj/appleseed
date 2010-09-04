<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** System Update Component Model
 * 
 * System Update Component Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  System
 */
class cSystemAdminUpdateModel extends cModel {
	
	protected $_Tablename = "SystemUpdate";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function AddServer ( $pServer ) {
		
		$table = $this->Get ( "Prefix" ) . $this->Get ( "Tablename" );
		
		$query = " INSERT IGNORE INTO $table (Server) VALUES ( ? )";
		
		$prepared[] = $pServer;
		
		$this->Query ( $query, $prepared );
		
		return ( true );
	}
}
