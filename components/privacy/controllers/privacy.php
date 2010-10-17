<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Privacy
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Privacy Component Controller
 * 
 * Privacy Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Privacy
 */
class cPrivacyPrivacyController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->_View = $this->GetView ( $pView );
		
		$this->_Prep();
		
		$this->_View->Display();
		
		return ( true );
	}
	
	public function _Prep ( ) {
		$li = $this->_View->Find ( '.circle', 0 );
		$row = $this->_View->Copy ( '.circle', 0 )->Find ( '.circle', 0 );
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		$circles = $this->Talk ( 'Friends', 'Circles' );
		
		foreach ( $circles as $c => $circle ) {
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			$row->Find ( 'label', 0 )->innertext = $circle;
			$row->Find ( 'input', 0 )->name = 'Privacy[' . $circle . ']';
			
		    $li->innertext .= $row->outertext;
			unset ( $row );
		
		}
		
		return ( true );
	}
}
