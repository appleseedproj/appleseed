<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: server.php                              CREATED: 04-24-2007 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 04-24-2007 +
  // +-------------------------------------------------------------------+
  // | Copyright (c) 2004-2007 Appleseed Project                         |
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
  // | VERSION:      0.7.2                                               |
  // | DESCRIPTION.  Light-weight server class definitions.              |
  // +-------------------------------------------------------------------+

  require_once ("code/include/classes/BASE/remote.php");
  require_once ("code/include/classes/BASE/xml.php");
  require_once ('code/include/external/JSON/JSON.php');
  
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

    function cSERVER ($pHOST = NULL) {
      
      // Create XML object.
      $this->XML = new cXML ();
      
      // Create REMOTE object.
      $this->REMOTE = new cREMOTE ($pHOST);
      
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
    
    function LoadSiteInfo () {
      $settings = file ("data/site.adat"); 
      
      // Initialize Variables.
      $setting_un = null;
      $setting_pw = null;
      $setting_db = null;
      $setting_pre = null;
      $setting_host = null;
      $setting_ver = null;
      
      foreach ($settings as $setting) {
        
        // Split the line into two parts, type and ethod.
        list ($settingtype, $settingmethod) = split (":", $setting,2);
  
        // Create a php variable using the resulting data.
        $settingidentifier = 'setting_' . $settingtype;
        $$settingidentifier = rtrim (ltrim ($settingmethod));
  
      } // foreach 
      
      global $gAPPLESEEDVERSION;
      $gAPPLESEEDVERSION = $setting_ver;
      
      $this->Database = $setting_db;
      $this->DatabaseUsername = $setting_un;
      $this->DatabasePassword = $setting_pw;
      $this->DatabaseHost = $setting_host;
      $this->TablePrefix = $setting_pre;
      $this->SiteURL = $setting_url;
      $this->SiteDomain = str_replace ("http://", "", $setting_url);
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
    
    function TokenCheckLocal ($pTOKEN, $pDOMAIN) {
      
      $authTokens = $this->TablePrefix . "authTokens";
      $userAuth = $this->TablePrefix . "userAuthorization";
      
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
                                mysql_real_escape_string ($pTOKEN),
                                mysql_real_escape_string ($pDOMAIN));
                                
      $sql_result = mysql_query ($sql_statement);
      
      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      if ($result_count == 0) {
        // No results found.  Unauthenticated. Send back an error.
        $this->XML->ErrorData ("ERROR.NOTFOUND");
        return (FALSE);
      } else {
        // Load and send back the username.
        global $gFULLNAME;
        global $gUSERNAME;
        $result = mysql_fetch_assoc ($sql_result);
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
        
        $this->XML->Load ("code/include/data/xml/token_check.xml");
        mysql_free_result ($sql_result);
      } // if
      
      return (TRUE);
    } // TokenCheckLocal
    
    function TokenCheckRemote ($pTOKEN, $pDOMAIN) {
     
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
    
    function FriendRequest ($pTOKEN, $pUSERNAME, $pDOMAIN) {
      
      if (!$this->TokenCheckRemote ($pTOKEN, $pDOMAIN) ) {
        // Invalid token, exit.
        $this->XML->ErrorData ("ERROR.TOKEN");
        return (FALSE);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($pUSERNAME, $pDOMAIN)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
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
                                mysql_real_escape_string ($pUSERNAME)
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
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($pDOMAIN)
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
                                mysql_real_escape_string ($pDOMAIN)
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
                                mysql_real_escape_string ($pUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      mysql_free_result ($sql_result);
      
      $this->XML->Load ("code/include/data/xml/friend_request.xml");
      
      return (TRUE);
    } // FriendRequest
    
    function FriendDeny ($pTOKEN, $pUSERNAME, $pDOMAIN) {
      
      if (!$this->TokenCheckRemote ($pTOKEN, $pDOMAIN) ) {
        // Invalid token, exit.
        $this->XML->ErrorData ("ERROR.TOKEN");
        return (FALSE);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($pUSERNAME, $pDOMAIN)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
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
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($this->ReturnUsername),
                                mysql_real_escape_string ($pDOMAIN)
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
                                mysql_real_escape_string ($pUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      mysql_free_result ($sql_result);
      
      $this->XML->Load ("code/include/data/xml/friend_deny.xml");
      
      return (TRUE);
    } // FriendDeny
    
    function FriendCancel ($pTOKEN, $pUSERNAME, $pDOMAIN) {
      
      if (!$this->TokenCheckRemote ($pTOKEN, $pDOMAIN) ) {
        // Invalid token, exit.
        $this->XML->ErrorData ("ERROR.TOKEN");
        return (FALSE);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($pUSERNAME, $pDOMAIN)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
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
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($this->ReturnUsername),
                                mysql_real_escape_string ($pDOMAIN)
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
                                mysql_real_escape_string ($pUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      mysql_free_result ($sql_result);
      
      $this->XML->Load ("code/include/data/xml/friend_cancel.xml");
      
      return (TRUE);
    } // FriendCancel
    
    function FriendDelete ($pTOKEN, $pUSERNAME, $pDOMAIN) {
      
      if (!$this->TokenCheckRemote ($pTOKEN, $pDOMAIN) ) {
        // Invalid token, exit.
        $this->XML->ErrorData ("ERROR.TOKEN");
        return (FALSE);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($pUSERNAME, $pDOMAIN)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
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
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($this->ReturnUsername),
                                mysql_real_escape_string ($pDOMAIN)
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
                                mysql_real_escape_string ($pUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      mysql_free_result ($sql_result);
      
      $this->XML->Load ("code/include/data/xml/friend_delete.xml");
      
      return (TRUE);
    } // FriendDelete
    
    function FriendApprove ($pTOKEN, $pUSERNAME, $pDOMAIN) {
      
      if (!$this->TokenCheckRemote ($pTOKEN, $pDOMAIN) ) {
        // Invalid token, exit.
        $this->XML->ErrorData ("ERROR.TOKEN");
        return (FALSE);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($pUSERNAME, $pDOMAIN)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
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
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($this->ReturnUsername),
                                mysql_real_escape_string ($pDOMAIN)
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
                                mysql_real_escape_string ($pUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      $gFULLNAME = $result['Fullname'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      mysql_free_result ($sql_result);
      
      $this->XML->Load ("code/include/data/xml/friend_approve.xml");
      
      return (TRUE);
    } // FriendApprove
    
    function FriendStatus ($pTOKEN, $pUSERNAME, $pDOMAIN) {
      
      if (!$this->TokenCheckRemote ($pTOKEN, $pDOMAIN) ) {
        // Invalid token, exit.
        $this->XML->ErrorData ("ERROR.TOKEN");
        return (FALSE);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($pUSERNAME, $pDOMAIN)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
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
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($this->ReturnUsername),
                                mysql_real_escape_string ($pDOMAIN)
                                );
                                
      $sql_result = mysql_query ($sql_statement);
      
      $result = mysql_fetch_assoc ($sql_result);
      
      global $gFRIENDSTATUS;
      if (!$gFRIENDSTATUS = $result['Verification']) $gFRIENDSTATUS = 0;
      
      $this->XML->Load ("code/include/data/xml/friend_status.xml");
      
      return (TRUE);
    } // FriendStatus
    
    function SiteVersion ($pTOKEN, $pDOMAIN) {
      
      if (!$this->TokenCheckRemote ($pTOKEN, $pDOMAIN) ) {
        // Invalid token, exit.
        $this->XML->ErrorData ("ERROR.TOKEN");
        return (FALSE);
      } // if
      
      $this->XML->Load ("code/include/data/xml/site_version.xml");
      
      return (TRUE);
    } // SiteVersion
      
    function UserInformation ($pTOKEN, $pUSERNAME, $pDOMAIN) {
      
      if (!$this->TokenCheckRemote ($pTOKEN, $pDOMAIN) ) {
        // Invalid token, exit.
        $this->XML->ErrorData ("ERROR.TOKEN");
        return (FALSE);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked (NULL, $pDOMAIN)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
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
                                mysql_real_escape_string ($pUSERNAME));
                                
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
                                mysql_real_escape_string ($pUSERNAME));
                                
      $sql_result = mysql_query($sql_statement);
      $result = mysql_fetch_assoc ($sql_result);
      mysql_free_result ($sql_result);
      
      $currently = strtotime ("now");
      $online = strtotime ($result['OnlineStamp']);

      $difference = $currently - $online;
      
      global $gONLINE;
      $gONLINE = NULL;
      if ($difference < 180) $gONLINE = "ONLINE";
            
      $this->XML->Load ("code/include/data/xml/user_information.xml");
      
      return (TRUE);
    } // UserInformation
    
    function LoginCheck ($pTOKEN, $pUSERNAME, $pDOMAIN) {
      
      if (!$this->TokenCheckRemote ($pTOKEN, $pDOMAIN) ) {
        // Invalid token, exit.
        $this->XML->ErrorData ("ERROR.TOKEN");
        return (FALSE);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($pUSERNAME, $pDOMAIN)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
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
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($pDOMAIN),
                                mysql_real_escape_string ($pUSERNAME));
                                
      $sql_result = mysql_query ($sql_statement);

      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);

      if ($result_count) {
        $result = mysql_fetch_assoc ($sql_result);
        
        global $gUSERNAME, $gFULLNAME, $gTIME, $gDOMAIN;
        global $gADDRESS, $gHOST;
        $gUSERNAME = $pUSERNAME;
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
        $this->XML->ErrorData ("ERROR.UNVERIFIED");
        return (FALSE);
      } // if
      
      $this->XML->Load ("code/include/data/xml/login_check.xml");
      
      return (TRUE);
    } // LoginCheck
    
    function IconList ($pTOKEN, $pUSERNAME, $pDOMAIN) {
      
      if (!$this->TokenCheckRemote ($pTOKEN, $pDOMAIN) ) {
        // Invalid token, exit.
        $this->XML->ErrorData ("ERROR.TOKEN");
        return (FALSE);
      } // if
      
      // Check if site is blocked.
      if ($errorcode = $this->Blocked ($pUSERNAME, $pDOMAIN)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
      } // if
      
      $userIcons = $this->TablePrefix . "userIcons";
      $userAuth = $this->TablePrefix . "userAuthorization";
      $finaldata = null;
      
      $this->XML->Load ("code/include/data/xml/icon_list/top.xml");
      $finaldata = $this->XML->Data;
      
      // Check our local database, see if this token exists.
      $sql_statement = "
        SELECT $userIcons.Filename,$userIcons.Keyword
        FROM   $userIcons,$userAuth
        WHERE  $userIcons.userAuth_uID = $userAuth.uID
        AND    $userAuth.Username = '%s'
      ";
      
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($pDOMAIN),
                                mysql_real_escape_string ($pUSERNAME));
                                
      $sql_result = mysql_query ($sql_statement);

      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      global $gFILENAME, $gKEYWORD;
      
      // Loop through the results.
      while ($result = mysql_fetch_assoc($sql_result)) {
        $gFILENAME = $result['Filename'];
        $gKEYWORD = $result['Keyword'];
        $this->XML->Load ("code/include/data/xml/icon_list/middle.xml");
        $finaldata .= $this->XML->Data;
      } // while
      
      $this->XML->Load ("code/include/data/xml/icon_list/bottom.xml");
      $finaldata .= $this->XML->Data;
      
      $this->XML->Data = $finaldata;
      
      return (TRUE);
    } // IconList
    
    function MessageRetrieve ($pUSERNAME, $pIDENTIFIER) {
      
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
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($pIDENTIFIER));
                                
      $sql_result = mysql_query ($sql_statement);
      
      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      if ($result_count == 0) {
        // No results found.  Unauthenticated. Send back an error.
        $this->XML->ErrorData ("ERROR.NOTFOUND");
        return (FALSE);
      } // if
      
      $result = mysql_fetch_assoc ($sql_result);
      global $gSUBJECT, $gBODY, $gSTAMP, $gFULLNAME;
      $tID = $result['tID'];
      $gSUBJECT = $result['Subject'];
      $gBODY = $result['Body'];
      $gSTAMP = $result['Stamp'];
      $gFULLNAME = $result['Fullname'];
      $domain = $result['Domain'];
      if ($result['Alias']) $gFULLNAME = $result['Alias'];
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($pUSERNAME, $domain)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
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
                                mysql_real_escape_string ($pIDENTIFIER),
                                $tID);
                                
      $sql_result = mysql_query ($sql_statement);
      
      $this->XML->Load ("code/include/data/xml/message_retrieve.xml");
      
      return (TRUE);
    } // MessageRetrieve
    
    function MessageNotify ($pRECIPIENT, $pFULLNAME, $pUSERNAME, $pDOMAIN, $pIDENTIFIER, $pSUBJECT) {
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked ($pUSERNAME, $pDOMAIN)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
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
                                mysql_real_escape_string ($pRECIPIENT));
                                
      $sql_result = mysql_query ($sql_statement);
      
      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      if ($result_count == 0) {
        // No results found.  No such user.
        $this->XML->ErrorData ("ERROR.NOTFOUND");
        return (FALSE);
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
                                mysql_real_escape_string ($pUSERNAME),
                                mysql_real_escape_string ($pDOMAIN),
                                mysql_real_escape_string ($pIDENTIFIER),
                                mysql_real_escape_string ($pSUBJECT));
      
      if (!$sql_result = mysql_query ($sql_statement)) {
        // Unable to insert notification.
        $this->XML->ErrorData ("ERROR.FAILED");
        return (FALSE);
      } // if
      
      mysql_free_result ($sql_result);
      
      // Send email notification.
      
      // Load the notification subject.
      $systemStrings = $this->TablePrefix . "systemStrings";
      
      $sql_statement = "
        SELECT $systemStrings.Output as Output
        FROM   $systemStrings
        WHERE  $systemStrings.Title = 'MAIL.FROM'
        AND    $systemStrings.Context = 'USER.MESSAGES'
      ";
      
      $sql_result = mysql_query ($sql_statement);
      
      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      $result = mysql_fetch_assoc ($sql_result);
      $subject = $result['Output'];
      
      mysql_free_result ($sql_result);
      
      // Load the notification message body.
      $sql_statement = "
        SELECT $systemStrings.Output as Output
        FROM   $systemStrings
        WHERE  $systemStrings.Title = 'MAIL.BODY'
        AND    $systemStrings.Context = 'USER.MESSAGES'
      ";
      
      $sql_result = mysql_query ($sql_statement);
      
      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      $result = mysql_fetch_assoc ($sql_result);
      $message = $result['Output'];
      
      mysql_free_result ($sql_result);
      
      // Load the notification sender.
      $sql_statement = "
        SELECT $systemStrings.Output as Output
        FROM   $systemStrings
        WHERE  $systemStrings.Title = 'MAIL.FROM'
        AND    $systemStrings.Context = 'USER.MESSAGES'
      ";
      
      $sql_result = mysql_query ($sql_statement);
      
      // Check if we got a result row.
      $result_count = mysql_num_rows ($sql_result);
      
      $result = mysql_fetch_assoc ($sql_result);
      $from = $result['Output'];
      
      mysql_free_result ($sql_result);
      
      $gSUCCESS = TRUE;
      
      $messagesurl = "http://" . $this->SiteDomain . "/profile/" . $pRECIPIENT . "/messages/";
      $from = str_replace ('%SITEDOMAIN%', $this->SiteDomain, $from);
      $subject = str_replace ('%SITEDOMAIN%', $this->SiteDomain, $subject);
      $message = str_replace ('%SENDERNAME%', $pFULLNAME, $message);
      $message = str_replace ('%RECIPIENTFULLNAME%', $gFULLNAME, $message);
      $message = str_replace ('%MESSAGESURL%', $messagesurl, $message);
      
      $headers = 'From: ' . $from . "\r\n" .
        'Reply-To: ' . $from . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
        
      mail ($email, $subject, $message, $headers);
      
      $this->XML->Load ("code/include/data/xml/message_notify.xml");
      
      return (TRUE);
    } // MessageNotify
    
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
      $split_host = split ('\.', $address);
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
        list ($null, $subentry) = split ('\.', $pDOMAIN, 2);
        if ($entry == '*.' . $subentry) {
          
          mysql_free_result ($sql_result);
          
          // If we're trusting subdomain.
          if ($trust == 10) return (FALSE);
          
          // If we're blocking subdomain.
          return ("ERROR.BLOCKED");
        } // if
        
        // Check to see if we're looking for a specific user at this address.
        if (strpos ($entry, '@') === TRUE) {
          list ($username, $domain) = split ('@', $entry);
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
    
    function UpdateNodeNetwork ($pTOKEN, $pDOMAIN, $pSUMMARY, $pUSERS) {
      
      $pSUMMARY = strip_tags ($pSUMMARY, '<a>');
      
      if (!$this->TokenCheckRemote ($pTOKEN, $pDOMAIN) ) {
        // Invalid token, exit.
        $this->XML->ErrorData ("ERROR.TOKEN");
        return (FALSE);
      } // if
      
      // Check if site or user is blocked.
      if ($errorcode = $this->Blocked (NULL, $pDOMAIN)) {
        $this->XML->ErrorData ($errorcode);
        return (FALSE);
      } // if
      
      $contentNodes = $this->TablePrefix . "contentNodes";
      
      $sql_statement = "
        SELECT tID
          FROM $contentNodes
         WHERE Domain = '%s'
      ";
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($pDOMAIN));
                                
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
                                  mysql_real_escape_string ($pUSERS),
                                  mysql_real_escape_string ($pSUMMARY),
                                  mysql_real_escape_string ($pDOMAIN));
                                
      } else {
        $sql_statement = "
          INSERT INTO $contentNodes
                      (Domain, Summary, Users, Stamp)
               VALUES ('%s', '%s', '%s', NOW());
        ";
        $sql_statement = sprintf ($sql_statement,
                                  mysql_real_escape_string ($pDOMAIN),
                                  mysql_real_escape_string ($pSUMMARY),
                                  mysql_real_escape_string ($pUSERS));
                                
        // No result found, add.
      } // if
                                
      if (!$sql_result = mysql_query ($sql_statement)) {
        $this->XML->ErrorData ("ERROR.NOTFOUND");
        return (FALSE);
      } // if;
      
      global $gMESSAGE, $gSUCCESS;
      
      $gMESSAGE = "MESSAGE.UPDATED";
      $gSUCCESS = TRUE;
      $this->XML->Load ("code/include/data/xml/update_node_network.xml");
      
      return (TRUE);
    } // UpdateNodeNetwork
    
  } // cSERVER
  
  class cAJAX extends cSERVER {
    
    function cAJAX () {
      
      // Create XML object.
      $this->XML = new cXML ();
      
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
      
      $token = $this->LoadToken ('*', $pDOMAIN);
      
      if (!$token) {
        $token = $this->CreateToken ('*', $pDOMAIN);
      } // if
      
      $REMOTE = new cREMOTE ($pDOMAIN);
      $datalist = array ("gACTION"     => "ASD_USER_INFORMATION",
                         "gUSERNAME"   => $pUSERNAME,
                         "gDOMAIN"     => $this->SiteDomain,
                         "gTOKEN"      => $token);
      $REMOTE->Post ($datalist, 1);

      $this->XML->Parse ($REMOTE->Return);

      // If no appleseed version was retrieved, an invalid url was used.
      $version = ucwords ($this->XML->GetValue ("version", 0));
      if (!$version) return (FALSE);
      
      $result = $this->XML->GetValue ("result", 0);
      $fullname = $this->XML->GetValue ("fullname", 0);
      $online = $this->XML->GetValue ("online", 0);
      
      $val['result'] = $result;
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
