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
class cExampleExampleController extends cController {
	
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
	 * @tutorial The default task is the Display view.
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
		
		// $this->GetSys ( 'Event' )->Trigger ( "System", "Node", "Discovery", array ( "domain" => "carnivale.appleseed" ) );
		
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
		
		$request = $this->GetSys ( "Request" )->Get();
		
		$task = $this->GetSys ( "Request" )->Get ( "Task" );
		
		/*
		 * @tutorial The Talk function allows you to send a request to the Interface class of another component.
		 * @tutorial This allows you to communicate between components
		 * @tutorial The parameters correspond to which cComponent class, and which method in that class to call.
		 * 
		 * @tutorial You can optionally pass parameters in a third variable of type "array".
		 * 
		 */
		$exampleResponse = $this->Talk ( "Example", "GetResponse" );
		
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
		$this->Customers->Retrieve ( $criteria, "fifth DESC", array ( "start" => 1500, "step" => 100 ) );
		
		/*
		 * @tutorial You can also retrieve saved session data.
		 */
		$session = $this->GetSys ( "Session" );
		
		/*
		 * @tutorial Set a context first before you use sessions.  
		 * 
		 * @tutorial This allows you to save variables that may also be being saved by other
		 * @tutorial components or another instance of the same component.
		 * 
		 * @tutorial This is set by default when the component is loaded, so you don't usually
		 * @tutorial need to set it manually.
		 */
		$session->Context ( $this->Get ( "Context" ) );
		
		/*
		 * @tutorial Retrieve an array of all the session variables in the current context.
		 * 
		 * @tutorial You can "Set", "Get", "Destroy" single session variables.
		 * @tutorial And you can also "Save" an array of data, 
		 * @tutorial or "Clear" all session data in the current context.
		 * 
		 */
		$saved = $session->Get();
		
		list ( $start, $step, $page ) = $this->_PageCalc();
		
		// Retrieve from the db, using no criteria except for the pagination settings.
		$this->Customers->Retrieve( null, null, array ( "start" => $start, "step" => $step ) );
		
		$baseURL = $this->GetSys ( "Router" )->Get ( "Base" );
		$this->List->Find ( "form", 0 )->action = $this->GetSys ( "Router" )->Get ( "Base" );
		
		/*
		 * @tutorial Each instance of a component is given an internal context variable
		 * @tutorial This variable looks like: <component>.#.<controller>
		 * @tutorial You can use the context to avoid what's called "component collisions".
		 * @tutorial That's when you have two instances of the same component on the same page.
		 * 
		 * @tutorial For instance, with two instances of the Example component in one foundation,
		 * @tutorial the first one's context will be "example.1.example"
		 * @tutorial and the second will be "example.2.example".
		 * 
		 * @philosophy If you're reasonably sure your component won't be called twice in a foundation,
		 * @philosophy you can easily get away without caring about the context.  But it's generally
		 * @philosophy a good idea to use contexts to prevent component collisions.
		 * 
		 */
		$this->List->Find( "input[name=Context]", 0 )->value = $this->_Context;
		
		$tbody = $this->List->Find ( "[id=customer-table-body] tbody tr", 0);
		
		$row = $this->List->Copy ( "[id=customer-table-body] tbody tr" )->Find ( "tr", 0 );
		
		/*
		 * @tutorial Clear out the original template element we buil the table from.
		 *
		 */
		$tbody->innertext = " " ;
		
		$cellCustomer_PK = $row->Find( "[class=Customer_PK]", 0 );
		$cellCustomerName = $row->Find( "[class=CustomerName]", 0 );
		$cellContactName = $row->Find( "[class=ContactName]", 0 );
		$cellCountry = $row->Find( "[class=Country]", 0 );
		$cellMasslist = $row->Find( "[class=Masslist] input[type=checkbox]", 0 );
		
