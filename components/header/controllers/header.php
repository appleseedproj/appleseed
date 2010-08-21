<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Header
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Header Component Controller
 * 
 * Header Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Header
 */
class cHeaderHeaderController extends cController {
	
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
		
		$pView = 'main';
		
		if ( $user->Remote ) $pView = 'remote';
		else if ( $user->Username ) $pView = 'local';
		
		if ( ( $user->Username ) && ( $user->Admin ) ) $pView .= '.admin';
		
		$header = $this->GetView ( $pView );
		
		$link = $header->Find ( "[id=user_login_profile_link]", 0 );
		
		$logged_in_text = $link->innertext;
		$link->innertext = __ ($logged_in_text, array ( "username" => $user->Username, "domain" => $user->Domain ) );
		
		$header->Display();
		
		return ( true );
	}

}