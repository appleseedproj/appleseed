<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: 0.7.3.php                               CREATED: 04-24-2007 + 
  // | LOCATION: /legacy/code/include/classes/asd/   MODIFIED:10-26-2010 +
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
  // | VERSION:      0.7.7                                               |
  // | DESCRIPTION.  Light-weight server class definitions.              |
  // +-------------------------------------------------------------------+

  require_once ("legacy/code/include/classes/BASE/remote.php");
  require_once ("legacy/code/include/classes/BASE/xml.php");
  require_once ('legacy/code/include/external/JSON/JSON.php');
  
  // Light-weight server class for node communication.
  class cSERVER {
    
    var $SiteURL;
    var $SiteDomain;
    var $Database;
    var $DatabaseUsername;
    var $DatabaseHost;
    var $DatabasePassword;
    var $DatabaseLink;
    var $TablePrefix;
    var $Token;
    var $Token_Uid;
    var $ReturnUsername;

    function Initialize ($pHOST = NULL, $pTOKENCHECK = TRUE) {
    	
      $gTOKEN = $_POST['gTOKEN'];
      $gIDENTIFIER = $_POST['gIDENTIFIER'];
      
      global $gAPPLESEEDVERSION;
      $gAPPLESEEDVERSION = '0.7.3';
      
      // Create REMOTE object.
      $this->REMOTE = new cREMOTE ($pHOST);
      
      // Create XML object.
      $this->XML = new cOLDXML ();
      
      // Initialize Variables.
      $this->SiteURL = null;
      $this->SiteDomain = null;
      $this->Database = null;
      $this->DatabaseHost = null;
      $this->DatabaseUsername = null;
      $this->DatabasePassword = null;
      $this->DatabaseLink = null;
      $this->TablePrefix = null;
      $this->Token = null;
      $this->Token_uID = null;
      $this->ReturnUsername = null;
      
      // Load Site Information.
      $this->LoadSiteInfo ();
      
      // Check for an authentication token.
      if ( (!$gTOKEN) and (!$gIDENTIFIER) and ($pTOKENCHECK) ) {
        $errortitle = "ERROR.NOTOKEN";
        echo $this->XML->ErrorData ($errortitle);
        exit;
      } // if
      
      // Create JSON Object
      $this->JSON = new cJSON ();
      
      // Connect To The Database.
      $this->Connect ();
      
      // Check if we are in shutdown mode.
      if ($this->CheckShutdown()) {
        $errortitle = "ERROR.SHUTDOWN";
        echo $this->XML->ErrorData ($errortitle);
        exit;
      } // if
      
      return (TRUE);
    } // Initialize
    
    function CheckShutdown () {
    	
      $systemConfig = $this->TablePrefix . "systemConfig";
    	
      $sql_statement = "
		SELECT Value FROM $systemConfig WHERE Concern = 'Shutdown';
      ";
      
      $sql_result = mysql_query ($sql_statement);
      
      $result = mysql_fetch_assoc ($sql_result);
      
      switch ($result['Value']) {
      	case 'YES':
      	case 'ADMIN':
      		return (TRUE);
      	break;
      	case 'NO':
      		return (FALSE);
      	break;
      } // switch
      
      return (FALSE);
    } // CheckShutdown
    
    function NoVersion () {
    	
      // Initialize Class
      $this->Initialize(NULL, FALSE);
      
      $errortitle = "ERROR.NOVERSION";
      $return = $this->XML->ErrorData ($errortitle);
      
      return ($return);
    } // NoVersion
    
    function LoadSiteInfo () {
      eval ( GLOBALS );
      
      $this->Database = $zApp->Config->GetConfiguration ('db');
      $this->DatabaseUsername = $zApp->Config->GetConfiguration ('un');
      $this->DatabasePassword = $zApp->Config->GetConfiguration ('pw');
      $this->DatabaseHost =  $zApp->Config->GetConfiguration ('host');
      $this->TablePrefix =  $zApp->Config->GetConfiguration ('pre');
      $this->SiteURL =  $zApp->Config->GetConfiguration ('url');
      $this->SiteDomain = str_replace ("http://", "", $zApp->Config->GetConfiguration ('url'));
      $this->SiteDomain = str_replace ("/", "", $this->SiteDomain);
      
      return (TRUE);
    } // LoadSiteInfo
    
    function Connect () {
      
      if (!$this->DatabaseLink = mysql_pconnect ($this->DatabaseHost, $this->DatabaseUsername, $this->DatabasePassword))
        return (FALSE);
        
      if (!mysql_select_db ($this->Database, $this->DatabaseLink))
        return (FALSE);
      
      return (TRUE);
    } // Connect
    
    function TokenCheckLocal () {
    	
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize ($gDOMAIN);
      
      $authTokens = $this->TablePrefix . "authTokens";
      $userAuth = $this->TablePrefix . "userAuthorization";
      $userProfile = $this->TablePrefix . "userProfile";
      
      // Check our local database, see if this token exists.
      
      $sql_statement = "
        SELECT $authTokens.Username 
        FROM   $authTokens
        WHERE  $authTokens.Token = '%s'
        AND    $authTokens.Domain = '%s'
        AND    $authTokens.Stamp > DATE_ADD(now(), INTERVAL -30 MINUTE) 
        AND    $authTokens.Source = 10
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gTOKEN),
                                mysql_real_escape_string ($gDOMAIN));
                                
      $sql_result = mysql_query ($sql_statement);
      
      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      if ($result_count == 0) {
        // No results found.  Unauthenticated. Send back an error.
        $return = $this->XML->ErrorData ("ERROR.NOTFOUND");
        return ($return);
      } else {
        global $gFULLNAME;
        global $gUSERNAME;
        $result = mysql_fetch_assoc ($sql_result);
        // Load and send back the username.
        $username = $result['Username'];
        mysql_free_result ($sql_result);
        if ($username == '*') {
          $gUSERNAME = '*';
          $gFULLNAME = '*';
        } else {
          $gUSERNAME = $result['Username'];
          // Load and send back the fullname.
        
          $sql_statement = "SELECT $userProfile.Fullname,$userProfile.Alias
                            FROM   $userProfile,$userAuth
                            WHERE  $userAuth.uID = $userProfile.userAuth_uID
                            AND    $userAuth.Username = '%s'
          ";
          
          $sql_statement = sprintf ($sql_statement,
                                    mysql_real_escape_string ($gUSERNAME));
                                
          $sql_result = mysql_query($sql_statement);
          $result = mysql_fetch_assoc ($sql_result);
          $gFULLNAME = $result['Fullname'];
          if ($result['Alias']) $gFULLNAME = $result['Alias'];
        } // if
        
        $this->XML->Load ("legacy/code/include/data/xml/token_check.xml");
        mysql_free_result ($sql_result);
      } // if
      
      $return = $this->XML->Data;
      
      return ($return);
    } // TokenCheckLocal
    
    function TokenCheckRemote ($pTOKEN, $pDOMAIN) {
    	
      global $gAPPLESEEDVERSION;
     
      $userAuth = $this->TablePrefix . 'userAuthorization'; 
      $authTokens = $this->TablePrefix . 'authTokens'; 
      
      // First, check our remote cache database, see if this token exists.
      $sql_statement = "
        SELECT $authTokens.Username 
        FROM   $authTokens
        WHERE  $authTokens.Token = '%s'
        AND    $authTokens.Domain = '%s'
        AND    $authTokens.Source = 20
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($pTOKEN),
                                mysql_real_escape_string ($pDOMAIN));
                                
      $sql_result = mysql_query ($sql_statement);
      
      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      // Check if a token record was found.
      if ($result_count == 0) {
        // If not, send back to see if Token is valid.
        $REMOTE = new cREMOTE ($pDOMAIN);
        $datalist = array ("gACTION"   => "ASD_TOKEN_CHECK",
                           "gTOKEN"    => $pTOKEN,
                           "gVERSION"  => $gAPPLESEEDVERSION,
                           "gDOMAIN"   => $this->SiteDomain);
        $REMOTE->Post ($datalist, 1);

        $this->XML->Parse ($REMOTE->Return);

        // If no appleseed version was retrieved, an invalid url was used.
        $version = ucwords ($this->XML->GetValue ("version", 0));
        if (!$version) return (FALSE);
        
        $result = $this->XML->GetValue ("result", 0);
        $username = $this->XML->GetValue ("username", 0);
        $fullname = $this->XML->GetValue ("fullname", 0);
        
        // Check if token is valid.
        if ($result) {
          // If so, store for later use.
          
          $this->TokenStore($pTOKEN, $username, $pDOMAIN);
          
          $this->ReturnUsername = $username;
          $this->ReturnFullname = $fullname;
        } else {
          // If not, send an error.
          return (FALSE);
        } // if
      } else {
        // A result was found, use this result.
        $result = mysql_fetch_assoc ($sql_result);
        mysql_free_result ($sql_result);
        
        $this->ReturnUsername = $result['Username'];
        $this->Token = $pTOKEN;
      } // if
      
      return (TRUE);
    } // TokenCheckRemote
    
    function TokenStore ($pTOKEN, $pUSERNAME, $pDOMAIN) {
      
      $authTokens = $this->TablePrefix . $authTokens;
      
      // Delete all existing tokens.
      $sql_statement = "
        DELETE FROM $authTokens
        WHERE Username = '%s'
        AND   Domain   = '%s'
        AND   Source   = 20;
      "; 
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($pDOMAIN));
                                
      $sql_result = mysql_query ($sql_statement);
      
      $authTokens = $this->TablePrefix . 'authTokens';
      
      // Insert new token.
      $sql_statement = "
        INSERT INTO $authTokens
        (Username, Domain, Address, Host, Token, Stamp, Source)
        VALUES ('%s', '%s', '%s', '%s', '%s', NOW(), 20);
      "; 
      
      $address = $_SERVER['REMOTE_ADDR'];
      $host = gethostbyaddr ($address);
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($pDOMAIN),
                                mysql_real_escape_string ($address),
                                mysql_real_escape_string ($host),
                                mysql_real_escape_string ($pTOKEN));
                                
      $sql_result = mysql_query ($sql_statement);
      
      return (TRUE);
    } // TokenStore
    
    function FriendRequest () {
    	
      $gUSERNAME = $_POST['gUSERNAME'];
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize ($gDOMAIN);
      
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($gUSERNAME, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      $userAuth = $this->TablePrefix . "userAuthorization";
      $friendInfo = $this->TablePrefix . "friendInformation";
      
      $friendInfo = $this->TablePrefix . "friendInformation";
      $userProfile = $this->TablePrefix . "userProfile";
      $userAuth = $this->TablePrefix . "userAuthorization";
      
      // Select the User ID
      $sql_statement = "
        SELECT $userAuth.uID, $userProfile.Fullname, $userProfile.Alias
        FROM   $userAuth,$userProfile
        WHERE  $userAuth.Username = '%s'
        AND    $userProfile.userAuth_uID = $userAuth.uID
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME)
                                );
      
      $sql_result = mysql_query ($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      global $gFULLNAME;
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      $uID = $result['uID'];
      mysql_free_result ($sql_result);
      
      // Delete any current records.
      $sql_statement = "
        DELETE FROM $friendInfo
        WHERE userAuth_uID = %s
        AND Username = '%s'
        AND Domain = '%s'
      ";      
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($uID),
                                mysql_real_escape_string ($gUSERNAME),
                                mysql_real_escape_string ($gDOMAIN)
                                );
                                
      $sql_result = mysql_query ($sql_statement);
      mysql_free_result ($sql_result);
      
      // Select the max sort ID
      $sql_statement = "
        SELECT MAX(sID)
        FROM   $friendInfo
        WHERE    userAuth_uID = %s 
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($uID)
                                );
      
      $sql_result = mysql_query ($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $sID = $result['sID'];
      if ($sID) 
        $sID++;
      else
        $sID = 1;
      mysql_free_result ($sql_result);
      
      // Insert the friend record.
      $sql_statement = "
         INSERT INTO $friendInfo
         (userAuth_uID, sID, Username, Domain, Verification, Stamp)
         VALUES
         (%s, %s, '%s', '%s', 2, NOW())
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($uID),
                                mysql_real_escape_string ($sID),
                                mysql_real_escape_string ($this->ReturnUsername),
                                mysql_real_escape_string ($gDOMAIN)
                                );
                                
      $sql_result = mysql_query($sql_statement);
      
      mysql_free_result ($sql_result);
      
      global $gRESULT;
      $gRESULT = 1;
                                
      // Load and send back the fullname.
      global $gFULLNAME;
      
      $sql_statement = "
        SELECT Fullname, Alias
        FROM   $userProfile,$userAuth
        WHERE  $userProfile.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      mysql_free_result ($sql_result);
      
      $this->XML->Load ("legacy/code/include/data/xml/friend_request.xml");
      $return = $this->XML->Data;
      
      return ($return);
    } // FriendRequest
    
    function GroupJoin () {
    	
      $gGROUPNAME = $_POST['gGROUPNAME'];
      $gUSERNAME = $_POST['gUSERNAME'];
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
    	
      $this->Initialize ($gDOMAIN);
      
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($gUSERNAME, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      $zGROUPS = new cGROUPINFORMATION ();
      $zGROUPS->Select ("Name", $gGROUPNAME);
 
      if ($zGROUPS->CountResult() == 0) {
        $gSUCCESS = FALSE;
        $gMESSAGE = "ERROR.NOTFOUND";
        $data = implode ("", file ("legacy/code/include/data/xml/group_join.xml"));
        $return = $zOLDAPPLE->ParseTags ($data);
        echo $return;
        exit;
      } // if
      $remoteusername = $gUSERNAME;
      $remotedomain = $gDOMAIN;
 
      $zGROUPS->FetchArray ();
      $membercriteria = array ("Username"                 => $remoteusername,
                               "Domain"                   => $remotedomain,
                               "groupInformation_tID"     => $zGROUPS->tID);
      $zGROUPS->groupMembers->SelectByMultiple ($membercriteria);
      $zGROUPS->groupMembers->FetchArray ();
 
      // Check for existing group membership record.
      if ($zGROUPS->groupMembers->CountResult() == 0) {
        $zGROUPS->groupMembers->groupInformation_tID = $zGROUPS->tID;
        $zGROUPS->groupMembers->Username = $remoteusername;
        $zGROUPS->groupMembers->Domain = $remotedomain;
        if ( ($zGROUPS->Access == GROUP_ACCESS_OPEN) or
             ($zGROUPS->Access == GROUP_ACESSS_OPEN_MEMBERSHIP) ) {
          $gMESSAGE = "MESSAGE.JOINED";
          $zGROUPS->groupMembers->Verification = GROUP_VERIFICATION_APPROVED;
        } else {
          $gMESSAGE = "MESSAGE.PENDING";
          
          $zGROUPS->groupMembers->Verification = GROUP_VERIFICATION_PENDING;
        } // if
 
        $zGROUPS->groupMembers->Stamp = SQL_NOW;
        $zGROUPS->groupMembers->Add ();
      } // if
      $gSUCCESS = TRUE;
 
      unset ($zGROUPS);
 
      $data = implode ("", file ("legacy/code/include/data/xml/group_join.xml"));
      $this->XML->Data = $zOLDAPPLE->ParseTags ($data);
       
      return (TRUE);
     } // GroupJoin
     
     function GroupLeave () {
     	
     	$AUTH->Select ("Token", $gTOKEN);

        if ($AUTH->CountResult () == 0) {
          $code = 1000;
          $message = "ERROR.INVALIDTOKEN";
          $return = $zXML->ErrorData ($code, $message);
          echo $return; exit;
        } // if
  
        $AUTH->FetchArray ();
  
        $remoteusername = $AUTH->Username;
        $remotedomain = $AUTH->Domain;
        list ($remotefullname, $NULL) = $zOLDAPPLE->GetUserInformation ($remoteusername, $remotedomain);
  
        unset ($AUTH);
  
  
       $zGROUPS = new cGROUPINFORMATION ();
       $zGROUPS->Select ("Name", $gGROUPNAME);
  
       if ($zGROUPS->CountResult() == 0) {
         $gSUCCESS = FALSE;
         $gMESSAGE = "ERROR.NOTFOUND";
         $data = implode ("", file ("legacy/code/include/data/xml/group_leave.xml"));
         $return = $zOLDAPPLE->ParseTags ($data);
         echo $return;
         exit;
       } // if
  
       $zGROUPS->FetchArray ();
       $membercriteria = array ("Username"                 => $remoteusername,
                                "Domain"                   => $remotedomain,
                                "groupInformation_tID"     => $zGROUPS->tID);
       $zGROUPS->groupMembers->SelectByMultiple ($membercriteria);
       $zGROUPS->groupMembers->FetchArray ();
       $zGROUPS->groupMembers->Delete ();
  
       $gSUCCESS = TRUE;
       $gMESSAGE = "MESSAGE.LEFT";
  
       unset ($zGROUPS);
  
       $data = implode ("", file ("legacy/code/include/data/xml/group_leave.xml"));
       $return = $zOLDAPPLE->ParseTags ($data);
       echo $return;
      
     } // GroupLeave
     
     function GroupInformation () {
       $GROUP = new cGROUPINFORMATION ();
       $GROUP->Select ("Name", $gGROUPNAME);

       if ($GROUP->CountResult() == 0) {
         $gSUCCESS = FALSE;
         $gFULLNAME = "unknown";
       } else {
         $GROUP->FetchArray ();
         $gSUCCESS = TRUE;
         $gFULLNAME = $GROUP->Fullname;
         $gDESCRIPTION = $GROUP->Description;
         $gMEMBERS = $GROUP->groupMembers->CountMembers($gGROUPNAME);
         $gSTAMP = $GROUP->Stamp;
         $gTAGS = $GROUP->Tags . " ";
       } // if
  
       $data = implode ("", file ("legacy/code/include/data/xml/group_information.xml"));
       $return = $zOLDAPPLE->ParseTags ($data);
  
       echo $return;
    	
    } // GroupInformation
    
    function FriendDeny () {
    	
      $gUSERNAME = $_POST['gUSERNAME'];
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize ($gDOMAIN);
      
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($gUSERNAME, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      $userAuth = $this->TablePrefix . "userAuthorization";
      $friendInfo = $this->TablePrefix . "friendInformation";
      
      // Delete the friend record.
      
      $friendInfo = $this->TablePrefix . "friendInformation";
      $userProfile = $this->TablePrefix . "userProfile";
      $userAuth = $this->TablePrefix . "userAuthorization";
      
      $sql_statement = "
        DELETE $friendInfo
        FROM   $friendInfo,$userAuth
        WHERE  $friendInfo.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
        AND    $friendInfo.Username = '%s'
        AND    $friendInfo.Domain = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME),
                                mysql_real_escape_string ($this->ReturnUsername),
                                mysql_real_escape_string ($gDOMAIN)
                                );
                                
      $sql_result = mysql_query($sql_statement);
      global $gRESULT;
      $gRESULT = 1;
                                
      // Load and send back the fullname.
      global $gFULLNAME;
      
      $sql_statement = "
        SELECT Fullname,Alias
        FROM   $userProfile,$userAuth
        WHERE  $userProfile.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      mysql_free_result ($sql_result);
      
      $this->XML->Load ("legacy/code/include/data/xml/friend_deny.xml");
      $return = $this->XML->Data;
      
      
      return ($return);
    } // FriendDeny
    
    function FriendCancel () {
      
      $gUSERNAME = $_POST['gUSERNAME'];
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize ($gDOMAIN);
      
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($gUSERNAME, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      $userAuth = $this->TablePrefix . "userAuthorization";
      $friendInfo = $this->TablePrefix . "friendInformation";
      
      // Delete the friend record.
      
      $friendInfo = $this->TablePrefix . "friendInformation";
      $userProfile = $this->TablePrefix . "userProfile";
      $userAuth = $this->TablePrefix . "userAuthorization";
      
      $sql_statement = "
        DELETE $friendInfo
        FROM   $friendInfo,$userAuth
        WHERE  $friendInfo.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
        AND    $friendInfo.Username = '%s'
        AND    $friendInfo.Domain = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME),
                                mysql_real_escape_string ($this->ReturnUsername),
                                mysql_real_escape_string ($gDOMAIN)
                                );
                                
      $sql_result = mysql_query($sql_statement);
      global $gRESULT;
      $gRESULT = 1;
                                
      // Load and send back the fullname.
      global $gFULLNAME;
      
      $sql_statement = "
        SELECT Fullname,Alias
        FROM   $userProfile,$userAuth
        WHERE  $userProfile.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      mysql_free_result ($sql_result);
      
      $this->XML->Load ("legacy/code/include/data/xml/friend_cancel.xml");
      $return = $this->XML->Data;
      
      
      return ($return);
    } // FriendCancel
    
    function FriendDelete () {
    	
      $gUSERNAME = $_POST['gUSERNAME'];
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize ($gDOMAIN);
      
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($gUSERNAME, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      
      $userAuth = $this->TablePrefix . "userAuthorization";
      $friendInfo = $this->TablePrefix . "friendInformation";
      
      // Delete the friend record.
      
      $friendInfo = $this->TablePrefix . "friendInformation";
      $userProfile = $this->TablePrefix . "userProfile";
      $userAuth = $this->TablePrefix . "userAuthorization";
      
      $sql_statement = "
        DELETE $friendInfo
        FROM   $friendInfo,$userAuth
        WHERE  $friendInfo.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
        AND    $friendInfo.Username = '%s'
        AND    $friendInfo.Domain = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME),
                                mysql_real_escape_string ($this->ReturnUsername),
                                mysql_real_escape_string ($gDOMAIN)
                                );
                                
      $sql_result = mysql_query($sql_statement);
      global $gRESULT;
      $gRESULT = 1;
                                
      // Load and send back the fullname.
      global $gFULLNAME;
      
      $sql_statement = "
        SELECT Fullname,Alias
        FROM   $userProfile,$userAuth
        WHERE  $userProfile.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      mysql_free_result ($sql_result);
      
      $this->XML->Load ("legacy/code/include/data/xml/friend_delete.xml");
      $return = $this->XML->Data;
      
      return ($return);
    } // FriendDelete
    
    function FriendApprove () {
    	
      $gUSERNAME = $_POST['gUSERNAME'];
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize ($gDOMAIN);
      
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($gUSERNAME, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      $userAuth = $this->TablePrefix . "userAuthorization";
      $friendInfo = $this->TablePrefix . "friendInformation";
      
      // Update the friend record.
      
      $friendInfo = $this->TablePrefix . "friendInformation";
      $userProfile = $this->TablePrefix . "userProfile";
      $userAuth = $this->TablePrefix . "userAuthorization";
      
      $sql_statement = "
        UPDATE $friendInfo,$userAuth
        SET    $friendInfo.Verification = 1
        WHERE  $friendInfo.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
        AND    $friendInfo.Username = '%s'
        AND    $friendInfo.Domain = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME),
                                mysql_real_escape_string ($this->ReturnUsername),
                                mysql_real_escape_string ($gDOMAIN)
                                );
                                
      $sql_result = mysql_query($sql_statement);
      global $gRESULT;
      
      $gRESULT = 1;
                                
      // Load and send back the fullname.
      global $gFULLNAME;
      
      $sql_statement = "
        SELECT Fullname,Alias
        FROM   $userProfile,$userAuth
        WHERE  $userProfile.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      mysql_free_result ($sql_result);
      
      $this->XML->Load ("legacy/code/include/data/xml/friend_approve.xml");
      $return = $this->XML->Data;
      
      
      return ($return);
    } // FriendApprove
    
    function FriendStatus () {
    	
      $gUSERNAME = $_POST['gUSERNAME'];
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize ($gDOMAIN);
      
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($gUSERNAME, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      $userAuth = $this->TablePrefix . "userAuthorization";
      $friendInfo = $this->TablePrefix . "friendInformation";
      
      $sql_statement = "
        SELECT $friendInfo.Verification AS Verification
        FROM $userAuth, $friendInfo 
        WHERE $friendInfo.userAuth_uID = $userAuth.uID 
        AND $userAuth.Username='%s'
        AND $friendInfo.Username='%s' 
        AND $friendInfo.Domain='%s';
      ";
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME),
                                mysql_real_escape_string ($this->ReturnUsername),
                                mysql_real_escape_string ($gDOMAIN)
                                );
                                
      $sql_result = mysql_query ($sql_statement);
      
      $result = mysql_fetch_assoc ($sql_result);
      
      global $gFRIENDSTATUS;
      if (!$gFRIENDSTATUS = $result['Verification']) $gFRIENDSTATUS = 0;
      
      $this->XML->Load ("legacy/code/include/data/xml/friend_status.xml");
      $return = $this->XML->Data;
      
      
      return ($return);
    } // FriendStatus
    
    function SiteVersion () {
    	
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize($gDOMAIN);
      
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      $this->XML->Load ("legacy/code/include/data/xml/site_version.xml");
      $return = $this->XML->Data;
      
      
      return ($return);
    } // SiteVersion
      
    function UserInformation () {
    	
      $gUSERNAME = $_POST['gUSERNAME'];
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize($gDOMAIN);
    	
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked (NULL, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      // Load and send back the fullname.
      global $gFULLNAME;
      
      $userProfile = $this->TablePrefix . "userProfile";
      $userInfo = $this->TablePrefix . "userInformation";
      $userAuth = $this->TablePrefix . "userAuthorization";
      
      $sql_statement = "
        SELECT Fullname, Alias
        FROM   $userProfile,$userAuth
        WHERE  $userProfile.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      mysql_free_result ($sql_result);
      
      // Load and send back the online status.
      $sql_statement = "
        SELECT OnlineStamp
        FROM   $userInfo,$userAuth
        WHERE  $userInfo.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      mysql_free_result ($sql_result);
      
      $currently = strtotime ("now");
      $online = strtotime ($result['OnlineStamp']);

      $difference = $currently - $online;
      
      global $gONLINE;
      $gONLINE = NULL;
      if ($difference < 180) $gONLINE = "ONLINE";
            
      $this->XML->Load ("legacy/code/include/data/xml/user_information.xml");
      $return = $this->XML->Data;
      
      
      return ($return);
    } // UserInformation
    
    function LoginCheck () {
    	
      global $gUSERNAME;
      $gUSERNAME = $_POST['gUSERNAME'];
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize($gDOMAIN);
      
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($gUSERNAME, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      $authVerification = $this->TablePrefix . "authVerification";
      $userProfile = $this->TablePrefix . "userProfile";
      $userAuth = $this->TablePrefix . "userAuthorization";
      
      // Check our local database, see if this token exists.
      $sql_statement = "
        SELECT $authVerification.*,$userProfile.Fullname,$userProfile.Alias
        FROM   $authVerification, $userProfile,$userAuth
        WHERE  $authVerification.Username = '%s'
        AND    $authVerification.Domain = '%s'
        AND    $userProfile.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
		AND    Active = '1';
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME),
                                mysql_real_escape_string ($gDOMAIN),
                                mysql_real_escape_string ($gUSERNAME));
                                
      $sql_result = mysql_query ($sql_statement);

      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);

      if ($result_count) {
        $result = mysql_fetch_assoc ($sql_result);
        
        global $gFULLNAME, $gTIME, $gDOMAIN;
        global $gADDRESS, $gHOST;
        $gFULLNAME = $result['Fullname'];
        if ($result['Alias']) $gFULLNAME = $result['Alias'];
        $gTIME = time ();
        $gDOMAIN = str_replace ("www.", NULL, $_SERVER['HTTP_HOST']);
        $gADDRESS = $result['Address'];
        $gHOST = $result['Host'];
        $tID = $result['tID'];
        
        // Set the Verification To Inactive
        $sql_statement = "
          UPDATE $authVerification
          SET Active = 0
          WHERE tID = %s
        "; 
        
        $sql_statement = sprintf ($sql_statement,
                                  mysql_real_escape_string ($tID));
                                  
        $sql_result = mysql_query ($sql_statement);
        
        mysql_free_result ($sql_result);
        
      } else {
        // No results found.  Unauthenticated. Send back an error.
        $return = $this->XML->ErrorData ("ERROR.UNVERIFIED");
        return ($return);
      } // if
      
      $this->XML->Load ("legacy/code/include/data/xml/login_check.xml");
      $return = $this->XML->Data;
      
      return ($return);
    } // LoginCheck
    
    function IconList () {
      
      $gUSERNAME = $_POST['gUSERNAME'];
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize($gDOMAIN);
      
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      // Check if site is blocked.
      if ($errorcode = $this->Blocked ($gUSERNAME, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      $userIcons = $this->TablePrefix . "userIcons";
      $userAuth = $this->TablePrefix . "userAuthorization";
      $finaldata = null;
      
      $this->XML->Load ("legacy/code/include/data/xml/icon_list/top.xml");
      $finaldata = $this->XML->Data;
      
      // Check our local database, see if this token exists.
      $sql_statement = "
        SELECT $userIcons.Filename,$userIcons.Keyword
        FROM   $userIcons,$userAuth
        WHERE  $userIcons.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME),
                                mysql_real_escape_string ($gDOMAIN),
                                mysql_real_escape_string ($gUSERNAME));
                                
      $sql_result = mysql_query ($sql_statement);

      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      global $gFILENAME, $gKEYWORD;
      
      // Loop through the results.
      while ($result = mysql_fetch_assoc($sql_result)) {
        $gFILENAME = $result['Filename'];
        $gKEYWORD = $result['Keyword'];
        $this->XML->Load ("legacy/code/include/data/xml/icon_list/middle.xml");
        $finaldata .= $this->XML->Data;
      } // while
      
      $this->XML->Load ("legacy/code/include/data/xml/icon_list/bottom.xml");
      $finaldata .= $this->XML->Data;
      
      $return = $this->XML->Data = $finaldata;
      
      return ($return);
    } // IconList
    
    function MessageRetrieve () {
    	
      $this->Initialize();
      
      $gUSERNAME = $_POST['gUSERNAME'];
      $gIDENTIFIER = $_POST['gIDENTIFIER'];
      
      $messageStore = $this->TablePrefix . "messageStore";
      $messageRecipient = $this->TablePrefix . "messageRecipient";
      $userAuth = $this->TablePrefix . "userAuthorization";
      $userProfile = $this->TablePrefix . "userProfile";
      
      // Select the message.
      $sql_statement = "
        SELECT $messageStore.tID AS tID,
               $messageStore.Subject AS Subject,  
               $messageStore.Body AS Body,
               $messageStore.Stamp AS Stamp,
               $messageRecipient.Domain AS Domain,
               $userProfile.Fullname AS Fullname,
               $userProfile.Alias AS Alias
        FROM   $messageStore,$userProfile,$messageRecipient
        WHERE  $messageRecipient.Username = '%s'
        AND    $userProfile.userAuth_uID = $messageStore.userAuth_uID
        AND    $messageRecipient.Identifier = '%s'
        AND    $messageRecipient.messageStore_tID = $messageStore.tID;
      ";
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gUSERNAME),
                                mysql_real_escape_string ($gIDENTIFIER));
                                
      $sql_result = mysql_query ($sql_statement);
      
      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      $result = mysql_fetch_assoc ($sql_result);
      global $gSUBJECT, $gBODY, $gSTAMP, $gFULLNAME;
      $tID = $result['tID'];
      $gSUBJECT = $result['Subject'];
      $gBODY = $result['Body'];
      $gSTAMP = $result['Stamp'];
      $gFULLNAME = $result['Fullname'];
      $domain = $result['Domain'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      
      if ($result_count == 0) {
        // No results found.  Unauthenticated. Send back an error.
        $return = $this->XML->ErrorData ("ERROR.NOTFOUND");
        return ($return);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($gUSERNAME, $domain)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      // Mark message as read.
      $sql_statement = "
        UPDATE $messageRecipient 
        SET    $messageRecipient.Standing = 2 
        WHERE  $messageRecipient.Identifier = '%s' 
        AND    $messageRecipient.Standing = 1 
        AND    $messageRecipient.messageStore_tID = '%s'
      ";
                       
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gIDENTIFIER),
                                $tID);
                                
      $sql_result = mysql_query ($sql_statement);
      
      $this->XML->Load ("legacy/code/include/data/xml/message_retrieve.xml");
      $return = $this->XML->Data;
      
      return ($return);
    } // MessageRetrieve
    
    function MessageNotify () {
      
      $gRECIPIENT       = $_POST['gRECIPIENT'];
      $gFULLNAME        = $_POST['gFULLNAME'];
      $gUSERNAME        = $_POST['gUSERNAME'];
      $gSUBJECT         = $_POST['gSUBJECT'];
      $gTOKEN           = $_POST['gTOKEN'];
      $gDOMAIN          = $_POST['gDOMAIN'];
      $gIDENTIFIER      = $_POST['gIDENTIFIER'];

      $this->Initialize($gDOMAIN);
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($gUSERNAME, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      global $gSUCCESS, $gFULLNAME;
      
      $userAuth = $this->TablePrefix . "userAuthorization";
      $userProfile = $this->TablePrefix . "userProfile";
      
      // First check if the user exists.
      $sql_statement = "
        SELECT $userAuth.uID as uID,
               $userProfile.Email as Email,
               $userProfile.Fullname as Fullname,
               $userProfile.Alias as Alias
        FROM   $userAuth,$userProfile
        WHERE  $userAuth.Username = '%s'
        AND    $userProfile.userAuth_uID = $userAuth.uID
      ";
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gRECIPIENT));
                                
      $sql_result = mysql_query ($sql_statement);
      
      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      if ($result_count == 0) {
        // No results found.  No such user.
        $return = $this->XML->ErrorData ("ERROR.NOTFOUND");
        return ($return);
      } // if
      
      $result = mysql_fetch_assoc ($sql_result);
      $uid = $result['uID'];
      $fullname = $result['Fullname'];
      $email = $result['Email'];
      $gFULLNAME = $fullname;
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      
      mysql_free_result ($sql_result);
      
      $messageNotify = $this->TablePrefix . "messageNotification";
      
      // Insert notification into database.
      $sql_statement = "
        INSERT INTO $messageNotify 
        (userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Stamp, Standing, Location)
        VALUES
        ('%s', '%s', '%s', '%s', '%s', NOW(), 1, 1)
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($uid),
                                mysql_real_escape_string ($gUSERNAME),
                                mysql_real_escape_string ($gDOMAIN),
                                mysql_real_escape_string ($gIDENTIFIER),
                                mysql_real_escape_string ($gSUBJECT));
      
      if (!$sql_result = mysql_query ($sql_statement)) {
        // Unable to insert notification.
        $return = $this->XML->ErrorData ("ERROR.FAILED");
        return ($return);
      } // if
      
      mysql_free_result ($sql_result);
      
      // Send email notification.
      
			$sender = $gUSERNAME . '@' . $gDOMAIN;
      $messagesurl = "http://" . $this->SiteDomain . "/profile/" . $gRECIPIENT . "/messages/";

			$from = __("Message Notify Sender", array ( "domain" => $this->SiteDomain ) );

			$subject = __("Message Notify Subject", array ( "sender" => $sender) );

			$body = __("Message Notify Body", array ( "recipient" => $gFULLNAME, "sender" => $sender, "url" => $messagesurl ) );
      
      $gSUCCESS = TRUE;
      
      $headers = 'From: ' . $from . "\r\n" .
        'Reply-To: ' . $from . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
        
      $this->_Email ( $email, $gRECIPIENT, $sender, $gSUBJECT );
      
      $this->XML->Load ("legacy/code/include/data/xml/message_notify.xml");
      $return = $this->XML->Data;
      
      return ($return);
    } // MessageNotify
    
	private function _Email ( $pAddress, $pRecipient, $pSender, $pSubject ) {
		global $zApp;
		
		$data = array ( 'account' => $pSender, 'source' => ASD_DOMAIN, 'request' => $pSender );
		$CurrentInfo = $zApp->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Info', $data );
		$SenderFullname = $CurrentInfo->fullname;
		$SenderNameParts = explode ( ' ', $CurrentInfo->fullname );
		$SenderFirstName = $SenderNameParts[0];
		
		list ( $RecipientUsername, $RecipientDomain ) = explode ( '@', $pRecipient );
		
		$SenderAccount = $pSender;
		
		$RecipientEmail = $pAddress;
		$MailSubject = __( "Legacy Someone Sent A Message", array ( "fullname" => $SenderFullname ) );
		$Byline = __( "Legacy Sent A Message" );
		$Subject = $pSubject;
		$Link = 'http://' . ASD_DOMAIN . '/profile/' . $RecipientUsername . '/messages/';
		$Body = __( "Legacy Message Description", array ( 'fullname' => $SenderFullname, 'domain' => ASD_DOMAIN, 'firstname' => $senderFirstname, 'link' => $Link ) );
		$LinkDescription = __( "Legacy Click Here" );
		
		$Message = array ( 'Type' => 'User', 'SenderFullname' => $SenderFullname, 'SenderAccount' => $SenderAccount, 'RecipientEmail' => $RecipientEmail, 'MailSubject' => $MailSubject, 'Byline' => $Byline, 'Subject' => $Subject, 'Body' => $Body, 'LinkDescription' => $LinkDescription, 'Link' => $Link );
		$zApp->GetSys ( 'Components' )->Talk ( 'Postal', 'Send', $Message );
		
		return ( true );
	} 
	
    function Blocked ($pUSERNAME, $pDOMAIN) {
    	
      // domain.com              = blocks domain.com and all subdomains.
      // *.domain.com            = same as above
      // *.com                   = blocks all .com domains
      // *.subdomain.domain.com  = blocks subdomain.domain.com and all (sub-)subdomains.
      // ###.###.###.###         = blocks specific ip address
      // ###.###.###.*           = blocks C block.
      // user@domain.com         = blocks specific user at a domain.
      
      $systemNodes = $this->TablePrefix . "systemNodes";
      
      $address = $_SERVER['REMOTE_ADDR'];
      $host = gethostbyaddr ($address);
      $split_host = explode ('.', $address);
      $cblock = $split_host[0] . '.' . $split_host[1] . '.' . $split_host[2];
      
      // First check if the user exists.
      $sql_statement = "
        SELECT $systemNodes.tID as tID,
               $systemNodes.Entry as Entry,
               $systemNodes.Trust as Trust
        FROM   $systemNodes
        WHERE  $systemNodes.Entry LIKE '#%s'
        OR     $systemNodes.Entry LIKE '%s#'
        AND    $systemNodes.EndStamp > NOW()
        OR     $systemNodes.EndStamp = '0000-00-00 00:00:00'
      ";
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($pDOMAIN),
                                mysql_real_escape_string ($cblock));
                                
      $sql_statement = str_replace ('#', '%', $sql_statement);
      
      if (!$sql_result = mysql_query ($sql_statement)) {
        // No entries were found.  Site is not blocked.
        return (FALSE);
      } // if
      
      // Loop through the entries.
      while ($result = mysql_fetch_assoc ($sql_result)) {
      
        $entry = $result['Entry'];
        $trust = $result['Trust'];
        
        // Check to see if we're looking for an ip address.
        if ($entry == $address) {
          mysql_free_result ($sql_result);
          
          // If we're trusting ip address.
          if ($trust == 10) return (FALSE);
          
          // If we're blocking address.
          return ("ERROR.BLOCKED.ADDRESS");
        } // if
        
        // Check to see if we're looking for a C-block of addresses.
        if ($entry == $cblock . '.*') {
          mysql_free_result ($sql_result);
          
          // If we're trusting ip address.
          if ($trust == 10) return (FALSE);
          
          // If we're blocking address.
          return ("ERROR.BLOCKED.ADDRESS");
        } // if
        
        // Check to see if we're looking for a domain.
        if ( ($entry == $pDOMAIN) or 
             ($entry == '*.' . $pDOMAIN) ) {
          mysql_free_result ($sql_result);
          
          // If we're trusting domain.
          if ($trust == 10) return (FALSE);
          
          // If we're blocking domain.
          return ("ERROR.BLOCKED");
        } // if
        
        // Check to see if we're looking for a subdomain.
        list ($null, $subentry) = explode ('.', $pDOMAIN, 2);
        if ($entry == '*.' . $subentry) {
          
          mysql_free_result ($sql_result);
          
          // If we're trusting subdomain.
          if ($trust == 10) return (FALSE);
          
          // If we're blocking subdomain.
          return ("ERROR.BLOCKED");
        } // if
        
        // Check to see if we're looking for a specific user at this address.
        if (strpos ($entry, '@') === TRUE) {
          list ($username, $domain) = explode ('@', $entry);
          if ($username == $pUSERNAME) {
             mysql_free_result ($sql_result);
      
             // If we're trusting user.
             if ($trust == 10) return (FALSE);
          
             // If we're blocking user.
             return ("ERROR.BLOCKED.USER");
          } // if
        } // if
        
      } // while
      
      // If we get to this point, then activity is accepted.
      mysql_free_result ($sql_result);
      
      return (FALSE);
    } // Blocked
    
    function UpdateNodeNetwork () {
    	
      $gSUMMARY = $_POST['gSUMMARY'];
      $gUSERS = $_POST['gUSERS'];
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      $this->Initialize ($gDOMAIN);
      
      $gSUMMARY = strip_tags ($gSUMMARY, '<a>');
      
      if (!$this->TokenCheckRemote ($gTOKEN, $gDOMAIN) ) {
        // Invalid token, exit.
        $return = $this->XML->ErrorData ("ERROR.TOKEN");
        return ($return);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked (NULL, $gDOMAIN)) {
        $return = $this->XML->ErrorData ($errorcode);
        return ($return);
      } // if
      
      $contentNodes = $this->TablePrefix . "contentNodes";
      
      $sql_statement = "
        SELECT tID
          FROM $contentNodes
         WHERE Domain = '%s'
      ";
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($gDOMAIN));
                                
      $sql_result = mysql_query ($sql_statement);
      $result_count = mysql_num_rows ($sql_result);
      
      mysql_free_result ($sql_result);
      
      if ($result_count) {
        // Result was found, update.
        $sql_statement = "
          UPDATE $contentNodes
           SET   Users = '%s',
                 Summary = '%s',
                 Stamp = NOW()
           WHERE Domain = '%s'
        ";
        $sql_statement = sprintf ($sql_statement,
                                  mysql_real_escape_string ($gUSERS),
                                  mysql_real_escape_string ($gSUMMARY),
                                  mysql_real_escape_string ($gDOMAIN));
                                
      } else {
        $sql_statement = "
          INSERT INTO $contentNodes
                      (Domain, Summary, Users, Stamp, Verification)
               VALUES ('%s', '%s', '%s', NOW(), 0);
        ";
        $sql_statement = sprintf ($sql_statement,
                                  mysql_real_escape_string ($gDOMAIN),
                                  mysql_real_escape_string ($gSUMMARY),
                                  mysql_real_escape_string ($gUSERS));
                                
        // No result found, add.
      } // if
                                
      if (!$sql_result = mysql_query ($sql_statement)) {
        $return = $this->XML->ErrorData ("ERROR.NOTFOUND");
        return ($return);
      } // if;
      
      global $gMESSAGE, $gSUCCESS;
      
      $gMESSAGE = "MESSAGE.UPDATED";
      $gSUCCESS = TRUE;
      $this->XML->Load ("legacy/code/include/data/xml/update_node_network.xml");
      $return = $this->XML->Data;
      
      return ($return);
    } // UpdateNodeNetwork
    
    function TrustedList () {
    } // TrustedList
    
  } // cSERVER
  
  // Client class for node communication.
  class cCLIENT {
  	
  	// Build a remote icon list. 
  	function BuildIconList ($pUSERNAME, $pDOMAIN) {
      global $zXML;
      global $gSITEDOMAIN;
      global $gAPPLESEEDVERSION;
      
      $VERIFY = new cAUTHTOKENS ();
      $token = $VERIFY->LoadToken ($pUSERNAME, $pDOMAIN);

      if (!$token) {
        $token = $VERIFY->CreateToken ($pUSERNAME, $pDOMAIN);
      } // if
      
      unset ($VERIFY);

      $zREMOTE = new cREMOTE ($pDOMAIN);
      $datalist = array ("gACTION"   => "ASD_ICON_LIST",
                         "gDOMAIN"   => $gSITEDOMAIN,
                         "gTOKEN"    => $token,
                         "gVERSION"  => $gAPPLESEEDVERSION,
                         "gUSERNAME" => $pUSERNAME);
      $zREMOTE->Post ($datalist);

      $zXML->Parse ($zREMOTE->Return);

      $iconcount = $zXML->GetNumberOfElements ("icon");
      
      for ($count = 0; $count < $iconcount; $count++) {
        $filename = $zXML->GetValue ("icon", $count);
        $keyword = $zXML->GetId ("icon", $count);
        $remotedata[] = new stdClass();
        $remotedata[count($remotedata)-1]->Filename = $filename;
        $remotedata[count($remotedata)-1]->Keyword = $keyword;
      } // for

      return ($remotedata);
  	} // BuildIconList
  	
  	function RetrieveMessage ($pUSERNAME, $pDOMAIN, $pIDENTIFIER) {
  	  global $zXML;
  		
      global $gAPPLESEEDVERSION;
      
      // Retrieve message data.
      $zREMOTE = new cREMOTE ($pDOMAIN);
      $datalist = array ("gACTION"          => "ASD_MESSAGE_RETRIEVE",
                         "gUSERNAME"        => $pUSERNAME,
                         "gVERSION"         => $gAPPLESEEDVERSION,
                         "gIDENTIFIER"      => $pIDENTIFIER);
      $zREMOTE->Post ($datalist);

      $zXML->Parse ($zREMOTE->Return);

      $errorcode = $zXML->GetValue ("title", 0);
      
      $remotedata = new stdClass();
      if ($errorcode) {
        $remotedata->Error = TRUE;
        $remotedata->ErrorTitle = $errorcode;
        return ($remotedata);
      } else {
        $remotedata->Subject = $zXML->GetValue ("subject", 0);
        $remotedata->Body = $zXML->GetValue ("body", 0);
        $remotedata->Stamp = $zXML->GetValue ("stamp", 0);
        if ( (!$remotedata->Subject) or (!$remotedata->Body) or (!$remotedata->Stamp) ) {
          $remotedata->Error = TRUE;
          $remotedata->ErrorTitle = "ERROR.INVALIDMESSAGE";
        } // if
      } // if
      	
      return ($remotedata);
  	} // RetrieveMessage
  	
  	function RemoteMessage ($pUSERNAME, $pDOMAIN, $pIDENTIFIER, $pSUBJECT, $pSENDERUSERNAME, $pSENDERFULLNAME) {
  	  global $zXML;
  	  
  	  global $gSITEDOMAIN, $gAPPLESEEDVERSION;
  		
      // Send the notification. 
      $zREMOTE = new cREMOTE ($pDOMAIN);
      $datalist = array ("gACTION"          => "ASD_MESSAGE_NOTIFY",
                         "gRECIPIENT"       => $pUSERNAME,
                         "gFULLNAME"        => $pSENDERFULLNAME,
                         "gUSERNAME"        => $pSENDERUSERNAME,
                         "gDOMAIN"          => $gSITEDOMAIN,
                         "gIDENTIFIER"      => $pIDENTIFIER,
                         "gVERSION"         => $gAPPLESEEDVERSION,
                         "gSUBJECT"         => $pSUBJECT);
      $zREMOTE->Post ($datalist);
      
      $zXML->Parse ($zREMOTE->Return);

      $version = $zXML->GetValue ("version", 0);
      $success = $zXML->GetValue ("success", 0);
      
      $remotedata = new stdClass();

      if (!$success) {
        $remotedata->Error = TRUE;
        $remotedata->ErrorTitle = ucwords ($zXML->GetValue ("title", 0));
        return ($remotedata);
      } else {
        $remotedata->Error = FALSE;
        return ($remotedata);
      } // if

  	} // RemoteMessage
  	
  	function GetUserInformation ($pUSERNAME, $pDOMAIN) {
  	  global $zXML;
  	  
  	  global $gAPPLESEEDVERSION, $gSITEDOMAIN;
  	  
      $VERIFY = new cAUTHTOKENS ();
      $token = $VERIFY->LoadToken ($pUSERNAME, $pDOMAIN);

      if (!$token) {
        $token = $VERIFY->CreateToken ($pUSERNAME, $pDOMAIN);
      } // if
      
      unset ($VERIFY);
      
      // Check Online (remote)
      $zREMOTE = new cREMOTE ($pDOMAIN);
      $datalist = array ("gACTION"   => "ASD_USER_INFORMATION",
                         "gUSERNAME" => $pUSERNAME,
                         "gVERSION"  => $gAPPLESEEDVERSION,
                         "gDOMAIN"   => $gSITEDOMAIN,
                         "gTOKEN"    => $token);
      $zREMOTE->Post ($datalist, 1);
      $zXML->Parse ($zREMOTE->Return);

      // If no user was found, return FALSE.
      $errorcode = ucwords ($zXML->GetValue ("code", 0));
      if ($errorcode) {
      	$remotedata->Error = TRUE;
      	$remotedata->ErrorTitle = $errorcode;
        return (FALSE);
      } // if
      
      $remotedata = new stdClass();

      $remotedata->Fullname = ucwords ($zXML->GetValue ("fullname", 0));

      $remotedata->Online = FALSE;
      if ($zXML->GetValue ("online", 0) == "ONLINE") $remotedata->Online = TRUE;
      
      return ($remotedata);

  	} // GetUserInformation
  	
  } // cCLIENT
  
  class cAJAX extends cSERVER {
    
    function cAJAX () {
      
      // Create XML object.
      $this->XML = new cOLDXML ();
      
      // Initialize Variables.
      $this->SiteURL = null;
      $this->SiteDomain = null;
      $this->Database = null;
      $this->DatabaseHost = null;
      $this->DatabaseUsername = null;
      $this->DatabasePassword = null;
      $this->DatabaseLink = null;
      $this->TablePrefix = null;
      $this->Token = null;
      $this->Token_uID = null;
      $this->ReturnUsername = null;
      
      // Load Site Information.
      $this->LoadSiteInfo ();
      
      // Create JSON Object
      $this->JSON = new cJSON ();
      
      // Connect To The Database.
      $this->Connect ();
      
      return (TRUE);
    } // Constructor
    
    function GetUserInformation ($pUSERNAME, $pDOMAIN) {
    	
      global $gAPPLESEEDVERSION;
    	
      $this->Initialize ($pDOMAIN, FALSE);
      
      $token = $this->LoadToken ('*', $pDOMAIN);
      
      if (!$token) {
        $token = $this->CreateToken ('*', $pDOMAIN);
      } // if
      
      $REMOTE = new cREMOTE ($pDOMAIN);
      $datalist = array ("gACTION"     => "ASD_USER_INFORMATION",
                         "gUSERNAME"   => $pUSERNAME,
                         "gVERSION"    => $gAPPLESEEDVERSION,
                         "gDOMAIN"     => $this->SiteDomain,
                         "gTOKEN"      => $token);
      $REMOTE->Post ($datalist, 1);

      $this->XML->Parse ($REMOTE->Return);

      // If no appleseed version was retrieved, an invalid url was used.
      $version = ucwords ($this->XML->GetValue ("version", 0));
      if (!$version) return (FALSE);
      
      $error = $this->XML->GetValue ("title", 0);
      if ($error) return ($error);
      
      $fullname = $this->XML->GetValue ("fullname", 0);
      $online = $this->XML->GetValue ("online", 0);
      
      $error = $this->XML->GetValue ("online", 0);
      
      $val['fullname'] = $fullname;
      $val['online'] = $online;
      
      $return = $this->JSON->encode ($val);
      
      return ($return);
    } // GetUserInformation
    
    function LoadToken ($pUSERNAME, $pDOMAIN) {
      
      $authTokens = $this->TablePrefix . 'authTokens';
      
      $sql_statement = "
        SELECT $authTokens.Token 
        FROM   $authTokens
        WHERE  $authTokens.Username = '%s'
        AND    $authTokens.Domain = '%s'
        AND    $authTokens.Stamp > DATE_ADD(now(), INTERVAL -30 MINUTE) 
        AND    $authTokens.Source = 10
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($pDOMAIN));
      $sql_result = mysql_query ($sql_statement);
      
      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      if ($result_count == 0) {
        return (NULL);
      } else {
        $result = mysql_fetch_assoc ($sql_result);
        $token = $result['Token'];
      } // if
      
      return ($token);
    } // LoadToken
    
    function CreateToken ($pUSERNAME, $pDOMAIN) {
      
      $token = $this->RandomString (32);
      
      $authTokens = $this->TablePrefix . 'authTokens';
      
      // Insert new token.
      $sql_statement = "
        INSERT INTO $authTokens
        (Username, Domain, Token, Stamp, Source)
        VALUES ('%s', '%s', '%s', NOW(), 10);
      "; 
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($pDOMAIN),
                                mysql_real_escape_string ($token));
                                
      $sql_result = mysql_query ($sql_statement);
      
      return ($token);
    } // CreateToken
    
    // Generate a random string of characters.
    function RandomString ($pSTRINGSIZE) {
  
      list($usec, $sec) = explode(' ', microtime());
      $seed = (float) $sec + ((float) $usec * 100000);
      mt_srand($seed);
  
      // Generate a random 32-byte string.
      $return_string = ""; $return_count = 0;
      for ($return_count = 0; $return_count < $pSTRINGSIZE; $return_count++) {
        $randval_num = mt_rand(48, 57);
        $randval_alpha = mt_rand(65, 90);
  
        // Randomly choose either the alpha or the numeric value.
        $eitheror = mt_rand (0, 2);
        if ($eitheror == 0) 
          $randval = $randval_num;
        else
          $randval = $randval_alpha;
        
        $charval = chr($randval);
        $return_string .= "$charval";
      }
  
      return ($return_string);
    } // RandomString
    
  } // cAJAX
  
  // Local Appleseed extension of JSON class.
  class cJSON extends Services_JSON {
    
  } // cJSON
