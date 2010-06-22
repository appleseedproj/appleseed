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

	/**
	 * Displays a view
	 *
	 * @access  public
	 * @var string $pView Which view file to load
	 * @var array $pData Extended data array
	 */
	public function Display ( $pView = null, $pData = null) {
		eval ( GLOBALS );
		
		return ( $this->LoadView ( $pView ) );
	}
	
	
	/**
	 * Loads a view
	 *
	 * @access  public
	 * @var string $pView Which view file to load
	 */
	public function LoadView ( $pView ) {
		eval ( GLOBALS );
		
		$viewpath = $this->_GetViewPath ( $pView ) ;
		
		if ( $viewpath ) {
			include ( $viewpath );
			return ( true );
		}
		
		return ( false );
	}
	
	/**
	 * Determines the view path, using inheritence
	 *
	 * @access  private
	 * @var string $pView Which view file to load
	 */
	private function _GetViewPath ( $pView = null ) {
		eval ( GLOBALS );
		
		if ( !$pView ) $pView = $this->_Component;
		
		$themepath = $zApp->Theme->Config->GetPath();
		
		$filename = $zApp->GetPath() . DS . 'components' . DS . $this->_Component . DS . 'views' . DS . $pView . '.php';
		if ( file_exists ( $filename ) ) $return = $filename;
		
		foreach ( $themepath as $t => $theme ) {
			$filename = $zApp->GetPath() . DS . 'themes' . DS . $theme . DS . 'views' . DS . $this->_Component . DS . $pView . '.php';
			if ( file_exists ( $filename ) ) $return = $filename;
		}
		
		if ( !$return ) {
			echo __("View Not Found", array ( 'name' => $pView ) );
			return ( false );
		}
		
		return ($return);
	}
	
	/**
	 * Gets the buffer counter of the current component.
	 *
	 * @access  public
	 */
	public function GetBufferCounter ( ) {
		
		return ( $this->_BufferCounter );
		
	}

}
