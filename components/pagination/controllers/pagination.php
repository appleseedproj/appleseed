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
class cPaginationPaginationController extends cController {
	
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
		
		switch ( $pView ) {
			case 'pagination':
				$this->List = $this->GetView ( "pagination" );
		
				if ( !$this->_PreparePagination ( $pData ) ) return ( true );
		
				$this->List->Display (); 
			break;
			case 'amount':
				$this->Amount = $this->GetView ( "amount" );
		
				if ( !$this->_PrepareAmount ( $pData ) ) return ( true );
		
				$this->Amount->Display (); 
			break;
		}
		
		return ( true );
	}
	
	private function _PrepareAmount ( $pData ) {
		
		$start = $pData['step'];
		$currentStep = $pData['step'];
		$total = $pData['total'];
		
		if ( $total >= 100 ) 
			$steps = array ( "5" => 5, "10" => 10, "25" => 25, "50" => 50, __( "All Pages" ) => 100000 );
		else if ( $total >= 50 ) 
			$steps = array ( "5" => 5, "10" => 10, "25" => 25, __( "All Pages" ) => 100000 );
		else if ( $total >= 10 )
			$steps = array ( "5" => 5, "10" => 10, __( "All Pages" ) => 100000 );
		else 
			return ( false );
			
		foreach ( $steps as $s => $step) {
			if ( $currentStep == $s ) 
				$selected = ' selected="selected" ';
			else
				$selected = null;
			$this->Amount->Find ( "[name=PaginationStep]", 0)->innertext .= '<option ' . $selected . ' value="' . $step . '">' . $s . "</option>";
		}
		
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
		
		$midpage = ceil ( $lastpage / 2 );
		
		if ( $lastpage == 1 ) return ( false );
		
		$base =$this->GetSys ( "Router")->Get ("Base" );
		
		$this->List->Find ( "li[class=page]", 0)->outertext = "";
		
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
			
			if ( $lastpage <= 10 ) {
				// Show all pages.
				if ( $page == $currentpage ) {
					$outertext .= "<li class=\"selected\"><a href=\"$link\"><span>" . $page . "</span></a></li>";
				} else {
					$outertext .= "<li><a href=\"$link\"><span>" . $page . "</span></a></li>";
				}
			} else {
				// Truncate the pages list for space.
				$acceptable = array ( 1, 2, $currentpage - 1, $currentpage, $currentpage + 1, $midpage - 1, $midpage, $midpage + 1, $lastpage, $lastpage - 1 );
				
				if ( in_array ( $page, $acceptable ) ) {
					if ( $page == $currentpage ) {
						$outertext .= "<li class=\"selected\"><a href=\"$link\"><span>" . $page . "</span></a></li>";
					} else {
						$outertext .= "<li><a href=\"$link\"><span>" . $page . "</span></a></li>";
					}
				}
			}
			$p += $step;
			$page += 1;
		}
		
		$this->List->Find ( "li[class=page]", 0)->outertext .= $outertext;
		
		$this->List->Find ( "li[class=first] a", 0)->href = $firstlink;
		$this->List->Find ( "li[class=prev] a", 0)->href = $prevlink;
		$this->List->Find ( "li[class=next] a", 0)->href = $nextlink;
		$this->List->Find ( "li[class=last] a", 0)->href = $lastlink;
		
		if ( $currentpage <= 1) {
			$this->List->Find ( "li[class=first]", 0)->innertext =  $this->List->Find ( "li[class=first] a span", 0)->outertext;
			$this->List->Find ( "li[class=first]", 0)->class = "disabled";
		}
		
		if ( $currentpage <= $prevpage) {
			$this->List->Find ( "li[class=prev]", 0)->innertext =  $this->List->Find ( "li[class=prev] a span", 0)->outertext;
			$this->List->Find ( "li[class=prev]", 0)->class = "disabled";
		}
		
		if ( $currentpage >= $nextpage) {
			$this->List->Find ( "li[class=next]", 0)->innertext =  $this->List->Find ( "li[class=next] a span", 0)->outertext;
			$this->List->Find ( "li[class=next]", 0)->class = "disabled";
		}
		
		if ( $currentpage >= $lastpage) {
			$this->List->Find ( "li[class=last]", 0)->innertext =  $this->List->Find ( "li[class=last] a span", 0)->outertext;
			$this->List->Find ( "li[class=last]", 0)->class = "disabled";
		}
		
		return ( true );
	}
	
	function Cancel () {
	}
	
}

