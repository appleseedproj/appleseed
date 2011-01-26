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
        
        while ( $Model->Fetch() ) {
        	$Task = $Model->Get ( 'Task' );
        	$Tasks[$Task] = strtotime ( $Model->Get ( 'Updated' ) );
        }
        
        // Run the janitor every minute.
       	// TODO: Make this configurable.
        if ( !$this->_IsPast ( $Tasks['Janitorial'], 1 ) ) return ( true );
       	$Model->Set ( 'Updated', NOW() );
       	$Model->Save ( array ( 'Task' => 'Janitorial' ) );
        
       	// Process the newsfeed every FIVE minutes.
       	// TODO: Make this configurable.
        if ( $this->_IsPast ( $Tasks['ProcessNewsfeed'], 5 ) ) {
        	$this->Talk ( 'Newsfeed', 'ProcessQueue' );
        	$Model->Set ( 'Updated', NOW() );
        	$Model->Save ( array ( 'Task' => 'ProcessNewsfeed' ) );
        }
		
       	// Update the node network every 24 hours.
       	// TODO: Make this configurable.
        if ( $this->_IsPast ( $Tasks['UpdateNodeNetwork'], 1440 ) ) {
			$this->GetSys ( 'Event' )->Trigger ( 'Update', 'Node', 'Network' );
        	$Model->Set ( 'Updated', NOW() );
        	$Model->Save ( array ( 'Task' => 'UpdateNodeNetwork' ) );
        }
		
		return ( true );
	}
	
	private function _IsPast ( $pTime, $pDistance ) {
		
		if ( !$pTime ) return ( false );
		
        $now = strtotime ( NOW() );
        
        $diff = $now - $pTime;
        $diffMinutes = $diff / 60;
        
        if ( $diffMinutes < $pDistance ) return ( false );
        
        return ( true );
	}

}