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
		
		$pages = array ();
		
		$base =$this->GetSys ( "Router")->Get ("Base" );
		
		$page = 1; $p = 0;
		while ( $p <= $total ) {
			if ( $uselink ) {
				$link = preg_replace ( '/\(\.\*\)/', $page, $uselink );
			} else {
				$link = $base . "Page," . $page;
			}
			$outertext .= "<li><span><a href=\"$link\">" . $page . "</a></span></li>";
			$p += $step;
			$page += 1;
		}
		
		$this->List->Find ( "li[class=prev]", 0)->outertext .= $outertext;
		
		$this->List->Reload();
		$this->List->RemoveElement ( "li[class=page]" );
		
		// $this->List->Find ( "li[class=page] a", 0)->outertext =  $this->List->Find ( "li[class=page] a", 0)->plaintext;
		
		//$pageLink = $this->List->Find ( "li[class=first] a span", 0)->outertext =  $this->List->Find ( "li[class=first] a span", 0)->plaintext;
		//$pageLink = $this->List->Find ( "li[class=prev] a span", 0)->outertext =  $this->List->Find ( "li[class=prev] a span", 0)->plaintext;
		//$pageLink = $this->List->Find ( "li[class=page] a span", 0)->outertext =  $this->List->Find ( "li[class=page] a span", 0)->plaintext;
		//$pageLink = $this->List->Find ( "li[class=next] a span", 0)->outertext =  $this->List->Find ( "li[class=next] a span", 0)->plaintext;
		//$pageLink = $this->List->Find ( "li[class=last] a span", 0)->outertext =  $this->List->Find ( "li[class=last] a span", 0)->plaintext;
		
	}
	
	function Cancel () {
	}
	
}

