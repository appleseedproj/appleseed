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
	
}
