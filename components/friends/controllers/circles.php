<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Friends
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Friends Component Friends Controller
 * 
 * Friends Component Friends Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Friends
 */
class cFriendsCirclesController extends cController {
	
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
		
		if ( ( $this->_Focus->Username != $this->_Current->Username ) or ( $this->_Focus->Domain != $this->_Current->Domain ) ) {
			// @todo: Find a way to load the 403 foundation.
			return ( true );
		}
		
		// Check if circle exists
		if ( !$this->_Load() ) {
			$relocate = '/profile/' . $this->_Focus->Username . '/friends/';
			$this->GetSys ( "Router" )->Redirect ( $relocate );
			return ( true );
		}
			
		$this->View = $this->GetView ( $pView ); 
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	public function Edit ( $pView = null, $pData = array ( ) ) {
		return ( $this->Display ( $pView, $pData ) );
	}
	
	public function Add ( $pView = null, $pData = array ( ) ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$this->Circles = $this->GetModel ( "Circles" );
		
		if ( ( $this->_Focus->Username != $this->_Current->Username ) or ( $this->_Focus->Domain != $this->_Current->Domain ) ) {
			// @todo: Find a way to load the 403 foundation.
			return ( true );
		}
		
		$this->View = $this->GetView ( $pView ); 
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	public function Save ( $pView = null, $pData = array ( ) ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( ( $this->_Focus->Username != $this->_Current->Username ) or ( $this->_Focus->Domain != $this->_Current->Domain ) ) {
			echo __( "Access Denied" );
			exit;
		}
		
		$this->Circles = $this->GetModel ( "Circles" );
		
		$this->Circles->Synchronize();
		$this->Circles->Protect( "tID" );
		
		$id = $this->GetSys ( "Request" )->Get ( "tID" );
		
		if ( $id ) {
			$this->Circles->Save ( array ( "tID" => $id, "userAuth_uID" => $this->_Focus->Id ) );
		} else {
			$this->Circles->Set ( "userAuth_uID", $this->_Focus->Id );
			$this->Circles->Save ( );
		}
		
		$circle = str_replace ( ' ', '-', strtolower ( $this->Circles->Get ( "Name" ) ) );
		$relocate = '/profile/' . $this->_Focus->Username . '/friends/' . $circle;
		$this->GetSys ( "Router" )->Redirect ( $relocate );
		return ( true );
	}
	
	public function Cancel ( $pView = null, $pData = array ( ) ) {
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$relocate = '/profile/' . $this->_Focus->Username . '/friends';
		$this->GetSys ( "Router" )->Redirect ( $relocate );
		return ( true );
	}
	
	public function Remove ( $pView = null, $pData = array ( ) ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( ( $this->_Focus->Username != $this->_Current->Username ) or ( $this->_Focus->Domain != $this->_Current->Domain ) ) {
			echo __( "Access Denied" );
			exit;
		}
		
		$this->Circles = $this->GetModel ( "Circles" );
		
		$this->Circles->Synchronize();
		$this->Circles->Protect( "tID" );
		
		$circle = urldecode ( strtolower ( $this->GetSys ( "Request" )->Get ( "Circle" ) ) );
		
		$this->Circles->Delete ( array ( "Name" => $circle, "userAuth_uID" => $this->_Focus->Id ) );
		
		$relocate = '/profile/' . $this->_Focus->Username . '/friends/';
		$this->GetSys ( "Router" )->Redirect ( $relocate );
		
		return ( true );
	}
	
	/*
	 * Check to see if the circle exists.
	 * 
	 */
	private function _Load ( ) {
		
		$this->Circles = $this->GetModel ( "Circles" );
		
		$currentCircle = str_replace ( '-', ' ', urldecode ( strtolower ( $this->GetSys ( "Request" )->Get ( "Circle" ) ) ) );
		
		$this->Circles->Retrieve ( array ( "userAuth_uID" => $this->_Focus->Id, "Name" => $currentCircle ) );
		
		if ( $this->Circles->Get ( "Total" ) == 0 ) return ( false );
		
		$this->Circles->Fetch();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		$data = (array) $this->Circles->Get ( "Data" );
		
		$this->View->Synchronize ( $data );
		
		$this->View->Find ( "[class=friends-circle-form]", 0 )->action = '/profile/' . $this->_Focus->Username . '/friends/circles/';
		
		$context = $this->Get ( "Context" );
		
		$this->View->Find ( "[name=Context]", 0 )->value = $context;
		
		$this->View->Find ( "[class=friends-circle-title]", 0 )->innertext = $this->Circles->Get ( "Name" );
		
		return ( true );
	}
}
