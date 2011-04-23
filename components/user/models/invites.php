<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   User
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** User Component Model
 * 
 * User Component Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  User
 */
class cUserInvitesModel extends cModel {
	
	protected $_Tablename = 'UserInvites';
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function CountInvites ( $pUserId ) {
		$this->Retrieve ( array ( 'Account_FK' => $pUserId, 'Active' => 1 ) );
		return ( $this->Get ( 'Total' ) );
	}
	
	public function Active ( $pAddress, $pUserId ) {
		
		$UserAccounts = new cModel ('UserAccounts');
		$UserAccounts->Structure();
		
		$UserAccounts->Retrieve ( array ( 'Email' => $pAddress ) );
		
		if ( $UserAccounts->Get ( 'Total' ) == 0 ) return ( false );
		
		return ( true );
	}
	
	public function Invited ( $pAddress, $pUserId ) {
		$this->Retrieve ( array ( 'Recipient' => $pAddress, 'Account_FK' => $pUserId, 'Active' => '2' ) );
		if ( $this->Get ( 'Total' ) == 0 ) return ( false );
		
		$this->Fetch();
		
		return ( $this->Get ( 'Value' ) );
	}
	
	public function InviteCode ( $pAddress, $pUserId ) {
		$this->Retrieve ( array ( 'Account_FK' => $pUserId, 'Active' => 1 ) );
		
		if ( $this->Get ( 'Total' ) == 0 ) return ( false );
		
		$this->Fetch();
		
		$this->Protect ( 'Invite_PK' );
		$this->Set ( 'Recipient', $pAddress );
		$this->Set ( 'Active', 2 ); // Pending
		$this->Set ( 'Stamp', NOW() );
		
		if ( $this->Save ( array ( 'Invite_PK' => $this->Get ( 'Invite_PK' ) ) ) ) {
			return ( $this->Get ( 'Value' ) );
		}
		
		
		return ( false );
	}
	
	public function AddInvites ( $pUserId, $pCount ) {
		
		if ( $pCount < 1 ) return ( false );
		
		for ( $i = 0; $i < $pCount; $i++ ) {
			$Value = $this->GetSys ( 'Crypt' )->Identifier ( 32 );
			
			$this->Protect ( 'Invite_PK' );
			$this->Set ( 'Account_FK', $pUserId );
			$this->Set ( 'Value', $Value );
			$this->Set ( 'Active', 1 );
			$this->Set ( 'Recipient', null );
			$this->Set ( 'Stamp', null );
			$this->Create();
		}
		
		return ( true );
	}
	
}
