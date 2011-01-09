<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: images.php                              CREATED: 12-31-2004 + 
  // | LOCATION: /code/common/                      MODIFIED: 06-02-2007 +
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
  // | VERSION:      0.7.9                                               |
  // | DESCRIPTION:  This file uses mod_rewrite to push out an image     |
  // | file if requested.  Uses MySQL for user authentication.           |
  // +-------------------------------------------------------------------+

  eval( GLOBALS ); // Import all global variables  
  
  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);
  
  // Only include base and server.
  require_once ('legacy/code/include/classes/base.php'); 
  require_once ('legacy/code/include/classes/asd/0.7.3.php'); 
  
  define ("PRIVACY_ALLOW",     "0");
  define ("PRIVACY_SCREEN",    "1");
  define ("PRIVACY_RESTRICT",  "2");
  define ("PRIVACY_BLOCK",     "3");
  define ("PRIVACY_HIDE",      "4");
 
  define ("USER_EVERYONE",     "1000");
  define ("USER_LOGGEDIN",     "2000");
 
  $zIMAGE = new cIMAGE();
  $zSERVER = new cSERVER();
  
  $zSERVER->Initialize(NULL, FALSE);
  
  // Load Site Information.
  $zSERVER->LoadSiteInfo ();
  
  // Suppress warning reports.
  error_reporting (E_ERROR);

  $pic_location = $_SERVER[REQUEST_URI];
  $requested_pic = $pic_location;

  // Remove beginning '/' off location.
  if ($pic_location[0] == '/') $pic_location[0] = '';

  // Step 0: Check if file exists.
  
  $filename = $_SERVER['DOCUMENT_ROOT'] . $_SERVER[REQUEST_URI];
  if (!file_exists ($filename)) {
    chdir ("legacy/code/site/error/");
    include ("404.php");
    exit(0); 
  } // if (file_exists)
  
  // Split the URL information into a list.
  list ($null, $null, $null, $ROOTDIR, $gOWNER, $NULL, $gDIRECTORY, $gFILENAME) = explode ('/', $requested_pic);
  
  // If we're not looking within the photos directory, then exit.
  //if ($ROOTDIR != 'photos') exit;
  
  // Get the session string from cookie.
  $Identifier = $_COOKIE['gLOGINSESSION'];
  $RemoteIdentifier = $_COOKIE['gREMOTELOGINSESSION'];
  
  $allowedfilename = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
  
  //$zIMAGE->Show ($allowedfilename);
    
  $userAuth = $zSERVER->TablePrefix . "userAuthorization";
  $userSessions = $zSERVER->TablePrefix . "userSessions";
  $authSessions = $zSERVER->TablePrefix . "authSessions";
  $photoPrivacy = $zSERVER->TablePrefix . "photoPrivacy";
  $friendCircles = $zSERVER->TablePrefix . "friendCircles";
  $friendCirclesList = $zSERVER->TablePrefix . "friendCirclesList";
  $friendInfo = $zSERVER->TablePrefix . "friendInformation";
      
  // Check if user has access to this photo.
  
  if ((!$Identifier) and (!$RemoteIdentifier)) {
    // Viewing user is anonymous.
    $access = DetermineAnonymousAccess ($gOWNER, $gDIRECTORY);
  } else {
    // Viewing user is logged in.  Determine if they are remote or local.
    if ($Identifier) 
      $user = GetLocalUserInformation ($Identifier);
    elseif ($RemoteIdentifier)
      $user = GetRemoteUserInformation ($RemoteIdentifier);
      
    if ((!$user['Username']) and (!$user['Domain'])) {
      // Insufficient user information.  Treat as anonymous.
      $access = DetermineAnonymousAccess ($gOWNER, $gDIRECTORY);
    } else {
      $username = $user['Username'];
      $domain = $user['Domain'];
      
      // If the owner is viewing their own image, just display the image.
      if ( ($gOWNER == $username) and ($zSERVER->SiteDomain == $domain)) {
        $zIMAGE->Show ($allowedfilename);
        exit;
      } // if
      
      // Determine the access level.
      $access = DetermineAccess ($gOWNER, $gDIRECTORY, $username, $domain);
    } // if
    
  } // if
  
  
  // Take action according to access.
  switch ($access) {
    case PRIVACY_ALLOW:
    case PRIVACY_SCREEN:
    case PRIVACY_RESTRICT:
      // Show the image.
      $zIMAGE->Show ($allowedfilename);
    break;
    
    case PRIVACY_BLOCK:
    case PRIVACY_HIDE:
    default:
      // NOTE: Add back when user themes are implemented.
      //$theme = LoadThemeInformation ($Identifier);
      $theme = "default";
      $gCONNECT['username'] = $zApp->Config->GetConfiguration ( "un" );
      $blockedfilename = $_SERVER['DOCUMENT_ROOT'] . "/legacy/themes/$theme/images/icons/block.png";
      
      // Show the blocked image.
      $zIMAGE->Show ($blockedfilename);
    break;
  } // switch
    
  // End the program.
  exit;
  
  //-- FUNCTIONS --------------------------------------
  
  // Determine access level for an anonymous user.
  function DetermineAnonymousAccess ($pOWNER, $pDIRECTORY) {
    global $zSERVER;
    
    $userAuth = $zSERVER->TablePrefix . "userAuthorization";
    $userSessions = $zSERVER->TablePrefix . "userSessions";
    $authSessions = $zSERVER->TablePrefix . "authSessions";
    $photoSets = $zSERVER->TablePrefix . "photoSets";
    $photoPrivacy = $zSERVER->TablePrefix . "photoPrivacy";
    $friendCircles = $zSERVER->TablePrefix . "friendCircles";
    $friendCirclesList = $zSERVER->TablePrefix . "friendCirclesList";
    $friendInfo = $zSERVER->TablePrefix . "friendInformation";
      
    $sql_statement = "
      SELECT   MIN($photoPrivacy.Access) AS FinalAccess
      FROM     $photoSets, $photoPrivacy, $userAuth
      WHERE    $photoPrivacy.userAuth_uID = $userAuth.uID
      AND      $userAuth.Username = '%s'
      AND      $photoSets.userAuth_uID = $userAuth.uID
      AND      $photoPrivacy.friendCircles_sID = %s
      AND      $photoPrivacy.photoSets_tID = $photoSets.tID
      AND      $photoSets.Directory = '%s'
    ";
    $sql_statement = sprintf ($sql_statement,
                              mysql_real_escape_string ($pOWNER),
                              mysql_real_escape_string (USER_EVERYONE),
                              mysql_real_escape_string ($pDIRECTORY));
                             
    $sql_result = mysql_query ($sql_statement);
    $sql_data = mysql_fetch_assoc ($sql_result);
    $access = $sql_data['FinalAccess'];
    
    return ($access);
  } // DetermineAnonymousAccess
  
  // Determine access for a logged in user.
  function DetermineAccess ($pOWNER, $pDIRECTORY, $pUSERNAME, $pDOMAIN) {
    global $zSERVER;
    
    $userAuth = $zSERVER->TablePrefix . "userAuthorization";
    $userSessions = $zSERVER->TablePrefix . "userSessions";
    $authSessions = $zSERVER->TablePrefix . "authSessions";
    $photoPrivacy = $zSERVER->TablePrefix . "photoPrivacy";
    $photoSets = $zSERVER->TablePrefix . "photoSets";
    $photoInfo = $zSERVER->TablePrefix . "photoInformation";
    $friendCircles = $zSERVER->TablePrefix . "friendCircles";
    $friendCirclesList = $zSERVER->TablePrefix . "friendCirclesList";
    $friendInfo = $zSERVER->TablePrefix . "friendInformation";
    
    // Strip off any size indicators from file request.
    $pFILENAME = str_replace ('_og.', NULL, $pFILENAME);
    $pFILENAME = str_replace ('_sm.', NULL, $pFILENAME);
    $pFILENAME = str_replace ('_md.', NULL, $pFILENAME);
    $pFILENAME = str_replace ('_lg.', NULL, $pFILENAME);
    $pLOCKID = 100;
    $pSETID = 100;
    
    $sql_statement = "
      SELECT   MIN($photoPrivacy.Access) AS FinalAccess
      FROM     $photoSets, $photoInfo, $photoPrivacy, $userAuth,  $friendCircles, $friendCirclesList, $friendInfo 
      WHERE    $photoPrivacy.userAuth_uID = $userAuth.uID
      AND      $friendCircles.userAuth_uID = $userAuth.uID
      AND      $userAuth.Username = '%s'
      AND      $userAuth.uID = $photoSets.userAuth_uID
      AND      $photoPrivacy.friendCircles_sID = $friendCircles.sID 
      AND      $photoPrivacy.photoSets_tID = $photoSets.tID
      AND      $photoSets.Directory = '%s'
      AND      $friendCircles.tID = $friendCirclesList.friendCircles_tID
      AND      $friendInfo.Username = '%s'
      AND      $friendInfo.Domain = '%s'
      AND      $friendInfo.tID = $friendCirclesList.friendInformation_tID
    ";
    
    $sql_statement = sprintf ($sql_statement,
                              mysql_real_escape_string ($pOWNER),
                              mysql_real_escape_string ($pDIRECTORY),
                              mysql_real_escape_string ($pUSERNAME),
                              mysql_real_escape_string ($pDOMAIN));
    
    $sql_result = mysql_query ($sql_statement);
    $sql_data = mysql_fetch_assoc ($sql_result);
    
    $access = $sql_data['FinalAccess'];
    
    // If we have a value, return.
    if ($access) {
      return ($access);
    } // if
    
    // If no value was found, user is not a friend.
    
    $sql_statement = "
      SELECT   MIN($photoPrivacy.Access) AS FinalAccess
      FROM     $photoSets, $photoPrivacy, $userAuth
      WHERE    $photoPrivacy.userAuth_uID = $userAuth.uID
      AND      $userAuth.Username = '%s'
      AND      $photoSets.userAuth_uID = $userAuth.uID
      AND     ($photoPrivacy.friendCircles_sID = %s
      OR       $photoPrivacy.friendCircles_sID = %s)
      AND      $photoPrivacy.photoSets_tID = $photoSets.tID
      AND      $photoSets.Directory = '%s'
    ";
    
    $sql_statement = sprintf ($sql_statement,
                              mysql_real_escape_string ($pOWNER),
                              mysql_real_escape_string (USER_EVERYONE),
                              mysql_real_escape_string (USER_LOGGEDIN),
                              mysql_real_escape_string ($pDIRECTORY));
                             
    $sql_result = mysql_query ($sql_statement);
    $sql_data = mysql_fetch_assoc ($sql_result);
    
    $access = $sql_data['FinalAccess'];
    
    return ($access);
  } // DetermineAccess
  
  // Load local user information from the database.
  function GetLocalUserInformation ($pIDENTIFIER) {
    global $zSERVER;
    
    $userAuth = $zSERVER->TablePrefix . "userAuthorization";
    $userSessions = $zSERVER->TablePrefix . "userSessions";
    $authSessions = $zSERVER->TablePrefix . "authSessions";
    $photoPrivacy = $zSERVER->TablePrefix . "photoPrivacy";
    $friendCircles = $zSERVER->TablePrefix . "friendCircles";
    $friendCirclesList = $zSERVER->TablePrefix . "friendCirclesList";
    $friendInfo = $zSERVER->TablePrefix . "friendInformation";
      
    // Possibly use a join statement to optimize?
    
    // Check for local.
    $sql_statement = "
      SELECT $userAuth.Username
      FROM   $userSessions,$userAuth
      WHERE  $userSessions.Identifier = '%s'
      AND    $userAuth.uID = $userSessions.userAuth_uID
    ";
    
    $sql_statement = sprintf ($sql_statement,
                              mysql_real_escape_string ($pIDENTIFIER));
                              
    $sql_result = mysql_query ($sql_statement);
    $sql_data = mysql_fetch_assoc ($sql_result);
    $Username = $sql_data['Username'];
    $uid = $sql_data['uID'];
    
    $return['Username'] = $Username;
    $return['Domain'] = $zSERVER->SiteDomain;
    
    return ($return);
  } // GetLocalUserInformation
  
  // Load remote user information from the database.
  function GetRemoteUserInformation ($pIDENTIFIER) {
    global $zSERVER;
    
    $userAuth = $zSERVER->TablePrefix . "userAuthorization";
    $userSessions = $zSERVER->TablePrefix . "userSessions";
    $authSessions = $zSERVER->TablePrefix . "authSessions";
    $photoPrivacy = $zSERVER->TablePrefix . "photoPrivacy";
    $friendCircles = $zSERVER->TablePrefix . "friendCircles";
    $friendCirclesList = $zSERVER->TablePrefix . "friendCirclesList";
    $friendInfo = $zSERVER->TablePrefix . "friendInformation";
      
    // Check for remote.
    $sql_statement = "
      SELECT $authSessions.Username, $authSessions.Domain
      FROM   $authSessions
      WHERE  Identifier = '%s'
    ";
    
    $sql_statement = sprintf ($sql_statement,
                              mysql_real_escape_string ($pIDENTIFIER));
                              
    $sql_result = mysql_query ($sql_statement);
    $sql_data = mysql_fetch_assoc ($sql_result);
    $return['Username'] = $sql_data['Username'];
    $return['Domain'] = $sql_data['Domain'];
    
    return ($return);
  } // GetRemoteUserInformation
