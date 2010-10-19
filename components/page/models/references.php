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

/** Page Component Page Model
 * 
 * Page Component Page Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Page
 */
class cPageReferencesModel extends cModel {
	
	protected $_Tablename = 'PageReferences';
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function Create ( $pType, $pIdentifier, $pUserId ) {
		
		$this->Protect ( 'Reference_FK', null );
		$this->Set ( 'User_FK', $pUserId );
		$this->Set ( 'Type', $pType );
		$this->Set ( 'Identifier', $pIdentifier );
		$this->Set ( 'Stamp', NOW() );
		
		$this->Save();
		
		return ( true );
	}
	
}
