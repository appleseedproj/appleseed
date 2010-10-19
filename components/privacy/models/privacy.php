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
	
}
