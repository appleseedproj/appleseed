<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Example
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Example Component Controller
 * 
 * Example Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Example
 */
class cExampleController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pObject ) {       
		parent::__construct( $pObject );
	}
	
	public function Display ( $pView = null, $pData = null ) {
		
		echo $this->GetSys ( "Components" )->Talk ( "Example", "GetResponse" );
		
		$Event = $this->GetSys ( "Event" );
		
		$Event->Trigger ( "Begin", "Example", "Display");
		
		$this->Model = &$this->GetModel( "Example" );
		
		parent::Display( $pView, $pData );

		$Event->Trigger ( "End", "Example", "Display" );
		return ( true );
	}
	
}
