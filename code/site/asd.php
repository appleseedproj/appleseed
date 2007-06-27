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
  $gTOKEN = $_POST['gTOKEN'];
  $gDOMAIN = $_POST['gDOMAIN'];
  $gIDENTIFIER = $_POST['gIDENTIFIER'];
  
  // Create the Server class.
  $zSERVER = new cSERVER ($gDOMAIN);
    
  // Check for an authentication token.
  if ( (!$gTOKEN) and (!$gIDENTIFIER) ) {
    $errortitle = "ERROR.NOTOKEN";
    $return = $zSERVER->XML->ErrorData ($errortitle);
    
    echo $return; exit;
  } // if
  
  switch (strtoupper ($gACTION)) {
    case 'USER_INFORMATION':
    case 'ASD_USER_INFORMATION':
    
      $gUSERNAME = $_POST['gUSERNAME'];
      
      // Get User Info.
      $zSERVER->UserInformation ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data; exit;
      
    break;
    
    case 'TOKEN_CHECK':
    case 'ASD_TOKEN_CHECK':
      
      // Check Friend Status.
      $zSERVER->TokenCheckLocal ($gTOKEN, $gDOMAIN);
      
      echo $zSERVER->XML->Data; exit;
      
    break;
    
    case 'FRIEND_STATUS':
    case 'ASD_FRIEND_STATUS':
      $gUSERNAME = $_POST['gUSERNAME'];
      
      // Check Friend Status.
      $zSERVER->FriendStatus ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data; exit;
      
    break;
    
    case 'FRIEND_REQUEST':
    case 'ASD_FRIEND_REQUEST':
      $gUSERNAME = $_POST['gUSERNAME'];
      
      // Check Friend Status.
      $zSERVER->FriendRequest ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data;
    break;
    
    case 'FRIEND_CANCEL':
    case 'ASD_FRIEND_CANCEL':
      $gUSERNAME = $_POST['gUSERNAME'];
      
      // Check Friend Status.
      $zSERVER->FriendCancel ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data;
    break;
    
    case 'FRIEND_DENY':
    case 'ASD_FRIEND_DENY':
      $gUSERNAME = $_POST['gUSERNAME'];
      
      // Check Friend Status.
      $zSERVER->FriendDeny ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data;
    break;
    
    case 'FRIEND_DELETE':
    case 'ASD_FRIEND_DELETE':
      $gUSERNAME = $_POST['gUSERNAME'];
      
      // Check Friend Status.
      $zSERVER->FriendDelete ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data;
    break;
    
    case 'FRIEND_APPROVE':
    case 'ASD_FRIEND_APPROVE':
      $gUSERNAME = $_POST['gUSERNAME'];
      
      // Check Friend Status.
      $zSERVER->FriendApprove ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data;
    break;
    
    case 'LOGIN_CHECK':
    case 'ASD_LOGIN_CHECK':
    
      $gUSERNAME = $_POST['gUSERNAME'];
      
      $zSERVER->LoginCheck ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data; exit;
    
    break;
    
    case 'ICON_LIST':
    case 'ASD_ICON_LIST':
    
      $gUSERNAME = $_POST['gUSERNAME'];
      
      // Retrieve the icon list.
      $zSERVER->IconList ($gTOKEN, $gUSERNAME, $gDOMAIN);
      
      echo $zSERVER->XML->Data; exit;
      
    break;
    
    case 'MESSAGE_RETRIEVE':
    case 'ASD_MESSAGE_RETRIEVE':
    
      $gIDENTIFIER = $_POST['gIDENTIFIER'];
      $gUSERNAME = $_POST['gUSERNAME'];
      
      // Retrieve a message. 
      $zSERVER->MessageRetrieve ($gUSERNAME, $gIDENTIFIER);
      
      echo $zSERVER->XML->Data; exit;
      
    break;
    
    case 'MESSAGE_NOTIFY':
    case 'ASD_MESSAGE_NOTIFY':
      $gRECIPIENT       = $_POST['gRECIPIENT'];
      $gFULLNAME        = $_POST['gFULLNAME'];
      $gUSERNAME        = $_POST['gUSERNAME'];
      $gIDENTIFIER      = $_POST['gIDENTIFIER'];
      $gSUBJECT         = $_POST['gSUBJECT'];
      
      // Store a message notification. 
      $zSERVER->MessageNotify ($gRECIPIENT, $gFULLNAME, $gUSERNAME, $gDOMAIN, $gIDENTIFIER, $gSUBJECT);
      
      echo $zSERVER->XML->Data; exit;
      
    break;
    
    case 'GROUP_JOIN':
    case 'ASD_GROUP_JOIN':
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
    case 'ASD_GROUP_LEAVE':
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
    case 'ASD_GROUP_INFORMATION':
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
    
    case 'UPDATE_NODE_NETWORK':
    case 'ASD_UPDATE_NODE_NETWORK':
    
      $gSUMMARY = $_POST['gSUMMARY'];
      $gUSERS = $_POST['gUSERS'];
      
      $zSERVER->UpdateNodeNetwork ($gTOKEN, $gDOMAIN, $gSUMMARY, $gUSERS);
      
      echo $zSERVER->XML->Data; exit;
    
    break;
    
    case 'NODE_INFORMATION':
    case 'ASD_NODE_INFORMATION':
    
    break;
    
    case 'TRUSTED_LIST':
    case 'ASD_TRUSTED_LIST':
    
      $zSERVER->TrustedList ($gTOKEN, $gDOMAIN);
      
      echo $zSERVER->XML->Data; exit;
    
    break;
    
    default:
    break;
  } // switch

?>
