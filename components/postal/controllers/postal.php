<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Postal
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Postal Component Controller
 * 
 * Postal Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Postal
 */
class cPostalPostalController extends cController {
	
	var $_Component = 'postal';
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		$pView = 'user';
		return ( parent::Display ( $pView, $pData ) );
	}
	
}

