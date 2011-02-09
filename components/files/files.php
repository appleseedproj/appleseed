<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Files
 * @copyright    Copyright (C) 2004 - 2011 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Files Component
 * 
 * Files Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Files
 */
class cFiles extends cComponent {
	
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
		
		$return[] = array ( 'id' => 'files', 'title' => 'Files Tab', 'link' => '/files/', 'owner' => false );
		
		return ( $return );
	} 

	public function GetFile ( $pData = null ) {
		$return = array ();

		$return['one'] = "A";
		$return['two'] = "B";
		$return['three'] = array ( "C", "D" );

		return ( $return );
	}
	
}
