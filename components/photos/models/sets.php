<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Photos
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Photos Component Sets Model
 * 
 * Photos Component Sets Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Photos
 */
class cPhotosSetsModel extends cModel {
	
	protected $_Tablename = 'PhotoSets';
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function Load ( $pOwner, $pDirectory = null ) {
		if ( $pDirectory ) {
			if ( is_int ( $pDirectory ) ) {
				$this->Retrieve ( array ( 'Owner_FK' => $pOwner, 'Set_PK' => $pDirectory ), 'Created DESC' );
				$this->Fetch();
			} else {
				$this->Retrieve ( array ( 'Owner_FK' => $pOwner, 'Directory' => $pDirectory ), 'Created DESC' );
				$this->Fetch();
			}
		} else {
			return ( $this->_Sets ( $pOwner ) );
		}
	}

	public function DirectoryExists ( $pSetId, $pOwner ) {
		return ( true );
	}

	private function _Sets ( $pUserId ) {
		eval ( GLOBALS );
		
		// Get a list of circles the current member is a member of.
		$Current = $zApp->GetSys ( 'Components' )->Talk ( 'User', 'Current' );
		$Focus = $zApp->GetSys ( 'Components' )->Talk ( 'User', 'Focus' );
		
		$Circles = $zApp->GetSys ( 'Components' )->Talk ( 'Friends', 'Circles', array ( 'Requesting' => $Current->Account, 'All' => true ) );
		$Friends = $zApp->GetSys ( 'Components' )->Talk ( 'Friends', 'Friends', array ( 'Requesting' => $Current->Account, 'All' => true ) );
		
		$prepared[] = $pUserId;
		
		$this->Privacy = new cModel ( 'PrivacySettings' );
		
		if ( $Focus->Account == $Current->Account ) {
			// We're looking at our own journal, so return everything.
		} elseif ( !$Current->Account ) {
			// We're not logged in, so search for Everybody
			$criteria = array ( 'User_FK' => $pUserId, 'Type' => 'Photosets', 'Everybody' => true );
			
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
			$criteria = array ( 'User_FK' => $pUserId, 'Type' => 'Photosets', $subcriteria );
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
