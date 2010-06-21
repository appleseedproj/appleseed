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

/** Controller Class
 * 
 * Base class for controllers
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cController extends cBase {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}

	public function Display ( $pView = null, $pData = null) {
		$this->LoadView ( $pView );
		return ( true );
	}
	
	public function LoadView ( $pView ) {
		require_once ( $this->_GetViewPath ( $pView ) );
	}
	
	private function _GetViewPath ( $pView = null ) {
		eval ( GLOBALS );
		
		if ( !$pView ) $pView = $this->_component;
		
		$themepath = $zApp->Theme->Config->GetPath();
		
		$filename = $zApp->GetPath() . DS . 'components' . DS . $this->_component . DS . 'views' . DS . $pView . '.php';
		if ( file_exists ( $filename ) ) $return = $filename;
		
		foreach ( $themepath as $t => $theme ) {
			$filename = $zApp->GetPath() . DS . 'themes' . DS . $theme . DS . 'views' . DS . $this->_component . DS . $pView . '.php';
			if ( file_exists ( $filename ) ) $return = $filename;
		}
		
		if ( !$filename ) {
			echo __("View Not Found", array ( 'name' => $pView ) );
			return ( false );
		}
		
		return ($filename);
	}
}
