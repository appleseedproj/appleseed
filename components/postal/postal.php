<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Postal
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Postal Component
 * 
 * Postal Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Postal
 */
class cPostal extends cComponent {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function Send ( $pData = array ( ) ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		include_once ( ASD_PATH . 'components/postal/controllers/postal.php' );
		$Controller = new cPostalPostalController();
		
		$Type = strtolower ( $pData['Type'] );
		$SenderFullname = $pData['SenderFullname'];
		$SenderAccount = $pData['SenderAccount'];
		$RecipientEmail = $pData['RecipientEmail'];
		$MailSubject = $pData['MailSubject'];
		$Byline = $pData['Byline'];
		$Subject = $pData['Subject'];
		$Body = str_replace ( "\n", "<br />", $pData['Body'] );
		
		$LinkDescription = $pData['LinkDescription'];
		$Link = $pData['Link'];
		$Stamp = $this->GetSys ( 'Date' )->Format ( NOW(), true );
		$data = array ( 'account' => $SenderAccount, 'source' => ASD_DOMAIN );
		$AccountLink = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'User', 'Link', $data );
		
		$View = $Controller->GetView ( $Type );
		
		list ( $username, $domain ) = explode ( '@', $SenderAccount );
		$IconData = array ( 'username' => $username, 'domain' => $domain, 'width' => 64, 'height' => 64 );
		$View->Find ( '.icon', 0 )->src = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Icon', $IconData );	
		
		$View->Find ( '.fullname', 0 )->innertext = $SenderFullname;
		$View->Find ( '.fullname', 0 )->href = $AccountLink;
		$View->Find ( '.account', 0 )->innertext = $SenderAccount;
		$View->Find ( '.account', 0 )->href = $AccountLink;
		$View->Find ( '.byline-description', 0 )->innertext = $Byline;
		$View->Find ( '.subject', 0 )->innertext = $Subject;
		$View->Find ( '.body', 0 )->innertext = $Body;
		
		$View->Find ( '.stamp', 0 )->innertext = $Stamp;
		
		$View->Find ( '.link-description', 0 )->innertext = $LinkDescription;
		$View->Find ( '.link', 0 )->src = $Link;
		$View->Find ( '.link', 0 )->innertext = $Link;
		$View->Find ( '.link', 0 )->href = $Link;
		
		$from = "no-reply@" . ASD_DOMAIN;
		$fromName = "Appleseed";
		$to = $RecipientEmail;
		$toName = $RecipientEmail;
		$subject = $MailSubject;
		$body = $View->Buffer();
		
		$Mailer = $this->GetSys ( "Mailer" );
		
		if ( !$Mailer->Send ( $from, $fromName, $to, $toName, $subject, $body ) ) {
			return ( false );
		}
		
		return ( true );
	}
	
	public function RegisterOptionsArea ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$return = array ();
		
		$return[] = array ( 'title' =>'Notifications', 'class' => 'notifications', 'link' => '/profile/(.*)/options/notifications/' );
		
		return ( $return );
	}
	
}

