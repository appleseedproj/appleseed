<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Event Class
 * 
 * Base class for Events
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cEvent extends cBase {
	
	private $_Events;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {
		parent::__construct();
	}

	public function Trigger ( $pEvent, $pComponent, $pTask, $pData = null ) {
		
		$event = ucwords ( strtolower ( ltrim ( rtrim ( $pEvent ) ) ) ); 
		$component = ucwords ( strtolower ( ltrim ( rtrim ( $pComponent ) ) ) ); 
		$task = ucwords ( strtolower ( ltrim ( rtrim ( $pTask ) ) ) ); 
		
		// Make an exception for system events and do proper casing manually.
		if ( strtolower ($event == "_system" ) ) $event = "_System";
		
		$function = $event . $component . $task;
		
		$hooks = $this->Hooks->GetHooks ();
		
		foreach ( $hooks as $h => $hook ) {
			if ( in_array ( $function, get_class_methods ( $hook ) ) ) {
				$lang_component = $hook->Get ( "Component" );
				$lang_hook = $hook->Get ( "Hook" );
			
				$hook_lang = 'hooks' . DS . strtolower ( $lang_component ) . '.' . $lang_hook . '.lang';
				$store = $this->GetSys ( "Language" )->Load ( $hook_lang );
		
				$this->Hooks->$h->$function ( $pData );
				
				$this->GetSys ( "Language" )->Restore( $store );
			} // if
		}
	}
	
}
