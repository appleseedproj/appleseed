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

/** Foundation Class
 * 
 * Base class for Foundation
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cFoundation extends cBase {
	
	protected $_Config;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {
		
 		// Load foundation configuration.
 		$this->_Config = new cConf ( );
		$this->_Config->Set ( "Data",  $this->_Config->Load ( "foundations" ) );
		
		return ( true );
	}
	
	/**
	 * Loads the proper foundation using inheritance order
	 *
	 * @access  public
	 * @param string $pRoute Which foundation to route to.
	 */
	public function Load ( $pRoute ) {
		eval ( GLOBALS );
		
		$Config = $this->GetSys ( "Config" );
		$paths = array_reverse ( $Config->GetPath() );
		
		$Buffer = $this->GetSys ( "Buffer" );
		
		foreach ( $paths as $p => $path ) {
			$route = ltrim ( rtrim ( $pRoute, '/' ), '/' );
			$filename = $zApp->GetPath () . DS . 'foundations' . DS . $path . DS . $route;
			if ( is_file ( $filename ) ) {
				$Buffer->LoadFoundation ( $filename );
				return ( true );
			}
		}
		
		echo __("Foundation Not Found", array ( 'route' => $route ) );
		exit;
	}

}
