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
	
	protected $_Tablename = "FriendInformation";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function CountFriends ( $pUserId ) {
		
		$this->Retrieve ( array ( "Owner_FK" => $pUserId, "Verification" => 1 ) );
		
		$count = $this->Get ( "Total" );
		
		return ( $count );
	}
	
	public function RetrieveMutual ( $pFocusId, $pCurrentFriends, $pLimit = null ) {
		
		$table = $this->_Prefix . $this->_Tablename;
		
		$prepared[] = $pFocusId;
		
		foreach ( $pCurrentFriends as $f => $friend ) {
			$placeholders[] = '?';
			$prepared[] = $friend;
		}
		
		$friendsList = implode ( ', ', $placeholders );
		
		$query = "

			SELECT SQL_CALC_FOUND_ROWS *, CONCAT_WS ('@', Username, Domain) AS Account
			FROM `$table`
			WHERE `Owner_FK` = ?
			HAVING Account IN (  $friendsList )
		";
		
		if ( $pLimit ) {
			$replacements['start'] = (int) $pLimit['start'] ? $pLimit['start'] : 0;
			$replacements['step'] = (int) $pLimit['step'] ? $pLimit['step'] : 20;
			
			$query .= ' LIMIT %start$s, %step$s ';
			$query = sprintfn ( $query, $replacements );
		
		}
		
		$this->Query ( $query, $prepared );
		
		return ( true );
	}
	
	
	public function RetrieveCircle ( $pFocusId, $pCircleId, $pLimit = null ) {
		
		$friendsTable = $this->_Prefix . $this->_Tablename;
		$circlesMapTable = $this->_Prefix . 'friendCirclesList';
		
		$prepared[] = $pFocusId;
		$prepared[] = $pCircleId;
		
		$query = "
			SELECT SQL_CALC_FOUND_ROWS *
			FROM `$friendsTable` AS f, `$circlesMapTable` AS c
			WHERE f.`Owner_FK` = ?
			AND f.`Verification` = 1 
			AND c.`friendCircles_tID` = ?
			AND c.`friendInformation_tID` = f.`Friend_PK`
		";
		
		if ( $pLimit ) {
			$replacements['start'] = (int) $pLimit['start'] ? $pLimit['start'] : 0;
			$replacements['step'] = (int) $pLimit['step'] ? $pLimit['step'] : 20;
			
			$query .= ' LIMIT %start$s, %step$s ';
			$query = sprintfn ( $query, $replacements );
		
		}
		
		$this->Query ( $query, $prepared );
		
		return ( true );
	}
	
	public function RetrieveFriends ( $pFocusId, $pLimit = null ) {
		
		$this->Retrieve ( array ( "Owner_FK" => $pFocusId, "Verification" => 1 ), null, $pLimit );
		
		return ( true );
	}
	
	public function RetrieveRequests ( $pFocusId, $pLimit = null ) {
		
		$this->Retrieve ( array ( "Owner_FK" => $pFocusId, "Verification" => 2 ), null, $pLimit );
		
		return ( true );
	}
	
	public function Friends ( $pFocusId ) {
		$this->RetrieveFriends ( $pFocusId );
		
		$return = array ();
		while ( $this->Fetch() ) {
			$return[] = $this->Get ( 'Username' ) . '@' . $this->Get ( 'Domain' );
		}
		
		return ( $return );
	}
	public function FriendsInCircle ( $pFocusId, $pCircleId ) {
		$this->RetrieveCircle ( $pFocusId, $pCircleId );
		
		$return = array ();
		while ( $this->Fetch() ) {
			$return[] = $this->Get ( 'Username' ) . '@' . $this->Get ( 'Domain' );
		}
		
		return ( $return );
	}
	
	public function CheckPending ( $pUserId, $pFriend ) {
		
		list ( $friendUsername, $friendDomain ) = split ( '@', $pFriend );
		
		$this->Retrieve ( array ( "Owner_FK" => $pUserId, "Username" => $friendUsername, "Domain" => $friendDomain, "Verification" => 3 ) );	
		
		if ( $this->Get ( "Total" ) > 0 ) return ( true );
		
		return ( false );
	}
	
	public function CheckRequest ( $pUserId, $pFriend ) {
		
		list ( $friendUsername, $friendDomain ) = split ( '@', $pFriend );
		
		$this->Retrieve ( array ( "Owner_FK" => $pUserId, "Username" => $friendUsername, "Domain" => $friendDomain, "Verification" => 2 ) );	
		
		if ( $this->Get ( "Total" ) > 0 ) return ( true );
		
		return ( false );
	}
	
	public function SavePending ( $pUserId, $pFriend ) {
		list ( $friendUsername, $friendDomain ) = explode ( '@', $pFriend );
		$this->Protect ( "Friend_PK" );
		$this->Set ( "Owner_FK", $pUserId );
		$this->Set ( "Username", $friendUsername );
		$this->Set ( "Domain", $friendDomain );
		$this->Set ( "Verification", 3 );
		$this->Set ( "Created", NOW() );
		$this->Save();
		
		return ( true );
	}
	
	public function RemoveFriend ( $pUserId, $pFriend ) {
		list ( $friendUsername, $friendDomain ) = explode ( '@', $pFriend );
		
		$this->Retrieve ( array ( "Owner_FK" => $pUserId, "Username" => $friendUsername, "Domain" => $friendDomain ) );
		$this->Fetch();
		
		$this->Delete();
		
		return ( true );
	}
	
	public function SaveApproved ( $pUserId, $pFriend ) {
		list ( $friendUsername, $friendDomain ) = explode ( '@', $pFriend );
		
		$this->Retrieve ( array ( "Owner_FK" => $pUserId, "Username" => $friendUsername, "Domain" => $friendDomain ) );
		$this->Fetch();
		
		$this->Set ( "Owner_FK", $pUserId );
		$this->Set ( "Username", $friendUsername );
		$this->Set ( "Domain", $friendDomain );
		$this->Set ( "Verification", 1 );
		$this->Set ( "Created", NOW() );
		$this->Save();
		
		return ( true );
	}
	
	public function CreateRelationship ( $pFirst, $pSecond ) {
		
		$UserAccounts = new cModel ( 'UserAccounts' );
		
		$UserAccounts->Retrieve ( array ( 'Account_PK' => $pFirst ) );
		$UserAccounts->Fetch();
		$firstUsername = $UserAccounts->Get ( 'Username' );
		
		$UserAccounts->Retrieve ( array ( 'Account_PK' => $pSecond ) );
		$UserAccounts->Fetch();
		$secondUsername = $UserAccounts->Get ( 'Username' );
		
		// Create first record
		$this->Set ( 'Owner_FK', $pFirst );
		$this->Set ( 'Username', $secondUsername );
		$this->Set ( 'Domain', ASD_DOMAIN );
		$this->Set ( 'Verification', 1 );
		$this->Set ( "Created", NOW() );
		
		$this->Save();
		
		// Create second record
		$this->Set ( 'Friend_PK', NULL );
		$this->Set ( 'Owner_FK', $pSecond );
		$this->Set ( 'Username', $firstUsername );
		$this->Set ( 'Domain', ASD_DOMAIN );
		$this->Set ( 'Verification', 1 );
		$this->Set ( "Created", NOW() );
		
		$this->Save();
		
		return ( true );
	}
	
}
