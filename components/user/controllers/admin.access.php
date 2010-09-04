<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   User
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** User Component Controller
 * 
 * User Component Admin Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  User
 */
class cUserAdminAccessController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$request = $this->GetSys ( "Request" )->Get();
		
		$task = $this->GetSys ( "Request" )->Get ( "Task" );
		
		$this->List = $this->GetView ( "admin.access" );
		
		$this->Data = $this->GetModel();
		
		$page = $this->GetSys ( "Request" )->Get ( "Page");
		$step = $this->GetSys ( "Request" )->Get ( "step", 10);
		
		$session = $this->GetSys ( "Session" );
		$session->Context ( $this->Get ( "Context" ) );
		
		$saved = $session->Get();
		
		if ( !$page ) {
			// Get which page was stored, defaulting to page 1
			$page = $session->Get ( "Page", 1 );
		} else {
			// Store the current page for retrieval
			$session->Set ( "Page", $page );
		}
		
		// Calculate the starting point in the list.
		$start = ( $page - 1 ) * $step;
		
		// Retrieve from the db, using no criteria except for the pagination settings.
		$this->Data->Retrieve( null, null, array ( "start" => $start, "step" => $step ) );
		
		$tbody = $this->List->Find ( "[id=customer-table-body] tbody tr", 0);
		
		$baseURL = $this->GetSys ( "Router" )->Get ( "Base" );
		$this->List->Find ( "form", 0 )->action = $this->GetSys ( "Router" )->Get ( "Base" );
		
		$this->List->Find( "input[name=Context]", 0 )->value = $this->_Context;
		
		$row = $this->List->Copy ( "[id=customer-table-body] tbody tr" )->Find ( "tr", 0 );
		
		$tbody->innertext = " " ;
		
		$cellAccess_PK = $row->Find( "[class=Access_PK]", 0 );
		$cellAccount = $row->Find( "[class=Account]", 0 );
		$cellContactName = $row->Find( "[class=ContactName]", 0 );
		$cellCountry = $row->Find( "[class=Country]", 0 );
		$cellRead = $row->Find( "[class=Read]", 0 );
		$cellWrite = $row->Find( "[class=Write]", 0 );
		$cellAdmin = $row->Find( "[class=Admin]", 0 );
		$cellInheritance = $row->Find( "[class=Inheritance]", 0 );
		$cellMasslist = $row->Find( "[class=Masslist] input[type=checkbox]", 0 );
		
		$YESNO = array ( "0" => "Option No", "1" => "Option Yes" );
		
		$customerName = $this->Data->Get ( 'ContactFirstName' ) . ' ' . $this->Data->Get ( "ContactLastName" );
		
		while ( $this->Data->Fetch() ) {
			
		    $oddEven = empty($oddEven) || $oddEven == 'even' ? 'odd' : 'even';
			
			$row->class = $oddEven;
			
			$id = $this->Data->Get ( 'Access_PK' );
			
			$url = $baseURL . "edit" . DS . $id . DS;
			
			$account = $this->Data->Get ( 'Account' );
			$country = $this->Data->Get ( 'Country' );
			$read = $this->Data->Get ( 'Read' );
			$write = $this->Data->Get ( 'Write' );
			$admin = $this->Data->Get ( 'Admin' );
			$inheritance = $this->Data->Get ( 'Inheritance' );
			
			$context = $this->_Component . '.' . strtolower ( __FUNCTION__ );
			
			$cellAccess_PK->innertext = $this->List->Link ( $id, $url );
			$cellAccount->innertext = $this->List->Link ( $account, $url );
			$cellCountry->innertext = $this->List->Link ( $country, $url );
			$cellRead->innertext = $this->List->Link ( $YESNO[$read], $url );
			$cellWrite->innertext = $this->List->Link ( $YESNO[$write], $url );
			$cellAdmin->innertext = $this->List->Link ( $YESNO[$admin], $url );
			$cellInheritance->innertext = $this->List->Link ( $YESNO[$inheritance], $url );
			$cellMasslist->name = "Masslist[" . $id . "]";
			
			$customerName = $this->Data->Get ( 'ContactFirstName' ) . ' ' . $this->Data->Get ( "ContactLastName" );
			
		    $tbody->innertext .= $row->outertext;
		}
		
		$link = $this->GetSys ( "Router" )->Get ( "Base" ) . '(.*)';
		$total = $this->Data->Get ( "Total" );
		
		$pageData = array ( 'start' => $start, 'step'  => $step, 'total' => $total, 'link' => $link );
		$pageControls =  $this->List->Find ("nav[class=pagination]");
		foreach ( $pageControls as $p => $pageControl ) {
			$pageControl->innertext = $this->GetSys ( "Components" )->Buffer ( "pagination", $pageData ); 
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
			$this->GetSys ( "Session" )->Delete ( "Message ");
			$this->GetSys ( "Session" )->Delete ( "Error ");
		}
		
		return ( true );
	}
	

}