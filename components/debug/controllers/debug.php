<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Debug
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Debug Component Controller
 * 
 * Debug Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Debug
 */
class cDebugDebugController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		$user = $this->Talk ( "User", "Current" );
		
		$parameters['account'] = $user->Username . '@' . $user->Domain;
		$access = $this->Talk ( "Security", "Access", $parameters );
		
		if ( ( !$user->Username ) or ( !$access->Get ( "Admin" ) ) ) return ( true );
		
		$logs = $this->GetSys ( "Logs ");
		
		$this->Debug = $this->GetView ( $pView );
		
		// Warnings
		
		// Queries
		
		// Memory
		$this->_PrepareMemory ();
		
		// Benchmarks
		$this->_PrepareBenchmarks ();
		
		$this->Debug->Display(); 
		
		return ( false );
	}
	
	private function _PrepareMemory ( ) {
		
		$benchmarks = $this->GetSys ( "Logs" )->GetLogs ( "Memory" );
		
		// First get the system benchmark
		foreach ( $benchmarks as $b => $benchmark ) {
			if ($benchmark->Context == "_system" ) {
				$total = $benchmark->Value;
				$memory = sprintf("%2.2f", ( $total / 1024 / 1024 ) );
				unset ( $benchmarks[$b]);
			}
		}
		$this->Debug->Find ( "[id=memory-system-total]", 0)->innertext = __ ("System Total Memory", array ( "memory" => $memory ) );
		
		$tbody = $this->Debug->Find ( "[id=debug-memory] table tbody", 0);
		
		$row = $tbody->Find ( "tr", 0);
		
		foreach ( $benchmarks as $l => $log ) {
		    $oddEven = empty($oddEven) || $oddEven == 'even' ? 'odd' : 'even';
			
			$row->class = $oddEven;
			
			$value = $log->Value;
			$context = $log->Context;
			
			list ( $controller, $component, $instance, $view ) = explode ('.', $context );
			
			$row->Find( "[class=debug-memory-id]", 0 )->innertext = $l;
			$row->Find( "[class=debug-memory-controller]", 0 )->innertext = $controller;
			$row->Find( "[class=debug-memory-component]", 0 )->innertext = $component;
			$row->Find( "[class=debug-memory-instance]", 0 )->innertext = $instance;
			$row->Find( "[class=debug-memory-view]", 0 )->innertext = $view;
			$memory = sprintf("%2.2f", ( $value / 1024 / 1024 ) );
			$row->Find( "[class=debug-memory-amount]", 0 )->innertext = __("Memory In Megabytes", array ( "memory" => $memory ) );
			
			$this->Debug->Find ( "[id=debug-memory] table tbody", 0)->innertext .= $row->outertext;
		
		}
		
		$this->Debug->Reload();
		
		$this->Debug->RemoveElement ( "[id=debug-memory] tr" );
		
		return ( true );
	}
	private function _PrepareBenchmarks ( ) {
		
		$benchmarks = $this->GetSys ( "Logs" )->GetLogs ( "Benchmarks" );
		
		// First get the system benchmark
		foreach ( $benchmarks as $b => $benchmark ) {
			if ($benchmark->Context == "_system" ) {
				$total = $benchmark->Value;
				$seconds = sprintf("%.4f", ($total));
				unset ( $benchmarks[$b]);
			}
		}
		$this->Debug->Find ( "[id=benchmarks-system-total]", 0)->innertext = __ ("System Total Time", array ( "seconds" => $seconds ) );
		
		$tbody = $this->Debug->Find ( "[id=debug-benchmarks] table tbody", 0);
		
		$row = $tbody->Find ( "tr", 0);
		
		foreach ( $benchmarks as $l => $log ) {
		    $oddEven = empty($oddEven) || $oddEven == 'even' ? 'odd' : 'even';
			
			$row->class = $oddEven;
			
			$value = $log->Value;
			$context = $log->Context;
			
			list ( $controller, $component, $instance, $view ) = explode ('.', $context );
			
			$row->Find( "[class=debug-benchmark-id]", 0 )->innertext = $l;
			$row->Find( "[class=debug-benchmark-controller]", 0 )->innertext = $controller;
			$row->Find( "[class=debug-benchmark-component]", 0 )->innertext = $component;
			$row->Find( "[class=debug-benchmark-instance]", 0 )->innertext = $instance;
			$row->Find( "[class=debug-benchmark-view]", 0 )->innertext = $view;
			$seconds = sprintf("%.4f", ($value));
			$row->Find( "[class=debug-benchmark-time]", 0 )->innertext = __("Benchmark In Seconds", array ( "seconds" => $seconds ) );
			
			$this->Debug->Find ( "[id=debug-benchmarks] table tbody", 0)->innertext .= $row->outertext;
		
		}
		
		$this->Debug->Reload();
		
		$this->Debug->RemoveElement ( "[id=debug-benchmarks] tr" );
		
		return ( true );
	}

}