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

/** Example Hook Class
 * 
 * Example Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cQuicksocialHook extends cHook {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function SystemNodeDiscovery ( $pData = array() ) {
		
		if (!class_exists ( 'cQuickNode' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicknode.php' );
		
		// Set caching on by default.
		$cache = $pData['cache'] ? isset ( $pData['cache'] ) : true;
		
		if ( $cache ) {
			$model = new cModel ( "NodeDiscovery");
		}
		
		$node = new cQuickNode ();
		
		$domain = $pData['domain'];
		
		$node->SetCallback ( "CheckLocalToken", array ( $this, '_CheckLocalToken' ) );
		$node->SetCallback ( "CreateLocalToken", array ( $this, '_CreateLocalToken' ) );
		
		$node = $node->Discover ( $domain );
		
		return ( $node );
	}
	
	public function BeginSystemInitialize ( $pData = null ) {
		
		$social = $this->GetSys ( "Request" )->Get ( "_social" );
		
		if ( $social != "true" ) return ( false );
		
		$task = $this->GetSys ( "Request" )->Get ( "_task" );
		
		switch ( $task ) {
			case 'verify':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicksocial.php' );
				$data = $this->GetSys ( "Request" )->Get(); 
				 
				$social = new cQuickSocial ();
				$social->SetCallback ( "CheckLocalToken", array ( $this, '_CheckLocalToken' ) );
				
				$social->ReplyToVerify();
				exit;
			break;
			case 'connect.check':
				echo "connect.check";
			break;
			case 'connect.return':
				echo "connect.return";
			break;
			case 'node.discover':
				require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicknode.php' );
				 
				$node = new cQuickNode ();
				$node->SetCallback ( "CheckRemoteToken", array ( $this, '_CheckRemoteToken' ) );
				$node->SetCallback ( "CreateRemoteToken", array ( $this, '_CreateRemoteToken' ) );
				$node->SetCallback ( "NodeInformation", array ( $this, '_NodeInformation' ) );
				$node->ReplyToDiscover();
				exit;
			break;
			case '':
			default:
				exit;
			break;
		}
		
	}
	
	public function _NodeInformation ( $pSource = null, $pVerified = false) {
		
		$return = array ();
		
		$return['methods'] = array ( "http" );
		
		$return['tasks'] = array (
			'node.discover',
			'connect.return',
			'connect.check'
		);
		
		$return['trusted'] = array ( );
		
		if ( $pVerified ) {
			$return['trusted'][] = '30rock.appleseed';
		}
		
		return ( $return );
	}
	
	public function _CheckLocalToken ( $pUsername, $pTarget, $pToken ) {
		
		if (!class_exists ( 'cQuickSocial' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicknode.php' );
				
		$social = new cQuickSocial ();
		
		// Verify if the specified token exists in the database.
		$model = new cModel ("LocalTokens");
		$model->Structure();
	
		$query = '
			SELECT Token 
				FROM #__LocalTokens
				WHERE Username = ?
				AND Target = ?
				AND Token = ?
				AND Stamp > DATE_SUB(NOW(), INTERVAL 24 HOUR)
		';
		
		$model->Query ( $query, array ( $pUsername, $pTarget, $pToken ) );
		
		$model->Fetch();
		$token = $model->Get ( "Token" );
		
		if ( $token ) return ( $token );
		
		return ( false );
	}
	
	public function _CreateLocalToken ( $pUsername, $pTarget ) {
	
		// Look for an existing token from the last 24 hours.
		$model = new cModel ("LocalTokens");
		$model->Structure();
	
		$query = '
			SELECT Token 
				FROM #__LocalTokens
				WHERE Username = ?
				And Target = ?
				AND Stamp > DATE_SUB(NOW(), INTERVAL 24 HOUR)
		';
		
		$model->Query ( $query, array ( $pUsername, $pTarget ) );
		
		$model->Fetch();
		$token = $model->Get ( "Token" );
		
		if ( $token ) {
			// Return the found token.
			return ( $token );
		} else {
			
			// Create a new token and store it.
			if ( !class_exists ( cQuickSocial ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicksocial.php' );
			$social = new cQuickSocial ();
			$token = $social->Token();
			
       		$model->Set ( "Username", $pUsername );
       		$model->Set ( "Target", $pTarget );
       		$model->Set ( "Token", $token );
       		$model->Set ( "Stamp", NOW() );
       		
       		$model->Save();
       		
       		return ( $token );
		}
	}
	
	public function _CheckRemoteToken ( $pUsername, $pSource, $pToken ) {
		
		if (!class_exists ( 'cQuickSocial' ) ) require ( ASD_PATH . 'hooks' . DS . 'quicksocial' . DS . 'libraries' . DS . 'QuickSocial-0.1.0' . DS . 'quicknode.php' );
				
		$social = new cQuickSocial ();
		
		// Look for an existing token from the last 24 hours.
		$model = new cModel ("RemoteTokens");
		$model->Structure();
	
		$query = '
			SELECT Token 
				FROM #__RemoteTokens
				WHERE Username = ?
				AND Source = ?
				AND Stamp > DATE_SUB(NOW(), INTERVAL 24 HOUR)
		';
			
		$model->Query ( $query, array ( $pUsername, $pSource ) );
		
		$model->Fetch();
		$token = $model->Get ( "Token" );
			
		if ( $token ) return ( $token );
		
		return ( false );
	}
	
	public function _CreateRemoteToken ( $pUsername, $pSource, $pToken ) {
		$model = new cModel ("RemoteTokens");
		$model->Structure();

		// Delete old tokens
		
		$query = '
			DELETE FROM #__RemoteTokens
				WHERE Username = ?
				AND Source = ?
		';
			
		$model->Query ( $query, array ( $pUsername, $pSource ) );
		
		// Store the verified token
		$model->Set ( "Username", $pUsername );
		$model->Set ( "Source", $pSource );
		$model->Set ( "Token", $pToken );
		$model->Set ( "Address", $_SERVER['REMOTE_ADDR'] );
		$model->Set ( "Host", $_SERVER['REMOTE_HOST'] );
		$model->Set ( "Stamp", NOW() );
		
		$model->Save();
		
		return ( true );
		
	}
	
}

