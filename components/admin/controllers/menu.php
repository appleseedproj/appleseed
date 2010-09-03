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
		
		$list = $this->Menu->Find ( '[id=admin-main-menu] ul', 0);
		
		$row = $this->Menu->Copy ( '[id=admin-main-menu] ul li' )->Find ( 'li', 0 );
		
		$list->innertext = "";
		
		$config = $this->Get ( "Config" );
		
		$ordering = explode ( ' ', $config['menu_ordering'] );
		
		$request = ltrim ( rtrim ( $_SERVER['REQUEST_URI'], '/' ), '/' );
		
		foreach ( $componentList as $c => $component ) {
			if ( !$menuItems = $components->Talk ( $component, 'AdminMenu' ) ) continue;
			
			foreach ( $menuItems as $m => $menu ) {
				$link = ltrim ( rtrim ( $menu['link'], '/' ), '/' );
			
				$row->Find ( 'a span[class=title]', 0 )->innertext = $menu['title'];
				$row->Find ( 'a', 0 )->href = $menu['link'];
				$row->class = $menu['class'];
				
				if ( $request == $link ) { 
					$row->class .= " selected";
				}
				
				$title = strtolower ( $menu['title'] );
				
				$rows[$title] = $row->outertext;
				
			}
		}
		
		$final = array ();
		
		foreach ( $ordering as $order ) {
			$order = strtolower ( $order );
			if ( !isset ( $rows[$order] ) ) continue;
			$final[] = $rows[$order];
			unset ( $rows[$order] );
		}
		
		foreach ( $rows as $row ) {
			$final[] = $row;
		}
		
		foreach ( $final as $f=> $finalRow ) {
			 $list->innertext .= $finalRow;
		}
		
		$this->Menu->Display();
		
		return ( true );
	}

}