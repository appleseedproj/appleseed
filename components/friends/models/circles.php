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
		
	
	public function Circles ( $pUserId ) {
		
		$this->Retrieve ( array ( "userAuth_uID" => $pUserId ), 'sID ASC' );
		
		while ( $this->Fetch() ) {
			$return[] = array ( "id" => $this->Get ( "tID" ), "name" => $this->Get ( "Name" ) );
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
		$this->Friend = new cModel ( "friendInformation" );
		$this->Friend->Structure();
		$this->Friend->Retrieve ( array ( "userAuth_uID" => $pUserId, "Username" => $username, "Domain" => $domain ) );
		$this->Friend->Fetch();
		if ( !$friendId = $this->Friend->Get ( "tID" ) ) return ( false );
		
		$this->CirclesMap = new cModel ( "friendCirclesList" );
		$this->CirclesMap->Structure();
		$this->CirclesMap->Retrieve ( array ( "friendInformation_tID" => $friendId, "friendCircles_tID" => '()' . $inList ) );
		$return = array ();
		while ( $this->CirclesMap->Fetch() ) {
			$this->Retrieve ( array ( "tID" => $this->CirclesMap->Get ( "friendCircles_tID" ) ) );
			$this->Fetch();
			$return[] = $this->Get ( "Name" );
		}
		
		return ( $return );
		
		$return = array ();
		
		return ( $return );
	}
	
	public function SaveFriendToCircle ( $pUserId, $pFriend, $pCircle ) {
		
		list ( $username, $domain ) = explode ( '@', $pFriend );
		
		// Get the circle id
		$this->Retrieve ( array ( "userAuth_uID" => $pUserId, "Name" => $pCircle ) );
		$this->Fetch();
		if ( !$circleId = $this->Get ( "tID" ) ) return ( false );
		
		// Get the friend id
		$this->Friend = new cModel ( "friendInformation" );
		$this->Friend->Structure();
		$this->Friend->Retrieve ( array ( "userAuth_uID" => $pUserId, "Username" => $username, "Domain" => $domain ) );
		$this->Friend->Fetch();
		if ( !$friendId = $this->Friend->Get ( "tID" ) ) return ( false );
		
		// Get the map id
		$this->CirclesMap = new cModel ( "friendCirclesList" );
		$this->CirclesMap->Structure();
		$this->CirclesMap->Retrieve ( array ( "friendInformation_tID" => $friendId, "friendCircles_tID" => $circleId ) );
		$this->CirclesMap->Fetch();
		if ( !$mapId = $this->CirclesMap->Get ( "tID" ) ) {
			// Doesn't exist in map table, so create it.
			$this->CirclesMap->Set ( "friendInformation_tID", $friendId );
			$this->CirclesMap->Set ( "friendCircles_tID", $circleId );
			$this->CirclesMap->Save ();
		} else {
			// Exists in map table, so delete it.
			$this->CirclesMap->Delete ();
		}
		
		return ( true );
	}
	
}