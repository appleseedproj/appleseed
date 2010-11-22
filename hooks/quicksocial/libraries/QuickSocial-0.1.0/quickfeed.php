<?php
/**
 * @version      $Id$
 * @package      QuickSocial.Library
 * @subpackage   QuickFeed
 * @copyright    Copyright (C) 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org/quicksocial/
 * @license      GNU Lesser General Public License (LGPL) version 3.0
 */

if ( !class_exists ( 'cQuickSocial' ) ) require ( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'quicksocial.php' );

/** QuickFeed Class
 * 
 * Manages feed data between nodes.
 * 
 * @package     QuickSocial.Framework
 * @subpackage  QuickFeed
 */
class cQuickFeed extends cQuickSocial {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
		
	public function Synchronize ( $pRequestDomain, $pAccount, $pRecipient, $pAction, $pActionOwner, $pActionLink, $pSubjectOwner, $pContext, $pContextOwner, $pContextLink, $pTitle, $pIcon, $pComment, $pDescription, $pIdentifier, $pCreated, $pUpdated ) {
		
		$fCreateLocalToken = $this->GetCallBack ( 'CreateLocalToken' );
		
		if ( !is_callable ( $fCreateLocalToken ) ) {
		    trigger_error('Invalid Callback: CreateLocalToken', E_USER_WARNING);
			return ( false );
		}
		
		list ( $accountUsername, $accountDomain ) = explode ( '@', $pAccount );
		
		$token = @call_user_func ( $fCreateLocalToken, $accountUsername, $pRequestDomain );
		
		$method = 'http';
		
		$data = array (
			'_social' => 'true',
			'_task' => 'feed.synchronize',
			'_token' => $token,
			'_method' => $method,
			'_account' => $pAccount,
			'_recipient' => $pRecipient,
			'_source' => QUICKSOCIAL_DOMAIN,
			'_actionOwner' => $pActionOwner,
			'_action' => $pAction,
			'_actionLink' => $pActionLink,
			'_subjectOwner' => $pSubjectOwner,
			'_contextOwner' => $pContextOwner,
			'_context' => $pContext,
			'_contextLink' => $pContextLink,
			'_title' => $pTitle,
			'_icon' => $pIcon,
			'_comment' => $pComment,
			'_description' => $pDescription,
			'_identifier' => $pIdentifier,
			'_created' => $pCreated,
			'_updated' => $pUpdated
		);
		
		$result = $this->_Communicate ( $pRequestDomain, $data );
		
		return ( $result );
	}
	
	public function ReplyToSynchronize ( ) {
		
		$social = $this->_GET['_social'];
		$task = $this->_GET['_task'];
		
		if ( $social != "true" ) return ( false );
		if ( $task != "feed.synchronize" ) return ( false );
		
		$fFeedSynchronize = $this->GetCallBack ( "FeedSynchronize" );
		
		if ( !is_callable ( $fFeedSynchronize ) ) {
		    trigger_error("Invalid Callback: FeedSynchronize", E_USER_WARNING);
			return ( false );
		}
		
		$token = $this->_GET['_token'];
		
		$Account = $this->_GET['_account'];
		$Recipient = $this->_GET['_recipient'];
		$Action = $this->_GET['_action'];
		$ActionOwner = $this->_GET['_actionOwner'];
		$ActionLink = $this->_GET['_actionLink'];
		$SubjectOwner = $this->_GET['_subjectOwner'];
		$Context = $this->_GET['_context'];
		$ContextOwner = $this->_GET['_contextOwner'];
		$ContextLink = $this->_GET['_contextLink'];
		$Title = $this->_GET['_title'];
		$Icon = $this->_GET['_icon'];
		$Comment = $this->_GET['_comment'];
		$Description = $this->_GET['_description'];
		$Identifier = $this->_GET['_identifier'];
		$Created = $this->_GET['_created'];
		$Updated = $this->_GET['_updated'];
		
		$source = $this->_GET['_source'];
		
		list ( $accountUsername, $accountDomain ) = explode ( '@', $Account );
		
		$verification = $this->Verify( $accountUsername, $source, $token );
		
		if ( $verification->success == 'true' ) {
			$result = @call_user_func ( $fFeedSynchronize, $Recipient, $Action, $ActionOwner, $ActionLink, $SubjectOwner, $Context, $ContextOwner, $ContextLink, $Title, $Icon, $Comment, $Description, $Identifier, $Created, $Updated );
			
			if ( !$result ) {
				$this->_Error ( "Unable To Synchronize" );
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
