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
		
		$user = $this->Talk ( "User", "Current" );
		
		$parameters['account'] = $user->Username . '@' . $user->Domain;
		$access = $this->Talk ( "Security", "Access", $parameters );
		
		$pView = 'main';
		
		if ( $user->Remote ) $pView = 'remote';
		else if ( $user->Username ) $pView = 'local';
		
		if ( ( $user->Username ) && ( $access->Get ( "Admin" ) ) ) $pView .= '.admin';
		
		$this->Header = $this->GetView ( $pView );
		
		$link = $this->Header->Find ( "[id=current-user-profile-link]", 0 );
		
		$icon = $this->Header->Find ( "[class=current-icon]", 0);
		
		//$icon->src = 'http://' . $user->Domain . '/_storage/legacy/photos/' . $user->Username . '/profile.jpg';
		$data = array ( "username" => $user->Username, "domain" => $user->Domain, "width" => 32, "height" => 32 );
		$icon->class .= " usericon ";
		$icon->src = $this->GetSys ( "Event" )->Trigger ( "On", "User", "Icon", $data );
		
		$this->Header->Find ( "[id=header-search]", 0)->innertext = $this->GetSys ( "Components" )->Buffer ( "search", "search", "global", "ask" ); 
		
		$this->Header->Find ( "[class=links-news]", 0)->href = 'http://' . $user->Domain . '/profile/' . $user->Username . '/news/';
		$this->Header->Find ( "[class=links-profile]", 0)->href = 'http://' . $user->Domain . '/profile/' . $user->Username . '/';
		$this->Header->Find ( "[class=links-options]", 0)->href = 'http://' . $user->Domain . '/profile/' . $user->Username . '/options/';
		
		$link->innertext = $user->Username . '@' . $user->Domain;
		$link->href = 'http://' . $user->Domain . '/profile/' . $user->Username ;
		
		$this->Header->Display();
		
		return ( true );
	}

}