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
		$this->_PrepareWarnings ();
		
		// Queries
		$this->_PrepareQueries ();
		
		// Memory
		$this->_PrepareMemory ();
		
		// Benchmarks
		$this->_PrepareBenchmarks ();
		
		echo $this->Debug; 
		
		return ( false );
	}
	
	private function _PrepareWarnings ( ) {
		
		$warnings = $this->GetSys ( "Logs" )->GetLogs ( "Warnings" );
		
		list ( $dir, $null ) = explode ( 'components', __FILE__ );
		
		$count = count ( $warnings );
		
		$this->Debug->Find ( "[id=warnings-system-total]", 0)->innertext = __ ("System Total Warnings", array ( "count" => $count ) );
		
		$tbody = $this->Debug->Find ( "[id=debug-warnings] table tbody tr", 0);
		
		$row = $this->Debug->Copy ( "[id=debug-warnings] table tbody tr" )->Find ( "tr", 0 );
		
		$tbody->innertext = " ";

		$debugWarningsId = $row->Find( "[class=debug-warnings-id]", 0 );
		$debugWarningsWarning = $row->Find( "[class=debug-warnings-warning]", 0 );
		$debugWarningsFile = $row->Find( "[class=debug-warnings-file]", 0 );
		$debugWarningsLine = $row->Find( "[class=debug-warnings-line]", 0 );
		
		foreach ( $warnings as $w => $warning ) {
		    $oddEven = empty($oddEven) || $oddEven == 'even' ? 'odd' : 'even';
			
			$row->class = $oddEven;
			
			$text = $warning->Value;
			$context = $warning->Context;
			
			list ( $null, $context ) = explode ( $dir, $context );
			
			list ( $file, $line ) = explode ( ':', $context );
			
			$debugWarningsId->innertext = $w;
			$debugWarningsWarning->innertext = $text;
			$debugWarningsFile->innertext = $file;
			$debugWarningsLine->innertext = $line;
			
			$tbody->innertext .= $row->outertext;
		}
		
		return ( true );
		
	}
	private function _PrepareQueries ( ) {
		
		$queries = $this->GetSys ( "Logs" )->GetLogs ( "Queries" );
		
		$count = count ( $queries );
		
		$this->Debug->Find ( "[id=queries-system-total]", 0)->innertext = __ ("System Total Queries", array ( "count" => $count ) );
		
		$tbody = $this->Debug->Find ( "[id=debug-queries] table tbody tr", 0);
		
		$row = $this->Debug->Copy ( "[id=debug-queries] table tbody tr" )->Find ( "tr", 0 );
		
		$tbody->innertext = " ";

		$debugQueriesId = $row->Find( "[class=debug-queries-id]", 0 );
		$debugQueriesClass = $row->Find( "[class=debug-queries-class]", 0 );
		$debugQueriesTable = $row->Find( "[class=debug-queries-table]", 0 );
		$debugQueriesQuery = $row->Find( "[class=debug-queries-query]", 0 );
			
		foreach ( $queries as $q => $query ) {
		    $oddEven = empty($oddEven) || $oddEven == 'even' ? 'odd' : 'even';
			
			$row->class = $oddEven;
			
			$statement = $query->Value;
			$context = $query->Context;
			
			list ( $class, $table ) = explode ('.', $context );
			
			$debugQueriesId->innertext = $q;
			$debugQueriesClass->innertext = $class;
			$debugQueriesTable->innertext = $table;
			$debugQueriesQuery->innertext = $statement;
			
			$tbody->innertext .= $row->outertext;
		
		}
		
		return ( true );
	}
	
	private function _PrepareMemory ( ) {
		
		$memories = $this->GetSys ( "Logs" )->GetLogs ( "Memory" );
		
		// First get the system memory amount
		foreach ( $memories as $m => $memory ) {
			if ($memory->Context == "_system" ) {
				$total = $memory->Value;
				$memory = sprintf("%2.2f", ( $total / 1024 / 1024 ) );
				unset ( $memories[$m]);
			}
		}
		
		$this->Debug->Find ( "[id=memory-system-total]", 0)->innertext = __ ("System Total Memory", array ( "memory" => $memory ) );
		
		$tbody = $this->Debug->Find ( "[id=debug-memory] table tbody tr", 0);
		
		$row = $this->Debug->Copy ( "[id=debug-memory] table tbody tr" )->Find ( "tr", 0 );
		
		$tbody->innertext = " ";

		$debugMemoryId = $row->Find( "[class=debug-memory-id]", 0 );
		$debugMemoryController = $row->Find( "[class=debug-memory-controller]", 0 );
		$debugMemoryComponent = $row->Find( "[class=debug-memory-component]", 0 );
		$debugMemoryInstance = $row->Find( "[class=debug-memory-instance]", 0 );
		$debugMemoryView = $row->Find( "[class=debug-memory-view]", 0 );
		
		foreach ( $memories as $l => $log ) {
		    $oddEven = empty($oddEven) || $oddEven == 'even' ? 'odd' : 'even';
			
			$row->class = $oddEven;
			
			$value = $log->Value;
			$context = $log->Context;
			
			list ( $controller, $component, $instance, $view ) = explode ('.', $context );
			
			$debugMemoryId->innertext = $l;
			$debugMemoryController->innertext = $controller;
			$debugMemoryComponent->innertext = $component;
			$debugMemoryInstance->innertext = $instance;
			$debugMemoryView->innertext = $view;
			
			$memory = sprintf("%2.2f", ( $value / 1024 / 1024 ) );
			$row->Find( "[class=debug-memory-amount]", 0 )->innertext = __("Memory In Megabytes", array ( "memory" => $memory ) );
			
			$tbody->innertext .= $row->outertext;
		
		}
		
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
		
		$tbody = $this->Debug->Find ( "[id=debug-benchmarks] table tbody tr", 0);
		
		$row = $this->Debug->Copy ( "[id=debug-benchmarks] table tbody tr" )->Find ( "tr", 0 );
		
		$tbody->innertext = " ";

		$debugBenchmarkId = $row->Find( "[class=debug-benchmark-id]", 0 );
		$debugBenchmarkController = $row->Find( "[class=debug-benchmark-controller]", 0 );
		$debugBenchmarkComponent = $row->Find( "[class=debug-benchmark-component]", 0 );
		$debugBenchmarkInstance = $row->Find( "[class=debug-benchmark-instance]", 0 );
		$debugBenchmarkView = $row->Find( "[class=debug-benchmark-view]", 0 );
		$debugBenchmarkTime = $row->Find( "[class=debug-benchmark-time]", 0 );
			
		foreach ( $benchmarks as $l => $log ) {
		    $oddEven = empty($oddEven) || $oddEven == 'even' ? 'odd' : 'even';
			
			$row->class = $oddEven;
			
			$value = $log->Value;
			$context = $log->Context;
			$seconds = sprintf("%.4f", ($value));
			
			list ( $controller, $component, $instance, $view ) = explode ('.', $context );
			
			$debugBenchmarkId->innertext = $l;
			$debugBenchmarkController->innertext = $controller;
			$debugBenchmarkComponent->innertext = $component;
			$debugBenchmarkInstance->innertext = $instance;
			$debugBenchmarkView->innertext = $view;
			$debugBenchmarkTime->innertext = __("Benchmark In Seconds", array ( "seconds" => $seconds ) );
			
			$tbody->innertext .= $row->outertext;
		}
		
		return ( true );
	}

}