<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: friends.php                             CREATED: 01-01-2005 + 
  // | LOCATION: /code/user/                        MODIFIED: 07-19-2005 +
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
  // | VERSION:      0.6.0                                               |
  // | DESCRIPTION:  Displays friends section of user profile page.      |
  // | WRAPPED BY:   /code/user/main.php                                 |
  // +-------------------------------------------------------------------+

  // Classes.
  global $zFRIENDS, $zHTML;
  
  // Add javascript to top of page.
  $zHTML->AddScript ("user/friends.js");

  // Buffers.
  global $bMAINSECTION, $bONLINENOW;
  
  // Variables.
  global $gCIRCLEVIEWTYPE, $gCIRCLEVIEW, $gCIRCLEVIEWADMIN;
  global $gCIRCLEDATA, $gCIRCLEVALUE;

  // Deprecate CIRCLEVIEW.
  if ($gCIRCLEVIEWADMIN) $gCIRCLEVIEW = $gCIRCLEVIEWADMIN;

  // Create the data class.
  $zFRIENDS = new cFRIENDINFORMATION ($zAPPLE->Context);

  // Display the select all button by default.
  $gSELECTBUTTON = 'select_all';

  // Set which tab to highlight.
  $gUSERFRIENDSTAB = '';

  // Buffer the main listing.
  ob_start ();  

  // Deprecate the auto submit action.
  if ($gAUTOSUBMITACTION) $gACTION = $gAUTOSUBMITACTION;
  
  // Set the circle view to ADMIN if user has access.
  if ( ( ($zAUTHUSER->Username == $zFOCUSUSER->Username) and 
         ($zAUTHUSER->Domain == $gSITEDOMAIN) ) or
       ($zLOCALUSER->userAccess->a == TRUE) ) {

    // Load the ADMIN view menu.
    global $gCIRCLEVIEWTYPE;
    $gCIRCLEVIEWTYPE = "CIRCLEVIEWADMIN";

  } // if

  // Take Appropriate Action.
  switch ($gACTION) {

    case 'CIRCLE_ALL':
      $zFRIENDS->addCircleToList ($gMASSLIST);
    break;

    case 'CIRCLE':
      $zFRIENDS->Circle ();
    break;

    case 'SELECT_ALL':
      $gSELECTBUTTON = 'select_none';
    break;

    case 'SELECT_NONE':
      $gMASSLIST = array (); $gSELECTBUTTON = 'select_all';
    break;

    case 'DELETE':
      if ($gSITEDOMAIN == $gFRIENDDOMAIN)
        $zFRIENDS->Remove ($gFRIENDUSERNAME);
      else
        $zFRIENDS->LongDistanceRemove ($gFRIENDUSERNAME, $gFRIENDDOMAIN);
    break;

    case 'DELETE_ALL':
      $zFRIENDS->RemoveAll ($gMASSLIST);
      $gMASSLIST = array (); $gSELECTBUTTON = 'select_all';
    break;

    case 'FRIEND_REQUEST':
      if ($gSITEDOMAIN == $gFRIENDDOMAIN)
        $zFRIENDS->Request ($gFRIENDUSERNAME);
      else
        $zFRIENDS->LongDistanceRequest ($gFRIENDUSERNAME, $gFRIENDDOMAIN);
    break;

    case 'FRIEND_APPROVE':
      if ($gSITEDOMAIN == $gFRIENDDOMAIN)
        $zFRIENDS->Approve ($gFRIENDUSERNAME);
      else
        $zFRIENDS->LongDistanceApprove ($gFRIENDUSERNAME, $gFRIENDDOMAIN);
    break;

    case 'FRIEND_DENY':
      if ($gSITEDOMAIN == $gFRIENDDOMAIN)
        $zFRIENDS->Deny ($gFRIENDUSERNAME);
      else 
        $zFRIENDS->LongDistanceDeny ($gFRIENDUSERNAME, $gFRIENDDOMAIN);
    break;

    case 'FRIEND_CANCEL':
      if ($gSITEDOMAIN == $gFRIENDDOMAIN)
        $zFRIENDS->Cancel ($gFRIENDUSERNAME);
      else 
        $zFRIENDS->LongDistanceCancel ($gFRIENDUSERNAME, $gFRIENDDOMAIN);
    break;

    case 'SAVE':
      // Check if user has write access;
      if ( ($gFOCUSUSERID != $gAUTHUSERID) and 
           ($zLOCALUSER->userAccess->a == TRUE) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $zSTRINGS->Lookup ('ERROR.CANTWRITE', $zAPPLE->Context);
        $zFRIENDS->Message = $zSTRINGS->Output;
        $zFRIENDS->Error = -1;
        break;        
      } // if

      // Synchronize Data
      $zFRIENDS->Synchronize();

      // UPDATE
      $zFRIENDS->userAuth_uID = $zFOCUSUSER->uID;
      $zFRIENDS->sID = SQL_SKIP;
      $zFRIENDS->Username = SQL_SKIP;
      $zFRIENDS->Domain = SQL_SKIP;
      $zFRIENDS->Verification = SQL_SKIP;
      $zFRIENDS->Sanity();

      if (!$zFRIENDS->Error) {
        $zSTRINGS->Lookup ('MESSAGE.SAVE', $zAPPLE->Context);
        $zFRIENDS->Message = $zSTRINGS->Output;
        $zFRIENDS->Update();
        $gACTION = "";
      } // if

    break;

    default:
    break;
  } // switch

  // Deprecate the view mode.
  if ($gCIRCLEVIEWADMIN) $gCIRCLEVIEW = $gCIRCLEVIEWADMIN;

  switch ($gPROFILESUBACTION) {
    case 'requests':
      if (($gCIRCLEVIEW) or ($gCIRCLEVIEWADMIN)) break;
      if ($zLOCALUSER->uID == $zFOCUSUSER->uID) {
        // View requests.
        $gCIRCLEVIEW = CIRCLE_REQUESTS;

        // Update the stamp for the last time user viewed friend requests.
        $zLOCALUSER->userInformation->UpdateFriendStamp ();
      } // if
    break;
  } // switch

  // Determine which framework we're viewing.
  switch ($gCIRCLEVIEW) {
    case CIRCLE_EDITOR:
      $friendview = "editor";
    break;
    case CIRCLE_REQUESTS:
      $friendview = "requests";
    break;
    case CIRCLE_VIEWALL:
      $friendview = "all";
    break;
    case CIRCLE_PENDING:
      $friendview = "pending";
    break;
    case CIRCLE_NEWEST:
      $friendview = "listing";
    break;
    case CIRCLE_VIEWCIRCLES:
      $friendview = "all";
    break;
    case CIRCLE_DEFAULT:
      $friendview = "all";
    break;
    default:
      $friendview = "all";
      if ($gCIRCLEVIEW) $friendview = 'circle';
    break;
  } // switch

  $gCIRCLEDATA = $zFRIENDS->CreateFriendsMenu ($gCIRCLEVIEWTYPE);
  
  global $gCIRCLELIST;

  if (!$gCIRCLEVALUE) $gCIRCLEVALUE = 'X';

  global $gTARGET, $gCIRCLETARGET;
  $gCIRCLETARGET = "/profile/" . $zFOCUSUSER->Username . "/friends";

  // Select the proper results.
  switch ($gCIRCLEVIEW) {
    case CIRCLE_EDITOR:
      $gSCROLLSTEP[$zAPPLE->Context] = 20;
      $friendcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                               "Verification" => FRIEND_VERIFIED);
    break;
    case CIRCLE_PENDING:
      $gSCROLLSTEP[$zAPPLE->Context] = 12;
      $friendcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                               "Verification" => FRIEND_PENDING);
    break;
    case CIRCLE_DEFAULT:
    case CIRCLE_VIEWALL:
      $gSCROLLSTEP[$zAPPLE->Context] = 12;
      $friendcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                               "Verification" => FRIEND_VERIFIED);
    break;
    case CIRCLE_NEWEST:
      $gSCROLLSTEP[$zAPPLE->Context] = 12;
      $friendcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                               "Verification" => FRIEND_VERIFIED);
    break;
    case CIRCLE_REQUESTS:
      $gSCROLLSTEP[$zAPPLE->Context] = 10;
      $friendcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                                 "Verification" => FRIEND_REQUESTS);
    break;
    case CIRCLE_VIEWCIRCLES:
      $gSCROLLSTEP[$zAPPLE->Context] = 12;
      $friendcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                               "Verification" => FRIEND_VERIFIED);
    break;
    default:
      $gSCROLLSTEP[$zAPPLE->Context] = 12;
      if ($gCIRCLEVIEW) {
        $zFRIENDS->LoadFriendsCircle ($gCIRCLEVIEW);
        $SKIP = TRUE;
      } else {
        $friendcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                                 "Verification" => FRIEND_VERIFIED);
      } // if
    break;
  } // switch

  // PART II: Load the necessary data from the database.
  switch ($gACTION) {

    case 'CIRCLE':
    case 'EDIT':
      $zFRIENDS->Synchronize();
      $zFRIENDS->Select ("tID", $gtID);
      $zFRIENDS->FetchArray();
    break;

    default:
      // Create the friend listing.
      if (!$SKIP) $zFRIENDS->SelectByMultiple ($friendcriteria, "tID DESC");
    break;
  } // switch

  $gPOSTDATA[$gCIRCLEVIEWTYPE] = $gCIRCLEVIEW;
  $gPOSTDATA['SCROLLSTART'] = $gSCROLLSTART;
        
  switch ($gACTION) {
    case 'CIRCLE':
    case 'EDIT':
      $zFRIENDS->Select ("tID", $gtID);
      $zFRIENDS->FetchArray ();
    
      global $gFRIENDSICON, $gFRIENDFULLNAME;
      $gTARGET = "http://" . $zFRIENDS->Domain . "/profile/" . $zFRIENDS->Username . "/";
    
      $gFRIENDSICON = "http://" . $zFRIENDS->Domain . "/icon/" . $zFRIENDS->Username . "/";
      if ($zFRIENDS->Domain != $gSITEDOMAIN) {
        $gFRIENDFULLNAME = $zFRIENDS->Username;
      } else {
        $USER = new cUSER ($zAPPLE->Context);
        $USER->Select ("Username", $zFRIENDS->Username);
        $USER->FetchArray (); 
    
        $gFRIENDFULLNAME = $USER->userProfile->GetAlias ();
        unset ($USER);
      } // if

      $gFRIENDSICON = "http://" . $zFRIENDS->Domain . "/icon/" . $zFRIENDS->Username . "/";

      global $gCIRCLESLISTING;
      $gCIRCLESLISTING = $zFRIENDS->GetCircles();
      if (!$gCIRCLESLISTING) {
        $gCIRCLESLISTING = OUTPUT_NBSP;
      } else {
        $gCIRCLESLISTING = join (', ', $gCIRCLESLISTING);
      } // if

      $gCIRCLELIST = $zFRIENDS->CreateFullCirclesMenu ();
      
      $gPOSTDATA['CIRCLEVIEWADMIN'] = CIRCLE_EDITOR;
      $gPOSTDATA['CIRCLEVIEW'] = CIRCLE_EDITOR;

      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/edit.aobj", INCLUDE_SECURITY_NONE);
    break;

    default:
      if ($gCIRCLEVIEW == CIRCLE_VIEWCIRCLES) {
        $zFRIENDS->BufferCircleView ();
        break;
      } // if
      if ( ($zFRIENDS->Error == 0) or 
           ($gACTION == 'FRIEND_APPROVE') or 
           ($gACTION == 'FRIEND_CANCEL') or 
           ($gACTION == 'FRIEND_REQUEST') or 
           ($gACTION == 'FRIEND_DENY') or 
           ($gACTION == 'CIRCLE_ALL') or 
           ($gACTION == 'MOVE_UP') or 
           ($gACTION == 'MOVE_DOWN') or 
           ($gACTION == 'DELETE_ALL') ) {
  
        $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/$friendview/list.top.aobj", INCLUDE_SECURITY_NONE);
  
        // Calculate scroll values.
        $gSCROLLMAX[$zAPPLE->Context] = $zFRIENDS->CountResult();
    
        // Adjust for a recently deleted entry.
        $zAPPLE->AdjustScroll ('user.friends', $zFRIENDS);
  
        // Check if any results were found.
        if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
          $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.FRIENDS');
          $zFRIENDS->Message = $zSTRINGS->Output;
          $zFRIENDS->Broadcast();
        } // if
    
        global $gLISTCOUNT;
      
        // Counter for switching up Alternate.
        $switchcount = 0;
  
        global $gCHECKED;
        global $gFRIENDSICON, $gFRIENDFULLNAME, $gFRIENDNAME;
    
        // Loop through the list.
        for ($gLISTCOUNT = 0; $gLISTCOUNT < $gSCROLLSTEP[$zAPPLE->Context]; $gLISTCOUNT++) {
          
         if ($zFRIENDS->FetchArray()) {
    
          // Check if entry is hidden or blocked for this user.
          // $gPRIVACYSETTING = $zFRIENDS->friendCirclesPrivacy->Determine ("zFOCUSUSER", "zLOCALUSER", "friendCircles_tID", $zFRIENDS->friendCircles->tID);
    
          $bONLINENOW = OUTPUT_NBSP;

          // Retrieve user info.

          if ($zFRIENDS->Domain != $gSITEDOMAIN) {
            $gFRIENDFULLNAME = $zFRIENDS->Username;
          } else {
            list ($gFRIENDFULLNAME, $online) = $zFRIENDS->GetUserInformation();
          } // if
          // Load the online icon.
          if ($online) $bONLINENOW = $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/onlinenow.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);

          // Create the default icon URL.
          $gFRIENDSICON = "http://" . $zFRIENDS->Domain . "/icon/" . $zFRIENDS->Username . "/";

          if ($zFRIENDS->Alias) {
            $gFRIENDNAME = $zFRIENDS->Alias;
          } else {
            $gFRIENDNAME = $gFRIENDFULLNAME;
          } // if
    
          // Adjust for a hidden entry.
          if ( $zAPPLE->AdjustHiddenScroll ($gPRIVACYSETTING, $zAPPLE->Context) ) continue;
    
          global $gTARGET;
          $gTARGET = "http://" . $zFRIENDS->Domain . "/profile/" . $zFRIENDS->Username . "/";

          global $bFRIENDICON;
          $bFRIENDICON = $zAPPLE->BufferUserIcon ($zFRIENDS->Username, $zFRIENDS->Domain, NULL);
    
          global $gEDITTARGET, $gEDITPOST;
          $gEDITTARGET = "http://" . $gSITEDOMAIN . "/profile/" . $zFOCUSUSER->Username . "/friends/";
          $gEDITPOST   = 'gACTION=EDIT&gtID=' . $zFRIENDS->tID;
    
          $gCHECKED = FALSE;
          // Select 
          if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;

          $zFRIENDS->friendCirclesList->Select ("friendInformation_tID", $zFRIENDS->tID);
          if ($zFRIENDS->friendCirclesList->CountResult () == 0) {
            $gCIRCLESLISTING = OUTPUT_NBSP;
          } else {
            $circlearray = array ();

            while ($zFRIENDS->friendCirclesList->FetchArray ()) {
              $zFRIENDS->friendCircles->Select ("tID", $zFRIENDS->friendCirclesList->friendCircles_tID);
              $zFRIENDS->friendCircles->FetchArray ();
              array_push ($circlearray, $zFRIENDS->friendCircles->Name);
            } // while

            global $gCIRCLESLISTING;
            $gCIRCLESLISTING = join (", ", $circlearray);

          } // if

          if ($zFRIENDS->Domain != $gSITEDOMAIN) 
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/$friendview/list.middle.remote.aobj", INCLUDE_SECURITY_NONE);
          else 
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/$friendview/list.middle.aobj", INCLUDE_SECURITY_NONE);
    
         } else {
          break;
         } // if
        } // for
    
        $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/friends/";
  
        $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/$friendview/list.bottom.aobj", INCLUDE_SECURITY_NONE);
  
      } elseif ( ($gACTION == 'SAVE') or ($gACTION == 'DELETE') or 
                 ($gACTION == 'CIRCLE') ) {
        if ($gtID) {
          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/edit.aobj", INCLUDE_SECURITY_NONE);
        } else {
          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/new.aobj", INCLUDE_SECURITY_NONE);
        } // if
      } // if
    break;
  } // switch

  // Retrieve output buffer.
  $bMAINSECTION = ob_get_clean (); 

  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/users/friends.afrw", INCLUDE_SECURITY_NONE);

?>
