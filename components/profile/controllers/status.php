<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Photos
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Profile Component Profile Controller
 * 
 * Profile Component Profile Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Profile
 */
class cProfileStatusController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->Status = $this->GetView ( $pView ); 
		
		$focus = $this->Talk ( 'User', 'Focus' );
		
		$this->Status->Find ( '[id=status-name]', 0 )->innertext = $focus->Fullname;
		
		$this->Status->Display();
		
		return ( true );
	}
	
}
