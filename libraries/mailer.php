<?php
/**
 * @subpackage   Library
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

require ( ASD_PATH . DS . 'libraries' . DS . 'external' . DS . 'Swift-4.0.6' . DS . 'swift_required.php' );

/** Mailer Class
 * 
 * Mailer and access management.
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cMailer {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function Send ( $pFrom, $pFromName, $pTo, $pToName, $pSubject, $pBody, $pType = "text/html" ) {
		
		// create the mail transport using the 'newInstance()' method
		$transport = Swift_SendmailTransport::newInstance();
		// create the mailer using the 'newInstance()' method
		$mailer = Swift_Mailer::newInstance($transport);
		// create a simple message using the 'newInstance()' method
		$message = Swift_Message::newInstance()
		// specify the subject of the message
		->setSubject($pSubject)
		// specify the From argument
		->setFrom(array($pFrom => $pFromName))
		// specify the To argument
		->setTo(array($pTo => $pToName))
		// build the body part of the message
		->setBody($pBody)
		// set the content type
		->setContentType("text/html");
		
		// send the email message
		if ($mailer->send($message))
		{
			return ( true );
		} else {
			return ( false );
		}
		
	}

}
