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

/** Photos Component Photos Controller
 * 
 * Photos Component Photos Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Photos
 */
class cPhotosPhotosController extends cController {
	
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

		$this->Set->Load ( $this->_Focus->Id, $Set );
		$this->Set->Fetch();

		$this->Photos->LoadFromSet ( $this->Set->Get ( 'Set_PK' ) );

		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {

		$this->View->Find ( 'form[class="edit"]', 0 )->action = '/profile/' . $this->_Focus->Username . '/photos/' . $this->Set->Get ( 'Directory' ) . '/edit/';

		$list = $this->View->Find ( '.list', 0);

		$item = $this->View->Find ( '.item', 0 );

		$list->innertext = "";
		
		while ( $this->Photos->Fetch() ) {
			$id = $this->Photos->Get ( 'Photo_PK' );

			$Identifier = $this->Photos->Get ( 'Identifier' );

			$filename = $this->Photos->Get ( 'Filename' );
			$extension = pathinfo ( $filename, PATHINFO_EXTENSION );

			$link = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/photos/' . $this->Set->Get ( 'Directory' ) . '/' . $this->Photos->Get ( 'Identifier' );
			$item->Find ( '.photo-link', 0 )->href = $link;

			list ( $file ) = explode ( '.' . $extension, $filename );

			$photoLocation = 'http://' . ASD_DOMAIN . '/_storage/photos/admin/' . $this->Set->Get ( 'Directory' ) . '/' . $Identifier . '.m' . '.jpg'; 
			$item->Find ( '.photo', 0 )->src = $photoLocation;

			$item->Find ( '.description', 0 )->innertext = $this->Photos->Get ( 'Description' );
			$list->innertext .= $item->outertext;
		}
	}
	
}
