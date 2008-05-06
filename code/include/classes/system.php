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
  // | VERSION:      0.7.3                                               |
  // | DESCRIPTION:  Extends the System class definitions.               |
  // +-------------------------------------------------------------------+

  require_once ("code/include/classes/BASE/system.php");

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
      
      global $zAPPLE, $zSTRINGS, $zXML;
      
      // Create node class.
      $zNODES = new cSYSTEMNODES ();
      
      // Select all trusted nodes.
      $criteria = array ("Trust"    => NODE_TRUSTED,
      					 "Callback" => TRUE);
      $zNODES->SelectByMultiple ($criteria);
      
      // Count the number of users on the system.
      $USER = new cUSERAUTHORIZATION();
      $USER->Select (NULL, NULL);
      $users = $USER->CountResult();
      unset ($USER);
      
      $summary = $gSETTINGS['NodeSummary'];
        
      while ($zNODES->FetchArray()) {
        
        $domain = $zNODES->Entry;
        
        // Get the ASD version of the node.
        $version = $zAPPLE->GetNodeVersion ($domain);

        // Create the Remote class.
        $zREMOTE = new cREMOTE ($domain);
        
        $VERIFY = new cAUTHTOKENS ();
        $token = $VERIFY->LoadToken (NODE_ALL_USERS, $domain);
        if (!$token) {
          $token = $VERIFY->CreateToken (NODE_ALL_USERS, $domain);
        } // if
        unset ($VERIFY);
        
        global $zAPPLE;
        $summary = $zAPPLE->ParseTags ($summary);
        
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
      global $zAPPLE, $zXML;
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
      
      global $zSTRINGS;
      
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
      
      $zSTRINGS->Lookup ('MESSAGE.SAVE');
      
      $this->Message = $zSTRINGS->Output;
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
      
      ksort (&$return);
      
      return ($return);
  	} // GetServerListing
  	
  	// Get file listing from update server.
  	function NodeFileListing ($pSERVER, $pVERSION = false) {
  	  global $zAPPLE;
  		
  	  // If we don't have the version, get it.
  	  if ($pVERSION) $version = $pVERSION; else $version = $zAPPLE->GetNodeVersion ($pSERVER);
  		
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
      
      $lines = split ("\n", $return);
      foreach ($lines as $line) {
      	$values = split ("\t", $line);
      	$index = count($files);
      	$files[$index] = new stdClass();
      	$files[$index]->File = $values[0];
      	$files[$index]->Checksum = $values[1];
      	$files[$index]->Directory = $values[2];
      	$files[$index]->Magic = $values[3];
      	
      	// Remove any null values.
      	if ($values[0] == null) unset ($files[$index]);
      } // foreach
      
      // Delete all current records for this server and version.
      $query = "
		DELETE FROM $this->TableName 
		WHERE Server = '%s'
		AND Version = '%s'
      ";
      $query = sprintf ($query,
                        mysql_real_escape_string ($pSERVER),
                        mysql_real_escape_string ($version));
      $this->Query($query);
      
      // Insert data into the database.
      foreach ($files as $file) {
      	$this->Server = $pSERVER;
      	$this->Filename = $file->File;
      	$this->Checksum = $file->Checksum;
      	$this->Directory = $file->Directory;
      	$this->Magic = $file->Magic;
      	$this->Version = $version;
      	
      	$this->Add();
      } // foreach
      
      return ($files);
  	} // NodeFileListing
  	
  	function GetVersionListing ($pSERVER) {
  		
  	  global $zAPPLE;
  	  
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
      
      $versions = split ("\n", $return);
      
      // Loop through and remove any previous versions.
      foreach ($versions as $v => $version) {
      	// Remove if blank
      	if (!$version) continue;
      	// Remove if less than current.
        //if (!$zAPPLE->CheckVersion ($gAPPLESEEDVERSION, $version)) continue;
        // Set up result.
        $result[$version] = $version;
      
      } // foreach
      
      // Switch to ascending.
      $result = array_reverse ($result);
      
      return ($result);
  	} // GetVersionListing
  	
  } // cSYSTEMUPDATE
  
