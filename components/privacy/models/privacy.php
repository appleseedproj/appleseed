<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Privacy
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Privacy Component Privacy Model
 * 
 * Privacy Component Privacy Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Privacy
 */
class cPrivacyModel extends cModel {
	
	protected $_Tablename = 'PrivacySettings';
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function Store ( $pCircle, $pType, $pIdentifier, $pUserId, $pEverybody = false, $pFriends = false ) {
		
		$this->Destroy ( 'Setting_PK' );
		
		$this->Set ( 'User_FK', $pUserId );
		$this->Set ( 'Type', $pType );
		$this->Set ( 'Identifier', $pIdentifier );
		
		if ( $pCircle ) {
			$this->Set ( 'Circle_FK', $pCircle );
		} else {
			$this->Protect ( 'Circle_FK' );
		}
		if ( $pEverybody ) {
			$this->Set ( 'Everybody', (int)true );
		} else {
			$this->Set ( 'Everybody', (int)false );
		}
		if ( $pFriends ) {
			$this->Set ( 'Friends', (int)true );
		} else {
			$this->Set ( 'Friends', (int)false );
		}
		
		$this->Save();
		
		return ( true );
	}
	
	public function RetrieveItem ( $pUserId, $pType, $pIdentifier ) {
		
		$criteria = array ( 'User_FK' => $pUserId, 'Type' => $pType, 'Identifier' => $pIdentifier );
		
		$this->Retrieve ( $criteria );
		
		if ( $this->Get ( 'Total' ) == 0 ) return ( false );
		
		while ( $this->Fetch() ) {
			$data[] = $this->Get ( "Data" );
		}
		
		$return = new stdClass();
		
		if ( count ( $data ) > 1 ) {
			// Return the circle data
			$return->Circles = array();
			$return->Friends = true;
			$return->Everybody = false;
		} else if ( $data[0]['Friends'] ) {
			$return->Circles = array();
			$return->Friends = true;
			$return->Everybody = false;
		} else if ( $data[0]['Everybody'] ) {
			$return->Circles = array();
			$return->Friends = false;
			$return->Everybody = true;
		} else {
			return ( false );
		}
		
		return ( $return );
	}
	
}
