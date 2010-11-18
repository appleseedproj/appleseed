<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Friends
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Friends Component
 * 
 * Friends Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Friends
 */
class cFriends extends cComponent {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function AddToProfileTabs ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$return = array();
		
		$return[] = array ( 'id' => 'friends', 'title' => 'Friends Tab', 'link' => '/friends/' );
		
		return ( $return );
	} 
	
	public function Circles ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$Requesting = $pData['Requesting'];
		$Target = $pData['Target'];
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( !$Requesting ) $Requesting = $this->_Current->Account;
		if ( !$Target ) $Target = $this->_Focus->Id;
		
		include_once ( ASD_PATH . '/components/friends/models/circles.php');
		$this->_Model = new cFriendsCirclesModel();
		
		$return = array();
		$circles = $this->_Model->Circles ( $Target );
		
		$circleMembership = $this->_Model->CirclesByMember ( $Target, $Requesting );
		
		if ( $this->_Focus->Account == $Requesting ) {
			foreach ( $circles as $c => $circle ) {  
				$id = $circle['id'];
				$return[$id] = $circle['name'];
			}
		} else {
			foreach ( $circles as $c => $circle ) {  
				if ( in_array ( $circle['name'], $circleMembership ) ) {
					if ( ( $circle['protected'] ) || ( $circle['shared'] ) ) {
						$id = $circle['id'];
						$return[$id] = $circle['name'];
					}
				}
			}
		}
		
		return ( $return );
	}
	
	public function Friends ( $pData = null ) {
		
		$Target = $pData['Target'];
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( !$Target ) $Target = $this->_Focus->Id;
		
		include_once ( ASD_PATH . '/components/friends/models/friends.php');
		$this->_Model = new cFriendsModel();
		
		$return = array();
		$this->_Model->RetrieveFriends ( $Target );
		
		while ( $this->_Model->Fetch() ) {
			$return[] = $this->_Model->Get ( 'Username' ) . '@' . $this->_Model->Get ( 'Domain' );
		}
		
		return ( $return );
	}
	
	public function FriendsInCircle ( $pData = null ) {
		
		$Target = $pData['Target'];
		$Circle = $pData['Circle'];
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( !$Target ) $Target = $this->_Focus->Id;
		if ( !$Circle ) return ( false );
		
		include_once ( ASD_PATH . '/components/friends/models/circles.php');
		$this->Circles = new cFriendsCirclesModel();
		
		if ( !$this->Circles->Load ( $this->_Focus->Id, $Circle ) ) {
			return ( false );
		}
		
		include_once ( ASD_PATH . '/components/friends/models/friends.php');
		$this->_Model = new cFriendsModel();
		
		$return = array();
		$this->_Model->RetrieveFriends ( $Target );
		$this->_Model->RetrieveCircle ( $Target, $this->Circles->Get ( 'tID' ) );
		
		while ( $this->_Model->Fetch() ) {
			$return[] = $this->_Model->Get ( 'Username' ) . '@' . $this->_Model->Get ( 'Domain' );
		}
		
		return ( $return );
	}
	
	public function NotifyAdd ( $pData = null ) {
		$this->Load ( 'Friends', null, 'NotifyAdd', $pData );
		
		return ( true );
	}
	
	public function NotifyApprove ( $pData = null ) {
		$this->Load ( 'Friends', null, 'NotifyApprove', $pData );
		
		return ( true );
	}
	
	public function TestLanguage ( $pData = null ) {
		$this->Load ( 'Friends', null, 'TestLanguage', $pData );
	}
	
	public function CreateRelationship ( $pData = null ) {
		$this->Load ( 'Friends', null, 'CreateRelationship', $pData );
	}
	
}
