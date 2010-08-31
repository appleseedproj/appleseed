<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Login
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Login Component Controller
 * 
 * Login Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Login
 */
class cLoginTabsController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		
		if ( !$this->Tabs = $this->GetView ( "tabs" ) ) return ( false );
		
		$remote = $this->GetSys ( "Request" )->Get ( "Remote" );
		
		if ( $remote ) {
			$this->Tabs->Find ( "[id=login-remote-tab]", 0)->class = "ui-tabs-selected";
		} else {
			$this->Tabs->Find ( "[id=login-local-tab]", 0)->class = "ui-tabs-selected";
		}
		
		echo $this->Tabs;
		
		return ( true );
	}

}