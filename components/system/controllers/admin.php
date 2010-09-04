<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** System Component Controller
 * 
 * System Component Admin Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  System
 */
class cSystemAdminController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		switch ( $pView ) {
			case 'admin.tabs':
				$view = $this->_DisplayTabs ( $pView, $pData );
			break;
			default:
				return ( false );
			break;
		}
		
		$view->Display();
		
		return ( true );
	}
	
	private function _DisplayTabs ( $pView = null, $pData = array ( ) ) {
		
		$request = ltrim ( rtrim ( $_SERVER['REQUEST_URI'], '/' ), '/' );
		$requestParts = explode ( "/", $request );
		
		// Grab only the first three parts of the url.
		foreach ( $requestParts as $r => $part ) {
			if ( $r > 2 ) break;
			$finalRequest[] = $part;
		}
		
		$link = implode ( "/", $finalRequest );
		
		$this->Tabs = $this->GetView ( $pView );
		
		switch ( $link ) {
			case 'admin/system':
			case 'admin/system/config':
				$this->Tabs->Find ( "[id=system-config-tab]", 0 )->class = "ui-tabs-selected";
			break;
			case 'admin/system/nodes':
				$this->Tabs->Find ( "[id=system-nodes-tab]", 0 )->class = "ui-tabs-selected";
			break;
			case 'admin/system/logs':
				$this->Tabs->Find ( "[id=system-logs-tab]", 0 )->class = "ui-tabs-selected";
			break;
			case 'admin/system/maintenance':
				$this->Tabs->Find ( "[id=system-maintenance-tab]", 0 )->class = "ui-tabs-selected";
			break;
			case 'admin/system/update':
				$this->Tabs->Find ( "[id=system-update-tab]", 0 )->class = "ui-tabs-selected";
			break;
			break;
		}
		
		return ( $this->Tabs );
	}
	
}