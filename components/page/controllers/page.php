<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Page
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Page Component Page Controller
 * 
 * Page Component Page Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Page
 */
class cPagePageController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->View = $this->GetView ( $pView ); 
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( !$this->_Current ) {
			$this->View->Find ( "[class=post]", 0 )->outertext = "";
		} else {
			$this->_Prep();
		}
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		$this->View->Find ( "[name=Context]", 0 )->value = $this->Get ( "Context" );
		
	}
	
	public function Share ( $pView = null, $pData = array ( ) ) {
		
		$Comments = $this->GetSys ( "Request" )->Get ( "Comment" );
		$Privacy = $this->GetSys ( "Request" )->Get ( "Privacy" );
		print_r ( $Privacy ); 
		
		return ( true );
	}
	
}

