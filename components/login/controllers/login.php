<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Login
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Login Component Controller
 * 
 * Login Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Login
 */
class cLoginLoginController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		
		if ( !$this->Login = $this->GetView ( $pView ) ) return ( false );
		
		$remote = $this->GetSys ( "Request" )->Get ( "Remote" );
		
		if ( $remote ) {
			$this->Login->Find ( "[id=login_remote_button]", 0)->class = "ui-tabs-selected";
		} else {
			$this->Login->Find ( "[id=login_local_button]", 0)->class = "ui-tabs-selected";
		}
		
		// Set the context for all of the forms.
		$hiddenContexts = $this->Login->Find( "input[name=Context]" );
		foreach ( $hiddenContexts as $hiddenContext ) {
			$hiddenContext->value = $this->_Context;
		}
		
		$this->Login->Display();
		
		return ( true );
	}
	
	function Remote () {
		echo "Remote";
		
		return ( $this->Display() );
	}

	function Login () {
		echo "Login";
		
		return ( $this->Display() );
	}

	function Join () {
		echo "Join";
		
		return ( $this->Display("success") );
	}

}