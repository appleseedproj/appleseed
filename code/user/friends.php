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
  global $zFRIENDS;

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

  $gCIRCLEVIEWTYPE = "CIRCLEVIEW";

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
      $zFRIENDS->Remove ($zAUTHUSER->Username, $zAUTHUSER->Domain, $gFRIENDUSERNAME, $gFRIENDDOMAIN);
    break;

    case 'DELETE_ALL':
      $zFRIENDS->RemoveAll ($gMASSLIST);
      $gMASSLIST = array (); $gSELECTBUTTON = 'select_all';
    break;

    case 'ADD_REMOTE_FRIEND':
      // Step 1:  Check if user is already a friend.
      $checkcriteria = array ("userAuth_uID" => $zLOCALUSER->uID,
                              "Username"       => $gUSERNAME,
                              "Domain"         => $gDOMAIN);
      $zFRIENDS->SelectByMultiple ($checkcriteria);
      $zFRIENDS->FetchArray ();

      // Exit out if user already is a friend.
      if ($zFRIENDS->CountResult() > 0) {
        global $gREQUESTNAME;
        $gREQUESTNAME = $zFRIENDS->Username;
        $zSTRINGS->Lookup ('ERROR.ALREADY', 'USER.FRIENDS');
        $zFRIENDS->Message = $zSTRINGS->Output;
        unset ($gREQUESTNAME);
        break;
      } // if

      // Step 2: Find the highest Sort ID.
      $statement = "SELECT MAX(sID) FROM friendInformation WHERE userAuth_uID = " . $zLOCALUSER->uID;
      $query = mysql_query($statement);
      $result = mysql_fetch_row($query);
      $sortid = $result[0] + 1;

      // Step 3: Create relationship if user already has a pending request.
      $checkcriteria = array ("userAuth_uID" => $zLOCALUSER->uID,
                              "Username"     => $gUSERNAME,
                              "Domain"       => $gDOMAIN,
                              "Verification"  => FRIEND_REQUESTS);

      $zFRIENDS->SelectByMultiple ($checkcriteria);
      $zFRIENDS->FetchArray ();

      // Update the friend's record.
      $verification = FRIEND_PENDING;
      if ($zFRIENDS->CountResult() > 0) {
        $zFRIENDS->Verification = FRIEND_VERIFIED;
        $zFRIENDS->Update ();
        $verification = FRIEND_VERIFIED;
      } else {
        $zREMOTE = new cREMOTE ($gDOMAIN);

        // Add to requested (Friend -> Remote); 
        $datalist = array ("gACTION"   => "ADD_REMOTE_FRIEND",
                           "gUSERNAME" => $gUSERNAME,
                           "gREMOTEUSERNAME" => $zFOCUSUSER->Username,
                           "gREMOTEDOMAIN"   => $gSITEDOMAIN);
        $zREMOTE->Post ($datalist);

        $returndata = split ("\n", $zREMOTE->Return);

        $result = $returndata[0];
        $email = $returndata[1];
        $fullname = $returndata[2];

        // Notify requested user.
        $zFRIENDS->NotifyRequest ($email, $fullname, $zFOCUSUSER->userProfile->GetAlias (), $gUSERNAME, $gDOMAIN);

      } // if

      // Add pending (FOCUSUSER -> Friend)
      $zFRIENDS->userAuth_uID = $zLOCALUSER->uID;
      $zFRIENDS->sID = $sortid;
      $zFRIENDS->UserID = NULL;
      $zFRIENDS->Username = $gUSERNAME;
      $zFRIENDS->Domain = $gDOMAIN;
      $zFRIENDS->Verification = $verification;

       $zFRIENDS->Add ();

      global $gREQUESTEDUSER;
      $gREQUESTEDUSER = $zFRIENDS->Username;

      // If no error, determine the success message and where to send the user.
      if ($zFRIENDS->Error == 0) {
        if ($verification == FRIEND_PENDING) {
          $zSTRINGS->Lookup ('MESSAGE.REQUEST', 'USER.FRIENDS');
          $gCIRCLEVIEWADMIN = CIRCLE_PENDING;
          $gCIRCLEVIEW = CIRCLE_PENDING;
        } else {
          global $gREQUESTEDUSER;
          $gREQUESTEDUSER = $zFRIENDS->Username;
          $zSTRINGS->Lookup ('MESSAGE.ADDED', 'USER.FRIENDS');
          $gCIRCLEVIEWADMIN = CIRCLE_NEWEST;
          $gCIRCLEVIEW = CIRCLE_NEWEST;
        } // if
        $zFRIENDS->Message = $zSTRINGS->Output;
      } // if
    break;

    case 'ADD_FRIEND':
      $zFRIENDS->Request ($zAUTHUSER->Username, $zAUTHUSER->Domain, $gFRIENDUSERNAME, $gFRIENDDOMAIN);
    break;

    case 'APPROVE':
      $zFRIENDS->Approve ($gFRIENDUSERNAME, $gFRIENDDOMAIN, $zAUTHUSER->Username, $zAUTHUSER->Domain);
    break;

    case 'DENY':
      $zFRIENDS->Deny ($gFRIENDUSERNAME, $gFRIENDDOMAIN, $zAUTHUSER->Username, $zAUTHUSER->Domain);
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
      $zFRIENDS->UserID = SQL_SKIP;
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
    default:
      $friendview = "all";
      if ($gCIRCLEVIEW) $friendview = 'circle';
    break;
  } // switch

  // Create the Circle View menu.
  if ($gCIRCLEVIEWTYPE == "CIRCLEVIEWADMIN") {

    $gCIRCLEDATA = array (CIRCLE_DEFAULT   => "Default View",
                          CIRCLE_NEWEST    => "Newest",
                          CIRCLE_VIEWALL   => "View All",
                          CIRCLE_REQUESTS  => "Requests",
                          CIRCLE_PENDING   => "Pending",
                          CIRCLE_EDITOR    => "Editor View");
  } else {
    $gCIRCLEDATA = array (CIRCLE_DEFAULT   => "Default View",
                          CIRCLE_NEWEST    => "Newest",
                          CIRCLE_VIEWALL   => "View All");
  } // if

  global $gCIRCLELIST;

  $gCIRCLELIST = array ("X"    => MENU_DISABLED . "Add To Circle:");
  if (!$gCIRCLEVALUE) $gCIRCLEVALUE = 'X';

  global $gTARGET, $gCIRCLETARGET;
  $gCIRCLETARGET = "/profile/" . $zFOCUSUSER->Username . "/friends";

  // Select the friend circles list.
  $zFRIENDS->friendCircles->Select ("userAuth_uID", $zFOCUSUSER->uID, "sID ASC");

  // Add the list of circles to the menu.
  if ($zFRIENDS->friendCircles->CountResult() > 0) {

    $gCIRCLEDATA[NULL] = MENU_DISABLED . "----------";
    $gCIRCLEDATA[CIRCLE_VIEWCIRCLES] = "View Circles";

    // Loop through the friends circles.
    while ($zFRIENDS->friendCircles->FetchArray ()) {
      $gCIRCLEDATA[$zFRIENDS->friendCircles->tID] = "&nbsp;" . $zFRIENDS->friendCircles->Name;
      $gCIRCLELIST[$zFRIENDS->friendCircles->tID] = "&nbsp;" . $zFRIENDS->friendCircles->Name;
      // array_push ($gCIRCLEDATA, $zFRIENDS->friendCircles->tID, $zFRIENDS->friendCircles->Name);
    } // while

  } else {
    $gCIRCLELIST = NULL;
  } // if

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

  switch ($gACTION) {
    case 'CIRCLE':
    case 'EDIT':
      $zFRIENDS->Select ("userAuth_uID", $guID);
      $zFRIENDS->FetchArray ();
      $USER = new cUSER ($zAPPLE->Context);
    
      $USER->Select ("uID", $zFRIENDS->UserID);
      $USER->FetchArray (); 
    
      global $gFRIENDSICON, $gFRIENDFULLNAME;
      $gTARGET = "http://" . $zFRIENDS->Domain . "/profile/" . $zFRIENDS->Username . "/";
    
      $gFRIENDSICON = "http://" . $zFRIENDS->Domain . "/icon/" . $zFRIENDS->Username . "/";
      if ($zFRIENDS->Domain != $gSITEDOMAIN) {
        list ($gFRIENDFULLNAME, $online) = $zFRIENDS->GetUserInformation();
      } else {
        $gFRIENDFULLNAME = ucwords ($USER->userProfile->GetAlias ());
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

      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/edit.aobj", INCLUDE_SECURITY_NONE);
    break;

    default:
      if ($gCIRCLEVIEW == CIRCLE_VIEWCIRCLES) {
        $zFRIENDS->BufferCircleView ();
        break;
      } // if
      if ( ($zFRIENDS->Error == 0) or 
           ($gACTION == 'ADD_FRIEND') or 
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
    
        $gPOSTDATA[$gCIRCLEVIEWTYPE] = $gCIRCLEVIEW;
  
        // Loop through the list.
        for ($gLISTCOUNT = 0; $gLISTCOUNT < $gSCROLLSTEP[$zAPPLE->Context]; $gLISTCOUNT++) {
         if ($zFRIENDS->FetchArray()) {
    
          // Check if entry is hidden or blocked for this user.
          // $gPRIVACYSETTING = $zFRIENDS->friendCirclesPrivacy->Determine ("zFOCUSUSER", "zLOCALUSER", "friendCircles_tID", $zFRIENDS->friendCircles->tID);
    
          $bONLINENOW = OUTPUT_NBSP;

          // Retrieve user info.
          list ($gFRIENDFULLNAME, $online) = $zFRIENDS->GetUserInformation();

          // Load the online icon.
          if ($online) $bONLINENOW = $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/onlinenow.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);

          // Create the default icon URL.
          $gFRIENDSICON = "http://" . $zFRIENDS->Domain . "/icon/" . $zFRIENDS->Username . "/";

          if ($zFRIENDS->Alias) {
            $gFRIENDNAME = ucwords ($zFRIENDS->Alias);
          } else {
            $gFRIENDNAME = ucwords ($gFRIENDFULLNAME);
          } // if
    
          unset ($USER);
    
          // Adjust for a hidden entry.
          if ( $zAPPLE->AdjustHiddenScroll ($gPRIVACYSETTING, $zAPPLE->Context) ) continue;
    
          global $gTARGET;
          $gTARGET = "http://" . $zFRIENDS->Domain . "/profile/" . $zFRIENDS->Username . "/";

          global $bFRIENDICON;
          $bFRIENDICON = $zAPPLE->BufferUserIcon ($zFRIENDS->Username, $zFRIENDS->Domain, NULL);
    
          global $gEDITTARGET;
          $gEDITTARGET = "/profile/" . $zFOCUSUSER->Username . "/friends/";
    
          global $gEXTRAPOSTDATA;
          $gEXTRAPOSTDATA['ACTION'] = "EDIT";
          $gEXTRAPOSTDATA['tID'] = $zFRIENDS->tID;
    
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

          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/$friendview/list.middle.aobj", INCLUDE_SECURITY_NONE);
    
          unset ($gEXTRAPOSTDATA);
    
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
