<?php
/**
 * @version      $Id$
 * @package      QuickSocial.Library
 * @subpackage   QuickRedirect
 * @copyright    Copyright (C) 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org/quicksocial/
 * @license      GNU Lesser General Public License (LGPL) version 3.0
 */
 
if ( !class_exists ( "cQuickSocial" ) ) require ( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'quicksocial.php' );

/** QuickRedirect Class
 * 
 * Redirect abstraction
 * 
 * @package     QuickSocial.Framework
 * @subpackage  QuickSearch
 */
class cQuickRedirect extends cQuickSocial {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function Redirect ( ) {
		$fRedirect = $this->GetCallback ( "Redirect" );
		
		if ( !is_callable ( $fRedirect ) ) { 
			echo "Invalid Callback: Redirect";
			exit;
		}
		
		$action = $this->_GET['_action'];
		$account = $this->_GET['_account'];
		$request = $this->_GET['_request'];
		$source = $this->_GET['_source'];
		
		switch ( $action ) {
			case 'friend.add':
			case 'friend.remove':
			case 'messages.compose':
			case 'messages':
			case 'approval':
			case 'notifications':
			case 'profile':
				$loggedIn = @call_user_func ( $fRedirect, $action, $account, $request, $source );
			break;
			default:
				echo "Invalid Action";
				exit;
			break;
		}
		
		exit;
	}
}