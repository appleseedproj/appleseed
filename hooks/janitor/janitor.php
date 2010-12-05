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

/** Janitor Hook Class
 * 
 * Janitor Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cJanitorHook extends cHook {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function EndPageShare ( $pData = null ) {
		$this->_Janitorial();
	    return ( true );
	}
	
	public function EndJournalSave ( $pData ) {
		$this->_Janitorial();
		return ( true );
	}
	
	public function EndFriendsApprove ( $pData ) {
		$this->_Janitorial();
		return ( true );
	}
	
	public function EndSystemInitialize ( $pData ) {
		$social = $this->GetSys ( 'Request' )->Get ( '_social' );
		
		if ( $social != 'true' ) return ( false );
		
		$task = $this->GetSys ( 'Request' )->Get ( '_task' );
		
		if ( $task != 'friend.approve' ) return ( false );
		
		$this->_Janitorial();
		
		return ( true );
	}
	
	private function _Janitorial ( ) {
		// create both cURL resources
		$ch1 = curl_init();

		// set URL and other appropriate options
		$url = 'http://' . ASD_DOMAIN . '/janitor/';
		
		curl_setopt($ch1, CURLOPT_URL, $url);
		curl_setopt($ch1, CURLOPT_HEADER, 0);
		curl_setopt($ch1, CURLOPT_TIMEOUT, 1);
		
	    curl_exec($ch1);
	    
	    curl_close ( $ch1 );
	    
	    return ( true );
	}
	
}
