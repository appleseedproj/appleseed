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
		
		$this->Form = $this->GetSys ( "HTML" );
		
		$this->Form->Load ( $this->LoadView ( "example_form" ), "example_form" );
		
		/*
		 * Reference
		 * 
		echo $this->GetSys ( "Components" )->Talk ( "Example", "GetResponse" );
		
		$this->GetSys ( "Event" )->Trigger ( "End", "Example", "Display" );
		
		$this->Model = &$this->GetModel( "Example" );
		$this->MapModel = &$this->GetModel( "Map" );
		$this->TagsModel = &$this->GetModel( "Tags" );
		
		$this->Form->Modify ( $pView, "label[for=passwd]", array ( "innertext" => "Blah Blah" ) )
		$this->Form->AddOption ( $pView, "select[name=country]", array ( "innertext" => "Blah Blah" ) )
		$this->Form->RemoveElement ( $pView, "select[name=country]" )
		$this->Form->DisableElement ( $pView, "select[name=country]" )
		$this->Form->Display ( $pView )
		*/
		
		$this->Form->Modify ( "label[for=passwd]", array ( "innertext" => "Blah Blah!" ) );
		$this->Form->Modify ( "input[name=text]", array ( "value" => "Default Value!" ) );
		
		parent::Display( $pView, $pData );

		$this->EventTrigger ( "End" );  // Shorthand
		
		return ( true );
	}
	
}
