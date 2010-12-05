<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Journal
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Journal Component Controller
 * 
 * Journal Component RSS Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Journal
 */
class cJournalRSSController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->_Current = $this->Talk ( 'User', 'Current' );
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$this->View = $this->GetView ( "entries" );
		
		$this->Model = $this->GetModel();
		
		if ( !$this->Model->Everybody ( $this->_Focus->Id, array ( 'start' => 0, 'step' => 20 ) ) ) {
			return ( false );
		}
		
		$RSS = $this->GetSys ( 'RSS' );
		
		$RSSTitle = __ ( 'RSS Feed Title', array ( 'fullname' => $this->_Focus->Fullname, 'domain' => ASD_DOMAIN ) );
		
		$RSSDescription = $this->_Focus->Description;
		$RSSUrl = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/journal/';
		$RSS->Create ( $RSSTitle, $RSSDescription, $RSSUrl );
		
		while ( $this->Model->Fetch() ) {
			$Item = $this->Model->Get ( 'Title' );
			$Description = $this->GetSys ( 'Render' )->Format ( $this->Model->Get ( 'Body' ) );
			$Created = $this->Model->Get ( 'Created' );
			
			$Author = $this->_Focus->Fullname;
			
			$Url = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/journal/' . strtolower ( str_replace ( ' ', '-', $this->Model->Get ( 'Title' ) ) );
			$Guid = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/journal/' . $this->Model->Get ( 'Identifier' );
			
			$RSS->Open ( $Item, $Description, $Url );
			$RSS->Element ( 'guid', $Guid );
			$RSS->Element ( 'author', $Author );
			$RSS->Element ( 'pubDate', date("D, d M Y H:i:s e", strtotime ( $Created ) ) );
			$RSS->Close ( );
		}
		
		echo $RSS->Output() ;
		
		return ( true );
	}
	
}
