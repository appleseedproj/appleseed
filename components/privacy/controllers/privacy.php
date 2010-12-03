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
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$this->_View = $this->GetView ( $pView );
		
		$Type = $pData['Type'];
		$Identifier = $pData['Identifier'];
		
		$this->_Model = $this->GetModel ();
		
		$this->_Prep( $Type, $Identifier );
		
		$this->_View->Display();
		
		return ( true );
	}
	
	public function _Prep ( $pType = null, $pIdentifier = null ) {
		
		$li = $this->_View->Find ( '.circle', 0 );
		$row = $this->_View->Copy ( '.circle', 0 )->Find ( '.circle', 0 );
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		$circles = $this->Talk ( 'Friends', 'Circles' );
		
		if ( ( $pType ) && ( $pIdentifier ) ) {
			$currentPrivacy = $this->_Model->RetrieveItem ( $this->_Focus->Id, $pType, $pIdentifier );
			if ( $currentPrivacy->Friends == 1 ) {
				$this->_View->Find ( '[name=Privacy[friends]]', 0 )->checked = true;
				$this->_View->Find ( '[name=Privacy[nobody]]', 0 )->checked = false;
			} else if ( $currentPrivacy->Everybody == 1 ) {
				$this->_View->Find ( '[name=Privacy[everybody]]', 0 )->checked = true;
				$this->_View->Find ( '[name=Privacy[nobody]]', 0 )->checked = false;
			} else if ( count ( $currentPrivacy->Circles ) > 0 ) {
				$this->_View->Find ( '[name=Privacy[nobody]]', 0 )->checked = false;
			}
		}
		
		foreach ( $circles as $c => $circle ) {
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			$row->Find ( 'label', 0 )->innertext = $circle;
			$row->Find ( 'input', 0 )->name = 'Privacy[' . $circle . ']';
			
			// Determine if this circle is selected.
			if ( isset ( $currentPrivacy ) and ( in_array ( $c, $currentPrivacy->Circles ) ) ) {
				$row->Find ( 'input', 0 )->checked = true;
			}
			
		    $li->innertext .= $row->outertext;
			unset ( $row );
		
		}
		
		return ( true );
	}
}
