<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Import
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Import Component
 * 
 * Import Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Import
 */
class cImport extends cComponent {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function RegisterOptionsArea ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$return = array ();
		
		$return[] = array ( 'title' =>"Services", 'class' => "services", 'link' => "/profile/(.*)/options/services/" );
		
		return ( $return );
	}
	
}
