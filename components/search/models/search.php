<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Search
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Profile Component Summary Model
 * 
 * Profile Component Summary Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Profile
 */
class cSearchModel extends cModel {
	
	protected $_Tablename = "SearchIndexes";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function Index ( $pContext, $pContext_FK, $pKeywords ) {
		
		$this->Retrieve ( array ( 'Context' => $pContext, 'Context_FK' => $pContext_FK ) );
		
		if ( $this->Get ( 'Total' ) > 0 ) {
			$this->Fetch();
		} else {
			$this->Set ( 'Created', NOW() );
		}
		
		$this->Set ( 'Context', $pContext );
		$this->Set ( 'Context_FK', $pContext_FK );
		$this->Set ( 'Keywords', $pKeywords );
		$this->Set ( 'Updated', NOW() );
		
		$this->Save();
		
		return ( true );
	}
	
}

