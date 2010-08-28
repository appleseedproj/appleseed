<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Debug
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Debug Component Controller
 * 
 * Debug Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Debug
 */
class cDebugDebugController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		$user = $this->Talk ( "User", "Current" );
		
		$parameters['account'] = $user->Username . '@' . $user->Domain;
		$access = $this->Talk ( "Security", "Access", $parameters );
		
		if ( ( !$user->Username ) or ( !$access->Get ( "Admin" ) ) ) return ( true );
		
		parent::Display( $pView, $pData );
	}

}