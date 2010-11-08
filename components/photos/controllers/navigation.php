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
			$this->Navigation->Find ( '.breadcrumb', 0 )->innertext = __ ( 'Back To Photoset', array ( 'photosetlink' => 'Photo album name' ) );
		} else if ( $Set ) {
			$this->Navigation->Find ( '.breadcrumb', 0 )->innertext = __ ( 'Back To Photosets' , array ( 'userlink' => $focusLinkData['link'] ) );
		} else {
			$this->Navigation->Find ( '.breadcrumb', 0 )->innertext = __ ( 'Back To Profile' , array ( 'userlink' => $focusLinkData['link'] ) );
		}
		
		$this->Navigation->Display();
		
		return ( true );
	}
	
}
