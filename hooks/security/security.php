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

/** Security Hook Class
 * 
 * Security Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cSecurityHook extends cHook {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	/*
	 * If a page is restricted, show a 404
	 * 
	 */
	public function OnSystemRestricted ( $pData = null ) {
		
		$restriction = ucwords ( strtolower ( $pData['restriction'] ) );
		
		$access = $this->GetSys ( 'Components' )->Talk ( 'Security', 'Access' );
		
		if ( $access->Get ( $restriction ) ) return ( true );
		
		$session = $this->GetSys ( 'Session' );
		
		$session->Context ( 'login.login.3.login' );
		
		$session->Set ( 'Message', __( 'Access Denied' ) );
		$session->Set ( 'Error', true );
		
		$this->GetSys ( 'Foundation' )->Load ( 'common/403.php' );
		
		return ( false );
	}
	
}
