<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Security
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Security Component
 * 
 * Security Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Security
 */
class cSecurity extends cComponent {
	
	private $_Cache;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function Access ( $pData = null ) {
		
		$account = $pData['account'];
		$context = $pData['context'];
		
		$access = new cSecurityAccess();
		
		if ( !$account ) {
			$user = $this->GetSys ( "Components" )->Talk ( "User", "Current" );
			
			// No user is logged in, so send back the defaults.
			if ( !$user ) {
				$access->Set ( "Read", isset ($pData['Read'] ) ? $pData['Read'] : false); 
				$access->Set ( "Write", isset ($pData['Write'] ) ? $pData['Write'] : false);  
				$access->Set ( "Admin", isset ($pData['Admin'] ) ? $pData['Admin'] : false); 
				return ( $access );
			}
			
			$account = $user->Username . '@' . $user->Domain;
		}
		
		if ( !$context ) {
			$context = $_SERVER['REQUEST_URI'];
		}
		
		$account = strtolower ( $account );
		
		// Return cached value to avoid duplicate effort.
		if ( isset ( $this->_Cache[$account][$context] ) ) {
			return ( $this->_Cache[$account][$context] );
		}
		
		$accessModel = new cModel ( "AccessControl" );
		
		$domain = $_SERVER['HTTP_HOST'];
		$pattern = '/(.*)@' . $domain . '/';
		
		if (preg_match ( $pattern, $account ) ) {
			list ( $username, $domain ) = explode ( '@', $account );
		}
		
		// Load security settings from userAccess.
		$criteria = array ( array ("account" => $account, "||account" => $username), "Location"     => $context);
		
		$accessModel->Retrieve ( $criteria );
		$accessModel->Fetch();
		
		// If no entries were found, go backwards for inheritance.
		if ( ($accessModel->Get ( "Total" ) == 0 ) and ($context != '/') ) {
	
			// Remove top directory off of Location.
			$currentLocation = strrchr (rtrim($context, "/"), "/");
			$currentLocationpos = strpos ($context, $currentLocation);
			$parentLocation = substr($context, 0, $currentLocationpos + 1);
	
			// Use recursive call of this function.
			$parameters['account'] = $account;
			$parameters['context'] = $parentLocation;
			$parentAccess = $this->Access ( $parameters );
			
			if ($parentAccess->Get ( "Inheritance" ) ) {
				// Inherit parent values.
				$access->Set ( "Inheritance", $parentAccess->Get ( "Inheritance" ) );
				$access->Set ( "Read", $parentAccess->Get ( "Read" ) );
				$access->Set ( "Write", $parentAccess->Get ( "Write" ) );
				$access->Set ( "Admin", $parentAccess->Get ( "Admin" ) );
			} else {
				// Use default values;
				$this->userAccess->Location = $context; 
				$access->Set ( "Read", isset ($pData['Read'] ) ? $pData['Read'] : false); 
				$access->Set ( "Write", isset ($pData['Write'] ) ? $pData['Write'] : false);  
				$access->Set ( "Admin", isset ($pData['Admin'] ) ? $pData['Admin'] : false); 
			} // if
	
			unset ($parentAccess);
		} else {
			$access->Set ( "Read", $accessModel->Get ( "Read" ) );
			$access->Set ( "Write", $accessModel->Get ( "Write" ) );
			$access->Set ( "Admin", $accessModel->Get ( "Admin" ) );
			$access->Set ( "Inheritance", $accessModel->Get ( "Inheritance" ) );
		} // if 
		
		// Store result in internal cache.
		$this->_Cache[$account][$context] = $access;
		
		return ($access);
	}
	
}

class cSecurityAccess extends cBase {
	
	protected $_Read;
	protected $_Write;
	protected $_Admin;
	protected $_Inheritance;
	
}
