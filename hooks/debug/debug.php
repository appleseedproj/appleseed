<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Debug Hook Class
 * 
 * Debug Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cDebugHook extends cHook {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function OnSystemEnd ( $pData = null ) {
		
		$this->GetSys ( "Components" )->Execute ( "debug" );
        
        return ( true );
	}
	
}
