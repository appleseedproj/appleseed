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

/** Photos Component Profile Controller
 * 
 * Photos Component Profile Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Photos
 */
class cPhotosProfileController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->Photo = $this->GetView ( $pView ); 
		
		$src = $this->_GetProfilePhoto ();
		
		if ( $src ) {
			$this->Photo->Find ( '[id=profile-photo-focus]', 0 )->src = $src;
		} else {
			$this->Photo->Find ( '[id=profile-photo-focus]', 0 )->class .= ' no-profile';
		}
		
		$this->Photo->Display();
		
		return ( true );
	}
	
	private function _GetProfilePhoto ( ) {
		
		$focus = $this->Talk ( "User", "Focus" );
		
		/*
		 * @todo: Switch to new photo system once photo component is completed.
		 * 
		 */
		
		$legacy_file = ASD_PATH . "_storage" . DS . "legacy" . DS . "photos" . DS . $focus->Username . DS . "profile.jpg";
		if ( !file_exists ( $legacy_file ) ) {
			return ( false );
		} else {
			$legacy_src = "/legacy/photos/" . $focus->Username . "/profile.jpg";
		}
		
		return ( $legacy_src );
	}
	
}
