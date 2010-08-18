<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Nicedit Hook Class
 * 
 * Nicedit Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cNiceditHook extends cHook {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function EndSystemBuffer ( $pData = null ) {
		
		$head = $this->GetSys ( "Buffer" )->GetQueue( "head" );
		$markup = $this->GetSys ( "HTML" );
		
		$markup->Load ( $head );
		
		$script = "\t" . '<script src="/hooks/nicedit/assets/Nicedit-0.9r23/nicEdit.js"></script>' . "\n";
		$script .= "\t" . '<script src="/hooks/nicedit/assets/nicEdit.js"></script>' . "\n\n";
		
		$markup->Find ( "head", 0)->innertext .= $script;
		
		ob_start();
		$markup->Display();
		$headMarkup = ob_get_clean();
		
		$this->GetSys ( "Buffer" )->SetQueue ( "head", $headMarkup );
		
		return ( true );
	}
	
}
