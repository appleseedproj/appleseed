<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Profile
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Profile Component Info Controller
 * 
 * Profile Component Info Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Profile
 */
class cProfileInfoController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->View = $this->GetView ( $pView ); 
		
		$focus = $this->Talk ( 'User', 'Focus' );
		
		if ( $focus->Description ) {
			$this->View->Find ( "p", 0 )->innertext = $focus->Description;
		} else {
			$this->View->Find ( "p", 0 )->innertext = __( "No User Information For", array ( "fullname" => $focus->Fullname ) );
		}
		
		$this->View->Display();
		
		return ( true );
	}
	
}