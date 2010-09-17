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
class cJournals extends cComponent {
	
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
		
		$return[] = array ( 'id' => 'journals', 'title' => 'Journals Tab', 'link' => '/journals/' );
		
		return ( $return );
	} 
	
	
}
