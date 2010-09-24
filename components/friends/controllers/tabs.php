<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Friends
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Friends Component Tabs Controller
 * 
 * Friends Component Tabs Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Friends
 */
class cFriendsTabsController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$current = $this->Talk ( 'User', 'Current' );
		
		$focus = $this->Talk ( 'User', 'Focus' );
		
		$current = $this->Talk ( 'User', 'Current' );
		
		$this->View = $this->GetView ( 'tabs' ); 
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		$focus = $this->Talk ( 'User', 'Focus' );
		$current = $this->Talk ( 'User', 'Current' );
		
		$ul = $this->View->Find ( "[class=friends-tabs-list]", 0);
		
		$row = $this->View->Copy ( "[class=friends-tabs-list]" )->Find ( "li", 0 );
		$rowOriginal = $row->outertext;
		
		$ul->innertext = "";
		
		// All
		$row->Find("a", 0)->innertext = "All";
		$row->class .= " system ";
		$ul->innertext .= $row->outertext;
		
		if ( $current->Account == $focus->Account ) {
			// Requests
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			$row->Find("a", 0)->innertext = "Requests";
			$row->Find("li", 0)->class .= " system system-all";
			$ul->innertext .= $row->outertext;
			
			// Create a model for Circles.
			$this->Circles = $this->GetModel ( "Circles" );
		
			$circles = $this->Circles->GetCircles ( $focus->Username );
			
			$currentCircle = urldecode ( strtolower ( $this->GetSys ( "Request" )->Get ( "Circle" ) ) );
			
			// No circles were found, so we're done.
			if ( count ( $circles ) == 0 ) return ( true );
		
			// Create the Circles tabs
			
			foreach ( $circles as $c => $circle ) {
				$row = new cHTML ();
				$row->Load ( $rowOriginal );
			
				$row->Find("a", 0)->innertext = $circle['name'];
				$row->Find("a", 0)->href = '/profile/' . $focus->Username . '/friends/' . str_replace ( ' ', '-', strtolower ( $circle['name'] ) );
				
				// Select the current tab
				if ( $currentCircle == str_replace ( ' ', '-', strtolower ( $circle['name'] ) ) ) {
					$row->Find("li", 0)->class .= " selected ";
				}
				
				$ul->innertext .= $row->outertext;
				
				unset ( $row );
			}
		} else if ( $current ) {
			// Mutual
			$row->Find("a", 0)->innertext = "Mutual";
			$row->class .= " system system-requests";
			$ul->innertext .= $row->outertext;
		} else {
		}
		
		return ( true );
	}
	
	
}
