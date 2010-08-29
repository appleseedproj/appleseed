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
	private $_MemBegins = array ();
	private $_MemEnds = array ();

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function Start ( $pContext ) {
		$this->_Starts[$pContext] = (float) array_sum(explode(' ',microtime())); 
		
		return ( true );
	}
	
	public function Stop ( $pContext ) {
		$benchmark_begin = $this->_Starts[$pContext];
		$benchmark_end = $this->_Stops[$pContext] = (float) array_sum(explode(' ',microtime())); 
		
		$this->GetSys ( "Logs" )->Add ( "Benchmarks", ( $benchmark_end - $benchmark_begin ), $pContext );
		
		return ( true );
	}	
	
	public function MemBegin ( $pContext ) {
		$this->_MemBegins[$pContext] = memory_get_usage();
		
		return ( true );
	}
	
	public function MemEnd ( $pContext ) {
		$memory_begin = $this->_MemBegins[$pContext];
		$memory_end = $this->_MemEnds[$pContext] = memory_get_usage();
		
		$this->GetSys ( "Logs" )->Add ( "Memory", ( $memory_end - $memory_begin ), $pContext );
		
		return ( true );
	}	
	
}