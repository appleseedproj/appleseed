<?php
/**
 * @version      $Id$
 * @package      QuickSocial.Library
 * @subpackage   QuickUser
 * @copyright    Copyright (C) 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org/quicksocial/
 * @license      GNU Lesser General Public License (LGPL) version 3.0
 */

if ( !class_exists ( "cQuickSocial" ) ) require ( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'quicksocial.php' );

/** QuickUser Class
 * 
 * User information retrieval
 * 
 * @package     QuickSocial.Framework
 * @subpackage  QuickUser
 */
class cQuickUser extends cQuickSocial {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function ReplyToUserIcon ( ) {
		
		$fUserIcon = $this->GetCallBack ( "UserIcon" );
		
		if ( !is_callable ( $fUserIcon ) ) $this->_Error ( "Invalid Callback: UserIcon" );
		
		$account = $_GET['_account'];
		$request = $_GET['_request'];
		
		$width = $_GET['_width'];
		$height = $_GET['_height'];
		
		@call_user_func ( $fUserIcon, $request, $width, $height );
		
		return ( true );
	}

}
