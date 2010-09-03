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

/** Photos Component
 * 
 * Photos Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Photos
 */
class cPhotos extends cComponent {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function AdminMenu ( $pData = null ) {
		
		$return = array ();
		
		$return[] = array ( 'title' =>"Photos", 'class' => "photos", 'link' => "/admin/photos/" );
		
		return ( $return );
	} 
	
	
}
