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
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = null ) {
		$this->EventTrigger ( "Begin" );
		
		// echo $this->GetSys ( "Components" )->Talk ( "Example", "GetResponse" );
		
		// $this->Model = &$this->GetModel( "Example" );
		// $this->MapModel = &$this->GetModel( "Map" );
		// $this->TagsModel = &$this->GetModel( "Tags" );
		
		parent::Display( $pView, $pData );

		// $this->GetSys ( "Event" )->Trigger ( "End", "Example", "Display" );
		$this->EventTrigger ( "End" );  // Shorthand
		
		return ( true );
	}
	
}
