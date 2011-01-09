<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: system.php                              CREATED: 10-31-2006 + 
  // | LOCATION: /code/include/classes/BASE/        MODIFIED: 04-19-2008 +
  // +-------------------------------------------------------------------+
  // | Copyright (c) 2004-2008 Appleseed Project                         |
  // +-------------------------------------------------------------------+
  // | This program is free software; you can redistribute it and/or     |
  // | modify it under the terms of the GNU General Public License       |
  // | as published by the Free Software Foundation; either version 2    |
  // | of the License, or (at your option) any later version.            |
  // |                                                                   |
  // | This program is distributed in the hope that it will be useful,   |
  // | but WITHOUT ANY WARRANTY; without even the implied warranty of    |
  // | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the     |
  // | GNU General Public License for more details.                      |	
  // |                                                                   |
  // | You should have received a copy of the GNU General Public License |
  // | along with this program; if not, write to:                        |
  // |                                                                   |
  // |   The Free Software Foundation, Inc.                              |
  // |   59 Temple Place - Suite 330,                                    | 
  // |   Boston, MA  02111-1307, USA.                                    |
  // |                                                                   |
  // |   http://www.gnu.org/copyleft/gpl.html                            |
  // +-------------------------------------------------------------------+
  // | AUTHORS: Michael Chisari <michael.chisari@gmail.com>              |
  // +-------------------------------------------------------------------+
  // | Extension of the Appleseed BASE API                               |
  // | VERSION:      0.7.9                                               |
  // | DESCRIPTION:  Extends the System class definitions.               |
  // +-------------------------------------------------------------------+

  require_once ("legacy/code/include/classes/BASE/system.php");

  // System strings class.
  class cSYSTEMSTRINGS extends cBASESYSTEMSTRINGS {

    // Extend functionality of BASE::Update () but purify input first.
    function Update () {
      // NOTE: Find a centralized way to purify.
      // $this->Purify ();
      parent::Update ();
    } // Update

    // Extend functionality of BASE::Add () but purify input first.
    function Add () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Add ();
    } // Add

  } // cSYSTEMSTRINGS

  // System options class.
  class cSYSTEMOPTIONS extends cBASESYSTEMOPTIONS {

    // Extend functionality of BASE::Update () but purify input first.
    function Update () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Update ();
    } // Update

    // Extend functionality of BASE::Add () but purify input first.
    function Add () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Add ();
    } // Add

  } // cSYSTEMOPTIONS
 
  // System logs class.
  class cSYSTEMLOGS extends cBASESYSTEMLOGS {
     
    // Extend functionality of BASE::Update () but purify input first.
    function Update () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Update ();
    } // Update

    // Extend functionality of BASE::Add () but purify input first.
    function Add () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Add ();
    } // Add

  } // cSYSTEMLOGS

  // System Tooltips class.
  class cSYSTEMTOOLTIPS extends cBASESYSTEMTOOLTIPS {
     
    // Extend functionality of BASE::Update () but purify input first.
    function Update () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Update ();
    } // Update

    // Extend functionality of BASE::Add () but purify input first.
    function Add () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Add ();
    } // Add

  } // cSYSTEMTOOLTIPS

  // System Nodes class.
  class cSYSTEMNODES extends cBASESYSTEMNODES {
     
  } // cSYSTEMNODES
  
  // System Maintenance Class
  class cSYSTEMMAINTENANCE extends cBASESYSTEMMAINTENANCE {
    
    function SendNodeNetworkUpdate () {
      global $gSITEDOMAIN, $gSETTINGS;
      global $gAPPLESEEDVERSION;
      
      global $zOLDAPPLE, $zXML;
      
      // Create node class.
      $zNODES = new cSYSTEMNODES ();
      
      // Select all trusted nodes.
      $criteria = array ("Trust"    => NODE_TRUSTED,
      					 "Callback" => TRUE);
      $zNODES->SelectByMultiple ($criteria);
      
      // Count the number of users on the system.
      $USER = new cOLDUSERAUTHORIZATION();
      $USER->Select (NULL, NULL);
      $users = $USER->CountResult();
      unset ($USER);
      
      $summary = $gSETTINGS['NodeSummary'];
        
      while ($zNODES->FetchArray()) {
        
        $domain = $zNODES->Entry;
        
        // Get the ASD version of the node.
        $version = $zOLDAPPLE->GetNodeVersion ($domain);

        // Create the Remote class.
        $zREMOTE = new cREMOTE ($domain);
        
        $VERIFY = new cAUTHTOKENS ();
        $token = $VERIFY->LoadToken (NODE_ALL_USERS, $domain);
        if (!$token) {
          $token = $VERIFY->CreateToken (NODE_ALL_USERS, $domain);
        } // if
        unset ($VERIFY);
        
        global $zOLDAPPLE;
        $summary = $zOLDAPPLE->ParseTags ($summary);
        
        $datalist = array ("gACTION"   => "ASD_UPDATE_NODE_NETWORK",
                           "gTOKEN"    => $token,
                           "gSUMMARY"  => $summary,
                           "gUSERS"    => $users,
                           "gVERSION"  => $gAPPLESEEDVERSION,
                           "gDOMAIN"   => $gSITEDOMAIN);
        $zREMOTE->Post ($datalist);

        $zXML->Parse ($zREMOTE->Return);

        $ip_address = $zXML->GetValue ("address", 0);
        $zREMOTEUSER->Username = $zXML->GetValue ("username", 0);
        $zREMOTEUSER->Fullname = $zXML->GetValue ("fullname", 0);
        $zREMOTEUSER->Domain = $zXML->GetValue ("domain", 0);
        unset ($zREMOTE);
      } // while
      
      return (TRUE);
    } // SendNodeNetworkUpdate
    
    function VerifyNodeNetwork () {
      global $zOLDAPPLE, $zXML;
      global $gAPPLESEEDVERSION;
      
      global $gSITEDOMAIN;
      
      $NODES = new cCONTENTNODES();
      
      $NODES->Select (NULL, NULL);
      
      while ($NODES->FetchArray()) {
        
        $zREMOTE = new cREMOTE ($NODES->Domain);
        
        $VERIFY = new cAUTHTOKENS ();
        $token = $VERIFY->LoadToken (NODE_ALL_USERS, $NODES->Domain);
        if (!$token) {
          $token = $VERIFY->CreateToken (NODE_ALL_USERS, $NODES->Domain);
        } // if
        unset ($VERIFY);
        
        $datalist = array ("gACTION"   => "SITE_VERSION",
                           "gTOKEN"    => $token,
                           "gVERSION"  => $gAPPLESEEDVERSION,
                           "gDOMAIN"   => $gSITEDOMAIN);
        $zREMOTE->Post ($datalist);

        $zXML->Parse ($zREMOTE->Return);
        
        $version = $zXML->GetValue ("version", 0);
        
        $NODE = new cCONTENTNODES;
        $NODE->Select ("Domain", $NODES->Domain);
        $NODE->FetchArray();
        $NODE->Verification = NODE_VERIFIED;
        
        if (!$version) {
          // No Appleseed Version was found.  Set to invalid.
          $NODE->Verification = NODE_INVALID;   
        } // if
        
        $NODE->Update();
        
        unset ($NODE);
        unset ($zREMOTE);
      } // while
      
      unset ($NODES);
      
      return (TRUE);
    } // VerifyNodeNetwork
    
    function ClearExpiredTokensAndSessions () {
    	
    	// Remove tokens over a day old.
    	$AUTHTOKENS = new cAUTHTOKENS ();
   		$deletestatement = "DELETE FROM " . $AUTHTOKENS->TableName . " WHERE Stamp <  DATE_ADD(now(),INTERVAL -1 DAY)";
    	$AUTHTOKENS->Query ($deletestatement);
    	unset ($AUTHTOKENS);
    	
    	// Remove verification over a month old.
    	$AUTHVERIFICATION = new cAUTHVERIFICATION ();
   		$deletestatement = "DELETE FROM " . $AUTHVERIFICATION->TableName . " WHERE Stamp <  DATE_ADD(now(),INTERVAL -1 MONTH)";
    	$AUTHVERIFICATION->Query ($deletestatement);
    	unset ($AUTHVERIFICATION);
    	
    	// Remove tokens over a month old.
    	$AUTHSESSIONS = new cAUTHSESSIONS ();
   		$deletestatement = "DELETE FROM " . $AUTHSESSIONS->TableName . " WHERE Stamp <  DATE_ADD(now(),INTERVAL -1 MONTh)";
    	$AUTHSESSIONS->Query ($deletestatement);
    	unset ($AUTHSESSIONS);
    	
    	// Remove sessions over a month old.
    	$USERSESSIONS = new cUSERSESSIONS ();
   		$deletestatement = "DELETE FROM " . $USERSESSIONS->TableName . " WHERE Stamp <  DATE_ADD(now(),INTERVAL -1 MONTH)";
    	$USERSESSIONS->Query ($deletestatement);
    	unset ($USERSESSIONS);
    	
    	return (TRUE);
    } // ClearExpiredTokens
    
    function GetTrustedList () {
      return (TRUE);
    } // GetTrustedList
    
    // Check if we're on a unix system capable of creating background processes.
    function CheckUnix () {
      
      $server = strtoupper (getenv('SERVER_SOFTWARE'));
      
      if (strstr ($server, 'UNIX')) {
        return (TRUE);
      } // if
      
      return (FALSE);
    } // CheckUnix
    
    // Perform system maintenance
    function Maintenance () {
      
      // Use an HTTP request which times out quickly to 
      global $gSITEDOMAIN;
      $path = "/maintenance/";

      // Check if any maintenance actions are due.
      $this->Select (NULL, NULL);

      // Loop through action timestamps.
      while ($this->FetchArray ()) {
        $timestamp = strtotime ($this->Stamp);
        
        // Select NOW() from the database for consistancy.
        $NOW = new cBASEDATACLASS();
        $NOW->Query ("SELECT NOW() AS NowStamp");
        $NOW->FetchArray();
        $now = strtotime ($NOW->NowStamp);
        unset ($NOW);
        
        $seconds = $this->Time * 60;
        $future = $timestamp + $seconds;

        // Check if we've hit the update time yet.
        if ($now < $future) {
          // We haven't.  On to the next action.
          continue;
        } // if
        
        // Update the time stamp.
        $TEMP = new cSYSTEMMAINTENANCE();
        $TEMP->Select ("tID", $this->tID);
        $TEMP->FetchArray();
        $TEMP->Stamp = SQL_NOW;
        $TEMP->Update();
        unset ($TEMP);
        
        $parameters = 'gACTION=' . $this->Action;
        
        // NOTE:  Duplicates requests.  Figure out why.
        $fp = fsockopen($gSITEDOMAIN, 80, $errno, $errstr, 1);
      
        if ($fp) {
          fputs($fp, "POST $path HTTP/1.0\r\n");
          fputs($fp, "Host: " . $gSITEDOMAIN . "\r\n");
          fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
          fputs($fp, "Content-length: " . strlen($parameters) . "\r\n");
          fputs($fp, "Connection: close\r\n\r\n");
          fputs($fp, $parameters);
        } else {
          // FAILED
        } // if
        
        fclose ($fp);
      } // while
      
      return (TRUE);
    } // Maintenance
    
  } // cSYSTEMMAINTENANCE

  // System Configuration class.
  class cSYSTEMCONFIG extends cBASESYSTEMCONFIG {
    
    function SaveConfiguration ($pCONFIGURATION) {
      
      foreach ($pCONFIGURATION as $Concern => $Value) {
        $this->Select ("Concern", $Concern);
        $this->FetchArray();
        if ($this->CountResult() == 0) {
          // No entries, add.
          $this->Concern = $Concern;
          $this->Value = $Value;
          $this->Add();
        } else {
          // Update.
          $this->Value = $Value;
          $this->Update();
        } // if
      } // foreach
      
      $this->Message = __("Configuration Saved");
      
      $this->Error = 0;
      
      return (TRUE);
    } // SaveConfiguration
     
  } // cSYSTEMCONFIG

  // System Update class.
  class cSYSTEMUPDATE extends cBASESYSTEMUPDATE {
  	
  	function GetServerListing () {
  		
  	  // Retrieve all servers from database
      $statement = "
        SELECT DISTINCT(Server) FROM $this->TableName;
      ";
      $this->Query ($statement);
      
      while ($this->FetchArray()) {
      	$return[$this->Server] = $this->Server;
      } // while
      
      // If update.appleseedproject.org isn't listed, add it to the list.
      if (!in_array('update.appleseedproject.org', $return)) $return['update.appleseedproject.org'] = 'update.appleseedproject.org';
      
      ksort ($return);
      
      return ($return);
  	} // GetServerListing
  	
  	// Get file listing from update server.
  	function NodeFileListing ($pSERVER, $pVERSION = false) {
  	  global $zOLDAPPLE;
  		
  	  // If we don't have the version, get it.
  	  if ($pVERSION) $version = $pVERSION; else $version = $zOLDAPPLE->GetNodeVersion ($pSERVER);
  		
      // Pull from node
      if (function_exists ("curl_exec")) {
      	$ch = curl_init();
      	
      	$URL = 'http://' . $pSERVER . '/?files';
      	
      	if ($pVERSION) $URL .= '=' . $pVERSION;

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // grab URL and pass it to the browser
        ob_start();
        curl_exec($ch);
        $return = ob_get_clean();

        // close cURL resource, and free up system resources
        curl_close($ch);
        
      } else {
        $parameters = 'files';
        
      	if ($pVERSION) $parameters .= '=' . $pVERSION; else $parameters .= '=1';
 
        $path = "/"; // path to cgi, asp, php program
 
        // Open a socket and set timeout to 10 seconds.
        $fp = fsockopen($pSERVER, 80, $errno, $errstr, 10);
 
        fputs($fp, "POST $path HTTP/1.0\r\n");
        fputs($fp, "Host: " . $pSERVER . "\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: " . strlen($parameters) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $parameters);
   
        while (!feof($fp)) {
           $data .= fgets($fp,1024);
        } // while
        $return = substr(strstr($data,"\r\n\r\n"),4);
      } // if
      
      $files = null;
      
      $lines = explode ("\n", $return);
      foreach ($lines as $line) {
      	$values = explode ("\t", $line);
      	// If we don't get four columns back, it's not a valid line.
      	if (count($values) != 5) continue;
      	$index = count($files);
      	$files[$index] = new stdClass();
      	$files[$index]->File = $values[0];
      	$files[$index]->Checksum = $values[1];
      	$files[$index]->Directory = $values[2];
      	$files[$index]->Magic = $values[3];
      	
      	// Remove any null values.
      	if ($values[0] == null) unset ($files[$index]);
      } // foreach
      
      // If no file listing exists, return false
      if (count($files) == 0) return (FALSE);
      
      return ($files);
  	} // NodeFileListing
  	
  	function GetVersionListing ($pSERVER) {
  	  global $zOLDAPPLE;
  	  
  	  global $gAPPLESEEDVERSION;
  		
      // Pull from node
      if (function_exists ("curl_exec")) {
      	$ch = curl_init();
      	
      	$URL = 'http://' . $pSERVER . '/?versions';
      	
        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // grab URL and pass it to the browser
        ob_start();
        curl_exec($ch);
        $return = ob_get_clean();

        // close cURL resource, and free up system resources
        curl_close($ch);
        
      } else {
        $parameters = 'versions=1';
        
        $path = "/"; // path to cgi, asp, php program
 
        // Open a socket and set timeout to 2 seconds.
        $fp = fsockopen($pSERVER, 80, $errno, $errstr, 2);
 
        fputs($fp, "POST $path HTTP/1.0\r\n");
        fputs($fp, "Host: " . $pSERVER . "\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: " . strlen($parameters) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $parameters);
   
        while (!feof($fp)) {
           $data .= fgets($fp,1024);
        } // while
        $return = substr(strstr($data,"\r\n\r\n"),4);
      } // if
      
      $versions = explode ("\n", $return);
      
      // Loop through and remove any previous versions.
      foreach ($versions as $v => $version) {
      	// Remove if blank
      	if (!$version) continue;
      	// Remove if less than current.
        //if (!$zOLDAPPLE->CheckVersion ($gAPPLESEEDVERSION, $version)) continue;
        // Set up result.
        $result[$version] = $version;
      
      } // foreach
      
      // Switch to ascending.
      $result = array_reverse ($result);
      
      return ($result);
  	} // GetVersionListing
  	
  	function AddServer ($pSERVER) {
  	  global $zOLDAPPLE;
  	  
  	  $this->Select("Server", $pSERVER);
  	  
  	  // Already exists in database.
  	  if ($this->CountResult() > 0) {
        $this->Message = __( "Server Exists", array ( "server" => $pSERVER ) );
        $this->Error = -1;
        return (FALSE);
  	  } // if
  		
      if ( (!$version = $zOLDAPPLE->GetNodeVersion ($pSERVER)) or 
  	       (!$files = $this->NodeFileListing ($pSERVER)) ) {
        $this->Message = __( "Invalid Server", array ( "server" => $pSERVER ) );
        $this->Error = -1;
  	  	return (FALSE);
  	  } // if
  	  
  	  $this->Server = $pSERVER;
  	  $this->Add();
  	  
      $this->Message = __( "Server Added", array ( "server" => $pSERVER ) );
      $this->Error = 0;
      
  	  return (TRUE);
  	} // AddServer
  	
  	function RemoveServer ($pSERVER) {
  	  global $zOLDAPPLE;
  		
      // Delete all current records for this server and version.
      $query = "
		DELETE FROM $this->TableName 
		WHERE Server = '%s'
      ";
      $query = sprintf ($query,
                        mysql_real_escape_string ($pSERVER));
      $this->Query($query);
  	  
  	  $zOLDAPPLE->SetTag ('SERVERNAME', $pSERVER);
  	  
      $this->Message = __( "Server Removed", array ( "server" => $pSERVER ) );
      $this->Error = 0;
      
  	  return (TRUE);
  	} // RemoveServer
  	
  	// Traverse through directory tree, find unwritable files.
  	function CheckDirectoryTree () {
  		
  		// If the current directory isn't writable, return it.
  		if (!is_writable (getcwd())) {
  			$files[] = getcwd();
  			return ($files);
  		} // if
  		
  		$code = getcwd() . "/code";
  		$frameworks = getcwd() . "/frameworks";
  		$themes = getcwd() . "/themes";
  		
  		$files = $this->ListDirectory ($code);
  		$files = array_merge ($files, $this->ListDirectory ($frameworks));
  		$files = array_merge ($files, $this->ListDirectory ($themes));
  		
  		foreach ($files as $f => $file) {
  			if (is_writable ($file)) unset ($files[$f]);
  		} // foreach
  		
  		return ($files);
  	} // CheckDirectoryTree
  	
    function ListDirectory ($pBEGIN) {
      $return = array();
      
      global $gLISTINGORIGIN;
      if (!$gLISTINGORIGIN) $gLISTINGORIGIN = $pBEGIN;
      
      $skip = array ($gLISTINGORIGIN . "/photos", $gLISTINGORIGIN . "/attachments");
      
      $handle = opendir($pBEGIN);
      
      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
          if (is_dir($pBEGIN. "/" . $file)) {
            $return = array_merge($return, $this->ListDirectory ($pBEGIN. "/" . $file));           
            $file = $pBEGIN . "/" . $file;
            $return[] = preg_replace("/\/\//si", "/", $file);
          } else {
            $file = $pBEGIN . "/" . $file;
            $return[] = preg_replace("/\/\//si", "/", $file);
          } // if
        } // if
      } // while

      closedir($handle);

      return ($return);
    } // ListDirectory
    
    function CreateBackupDirectories ($pCURRENTREFERENCE) {
      global $zOLDAPPLE;
      
      global $gAPPLESEEDVERSION, $gFRAMELOCATION;
      global $gRESULT;
      
      global $bRESULT;
      
      $backupDirectory = 'backup/' . $gAPPLESEEDVERSION;
      
      // Create the main backup directory.
      if (!mkdir ($backupDirectory)) {
      	if (file_exists($backupDirectory)) {
      	  $gRESULT = __( "Backup Directory Already Exists");
      	} else {
      	  $gRESULT = __( "Could Not Create Backup Directory");
      	} // if
        $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.error.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        return (FALSE);
      } // if
      
      $permissions = TRUE;
      
      if (!chmod ($backupDirectory, 0777)) {
        $permissions = FALSE;
      } // if
      
  	  $directories = null;
  	  
  	  foreach ($pCURRENTREFERENCE as $cur) {
  	  	if ($cur->Directory) {
  	  		if (!$cur->Magic) {
  	  			$directories[] = $cur->File;
  	  		} // if
  	  	} // if
  	  } // foreach
  	  
  	  sort ($directories);
  	  
  	  foreach ($directories as $directory) {
        if (!mkdir ($backupDirectory . '/' . $directory)) {
      	  $gRESULT = __( "Could Not Create Backup Directory");
          $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.error.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      	  return (FALSE);
      	} // if
        if (!chmod ($backupDirectory . '/' . $directory, 0777)) {
          $permissions = FALSE;
        } // if
  	  } // foreach
  	  
  	  if (!$permissions) {
      	  $gRESULT = __( "Could Not Set Permission" );
          $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.warning.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
  	  } // if
  	  
 	  $gRESULT = __( "Backup Directory Created" );
      $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.message.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
  	  return (TRUE);
    } // CreateBackupDirectories
    
    function CreateNewDirectories ($pLATESTREFERENCE) {
    	
      global $zOLDAPPLE;
      
      global $gAPPLESEEDVERSION, $gFRAMELOCATION;
      global $gRESULT;
      
      global $bRESULT;
      
  	  $directories = null;
  	  
  	  foreach ($pLATESTREFERENCE as $cur) {
  	  	if ($cur->Directory) {
  	  		if (!$cur->Magic) {
  	  			$directories[] = $cur->File;
  	  		} // if
  	  	} // if
  	  } // foreach
  	  
  	  sort ($directories);
  	  
  	  $permissions = TRUE;
  	  foreach ($directories as $directory) {
  	  	if (!file_exists ($directory)) {
          if (!mkdir ($directory)) {
            $gRESULT = __( "Error Creating New Directories" );
            $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.error.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      	    return (FALSE);
      	  } // if
          if (!chmod ($directory, 0777)) {
      	  	$permissions = FALSE;
      	  } // if
  	  	} // if
  	  } // foreach
  	  
  	  if (!$permissions) {
          $gRESULT = __( "Could Not Set Permissions" );
          $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.warning.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
  	  } // if
  	  
      $gRESULT = __( "New Directories Created" );
      $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.message.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
  	  return (TRUE);
    } // CreateNewDirectories
    
    function Merge ($pCURRENT, $pLATEST, $pSERVER, $pVERSION) {
    	
      global $zOLDAPPLE;
      
      global $gAPPLESEEDVERSION, $gFRAMELOCATION;
      global $gRESULT;
      
      global $bRESULT;
      
      // Find all new files.
      $new = null;
      foreach ($pLATEST as $latest) {
      	$found = false;
      	foreach ($pCURRENT as $current) {
      	  if ($latest->File == $current->File) {
      	  	$found = true;
      	  } // if
      	} // foreach
      	if ($found == false) $new[] = $latest->File;
      } // foreach
      
      // Find all unused old files.
      $old = null;
      foreach ($pCURRENT as $current) {
      	$found = false;
        foreach ($pLATEST as $latest) {
      	  if ($current->File == $latest->File) {
      	  	$found = true;
      	  } // if
      	} // foreach
      	if ($found == false) $old[] = $current->File;
      } // foreach
      
      // Find all common files.
      $common = null;
      foreach ($pCURRENT as $current) {
      	$found = false;
        foreach ($pLATEST as $latest) {
      	  if ($current->File == $latest->File) {
      	  	$found = true;
      	  } // if
      	} // foreach
      	if ($found == true) $common[] = $current->File;
      } // foreach
      
      // Check if compression is available.
      $compression = FALSE;
      if (function_exists ("gzuncompress")) $compression = TRUE;
      
      // Directory to start from.
      $backup = "backup/" . $gAPPLESEEDVERSION . "/";
      
      global $zDEBUG;
      
      // Backup and delete all the old files.
      foreach ($old as $o) {
      	// Move file to backup directory.
      	if (!rename ($o, $backup . $o)) {
      		global $gBACKUPFILE;
      		$gBACKUPFILE = $o;
            $gRESULT = __( "Could Not Rename New Directories", array ( "filename" => $gBACKUPFILE ) );
            $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.warning.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      	} // if
      } // foreach
      
      // Backup and retrieve all the common files.
      foreach ($common as $c) {
      	
      	// Find the file in the list of latest files.
        $index = $this->FindFile ($c, $pLATEST);
        
        // Do not handle directories.
        if ($pLATEST[$index]->Directory) continue;
        
      	// Backup old file.
      	if (!rename ($c, $backup . $c)) {
      		global $gBACKUPFILE;
      		$gBACKUPFILE = $o;
            $gRESULT = __( "Could Not Rename Common Files", array ( "filename" => $gBACKUPFILE ) );
            $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.warning.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      	} // if
      	
      	// Retrieve new file.
        $content = $this->Retrieve ($pSERVER, $pVERSION, $c, $compression);
        
        // If it's a magic file, retrieve it, and save it as a ".magic" file.
        if ($pLATEST[$index]->Magic) {
          // If it doesn't exist, save it, but still warn the admin.
          if (file_exists ($c)) {
            $this->SaveFile ($content, $c . ".magic");
          } else {
            $this->SaveFile ($content, $c);
          } // if
    	  global $gMAGICFILE;
          $gMAGICFILE = $o;
          $gRESULT = __( "Processing Magic File", array ( "filename" => $gMAGICFILE ) );
          $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.warning.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } else {
          $this->SaveFile ($content, $c);
        } // if
        
      	if (!$this->FixPermissions ($c)) {
      	  global $gBACKUPFILE;
          $gBACKUPFILE = $o;
          $gRESULT = __( "Could Not Set New Permissions", array ( "filename" => $gBACKUPFILE ) );
          $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.warning.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      	} // if
      } // foreach
      
      // Retrieve all the new files.
      foreach ($new as $n) {
      	
      	// Find the file in the list of latest files.
        $index = $this->FindFile ($n, $pLATEST);
        
        // Create directories.
        if ($pLATEST[$index]->Directory) {
          mkdir ($n);
      	  if (!chmod ($n, 0777)) {
      	    global $gBACKUPFILE;
            $gBACKUPFILE = $n;
            $gRESULT = __( "Could Not Set New Permissions", array ( "filename" => $gBACKUPFILE ) );
            $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.warning.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      	  } // if
      	  continue;
        } // if
        
        // Retrieve new file.
        $content = $this->Retrieve ($pSERVER, $pVERSION, $n, $compression);
        
        // If it's a magic file, retrieve it, and save it as a ".magic" file.
        if ($pLATEST[$index]->Magic) {
          // If it doesn't exist, save it, but still warn the admin.
          if (file_exists ($n)) {
            $this->SaveFile ($content, $n . ".magic");
          } else {
            $this->SaveFile ($content, $n);
          } // if
      	  global $gMAGICFILE;
          $gMAGICFILE = $n;
          $gRESULT = __( "Processing Magic File", array ( "filename" => $gMAGICFILE ) );
          $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.warning.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } else {
          $this->SaveFile ($content, $n);
        } // if
        
      	if (!$this->FixPermissions ($n)) {
      	  global $gBACKUPFILE;
          $gBACKUPFILE = $n;
          $gRESULT = __( "Could Not Set New Permissions", array ( "filename" => $gBACKUPFILE ) );
          $bRESULT .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.warning.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      	} // if
      } // foreach
      
      return (TRUE);
    } // Merge
    
    function Backup ($pVERSION) {
    } // Backup
    
    function Restore ($pVERSION) {
    } // Restore
    
  	function Retrieve ($pSERVER, $pVERSION, $pFILENAME, $pCOMPRESSED = FALSE) {
      $release = 'releases/' . $pVERSION . '/';
      
      // Pull from node
      if (function_exists ("curl_exec")) {
      	$ch = curl_init();
      	
      	if ($pCOMPRESSED) 
      	  $URL = 'http://' . $pSERVER . '/?cfile=' . $release . $pFILENAME;
      	else
      	  $URL = 'http://' . $pSERVER . '/?file=' . $release . $pFILENAME;
      	
        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // grab URL and pass it to the browser
        ob_start();
        curl_exec($ch);
        $return = ob_get_clean();

        // close cURL resource, and free up system resources
        curl_close($ch);
        
      } else {
      	if ($pCOMPRESSED) 
          $parameters = 'cfile=' . $pFILENAME;
        else
          $parameters = 'file=' . $pFILENAME;
        
        $path = "/"; // path to cgi, asp, php program
 
        // Open a socket and set timeout to 2 seconds.
        $fp = fsockopen($pSERVER, 80, $errno, $errstr, 2);
 
        fputs($fp, "POST $path HTTP/1.0\r\n");
        fputs($fp, "Host: " . $pSERVER . "\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: " . strlen($parameters) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $parameters);
   
        while (!feof($fp)) {
           $data .= fgets($fp,1024);
        } // while
        $return = substr(strstr($data,"\r\n\r\n"),4);
      } // if
      
      if ($pCOMPRESSED) $return = gzuncompress ($return);
      
      return ($return);
  	} // Retrieve
  	
  	function SaveFile ($pCONTENT, $pFILENAME) {
  		
  	  if (!$handle = fopen($pFILENAME, 'w')) return (FALSE);
  	  
      fwrite($handle, $pCONTENT, strlen ($pCONTENT));
      fclose($handle);
      
      return (TRUE);
  	} // SaveFile
  	
  	// Fix permissions on a regular file.
  	function FixPermissions ($pFILENAME) {
      if (!chmod ($pFILENAME, 0666)) return (FALSE);
      
      return (TRUE);
  	} // FixPermissions
  	
  	function FindFile ($pFILE, $pREFERENCE) {
  	  foreach ($pREFERENCE as $r => $ref) {
        if ($ref->File == $pFILE) {
        	return ($r);
        } // if
  	  } // foreach
  	  return (FALSE);
  	} // FindFile
  	
  } // cSYSTEMUPDATE
  
