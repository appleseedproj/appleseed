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
	
	/**
	 * Display the default view
	 * 
	 * @tutorial The default task is to display the default view.  
	 * @tutorial Tasks passed through browser requests are mapped to a class method.
	 * @tutorial For instance, if a form passes Task=Edit, then $this->Edit() will be executed.
	 * 
	 * @philosophy Views are dumb.  There is no view class, just a basic HTML file.
	 * @philosophy Logic is kept in the controller, and out of the view.
	 * @philosophy While you can use PHP within your view, it's not recommended.
	 *
	 * @access  public
	 */
	public function Display ( $pView = null, $pData = null ) {
		/*
		 * @tutorial This triggers an event, which loads the appropriate hooks.
		 * @tutorial This shorthand automatically determines the current component and method.
		 * @tutorial Long version: $this->GetSys ( "Event" )->Trigger ( "Begin", "Example", "Display" );
		 * @tutorial An optional array of parameters can be sent as the fourth method parameter.
		 * 
		 * @philosophy Your components should include a lot of hooks, so that it can be easily extended.
		 * @philosophy Be careful to not make your component dependant on hooks, however.
		 * @philosophy A component should accomplish it's goals by itself. Hooks simply let others extend it to do more. 
		 * 
		 */
		$this->EventTrigger ( "Begin" );
		
		/*
		 * @tutorial The Talk function allows you to send a request to the Interface class of another component.
		 * @tutorial This allows you to communicate between components
		 * @tutorial The paramters correspond to which cComponent class, and which method in that class to call.
		 * @tutorial You can optionally pass parameters in a third variable of type "array".
		 * 
		 */
		echo $this->GetSys ( "Components" )->Talk ( "Example", "GetResponse" );
		
		/*
		 * @tutorial This loads an instance of your model class.  
		 * @tutorial First parameter is the "Suffix", so, "Tags" looks for the cExampleTagsModel class.
		 * @tutorial Second parameter is an alternate table name, default is the same as your component (ie, "Example");
		 * 
		 */
		$this->Model = $this->GetModel( "Example" );
		
		/*
		 * @tutorial In order to modify views based on your model, you load the view into an DOM parser.
		 * @tutorial The DOM parser is based extends SimpleHTMLDom
		 * @link http://simplehtmldom.sourceforge.net/
		 * @tutorial Some extra functionality is included, but basically all the original functions are still intact.
		 * 
		 */
		$this->Form = $this->GetView ( "example_form" );
		
		// $this->Form->Load ( $this->LoadView ( "example_form" ), "example_form" );
		
		// $this->Form->SetDefaults ( "select[name=country]" );
		
		$this->Form->Modify ( "label[for=passwd]", array ( "innertext" => "Blah Blah!" ) );
		$this->Form->Modify ( "input[name=text]", array ( "value" => "Default Value!" ) );
		$this->Form->Modify ( "textarea[name=text_area]", array ( "innertext" => "Blah!" ) );
		
		/*
		 * @tutorial You can traverse the DOM to set attributes or modify values.
		 * 
		 * @philosophy Using SimpleHTMLDom's original functions, use proper case to follow Appleseed standards.
		 *
		 */
		$this->Form->Find("select[name=DynamicSelect]", 0)->innertext .= "<option value='1'>One</option>";
		$this->Form->Find("select[name=DynamicSelect]", 0)->innertext .= "<option value='2'>Two</option>";
		$this->Form->Find("select[name=DynamicSelect]", 0)->innertext .= "<option selected value='3'>Three</option>";
		$this->Form->Find("select[name=DynamicSelect]", 0)->innertext .= "<option value='4'>Four</option>";
		
		/* 
		 * @tutorial Quirk of SimpleHTMLDom, reload after modifying innertext/outertext, otherwise new Children won't be found.
		 *
		 */
		$this->Form->Reload();
		
		/*
		 * @tutorial We've dynamically populated a select list.  
		 * @tutorial Now let's disable one option and select another.
		 * 
		 */
		$this->Form->Find("select[name=DynamicSelect]", 0)->Children(0)->disabled = "disabled";
		$this->Form->Find("select[name=DynamicSelect]", 0)->Children(0)->selected = "selected";
		
		/*
		 * @tutorial Here's an easier way to populate a select list, though.  
		 * @tutorial All you need is an associative array (value => label)
		 * 
		 */
		$options = array ( "5" => "Five", "6" => "Six", "7" => "Seven", "8" => "Eight" );
		$this->Form->AddOptions ("select[name=DynamicSelect]", $options );
		
		
		/*
		 * @tutorial You can remove a specified element as well.
		 * 
		 */
		$this->Form->RemoveElement ( "input[name=removed_text]" );
		
		/*
		 * @tutorial Here's a trick:
		 * @tutorial If you know that your element is wrapped inside other elements (ie, a table), you can delete parent nodes
		 *
		 * @philosophy You may not know what your view structure looks like.
		 * @philosophy Since themes are able to overwrite views, you need to be careful with tricks like this.
		 * @philosophy A better way would be to target rows directly using classes or id's.
		 *  
		 */
		$this->Form->Find("input[name=removed_row]", 0)->Parent(0)->Parent(0)->outertext = "";
		
		/*
		 * @tutorial And you can disable an element, too.
		 * 
		 */
		$this->Form->DisableElement ( "input[name=file]" );
		
		/*
		 * @tutorial Finally, you can automatically synchronize between $_REQUEST data and an optional set of defaults.
		 * 
		 */
		$defaults = array ( "text_area" => "This is the default", "StaticCheck[0]" => "checked", "StaticSelect" => "Thing 2.1", "DynamicSelect" => "6" );
		$this->Form->Synchronize ( $defaults );
		
		
		/*
		 * @tutorial The order in which views are loaded and edited is important.  
		 * @tutorial If you call one view within another view, make sure to edit them separately, and in the proper order (outer view last).
		 * @tutorial In this instance, the "example" view wraps "example_form", so load and edit "example" after "example_form"
		 * 
		 */
		$this->View = $this->GetView ( "example" );
		
		$this->View->Display();

		$this->EventTrigger ( "End" );  // Shorthand
		
		/*
		 * @tutorial Clear the memory and unset the variables
		 * 
		 * @philosophy This is just good practice, although it may also help with memory leaks in SimpleHTMLDom
		 * 
		 */
		$this->View->Clear(); $this->Form->Clear();
		unset ( $this->View ); unset ( $this->Form );
		
		return ( true );
	}
	
}
