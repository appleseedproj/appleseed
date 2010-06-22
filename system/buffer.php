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

/** Buffering Class
 * 
 * Tools for page buffering and manipulation.
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cBuffer extends cBase {
	
	var $_Buffer;
	var $_Queue;
	var $_Count;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
		$this->_Count = array ();
		
	}
	
	/**
	 * Buffer and load a foundation
	 *
	 * @access  public
	 * @var string $pFoundation Full path to foundation file
	 */
	public function LoadFoundation ( $pFoundation ) {
		eval ( GLOBALS );
		
		ob_start ();
		
		require_once ( $pFoundation );
		
		$buffer = ob_get_contents ();
		ob_end_clean ();
		
		$this->_Buffer = $buffer;
		
		return ( true );
	}
	
	/**
	 * Get the private buffer value
	 *
	 * @access  public
	 */
	public function GetBuffer ( ) {
		return ( $this->_Buffer );
	}
	
	/**
	 * Process the buffer, merging the queue.
	 *
	 * @access  public
	 */
	public function Process ( ) {
		
		// print_r ($this->_Queue); 
		//exit;
		
		$processed = $this->_Buffer;
		
		foreach ( $this->_Queue['component'] as $q => $queue ) {
			$pattern = "/\#\@ component$q \[(.*)\] \@\#/";
			
			$processed = preg_replace ( $pattern, $queue->Buffer, $processed );
		}
		
		return ( $processed );
	}
	
	/**
	 * Add to the buffer counter
	 *
	 * @access  public
	 * @var string $pContext Which context to add to (ie, "component")
	 */
	public function AddToCount ( $pContext ) {
		$this->_Count[$pContext]++;
		
		return ( true );
	}
	
	/**
	 * Add a buffer segment to the queue
	 *
	 * @access  public
	 * @var string $pContext Which context to add to (ie, "component")
	 * @var string $pData Data for how this component was called
	 * @var array $pData Buffer segment value
	 */
	public function Queue ( $pContext, $pData, $pBuffer ) {
		$count = $this->_Count[$pContext];
		$this->_Queue[$pContext][$count]->Parameters = $pData;
		$this->_Queue[$pContext][$count]->Buffer = $pBuffer;
		
	}
	
	/**
	 * Create a placeholder in the outermost buffer.
	 *
	 * @access  public
	 * @var string $pContext Which context to add to (ie, "component")
	 * @var array $pData Data for how this component was called
	 */
	public function PlaceHolder ( $pContext, $pData ) {
		
		foreach ($pData as $d => $data ) {
			if ( is_array ( $data ) ) { 
				$pData[$d] = join ( ',', $data );
			}
		}
		
		$info = '[' . join ('/', $pData ) . ']';
		
		$count = $this->_Count[$pContext];
		echo "#@ component$count $info @#\n"; 
		
		return ( true );
	}

}
