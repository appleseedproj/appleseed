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
	
	private $_Messages;
	
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
		
		$this->_PrepareMessage ( &$this->Form );
		
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
	private function _Communicate ( $pTarget, $pData, $pRaw = false ) {
		
		$url = 'http://' . $pTarget;
		
		if ( count ( $pData ) > 0 ) $url .= '/?' . http_build_query ($pData );
		
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
		
		$result = $curl_response;
		
		curl_close($curl);
		
		// Decode the result
		if ( !$pRaw ) $result = json_decode ( $result );
		
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
		
		$skipBackups = $this->GetSys ( "Request" )->Get ( "SkipBackups" );
		
		if ( !$server ) {
			$session->Set ( "Message", "No Valid Servers Found" );
			$session->Set ( "Error", true );
			return ( $this->Display ( $pView, $pData ) );
		}
		
		if ( !$skipBackups ) {
			if ( !is_writable ( $backupDirectory ) ) {
				$session->Set ( "Message", __( "Backup Directory Unwritable", array ( "directory" => $backupDirectory ) ) );
				$session->Set ( "Error", true );
				return ( $this->Display ( $pView, $pData ) );
			}
		
			$subdirectory = date('Y-m-d_h\hi\ms\s');
			$directory = $backupDirectory . DS . $subdirectory;
		
			if (!file_exists ( $directory ) ) {
				if ( !mkdir ( $directory ) ) {
					$session->Set ( "Message", __( "Backup Directory Unwritable", array ( "directory" => $backupDirectory ) ) );
					$session->Set ( "Error", true );
					return ( $this->Display ( $pView, $pData ) );
				}
				chmod ( $directory, 0777 );
			} 
		
			// Step 1: Back up the database.
			$sqlDataFile = $directory . DS . "data.sql";
			if ( !$this->_BackupDatabase( $sqlDataFile ) ) {
				$session->Set ( "Message", __( "Error Creating Data Backup", array ( "filename" => $sqlDataFile ) ) );
				$session->Set ( "Error", true );
				return ( $this->Display ( $pView, $pData ) );
			} else {
				$this->_Messages[] = __( "Created Database Backup", array ( "file" => $subdirectory . DS . "data.sql" ) );
			}
			
			// Step 2: Back up the files
			$storeDirectory = $directory . DS . "files";
			if ( !$this->_BackupDirectory( $storeDirectory ) ) {
				$session->Set ( "Message", __( "Error Creating File Backup", array ( "directory" => $storeDirectory ) ) );
				$session->Set ( "Error", true );
				return ( $this->Display ( $pView, $pData ) );
			} else {
				$this->_Messages[] = __( "Created File Backup", array ( "file" => $subdirectory . DS . "files" . DS ) );
			}
		} else {
			$this->_Messages[] = __("Skipping Backups");
		}
		
		// Step 3: Update the files.
		$this->_UpdateFiles ( $server, $version );
		
		// Step 4: Update the database.
		$this->_UpdateDatabase ( $server, $version );
		
		// Display the results.
		$this->Results = $this->GetView ( "admin.update.results" );
		
		$tbody = $this->Results->Find ( "[id=update-results-body] tbody tr", 0);
		
		$row = $this->Results->Copy ( "[id=update-results-body] tbody tr" )->Find ( "tr", 0 );
		
		$cellCurrent = $row->Find( "[class=Current]", 0 );
		$cellUpdate = $row->Find( "[class=Update]", 0 );
		
		$tbody->innertext = " " ;
		
		foreach ( $this->_Messages as $m => $message ) {
		    $oddEven = empty($oddEven) || $oddEven == 'odd' ? 'even' : 'odd';
			
			$row->class = $oddEven;
			
			$cellUpdate->innertext = $message;
			$cellCurrent->innertext = ($m + 1);
			$tbody->innertext .= $row->outertext;
		}
		
		$session->Set ( "Message", __( "Update Has Completed", array ( "version" => $version ) ) );
		$session->Set ( "Error", false );
		
		$this->_PrepareMessage ( &$this->Results );
		
		$this->Results->Display();
		
		return ( true );
	}
	
	private function _BackupDatabase ( $pBackupFile ) {
		
		// Adapted from: http://davidwalsh.name/backup-mysql-database-php
		
		$prefix = $this->GetSys ( "Config" )->GetConfiguration ( "pre" );
		$db = $this->GetSys ( "Database" )->Get ( "DB" );
		
		$query = "SHOW TABLES LIKE '$prefix%%'";
		
		$handle = $db->Prepare ( $query ) or die ();
		
		$handle->Execute ();
		
		$tables = array();
		while ( $data = & $handle->Fetch ( PDO::FETCH_NUM ) ) {
	  		$tables[] = $data[0];
		} 
		
		$return = null;
		
		$return = "SET FOREIGN_KEY_CHECKS = 0;\n\n";
		
		foreach ( $tables as $table ) { 
			
			// DROP TABLE
			$return .= "-- Table: $table\n\n";
			$return .= 'DROP TABLE IF EXISTS `' . $table . '`;' . "\n";
			
			// CREATE TABLE
			$createResult = $db->Prepare('SHOW CREATE TABLE '.$table);
			$createResult->Execute ();
			$row2 = $createResult->Fetch ( PDO::FETCH_NUM );
			$return.= "\n".$row2[1].";\n\n";
			
			// Get the number of fields
			$result = $db->Prepare('SELECT * FROM '.$table);
			$result->Execute ();
			$data = $result->Fetch ( PDO::FETCH_ASSOC );
			$num_fields = count($data);
			
			$result = $db->Prepare('SELECT * FROM '.$table);
			$result->Execute ();
			
			
			for ( $i = 0; $i < $num_fields; $i++ )  { 
				while ( $row = $result->Fetch ( PDO::FETCH_NUM ) ) { 
					
					$return.= 'INSERT INTO '.$table.' VALUES(';
					
					for ( $j=0; $j < $num_fields; $j++ ) { 
						$row[$j] = addslashes ( $row[$j] );
						$row[$j] = ereg_replace ( "\n", "\\n", $row[$j] );
						if ( isset ( $row[$j] ) ) { 
							$return .= '"'.$row[$j].'"' ; 
						} else { 
							$return .= '""';
						} 
						if ( $j< ( $num_fields-1 ) ) { $return.= ','; } 
					} 
					$return.= ");\n";
				} 
			} 
			$return.="\n\n";
		} 
		
  		// Save the file.
		if ( !$handle = fopen( $pBackupFile, 'w+' ) ) return ( false );
		if ( !fwrite($handle,$return) ) return ( false );
		fclose($handle);
		
		chmod ( $pBackupFile, 0777 );
		return ( true );
	}
	
	private function _BackupDirectory ( $pLocation ) {
		
		if ( !mkdir ( $pLocation ) ) return ( false );
		chmod ( $pLocation, 0777 );
		
		$source = ASD_PATH;
		$dest = $pLocation;
		
		$backupDirectories = $this->_GetDirectoriesToBackup ( $source );
		
		foreach ( $backupDirectories as $d => $directory ) {
			$source = ASD_PATH . DS . $directory;
			$dest = $pLocation . DS . $directory;
			smartCopy($source, $dest, array('folderPermission'=>0777,'filePermission'=>0777));
		}
		
		return ( true );
	}
	
	private function _GetDirectoriesToBackup ( $pLocation ) {
		
		$dirs = scandirs ( $pLocation );
		
		foreach ( $dirs as $d => $dir ) {
			if ( preg_match ( '/^_/', $dir ) ) unset ( $dirs[$d] );
		}
		
		return ( $dirs );
	}
	
	private function _UpdateFiles ( $pServer, $pVersion ) {
		$url = $pServer . "/diffs/" . $pVersion . ".diff";
		
		$diff = $this->_Communicate ( $url, array(), true );
		
		if ( !$diff ) {
			$this->_Messages[] = "Error Retrieving File Diff";
			return ( false );
		}
		
		$diffData = explode ( "\n", $diff );
		if ( count ( $diffData ) == 0 ) {
			$this->_Messages[] = "Diff Empty No Files Were Updated";
			return ( false );
		}
		
		foreach ( $diffData as $d => $data ) {
			list ( $action, $file, $md5 ) = explode ( "\t", $data );
			
			$fileURL = $pServer . '/releases/' . $pVersion . '/' . $file . '.txt';
			
			if ( $action == 'U' ) $fileData = $this->_Communicate ( $fileURL, array (), true );
			
			$oldFile = ASD_PATH . $file;
			
			$oldDirectory = implode ( "/", explode ( "/", ASD_PATH . $file, -1 ) );
			
			if ( !is_dir ( $oldDirectory ) ) {
				if ( !rmkdir ( $oldDirectory ) ) {
					$this->_Messages[] = __( "Could Not Create Directory", array ( "directory" => $oldDirectory ) );
					continue;
				}
			}
			
			$oldMd5 = md5 ( $fileData );
			
			if ( $action == 'U' ) {
				if ( $oldMd5 != $md5 ) {
					$this->_Messages[] = __( "Checksums Do Not Match", array ( "filename" => $file, "found" => $oldMd5, "expected" => $md5 ) );
					continue;
				}
				if ( !$handle = fopen( $oldFile, 'w+' ) ) {
					$this->_Messages[] = __( "Could Not Open File", array ( "filename" => $file ) );
					continue;
				}
				if ( !fwrite($handle,$fileData) ) {
					$this->_Messages[] = __( "Could Not Update File", array ( "filename" => $file ) );
					continue;
				}
				$this->_Messages[] = __( "Updated File Or Directory", array ( "filename" => $file ) );
				fclose($handle);
			} else if ( $action == 'D' ) {
				
				if ( !rrmdir ( $file ) ) {
					$this->_Messages[] = __( "Could Not Delete File Or Directory", array ( "filename" => $file ) );
					continue;
				}
				
				$this->_Messages[] = __( "Deleted File Or Directory", array ( "filename" => $file ) );
			}
			
			/*
			echo $action, '<br />';
			echo $file, '<br />';
			echo $fileURL, '<br />';
			echo $oldFile, '<br />';
			echo "File Data: <pre>", htmlspecialchars ($fileData), '</pre><br />';
			echo "Expected: ", $md5, '<br />';
			echo "Actual: ", md5 ( $fileData ), '<hr />';
			*/
		}
		
		return ( true );
	}

	private function _UpdateDatabase ( $pServer, $pVersion ) {
		return ( true );
	}

	private function _PrepareMessage ( $pMarkup ) {
		
		$session = $this->GetSys ( "Session" ); 
		$session->Context ( $this->Get ( "Context" ) );
	
		if ( $message =  $session->Get ( "Message" ) ) {
			$pMarkup->Find ( "[id=update-message]", 0 )->innertext = $message;
			if ( $error =  $session->Get ( "Error" ) ) {
				$pMarkup->Find ( "[id=update-message]", 0 )->class = "error";
			} else {
				$pMarkup->Find ( "[id=update-message]", 0 )->class = "message";
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


// Adapted from: http://sina.salek.ws/content/unix-smart-recursive-filefolder-copy-function-php
function smartCopy($source, $dest, $options=array('folderPermission'=>0755,'filePermission'=>0755)) {
	
	if ( strstr ( "_backup", $source ) ) return ( false );
	
	$result=false;
 
	//For Cross Platform Compatibility
	if (!isset($options['noTheFirstRun'])) {
	$source=str_replace('\\','/',$source);
	$dest=str_replace('\\','/',$dest);
	$options['noTheFirstRun']=true;
	}
	 
	if (is_file($source)) {
	if ($dest[strlen($dest)-1]=='/') {
	if (!file_exists($dest)) {
	makeAll($dest,$options['folderPermission'],true);
	}
	$__dest=$dest."/".basename($source);
	} else {
	$__dest=$dest;
	}
	$result=copy($source, $__dest);
	chmod($__dest,$options['filePermission']);
 
	} elseif(is_dir($source)) {
	if ($dest[strlen($dest)-1]=='/') {
	if ($source[strlen($source)-1]=='/') {
	//Copy only contents
	} else {
	//Change parent itself and its contents
	$dest=$dest.basename($source);
	@mkdir($dest);
	chmod($dest,$options['filePermission']);
	}
	} else {
	if ($source[strlen($source)-1]=='/') {
	//Copy parent directory with new name and all its content
	@mkdir($dest,$options['folderPermission']);
	chmod($dest,$options['filePermission']);
	} else {
	//Copy parent directory with new name and all its content
	@mkdir($dest,$options['folderPermission']);
	chmod($dest,$options['filePermission']);
	}
	}
	 
	$dirHandle=opendir($source);
	while($file=readdir($dirHandle))
	{
	if($file!="." && $file!="..")
	{
	$__dest=$dest."/".$file;
	$__source=$source."/".$file;
	//echo "$__source ||| $__dest<br />";
	if ($__source!=$dest) {
	$result=smartCopy($__source, $__dest, $options);
	}
	}
	}
	closedir($dirHandle);
 
	} else {
	$result=false;
	}
	return $result;
}