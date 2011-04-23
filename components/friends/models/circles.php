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
	
	protected $_Tablename = "FriendCircles";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function Load ( $pUserId, $pCircle ) {
		
		$this->Retrieve ( array ( "Owner_FK" => $pUserId, "Name" => $pCircle ) );
		
		if ( !$this->Fetch() ) return ( false );
		
		return ( $this->Get ( "Data" ) );
	}
	
	public function SaveCircle ( $pCircle, $pUserId, $pId = null ) {
		
		$this->Protect( "Circle_PK" );
		
		$Sharing = $this->GetSys ( "Request" )->Get ( "Sharing" );
		
		$this->Set ( 'Private', (int)false );
		$this->Set ( 'Protected', (int)false );
		$this->Set ( 'Shared', (int)false );
		
		if ( $Sharing == 'protected' ) {
			$this->Set ( 'Protected', (int)true );
		} else if ( $Sharing == 'shared' ) {
			$this->Set ( 'Shared', (int)true );
		} else {
			$this->Set ( 'Private', (int)true );
		}
		
		$this->Set ( "Owner_FK", $pUserId );
		$this->Set ( "Name", $pCircle );
		
		if ( $pId ) {
			$this->Save ( array ( "Circle_PK" => $pId, "Owner_FK" => $pUserId ) );
		} else {
			$this->Save ( );
		}
		
		return ( true );
	}
	
	public function DeleteCircle ( $pCircle, $pUserId ) {
		$this->Protect( "Circle_PK" );
		
		$this->Delete ( array ( "Name" => $pCircle, "Owner_FK" => $pUserId ) );
		
		return ( true );
	}
		
	
	public function Circles ( $pUserId ) {
		
		$this->Retrieve ( array ( "Owner_FK" => $pUserId ), 'sID ASC' );
		
		while ( $this->Fetch() ) {
			$return[] = array ( 'id' => $this->Get ( 'Circle_PK' ), 'name' => $this->Get ( 'Name' ), 'private' => $this->Get ( 'Private' ), 'protected' => $this->Get ( 'Protected' ), 'shared' => $this->Get ( 'Shared' ) );
		}
		
		return ( $return );
	}
	
	public function CirclesByMember ( $pUserId, $pFriend ) {
		
		$circles = $this->Circles ( $pUserId );
		
		foreach ( $circles as $c => $circle ) {
			$in[] = $circle['id'];
		}
		
		$inList = implode ( ',', $in );
		
		list ( $username, $domain ) = explode ( '@', $pFriend );
		
		// Get the friend id
		$this->Friend = new cModel ( 'FriendInformation' );
		$this->Friend->Structure();
		$this->Friend->Retrieve ( array ( "Owner_FK" => $pUserId, "Username" => $username, "Domain" => $domain ) );
		$this->Friend->Fetch();
		if ( !$friendId = $this->Friend->Get ( "Friend_PK" ) ) return ( false );
		
		$this->CirclesMap = new cModel ( "FriendCirclesMap" );
		$this->CirclesMap->Structure();
		$this->CirclesMap->Retrieve ( array ( "Friend_FK" => $friendId, "Circle_FK" => '()' . $inList ) );
		$return = array ();
		while ( $this->CirclesMap->Fetch() ) {
			$this->Retrieve ( array ( "Circle_PK" => $this->CirclesMap->Get ( "Circle_FK" ) ) );
			$this->Fetch();
			$return[] = $this->Get ( "Name" );
		}
		
		return ( $return );
	}
	
	public function SaveFriendToCircle ( $pUserId, $pFriend, $pCircle ) {
		
		list ( $username, $domain ) = explode ( '@', $pFriend );
		
		// Get the circle id
		$this->Retrieve ( array ( "Owner_FK" => $pUserId, "Name" => $pCircle ) );
		$this->Fetch();
		if ( !$circleId = $this->Get ( "Circle_PK" ) ) return ( false );
		
		// Get the friend id
		$this->Friend = new cModel ( "FriendInformation" );
		$this->Friend->Structure();
		$this->Friend->Retrieve ( array ( "Owner_FK" => $pUserId, "Username" => $username, "Domain" => $domain ) );
		$this->Friend->Fetch();
		if ( !$friendId = $this->Friend->Get ( "Friend_PK" ) ) return ( false );
		
		// Get the map id
		$this->CirclesMap = new cModel ( "FriendCirclesMap" );
		$this->CirclesMap->Structure();
		$this->CirclesMap->Retrieve ( array ( "Friend_FK" => $friendId, "Circle_FK" => $circleId ) );
		$this->CirclesMap->Fetch();
		if ( !$mapId = $this->CirclesMap->Get ( "Map_PK" ) ) {
			// Doesn't exist in map table, so create it.
			$this->CirclesMap->Set ( "Friend_FK", $friendId );
			$this->CirclesMap->Set ( "Circle_FK", $circleId );
			$this->CirclesMap->Save ();
		} else {
			// Exists in map table, so delete it.
			$this->CirclesMap->Delete ();
		}
		
		return ( true );
	}
	
}
