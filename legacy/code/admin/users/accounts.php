<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: accounts.php                            CREATED: 02-11-2005 + 
  // | LOCATION: /code/admin/users/                 MODIFIED: 04-11-2007 +
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
  // | VERSION:     0.7.3                                                |
  // | DESCRIPTION: User accounts editor.                                |
  // +-------------------------------------------------------------------+

  eval( GLOBALS ); // Import all global variables  
  
  // Change to document root directory
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include BASE API classes.
  require_once ('legacy/code/include/classes/BASE/application.php'); 
  require_once ('legacy/code/include/classes/BASE/debug.php'); 
  require_once ('legacy/code/include/classes/base.php'); 
  require_once ('legacy/code/include/classes/system.php'); 
  require_once ('legacy/code/include/classes/BASE/remote.php'); 
  require_once ('legacy/code/include/classes/BASE/tags.php'); 
  require_once ('legacy/code/include/classes/BASE/xml.php'); 

  // Include Appleseed classes.
  require_once ('legacy/code/include/classes/appleseed.php'); 
  require_once ('legacy/code/include/classes/privacy.php'); 
  require_once ('legacy/code/include/classes/friends.php'); 
  require_once ('legacy/code/include/classes/messages.php'); 
  require_once ('legacy/code/include/classes/users.php'); 
  require_once ('legacy/code/include/classes/auth.php'); 
  require_once ('legacy/code/include/classes/search.php'); 

  // Create the Application class.
  $zOLDAPPLE = new cOLDAPPLESEED ();
  
  // Set Global Variables (Put this at the top of wrapper scripts)
  $zOLDAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zOLDAPPLE->Initialize("admin.users.accounts", TRUE);

  // Create local classes.
  $ADMINDATA = new cUSER ($zOLDAPPLE->Context);

  // Load security settings for the current page.
  $zLOCALUSER->Access (FALSE, FALSE, FALSE);

  // Load admin strings into cache.
  cLanguage::Load ('_system/admin.lang');

  // Check to see if user has read access for this area.
  if ($zLOCALUSER->userAccess->r == FALSE) {

    $zOLDAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
    $zOLDAPPLE->End();

  } // if

  // Create a warning message if user has no write access.
  if ($zLOCALUSER->userAccess->w == FALSE) {
    $ADMINDATA->Message = __("Write Access Denied");
    $ADMINDATA->Error = 0;
  } // if

  // Set the page title.
  $gPAGESUBTITLE = ' - Admin';

  // Set how much to step when scrolling.
  $gSCROLLSTEP[$zOLDAPPLE->Context] = 10;

  // Set the post data to move back and forth.
  $gPOSTDATA = Array ("CRITERIA"          => $gCRITERIA,
                      "SCROLLSTART"       => $gSCROLLSTART,
                      "SORT"              => $gSORT);

  // Set which switch to highlight.
  global $gSelectedSwitch;
  $gSelectedSwitch['admin_users'] = 'selected';

  // Set which tab to highlight.
  global $gSelectedTab;
  $gSelectedTab['admin_users_accounts'] = 'selected';

  // Display the select all button by default.
  $gSELECTBUTTON = 'Select All';

  // Change the select button if anything is selected.
  if ($zOLDAPPLE->ArrayIsSet ($gMASSLIST) ) $gSELECTBUTTON = 'Select None';

  // PART I: Determine appropriate action.
  switch ($gACTION) {
    case 'SELECT_ALL':
      $gSELECTBUTTON = 'Select None';
    break;

    case 'SELECT_NONE':
      $gMASSLIST = array ();
      $gSELECTBUTTON = 'Select All';
    break;

    case 'MOVE_UP':
      // Check if any items were selected.
      if (!$gMASSLIST) {
        $ADMINDATA->Message = __("None Selected");
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
        $ADMINDATA->Message = __("None Selected");
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
        $ADMINDATA->Message = __("None Selected");
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
        $phototarget = "_storage/legacy/photos/" . $ADMINDATA->Username . "/";

        $ADMINDATA->Delete();

        if (!$zOLDAPPLE->RemoveDirectory ($phototarget, TRUE)) {
          // Output error message if unsuccessful.
          $ADMINDATA->Message = __("Error Removing Directory");
          $ADMINDATA->Error = -1;
        } // if
        
        if (!$ADMINDATA->Error) $datalist[$number] = $uidvalue;
      } // if

      // If no errors, output a successful message.
      if (!$ADMINDATA->Error) {
        global $gDATALIST; $gDATALIST = implode(", ", $datalist);
        // Use proper grammer depending on how many records chosen.
        if (count ($gMASSLIST) == 1) {
          $ADMINDATA->Message = __("Record Deleted", array ('id' => $datalist[0]));
        } else {
          $ADMINDATA->Message = __("Records Deleted");
        } // if
        unset ($gDATALIST);
        unset ($gMASSLIST);
      } // if
    break;

    case 'SAVE':
      // Check if user has write access;
      if ($zLOCALUSER->userAccess->w == FALSE) {
        $ADMINDATA->Message = __("Write Access Denied");
        $ADMINDATA->Error = -1;
        break;        
      } // if

      // Look up the old username.
      $ADMINDATA->Select ("uID", $guID);
      $ADMINDATA->FetchArray();

      $oldphotodir = "_storage/legacy/photos/" . $ADMINDATA->Username;
      $newphotodir = "_storage/legacy/photos/" . $gUSERNAME;

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
          $ADMINDATA->Initialize (FALSE);

          // Update the amount of invites available.
					global $gAMOUNT;
					$ADMINDATA->userInvites->Amount = $gAMOUNT;
          $ADMINDATA->userInvites->ChangeInvites ();

          // Look up the saved message.
          $ADMINDATA->Message = __("Record Added", array ('id' => $ADMINDATA->LastIncrement));
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
          $ADMINDATA->Message = __("Record Updated", array ('id' => $ADMINDATA->uID));
          $ADMINDATA->Update();

          // Rename the user's photo directory.
          rename ($oldphotodir, $newphotodir);
          
          // Update the amount of invites available.
					global $gAMOUNT;
					$ADMINDATA->userInvites->Amount = $gAMOUNT;
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
        $ADMINDATA->Message = __("Write Access Denied");
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
        $ADMINDATA->Message = __("Record Deleted", array ('id' => $ADMINDATA->uID));

        // Delete photo directory
        $phototarget = "_storage/legacy/photos/" . $ADMINDATA->Username . "/";

        if (!$zOLDAPPLE->RemoveDirectory ($phototarget, TRUE)) {
          // Output error message if unsuccessful.
          $ADMINDATA->Message = __("Error While Storing");
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
        $ADMINDATA->SelectByAll($gCRITERIA, $gSORT, 1);

        // If only one result, jump right to edit form.
        if ( ($ADMINDATA->CountResult() == 1) AND ($gACTION == 'SEARCH') ) {
          // Fetch the data.
          $ADMINDATA->FetchArray();
          $ADMINDATA->Select ("uID", $ADMINDATA->uID);
          $gACTION = 'EDIT';
          $gCRITERIA = ''; $gPOSTDATA['CRITERIA'] = '';
        } // if
      } else {
        $ADMINDATA->Select(NULL, NULL, $gSORT);
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
        $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/edit.aobj", INCLUDE_SECURITY_NONE);
      break;
      case 'NEW':
        $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/new.aobj", INCLUDE_SECURITY_NONE);
      break;
      case 'SAVE':
        // Skip to the default.
      default:
        if ( ( ($ADMINDATA->Error == 0) AND 
               ($ADMINDATA->userProfile->Error == 0) ) OR
             ($gACTION == 'DELETE_ALL') OR 
             ($gACTION == 'MOVE_UP') OR
             ($gACTION == 'MOVE_DOWN') ) {

          $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/list.top.aobj", INCLUDE_SECURITY_NONE);

          // Calculate scroll values.
          $gSCROLLMAX[$zOLDAPPLE->Context] = $ADMINDATA->CountResult();

          // Adjust for a recently deleted entry.
          $zOLDAPPLE->AdjustScroll ('admin.users.accounts', $ADMINDATA);

          // Check if any results were found.
          if ($gSCROLLMAX[$zOLDAPPLE->Context] == 0) {
            $ADMINDATA->Message = __("No Results Found");
            $ADMINDATA->Broadcast();
          } // if

          // Loop through the list.
          $target = "_admin/users/accounts/";
          for ($listcount = 0; $listcount < $gSCROLLSTEP[$zOLDAPPLE->Context]; $listcount++) {
           if ($ADMINDATA->FetchArray()) {
            if ($gACTION == 'SELECT_ALL') $checked = TRUE;

            global $gEXTRAPOSTDATA;
            $gEXTRAPOSTDATA['ACTION'] = "EDIT"; 
            $gEXTRAPOSTDATA['uID']    = $ADMINDATA->uID;
            $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/list.middle.aobj", INCLUDE_SECURITY_NONE);
            unset ($gEXTRAPOSTDATA);

           } else {
            break;
           } // if
          } // for

          $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/list.bottom.aobj", INCLUDE_SECURITY_NONE);

        } elseif ( ($gACTION == 'SAVE') or ($gACTION == 'DELETE') ) {
          if ($guID) {
            global $gEDITUSERNAME; $gEDITUSERNAME = $ADMINDATA->Username;
            $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/edit.aobj", INCLUDE_SECURITY_NONE);
          } else {
            $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/users/accounts/new.aobj", INCLUDE_SECURITY_NONE);
          } // if
        } // if
      break;
    } // switch
  } else {
    // Access Denied
    $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/common/denied.aobj", INCLUDE_SECURITY_NONE);
  } // if

  // Retrieve output buffer.
  $bMAINSECTION = ob_get_clean (); 
  
  // End buffering.
  ob_end_clean (); 

  // Include the outline frame.
  $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/frames/admin/users/accounts.afrw", INCLUDE_SECURITY_NONE);

  // End the application.
  $zOLDAPPLE->End ();

?>
