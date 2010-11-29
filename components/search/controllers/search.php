<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Search
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Search Component Controller
 * 
 * Search Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Search
 */
class cSearchSearchController extends cController {
	
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
	 * Display the global search box
	 * 
	 * @access  public
	 */
	public function Ask ( $pView = null, $pData = null ) {
		
		if ( !$pView ) $pView = "global";
		
		$this->Ask = $this->GetView ( $pView );
		
		$this->Ask->Display();
		
		return ( true );
	}
	
	public function Index ( $pView = null, $pData = null ) {
		
		$text = $pData['text'];
		$context = $pData['context'];
		$id = $pData['id'];
		
		$this->Model = $this->GetModel();
		
		// Change newlines and tabs into spaces, double spaces into single spaces, remove html tags, and trailing spaces
		$text = str_replace ( "\n", " ", $text );
		$text = str_replace ( "\t", " ", $text );
		$text = str_replace ( "  ", " ", $text );
		$text = strip_tags ( $text );
		$text = ltrim ( rtrim ( $text ) );
		
		$depth = $pData['depth'];
		
		if ( !$depth ) $depth = 5;
		
		$parts = explode ( ' ', $text );
		
		// Only leave "word" characters and whitespace
		$sentence = preg_replace('/[^\w\s]+/', '', strtolower($text));
		
		// Tokenize
		$tokens = explode(' ', $sentence);
		
		for($i = 0; $i < count($tokens); $i++) {
    		for($j = 1; $j <= count($tokens) - $i; $j++) {
    			
    			// If we're above the specified phrase depth, continue.
    			if ( count ( array_slice($tokens, $i, $j) ) > $depth ) continue;
    			
    			// Continue if phrase is less than three characters.
    			if ( strlen ( implode(' ', array_slice($tokens, $i, $j)) ) < 3 ) continue;
    			
    			$phrases[] = implode(' ', array_slice($tokens, $i, $j));
    		}
		}
		
		foreach ( $phrases as $p => $phrase ) {
			$e = hash("sha512", $phrase);
			$encrypted[$e] = $e;
		}
		
		shuffle ( $encrypted );
		
		$keywords = implode ( ' ', $encrypted );
		
		$this->Model->Index ( $context, $id, $keywords );
		
		return ( true );
	}
	
}

