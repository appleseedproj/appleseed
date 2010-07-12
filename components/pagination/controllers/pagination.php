<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Pagination
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Pagination Component Controller
 * 
 * Pagination Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Pagination
 */
class cPaginationController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		
		/*
		 * @tutorial 
		 */
		 
		parent::__construct( );
	}
	
	/**
	 * Display the default view
	 * 
	 * @access  public
	 */
	public function Display ( $pView = null, $pData = null ) {
		
		$this->List = $this->GetView ( "pagination" );
		
		$this->_PreparePagination ( $pData );
		
		$this->List->Display ();
		
		return ( true );
	}
	
	private function _PreparePagination ( $pData ) {
		
		$start = $pData['start'];
		$step = $pData['step'];
		$total = $pData['total'];
		$uselink = $pData['link'];
		
		$currentpage = ($start / $step ) + 1;
		
		$prevpage = $currentpage - 1;  
		if ( $prevpage < 1) $prevpage = 1;
		
		$lastpage = ceil ($total / $step );
		$nextpage = $currentpage + 1;
		if ( $nextpage > $lastpage) $nextpage = $lastpage;
		
		$pages = array ();
		
		$base =$this->GetSys ( "Router")->Get ("Base" );
		
		$page = 1; $p = 0;
		while ( $p < $total ) {
			if ( $uselink ) {
				$firstlink = preg_replace ( '/\(\.\*\)/', 1, $uselink );
				$prevlink = preg_replace ( '/\(\.\*\)/', $prevpage, $uselink );
				$nextlink = preg_replace ( '/\(\.\*\)/', $nextpage, $uselink );
				$lastlink = preg_replace ( '/\(\.\*\)/', $lastpage, $uselink );
				$link = preg_replace ( '/\(\.\*\)/', $page, $uselink );
			} else {
				$firstlink = $base . "Page,1";
				$prevlink = $base . "Page," . $prevpage;
				$nextlink = $base . "Page," . $nextpage;
				$lastlink = $base . "Page," . $lastpage;
				$link = $base . "Page," . $page;
			}
			if ( $page == $currentpage ) {
				$outertext .= "<li class=\"selected\"><span><a href=\"$link\">" . $page . "</a></span></li>";
			} else {
				$outertext .= "<li><span><a href=\"$link\">" . $page . "</a></span></li>";
			}
			$p += $step;
			$page += 1;
		}
		
		$this->List->Find ( "li[class=prev]", 0)->outertext .= $outertext;
		
		$this->List->Reload();
		
		$this->List->RemoveElement ( "li[class=page]" );
		
		$this->List->Find ( "li[class=first] a", 0)->href = $firstlink;
		$this->List->Find ( "li[class=prev] a", 0)->href = $prevlink;
		$this->List->Find ( "li[class=next] a", 0)->href = $nextlink;
		$this->List->Find ( "li[class=last] a", 0)->href = $lastlink;
		
		if ( $currentpage <= 1)
			$this->List->Find ( "li[class=first]", 0)->innertext =  $this->List->Find ( "li[class=first] a span", 0)->outertext;
		
		if ( $currentpage <= $prevpage)
			$this->List->Find ( "li[class=prev]", 0)->innertext =  $this->List->Find ( "li[class=prev] a span", 0)->outertext;
		
		if ( $currentpage >= $nextpage)
			$this->List->Find ( "li[class=next]", 0)->innertext =  $this->List->Find ( "li[class=next] a span", 0)->outertext;
		
		if ( $currentpage >= $lastpage)
			$this->List->Find ( "li[class=last]", 0)->innertext =  $this->List->Find ( "li[class=last] a span", 0)->outertext;
		
		return ( true );
	}
	
	function Cancel () {
	}
	
}

