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

/** Profile Component Contact Controller
 * 
 * Profile Component Contact Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Profile
 */
class cProfileContactController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->View = $this->GetView ( $pView ); 
		
		$focus = $this->Talk ( 'User', 'Focus' );
		$current = $this->Talk ( 'User', 'Current' );
		
		// If the user isn't logged in, or we're viewing our own profile, don't display.
		if ( ( $current->Account == $focus->Account ) or ( !$current ) ) {
			return ( false );
		}
		
		// If the user is already a friend, don't show the Add Friend button.
		
		$this->View->Display();
		
		return ( true );
	}
	
}

