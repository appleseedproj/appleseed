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
 * User Component User Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  User
 */
class cUserUserController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function CreateUserLink ( $pView = null, $pData = array ( ) ) {
		
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$this->View = $this->GetView ( 'userlink' );
		
		$request = $pData['request'];
		
		list ( $requestUsername, $requestDomain ) = explode ( '@', $request );
		
		if ( $requestDomain == ASD_DOMAIN ) {
			$info = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Info', array ( 'account' => $this->_Current->Account, 'source' => ASD_DOMAIN, 'request' => $request ) );
			$this->View->Find ( '.userlink', 0 )->innertext = $info->fullname;
		} else {
			$this->View->Find ( '.userlink', 0 )->innertext = $account;
		}
		
		$this->View->Find ( '.userlink', 0 )->rel = $account;
		$this->View->Find ( '.userlink', 0 )->href = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'User', 'Link', array ( 'account' => $request, 'source' => ASD_DOMAIN ) );
		
		$return = $this->View->Buffer();
		
		return ( $return );
	}
}
