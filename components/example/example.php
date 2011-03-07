<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Example
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Example Component
 * 
 * Example Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Example
 */
class cExample extends cComponent {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
		parent::__construct();
	}
	
	public function GetResponse ( $pData = null ) {
		$return = array();
		$return['value'] = 'This is a response from the Example component.';
		
		return ( $return );
	}
	
	public function PutResponse ( $pData = null ) {
		$return = array();
		$return['value'] = 'This is a put response from the Example component.';
		
		return ( $return );
	}
	
}
