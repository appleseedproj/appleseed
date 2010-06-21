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

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
 		// Load foundation configuration.
 		$this->Config = new cConf ( );
		$this->Config->Config = $this->Config->Load ( "foundations" );
		
		return ( true );
	}
	
	/**
	 * Loads the proper foundation using inheritance order
	 *
	 * @access  public
	 * @var string $pRoute Which foundation to route to.
	 */
	public function Load ( $pRoute ) {
		eval ( GLOBALS );
		
		$paths = array_reverse ( $this->Config->GetPath() );
		
		foreach ( $paths as $p => $path ) {
			$route = ltrim ( rtrim ( $pRoute, '/' ), '/' );
			$filename = $zApp->GetPath () . DS . 'foundations' . DS . $path . DS . $route;
			if ( file_exists ( $filename ) ) {
				$this->Buffer->LoadFoundation ( $filename );
				return ( true );
			} else {
				echo __("Foundation Not Found", array ( 'route' => $route ) );
				exit;
			}
		}
		// 404 error
		
		return ( false );
	}

}
