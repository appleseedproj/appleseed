<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Newsfeed
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Newsfeed Component Outgoing Model
 * 
 * Newsfeed Component Outgoing Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Newsfeed
 */
class cNewsfeedIncomingModel extends cModel {
	
	protected $_Tablename = 'NotificationsIncoming';
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function Queue ( $pOwnerId, $pAction, $pActionOwner, $pActionLink, $pSubjectOwner, $pContext, $pContextOwner, $pContextLink, $pIcon, $pComment, $pDescription, $pIdentifier ) {
	
		$this->Protect ( 'Incoming_PK', null );
		$this->Set ( 'Owner_FK', $pOwnerId );
		$this->Set ( 'Action', $pAction );
		$this->Set ( 'ActionOwner', $pActionOwner );
		$this->Set ( 'ActionLink', $pActionLink );
		$this->Set ( 'SubjectOwner', $pSubjectOwner );
		$this->Set ( 'Context', $pContextLink );
		$this->Set ( 'ContextOwner', $pContextOwner );
		$this->Set ( 'ContextLink', $pContextLink );
		$this->Set ( 'Icon', $pIcon );
		$this->Set ( 'Comment', $pComment );
		$this->Set ( 'Description', $pDescription );
		$this->Set ( 'Identifier', $pIdentifier );
		$this->Set ( 'Current', NOW() );
		$this->Set ( 'Updated', NOW() );
		
		$this->Create();
		
		return ( true );
	}
	
	public function Incoming ( $pOwnerId, $pStart = 0, $pStep = 50 ) {
		
		$this->Retrieve ( array ( 'Owner_FK' => $pOwnerId ), 'Updated DESC', array ( "start" => $pStart, "step" => $pStep ) );
		
		return ( true );
	}
	
}
