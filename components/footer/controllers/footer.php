<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Footer
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Footer Component Controller
 * 
 * Footer Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Footer
 */
class cFooterController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		
		$login = null;
		
		switch ( $login ) {
			default:
				$pView = 'main';
			break;
		}
		
		$this->Form = $this->GetView ( $pView );
		
		$this->Form->Display();
		
		//parent::Display( $pView, $pData );
	}

}