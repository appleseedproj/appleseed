<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Photos
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Photos Component Photos Model
 * 
 * Photos Component Photos Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Photos
 */
class cPhotosModel extends cModel {
	
	protected $_Tablename = 'Photos';
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}

	public function LoadFromSet ( $pSetId ) {
		$this->Retrieve ( array ( 'Set_FK' => $pSetId ), 'Created DESC' );

		return ( true );
	}

	public function Load ( $pIdentifier ) {

		$this->Retrieve ( array ( 'Identifier' => $pIdentifier ) );

		return ( true );
	}
	
	public function GetCover ( $pSetId ) {
		
		$this->Retrieve ( array ( 'Set_FK' => $pSetId ), 'Created DESC' );
		$this->Fetch();

		return ( true );
	}
	
}
