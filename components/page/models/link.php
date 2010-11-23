<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   ProfilePage
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Page Component Link Model
 * 
 * Page Component Link Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Page
 */
class cPageLinkModel extends cModel {
	
	protected $_Tablename = 'PageLinks';
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function RetrieveCurrent ( $pUserId ) {
		
		$criteria = array ( 'User_FK' => $pUserId, 'Current' => 1 );
		$this->Retrieve ( $criteria );
		
		return ( true );
	}
	
	public function ClearCurrent ( $pUserId ) {
		$table = $this->_Prefix . $this->_Tablename;
		$query = 'UPDATE `' . $table . '` SET `Current` = ? WHERE `User_FK` = ?';
		
		$prepared[] = '0'; 
		$prepared[] = $pUserId;
		
		$this->Query ( $query, $prepared );
		
		return ( true );
	}
	
	function Remove ( $pIdentifier, $pUserId ) {
		
		// Remove the Page Link 
		$this->Delete ( array ( 'Identifier' => $pIdentifier, 'User_FK' => $pUserId ) );
		
		// Remove the page reference.
		include_once ( ASD_PATH . '/components/page/models/references.php' );
		$Reference = new cPageReferencesModel ();
		$Reference->Delete ( array ( 'Identifier' => $pIdentifier, 'User_FK' => $pUserId ) );
		
		return ( true );
	}
	
	public function RetrievePageLinks ( $pUserId ) {
		
		$Links = $this->_Prefix . $this->_Tablename;
		$Refs = $this->_Prefix . 'PageReferences';
		
		$query = "
			select SQL_CALC_FOUND_ROWS Refs.Stamp, Links.* 
				from 
					`$Refs` as Refs, `$Links` as Links 
				where 
					Refs.Identifier = Links.Identifier
				and
					Links.User_FK = ?
				order by
					Refs.Stamp DESC
		";
		
		$prepared[] = $pUserId;
		
		$this->Query ( $query, $prepared );
		
		return ( true );
	}
	
	public function RetrieveLink ( $pUserId, $pIdentifier ) {
		
		$criteria = array ('User_FK' => $pUserId, 'Identifier' => $pIdentifier );
		
		$this->Retrieve ( $criteria );
		
		if ( $this->Get ( "Total" ) == 0) return ( false );
		
		$this->Fetch();
		
		return ( $this->Get ( "Data" ) );
	}
	
	public function Link ( $pComment, $pPrivacy, $pUserId, $pOwner, $pLink, $pTitle, $pDescription, $pThumb ) {
		
		$Identifier = $this->CreateUniqueIdentifier();
		
		$privacyData = array ( 'Privacy' => $pPrivacy, 'Type' => 'Link', 'Identifier' => $Identifier );
		$this->GetSys ( 'Components' )->Talk ( 'Privacy', 'Store', $privacyData );
		
		$this->Protect ( 'Link_PK', null );
		$this->Set ( 'User_FK', $pUserId );
		$this->Set ( 'Owner', $pOwner );
		$this->Set ( 'Identifier', $Identifier );
		$this->Set ( 'Content', $pComment );
		$this->Set ( 'Link', $pLink );
		$this->Set ( 'Title', $pTitle );
		$this->Set ( 'Description', $pDescription );
		$this->Set ( 'Thumb', $pThumb );
		
		if ( $pCurrent ) {
			$this->Set ( 'Current', (int)true );
			$this->ClearCurrent ( $pUserId );
		} else {
			$this->Set ( 'Current', '0' );
		}
		
		$this->Save();
		
		include_once ( ASD_PATH . '/components/page/models/references.php' );
		
		$Reference = new cPageReferencesModel ();
		
		$Reference->Create ( 'Link', $Identifier, $pUserId );
		
		return ( true );
	}
	
}
