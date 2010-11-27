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
		$pString = eregi_replace(' (((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)', '<a target="_blank" href="\\1">\\1</a>', $pString); 
		$pString = eregi_replace(' ([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)', '\\1<a target="_blank" href="http://\\2">\\2</a>', $pString); 
		$pString = eregi_replace(' ([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})', '<a href="mailto:\\1">\\1</a>', $pString); 
		
		return ( $pString );
	}
	
	public function Format ( $pString ) {
		eval ( GLOBALS );
		
		$return = ltrim ( rtrim ( $this->TextileThis ( $pString ) ) );
		
		// Remove superfluous <p> tags that TextileThis adds.
		$return = preg_replace ( "/<\/p>$/", "", $return );
		$return = preg_replace ( "/^<p>/", "", $return );
		
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