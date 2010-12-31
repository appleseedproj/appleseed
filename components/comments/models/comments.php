<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Comments
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Comments Component Model
 * 
 * Comments Component Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Comments
 */
class cCommentsModel extends cModel {
	
	protected $_Tablename = "CommentEntries";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function Load ( $pContext, $pId ) {
		
		$this->Retrieve ( array ( 'Context' => $pContext, 'Context_FK' => $pId ) );
		
		if ( $this->Get ( 'Total' ) == 0 ) return ( false );
		
		$result = array ();
		
		while ( $this->Fetch ( ) ) {
			$items[] = $this->Get ( 'Data' ) ;
		}
		
		return ( $items );
	}
	
	public function Store ( $pContext, $pId, $pBody, $pParent, $pOwner ) {
		
		$this->Protect ( 'Entry_PK' );
		$this->Set ( 'Body', $pBody );
		$this->Set ( 'Parent_ID', $pParent );
		$this->Set ( 'Owner', $pOwner );
		$this->Set ( 'Created', NOW() );
		$this->Set ( 'Context', $pContext );
		$this->Set ( 'Context_FK', $pId );
		$this->Set ( 'Status', 1 );
		$this->Set ( 'Address', $_SERVER['REMOTE_ADDR'] );
		
		$this->Save();
		
		return ( true );
	}
	
	public function Ownership ( $pId, $pAccount ) {
		
		$this->Retrieve ( array ( 'Entry_PK' => $pId, 'Owner' => $pAccount ) );
		
		if ( $this->Get ( 'Total' ) == 1 ) return ( true );
		
		return ( false );
	}
	
	public function Remove ( $pId ) {
		
		$this->Retrieve ( array ( 'Entry_PK' => $pId ) );
		
		if ( $this->Get ( 'Total' ) == 0 ) return ( false );
		
		$this->Fetch();
		
		$this->Set ( 'Body', '--deleted--' );
		$this->Set ( 'Owner', null );
		$this->Set ( 'Status', 0 );
		$this->Set ( 'Address', null );
		
		$this->Save();
	}
	
}
