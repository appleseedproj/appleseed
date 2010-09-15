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

/** Profile Component Tabs Controller
 * 
 * Profile Component Tabs Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Profile
 */
class cProfileTabsController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		
		$focus = $this->Talk ( 'User', 'Focus' );
		$current = $this->Talk ( 'User', 'Current' );
		
		$components = $this->GetSys ( 'Components' );
		$componentList = $components->Get ( 'Config' )->Get ( 'Components' ); 
		
		$this->Tabs = $this->GetView ( 'tabs' );
		
		$list = $this->Tabs->Find ( 'ul', 0);
		
		$row = $this->Tabs->Copy ( 'ul li' )->Find ( 'li', 0 );
		
		$more = $this->Tabs->Copy ( 'ul li[class=more]' )->Find ( 'li', 0 );
		
		$list->innertext = "";
		
		$config = $this->Get ( "Config" );
		
		$ordering = explode ( ' ', $config['tabs_ordering'] );
		$maxTabs = isset ( $config['maximum_tabs'] ) ? $config['maximum_tabs'] : 10;
		
		$request = ltrim ( rtrim ( $_SERVER['REQUEST_URI'], '/' ), '/' );
		
		foreach ( $componentList as $c => $component ) {
			if ( !$tabItems = $components->Talk ( $component, 'AddToProfileTabs' ) ) continue;
			
			foreach ( $tabItems as $m => $menu ) {
				$link = 'profile/' . $focus->Username . '/' . ltrim ( rtrim ( $menu['link'], '/' ), '/' );
				
				$row->Find ( 'a', 0 )->innertext = __( $menu['title'] );
				$row->Find ( 'a', 0 )->href = '/profile/' . $focus->Username . $menu['link'];
				$row->class = $menu['class'];
				
				$requestPattern = '/^' . addcslashes ($link, '/') . '\/(.*)$/';
				if ( ( $request == $link ) or ( preg_match ( $requestPattern, $request ) ) ) { 
					$row->class .= " selected";
				}
				
				$id = strtolower ( $menu['id'] );
				$title = strtolower ( $menu['title'] );
				$owner = $menu['owner'];
				
				if ( $owner ) {
					if ( ( $focus->Username != $current->Username ) or ( $focus->Domain != $current->Domain ) ) continue;
				}
				
				$rows[$id] = $row->outertext;
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
		
		$tabCount = 0;
		foreach ( $final as $f=> $finalRow ) {
			$tabCount++;
				
			if ( $tabCount > $maxTabs ) continue;
				
			$list->innertext .= $finalRow;
		}
		
		if ( $tabCount > $maxTabs ) $list->innertext .= $more;
		
		$this->Tabs->Reload();
				
		$this->Tabs->Display();
		
		return ( true );
	}

}
