<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Photos
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Photos Component Navigation Controller
 * 
 * Photos Component Navigation Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Photos
 */
class cPhotosNavigationController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->Navigation = $this->GetView ( $pView ); 
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$Set = $this->GetSys ( 'Request' )->Get ( 'Set' );
		$Photo = $this->GetSys ( 'Request' )->Get ( 'Photo' );

		$focusLinkData = $this->Talk ( 'User', 'Link', array ( 'request' => $this->_Focus->Account ) );
		
		if ( $Photo ) {
			$this->Set = $this->GetModel ( 'Sets' );

			$this->Set->Load ( $this->_Focus->Id, $Set );
			$this->Set->Fetch();
		
			$setlink = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/photos/' . $this->Set->Get ( 'Directory' );
			$setname = $this->Set->Get ( 'Name' );
			$this->Navigation->Find ( '.breadcrumb', 0 )->innertext = __ ( 'Back To Photoset', array ( 'setlink' => $setlink, 'setname' => $setname ) );
		} else if ( $Set ) {
			$setlink = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/photos/';
			$this->Navigation->Find ( '.breadcrumb', 0 )->innertext = __ ( 'Back To Photosets' , array ( 'fullname' => $this->_Focus->Fullname, 'setlink' => $setlink ) );
		} else {
			$this->Navigation->Find ( '.breadcrumb', 0 )->innertext = __ ( 'Back To Profile' , array ( 'userlink' => $focusLinkData['link'] ) );
		}
		
		$this->Navigation->Display();
		
		return ( true );
	}
	
}
