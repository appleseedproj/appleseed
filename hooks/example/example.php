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

/** Example Hook Class
 * 
 * Example Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cExampleExampleHook extends cHook {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function BeginExampleDisplay ( $pData = null ) {
		
		//echo "<h2>" . __("Start Hook") . "</h2>";
		
		return ( true );
	}
	
	public function EndExampleDisplay ( $pData = null ) {
		// echo "<h3>" . __("End Hook") . "</h3>";
		
		return ( true );
	}
	
}
