<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Journal
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Journal Component Controller
 * 
 * Journal Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Journal
 */
class cJournalEntryController extends cController {
	
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
		
		$Entry = $this->GetSys ( 'Request' )->Get ( 'Entry' );
		
		$this->View = $this->GetView ( 'entry' );
		
		$this->Model = $this->GetModel();
		
		$this->Model->Load ( $this->_Focus->Id, $Entry );
		
		$this->View->Find ( '.title', 0 )->innertext = $this->Model->Get ( 'Title' );
		$this->View->Find ( '.permalink-link', 0 )->href = '/profile/' . $this->_Focus->Username . '/journal/' . $this->Model->Get ( 'Identifier' );
		$this->View->Find ( '.permalink-link', 0 )->innertext = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/journal/' . $this->Model->Get ( 'Identifier' );
		$this->View->Find ( '.body', 0 )->innertext = $this->GetSys ( 'Render' )->Format ( $this->Model->Get ( 'Body' ) );
		
		$this->View->Find ( '.edit', 0 )->href = '/profile/' . $this->_Focus->Username . '/journal/edit/' . $this->Model->Get ( 'Identifier' );
		
		$this->View->Display();
		
		return ( true );
	}
	
	public function Add ( $pView = null, $pData = array ( ) ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$this->View = $this->GetView ( 'edit' );
		
		$this->_PrepAdd();
		
		$this->View->Display();
		
		return ( true );
	}
	
	public function Edit ( $pView = null, $pData = array ( ) ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$Identifier = $this->GetSys ( 'Request' )->Get ( 'Identifier' );
		
		$this->View = $this->GetView ( 'edit' );
		
		$this->Model = $this->GetModel ();
		
		$this->Model->Load ( $this->_Focus->Id, $Identifier );
		
		$this->_PrepEdit();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _PrepAdd ( ) {
		
		$this->View->Find ( '.journal', 0 )->action = "/profile/" . $this->_Focus->Username . '/journal/save/';
		
		$privacyData = array ( 'start' => $start, 'step'  => $step, 'total' => $total, 'link' => $link );
		$privacyControls =  $this->View->Find ('.privacy');
		
		foreach ( $privacyControls as $c => $control ) {
			$control->innertext = $this->GetSys ( 'Components' )->Buffer ( 'privacy', $pageData ); 
		}
		
		$Contexts =  $this->View->Find ( '[name=Context]' );
		foreach ( $Contexts as $c => $context ) {
			$context->value = $this->Get ( 'Context' );
		}
		
		$this->View->Find ( '.remove', 0 )->outertext= "";
		
		return ( true );
	}
	
	private function _PrepEdit ( ) {
		
		$Identifier = $this->GetSys ( 'Request' )->Get ( 'Identifier' );
		
		$this->View->Find ( '.journal', 0 )->action = "/profile/" . $this->_Focus->Username . '/journal/' . $Identifier;
		
		$privacyData = array ( 'start' => $start, 'step'  => $step, 'total' => $total, 'link' => $link );
		$privacyControls =  $this->View->Find ('.privacy');
		
		foreach ( $privacyControls as $c => $control ) {
			$control->innertext = $this->GetSys ( 'Components' )->Buffer ( 'privacy', $pageData ); 
		}
		
		$Contexts =  $this->View->Find ( '[name=Context]' );
		foreach ( $Contexts as $c => $context ) {
			$context->value = $this->Get ( 'Context' );
		}
		
		$this->View->Find ( '[name=Title]', 0 )->value = $this->Model->Get ( 'Title' );
		$this->View->Find ( '[name=Body]', 0 )->innertext = $this->Model->Get ( 'Body' );
		
		return ( true );
	}
	
	public function Save ( ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$this->Model = $this->GetModel ();
		
		$Body = $this->GetSys ( 'Request' )->Get ( 'Body' );
		$Title = $this->GetSys ( 'Request' )->Get ( 'Title' );
		$Identifier = $this->GetSys ( 'Request' )->Get ( 'Identifier' );
		
		$Identifier = $this->Model->Store ( $this->_Focus->Id, $Identifier, $Title, $Body );
		
		$location = '/profile/' . $this->_Focus->Username . '/journal/' . $this->Model->Get ( 'Identifier' );
		
		header ( 'Location: ' . $location );
		exit;
	}
	
	public function Cancel ( ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$Entry = $this->GetSys ( 'Request' )->Get ( 'Entry' );
		
		$location = '/profile/' . $this->_Focus->Username . '/journal/' . $Entry;
		
		header ( 'Location: ' . $location );
		exit;
	}
	
}
