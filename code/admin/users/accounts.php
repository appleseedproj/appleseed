<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: accounts.php                            CREATED: 02-11-2005 + 
  // | LOCATION: /code/admin/users/                 MODIFIED: 01-13-2006 +
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
  // | VERSION:     0.6.0                                                |
  // | DESCRIPTION: User accounts editor.                                |
  // +-------------------------------------------------------------------+

  // Change to document root directory
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include BASE API classes.
  require_once ('code/include/classes/BASE/application.php'); 
  require_once ('code/include/classes/base.php'); 
  require_once ('code/include/classes/system.php'); 
  require_once ('code/include/classes/BASE/remote.php'); 

  // Include Appleseed classes.
  require_once ('code/include/classes/appleseed.php'); 
  require_once ('code/include/classes/privacy.php'); 
  require_once ('code/include/classes/friends.php'); 
  require_once ('code/include/classes/messages.php'); 
  require_once ('code/include/classes/users.php'); 
  require_once ('code/include/classes/auth.php'); 

  // Create the Application class.
  $zAPPLE = new cAPPLESEED ();
  
  // Set Global Variables (Put this at the top of wrapper scripts)
  $zAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zAPPLE->Initialize("admin.users.accounts");

  // Create local classes.
  $ADMINDATA = new cUSER ($zAPPLE->Context);

  // Load security settings for the current page.
  $zLOCALUSER->Access (FALSE, FALSE, FALSE);

  // Check to see if user has read access for this area.
  if ($zLOCALUSER->userAccess->r == FALSE) {

    $zAPPLE->IncludeFile ('code/site/error/403.php', INCLUDE_SECURITY_NONE);
    $zAPPLE->End();

  } // if

  // Create a warning message if user has no write access.
  if ($zLOCALUSER->userAccess->w == FALSE) {
    $zSTRINGS->Lookup ('ERROR.CANTWRITE', $zAPPLE->Context);
    $ADMINDATA->Message = $zSTRINGS->Output;
    $ADMINDATA->Error = 0;
  } // if

  // Set the page title.
  $gPAGESUBTITLE = ' - Admin';

  // Set how much to step when scrolling.
  $gSCROLLSTEP[$zAPPLE->Context] = 10;

  // Set the post data to move back and forth.
  $gPOSTDATA = Array ("SEARCHACCOUNTSBY" => $gSEARCHACCOUNTSBY,
                      "CRITERIA"          => $gCRITERIA,
                      "SCROLLSTART"       => $gSCROLLSTART,
                      "SORT"              => $gSORT);

  // Set which switch to highlight.
  $gADMINUSERSSWITCH = '';

  // Set which tab to highlight.
  $gADMINUSERSACCOUNTSTAB = '';

  // Display the select all button by default.
  $gSELECTBUTTON = 'select_all';

  // Change the select button if anything is eelected.
  if ($zAPPLE->ArrayIsSet ($gMASSLIST) ) $gSELECTBUTTON = 'select_none';

  // PART I: Determine appropriate action.
  switch ($gACTION) {
    case 'SELECT_ALL':
      $gSELECTBUTTON = 'select_none';
    break;

    case 'SELECT_NONE':
      $gMASSLIST = array ();
      $gSELECTBUTTON = 'select_all';
    break;

    case 'MOVE_UP':
      // Check if any items were selected.
      if (!$gMASSLIST) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED', $zAPPLE->Context);
        $ADMINDATA->Message = $zSTRINGS->Output;
        $ADMINDATA->Error = -1;
        break;
      } // if

      // Loop through the list
      foreach ($gMASSLIST as $number => $uidvalue) {
        $ADMINDATA->uID = $uidvalue;
        $ADMINDATA->Move(UP, $uidvalue);
        // Move in the selected list.
        if (!$ADMINDATA->Error) $gMASSLIST[$number] = $uidvalue - 1;
      } // if

    break;

    case 'MOVE_DOWN':
      // Check if any items were selected.
      if (!$gMASSLIST) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED', $zAPPLE->Context);
        $ADMINDATA->Message = $zSTRINGS->Output;
        $ADMINDATA->Error = -1;
        break;
      } // if

      // Loop through the list
      foreach ($gMASSLIST as $number => $uidvalue) {
        $ADMINDATA->uID = $uidvalue;
        $ADMINDATA->Move(DOWN, $uidvalue);
        // Move in the selected list.
        if (!$ADMINDATA->Error) $gMASSLIST[$number] = $uidvalue + 1;
      } // if

    break;

    case 'DELETE_ALL':
      // Check if any items were selected.
      if (!$gMASSLIST) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED', $zAPPLE->Context);
        $ADMINDATA->Message = $zSTRINGS->Output;
        $ADMINDATA->Error = -1;
        break;
      } // if

      // Loop through the list
      foreach ($gMASSLIST as $number => $uidvalue) {
        $ADMINDATA->uID = $uidvalue;

        // Retrieve username before deleting for deleting photo data.
        $photodefs = array ("uID" => $ADMINDATA->uID);
        $ADMINDATA->SelectByMultiple ($photodefs);
        $ADMINDATA->FetchArray ();

        // Delete photo directory
        $phototarget = "photos/" . $ADMINDATA->Username . "/";

        $ADMINDATA->Delete();

        if (!$zAPPLE->RemoveDirectory ($phototarget, TRUE)) {
          // Output error message if unsuccessful.
          $zSTRINGS->Lookup ('ERROR.STORED', $zAPPLE->Context);
          $ADMINDATA->Message = $zSTRINGS->Output;
          $ADMINDATA->Error = -1;
        } // if
        
        if (!$ADMINDATA->Error) $datalist[$number] = $uidvalue;
      } // if

      // If no errors, output a successful message.
      if (!$ADMINDATA->Error) {
        global $gDATALIST; $gDATALIST = implode(", ", $datalist);
        // Use proper grammer depending on how many records chosen.
        if (count ($gMASSLIST) == 1) {
          global $gDATAID; $gDATAID = $datalist[0];
          $zSTRINGS->Lookup ('MESSAGE.DELETE', $zAPPLE->Context);
          unset ($gDATAID);
        } else {
          $zSTRINGS->Lookup ('MESSAGE.DELETEALL', $zAPPLE->Context);
        } // if
        $ADMINDATA->Message = $zSTRINGS->Output;
        unset ($gDATALIST);
        unset ($gMASSLIST);
      } // if
    break;

    case 'SAVE':
      // Check if user has write access;
      if ($zLOCALUSER->userAccess->w == FALSE) {
        $zSTRINGS->Lookup ('ERROR.CANTWRITE', $zAPPLE->Context);
        $ADMINDATA->Message = $zSTRINGS->Output;
        $ADMINDATA->Error = -1;
        break;        
      } // if

      // Look up the old username.
      $ADMINDATA->Select ("uID", $guID);
      $ADMINDATA->FetchArray();

      $oldphotodir = "photos/" . $ADMINDATA->Username;
      $newphotodir = "photos/" . $gUSERNAME;

      // Synchronize Data
      $ADMINDATA->Synchronize();
      $ADMINDATA->userProfile->Synchronize();
      $ADMINDATA->userInvites->Synchronize();
      $ADMINDATA->userInvites->userAuth_uID = $ADMINDATA->uID;

      if (!$ADMINDATA->userProfile->userAuth_uID) {
        // Load up Profile information to see if record exists.
        $ADMINDATA->userProfile->Select ("userAuth_uID", $ADMINDATA->uID);
        if ($ADMINDATA->userProfile->CountResult() > 0)
          $ADMINDATA->userProfile->userAuth_uID = $ADMINDATA->uID;
      } // if
      
      // If uID is empty then we're adding, otherwise we're updating.
      if ($ADMINDATA->uID == "") {
        $ADMINDATA->uID = 1;
        $ADMINDATA->Sanity();
        $ADMINDATA->userProfile->userAuth_uID = 1;

        $ADMINDATA->Sanity();
        $ADMINDATA->userProfile->Fullname = SQL_SKIP;
        $ADMINDATA->userProfile->Alias = SQL_SKIP;
        $ADMINDATA->userProfile->Zipcode = SQL_SKIP;
        $ADMINDATA->userProfile->Sanity();

        $ADMINDATA->userInvites->Recipient = SQL_SKIP;
        $ADMINDATA->userInvites->Sanity ();

        // If everything checks out, then add the record.
        if ( (!$ADMINDATA->Error) AND (!$ADMINDATA->userProfile->Error) AND
             (!$ADMINDATA->userInvites->Error) ) {
          // Cascade only the user profiles and information tables.
          unset ($ADMINDATA->Cascade);
          $ADMINDATA->Cascade = array ("userProfile", "userInformation");
          
          // Add the authorization record.
          $ADMINDATA->Add();

          // Initialize the new account.
          $ADMINDATA->Initialize ();

          // Update the amount of invites available.
          $ADMINDATA->userInvites->ChangeInvites ();

          // Look up the saved message.
          global $gDATAID;  $gDATAID = $ADMINDATA->LastIncrement;
          $zSTRINGS->Lookup ('MESSAGE.NEW', $zAPPLE->Context);
          $ADMINDATA->Message = $zSTRINGS->Output;
          unset ($gDATAID);
        } // if
      } else {
        // If password is null, then set to SQL_SKIP to skip it.
        if ($ADMINDATA->Pass == "") $ADMINDATA->Pass = SQL_SKIP;

        // Don't bother with the invite information.
        $ADMINDATA->Invite = SQL_SKIP;

        $ADMINDATA->Sanity();
        $ADMINDATA->userProfile->Fullname = SQL_SKIP;
        $ADMINDATA->userProfile->Alias = SQL_SKIP;
        $ADMINDATA->userProfile->Zipcode = SQL_SKIP;
        $ADMINDATA->userProfile->Sanity();

        $ADMINDATA->userInvites->Recipient = SQL_SKIP;
        $ADMINDATA->userInvites->Sanity ();

        if ( (!$ADMINDATA->Error) AND (!$ADMINDATA->userProfile->Error) AND
             (!$ADMINDATA->userInvites->Error) ) {
          global $gDATAID;  $gDATAID = $ADMINDATA->uID;
          $zSTRINGS->Lookup ('MESSAGE.SAVE', $zAPPLE->Context);
          $ADMINDATA->Message = $zSTRINGS->Output;
          unset ($gDATAID);
          $ADMINDATA->Update();

          // Rename the user's photo directory.
          rename ($oldphotodir, $newphotodir);
          
          // Update the amount of invites available.
          $ADMINDATA->userInvites->ChangeInvites ();

          if ($ADMINDATA->userProfile->userAuth_uID == "") {
            // Set the profile ID to the same as the authorization ID.
            $ADMINDATA->userProfile->userAuth_uID = $ADMINDATA->uID;

            // Skip past everything but the username.
            $ADMINDATA->userProfile->Description = SQL_SKIP;
            $ADMINDATA->userProfile->Gender = SQL_SKIP;
            $ADMINDATA->userProfile->Birthday = SQL_SKIP;
            $ADMINDATA->userProfile->Zipcode = SQL_SKIP;
            $ADMINDATA->userProfile->Fullname = SQL_SKIP;
            $ADMINDATA->userProfile->Alias = SQL_SKIP;

            // Add the profile record.
            $ADMINDATA->userProfile->Add();

          } else {
            // Skip past everything but the username.
            $ADMINDATA->userProfile->Description = SQL_SKIP;
            $ADMINDATA->userProfile->Gender = SQL_SKIP;
            $ADMINDATA->userProfile->Birthday = SQL_SKIP;
            $ADMINDATA->userProfile->Zipcode = SQL_SKIP;
            $ADMINDATA->userProfile->Fullname = SQL_SKIP;
            $ADMINDATA->userProfile->Alias = SQL_SKIP;
            
            // Update the profile record.
            $ADMINDATA->userProfile->Update();
          } // if
        } // if
      } // if

      // Move the error messages up to the top class variable.
      if ( ($ADMINDATA->Error == 0) AND 
           ($ADMINDATA->userProfile->Error == -1) ) {
        $ADMINDATA->Error = $ADMINDATA->userProfile->Error;
        $ADMINDATA->Message = $ADMINDATA->userProfile->Message;
      } // if

      // Move the error messages up to the top class variable.
      if ( ($ADMINDATA->Error == 0) AND 
           ($ADMINDATA->userInvites->Error == -1) ) {
        $ADMINDATA->Error = $ADMINDATA->userInvites->Error;
        $ADMINDATA->Message = $ADMINDATA->userInvites->Message;
      } // if

    break;

    case 'NEW':
    break;

    case 'EDIT':
    break;

    case 'DELETE':
      // Check if user has write access;
      if ($zLOCALUSER->userAccess->w == FALSE) {
        $zSTRINGS->Lookup ('ERROR.CANTWRITE', $zAPPLE->Context);
        $ADMINDATA->Message = $zSTRINGS->Output;
        $ADMINDATA->Error = -1;
        break;        
      } // if


      // Synchronize Data
      $ADMINDATA->Synchronize();

      // Retrieve username before deleting for deleting photo data.
      $photodefs = array ("uID" => $ADMINDATA->uID);
      $ADMINDATA->SelectByMultiple ($photodefs);
      $ADMINDATA->FetchArray ();

      $ADMINDATA->Delete();
      if (!$ADMINDATA->Error) {
        global $gDATAID;  $gDATAID = $ADMINDATA->uID;
        $zSTRINGS->Lookup ('MESSAGE.DELETE', $zAPPLE->Context);
        $ADMINDATA->Message = $zSTRINGS->Output;
        unset ($gDATAID);

        // Delete photo directory
        $phototarget = "photos/" . $ADMINDATA->Username . "/";

        if (!$zAPPLE->RemoveDirectory ($phototarget, TRUE)) {
          // Output error message if unsuccessful.
          $zSTRINGS->Lookup ('ERROR.STORED', $zAPPLE->Context);
          $ADMINDATA->Message = $zSTRINGS->Output;
          $ADMINDATA->Error = -1;
        } // if 

      } // if
      $ADMINDATA->Select("", "", $gSORT);
    break;

    default:
    break;
  } // switch

  // PART II: Load the necessary data from the database.
  switch ($gACTION) {

    case 'EDIT':
      $ADMINDATA->Synchronize();
      $ADMINDATA->Select ("uID", $ADMINDATA->uID);
      $ADMINDATA->FetchArray();
      $ADMINDATA->userInvites->CountInvites();
    break;

    case 'SAVE':
    case 'DELETE':
    default:
      if ($gCRITERIA) {
        if ($gSEARCHACCOUNTSBY == SQL_SKIP) {
          // Search all fields
          $ADMINDATA->SelectByAll($gCRITERIA, $gSORT, 1);
        } else {
          // Search a single field
          $ADMINDATA->Select($gSEARCHACCOUNTSBY, $gCRITERIA, $gSORT, 1);
        } // if

        // If only one result, jump right to edit form.
        if ( ($ADMINDATA->CountResult() == 1) AND ($gACTION == 'SEARCH') ) {
          // Fetch the data.
          $ADMINDATA->FetchArray();
          $ADMINDATA->Select ("uID", $ADMINDATA->uID);
          $gACTION = 'EDIT';
          $gCRITERIA = ''; $gPOSTDATA['CRITERIA'] = '';
        } // if
      } else {
        $ADMINDATA->Select("", "", $gSORT);
      } // if
    break;
    
  } // switch

  // PART III: Pre-parse the html for the main window. 
  
  // Buffer the main listing.
  ob_start ();  

  if ($zLOCALUSER->userAccess->a == TRUE) {
    // Choose an action
    switch ($gACTION) {
      case 'EDIT':
        global $gEDITUSERNAME; $gEDITUSERNAME = $ADMINDATA->Username;
        $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/edit.aobj", INCLUDE_SECURITY_NONE);
      break;
      case 'NEW':
        $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/new.aobj", INCLUDE_SECURITY_NONE);
      break;
      case 'SAVE':
        // Skip to the default.
      default:
        if ( ( ($ADMINDATA->Error == 0) AND 
               ($ADMINDATA->userProfile->Error == 0) ) OR
             ($gACTION == 'DELETE_ALL') OR 
             ($gACTION == 'MOVE_UP') OR
             ($gACTION == 'MOVE_DOWN') ) {

          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/list.top.aobj", INCLUDE_SECURITY_NONE);

          // Calculate scroll values.
          $gSCROLLMAX[$zAPPLE->Context] = $ADMINDATA->CountResult();

          // Adjust for a recently deleted entry.
          $zAPPLE->AdjustScroll ('admin.users.accounts', $ADMINDATA);

          // Check if any results were found.
          if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
            $zSTRINGS->Lookup ('MESSAGE.NONE', $zAPPLE->Context);
            $ADMINDATA->Message = $zSTRINGS->Output;
            $ADMINDATA->Broadcast();
          } // if

          // Loop through the list.
          $target = "_admin/users/accounts/";
          for ($listcount = 0; $listcount < $gSCROLLSTEP[$zAPPLE->Context]; $listcount++) {
           if ($ADMINDATA->FetchArray()) {
            if ($gACTION == 'SELECT_ALL') $checked = TRUE;

            $gEXTRAPOSTDATA['ACTION'] = "EDIT"; 
            $gEXTRAPOSTDATA['uID']    = $ADMINDATA->uID;
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/list.middle.aobj", INCLUDE_SECURITY_NONE);
            unset ($gEXTRAPOSTDATA);

           } else {
            break;
           } // if
          } // for

          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/list.bottom.aobj", INCLUDE_SECURITY_NONE);

        } elseif ( ($gACTION == 'SAVE') or ($gACTION == 'DELETE') ) {
          if ($guID) {
            global $gEDITUSERNAME; $gEDITUSERNAME = $ADMINDATA->Username;
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/edit.aobj", INCLUDE_SECURITY_NONE);
          } else {
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/new.aobj", INCLUDE_SECURITY_NONE);
          } // if
        } // if
      break;
    } // switch
  } else {
    // Access Denied
    $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/common/denied.aobj", INCLUDE_SECURITY_NONE);
  } // if

  // Retrieve output buffer.
  $bMAINSECTION = ob_get_clean (); 
  
  // End buffering.
  ob_end_clean (); 

  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/admin/users/accounts.afrw", INCLUDE_SECURITY_NONE);

?>
