<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Profile
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Profile Component
 * 
 * Profile Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Profile
 */
class cProfile extends cComponent {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function AdminMenu ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$return = array ();
		
		$return[] = array ( 'title' =>"Profile", 'class' => "profile", 'link' => "/admin/profile/" );
		
		return ( $return );
	} 
	
	public function AddToProfileTabs ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$return = array ();
		
		$return[] = array ( 'id' => 'options', 'title' => 'Options Tab', 'link' => '/options/', 'owner' => true );
		$return[] = array ( 'id' => 'info', 'title' => 'Info Tab', 'link' => '/info/' );
		
		return ( $return );
	} 
	
}
