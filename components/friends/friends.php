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
		
		$return = array();
		
		$return[] = array ( 'id' => 'friends', 'title' => 'Friends Tab', 'link' => '/friends/' );
		
		return ( $return );
	} 
	
	public function Circles ( $pData = null ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		include_once ( ASD_PATH . '/components/friends/models/circles.php');
		$this->_Model = new cFriendsCirclesModel();
		
		$return = array();
		$circles = $this->_Model->Circles ( $this->_Focus->Id );
		
		foreach ( $circles as $c => $circle ) {  
			$id = $circle['id'];
			$return[$id] = $circle['name'];
		}
		
		return ( $return );
	}
}
