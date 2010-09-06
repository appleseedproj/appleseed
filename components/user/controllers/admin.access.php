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
		
		$this->Model = $this->GetModel ( "Access" );
		
		$session = $this->GetSys ( "Session" );
		$session->Context ( $this->Get ( "Context" ) );
		
		$saved = $session->Get();
		
		list ( $start, $step, $page ) = $this->_PageCalc();
		
		// Retrieve from the db, using no criteria except for the pagination settings.
		$this->Model->Retrieve( null, null, array ( "start" => $start, "step" => $step ) );
		
		$tbody = $this->List->Find ( "[id=customer-table-body] tbody tr", 0);
		
		$baseURL = $this->GetSys ( "Router" )->Get ( "Base" );
		$this->List->Find ( "form", 0 )->action = $this->GetSys ( "Router" )->Get ( "Base" );
		
		$this->List->Find( "input[name=Context]", 0 )->value = $this->_Context;
		
		$row = $this->List->Copy ( "[id=customer-table-body] tbody tr" )->Find ( "tr", 0 );
		
		$tbody->innertext = " " ;
		
		$cellAccess_PK = $row->Find( "[class=Access_PK]", 0 );
		$cellAccount = $row->Find( "[class=Account]", 0 );
		$cellContactName = $row->Find( "[class=ContactName]", 0 );
		$cellRead = $row->Find( "[class=Read]", 0 );
		$cellWrite = $row->Find( "[class=Write]", 0 );
		$cellAdmin = $row->Find( "[class=Admin]", 0 );
		$cellLocation = $row->Find( "[class=Location]", 0 );
		$cellInheritance = $row->Find( "[class=Inheritance]", 0 );
		$cellMasslist = $row->Find( "[class=Masslist] input[type=checkbox]", 0 );
		
		$YESNO = array ( "0" => "Option No", "1" => "Option Yes" );
		
		$customerName = $this->Model->Get ( 'ContactFirstName' ) . ' ' . $this->Model->Get ( "ContactLastName" );
		
		while ( $this->Model->Fetch() ) {
			
		    $oddEven = empty($oddEven) || $oddEven == 'even' ? 'odd' : 'even';
			
			$row->class = $oddEven;
			
			$id = $this->Model->Get ( 'Access_PK' );
			
			$url = $baseURL . "edit" . DS . $id . DS;
			
			$account = $this->Model->Get ( 'Account' );
			$read = $this->Model->Get ( 'Read' );
			$write = $this->Model->Get ( 'Write' );
			$admin = $this->Model->Get ( 'Admin' );
			$location = $this->Model->Get ( 'Location' );
			$inheritance = $this->Model->Get ( 'Inheritance' );
			
			$context = $this->_Component . '.' . strtolower ( __FUNCTION__ );
			
			$cellAccess_PK->innertext = $this->List->Link ( $id, $url );
			$cellAccount->innertext = $this->List->Link ( $account, $url );
			$cellRead->innertext = $this->List->Link ( $YESNO[$read], $url );
			$cellWrite->innertext = $this->List->Link ( $YESNO[$write], $url );
			$cellAdmin->innertext = $this->List->Link ( $YESNO[$admin], $url );
			$cellLocation->innertext = $this->List->Link ( $location, $url );
			$cellInheritance->innertext = $this->List->Link ( $YESNO[$inheritance], $url );
			$cellMasslist->name = "Masslist[" . $id . "]";
			
			$customerName = $this->Model->Get ( 'ContactFirstName' ) . ' ' . $this->Model->Get ( "ContactLastName" );
			
		    $tbody->innertext .= $row->outertext;
		}
		
		$link = $this->GetSys ( "Router" )->Get ( "Base" ) . '(.*)';
		$total = $this->Model->Get ( "Total" );
		
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
	
	private function _PageCalc ( ) {
		
		$session = $this->GetSys ( "Session" );
		$session->Context ( $this->Get ( "Context" ) );
		
		$page = $this->GetSys ( "Request" )->Get ( "Page");
		
		if ( $step = $this->GetSys ( "Request" )->Get ( "PaginationStep" ) ) {
			$page = 1;
			$session->Set ( "PaginationStep", $step );
		} else {
			$step = $session->Get ( "PaginationStep", 10 );
		}
		
		if ( !$page ) {
			// Get which page was stored, defaulting to page 1
			$page = $session->Get ( "Page", 1 );
		} else {
			// Store the current page for retrieval
			$session->Set ( "Page", $page );
		}
		
		// Calculate the starting point in the list.
		$start = ( $page - 1 ) * $step;
		
		$return = array ( $start, $step, $page );
		
		return ( $return );
	}
	
	public function Edit ( ) {
		
		$this->Model = $this->GetModel ( "Access" );
		
		$this->_PrepareForm();
		
		$this->Form->Display();
		
		unset ( $this->Form );
		
		return ( true );
	}
	
	public function Add ( ) {
		
		$this->Model = $this->GetModel ( "Access" );
		
		$this->_PrepareForm();
		
		$this->Form->Display();
		
		unset ( $this->Form );
		
		return ( true );
	}
	
	public function _PrepareForm() {
		
		$Access_PK = $this->GetSys ( "Request" )->Get ( 'Access_PK', $this->Model->Get ( "Access_PK" ) );
		
		$this->Form = $this->GetView ( "admin.access.form" );
		
		$this->Form->Find ( "form", 0 )->action = $this->GetSys ( "Router" )->Get ( "Base" );
		$this->Form->Find( "input[name=Context]", 0 )->value = $this->_Context;
		
		$this->_PrepareMessage();
		
		if ( $Access_PK ) {
			$this->_PrepareEditForm ( );
		} else {
			$this->_PrepareAddForm ( );
		}
		
		return ( true );
	}
	
	function Apply ( ) {
		
		if ( !$this->_Save() ) {
			$this->Go ( "Edit" );
			return ( false );
		}
		
		$this->GetSys ( "Request" )->Set ( "Access_PK", $this->Model->Get ( "Access_PK" ) );
		
		$message = __( "Record Applied", array ( "id" => $this->Model->Get ( "Access_PK" ) ) ); 
		$this->GetSys ( "Session" )->Set ( "Message", $message );
		
		$this->Go ( "Edit" );
		 
		return ( true );
	}
	
	function Save ( ) {
		
		if ( !$this->_Save() ) {
			$this->Go ( "Edit" );
			return ( false );
		}
		
		$message = __( "Record Saved", array ( "id" => $this->Model->Get ( "Access_PK" ) ) ); 
		$this->GetSys ( "Session" )->Set ( "Message", $message );
		
		$this->Go ( "Display" );
		
		return ( true );
	}
	
	/**
	 * Internal function to save the data.
	 * 
	 * @access  public
	 */
	function _Save ( ) {
		
		$this->Model = $this->GetModel ( "Access" );
		$this->Model->Synchronize();
		
		if ( $this->Model->Get ( 'Read' ) == 'on' ) $this->Model->Set ( 'Read', true ); else $this->Model->Set ( 'Read', false );
		if ( $this->Model->Get ( 'Write' ) == 'on' ) $this->Model->Set ( 'Write', true ); else $this->Model->Set ( 'Write', false );
		if ( $this->Model->Get ( 'Admin' ) == 'on' ) $this->Model->Set ( 'Admin', true ); else $this->Model->Set ( 'Admin', false );
		if ( $this->Model->Get ( 'Inheritance' ) == 'on' ) $this->Model->Set ( 'Inheritance', true ); else $this->Model->Set ( 'Inheritance', false );
		
		$validate = $this->GetSys ( 'Validation' );
		
		$fields = $this->Model->Get ( 'Fields' );
		$data = $this->GetSys ( 'Request' )->Get ();
		
		if ( !$validate->Validate ( $fields, $data ) ) {
			return ( false );
		}
		
		$this->Model->Save();
		
		return ( true );
	}
	
	function Cancel ( ) {
		
		$this->GetSys ( 'Session' )->Set ( 'Message', 'Edit Cancelled' );
		
		$this->Go ( "Display" );
		
		return ( true );
	}
	
	function Delete_All ( ) {
		$selected = $this->GetSys ( "Request" )->Get ( "Masslist" );
		
		if ( !$selected ) {
			$this->GetSys ( "Session" )->Set ( "Message", "None Selected" );
			$this->GetSys ( "Session" )->Set ( "Error", TRUE );
			
			$this->Go ( "Display" );
			
			return ( false );
		}
		
		$criteria['Access_PK'] = $selected;
		
		$this->Model = $this->GetModel( "Access" );
		
		$this->Model->Delete ( $criteria );
		
		$count = count ( $selected );
		
		$this->GetSys ( "Session" )->Set ( "Message", __ ("Selected Items Deleted", array ( "count" => $count ) ) );
		$this->GetSys ( "Session" )->Set ( "Error", TRUE );
		
		$this->Go ( "Display" );
		
		return ( true );
	}
	
	
	
	private function _PrepareEditForm ( ) {
		
		$Access_PK = $this->GetSys ( "Request" )->Get ( 'Access_PK', $this->Model->Get ( "Access_PK" ) );
		
		$this->Model->Retrieve ( $Access_PK );
		
		$this->Model->Fetch();
		$defaults = (array) $this->Model->Get ( "Data" );
		$this->Form->Synchronize ( $defaults );
		
	}
	
	private function _PrepareAddForm ( ) {
		
		$this->Form->Find ( "[id=edit-subtitle]", 0)->innertext = "New Access Subtitle";
		$this->Form->Find ( "form[id=user-access-edit] fieldset p", 0)->innertext = "New Access Description";
		
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
			$markup->Find ( "[id=user-access-message]", 0 )->innertext = $message;
			if ( $error =  $this->GetSys ( "Session" )->Get ( "Error" ) ) {
				$markup->Find ( "[id=user-access-message]", 0 )->class = "error";
			} else {
				$markup->Find ( "[id=user-access-message]", 0 )->class = "message";
			}
			$this->GetSys ( "Session" )->Delete ( "Message ");
			$this->GetSys ( "Session" )->Delete ( "Error ");
		}
		
		return ( true );
	}
}