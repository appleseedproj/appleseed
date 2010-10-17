<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Robots
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Robots Component Controller
 * 
 * Robots Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Robots
 */
class cRobotsRobotsController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		header('Content-Type: text/plain');
		
		echo "
User-agent: *

# Disable crawling all user profiles.  
# User will have option to opt in to searches in later versions.
Disallow: /profile/

		";
		
		// Must exist so language and debugging info doesn't get output.
		exit;
	}
	
}
