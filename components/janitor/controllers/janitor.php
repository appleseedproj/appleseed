<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Janitor
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Janitor Component Controller
 * 
 * Janitor Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Janitor
 */
class cJanitorJanitorController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {
		
        ignore_user_abort(true);
        
        $Model = $this->GetModel();
        
        $Model->Retrieve();
        
        $Model->Fetch();
        
        $lastUpdated = strtotime ( $Model->Get ( 'Updated' ) );
        $now = strtotime ( NOW() );
        
        $diff = $now - $lastUpdated;
        $diffMinutes = $diff / 60;
        
        if ( $diffMinutes < 1 ) return ( true );
        
		$this->Talk ( 'Newsfeed', 'ProcessQueue' );
		
		$this->GetSys ( 'Event' )->Trigger ( 'Update', 'Node', 'Network' );
		
		$Model->Query(' DELETE FROM #__Janitor' );
		$Model->Set ( 'Updated', NOW() );
		$Model->Save();
		
		return ( true );
	}

}