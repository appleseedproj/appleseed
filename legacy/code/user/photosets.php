<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: photosets.php                           CREATED: 07-25-2005 + 
  // | LOCATION: /code/user/                        MODIFIED: 07-08-2007 +
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
  // | DESCRIPTION: Photo sets view and management.                      |
  // +-------------------------------------------------------------------+

  // If we're viewing a specific photoset or photo, return to those scripts.
  if ($gPROFILESUBACTION) {
    // Check if we're viewing based on a tag.
    if ($zTAGS->DetectTags()) {
      $zOLDAPPLE->IncludeFile ("legacy/code/user/tags/photos.php", INCLUDE_SECURITY_NONE);
      $zOLDAPPLE->End();
    } // if
    
    // Check if we're requesting a specific photo or a full photoset.
    if (strstr ($gPROFILESUBACTION, '/') ) {
      $zOLDAPPLE->IncludeFile ("legacy/code/user/photo.php", INCLUDE_SECURITY_NONE);
    } else {
      $zOLDAPPLE->IncludeFile ("legacy/code/user/photos.php", INCLUDE_SECURITY_NONE);
    } // if
    $zOLDAPPLE->End();
  } // if
  
  // Include necessary files
  require_once ('legacy/code/include/classes/photo.php'); 
  require_once ('legacy/code/include/classes/comments.php'); 

  // Create class for the data we'll be modifying/viewing.
  global $gVIEWDATA;

  global $gTAGSLABEL;

  $gVIEWDATA = new cPHOTOSETS ($zOLDAPPLE->Context);

  // Set the proper context.
  $zOLDAPPLE->Context = "user.photosets";
           
  // Check if user is admin or is viewing their own page.
  if ($gFOCUSUSERID != $zAUTHUSER->uID) {
    $listlocation = "listing/";
    if ($zLOCALUSER->userAccess->a == TRUE) $listlocation = "editor/";
  } else {
    $listlocation = "editor/";
  } // if

  // Create a warning message if user has no write access.
  if ( ($gFOCUSUSERID != $zAUTHUSER->uID) and 
       ($zLOCALUSER->userAccess->a == TRUE) and 
       ($zLOCALUSER->userAccess->w == FALSE) ) {
    $gVIEWDATA->Message = __("Write Access Denied");
    $gVIEWDATA->Error = 0;
  } // if

  // Set how much to step when scrolling.
  $gSCROLLSTEP[$zOLDAPPLE->Context] = 5;

  $gSORT = "sID";

  // Set the post data to move back and forth.
  $gPOSTDATA = Array ("SCROLLSTART"     => $gSCROLLSTART[$zOLDAPPLE->Context],
                      "SORT"            => $gSORT);

  // Set which tab to highlight.
  $gUSERPHOTOSTAB = '';
  $this->SetTag ('USERPHOTOSTAB', $gUSERPHOTOSTAB);

  // Display the select all button by default.
  $gSELECTBUTTON = 'Select All';

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
        $gVIEWDATA->Message = __("None Selected");
        $gVIEWDATA->Error = -1;
        break;
      } // if

      // Loop through the list
      foreach ($gMASSLIST as $number => $tidvalue) {
        $gVIEWDATA->Select ("tID", $tidvalue);
        $gVIEWDATA->FetchArray ();

        $movecriteria = array ("userAuth_uID" => $zFOCUSUSER->uID);
        $gVIEWDATA->MoveWithin (UP, $gVIEWDATA->sID, "sID", $movecriteria);

        // Move in the selected list.
        if (!$gVIEWDATA->Error) $gMASSLIST[$number] = $gVIEWDATA->tID;
      } // if
    break;

    case 'MOVE_DOWN':
      // Check if any items were selected.
      if (!$gMASSLIST) {
        $gVIEWDATA->Message = __("None Selected");
        $gVIEWDATA->Error = -1;
        break;
      } // if

      // Reverse the array when moving specific elements downwards;
      $gMASSLIST = array_reverse ($gMASSLIST);

      // Loop through the list
      foreach ($gMASSLIST as $number => $tidvalue) {
        $gVIEWDATA->Select ("tID", $tidvalue);
        $gVIEWDATA->FetchArray ();

        // Make sure we haven't hit the highest sID.
        $max = $gVIEWDATA->Max ("sID", "userAuth_uID", $zFOCUSUSER->uID);
        if ($gVIEWDATA->sID < $max) {
          $movecriteria = array ("userAuth_uID" => $zFOCUSUSER->uID);
          $gVIEWDATA->MoveWithin (DOWN, $gVIEWDATA->sID, "sID", $movecriteria);
        } // if

        // Move in the selected list.
        if (!$gVIEWDATA->Error) $gMASSLIST[$number] = $gVIEWDATA->tID;
      } // if
    break;

    case 'DELETE_ALL':
      // Check if any items were selected.
      if (!$gMASSLIST) {
        $gVIEWDATA->Message = __("None Selected");
        $gVIEWDATA->Error = -1;
        break;
      } // if

      // Loop through the list
      foreach ($gMASSLIST as $number => $tidvalue) {
        $gVIEWDATA->tID = $tidvalue;

        $gVIEWDATA->Select ("tID", $gVIEWDATA->tID);
        $gVIEWDATA->FetchArray ();

        // Remove the directory.
        $photosetdir = "photos/" . $zFOCUSUSER->Username . "/sets/" . $gVIEWDATA->Directory;

        // Begin transaction.
        $gVIEWDATA->Begin ();

        // Delete record.
        $gVIEWDATA->Delete();

        // Set error message if unable to delete photo set directory.
        if (!$zOLDAPPLE->RemoveDirectory ($photosetdir) ) {
          $gVIEWDATA->Message = __("Error Modifying Photoset Directory");
          $gVIEWDATA->Error = -1;;
        } // if

        // If no errors, commit changes, otherwise rollback.
        if (!$gVIEWDATA->Error) {
          $datalist[$number] = $tidvalue;
          $gVIEWDATA->Commit ();
        } else {
          $gVIEWDATA->Rollback ();
        } // if

      } // if

      // If no errors, output a successful message.
      if (!$gVIEWDATA->Error) {

        // Use proper grammer depending on how many records chosen.
        if (count ($gMASSLIST) == 1) {
          // Look up the name of the deleted photoset.
          $gVIEWDATA->Select("tID", $datalist[0]);
          $gVIEWDATA->FetchArray ();

          $gVIEWDATA->Message = __("Record Deleted", array ('setname' => $gVIEWDATA->Name));
        } else {
          $gVIEWDATA->Message = __("Records Deleted");
        } // if
        unset ($gMASSLIST);
      } // if

      // Adjust the Sort ID (sID) listing.
      $gVIEWDATA->AdjustSort ("sID", "userAuth_uID", $zFOCUSUSER->uID);

    break;

    case 'SAVE':
      // Check if user has write access;
      if ( ($gFOCUSUSERID != $zAUTHUSER->uID) and 
           ($zLOCALUSER->userAccess->a == TRUE) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $gVIEWDATA->Message = __("Write Access Denied");
        $gVIEWDATA->Error = -1;
        break;        
      } // if

      // Synchronize Data
      $gVIEWDATA->Synchronize();

      // Check for special directories and rename.
      if ($gVIEWDATA->Directory == 'icons') $gVIEWDATA->Directory = '_icons';

      // Check whether we're adding or updating a record.
      if ($gVIEWDATA->tID == "") {
        // ADD

        $gVIEWDATA->tID = 1;
        $gVIEWDATA->sID = $gVIEWDATA->Max ("sID", "userAuth_uID", $zFOCUSUSER->uID) + 1;
        $gVIEWDATA->userAuth_uID = $zFOCUSUSER->uID;
        $gVIEWDATA->Sanity();

        // Create the photosets directory.
        $photosetdir = "photos/" . $zFOCUSUSER->Username . "/sets/" . $gVIEWDATA->Directory . "/";

        // Begin Transaction.
        $gVIEWDATA->Begin ();

        // If no errors, output a successful message.
        if (!$gVIEWDATA->Error) {

          // Attempt to create the photoset directory. 
          if (!$zOLDAPPLE->CreateDirectory ($photosetdir) ) {
            $gVIEWDATA->Message = __("Error Modifying Photoset Directory");
            $gVIEWDATA->Error = -1;;
            $gACTION = "NEW";
            $gVIEWDATA->Rollback ();
          } else {
            // Add the record to the database.
            $gVIEWDATA->Add();
            $gVIEWDATA->Message = __("Record Added", array ('setname' => $gVIEWDATA->Name));

            // Retrieve the last inserted ID.
            $gVIEWDATA->tID = $gVIEWDATA->AutoIncremented ();

            // Update the privacy settings.
            $gVIEWDATA->photoPrivacy->SaveSettings ($gPRIVACY, "photoSets_tID", $gVIEWDATA->tID);

            $gADDEDNEW = TRUE;
            $gVIEWDATA->Commit ();
          } // if
        } else {
          $gACTION = "NEW";
          $gVIEWDATA->Rollback ();
        } // if

      } else {
        // UPDATE

        // Lookup old Directory name, to rename it.
        $OLDDATA = new cPHOTOSETS ();
        $OLDDATA->Select ("tID", $gVIEWDATA->tID);
        $OLDDATA->FetchArray ();

        $photosetdir = "photos/" . $zFOCUSUSER->Username . "/sets/" . $gVIEWDATA->Directory;
        $olddir = "photos/" . $zFOCUSUSER->Username . "/sets/" . $OLDDATA->Directory;

        // Begin Transaction.
        $gVIEWDATA->Begin ();

        $gVIEWDATA->userAuth_uID = $zFOCUSUSER->uID;
        $gVIEWDATA->sID = $OLDDATA->sID;
        $gVIEWDATA->Sanity();

        // Update the privacy settings.
        $gVIEWDATA->photoPrivacy->SaveSettings ($gPRIVACY, "photoSets_tID", $gVIEWDATA->tID);

        if ( (!$gVIEWDATA->Error) and (!$gVIEWDATA->photoPrivacy->Error) ) {
          $gVIEWDATA->Message = __("Record Updated", array ("setname" => $gVIEWDATA->Name));
          $gVIEWDATA->Update();
          $gACTION = "";

          // Rename the old directory.
          if (!rename ($olddir, $photosetdir) ) {
            $gVIEWDATA->Message = __("Error Modifying Photoset Directory");
            $gVIEWDATA->Error = -1;;
            $gACTION = "EDIT";
            $gVIEWDATA->Rollback ();
          } else {
            $gVIEWDATA->Commit ();
          } // if

        } else {
          $gACTION = "EDIT";
          $gVIEWDATA->Rollback ();
        } // if

        unset ($OLDDATA);

      } // if
    break;

    case 'NEW':
    case 'EDIT':
    break;

    case 'DELETE':
      // Check if user has write access;
      if ( ($gFOCUSUSERID != $zAUTHUSER->uID) and 
           ($zLOCALUSER->userAccess->a == TRUE) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $gVIEWDATA->Message = __("Write Access Denied");
        $gVIEWDATA->Error = -1;
        break;        
      } // if

      // Synchronize Data
      $gVIEWDATA->Synchronize();
      $gVIEWDATA->Select ("tID", $gVIEWDATA->tID);
      $gVIEWDATA->FetchArray ();

      $photosetdir = "photos/" . $zFOCUSUSER->Username . "/sets/" . $gVIEWDATA->Directory;

      if (!$zOLDAPPLE->RemoveDirectory ($photosetdir) ) {
        $gVIEWDATA->Message = __("Error Modifying Photoset Directory");
        $gVIEWDATA->Error = -1;;
        $gVIEWDATA->Rollback ();
      } // if

      if (!$gVIEWDATA->Error) {
        $gVIEWDATA->Message = __("Record Deleted", array ('setname' => $gVIEWDATA->Name));
        $gVIEWDATA->Delete();
      } // if

      // Adjust the Sort ID (sID) listing.
      $gVIEWDATA->AdjustSort ("sID", "userAuth_uID", $zFOCUSUSER->uID);

      // Load list for viewing.
      $gVIEWDATA->Select("userAuth_uID", $zFOCUSUSER->uID, $gSORT);

    break;

    default:
    break;
  } // switch

  // PART II: Load the necessary data from the database.
  switch ($gACTION) {

    case 'EDIT':
      $gVIEWDATA->Synchronize();
      $gVIEWDATA->Select ("tID", $gtID);
      $gVIEWDATA->FetchArray();
    case 'NEW':
      global $bPRIVACYOPTIONS;
      $bPRIVACYOPTIONS = $gVIEWDATA->photoPrivacy->BufferOptions ("photoSets_tID", $gVIEWDATA->tID);
    break;

    case 'SAVE':
    case 'DELETE':
    default:
      $gVIEWDATA->Select("userAuth_uID", $zFOCUSUSER->uID, $gSORT);
    break;
    
  } // switch

  // Change the select button if anything is eelected.
  if ($zOLDAPPLE->ArrayIsSet ($gMASSLIST) ) $gSELECTBUTTON = 'Select None';

  // PART III: Pre-parse the html for the main window. 
  
  // Buffer the main listing.
  ob_start ();  

  // Choose an action
  switch ($gACTION) {
    case 'EDIT':
      $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photosets/edit.aobj", INCLUDE_SECURITY_NONE);
    break;
    case 'NEW':
      $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photosets/new.aobj", INCLUDE_SECURITY_NONE);
    break;
    case 'DELETE':
    case 'SAVE':
      // Skip to the default.
    default:
      if ( ($gVIEWDATA->Error == 0) or 
           ($gACTION == 'MOVE_DOWN') or
           ($gACTION == '') or
           ($gACTION == 'MOVE_UP') or
           ($gACTION == 'DELETE_ALL') ) {

        $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photosets/" . $listlocation . "list.top.aobj", INCLUDE_SECURITY_NONE);

        // Calculate scroll values.
        $gSCROLLMAX[$zOLDAPPLE->Context] = $gVIEWDATA->CountResult();

        // If we're uploading, jump to the last page.
        if ($gADDEDNEW) {

          // Calculate the scroll values.
          $zHTML->CalcScroll ($zOLDAPPLE->Context);
           
          // Jump to the last page.
          if ($gMAXPAGES > $gCURRENTPAGE) $gSCROLLSTART[$zOLDAPPLE->Context] = $gMAXPAGES * $gSCROLLSTEP[$zOLDAPPLE->Context];

        } // if

        // Adjust for a recently deleted entry.
        $zOLDAPPLE->AdjustScroll ($zOLDAPPLE->Context, $gVIEWDATA);

        global $gTARGET, $gVIEWTARGET;
        global $gEXTRADATA, $gCHECKED;

        global $gLISTCOUNT;

        // Loop through the list.
        for ($gLISTCOUNT = 0; $gLISTCOUNT < $gSCROLLSTEP[$zOLDAPPLE->Context]; $gLISTCOUNT++) {
         if ($gVIEWDATA->FetchArray()) {

          // Retrieve the privacy settings for this photoset.
          $gPRIVACYSETTING = $gVIEWDATA->photoPrivacy->Determine ("zFOCUSUSER", "zAUTHUSER", "photoSets_tID", $gVIEWDATA->tID);

          // Adjust for a hidden entry.
          if ( $zOLDAPPLE->AdjustHiddenScroll ($gPRIVACYSETTING, $zOLDAPPLE->Context) ) continue;

          $gCHECKED = FALSE;

          // Target for viewing a photoset.
          $gVIEWTARGET = "/profile/" . $zFOCUSUSER->Username . "/photos/" . $gVIEWDATA->Directory . "/";

          // Target for editin or scrolling through photosets.
          $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/photos/";

          $gEXTRADATA = array ("tID"   => $gVIEWDATA->tID,
                               "ACTION" => "EDIT");
          if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;
          global $gPHOTOLOCATION;
          // Look up first photo filename in list.
          $firstcriteria = array ("userAuth_uID"  => $zFOCUSUSER->uID,
                                  "photoSets_tID" => $gVIEWDATA->tID);
          $gVIEWDATA->photoInfo->SelectByMultiple ($firstcriteria, $gSORT);
          $gVIEWDATA->photoInfo->FetchArray ();

          global $gTHUMBX, $gTHUMBY;
          $gTHUMBX = $gVIEWDATA->photoInfo->ThumbWidth; $gTHUMBY = $gVIEWDATA->photoInfo->ThumbHeight;

          $gPHOTOLOCATION = "photos/" . $zFOCUSUSER->Username . "/sets/" . $gVIEWDATA->Directory . "/_sm." . $gVIEWDATA->photoInfo->Filename;

          if (!file_exists ($gPHOTOLOCATION) ) {
            $gTHUMBX = $gPHOTOTHUMBX; $gTHUMBY = $gPHOTOTHUMBY;
            $gPHOTOLOCATION = "/$gTHEMELOCATION/images/error/noimage.png";
          } // if

          global $gPROTECTED; $gPROTECTED = "";
          global $gPHOTOCOUNT; $gPHOTOCOUNT = $gVIEWDATA->photoInfo->CountResult ();

          // Show the appropriate icon if photoset is not public.
          switch ($gPRIVACYSETTING) {
            case PRIVACY_SCREEN:
              $gPROTECTED = $zOLDAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/screen.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
            break;

            case PRIVACY_RESTRICT:
              $gPROTECTED = $zOLDAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/restrict.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
            break;

            case PRIVACY_BLOCK:
              $gPROTECTED = $zOLDAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/block.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
            break;

            case PRIVACY_ALLOW:
            case PRIVACY_HIDE:
            default:
            break;
          } // switch

          $gEXTRAPOSTDATA['ACTION'] = "EDIT"; 
          $gEXTRAPOSTDATA['tID']    = $gVIEWDATA->photoInfo->tID;

          // Check if photoset is blocked
          if ( ($gPRIVACYSETTING == PRIVACY_BLOCK) and
               ($zLOCALUSER->userAccess->r == FALSE) and
               ($zLOCALUSER->uID != $zFOCUSUSER->uID)  ) {

            $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photosets/" . $listlocation . "list.middle.block.aobj", INCLUDE_SECURITY_NONE);
          } else {
            $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photosets/" . $listlocation . "list.middle.aobj", INCLUDE_SECURITY_NONE);
          } // if

          unset ($gEXTRAPOSTDATA['ACTION']); 

          $gPOSTDATA['SCROLLSTART'][$zOLDAPPLE->Context] = $gSCROLLSTART[$zOLDAPPLE->Context];

          unset ($gPROTECTED);
          
         } else {
          break;
         } // if
        } // for

        // Check if any results were found.
        if ($gSCROLLMAX[$zOLDAPPLE->Context] == 0) {
          $gVIEWDATA->Message = __("None Selected");
          $gVIEWDATA->Broadcast();
        } // if

        unset ($gEXTRADATA);

        $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photosets/" . $listlocation. "list.bottom.aobj", INCLUDE_SECURITY_NONE);

        // $zHTML->Scroll ($gTARGET, 'member', $zOLDAPPLE->Context, SCROLL_NOFIRST);

      } elseif ( ($gACTION == 'SAVE') or ($gACTION == 'DELETE') ) {
        if ($gtID) {
          $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photosets/edit.aobj", INCLUDE_SECURITY_NONE);
        } else {
          $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photosets/new.aobj", INCLUDE_SECURITY_NONE);
        } // if
      } // if
    break;
  } // switch

  global $bMAINSECTION;
  // Retrieve output buffer.
  $bMAINSECTION = ob_get_clean (); 
  
  // End buffering.
  ob_end_clean (); 

  // Include the outline frame.
  $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/frames/users/photosets.afrw", INCLUDE_SECURITY_NONE);

?>
