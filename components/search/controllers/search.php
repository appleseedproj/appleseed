<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Search
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Search Component Controller
 * 
 * Search Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Search
 */
class cSearchSearchController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
		/*
		 * @tutorial 
		 */
		 
		parent::__construct( );
	}
	
	/**
	 * Display the global search box
	 * 
	 * @access  public
	 */
	public function Ask ( $pView = null, $pData = null ) {
		
		if ( !$pView ) $pView = "global";
		
		$this->Ask = $this->GetView ( $pView );
		
		$this->Ask->Display ();
		
		return ( true );
	}
	
}

