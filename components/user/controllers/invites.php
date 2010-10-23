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
		
		$this->Model = $this->GetModel ( 'Invites' );
		
		$Email = $this->GetSys ( 'Request' )->Get ( 'Email' );
		$Count = $this->Model->CountInvites( $this->_Focus->Id );
		
		$this->View = $this->GetView ( $pView );
		
		$this->View->Find ( '.invite-count', 0 )->innertext = __( 'Invite Has Been Sent', array ( 'count' => $Count, 'email' => $Email ) );
		$this->View->Find ( '[name=Context]', 0 )->value = $this->Get ( 'Context' );
		
		$this->View->Display();
		
		return ( true );
	}
	
}
