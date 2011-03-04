<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2011 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Client Class
 * 
 * Base class for Client
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cClient extends cBase {
	
	protected $_Config;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
 		// Load language configuration.
 		$this->_Config = new cConf ();
		$this->_Config->Set ( "Data",  $this->_Config->Load ( "client" ) );
		
		return ( true );
	}
	
}
