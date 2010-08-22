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
		
		if ( !$context ) {
			$context = $_SERVER['REQUEST_URI'];
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
		
		$access = new cSecurityAccess();
		
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
				$this->userAccess->r = isset ($pData['Read'] ) ? $pData['Read'] : null;
				$this->userAccess->w = isset ($pData['Write'] ) ? $pData['Write'] : null; 
				$this->userAccess->a = isset ($pData['Admin'] ) ? $pData['Admin'] : null; 
			} // if
	
			unset ($parentAccess);
		} else {
			$access->Set ( "Read", $accessModel->Get ( "r" ) );
			$access->Set ( "Write", $accessModel->Get ( "w" ) );
			$access->Set ( "Admin", $accessModel->Get ( "a" ) );
			$access->Set ( "Inheritance", $accessModel->Get ( "Inheritance" ) );
		} // if 
		
		return ($access);
	}
	
}

class cSecurityAccess extends cBase {
	
	protected $_Read;
	protected $_Write;
	protected $_Admin;
	protected $_Inheritance;
	
}
