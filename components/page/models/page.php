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
class cPageModel extends cModel {
	
	protected $_Tablename = 'PagePosts';
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function RetrieveCurrent ( $pUserId ) {
		
		$criteria = array ( 'User_FK' => $pUserId, 'Current' => 1 );
		$this->Retrieve ( $criteria );
		
		return ( true );
	}
	
	public function ClearCurrent ( $pUserId ) {
		$table = $this->_Prefix . $this->_Tablename;
		$query = 'UPDATE `' . $table . '` SET `Current` = ? WHERE `User_FK` = ?';
		
		$prepared[] = '0'; 
		$prepared[] = $pUserId;
		
		$this->Query ( $query, $prepared );
		
		return ( true );
		
		$criteria = array ( 'User_FK' => $pUserId, 'Current' => 1 );
		$this->Retrieve ( $criteria );
		
		if ( $this->Get ( 'Total' ) == 0 ) return ( true );
		
		$this->Fetch();
		$this->Set ( 'Current', 0 );
		
		$this->Save();
		
		return ( true );
	}
	
	public function RetrievePagePosts ( $pUserId ) {
		
		$criteria = array ( 'User_FK' => $pUserId );
		$this->Retrieve ( $criteria, 'Stamp DESC' );
		
		return ( true );
	}
	
	public function Post ( $pComment, $pPrivacy, $pUser, $pOwner, $pCurrent = false ) {
		$this->Protect ( 'Post_PK', null );
		$this->Set ( 'User_FK', $pUser );
		$this->Set ( 'Action', 'posted' );
		$this->Set ( 'ActionOwner', $pOwner );
		$this->Set ( 'Comment', $pComment );
		$this->Set ( 'Stamp', NOW() );
		
		if ( $pCurrent ) {
			$this->Set ( 'Current', (int)true );
			$this->ClearCurrent ( $pUser );
		} else {
			$this->Set ( 'Current', '0' );
		}
		
		$this->Save();
		
		return ( true );
	}
	
}
