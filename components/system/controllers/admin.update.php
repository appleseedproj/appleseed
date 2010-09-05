<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** System Component Controller
 * 
 * System Component Admin Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  System
 */
class cSystemAdminUpdateController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$session = $this->GetSys ( "Session" ); 
		$session->Context ( $this->Get ( "Context" ) );
		
		// Current version.
		$current = $this->GetSys ( "Config" )->GetConfiguration ( "ver" );
		
		$this->Form = $this->GetView ( "admin.update" );
		
		$update = $this->GetModel ( "admin.update" );
		
		$update->Retrieve();
		
		$defaults = array();
		
		while ( $update->Fetch() ) {
			$server = $update->Get ( "Server" );
			$default = $update->Get ( "Default" );
			$serverList[] = $server;
			
			if ( $default == 1 ) {
				$defaults['Server'] = $server;
				$currentServer = $server;
			}
		}
		
		$servers = $this->_QueryServers ( $serverList );
		
		foreach ( $servers as $server => $serverInformation ) {
			if ( in_array ( $server, $serverList ) ) {
				$viableServers[] = $server;
			}
		}
		
		if ( count ( $viableServers ) == 0 ) {
			$session->Set ( "Message", "No Valid Servers Found" );
			$session->Set ( "Error", true );
		}
		
		if ( !in_array ( $currentServer, $viableServers ) ) {
			$currentServer = $viableServers[0];
		}
		
		if ( $selectedServer = $this->GetSys ( "Request" )->Get ( "Server" ) ) {
			$currentServer = $selectedServer;
		}
		
		$this->Form->Find ( "[name=Context]", 0 )->value  = $this->Get ( "Context" );
		$this->Form->Find ( "[name=Current]", 0 )->value  = $current;
		
		foreach ( $servers as $server => $serverInformation ) {
			$this->Form->Find ( "[name=Server]", 0 )->innertext .= '<option value="' . $server . '">' . $server . '</option>';
			if ( $currentServer == $server ) {
				foreach ( $serverInformation->releases as $r => $release ) {
					if ( !$this->_CompareValidUpgrade ( $current, $release ) ) {
						$disabled = 'disabled="disabled"';
					} else {
						$defaults['Version'] = $release;
						$disabled = null;
					}
					
					$this->Form->Find ( "[name=Version]", 0 )->innertext .= '<option ' . $disabled . ' value="' . $release . '">' . $release . '</option>';
				}
			}
		}
		
		$defaults['BackupDirectory'] = ASD_PATH . "_backup";
		
		$this->Form->Synchronize( $defaults );
		
		$this->_PrepareMessage ();
		
		$this->Form->Display();
		
		return ( true );
	}
	
	private function _QueryServers ( $pServers ) {
		
		$finalServers = array ();
		
		$data = array ( "_versions" => "true" );
		
		foreach ( $pServers as $s => $server ) {
				$result = $this->_Communicate ( $server, $data );
				
				if ( $result->success != "true" ) continue;
				$finalServers[$server] = $result;
		}
		
		return ( $finalServers );
	}
	
	// Single requests
	private function _Communicate ( $pTarget, $pData ) {
		
		$url = 'http://' . $pTarget;
		
		$url .= '/?' . http_build_query ($pData );
		
		$curl = curl_init();
		
	    $options = array(
	    	CURLOPT_URL				=> $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_VERBOSE			=> true,
			CURLOPT_USERAGENT      => "Appleseed QuickSocial API v0.1",
			CURLOPT_AUTOREFERER    => true,
			CURLOPT_CONNECTTIMEOUT => 1,
			CURLOPT_TIMEOUT        => 1,      
			CURLOPT_MAXREDIRS      => 10,       
		);
	   	curl_setopt_array( $curl, $options );
		
		// Retrieve the result
		$curl_response = curl_exec ( $curl ) ;
		
		curl_close($curl);
		
		// Decode the result
		$result = json_decode ( $curl_response );
		
		return ( $result );
	}
	
	public function AddServer ( $pView = null, $pData = array ( ) ) {
		
		$session = $this->GetSys ( "Session" ); 
		$session->Context ( $this->Get ( "Context" ) );
		
		$server = strtolower ( ltrim ( rtrim ( $this->GetSys ( "Request" )->Get ( "NewServer" ) ) ) );
		$server = str_replace ( "http://", "", $server );
		
		if ( !$server ) {
			$session->Set ( "Message", "No Server Name Provided" );
			$session->Set ( "Error", true );
			return ( $this->Display ( $pView, $pData ) );
		}
		
		$data = array ( "_versions" => "true" );
		$response = $this->_Communicate ( $server, $data );
		
		if ( $response->success != "true" ) {
			$session->Set ( "Message", __("Not A Valid Update Server", array ( "server" => $server ) ) );
			$session->Set ( "Error", true );
			return ( $this->Display ( $pView, $pData ) );
		}
		
		$update = $this->GetModel ( "admin.update" );
		
		$update->AddServer ( $server );
		
		$this->GetSys ( "Request" )->Set ( "Server", $server );
		$this->GetSys ( "Request" )->Set ( "NewServer", null );
		
		return ( $this->Display ( $pView, $pData ) );
	}
	
	public function Refresh ( $pView = null, $pData = array ( ) ) {
		
		return ( $this->Display ( $pView, $pData ) );
	}
	
	public function Update ( $pView = null, $pData = array ( ) ) {
		
		$session = $this->GetSys ( "Session" ); 
		$session->Context ( $this->Get ( "Context" ) );
	
		$server = $this->GetSys ( "Request" )->Get ( "Server" );
		$backupDirectory = $this->GetSys ( "Request" )->Get ( "BackupDirectory" );
		$version = $this->GetSys ( "Request" )->Get ( "Version" );
		
		if ( !$server ) {
			$session->Set ( "Message", "No Valid Servers Found" );
			$session->Set ( "Error", true );
			return ( $this->Display ( $pView, $pData ) );
		}
		
		if ( !is_writable ( $backupDirectory ) ) {
			$session->Set ( "Message", "Backup Directory Unwritable" );
			$session->Set ( "Error", true );
			return ( $this->Display ( $pView, $pData ) );
		}
		
		echo "Success!";
		return ( true );
	}

	private function _PrepareMessage ( ) {
		
		$session = $this->GetSys ( "Session" ); 
		$session->Context ( $this->Get ( "Context" ) );
	
		$markup = & $this->Form;
		
		if ( $message =  $session->Get ( "Message" ) ) {
			$markup->Find ( "[id=update-message]", 0 )->innertext = $message;
			if ( $error =  $session->Get ( "Error" ) ) {
				$markup->Find ( "[id=update-message]", 0 )->class = "error";
			} else {
				$markup->Find ( "[id=update-message]", 0 )->class = "message";
			}
			$session->Delete ( "Message ");
			$session->Delete ( "Error ");
		}
		
		return ( true );
	}
	
	private function _CompareValidUpgrade ( $pCurrent, $pNew ) {
		list ( $currentMajor, $currentMinor, $currentMicro ) = explode ( '.', $pCurrent );
		
		$newCurrentMicro = $currentMajor . '.' . $currentMinor . '.' . ($currentMicro+1);
		$newCurrentMinor = $currentMajor . '.' . ($currentMinor+1) . '.0';
		$newCurrentMajor = ($currentMajor+1) . '.' . '0.0';
		
		if ( $pNew == $newCurrentMajor ) return ( true );
		if ( $pNew == $newCurrentMinor ) return ( true );
		if ( $pNew == $newCurrentMicro ) return ( true );
		
		return ( false );
	}
	
}