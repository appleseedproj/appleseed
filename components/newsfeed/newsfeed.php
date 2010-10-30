<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Newsfeed
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Newsfeed Component
 * 
 * Newsfeed Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Newsfeed
 */
class cNewsfeed extends cComponent {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function AddToProfileTabs ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$return = array ();
		
		$return[] = array ( 'id' => 'newsfeed', 'title' => 'News Tab', 'link' => '/news/', 'owner' => true );
		
		return ( $return );
	} 
	
	public function Notify ( $pData = null ) {
		$this->Load ( 'Newsfeed', null, 'Notify', $pData );
	}
	
}
