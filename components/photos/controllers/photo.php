<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Photos
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Photos Component Photo Controller
 * 
 * Photos Component Photo Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Photos
 */
class cPhotosPhotoController extends cController {
	
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
		
		$this->View = $this->GetView ( $pView ); 
		
		$this->Set = $this->GetModel ( 'Sets' );

		$this->Photos = $this->GetModel ( 'Photos' );

		$Set = $this->GetSys ( 'Request' )->Get ( 'Set' );
		$Identifier = $this->GetSys ( 'Request' )->Get ( 'Photo' );

		$this->Set->Load ( $this->_Focus->Id, $Set );
		$this->Set->Fetch();

		$this->Photos->Load ( $Identifier );
		$this->Photos->Fetch();

		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {

		$id = $this->Photos->Get ( 'Photo_PK' );

		$Identifier = $this->Photos->Get ( 'Identifier' );
		$extension = pathinfo ( $filename, PATHINFO_EXTENSION );

		$photoLocation = 'http://' . ASD_DOMAIN . '/_storage/photos/' . $this->_Focus->Username . '/' . $this->Set->Get ( 'Directory' ) . '/' . $Identifier . '.n' . '.jpg';
		$this->View->Find ( '.original', 0 )->src = $photoLocation;

		$this->View->Find ( '.description', 0 )->innertext = $this->Photos->Get ( 'Description' );
	}
	
}
