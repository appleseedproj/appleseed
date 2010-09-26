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

/** Friends Component Circles Model
 * 
 * Friends Component Circles Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Friends
 */
class cFriendsCirclesModel extends cModel {
	
	protected $_Tablename = "friendCircles";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function Load ( $pUserId, $pCircle ) {
		
		$this->Retrieve ( array ( "userAuth_uID" => $pUserId, "Name" => $pCircle ) );
		
		if ( !$this->Fetch() ) return ( false );
		
		return ( $this->Get ( "Data" ) );
	}
	
	public function SaveCircle ( $pCircle, $pUserId, $pId = null ) {
		
		$this->Protect( "tID" );
		
		$this->Set ( "userAuth_uID", $pUserId );
		$this->Set ( "Name", $pCircle );
		
		if ( $pId ) {
			$this->Save ( array ( "tID" => $pId, "userAuth_uID" => $pUserId ) );
		} else {
			$this->Save ( );
		}
		
		return ( true );
	}
	
	public function DeleteCircle ( $pCircle, $pUserId ) {
		$this->Protect( "tID" );
		
		$this->Delete ( array ( "Name" => $pCircle, "userAuth_uID" => $pUserId ) );
		
		return ( true );
	}
		
	
	public function GetCircles ( $pUsername ) {
		
		$userAuth = new cModel ( "userAuthorization" );
		$userAuth->Structure();
		
		$userAuth->Retrieve ( array ( "Username" => $pUsername ) );
		
		$userAuth->Fetch();
		
		$this->Retrieve ( array ( "userAuth_uID" => $userAuth->Get ( "uID" ) ), 'sID ASC' );
		
		while ( $this->Fetch() ) {
			$return[] = array ( "id" => $this->Get ( "tID" ), "name" => $this->Get ( "Name" ) );
		}
		
		return ( $return );
	}
	
}