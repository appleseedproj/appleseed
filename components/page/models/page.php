<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   ProfilePage
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Page Component Page Model
 * 
 * Page Component Page Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Page
 */
class cPagePageModel extends cModel {
	
	protected $_Tablename = "PagePosts";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function RetrieveCurrent ( $pUserId ) {
		
		$criteria = array ( "Owner_FK" => $pUserId, "Current" => 1 );
		$this->Retrieve ( $criteria );
		
		return ( true );
	}
	
	public function ClearCurrent ( $pUserId ) {
		$criteria = array ( "Owner_FK" => $pUserId, "Current" => 1 );
		$this->Retrieve ( $criteria );
		
		if ( $this->Get ( " Total" ) == 0 ) return ( true );
		
		$this->Fetch();
		$this->Set ( "Current", 0 );
		
		$this->Save();
		
		return ( true );
	}
	
}
