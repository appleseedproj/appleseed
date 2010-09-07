<?php
/**
 * @version      $Id$
 * @package      QuickSocial.Library
 * @subpackage   QuickNode
 * @copyright    Copyright (C) 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org/quicksocial/
 * @license      GNU Lesser General Public License (LGPL) version 3.0
 */

if ( !class_exists ( "cQuickSocial" ) ) require ( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'quicksocial.php' );

/** QuickNode Class
 * 
 * Node discovery and information.
 * 
 * @package     QuickSocial.Framework
 * @subpackage  QuickNode
 */
class cQuickNode extends cQuickSocial {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function Discover ( $pTarget ) {
		
		$fCreateLocalToken = $this->GetCallBack ( "CreateLocalToken" );
		
		if ( !is_callable ( $fCreateLocalToken ) ) {
		    trigger_error("Invalid Callback: CreateLocalToken", E_USER_WARNING);
			return ( false );
		}
		
		$token = @call_user_func ( $fCreateLocalToken, null, $pTarget );
		
		$data = array (
			"_social" => "true",
			"_task" => "node.discover",
			"_source" => $_SERVER['HTTP_HOST'],
			"_token" => $token
		);
		
		$result = $this->_Communicate ( $pTarget, $data );
		
		return ( $result );
	}
	
	public function ReplyToDiscover ( ) {
		$social = $_GET['_social'];
		$task = $_GET['_task'];
		
		if ( $social != "true" ) return ( false );
		if ( $task != "node.discover" ) return ( false );
		
		$source = $_GET['_source'];
		$token = $_GET['_token'];
		
		$verified = false;
		if ( ( $source ) && ( $token ) ) {
			$verified = $this->Verify ( null, $source, $token );
		}
		
		$fNodeInformation = $this->GetCallback ( "NodeInformation" );
		
		if ( !is_callable ( $fNodeInformation ) ) $this->_Error ( "Invalid Callback: NodeInformation" );
		
		$data = @call_user_func ( $fNodeInformation, $source, $verified );
		
		if ( !is_array ( $data ) ) $this->_Error ( "Invalid Callback Return" );
		
		$data['version'] = QUICKSOCIAL_VERSION;
		$data['success'] = "true";
		$data['error'] = "";
		
		echo json_encode ( $data );
		exit;
	}

}
