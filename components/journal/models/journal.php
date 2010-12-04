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
			$this->Set ( 'Created', NOW() );
		} else {
			$this->Retrieve ( array ( 'Owner_FK' => $pUserId, 'Identifier' => $pIdentifier ) );
			if ( $this->Get ( 'Total' ) > 0 ) {
				$this->Fetch();
			} else {
				$this->Set ( 'Created', NOW() );
			}
		}
		
		if ( !$pUserId ) return ( false );
		
		$this->Set ( 'Owner_FK', $pUserId );
		$this->Set ( 'Identifier', $pIdentifier );
		$this->Set ( 'Title', $pTitle );
		$this->Set ( 'Body', $pBody );
		$this->Set ( 'Updated', NOW() );
		
		$this->Save();
		
		return ( $this->Get ( 'Identifier' ) );
	}
	
	public function Entries ( $pUserId, $pLimit ) {
		eval ( GLOBALS );
		
		// $this->Retrieve ( array ( 'Owner_FK' => $pUserId ), 'Created DESC', $pLimit );
		
		$start = $pLimit['start'] ? $pLimit['start'] : 0;
		$limit = $pLimit['limit'] ? $pLimit['limit'] : 10;
		
		// Get a list of circles the current member is a member of.
		$Current = $zApp->GetSys ( 'Components' )->Talk ( 'User', 'Current' );
		$Focus = $zApp->GetSys ( 'Components' )->Talk ( 'User', 'Focus' );
		
		$Circles = $zApp->GetSys ( 'Components' )->Talk ( 'Friends', 'Circles', array ( 'Requesting' => $Current->Account, 'All' => true ) );
		$Friends = $zApp->GetSys ( 'Components' )->Talk ( 'Friends', 'Friends', array ( 'Requesting' => $Current->Account, 'All' => true ) );
		
		$prepared[] = $pUserId;
		
		$this->Privacy = new cModel('PrivacySettings');
		
		if ( $Focus->Account == $Current->Account ) {
			// We're looking at our own journal, so return everything.
		} elseif ( !$Current->Account ) {
			// We're not logged in, so search for Everybody
			$criteria = array ( 'User_FK' => $pUserId, 'Everybody' => true );
			
			$this->Privacy->Retrieve ( $criteria );
			
			// No identifiers were found, which means no entries were found.
			if ( $this->Privacy->Get ( 'Total' ) == 0 ) return ( false );
			
			while ( $this->Privacy->Fetch() ) {
				$Identifiers[] = $this->Privacy->Get ( 'Identifier' );
			}
		} else {
			// We're logged in, so search based on our criteria
			$subcriteria['Everybody'] = true;
			
			if ( in_array ( $Current->Account, $Friends ) ) {
				$subcriteria['||Friends'] = true;
			}
			
			foreach ( $Circles as $c => $circle ) {
				$circleList[] = $c;
			}
			if ( count ( $circleList > 0 ) ) {
				$subcriteria['||Circle_FK'] = '()' . implode ( $circleList );
			}
			$criteria = array ( 'User_FK' => $pUserId, $subcriteria );
			$this->Privacy->Retrieve ( $criteria );
			
			// No identifiers were found, which means no entries were found.
			if ( $this->Privacy->Get ( 'Total' ) == 0 ) return ( false );
			
			while ( $this->Privacy->Fetch() ) {
				$Identifiers[] = $this->Privacy->Get ( 'Identifier' );
			}
		}
		
		if ( $Focus->Account == $Current->Account ) {
			$this->Retrieve ( array ( 'Owner_FK' => $pUserId ), 'Created DESC', $pLimit );
		} else {
			$this->Retrieve ( array ( 'Owner_FK' => $pUserId, 'Identifier' => '()' . implode ( ',', $Identifiers ) ), 'Created DESC', $pLimit );
		}
		
		return ( true );
	}
	
}