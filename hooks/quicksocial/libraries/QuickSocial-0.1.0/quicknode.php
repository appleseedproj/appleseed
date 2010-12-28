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
		parent::__construct();
	}
	
	public function Synchronize ( $pTarget, $pDescription, $pMethods ) {
		
		$fCreateLocalToken = $this->GetCallBack ( "CreateLocalToken" );
		$fLoadNodeNetwork = $this->GetCallBack ( "LoadNodeNetwork" );
		$fStoreNodeNetwork = $this->GetCallBack ( "StoreNodeNetwork" );
		$fNodeInformation = $this->GetCallback ( "NodeInformation" );
		
		if ( !is_callable ( $fCreateLocalToken ) ) {
		    trigger_error("Invalid Callback: CreateLocalToken", E_USER_WARNING);
			return ( false );
		}
		
		if ( !is_callable ( $fLoadNodeNetwork ) ) {
		    trigger_error("Invalid Callback: LoadNodeNetwork", E_USER_WARNING);
			return ( false );
		}
		
		if ( !is_callable ( $fStoreNodeNetwork ) ) {
		    trigger_error("Invalid Callback: StoreNodeNetwork", E_USER_WARNING);
			return ( false );
		}
		
		if ( !is_callable ( $fNodeInformation ) ) {
		    trigger_error("Invalid Callback: NodeInformation", E_USER_WARNING);
			return ( false );
		}
		
		$token = @call_user_func ( $fCreateLocalToken, null, $pTarget );
		
		$info = @call_user_func ( $fNodeInformation );
		
		list ( $trusted, $discovered, $blocked ) = @call_user_func ( $fLoadNodeNetwork );
		
		$data = array (
			"_social" => "true",
			"_task" => "node.synchronize",
			"_source" => QUICKSOCIAL_DOMAIN,
			"_token" => $token,
			"_methods" => $info['methods'],
			"_description" => $info['description'],
			"_version" => QUICKSOCIAL_VERSION,
			"_trusted" => $trusted,
			"_discovered" => $discovered,
			"_blocked" => $blocked
		);
		
		$result = $this->_Communicate ( $pTarget, $data );
		
		if ( $result->success == 'true' ) {
			@call_user_func ( $fStoreNodeNetwork, $pTarget, $result->methods, $result->description, $result->version, $result->trusted, $result->discovered, $result->blocked );
		}
		
		return ( $result );
	}
	
	public function ReplyToSynchronize ( ) {
		$social = $this->_GET['_social'];
		$task = $this->_GET['_task'];
		
		if ( $social != "true" ) return ( false );
		if ( $task != "node.synchronize" ) return ( false );
		
		$source = $this->_GET['_source'];
		$token = $this->_GET['_token'];
		
		$verified = $this->Verify ( null, $source, $token );
		if ( $verified->success != 'true' ) {
			$this->_Error ( 'Invalid Node' );
			exit;
		}
		
		$fNodeInformation = $this->GetCallback ( "NodeInformation" );
		if ( !is_callable ( $fNodeInformation ) ) $this->_Error ( "Invalid Callback: NodeInformation" );
		
		$fLoadNodeNetwork = $this->GetCallback ( "LoadNodeNetwork" );
		if ( !is_callable ( $fLoadNodeNetwork ) ) $this->_Error ( "Invalid Callback: LoadNodeNetwork" );
		
		$fStoreNodeNetwork = $this->GetCallback ( "StoreNodeNetwork" );
		if ( !is_callable ( $fStoreNodeNetwork ) ) $this->_Error ( "Invalid Callback: StoreNodeNetwork" );
		
		$data = @call_user_func ( $fNodeInformation, $source, $verified );
		
		if ( !is_array ( $data ) ) $this->_Error ( "Invalid Callback Return" );
		
		$new_methods = $this->_GET['_methods'];
		$new_description = $this->_GET['_description'];
		$new_version = $this->_GET['_version'];
		$new_trusted = $this->_GET['_trusted'];
		$new_discovered = $this->_GET['_discovered'];
		$new_blocked = $this->_GET['_blocked'];
		
		list ( $trusted, $discovered, $blocked ) = @call_user_func ( $fLoadNodeNetwork );
		
		@call_user_func ( $fStoreNodeNetwork, $source, $new_methods, $new_description, $new_version, $new_trusted, $new_discovered, $new_blocked );
		
		$data['trusted'] = $trusted;
		$data['discovered'] = $discovered;
		$data['blocked'] = $blocked;
		
		$data['version'] = QUICKSOCIAL_VERSION;
		$data['success'] = "true";
		$data['error'] = "";
		
		echo json_encode ( $data );
		exit;
	}
} 