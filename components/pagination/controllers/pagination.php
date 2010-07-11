<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Pagination
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Pagination Component Controller
 * 
 * Pagination Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Pagination
 */
class cPaginationController extends cController {
	
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
	 * Display the default view
	 * 
	 * @access  public
	 */
	public function Display ( $pView = null, $pData = null ) {
		
		$this->List = $this->GetView ( "pagination" );
		
		$this->List->Display ();
		
		return ( true );
	}
}

