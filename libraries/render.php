<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   Library
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

require ( ASD_PATH . DS . 'libraries' . DS . 'external' . DS . 'Textile-2.2' . DS . 'classTextile.php' );

/** Render Class
 * 
 * Handles basic markup rendering 
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cRender extends Textile {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}

	public function LiveLinks ( $pString ) {

		$pString = preg_replace("/([ \t]|^)www\./i", "\\1http://www.", $pString);
		$pString = preg_replace("/([ \t]|^)ftp\./i", "\\1ftp://ftp.", $pString);
		$pString = preg_replace("/(http:\/\/[^ )\r\n!]+)/i", "<a href=\"\\1\">\\1</a>", $pString);
		$pString = preg_replace("/(https:\/\/[^ )\r\n!]+)/i", "<a href=\"\\1\">\\1</a>", $pString);
		$pString = preg_replace("/(ftp:\/\/[^ )\r\n!]+)/i", "<a href=\"\\1\">\\1</a>", $pString);
		$pString = preg_replace("/([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))/i", "<a href=\"mailto:\\1\">\\1</a>", $pString);

		return ( $pString );
	}
	
	public function Format ( $pString ) {
		eval ( GLOBALS );
		
		// Remove first summary marker pair
		$pString = preg_replace ( "/---(.+?)---/is", "$1", $pString, 1 );
		
		$return = ltrim ( rtrim ( $this->TextileThis ( $pString ) ) );
		
		// Add target=__new to all links
		$HTML = $zApp->GetSys ( 'HTML' );
		$HTML->Load ( $return );
		
		$anchors = $HTML->Find ( 'a' );
		
		foreach ( $anchors as $a => $anchor ) {
			$anchor->target = "__new";
		}
		
		$return = $HTML->outertext;
		
		return ( $return );
	}
	
}
