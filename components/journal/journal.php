<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Journals
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Journals Component
 * 
 * Journals Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Journals
 */
class cJournal extends cComponent {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function AddToProfileTabs ( $pData = null ) {
		
		$return = array ();
		
		$return[] = array ( 'id' => 'journal', 'title' => 'Journal Tab', 'link' => '/journal/' );
		
		return ( $return );
	} 
	
	public function IdentifierExists ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$Identifier = $pData['Identifier'];
		
		$Model = new cModel ( 'JournalEntries' );
		
		$Model->Retrieve ( array ( 'Identifier' => $Identifier ) );
		
		if ( $Model->Get ( 'Total' ) > 0 ) {
			return ( true );
		}
		
		return ( false );
	}
	
} 