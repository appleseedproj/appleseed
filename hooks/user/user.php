<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   User
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** User Hook Class
 * 
 * User Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  User
 */
class cUserHook extends cHook {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function GetCurrentUser ( $pData = null ) {
		$Components = Wob::_("Components" );
		$Components->User->Set ( 'Source', 'Component' );
		$Current = $Components->User->Current();
		return ( $Current );
	}
	
}
