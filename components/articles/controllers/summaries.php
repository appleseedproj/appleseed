<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Summaries
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Articles Component Controller
 * 
 * Articles Component Summaries Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Articles
 */
class cArticlesSummariesController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->View = $this->GetView ( "summaries" );
		
		$this->Model = $this->GetModel();
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		list ( $this->_PageStart, $this->_PageStep, $this->_Page ) = $this->_PageCalc();
		
		$this->Model->RetrieveArticles ( array ( 'start' => $this->_PageStart, 'step' => $this->_PageStep ) );
		
		$link = '/articles/(.*)';
		
		$pageData = array ( 'start' => $this->_PageStart, 'step'  => $this->_PageStep, 'total' => $this->Model->Get ( "Total" ), 'link' => $link );
		$pageControls =  $this->View->Find ("nav[class=pagination]");
		foreach ( $pageControls as $p => $pageControl ) {
			$pageControl->innertext = $this->GetSys ( "Components" )->Buffer ( "pagination", $pageData ); 
		}
		
		$li = $this->View->Find ( 'ul[class=article-list] li', 0);
		
		$row = $this->View->Copy ( '[class=article-list]' )->Find ( 'li', 0 );
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		while ( $this->Model->Fetch() ) {
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			
			$row->Find ( "[class=title]", 0 )->innertext = $this->Model->Get ( "Title" );
			$row->Find ( "[class=summary]", 0 )->innertext = str_replace ( "\n", "<br />", $this->Model->Get ( "Summary" ) );
			
		    $li->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		return ( true );
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