<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Login
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Login Component Logout Controller
 * 
 * Login Component Logout Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Login
 */
class cLoginLogoutController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Logout ( $pView = null, $pData = array ( ) ) {
		
		// Trigger the logout event
		$return = $this->GetSys ( "Event" )->Trigger ( "On", "Login", "Logout" );
		
		return ( parent::Display ( $pView, $pData ) );
	}
	
}