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
 * User Component Invites Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  User
 */
class cUserInvitesController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( $this->_Focus->Account != $this->_Current->Account ) return ( false );
		
		$this->Model = $this->GetModel ( 'Invites' );
		
		$Count = $this->Model->CountInvites( $this->_Focus->Id );
		
		if ( $Count < 1 ) return ( false );
		
		$this->View = $this->GetView ( $pView );
		
		$this->View->Find ( '.invite-count', 0 )->innertext = __( 'You Have Invites', array ( 'count' => $Count ) );
		$this->View->Find ( '[name=Context]', 0 )->value = $this->Get ( 'Context' );
		
		$this->View->Display();
		
		return ( true );
	}
	
	public function Invite ( $pView = null, $pData = array ( ) ) {
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$this->Model = $this->GetModel ( 'Invites' );
		
		$Email = $this->GetSys ( 'Request' )->Get ( 'Email' );
		$Count = $this->Model->CountInvites( $this->_Focus->Id );
		
		$this->View = $this->GetView ( $pView );
		
		$this->View->Find ( '.invite-count', 0 )->innertext = __( 'Invite Has Been Sent', array ( 'count' => $Count, 'email' => $Email ) );
		$this->View->Find ( '[name=Context]', 0 )->value = $this->Get ( 'Context' );
		
		if ( strstr ( $Email, ',' ) ) {
			$Emails = explode ( ',', $Email );
			foreach ( $Emails as $e => $Email ) {
				if ( $Invite = $this->_Invite ( $Email ) ) {
					$this->_Email ( $Email, $Invite );
				}
			}
		} else {
			if ( $Invite = $this->_Invite ( $Email ) ) {
				$this->_Email ( $Email, $Invite );
			}
		}
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Invite ( $pAddress ) {
		
		$Validate = $this->GetSys ( "Validation" );
		if ( !$Validate->Email ( $pAddress ) ) {
			// Throw an error.
			return ( false );
		}
		
		// User with that email is already a member of this site.
		if ( $this->Model->Active ( $pAddress ) ) {
			// Throw an error.
			
			// Return the existing Invite
			return ( false );
		}
		
		if ( $Invite = $this->Model->Invited ( $pAddress, $this->_Focus->Id ) ) {
			// Throw an error.
			
			// Return the existing Invite
			return ( $Invite );
		}
		
		if ( !$Invite = $this->Model->InviteCode ( $pAddress, $this->_Focus->Id ) ) {
			// Throw an error.
			return ( false );
		}
		
		return ( $Invite );
	}
	
	private function _Email ( $pAddress, $pInvite ) {
		$data = array ( 'account' => $this->_Current->Account, 'source' => ASD_DOMAIN, 'request' => $this->_Current->Account );
		$CurrentInfo = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Info', $data );
		$SenderFullname = $CurrentInfo->fullname;
		$SenderNameParts = explode ( ' ', $CurrentInfo->fullname );
		$SenderFirstName = $SenderNameParts[0];
		
		$SenderAccount = $this->_Current->Account;
		
		$RecipientEmail = $pAddress;
		$MailSubject = __( "Someone Sent An Invite", array ( "fullname" => $SenderFullname ) );
		$Byline = __( "Sent An Invite" );
		$Subject = __( "You Are Invited", array ( 'domain' => ASD_DOMAIN ) );
		$Link = 'http://' . ASD_DOMAIN . '/join/' . $pInvite;
		$Body = __( "Invite Description", array ( 'fullname' => $fullname, 'domain' => ASD_DOMAIN, 'firstname' => $senderFirstname, 'link' => $Link ) );
		$LinkDescription = __( "Click Here", array ( 'domain' => ASD_DOMAIN ) );
		
		$Message = array ( 'Type' => 'User', 'SenderFullname' => $SenderFullname, 'SenderAccount' => $SenderAccount, 'RecipientEmail' => $RecipientEmail, 'MailSubject' => $MailSubject, 'Byline' => $Byline, 'Subject' => $Subject, 'Body' => $Body, 'LinkDescription' => $LinkDescription, 'Link' => $Link );
		$this->Talk ( 'Postal', 'Send', $Message );
		
		return ( true );
	} 
	
	public function AddInvites ( $pView = null, $pData = array ( ) ) {
		
		$UserId = $pData['UserId'];
		$Count = $pData['Count'];
		
		$this->Model = $this->GetModel ( 'Invites' );
		
		$this->Model->AddInvites ( $UserId, $Count );
		
		return ( true );
		
	}
}
