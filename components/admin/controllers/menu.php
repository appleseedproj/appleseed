<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Admin
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Admin Component Controller
 * 
 * Admin Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Admin
 */
class cAdminMenuController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		
		
		$components = $this->GetSys ( 'Components' );
		$componentList = $components->Get ( 'Config' )->Get ( 'Components' ); 
		
		$this->Menu = $this->GetView ( 'menu' );
		
		$list = $this->Menu->Find ( '[id=admin-main-menu] ul li', 0);
		
		$row = $this->Menu->Copy ( '[id=admin-main-menu] ul li' )->Find ( 'li', 0 );
		
		$list->innertext = '';
		
		$request = ltrim ( rtrim ( $_SERVER['REQUEST_URI'], '/' ), '/' );
		
		foreach ( $componentList as $c => $component ) {
			if ( !$menuItem = $components->Talk ( $component, 'AdminMenu' ) ) continue;
			
			$link = ltrim ( rtrim ( $menuItem['link'], '/' ), '/' );
			
			$row->Find ( 'a span[class=title]', 0 )->innertext = $menuItem['title'];
			$row->Find ( 'a', 0 )->href = $menuItem['link'];
			$row->class = $menuItem['class'];
			
			if ( $request == $link ) { 
				$row->class .= " selected";
			}
			
			$list->innertext .= $row->outertext;
		}
		
		$this->Menu->Display();
		
		return ( true );
	}

}