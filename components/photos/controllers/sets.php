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

/** Photos Component Sets Controller
 * 
 * Photos Component Sets Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Photos
 */
class cPhotosSetsController extends cController {
	
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
		
		$this->Sets = $this->GetModel ( 'Sets' );

		$this->Photo = $this->GetModel ( 'Photos' );
		
		$this->Sets->Load ( $this->_Focus->Id );

		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {

		$list = $this->View->Find ( '.list', 0);

		$item = $this->View->Find ( '.item', 0 );

		$list->innertext = "";
		
		while ( $this->Sets->Fetch() ) {
			$id = $this->Sets->Get ( 'Set_PK' );

			$this->Photo->GetCover ( $id );
			$item->Find ( '.name', 0 )->innertext = $this->Sets->Get ( 'Name' );
			$item->Find ( '.description', 0 )->innertext = $this->Sets->Get ( 'Description' );

			$filename = $this->Photo->Get ( 'Filename' );
			$extension = pathinfo ( $filename, PATHINFO_EXTENSION );

			$link = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/photos/' . $this->Sets->Get ( 'Directory' );
			$item->Find ( '.photoset-name-link', 0 )->href = $link;
			$item->Find ( '.photoset-cover-link', 0 )->href = $link;

			list ( $file ) = explode ( '.' . $extension, $filename );

			$coverLocation = 'http://' . ASD_DOMAIN . '/_storage/photos/admin/' . $this->Sets->Get ( 'Directory' ) . '/' . $file . '_m.' . $extension;
			$item->Find ( '.cover', 0 )->src = $coverLocation;
			$list->innertext .= $item->outertext;
		}
	}
	
}
