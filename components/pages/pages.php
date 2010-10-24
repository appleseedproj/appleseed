<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Pages
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Pages Component
 * 
 * Pages Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Pages
 */
class cPages extends cComponent {
	
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
		
		$return[] = array ( 'title' =>"Pages", 'class' => "pages", 'link' => "/admin/pages/" );
		
		return ( $return );
	} 
	
	
}
