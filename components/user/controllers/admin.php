<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   User
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** User Component Controller
 * 
 * User Component Admin Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  User
 */
class cUserAdminController extends cController {
	
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
				$view = $this->GetView ( $pView );
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
			case 'admin/users':
			case 'admin/users/config':
				$this->Tabs->Find ( "[id=user-config-tab]", 0 )->class = "ui-tabs-selected";
			break;
			case 'admin/users/accounts':
				$this->Tabs->Find ( "[id=user-accounts-tab]", 0 )->class = "ui-tabs-selected";
			break;
			case 'admin/users/access':
				$this->Tabs->Find ( "[id=user-access-tab]", 0 )->class = "ui-tabs-selected";
			break;
			break;
		}
		
		return ( $this->Tabs );
	}
	
}