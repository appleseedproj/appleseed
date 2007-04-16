<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: asd.php                                 CREATED: 12-31-2004 + 
  // | LOCATION: /code/site/                        MODIFIED: 04-11-2007 +
  // +-------------------------------------------------------------------+
  // | Copyright (c) 2004-2006 Appleseed Project                         |
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
  // | VERSION:      0.3.0                                               |
  // | DESCRIPTION:  ASD Network traffic hub.                            |
  // +-------------------------------------------------------------------+

  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include BASE API classes.
  require_once ('code/include/classes/BASE/application.php'); 
  require_once ('code/include/classes/BASE/debug.php'); 
  require_once ('code/include/classes/base.php'); 
  require_once ('code/include/classes/system.php'); 
  require_once ('code/include/classes/BASE/remote.php'); 

  // Include Appleseed classes.
  require_once ('code/include/classes/appleseed.php'); 
  require_once ('code/include/classes/privacy.php'); 
  require_once ('code/include/classes/friends.php'); 
  require_once ('code/include/classes/groups.php'); 
  require_once ('code/include/classes/messages.php'); 
  require_once ('code/include/classes/users.php'); 
  require_once ('code/include/classes/auth.php'); 

  // Create the Application class.
  $zAPPLE = new cAPPLESEED ();
  
  // Set Global Variables (Put this at the top of wrapper scripts)
  $zAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zAPPLE->Initialize("site", TRUE);

  switch ($gACTION) {
    case 'GET_USER_INFORMATION':
      $USER = new cUSER();

      $USER->Select ("Username", $gUSERNAME);
      $USER->FetchArray();

      // If no users found, push out an error message.
      if ($USER->CountResult () == 0) {
        $code = 1000;
        $message = "User '$gUSERNAME' Not Found";
        $return = $zXML->ErrorData ($code, $message);
      } else { 
        global $gRETURNFULLNAME, $gRETURNONLINE;
        $gRETURNFULLNAME = $USER->userProfile->GetAlias ();
      
        if ($USER->userInformation->CheckOnline ())
          $gRETURNONLINE = "ONLINE";
        else
          $gRETURNONLINE = "OFFLINE";

        $data = implode ("", file ("code/include/data/xml/get_user_information.xml"));
        $return = $zAPPLE->ParseTags ($data);

      } // if
      echo $return;
      unset ($USER);
    break;
    case 'CHECK_FRIEND_STATUS':

      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zXML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      // Authenticate token.
      $AUTH = new cAUTHSESSIONS ();
      $AUTH->Select ("Token", $gTOKEN);

      if ($AUTH->CountResult () == 0) {
        $code = 1000;
        $message = "ERROR.INVALIDTOKEN";
        $return = $zXML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      $AUTH->FetchArray ();

      unset ($AUTH);

      $USER = new cUSER();

      $USER->Select ("Username", $gLOCALUSERNAME);
      $USER->FetchArray();

      // If no users found, push out an error message.
      if ($USER->CountResult () == 0) {
        $code = 1000;
        $message = "ERROR.NOTFOUND";
        $return = $zXML->ErrorData ($code, $message);
      } else { 
        $friendcriteria = array ("userAuth_uID" => $USER->uID,
                                 "Username" => $remoteusername,
                                 "Domain"   => $remooteusername);
        $FRIEND = new cFRIENDINFORMATION();
        $FRIEND->SelectByMultiple ($friendcriteria);
        $FRIEND->FetchArray();

        global $gFRIENDSTATUS;
        $gFRIENDSTATUS = $FRIEND->Verification; 
        if (!$gFRIENDSTATUS) $gFRIENDSTATUS = 0;

        $data = implode ("", file ("code/include/data/xml/check_friend_status.xml"));
        $return = $zAPPLE->ParseTags ($data);

      } // if
      echo $return;
      unset ($USER);
    break;
    case 'ADD_FRIEND_REQUEST':

      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zXML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      // Authenticate token.
      $AUTH = new cAUTHSESSIONS ();
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
      list ($remotefullname, $NULL) = $zAPPLE->GetUserInformation ($remoteusername, $remotedomain);

      unset ($AUTH);

      $USER = new cUSER ();
      $zFRIENDS = new cFRIENDINFORMATION ();

      $USER->Select ("Username", $gLOCALUSERNAME);
      $USER->FetchArray ();

      global $gLOCALFULLNAME;
      $gLOCALFULLNAME = $USER->userProfile->GetAlias ();
      
      // Find the highest Sort ID.
      $statement = "SELECT MAX(sID) FROM friendInformation WHERE userAuth_uID = " . $USER->uID;
      $query = mysql_query($statement);
      $result = mysql_fetch_row($query);
      $sortid = $result[0] + 1;

      // Update the friend's record.
      $zFRIENDS->userAuth_uID = $USER->uID;
      $zFRIENDS->sID = $sortid;
      $zFRIENDS->Username = $remoteusername;
      $zFRIENDS->Domain = $remotedomain;
      $zFRIENDS->Verification = FRIEND_REQUESTS;
      $zFRIENDS->Stamp = SQL_NOW;

      $zFRIENDS->Add ();

      if ($zFRIENDS->Error == 0) {
        global $gFRIENDRESULT;
        $gFRIENDRESULT = SUCCESS;
        $data = implode ("", file ("code/include/data/xml/add_friend_request.xml"));
        $return = $zAPPLE->ParseTags ($data);
        $zFRIENDS->NotifyRequest ($USER->userProfile->Email, $USER->userProfile->GetAlias (), $remotefullname, $gLOCALUSERNAME); 
      } else {
        $code = 2000;
        $message = $zFRIENDS->Message;
        $return = $zXML->ErrorData ($code, $message);
      } // if
      echo $return;
      unset ($USER);
    break;
    case 'APPROVE_FRIEND_REQUEST':

      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zXML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      // Authenticate token.
      $AUTH = new cAUTHSESSIONS ();
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
      list ($remotefullname, $NULL) = $zAPPLE->GetUserInformation ($remoteusername, $remotedomain);

      unset ($AUTH);

      $USER = new cUSER ();
      $zFRIENDS = new cFRIENDINFORMATION ();

      $USER->Select ("Username", $gLOCALUSERNAME);
      $USER->FetchArray ();

      global $gLOCALFULLNAME;
      $gLOCALFULLNAME = $USER->userProfile->GetAlias ();
      
      // Find the highest Sort ID.
      $statement = "SELECT MAX(sID) FROM friendInformation WHERE userAuth_uID = " . $USER->uID;
      $query = mysql_query($statement);
      $result = mysql_fetch_row($query);
      $sortid = $result[0] + 1;

      // Update the friend's record.
      $friendcriteria = array ("userAuth_uID" => $USER->uID,
                               "Username" => $remoteusername,
                               "Domain"   => $remotedomain);
      $zFRIENDS->SelectByMultiple ($friendcriteria);
      $zFRIENDS->FetchArray();
      $resultcount = $zFRIENDS->CountResult();
      $zFRIENDS->Verification = FRIEND_VERIFIED;

      $zFRIENDS->Update ();

      if (($zFRIENDS->Error == 0) and ($resultcount > 0)) {
        global $gFRIENDRESULT;
        $gFRIENDRESULT = SUCCESS;
        $data = implode ("", file ("code/include/data/xml/approve_friend_request.xml"));
        $return = $zAPPLE->ParseTags ($data);
        $zFRIENDS->NotifyApproval ($USER->userProfile->Email, $remotefullname, $USER->userProfile->GetAlias (), $gLOCALUSERNAME); 
      } else {
        $code = 2000;
        $message = $zFRIENDS->Message;
        $return = $zXML->ErrorData ($code, $message);
      } // if
      echo $return;
      unset ($USER);
    break;
    case 'CHECK_LOGIN':
      $zVERIFY = new cAUTHVERIFICATION();

      $criteria = array ("Username" => $gUSERNAME,
                         "Domain"   => $gDOMAIN,
                         "Active"   => TRUE);
      $zVERIFY->SelectByMultiple ($criteria);
      $zVERIFY->FetchArray ();

      if ($zVERIFY->CountResult() > 0) {

        $USER = new cUSER ();
                      
        $USER->Select ("Username", $zVERIFY->Username);
        $USER->FetchArray();

        global $gCHECKUSERNAME, $gCHECKFULLNAME, $gCHECKTIME, $gSELFDOMAIN;
        global $gVERIFYADDRESS, $gVERIFYHOST;
        $gCHECKUSERNAME = $USER->Username;
        $gCHECKFULLNAME = $USER->userProfile->GetAlias ();
        $gCHECKTIME = time ();
        $gSELFDOMAIN = str_replace ("www.", NULL, $_SERVER['HTTP_HOST']);
        $gVERIFYADDRESS = $zVERIFY->Address;
        $gVERIFYHOST = $zVERIFY->Host;
        $gVERIFYTOKEN = $zVERIFY->Token;
        $data = implode ("", file ("code/include/data/xml/check_login.xml"));
        $return = $zAPPLE->ParseTags ($data);
        unset ($USER);
      } else {
        $code = 2000;
        $message = $zFRIENDS->Message;
        $return = $zXML->ErrorData ($code, $message);
      } // if

      $zVERIFY->Active = FALSE;
      $zVERIFY->Update();
      unset ($zVERIFY);

      echo $return;

    break;
    case 'GET_ICON_LIST':
     $USER = new cUSER ();
                      
     $USER->Select ("Username", $gUSERNAME);
     $USER->FetchArray();

     $USER->userIcons->Select ("userAuth_uID", $USER->uID);
     
     $return = "";

     $data = implode ("", file ("code/include/data/xml/get_icon_list/top.xml"));
     $return = $zAPPLE->ParseTags ($data);

     global $gICONKEYWORD, $gICONFILENAME;
     while ($USER->userIcons->FetchArray()) {
       $gICONKEYWORD = $USER->userIcons->Keyword;
       $gICONFILENAME = $USER->userIcons->Filename;
       $data = implode ("", file ("code/include/data/xml/get_icon_list/middle.xml"));
       $return .= $zAPPLE->ParseTags ($data);
     } // while

     $data = implode ("", file ("code/include/data/xml/get_icon_list/bottom.xml"));
     $return .= $zAPPLE->ParseTags ($data);

     echo $return;

     unset ($USER);
    break;
    case 'MESSAGE_RETRIEVE':
      // NOTE: Replace this with proper class data.
      $zMESSAGE = new cMESSAGE ();
      $messagecriteria = array ("Sender_Username" => $gSENDER_USERNAME,
                                "Identifier"      => $gIDENTIFIER);
      $zMESSAGE->messageStore->SelectByMultiple ($messagecriteria);
      $zMESSAGE->messageStore->FetchArray ();
      if ($zMESSAGE->messageStore->CountResult () == 0) {
        $code = 3000;
        $message = $zFRIENDS->Message;
        $return = $zXML->ErrorData ($code, $message);
      } else {
        global $gMESSAGEBODY, $gMESSAGESUBJECT, $gMESSAGESTAMP;
        $gMESSAGEBODY = $zMESSAGE->messageStore->Body;
        $gMESSAGESUBJECT = $zMESSAGE->messageStore->Subject;
        $gMESSAGESTAMP = $zMESSAGE->messageStore->Stamp;
        $USER = new cUSER ();
        $USER->Select ("Username", $zMESSAGE->messageStore->Sender_Username);
        $USER->FetchArray ();
        $gMESSAGEFULLNAME = $USER->userProfile->Fullname;
        $data = implode ("", file ("code/include/data/xml/message_request.xml"));
        $return = $zAPPLE->ParseTags ($data);

        // Mark message as read.
        $query = "UPDATE " . $zMESSAGE->messageStore->TableName . " SET Standing = " . MESSAGE_READ . " WHERE tID = " . $zMESSAGE->messageStore->tID . " AND Standing = " . MESSAGE_UNREAD;
        $zMESSAGE->messageStore->Query ($query);

      } // if

      echo $return;

      unset ($zMESSAGE);
    break;
    case 'MESSAGE_NOTIFY':

      $zMESSAGE = new cMESSAGE ();

      $USER = new cUSER ();
      $USER->Select ("Username", $gRECIPIENTNAME);
      $USER->FetchArray();

      if ($USER->CountResult() == 0) {
        $code = 1000;
        $message = "User '$gUSERNAME' Not Found";
        $return = $zXML->ErrorData ($code, $message);
        echo $return;
        break;
      } // if

      // NOTE:  Check if referring domain matches gSENDER_DOMAIN.

      $zMESSAGE->messageNotification->userAuth_uID = $USER->uID;
      $zMESSAGE->messageNotification->Sender_Username = $gSENDER_USERNAME;
      $zMESSAGE->messageNotification->Sender_Domain = $gSENDER_DOMAIN;
      $zMESSAGE->messageNotification->Identifier = $gIDENTIFIER;
      $zMESSAGE->messageNotification->Subject = $gSUBJECT;
      $zMESSAGE->messageNotification->Stamp = SQL_NOW;
      $zMESSAGE->messageNotification->Standing = MESSAGE_UNREAD;
      $zMESSAGE->messageNotification->Location = FOLDER_INBOX;

      $zMESSAGE->messageNotification->Add ();

      $zAPPLE->Context = "USER.MESSAGES";
      $zMESSAGE->NotifyMessage ($USER->userProfile->Email, $USER->Username, $USER->userProfile->Fullname, $gSENDER_FULLNAME) ;

      if ($zMESSAGE->Error == 0) {
        global $gRECIPIENTFULLNAME, $gSUCCESS;
        $gSUCCESS = TRUE;
        $gRECIPIENTFULLNAME = $USER->userProfile->Fullname;
        $data = implode ("", file ("code/include/data/xml/message_notify.xml"));
        $return = $zAPPLE->ParseTags ($data);
      } else {
        $code = 1000;
        $message = "Could not update";
        $return = $zXML->ErrorData ($code, $message);
      } // if

      echo $return;

      unset ($zMESSAGE);
    break;
    case 'JOIN_GROUP':
      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zXML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      // Authenticate token.
      $AUTH = new cAUTHSESSIONS ();
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
      list ($remotefullname, $NULL) = $zAPPLE->GetUserInformation ($remoteusername, $remotedomain);

      unset ($AUTH);

     $zGROUPS = new cGROUPINFORMATION ();
     $zGROUPS->Select ("Name", $gGROUPNAME);

     if ($zGROUPS->CountResult() == 0) {
       $gSUCCESS = FALSE;
       $gMESSAGE = "ERROR.NOTFOUND";
       $data = implode ("", file ("code/include/data/xml/join_group.xml"));
       $return = $zAPPLE->ParseTags ($data);
       echo $return;
       exit;
     } // if

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

     $data = implode ("", file ("code/include/data/xml/join_group.xml"));
     $return = $zAPPLE->ParseTags ($data);
     echo $return;
    break;
    case 'LEAVE_GROUP':
      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zXML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      // Authenticate token.
      $AUTH = new cAUTHSESSIONS ();
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
      list ($remotefullname, $NULL) = $zAPPLE->GetUserInformation ($remoteusername, $remotedomain);

      unset ($AUTH);


     $zGROUPS = new cGROUPINFORMATION ();
     $zGROUPS->Select ("Name", $gGROUPNAME);

     if ($zGROUPS->CountResult() == 0) {
       $gSUCCESS = FALSE;
       $gMESSAGE = "ERROR.NOTFOUND";
       $data = implode ("", file ("code/include/data/xml/leave_group.xml"));
       $return = $zAPPLE->ParseTags ($data);
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

     $data = implode ("", file ("code/include/data/xml/leave_group.xml"));
     $return = $zAPPLE->ParseTags ($data);
     echo $return;
    break;
    case 'GET_GROUP_INFORMATION':
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

     $data = implode ("", file ("code/include/data/xml/get_group_information.xml"));
     $return = $zAPPLE->ParseTags ($data);

     echo $return;
    break;
    default:
    break;
  } // switch

?>