		$customerName = $this->Customers->Get ( 'ContactFirstName' ) . ' ' . $this->Customers->Get ( "ContactLastName" );
		
		while ( $this->Customers->Fetch() ) {
			
		    $oddEven = empty($oddEven) || $oddEven == 'even' ? 'odd' : 'even';
			
			$row->class = $oddEven;
			
			$id = $this->Customers->Get ( 'Customer_PK' );
			
			$url = $baseURL . "edit" . DS . $id . DS;
			
			$customerName = $this->Customers->Get ( 'CustomerName' );
			$country = $this->Customers->Get ( 'Country' );
			
			$context = $this->_Component . '.' . strtolower ( __FUNCTION__ );
			
			$cellCustomer_PK->innertext = $this->List->Link ( $id, $url );
			$cellCustomerName->innertext = $this->List->Link ( $customerName, $url );
			$cellCountry->innertext = $this->List->Link ( $country, $url );
			$cellMasslist->name = "Masslist[" . $id . "]";
			
			$customerName = $this->Customers->Get ( 'ContactFirstName' ) . ' ' . $this->Customers->Get ( "ContactLastName" );
			
			// Exception, CustomerName combined ContactFirstName and ContactLastName
			$cellContactName->innertext = $this->List->Link ( $customerName, $url );
				
		    $tbody->innertext .= $row->outertext;
		}
		
		/*
		 * @tutorial You can also call components from within other components.
		 * @tutorial Here we call the pagination component, passing along a set of parameters
		 * 
		 * @tutorial The "Buffer" function is similar to the "Go" function, 
		 * @tutorial except it returns a buffer instead of echoing to the display.
		 * 
		 * @tutorial You can use a shortcut with the parameters of both "Go" and "Buffer"
		 * @tutorial If you use an array for any parameter except the first ("component"),
		 * @tutorial then the function will assume that's the Data parameter, 
		 * @tutorial and will use defaults for the remaining.
		 * 
		 * @tutorial For instance, this allows you to shorten:
		 * @tutorial Buffer ( "pagination", null, null, null, array ( "key" => "value" ) );
		 * @tutorial to:
		 * @tutorial Buffer ( "pagination", array ( "key" => "value" ) );
		 * 
		 * @philosophy Sometimes, it's the little things in life.
		 * 
		 */
		 
		$link = $this->GetSys ( "Router" )->Get ( "Base" ) . '(.*)';
		$total = $this->Customers->Get ( "Total" );
		
		$pageData = array ( 'start' => $start, 'step'  => $step, 'total' => $total, 'link' => $link );
		$pageControls =  $this->List->Find ("nav[class=pagination]");
		foreach ( $pageControls as $p => $pageControl ) {
			$pageControl->innertext = $this->GetSys ( "Components" )->Buffer ( "pagination", $pageData ); 
		}
		
		$pageData = array ( 'total' => $total, 'step' => $step, 'link' => $link );
		$pageControls =  $this->List->Find ("nav[class=pagination-amount]");
		foreach ( $pageControls as $p => $pageControl ) {
			$pageControl->innertext = $this->GetSys ( "Components" )->Buffer ( "pagination", "pagination", "amount", $pageData ); 
		}
		$this->List->Synchronize();
		
		$this->_PrepareMessage();
		
		$this->List->Display();
		
		$this->List->Clear();
		unset ( $this->List );
		
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
		
		$this->Form = $this->GetView ( "example_form" );
		
		$this->_PrepareForm();
		
		/*
		 * @tutorial The order in which views are loaded and edited is important.  
		 * @tutorial If you call one view within another view, make sure to edit them separately, and in the proper order (outer view last).
		 * @tutorial In this instance, the "example" view wraps "example_form", so load and edit "example" after "example_form"
		 * 
		 */
		$this->Form->Display();
		
		$this->Form->Clear();
		unset ( $this->Form );
		
