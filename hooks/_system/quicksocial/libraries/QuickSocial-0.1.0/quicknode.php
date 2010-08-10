<?php
/**
 * @version      $Id$
 * @package      QuickSocial.Library
 * @subpackage   QuickNode
 * @copyright    Copyright (C) 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org/quicksocial/
 * @license      GNU Lesser General Public License (LGPL) version 3.0
 */

/** QuickNode Class
 * 
 * Node discovery and information.
 * 
 * @package     QuickSocial.Framework
 * @subpackage  QuickNode
 */
class cQuickNode extends cQuickSocial {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function Discover ( $pTarget ) {
		
		$data = array (
			"_social" => "true",
			"_task" => "node.discover"
		);
		
		$result = $this->_Communicate ( $pTarget, $data );
		
		return ( $result );
	}
	
	public function ReplyToDiscover ( $fDiscoverReply ) {
		$social = $_GET['_social'];
		$task = $_GET['_task'];
		
		if ( $social != "true" ) return ( false );
		if ( $task != "node.discover" ) return ( false );
		
		if ( !is_callable ( $fDiscoverReply ) ) $this->_Error ( "Invalid Callback" );
		
		$data = @call_user_func ( $fDiscoverReply );
		
		if ( !is_array ( $data ) ) $this->_Error ( "Invalid Callback Return" );
		
		$data['version'] = QUICKSOCIAL_VERSION;
		$data['success'] = "true";
		$data['error'] = "";
		
		echo json_encode ( $data );
		exit;
	}

}
