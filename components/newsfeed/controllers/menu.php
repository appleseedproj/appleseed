<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Newsfeed
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Newsfeed Component Menu Controller
 * 
 * Newsfeed Component Menu Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Newsfeed
 */
class cNewsfeedMenuController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->_Current = $this->Talk ( 'User', 'Current' );
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		if ( $this->_Focus->Account != $this->_Current->Account ) {
			$this->GetSys ( "Foundation" )->Redirect ( "common/403.php" );
			return ( false );
		}
		
		$this->Model = $this->GetModel ( 'Incoming' );
		
		$this->View = $this->GetView ( $pView );
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		$Circles = $this->Talk ( 'Friends', 'Circles' );
		
		$Current = urldecode ( str_replace ( '-', ' ' , $this->GetSys ( 'Request' )->Get ( 'Circle' ) ) );
		
		if ( !$Current ) $this->View->Find ( '.friends', 0 )->class .= ' selected ';
		
		$this->View->Find ( '.friends .link', 0 )->href = '/profile/' . $this->_Current->Username . '/news/';
		$this->View->Find ( '.add', 0 )->href = '/profile/' . $this->_Current->Username . '/friends/circles/add';
		
		if ( !$Circles ) return ( false );
		
		$li = $this->View->Find ( '.list', 0);
		
		$row = $this->View->Copy ( '.list' )->Find ( '.circle', 0 );
		
		$rowOriginal = $row->outertext;
		
		$this->View->Find ( '.circle', 0 )->outertext = '';
		
		foreach ($Circles as $c => $circle ) {
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			$row->Find ( '.link', 0 )->innertext = $circle;
			$row->Find ( '.link', 0 )->href = '/profile/' . $this->_Current->Username . '/news/' . $circle;
			if ( $Current == $circle ) $row->Find ( '.item', 0 )->class .= ' selected ';
		    $li->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		$this->View->Reload();
		
		return ( true );
	}
}
