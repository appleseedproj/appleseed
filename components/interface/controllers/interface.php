<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Interface
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Interface Component Controller
 * 
 * Interface Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Interface
 */
class cInterfaceInterfaceController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		$Method = $this->GetSys ( 'Request' )->Get ( 'Method' );
		$Component = ucwords ( $this->GetSys ( 'Request' )->Get ( 'Component' ) );
		
		$Data = $this->GetSys ( 'Request' )->Get ();
		
		$Components = $this->GetSys ( 'Components' );
		$ComponentsList = $Components->Get ( 'Config' )->Get ( 'Components' );
		
		if ( !in_array ( strtolower ( $Component ), $ComponentsList ) ) {
			$message = "Component Not Found";
			return ( $this->_Error ( $message ) );
		}
		
		if ( !method_exists ( $Components->$Component, $Method ) ) {
			return ( $this->_Error ( "Method Not Found" ) );
		}
		
		// Set the source
		$Components->$Component->Set ( 'Source', 'Client' );
		
		unset ( $Data['method'] );
		unset ( $Data['component'] );
		
		$result = $Components->$Component->$Method ( $Data );
		
		if ( !is_array ( $result ) ) {
			return ( $this->_Error ( "Invalid Return" ) );
		}
		
		header('Content-type: application/json');
		echo json_encode ( $result );
		
		return ( true );
	}
	
	function _Error ( $pMessage ) {
		
		
		$output = array();
		
		$output['Success'] = false;
		$output['Error'] = $pMessage;
		
		echo json_encode ( $output );
		
		return ( true );
	}

}