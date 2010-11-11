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

/** Photos Component Sets Model
 * 
 * Photos Component Sets Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Photos
 */
class cPhotosSetsModel extends cModel {
	
	protected $_Tablename = 'PhotoSets';
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function Load ( $pOwner ) {
		
		$this->Retrieve ( array ( 'Owner_FK' => $pOwner ), 'Created DESC' );
	}
	
}
