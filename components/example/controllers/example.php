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
		
		/*
		 * @tutorial 
		 */
		 
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
		 * @tutorial Long version: $this->GetSys ( "Event" )->Trigger ( "Load", "Example", "Display" );
		 * @tutorial An optional array of parameters can be sent as the fourth method parameter.
		 * 
		 * @tutorial Components are referenced by <Action><Controller><Task>
		 * @tutorial For example, the trigger below corresponds to a hook class method called LoadExampleDisplay;
		 * 
		 * @tutorial Some events are automatically triggered whenever certain actions occur.
		 * @tutorial ie: Begin, End: Entry and exit points of a controller task.  
		 * 
		 * @philosophy Your components should include custom hooks, so that it can be easily extended.
		 * @philosophy Be careful to not make your component dependant on hooks, however.
		 * @philosophy A component should accomplish it's goals by itself. Hooks simply let others extend it to do more. 
		 * 
		 */
		// $this->EventTrigger ( "Load" );
		
		/*
		 * @tutorial The Talk function allows you to send a request to the Interface class of another component.
		 * @tutorial This allows you to communicate between components
		 * @tutorial The parameters correspond to which cComponent class, and which method in that class to call.
		 * @tutorial You can optionally pass parameters in a third variable of type "array".
		 * 
		 */
		$exampleResponse = $this->GetSys ( "Components" )->Talk ( "Example", "GetResponse" );
		
		$this->List = $this->GetView ( "example_list" );
		
		$this->Customers = $this->GetModel();
		
		/*
		 * @tutorial You can construct a query based on a multi-dimensional array of criteria.  
		 * @tutorial Keys on the left correspond to fieldnames, values on the right.
		 * @tutorial You can prefix the values with the following symbols to modify the query:
		 * 
		 * @tutorial ~~ (LIKE) !~ (NOT LIKE) != (NOT EQUAL) =n (IS NULL) !n (NOT NULL)
		 * @tutorial >> (GREATER THAN) << (LESS THAN) >= (GREATER THAN OR EQUAL TO) <= (LESS THAN OR EQUAL TO)
		 * @tutorial An equal comparison is the default.
		 * 
		 * @tutorial You can also prefix the field names with an "||" symbol to specify an "OR" comparison.
		 * @tutorial An "AND" comparison is default.
		 * 
		 */
		$criteria = array ( 
			"x"        => array ( "y" => "YYY", "z" => "ZZZ" ),
			"zero"     => "0Zero",
			"first"    => "~~%%1First%%", 
			"second"   => "!~%%2Second%%", 
			"third"    => "!=3Third", 
			"fourth"   => "=n", 
			"||a"      => array ( "b" => "~~BBB", "c" => "~~CCC" ),
			"fifth"    => "!n",
			"sixth"    => ">>6", 
			"seventh"  => "<<7",
			"eighth"   => ">=8", 
			"ninth"    => "<=9"
		);
		
		/*
		 * @tutorial The above criteria evaluates to:
		 * 
		 * @tutorial (y = 'YYY' AND z = 'ZZZ') AND zero = '0Zero' AND first LIKE '%1First%' 
		 * @tutorial AND second NOT LIKE '%2Second%' AND third NOT EQUAL '3Third' AND fourth IS NULL 
		 * @tutorial OR (b LIKE 'BBB' AND c LIKE 'CCC') AND fifth IS NOT NULL AND sixth > '6' 
		 * @tutorial AND seventh < '7' AND eighth >= '8' AND ninth <= '9'
		 * 
		 * @tutorial Note the double %% for LIKE comparisons. We use sprintf internally, this avoids conflict.
		 * 
		 * @philosophy This monster is here to give you an idea of how to build query criteria only.
		 * @philosophy These shortcuts are meant as just that, shortcuts.
		 * @philosophy If you ever find yourself creating a query criteria anywhere near as complex as this,
		 * @philosophy then just use a custom query!
		 * 
		 */
		 
		// This query fails because it has nothing to do with our database structure.
		$this->Customers->Retrieve ( $criteria, "fifth DESC", array ( "start" => 1500, "step", 100 ) );
		
		$this->Customers->Retrieve( null, null, array ( "start" => 0, "step" => 10 ) );
		// $this->Customers->Query ( "SELECT * FROM #__ExampleCustomers" ); 
		
		$tbody = $this->List->Find ( "[id=customer_table_body]", 0);
		
		$row = $tbody->Find ( "tr", 0);
		
		while ( $this->Customers->Fetch() ) {
			
		    $oddEven = empty($oddEven) || $oddEven == 'even' ? 'odd' : 'even';
			
			$row->class = $oddEven;
			
			$row->Find( "[class=Customer_PK]", 0 )->innertext = $this->Customers->Get ( 'Customer_PK' );
			$row->Find( "[class=CustomerName]", 0 )->innertext = $this->Customers->Get ( 'CustomerName' );
			$row->Find( "[class=Country]", 0 )->innertext = $this->Customers->Get ( 'Country' );
			
			// Exception, CustomerName combined ContactFirstName and ContactLastName
			$row->Find( "[class=ContactName]", 0 )->innertext = $this->Customers->Get ( 'ContactFirstName' ) . ' ' . $this->Customers->Get ( "ContactLastName" );
				
		    $this->List->Find ( "[id=customer_table_body]", 0)->innertext .= $row->outertext;
		    
		}
		
		$this->List->Reload();
		
		$this->List->RemoveElement ( "[id=customer_table_body] tr" );
		
		$this->List->Display();
		
		$this->List->Clear();
		unset ( $this->List );
		
		/*
		 * @tutorial Clear the memory and unset the variables
		 * 
		 * @philosophy This is just good practice, although it may also help with memory leaks in SimpleHTMLDom
		 * 
		 */
		return ( true );
	}
	
	function Edit ( ) {
		
		/*
		 * @tutorial This loads an instance of your model class.  
		 * @tutorial First parameter is the "Suffix", so, "Tags" looks for the cExampleTagsModel class.
		 * @tutorial Second parameter is an alternate table name, default is the same as your component (ie, "Example");
		 * 
		 */
		$this->Customers = $this->GetModel();
		
		$this->Employees = $this->GetModel("Employees");
		
		$this->_PrepareEditForm();
		
		/*
		 * @tutorial The order in which views are loaded and edited is important.  
		 * @tutorial If you call one view within another view, make sure to edit them separately, and in the proper order (outer view last).
		 * @tutorial In this instance, the "example" view wraps "example_form", so load and edit "example" after "example_form"
		 * 
		 */
		$this->View = $this->GetView ( "example" );
		$this->View->Display();
		
		$this->View->Clear(); $this->Form->Clear();
		unset ( $this->View ); unset ( $this->Form );
		
		return ( true );
	}
	
	/**
	 * Prepare the Edit form
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
	public function _PrepareEditForm() {
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
		 * @tutorial You can remove a specified element as well.
		 * 
		 */
		$this->Form->RemoveElement ( "input[name=removed_text]" );
		
		/*
		 * @tutorial Here's a trick:
		 * @tutorial If you know that your element is wrapped inside other elements (ie, a table), you can delete parent nodes
		 * 
		 * @tutorial If the element you're looking for doesn't exist, however, this will cause a fatal error.
		 *
		 * @philosophy You may not know what your view structure looks like.
		 * @philosophy Since themes are able to overwrite views, you need to be careful with tricks like this.
		 * @philosophy A better way would be to target rows directly using classes or id's.
		 *  
		 */
		$this->Form->Find("input[name=Phone]", 0)->Parent(0)->Parent(0)->outertext = "";
		
		/*
		 * @tutorial And you can disable an element, too.
		 * 
		 */
		$this->Form->DisableElement ( "input[name=PostalCode]" );
		
		
		/*
		 * @tutorial Get the primary key of what record we're editing.
		 * 
		 * @philosophy The rules for tables are: Primary Keys = _PK, Foreign Keys = _FK, Tables = Plural, naming is ProperCase.
		 * @philosophy Otherwise, prioritize readability above all else.
		 * 
		 */
		$Customer_PK = cRequest::Get ( "Customer_PK", 131);
		
		/*
		 * @tutorial Retrieve a single record based on the primary key.
		 * 
		 */
		$this->Customers->Retrieve ( $Customer_PK );
		
		/*
		 * @tutorial You can traverse the DOM to set attributes or modify values.
		 * 
		 * @philosophy Using SimpleHTMLDom's original functions, use proper case to follow Appleseed standards.
		 *
		 */
		$this->Form->Find("select[name=SalesRep_Employee_FK]", 0)->innertext .= "<option value=''>" . __ ("Select A Sales Rep") . "</option>";
		
		/* 
		 * @tutorial Quirk of SimpleHTMLDom, reload after modifying innertext/outertext, otherwise new Children won't be found.
		 *
		 */
		$this->Form->Reload();
		
		/*
		 * @tutorial We've dynamically populated a select list.
		 * @tutorial Now let's disable the option we added.
		 * 
		 * @tutorial You can select an option the same way by using ->selected = "selected"
		 * 
		 */
		$this->Form->Find("select[name=SalesRep_Employee_FK]", 0)->Children(0)->disabled = "disabled";
		
		/*
		 * @tutorial Load from the database based on this primary key.
		 * @tutorial Then load a list of employees to be used for a select option. 
		 * 
		 */
		$this->Employees->Retrieve( null, "LastName DESC" );
		
		// First element from Retrieve
		$employees[$this->Employees->Get ( "Employee_PK" )] = $this->Employees->Get ( "FirstName" ) . " " . $this->Employees->Get ( "LastName" );
		
		// Loop until no more results are found.
		while ( $this->Employees->Fetch() ) {
			$employees[$this->Employees->Get ( "Employee_PK" )] = $this->Employees->Get ( "FirstName" ) . " " . $this->Employees->Get ( "LastName" );
		}
		
		/*
		 * @tutorial Here's an easy way to populate a select list.  
		 * @tutorial All you need is an associative array (value => label)
		 * 
		 * @tutorial If you want to get more advanced, you can create optgroups by using a multidimensional array.
		 * @tutorial For instance:
		 * @tutorial array ( "Group 1" => array ( "1" => "First", "2" => "Second" ), "Group 2" => array ( "3" => "Third" ) )
		 * 
		 */
		$this->Form->AddOptions ("select[name=SalesRep_Employee_FK]", $employees );
		
		/*
		 * @tutorial Finally, you can automatically synchronize between $_REQUEST data and an optional set of defaults.
		 * @tutorial Be sure to inject any data from the database from the database into the defaults array.
		 * 
		 */
		$defaults = array ( "CustomerName" => "Michael Chisari", "AddressLine2" => "Default");
		$data = (array) $this->Customers->Get ( "Data" );
		
		$defaults = array_merge ( (array)$defaults, (array)$data );
		$this->Form->Synchronize ( $defaults );
		
		return ( true );
	}
	
	function Save ( ) {
		
		/*
		 * @tutorial First, we create our model.
		 * @tutorial Then, we synchronize our record data with the information from the URL request.
		 * 
		 */
		$this->Customers = $this->GetModel();
		$this->Customers->Synchronize();
		
		/*
		 * @tutorial We can protect fields from being updated by using the Protect function.
		 * 
		 * @tutorial For instance, our database table has a field called CreditLimit, but the form doesn't include it.
		 * @tutorial If we don't protect it, it will get set to "null" in the database when the record is saved.
		 * 
		 * @tutorial Same with PostalCode, which is disabled and doesn't submit with the form.
		 * 
		 * @tutorial The function "Endanger" will do the opposite, and take the field off of the protected list.
		 *
		 */
		$this->Customers->Protect ( "CreditLimit" );
		$this->Customers->Protect ( "PostalCode" );
		 
		/*
		 * @tutorial Now, simply save the data.
		 *
		 */
		 $this->Customers->Save();
		 
		 $this->Display ();
		 
		 return ( true );
	}
	
}
