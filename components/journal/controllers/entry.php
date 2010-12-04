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
		
		$this->View = $this->GetView ( 'entry' );
		
		$this->Model = $this->GetModel();
		
		$this->_Prep ( );
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		$Entry = urldecode ( $this->GetSys ( 'Request' )->Get ( 'Entry' ) );
		
		if ( !$this->Model->Load ( $this->_Focus->Id, $Entry ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/404.php' );
			return ( false );
		}
		
		$this->View->Find ( '.title', 0 )->innertext = $this->Model->Get ( 'Title' );
		$this->View->Find ( '.permalink-link', 0 )->href = '/profile/' . $this->_Focus->Username . '/journal/' . $this->Model->Get ( 'Identifier' );
		$this->View->Find ( '.permalink-link', 0 )->innertext = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/journal/' . $this->Model->Get ( 'Identifier' );
		$this->View->Find ( '.body', 0 )->innertext = $this->GetSys ( 'Render' )->Format ( $this->Model->Get ( 'Body' ) );
		
		if ( $this->_Current->Account == $this->_Focus->Account ) {
			$this->View->Find ( '.edit', 0 )->href = '/profile/' . $this->_Focus->Username . '/journal/edit/' . $this->Model->Get ( 'Identifier' );
		} else {
			$this->View->Find ( '.edit', 0 )->outertext = ""; 
		}
		
		$this->View->Find ( '.back', 0 )->href = '/profile/' . $this->_Focus->Username . '/journal/';
		
		return ( true );
	}
	
	public function Add ( $pView = null, $pData = array ( ) ) {
		
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}
		
		$this->View = $this->GetView ( 'edit' );
		
		$this->_PrepAdd();
		
		$this->View->Display();
		
		return ( true );
	}
	
	public function Edit ( $pView = null, $pData = array ( ) ) {
		
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}
		
		$Identifier = $this->GetSys ( 'Request' )->Get ( 'Identifier' );
		
		$this->View = $this->GetView ( 'edit' );
		
		$this->Model = $this->GetModel ();
		
		$this->Model->Load ( $this->_Focus->Id, $Identifier );
		
		$this->_PrepEdit();
		
	 	$this->View->Display();
	 	
		return ( true );
	}
	
	private function _PrepAdd ( ) {
		
		$this->View->Find ( '.journal', 0 )->action = "/profile/" . $this->_Focus->Username . '/journal/add';
		
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
		
		$this->View->Find ( '.journal', 0 )->action = "/profile/" . $this->_Focus->Username . '/journal/edit/' . $Identifier;
		
		$privacyData = array ( 'Type' => 'journal', 'Identifier'  => $Identifier );
		$privacyControls =  $this->View->Find ('.privacy');
		
		foreach ( $privacyControls as $c => $control ) {
			$control->innertext = $this->GetSys ( 'Components' )->Buffer ( 'privacy', $privacyData ); 
		}
		
		$Contexts =  $this->View->Find ( '[name=Context]' );
		foreach ( $Contexts as $c => $context ) {
			$context->value = $this->Get ( 'Context' );
		}
		
		if ( $Identifier ) {
			$this->View->Find ( '.preview-title', 0 )->innertext = $this->Model->Get ( 'Title' );
			$this->View->Find ( '.preview-url', 0 )->innertext = str_replace ( ' ', '-', strtolower ( $this->Model->Get ( 'Title' ) ) ) ;
			$this->View->Find ( '.preview', 0 )->innertext = $this->GetSys ( 'Render' )->Format ( $this->Model->Get ( 'Body' ) );
		}
		
		$this->View->Find ( '[name=Title]', 0 )->value = $this->Model->Get ( 'Title' );
		$this->View->Find ( '[name=Body]', 0 )->innertext = $this->Model->Get ( 'Body' );
		
		return ( true );
	}
	
	public function Save ( ) {
		
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}
		
		$this->Model = $this->GetModel ();
		
		$Body = $this->GetSys ( 'Request' )->Get ( 'Body' );
		$Title = $this->GetSys ( 'Request' )->Get ( 'Title' );
		$Identifier = $this->GetSys ( 'Request' )->Get ( 'Identifier' );
		$Privacy = $this->GetSys ( 'Request' )->Get ( 'Privacy' );
		
		$Identifier = $this->Model->Store ( $this->_Focus->Id, $Identifier, $Title, $Body );
		
		$privacyData = array ( 'Privacy' => $Privacy, 'Type' => 'Journal', 'Identifier' => $Identifier );
		$this->GetSys ( 'Components' )->Talk ( 'Privacy', 'Store', $privacyData );
		
		$location = '/profile/' . $this->_Focus->Username . '/journal/' . $this->Model->Get ( 'Identifier' );
		
		$id = $this->Model->Get ( 'Entry_PK' );
		$context = 'journal';
		
		// First 200 characters of the text, without formatting.
		$text = substr ( strip_tags ( $this->GetSys ( 'Render' )->Format ( $Body ) ), 0, 200 );
		// Title, without formatting.
		$text .= ' ' . strip_tags ( $Title );
		
		$this->Talk ( 'Search', 'Index', array ( 'text' => $text, 'context' => $context, 'id' => $id ) );
		
		header ( 'Location: ' . $location );
		exit;
	}
	
	private function _CheckAccess ( ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( ( $this->_Focus->Username != $this->_Current->Username ) or ( $this->_Focus->Domain != $this->_Current->Domain ) ) {
			return ( false );
		}
		
		return ( true );
	}
	
	public function Cancel ( ) {
		
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}
		
		$Identifier = $this->GetSys ( 'Request' )->Get ( 'Identifier' );
		
		if ( $Identifier ) {
			$location = '/profile/' . $this->_Focus->Username . '/journal/' . $Identifier;
		} else {
			$location = '/profile/' . $this->_Focus->Username . '/journal/';
		}
		
		header ( 'Location: ' . $location );
		exit;
	}
	
}
