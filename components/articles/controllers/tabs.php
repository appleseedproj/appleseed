<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Tabs
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Articles Component Controller
 * 
 * Articles Component Tabs Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Articles
 */
class cArticlesTabsController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->_Current = $this->Talk ( 'User', 'Current' );
		$parameters['account'] = $this->_Current->Account;
		$access = $this->Talk ( "Security", "Access", $parameters );
		
		if ( ( $this->_Current ) && ( $access->Get ( "Admin" ) ) ) {
			$this->View = $this->GetView ( "tabs.admin" );
		} else if ( $this->_Current ) {
			$this->View = $this->GetView ( "tabs.current" );
		} else {
			$this->View = $this->GetView ( "tabs" );
		}
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		$parts = explode ( $this->GetSys ( "Router" )->Get ( "Base" ), $_SERVER['REQUEST_URI'] );
		
		$concern = $parts[1];
		
		$this->View->Find ( "[class=articles-read-tab]", 0 )->class .= " selected ";
		
	}
	
}