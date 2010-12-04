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
 * Journal Component Entries Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Journal
 */
class cJournalEntriesController extends cController {
	
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
		
		$this->View = $this->GetView ( "entries" );
		
		$this->Model = $this->GetModel();
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		list ( $this->_PageStart, $this->_PageStep, $this->_Page ) = $this->_PageCalc();
		
		$this->Model->Entries ( $this->_Focus->Id, array ( 'start' => $this->_PageStart, 'step' => $this->_PageStep ) );
		
		$link = '/profile/' . $this->_Focus->Username . '/journal/(.*)';
		
		$pageData = array ( 'start' => $this->_PageStart, 'step'  => $this->_PageStep, 'total' => $this->Model->Get ( "Total" ), 'link' => $link );
		$pageControls =  $this->View->Find ("nav[class=pagination]");
		foreach ( $pageControls as $p => $pageControl ) {
			$pageControl->innertext = $this->GetSys ( "Components" )->Buffer ( "pagination", $pageData ); 
		}
		
		$li = $this->View->Find ( 'ul[class=journal-entries] li', 0);
		
		$row = $this->View->Copy ( '[class=journal-entries]' )->Find ( 'li', 0 );
		
		if ( $this->_Current->Account == $this->_Focus->Account ) {
			$this->View->Find ( '.add', 0 )->href = '/profile/' . $this->_Focus->Username . '/journal/add/';
		} else { 
			$this->View->Find ( '.add', 0 )->outertext = "";
		}
		
		$this->View->Find ( '.rss', 0 )->href = '/profile/' . $this->_Focus->Username . '/journal/rss/';
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		while ( $this->Model->Fetch() ) {
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			
			$url = strtolower ( str_replace ( ' ', '-', $this->Model->Get ( 'Title' ) ) );
			
			$summary = $this->_Summary ( $this->Model->Get ( 'Body' ) );
			
			$row->Find ( '.title-link', 0 )->innertext = $this->Model->Get ( "Title" );
			$row->Find ( '.title-link', 0 )->href = '/profile/' . $this->_Focus->Username . '/journal/' . $url;
			$row->Find ( '.body', 0 )->innertext = $summary;
			
			$username = $this->Model->Get ( 'Submitted_Username');
			$domain = $this->Model->Get ( 'Submitted_Domain');
			
			$data = array ( 'username' => $username, 'domain' => $domain, 'width' => 64, 'height' => 64 );
			$row->Find ( '.icon', 0 )->src = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Icon', $data );
			
			$data = array ( 'account' => $username . '@' . $domain, 'source' => ASD_DOMAIN );
			$row->Find ( '.submitted', 0 )->href = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'User', 'Link', $data );
			
			$data = array ( 'account' => $username . '@' . $domain, 'source' => ASD_DOMAIN );
			$row->Find ( '.fullname', 0 )->href = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'User', 'Link', $data );
			$row->Find ( '.fullname', 0 )->innertext = $username . '@' . $domain;
			
			$row->Find ( '.readmore', 0 )->href = '/profile/' . $this->_Focus->Username . '/journal/' . $url;
			
			$row->Find ( '.created', 0 )->innertext = $this->GetSys ( 'Date' )->Format ( $this->Model->Get ( 'Created' ), true );
			
		    $li->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		return ( true );
	}
	
	private function _Summary ( $pBody ) {
		
		if ( preg_match ( "/---(.+?)---/is", $pBody, $matches ) ) {
			$pBody = $matches[1];
		}
		
		$summary = $this->GetSys ( 'Render' )->Format ( $pBody );
		
		return ( $summary );
		
		/*
		 * This code will automatically reduce the summary to 500 characters
		 * 
		 * Possibly use this for Descriptions for newsfeeds, but not for summaries?
		 * 
		 */
		
		$stripped = strip_tags ( $this->GetSys ( 'Render' )->Format ( $pBody ) );
		
		$end = strlen ( $stripped );
		if ( strlen ( $stripped ) < 500 ) {
			// Return the whole article back.
			return ( $this->GetSys ( 'Render' )->Format ( $pBody ) );
		} else {
			$end = 500;
		}
		
		$start = $end - 5;
		if ( $start < 0 ) $start = 0;
		
		$split = substr_replace ( $stripped, '##%%&&@@', $start, 5 );
		
		list ( $summary, $null ) = explode ( '##%%&&@@', $split );
		
		//$summary = $summary . $split;
		
		if ( strlen ( $stripped ) > strlen ( $summary ) ) $summary .= '...';
		
		$summary = $this->GetSys ( 'Render' )->Format ( $summary );
		
		return ( $summary );
	}
			
	
	private function _PageCalc ( ) {
		
		$page = $this->GetSys ( 'Request' )->Get ( 'Page');
		
		if ( !$page ) $page = 1;
		
		$step = 10;
		
		// Calculate the starting point in the list.
		$start = ( $page - 1 ) * $step;
		
		$return = array ( $start, $step, $page );
		
		return ( $return );
	}
	

}
