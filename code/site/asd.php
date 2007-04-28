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
  // | VERSION:      0.7.0                                               |
  // | DESCRIPTION:  ASD Network traffic hub.                            |
  // +-------------------------------------------------------------------+

  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include Lightweight Server Classes
  require_once ('code/include/classes/server.php'); 
  
  // Suppress warning reports.
  error_reporting (E_ERROR);
  
  $gACTION = $_POST['gACTION'];
  
  switch ($gACTION) {
    case 'USER_INFORMATION':
    
      $gUSERNAME = $_POST['gUSERNAME'];
      
      // Create the Server class.
      $zSERVER = new cSERVER ($gDOMAIN);
      
      // Get User Info.
      $zSERVER->UserInformation ($gUSERNAME);
      
      echo $zSERVER->XML->Data; exit;
      
    break;
    case 'TOKEN_CHECK':
      
      $gTOKEN = $_POST['gTOKEN'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      // Create the Server class.
      $zSERVER = new cSERVER ($gDOMAIN);
      
      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zSERVER->XML->ErrorData ($code, $message);
        
        echo $return; exit;
      } // if
      
      // Check Friend Status.
      $zSERVER->TokenCheckLocal ($gTOKEN, $gDOMAIN);
      
      echo $zSERVER->XML->Data; 
      
      exit;
      
    break;
    case 'FRIEND_STATUS':
      $gTOKEN = $_POST['gTOKEN'];
      $gUSERNAME = $_POST['gUSERNAME'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      // Create the Server class.
      $zSERVER = new cSERVER ($gDOMAIN);
      
      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zSERVER->XML->ErrorData ($code, $message);
        
        echo $return; exit;
      } // if
      
      // Check Friend Status.
      $zSERVER->FriendStatus ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data; exit;
      
    break;
    case 'FRIEND_REQUEST':
      $gTOKEN = $_POST['gTOKEN'];
      $gUSERNAME = $_POST['gUSERNAME'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      // Create the Server class.
      $zSERVER = new cSERVER ($gDOMAIN);
      
      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zSERVER->XML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      // Check Friend Status.
      $zSERVER->FriendRequest ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data;
    break;
    case 'FRIEND_CANCEL':
      $gTOKEN = $_POST['gTOKEN'];
      $gUSERNAME = $_POST['gUSERNAME'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      // Create the Server class.
      $zSERVER = new cSERVER ($gDOMAIN);
      
      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zSERVER->XML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      // Check Friend Status.
      $zSERVER->FriendCancel ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data;
    break;
    case 'FRIEND_DELETE':
      $gTOKEN = $_POST['gTOKEN'];
      $gUSERNAME = $_POST['gUSERNAME'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      // Create the Server class.
      $zSERVER = new cSERVER ($gDOMAIN);
      
      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zSERVER->XML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      // Check Friend Status.
      $zSERVER->FriendDelete ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data;
    break;
    case 'FRIEND_APPROVE':
      $gTOKEN = $_POST['gTOKEN'];
      $gUSERNAME = $_POST['gUSERNAME'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      // Create the Server class.
      $zSERVER = new cSERVER ($gDOMAIN);
      
      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zSERVER->XML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      // Check Friend Status.
      $zSERVER->FriendApprove ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data;
    break;
    case 'LOGIN_CHECK':
    
      $gUSERNAME = $_POST['gUSERNAME'];
      $gDOMAIN = $_POST['gDOMAIN'];
      
      // Create the Server class.
      $zSERVER = new cSERVER ($gDOMAIN);
      
      $zSERVER->LoginCheck ($gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data; exit;
    
    break;
    case 'ICON_LIST':
    
      $gDOMAIN = $_POST['gDOMAIN'];
      $gUSERNAME = $_POST['gUSERNAME'];
    
      // Create the Server class.
      $zSERVER = new cSERVER ($gDOMAIN);
      
      // Retrieve the icon list.
      $zSERVER->IconList ($gUSERNAME);
      
      echo $zSERVER->XML->Data; exit;
      
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
    case 'GROUP_JOIN':
      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zXML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      // Authenticate token.
      /* TEMPORARILY REMOVED
      $AUTH = new cAUTHSESSIONS ();
      $AUTH->Select ("Token", $gTOKEN);

      if ($AUTH->CountResult () == 0) {
        $code = 1000;
        $message = "ERROR.INVALIDTOKEN";
        $return = $zXML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      $AUTH->FetchArray ();
      */
      
      $remoteusername = $AUTH->Username;
      $remotedomain = $AUTH->Domain;
      list ($remotefullname, $NULL) = $zAPPLE->GetUserInformation ($remoteusername, $remotedomain);

      unset ($AUTH);

     $zGROUPS = new cGROUPINFORMATION ();
     $zGROUPS->Select ("Name", $gGROUPNAME);

     if ($zGROUPS->CountResult() == 0) {
       $gSUCCESS = FALSE;
       $gMESSAGE = "ERROR.NOTFOUND";
       $data = implode ("", file ("code/include/data/xml/group_join.xml"));
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

     $data = implode ("", file ("code/include/data/xml/group_join.xml"));
     $return = $zAPPLE->ParseTags ($data);
     echo $return;
    break;
    case 'GROUP_LEAVE':
      // Check for an authentication token.
      if (!$gTOKEN) {
        $code = 1000;
        $message = "ERROR.NOTOKEN";
        $return = $zXML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      // Authenticate token.
      /* TEMPORARILY REMOVED
      $AUTH = new cAUTHSESSIONS ();
      $AUTH->Select ("Token", $gTOKEN);

      if ($AUTH->CountResult () == 0) {
        $code = 1000;
        $message = "ERROR.INVALIDTOKEN";
        $return = $zXML->ErrorData ($code, $message);
        echo $return; exit;
      } // if

      $AUTH->FetchArray ();

      */
      
      $remoteusername = $AUTH->Username;
      $remotedomain = $AUTH->Domain;
      list ($remotefullname, $NULL) = $zAPPLE->GetUserInformation ($remoteusername, $remotedomain);

      unset ($AUTH);


     $zGROUPS = new cGROUPINFORMATION ();
     $zGROUPS->Select ("Name", $gGROUPNAME);

     if ($zGROUPS->CountResult() == 0) {
       $gSUCCESS = FALSE;
       $gMESSAGE = "ERROR.NOTFOUND";
       $data = implode ("", file ("code/include/data/xml/group_leave.xml"));
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

     $data = implode ("", file ("code/include/data/xml/group_leave.xml"));
     $return = $zAPPLE->ParseTags ($data);
     echo $return;
    break;
    case 'GROUP_INFORMATION':
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

     $data = implode ("", file ("code/include/data/xml/group_information.xml"));
     $return = $zAPPLE->ParseTags ($data);

     echo $return;
    break;
    default:
    break;
  } // switch

?>
