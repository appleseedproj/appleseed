<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Profile Hook Class
 * 
 * Profile Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cProfileHook extends cHook {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	/*
	 * If a focus user can't be found, show a 404
	 * 
	 */
	public function OnSystemRoute ( $pData = null ) {
		
		$currentFoundation = $pData['foundation'];
		
		$request = ltrim ( rtrim ( $_SERVER['REQUEST_URI'], '/' ), '/' );
		
		if ( preg_match ( '/^profile/', $request ) ) {
			$focus = $this->GetSys ( 'Components' )->Talk ( 'User', 'Focus' );
		
			// Focus user was not found
			if ( !$focus ) {
				return ( 'common/404.php' );
			}
		}
		
		if ( $currentFoundation == 'profile/landing.php' ) {
			$focus = $this->GetSys ( 'Components' )->Talk ( 'User', 'Focus' );
			$current = $this->GetSys ( 'Components' )->Talk ( 'User', 'Current' );
			if ( $focus->Account == $current->Account ) {
				$url = 'http://' . ASD_DOMAIN . '/profile/' . $focus->Username . '/news/';
				header ( 'Location:' . $url );
				exit;
			} else {
				return ( 'profile/page.php' );
			}
		}
		
		return ( false );
	}
	
}