		return ( true );
	}
	
	function Add ( ) {
		/*
		 * @tutorial This loads an instance of your model class.  
		 * @tutorial First parameter is the "Suffix", so, "Tags" looks for the cExampleTagsModel class.
		 * @tutorial Second parameter is an alternate table name, default is the same as your component (ie, "Example");
		 * 
		 */
		$this->Customers = $this->GetModel();
		
		$this->Employees = $this->GetModel("Employees");
		
		$this->Form = $this->GetView ( "example_form" );
		
		$this->_PrepareForm();
		
		/*
		 * @tutorial The order in which views are loaded and edited is important.  
		 * @tutorial If you call one view within another view, make sure to edit them separately, and in the proper order (outer view last).
		 * @tutorial In this instance, the "example" view wraps "example_form", so load and edit "example" after "example_form"
		 * 
		 */
		$this->Form->Display();
		
		$this->Form->Clear();
		unset ( $this->Form );
		
		return ( true );
	}
	
	/**
	 * Prepare the form
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
	public function _PrepareForm() {
		
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
		$this->Form->Find("input[name=Phone]", 0)->Parent(0)->outertext = "";
		
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
		$Customer_PK = $this->GetSys ( "Request" )->Get ( 'Customer_PK', $this->Customers->Get ( "Customer_PK" ) );
		
		/*
		 * @tutorial Retrieve a single record based on the primary key.
		 * 
		 */
		 
		if ( $Customer_PK ) {
			$this->Customers->Retrieve ( $Customer_PK );
			$this->Customers->Fetch();
		}
		
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
		$this->Form->AddOptions ( "select[name=SalesRep_Employee_FK]", $employees );
		
		/*
		 * @tutorial Set the target for the form.
		 * 
		 */
		$this->Form->Find ( "form", 0 )->action = $this->GetSys ( "Router" )->Get ( "Base" );
		
		/*
		 * @tutorial Don't forget to set the current context.
		 * 
		 */
		$this->Form->Find( "input[name=Context]", 0 )->value = $this->_Context;
		
		/*
		 * @tutorial Finally, you can automatically synchronize between $_REQUEST data and an optional set of defaults.
		 * @tutorial Be sure to inject any data from the database from the database into the defaults array.
		 * 
		 */
		$defaults = array ( "CustomerName" => "Michael Chisari", "AddressLine2" => "2nd Floor");
		$data = (array) $this->Customers->Get ( "Data" );
		
		$defaults = array_merge ( (array)$defaults, (array)$data );
		
		$this->Form->Synchronize ( $defaults );
		
		$this->_ShowReasons ();
		
		$this->_PrepareMessage();
		
		if ( $this->Customers->Get ( "Customer_PK" ) ) {
			$this->_PrepareEditForm ( );
		} else {
			$this->_PrepareAddForm ( );
		}
		
		return ( true );
	}
	
	function _PrepareEditForm ( ) {
	}
	
	function _PrepareAddForm ( ) {
		
		/*
		 * @tutorial No need to use __() translation function, since the legend element is 
		 * @tutorial automatically 
		 */
		$this->Form->Find ( "[id=example_subtitle]", 0)->innertext = "Add New";
		
		return ( true );
	}
	
	function Cancel ( ) {
		
		/*
		 * @philosophy Keep in mind that this is only *one* way to do this.  You aren't 
		 * @philosophy restricted to using most of the methods presented in this tutorial,
		 * @philosophy and hopefully the framework is flexible enough so that other 
		 * @philosophy methods can be used just as easily.
		 * 
		 */
		$this->GetSys ( "Session" )->Set ( "Message", "Edit Cancelled" );
		
		$this->Go ( "Display" );
		
		return ( true );
	}
	
	function Apply ( ) {
		
		if ( !$this->_Save() ) {
			$this->Go ( "Edit" );
			return ( false );
		}
		
		/*
		 * @tutorial When saving a new record, you can set the Request value to the primary key, so that it switches
		 * @tutorial from an Add form to an Edit form.  This isn't the only way to manage the difference between
		 * @tutorial Edit and Add, but it allows you to have the same view and preparation for both forms.
		 * 
		 */
		$this->GetSys ( "Request" )->Set ( "Customer_PK", $this->Customers->Get ( "Customer_PK" ) );
		
		/*
		 * @tutorial Here you can set a session variable with the result messages
		 * @tutorial The Display function will check the session and display the message it finds.
		 * 
		 */
		$message = __( "Record Applied", array ( "id" => $this->Customers->Get ( "Customer_PK" ) ) ); 
		$this->GetSys ( "Session" )->Set ( "Message", $message );
		
		$this->Go ( "Edit" );
		 
		return ( true );
	}
	
	function Save ( ) {
		
		if ( !$this->_Save() ) {
			$this->Go ( "Edit" );
			return ( false );
		}
		
		$message = __( "Record Saved", array ( "id" => $this->Customers->Get ( "Customer_PK" ) ) ); 
		$this->GetSys ( "Session" )->Set ( "Message", $message );
		
		/*
		 * @tutorial Here we tell the controller to load the Display method.
		 * @tutorial Why don't we just call the task directly?
		 * @tutorial We can, but the event triggers for that task won't occur if we do.
		 * 
		 */
		$this->Go ( "Display" );
		
		/*
		 * @tutorial We can also do a browser redirect.
		 * 
		 * @philosophy This isn't as efficient, though,
		 * @philosophy since we've already loaded the Appleseed framework, 
		 * @philosophy we're loading it again on refresh.
		 * 
		 */
		// $location = $this->GetSys ( "Router" )->Get ( "Base" ); 
		// $this->GetSys ( "Router" )->Redirect ( $location );
		 
		return ( true );
	}
	
	/**
	 * Internal function to save the data.
	 * 
	 * @tutorial This gets called by both Save and Apply
	 * 
	 * @access  public
	 */
	function _Save ( ) {
		
		/*
		 * @tutorial First, we create our model.
		 * @tutorial Then, we synchronize our record data with the information from the URL request.
		 * 
		 */
		$this->Customers = $this->GetModel();
		$this->Customers->Synchronize();
		
		/*
		 * @tutorial There are two ways to validate the data against the database.  You can do it
		 * @tutorial somewhat automatically, using the Validate model function.  
		 * 
		 * @tutorial This looks at the field structure in the database, and makes sure that the 
		 * @tutorial data provided will enter properly.
		 */
		$validate = $this->GetSys ( "Validation" );
		
		$fields = $this->Customers->Get ( "Fields" );
		$data = $this->GetSys ( "Request" )->Get ();
		
		if ( !$validate->Validate ( $fields, $data ) ) return ( false );
		
		/*
		 * @tutorial The other way is to use the Validation class to manually check each field.
		 * @tutorial This is useful for custom validations that can't be expressed in the database.
		 * 
		 * @tutorial The following functions are available in the Validation class:
		 * 
		 * @tutorial Email Url Username Domain Null NotNull Digits Number Required Illegal
		 * @tutorial Length MinLength MaxLength Size MinSize MaxSize
		 */
		
		$email = $this->GetSys ( "Request" )->Get ( "Email" );
		
		if ( $email ) {
			if ( !$validate->Email ( $email ) ) {
				$message = __( "Invalid Email", array ( "email" => $email ) ); 
				$this->GetSys ( "Session" )->Set ( "Message", $message );
				$this->GetSys ( "Session" )->Set ( "Error", true );
			
				return ( false );
			}
		}
		
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
		 
		 return ( true );
	}
	
	function Move_Up ( ) {
		
		$selected = $this->GetSys ( "Request" )->Get ( "Masslist" );
		
		if ( !$selected ) {
			$this->GetSys ( "Session" )->Set ( "Message", "None Selected" );
			$this->GetSys ( "Session" )->Set ( "Error", TRUE );
		}
		
		$this->Go ( "Display" );
	}
	
	function Move_Down ( ) {
		
		$selected = $this->GetSys ( "Request" )->Get ( "Masslist" );
		
		if ( !$selected ) {
			$this->GetSys ( "Session" )->Set ( "Message", "None Selected" );
			$this->GetSys ( "Session" )->Set ( "Error", TRUE );
		}
		
		$this->Go ( "Display" );
	}
	
	function Delete_All ( ) {
		$selected = $this->GetSys ( "Request" )->Get ( "Masslist" );
		
		if ( !$selected ) {
			$this->GetSys ( "Session" )->Set ( "Message", "None Selected" );
			$this->GetSys ( "Session" )->Set ( "Error", TRUE );
			
			$this->Go ( "Display" );
			
			return ( false );
		}
		
		//$criteria['Customer_PK'] = "()" . join ( ', ', array_keys ( $selected ) );
		$criteria['Customer_PK'] = $selected;
		
		$this->Customers = $this->GetModel();
		
		$this->Customers->Delete ( $criteria );
		
		$this->Go ( "Display" );
		
		return ( true );
	}
	
	private function _PrepareMessage ( ) {
		
		if ( $this->Form ) {
			$markup = & $this->Form;
		} else if ( $this->List ) {
			$markup = & $this->List;
		} else {
			return ( false );
		}
		
		if ( $message =  $this->GetSys ( "Session" )->Get ( "Message" ) ) {
			$markup->Find ( "[id=example_message]", 0 )->innertext = $message;
			if ( $error =  $this->GetSys ( "Session" )->Get ( "Error" ) ) {
				$markup->Find ( "[id=example_message]", 0 )->class = "error";
			} else {
				$markup->Find ( "[id=example_message]", 0 )->class = "message";
			}
			$this->GetSys ( "Session" )->Destroy ( "Message ");
			$this->GetSys ( "Session" )->Destroy ( "Error ");
		}
		
		return ( true );
	}
	
	function _ShowReasons ( ) {
		$reasons = $this->GetSys ( "Validation" )->GetReasons();
		
		if ( count ( $reasons ) < 1 ) return ( false );
			
		foreach ( $reasons as $field => $reason ) {
			
			$search = "[name=" . $field . "]";
			$this->Form->Find ( $search, 0 )->outertext .= '<label for="' . $field . '" class="error">' . $reason[0] . '</label>';
		}
		
		$message = __( "Errors On Page" ); 
		$this->GetSys ( "Session" )->Set ( "Message", $message );
		$this->GetSys ( "Session" )->Set ( "Error", true );
		
		return ( true );
	}
	
	private function _PageCalc ( ) {
		
		/*
		 * @tutorial You can use stored session data to do a lot of things.  Here, we skip
		 * @tutorial to the page in the list that had previously been viewed.  So if the user
		 * @tutorial leaves the page and comes back, it stores their position.
		 * 
		 */
		 
		$session = $this->GetSys ( "Session" );
		$session->Context ( $this->Get ( "Context" ) );
		
		$page = (int) $this->GetSys ( "Request" )->Get ( "Page");
		
		if ( $step = $this->GetSys ( "Request" )->Get ( "PaginationStep" ) ) {
			$page = 1;
			$session->Set ( "PaginationStep", $step );
		} else {
			$step = $session->Get ( "PaginationStep", 10 );
		}
		
		if ( !$page ) {
			// Get which page was stored, defaulting to page 1
			$page = (int) $session->Get ( "Page", 1 );
			if ( $page < 1 ) $page = 1;
		} else {
			// Store the current page for retrieval
			$session->Set ( "Page", $page );
		}
		
		// Calculate the starting point in the list.
		$start = ( $page - 1 ) * $step;
		
		$return = array ( $start, $step, $page );
		
		return ( $return );
	}
	
	
}
