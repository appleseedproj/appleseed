<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Photos
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Profile Component Profile Controller
 * 
 * Profile Component Profile Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Profile
 */
class cProfileStatusController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->Status = $this->GetView ( $pView ); 
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$this->Model = $this->GetModel ( "Page" );
		$this->Model->RetrieveCurrent( $this->_Focus->uID );
		
		if ( $this->Model->Get ( "Total" ) > 0 ) {
			$this->Model->Fetch();
			$this->Status->Find ( '[class=current]', 0 )->innertext = $this->Model->Get ( "Comment" );
			$this->Status->Find ( '[class=stamp]', 0 )->innertext = $this->Model->Get ( "Stamp" );
			$this->Status->Find ( '[name=Context]', 0 )->value = $this->Get ( "Context" );
			$this->Status->Find ( '[name=Task]', 0 )->value = "Clear";
			$this->Status->Find ( '[class=clear-status]', 0 )->action = $this->GetSys ( "Router" )->Get ( "Request" );
		} else {
			$this->Status->Find ( '[class=clear-status]', 0 )->outertext = "";
			$this->Status->Find ( '[class=stamp]', 0 )->outertext = "";
			$this->Status->Find ( '[class=current]', 0 )->outertext = "";
		}
		
		$this->Status->Find ( '[class=name]', 0 )->innertext = $this->_Focus->Fullname;
		
		$this->Status->Display();
		
		return ( true );
	}
	
	public function Clear ( ) {
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( $this->_Current->Account != $this->_Focus->Account ) {
			$this->GetSys ( "Foundation" )->Redirect ( "common/403.php" );
			return ( false );
		}
		
		$this->Model = $this->GetModel ( "Page" );
		$this->Model->ClearCurrent( $this->_Focus->uID );
		
		$redirect = $this->GetSys ( "Router" )->Get ( "Request" );
		header ( 'Location:' . $redirect );
		exit;
	}
	
}
