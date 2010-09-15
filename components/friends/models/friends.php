<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Friends
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Friends Component Model
 * 
 * Friends Component Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Friends
 */
class cFriendsModel extends cModel {
	
	protected $_Tablename = "friendInformation";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function CountFriends ( $pUserId ) {
		
		$this->Retrieve ( array ( "userAuth_uID" => $pUserId, "Verification" => 1 ) );
		
		$count = $this->Get ( "Total" );
		
		return ( $count );
	}
	
}