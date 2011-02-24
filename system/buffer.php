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
	
	protected $_Buffer;
	
	protected $_Queue;
	
	protected $_Count;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
		$this->_Count = array ();
		$this->_Queue = array ();
		
	}
	
	/**
	 * Buffer and load a foundation
	 *
	 * @access  public
	 * @param string $pFoundation Full path to foundation file
	 */
	public function LoadFoundation ( $pFoundation ) {
		eval ( GLOBALS );
		
		ob_start ();
		
		require ( $pFoundation );
		
		$buffer = ob_get_contents ();
		ob_end_clean ();
		
		$replacement = "#@ head @#";
		preg_match("/<head.*>(.*)<\/head>/smU", $buffer, $headData);
		
		$newbuffer = preg_replace("/<head.*>(.*)<\/head>/smU", $replacement, $buffer);
		if ( $newbuffer ) $buffer = $newbuffer;
		
		$this->_Queue['head'] = $headData[0];
		
		$this->_Buffer = $buffer;
		
		return ( true );
	}
	
	/**
	 * Get a private queue entry
	 *
	 * @access  public
	 */
	public function GetQueue ( $pEntry ) {
		return ( $this->_Queue[$pEntry] );
	}
	
	/**
	 * Set the private queue entry
	 *
	 * @access  public
	 */
	public function SetQueue ( $pEntry, $pValue ) {
		$this->_Queue[$pEntry] = $pValue;
		
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
	 * Set the private buffer value
	 *
	 * @access  public
	 */
	public function SetBuffer ( $pBuffer ) {
		$this->_Buffer = $pBuffer;
		
		return ( true );
	}
	
	/**
	 * Process the buffer, merging the queue.
	 *
	 * @access  public
	 */
	public function Process ( ) {
		
		$this->GetSys ( "Event" )->Trigger ( "Begin", "System", "Buffer" );
		
		// Check if we've been instructed to redirect and process a different foundation.
		if ( $redirect = $this->GetSys ( "Foundation" )->Get ( "Redirect" ) ) {
			//unset ( $this->_Buffer );
			//unset ( $this->_Queue );
			$this->LoadFoundation ( $redirect );
		}
		
		$processed = $this->_Buffer;
		
		$whilepattern = "/\#\@ component(.*) \@\#/";
		
		do {
			foreach ( $this->_Queue['component'] as $q => $queue ) {
				$pattern = "/\#\@ component$q \[(.*)\] \@\#/";
			
				// Manually escape dollar signs so that they aren't processed.
				$queue->Buffer = preg_replace("!" . '\x24' . "!" , '\\\$' , $queue->Buffer);
				
				$processed = preg_replace ( $pattern, $queue->Buffer, $processed );
			}
		} while ( preg_match ( $whilepattern, $processed ) );
		
		$pattern = "/\#\@ head \@\#/";
		$processed = preg_replace ( $pattern, $this->_Queue['head'], $processed );
		
		$this->GetSys ( "Event" )->Trigger ( "End", "System", "Buffer" );
		
		$this->_Buffer = $processed;
		
		return ( $this->_Buffer );
	}
	
	/**
	 * Add to the buffer counter
	 *
	 * @access  public
	 * @param string $pContext Which context to add to (ie, "component")
	 */
	public function AddToCount ( $pContext ) {
		if ( !isset ( $this->_Count[$pContext] ) )
			$this->_Count[$pContext] = 0;

		$this->_Count[$pContext]++;
		
		return ( true );
	}
	
	/**
	 * Add a buffer segment to the queue
	 *
	 * @access  public
	 * @param string $pContext Which context to add to (ie, "component")
	 * @param string $pData Data for how this component was called
	 * @param array $pData Buffer segment value
	 */
	public function Queue ( $pContext, $pData, $pBuffer ) {
		$count = $this->_Count[$pContext];
		
		if ( !isset ( $this->_Queue[$pContext][$count]->Parameters ) ) 
			$this->_Queue[$pContext][$count] = new stdClass();
				
		$this->_Queue[$pContext][$count]->Parameters = $pData;
		$this->_Queue[$pContext][$count]->Buffer = $pBuffer;
		
		return ( true );
	}
	
	/**
	 * Create a placeholder in the outermost buffer.
	 *
	 * @access  public
	 * @param string $pType Which buffer type to add to (ie, "component")
	 * @param array $pContext Context for how this component was called
	 */
	public function PlaceHolder ( $pType, $pContext ) {
		
		$info = '[' . $pContext . ']';
		
		$count = $this->_Count[$pType];
		echo "#@ component$count $info @#\n"; 
		
		return ( true );
	}

}
