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

/** Benchmark Class
 * 
 * Base class for Performance and Memory Benchmarking
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cBenchmark extends cBase {
	
	private $_Starts = array ();
	private $_Stops = array ();

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
}
