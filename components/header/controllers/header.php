<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Header
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Header Component Controller
 * 
 * Header Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Header
 */
class cHeaderHeaderController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		
		$current = $this->Talk ( "User", "Current" );
		
		$parameters['account'] = $current->Username . '@' . $current->Domain;
		$access = $this->Talk ( "Security", "Access", $parameters );
		
		$pView = 'main';
		
		if ( $current->Remote ) $pView = 'remote';
		else if ( $current->Username ) $pView = 'local';
		
		if ( ( $current->Username ) && ( $access->Get ( "Admin" ) ) ) $pView .= '.admin';
		
		$this->Header = $this->GetView ( $pView );
		
		$link = $this->Header->Find ( "[id=current-user-profile-link]", 0 );
		
		$icon = $this->Header->Find ( "[class=current-icon]", 0);
		
		$data = array ( "username" => $current->Username, "domain" => $current->Domain, "width" => 32, "height" => 32 );
		$icon->class .= " usericon ";
		$icon->src = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Icon", $data );
		
		$this->Header->Find ( "[id=header-search]", 0)->innertext = $this->GetSys ( "Components" )->Buffer ( "search", "search", "global", "ask" ); 
		
		$this->Header->Find ( "[class=links-news]", 0)->href = 'http://' . $current->Domain . '/profile/' . $current->Username . '/news/';
		$this->Header->Find ( "[class=links-profile]", 0)->href = 'http://' . $current->Domain . '/profile/' . $current->Username . '/';
		$this->Header->Find ( "[class=links-options]", 0)->href = 'http://' . $current->Domain . '/profile/' . $current->Username . '/options/';
		
		$this->Header->Find ( "[class=links-options]", 0)->href = 'http://' . $current->Domain . '/profile/' . $current->Username . '/options/';
		
		$this->Header->Find ( "[class=mail]", 0)->href = 'http://' . $current->Domain . '/profile/' . $current->Username . '/messages/';
		$this->Header->Find ( "[class=friends]", 0)->href = 'http://' . $current->Domain . '/profile/' . $current->Username . '/friends/requests/';
		
		$link->innertext = $current->Username . '@' . $current->Domain;
		$link->href = 'http://' . $current->Domain . '/profile/' . $current->Username ;
		
		$this->Header->Display();
		
		return ( true );
	}

}