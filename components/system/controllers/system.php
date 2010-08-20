<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** System Component Controller
 * 
 * System Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  System
 */
class cSystemSystemController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		
		$login = null;
		
		switch ( $login ) {
			default:
				$pView = 'message';
			break;
		}
		
		parent::Display( $pView, $pData );
		return ( true );
	}

}