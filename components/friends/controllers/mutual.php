<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Friends
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Friends Component Mutual Controller
 * 
 * Friends Component Mutual Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Friends
 */
class cFriendsMutualController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$current = $this->Talk ( 'User', 'Current' );
		
		$focus = $this->Talk ( 'User', 'Focus' );
		
		// If user isn't logged in, or we're viewing our own page, then don't display.
		if ( ( $focus->Username == $current->Username ) or ( !$current->Username ) ) {
			return ( true );
		}
		
		$current = $this->Talk ( 'User', 'Current' );
		
		$this->View = $this->GetView ( $pView ); 
		
		$this->View->Display();
		
		return ( true );
	}
	
}
