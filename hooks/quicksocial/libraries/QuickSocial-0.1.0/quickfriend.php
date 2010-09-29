<?php
/**
 * @version      $Id$
 * @package      QuickSocial.Library
 * @subpackage   QuickFriend
 * @copyright    Copyright (C) 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org/quicksocial/
 * @license      GNU Lesser General Public License (LGPL) version 3.0
 */
 
if ( !class_exists ( "cQuickSocial" ) ) require ( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'quicksocial.php' );

/** QuickFriend Class
 * 
 * Friend connections between users.
 * 
 * @package     QuickSocial.Framework
 * @subpackage  QuickFriend
 */
class cQuickFriend extends cQuickSocial {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function Friend ( $pAccount, $pRequest ) {
		
		list ( $pAccountUsername, $pAccountDomain ) = explode ( '@', $pAccount );
		list ( $pRequestUsername, $pRequestDomain ) = explode ( '@', $pRequest );
		
		$fCreateLocalToken = $this->GetCallBack ( "CreateLocalToken" );
		
		if ( !is_callable ( $fCreateLocalToken ) ) {
		    trigger_error("Invalid Callback: CreateLocalToken", E_USER_WARNING);
			return ( false );
		}
		
		$token = @call_user_func ( $fCreateLocalToken, $pAccountUsername, $pRequestDomain );
		
		$method = 'http';
		
		$data = array (
			"_social" => "true",
			"_task" => "friend.add",
			"_token" => $token,
			"_method" => $method,
			"_account" => $pAccount,
			"_request" => $pRequest,
			"_source" => QUICKSOCIAL_DOMAIN
		);
		
		$result = $this->_Communicate ( $pRequestDomain, $data );
		
		return ( $result );
	}
	
	public function ReplyToFriend ( ) {
		
		$social = $this->_GET['_social'];
		$task = $this->_GET['_task'];
		
		if ( $social != "true" ) return ( false );
		if ( $task != "friend.add" ) return ( false );
		
		$fFriendAdd = $this->GetCallBack ( "FriendAdd" );
		
		if ( !is_callable ( $fFriendAdd ) ) {
		    trigger_error("Invalid Callback: FriendAdd", E_USER_WARNING);
			return ( false );
		}
		
		$token = $this->_GET['_token'];
		
		$account = $this->_GET['_account'];
		$request = $this->_GET['_request'];
		$source = $this->_GET['_source'];
		
		list ( $accountUsername, $accountDomain ) = explode ( '@', $account );
		list ( $requestUsername, $requestDomain ) = explode ( '@', $request );
		
		$verification = $this->Verify( $accountUsername, $source, $token );
		
		if ( $verification->success == 'true' ) {
			$result = @call_user_func ( $fFriendAdd, $account, $request );
			if ( !$result ) {
				$this->_Error ( "Unable To Add" );
			} else {
				$data['success'] = "true";
				$data['error'] = "";
			}
		} else {
			$this->_Error ( "Invalid Token" );
		}
		
		echo json_encode ( $data );
		exit;
	}

	public function ApproveFriend ( $pAccount, $pRequest ) {
		
		list ( $pAccountUsername, $pAccountDomain ) = explode ( '@', $pAccount );
		list ( $pRequestUsername, $pRequestDomain ) = explode ( '@', $pRequest );
		
		$fCreateLocalToken = $this->GetCallBack ( "CreateLocalToken" );
		
		if ( !is_callable ( $fCreateLocalToken ) ) {
		    trigger_error("Invalid Callback: CreateLocalToken", E_USER_WARNING);
			return ( false );
		}
		
		$token = @call_user_func ( $fCreateLocalToken, $pAccountUsername, $pRequestDomain );
		
		$method = 'http';
		
		$data = array (
			"_social" => "true",
			"_task" => "friend.approve",
			"_token" => $token,
			"_method" => $method,
			"_account" => $pAccount,
			"_request" => $pRequest,
			"_source" => QUICKSOCIAL_DOMAIN
		);
		
		$result = $this->_Communicate ( $pRequestDomain, $data );
		
		return ( $result );
	}
	
	public function ReplyToApproveFriend ( ) {
		
		$social = $this->_GET['_social'];
		$task = $this->_GET['_task'];
		
		if ( $social != "true" ) return ( false );
		if ( $task != "friend.approve" ) return ( false );
		
		$fFriendApprove = $this->GetCallBack ( "FriendApprove" );
		
		if ( !is_callable ( $fFriendApprove ) ) {
		    trigger_error("Invalid Callback: FriendApprove", E_USER_WARNING);
			return ( false );
		}
		
		$token = $this->_GET['_token'];
		
		$account = $this->_GET['_account'];
		$request = $this->_GET['_request'];
		$source = $this->_GET['_source'];
		
		list ( $accountUsername, $accountDomain ) = explode ( '@', $account );
		list ( $requestUsername, $requestDomain ) = explode ( '@', $request );
		
		$verification = $this->Verify( $accountUsername, $source, $token );
		
		if ( $verification->success == 'true' ) {
			$result = @call_user_func ( $fFriendApprove, $account, $request );
			if ( !$result ) {
				$this->_Error ( "Unable To Approve" );
			} else {
				$data['success'] = "true";
				$data['error'] = "";
			}
		} else {
			$this->_Error ( "Invalid Token" );
		}
		
		echo json_encode ( $data );
		exit;
	}

}
