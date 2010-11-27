<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Journal
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Journal Component Model
 * 
 * Journal Component Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Journal
 */
class cJournalModel extends cModel {
	
	protected $_Tablename = "JournalEntries";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function Load ( $pUserId, $pEntry ) {
		
		$url = str_replace ( '-', ' ', $pEntry );
		
		$criteria = array ( 'Owner_FK' => $pUserId, array ( 'Identifier' => $pEntry, '||Title' => $url ) );
		
		$this->Retrieve ( $criteria, 'Created DESC' );
		
		if ( $this->Get ( 'Total' ) == 0 ) return ( false );
		
		$this->Fetch();
		
		return ( true );
	}
	
	public function Store ( $pUserId, $pIdentifier, $pTitle, $pBody ) {
		
		if ( !$pIdentifier ) {
			$pIdentifier = $this->CreateUniqueIdentifier();
		}
		
		if ( !$pUserId ) return ( false );
		
		$this->Set ( 'Owner_FK', $pUserId );
		$this->Set ( 'Identifier', $pIdentifier );
		$this->Set ( 'Title', $pTitle );
		$this->Set ( 'Body', $pBody );
		$this->Set ( 'Created', NOW() );
		$this->Set ( 'Updated', NOW() );
		
		$this->Save();
		
		return ( $this->Get ( 'Identifier' ) );
	}
	
	public function Entries ( $pUserId, $pLimit ) {
		
		$this->Retrieve ( array ( 'Owner_FK' => $pUserId ) );
		
	}
	
}