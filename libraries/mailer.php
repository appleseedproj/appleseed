<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   Library
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

require ( ASD_PATH . DS . 'libraries' . DS . 'external' . DS . 'Swift-4.0.6' . DS . 'swift_required.php' );

/** Mailer Class
 * 
 * Mailer and access management.
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cMailer {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		$this->_Instance = Swift_Message::newInstance();
		
	}

}
