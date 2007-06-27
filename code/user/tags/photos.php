<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: photos.php                              CREATED: 07-07-2007 + 
  // | LOCATION: /code/user/tags/                   MODIFIED: 07-07-2007 +
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
  // | VERSION:     0.7.2                                                |
  // | DESCRIPTION: Displays a listing of photos with a specific tag.    |
  // +-------------------------------------------------------------------+

  // Include necessary files
  require_once ('code/include/classes/photo.php'); 
  require_once ('code/include/classes/comments.php'); 
  
  $tagcriteria = $zTAGS->DetectTags();

  if ($tagcriteria) {
    global $gVIEWDATA;
    $gVIEWDATA = new cPHOTOSETS ();
    $zIMAGE = new cIMAGE();
  } // if

  global $gIMAGEHINT;

  // Create the image manipulation class.
  global $zIMAGE;
  $zIMAGE = new cIMAGE ($zAPPLE->Context);
           
  // Create the comment manipulation class.
  global $zCOMMENTS;
  $zCOMMENTS = new cCOMMENTINFORMATION ("USER.COMMENTS");

  // Initialize the commenting subsystem.
  $zCOMMENTS->Initialize ();
           
  global $gPHOTOLISTING, $gPHOTOLISTINGEDITOR;

  // If the photos view isn't set, default to 'four'.
  if (!$gPHOTOLISTING) $gPHOTOLISTING = VIEW_COMPACT;

  // Depracate the photolisting view.
  if ($gPHOTOLISTINGEDITOR != "") $gPHOTOLISTING = $gPHOTOLISTINGEDITOR;
  $gPHOTOLISTINGEDITOR = $gPHOTOLISTING;

  global $gPHOTOLISTTYPE; $gPHOTOLISTTYPE = "PHOTOLISTING";

  global $gTAGSLABEL;

  // Check if user is admin or is viewing their own page.
  if ($gFOCUSUSERID != $zAUTHUSER->uID) {
    if ($zLOCALUSER->userAccess->a == TRUE) {
      $gPHOTOLISTTYPE = "PHOTOLISTINGEDITOR";
    }
  } else {
    $gPHOTOLISTTYPE = "PHOTOLISTINGEDITOR";
  } // if

  // Set how much to step when scrolling.
  switch ($gPHOTOLISTING) {
    case VIEW_EDITOR:
      // Set the editor view.
      $viewlocation = "editor/";
      $gSCROLLSTEP[$zAPPLE->Context] = 20;
    break;

    case VIEW_DEFAULT:
      $viewlocation = "compact/";
      $gSCROLLSTEP[$zAPPLE->Context] = 20;
    break;

    case VIEW_ALL:
      $viewlocation = "all/";
      $gSCROLLSTEP[$zAPPLE->Context] = 10000;
    break;

    case VIEW_STANDARD:
      $viewlocation = "standard/";
      $gSCROLLSTEP[$zAPPLE->Context] = 20;
    break;

    case VIEW_COMPACT:
      $viewlocation = "compact/";
      $gSCROLLSTEP[$zAPPLE->Context] = 10;
    break;

    case VIEW_STANDARD:
      $viewlocation = "standard/";
      $gSCROLLSTEP[$zAPPLE->Context] = 20;
    break;

    case VIEW_FULL:
      $viewlocation = "full/";
      $gSCROLLSTEP[$zAPPLE->Context] = 40;
    break;
  } // switch

  // Create a warning message if user has no write access.
  if ( ($gFOCUSUSERID != $zAUTHUSER->uID) and 
       ($zLOCALUSER->userAccess->a == TRUE) and 
       ($zLOCALUSER->userAccess->w == FALSE) ) {
    $zSTRINGS->Lookup ('ERROR.CANTWRITE', $zAPPLE->Context);
    $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
    $gVIEWDATA->photoInfo->Error = 0;
  } // if

  // Set how much to step when scrolling.
  if ( ($gPHOTOLISTING == VIEW_EDITOR) or 
       ($gPHOTOLISTING == VIEW_DEFAULT) ) $gSCROLLSTEP[$zAPPLE->Context] = 20;
  if ($gPHOTOLISTING == VIEW_COMPACT) $gSCROLLSTEP[$zAPPLE->Context] = 40;
  if ($gPHOTOLISTING == VIEW_ALL) $gSCROLLSTEP[$zAPPLE->Context] = 10000;
  if ($gPHOTOLISTING == VIEW_STANDARD) $gSCROLLSTEP[$zAPPLE->Context] = 20;
  if ($gPHOTOLISTING == VIEW_FULL) $gSCROLLSTEP[$zAPPLE->Context] = 10;

  // Set the post data to move back and forth.
  $gPOSTDATA = Array ("SCROLLSTART"       => array ($zAPPLE->Context => $gSCROLLSTART[$zAPPLE->Context]),
                      "SORT"              => $gSORT,
                      "PHOTOLISTING"      => $gPHOTOLISTING,
                      "COMMENTVIEW"       => $gCOMMENTVIEW);
  // Set which tab to highlight.
  $gUSERPHOTOSTAB = '';
  $this->SetTag ('USERPHOTOSTAB', $gUSERPHOTOSTAB);
  
  // Display the select all button by default.
  $gSELECTBUTTON = 'select_all';

  global $gCOMMENTSELECTBUTTON;
  // Display the select all button by default.
  $gCOMMENTSELECTBUTTON = 'select_all';
  
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
        $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
        $gVIEWDATA->photoInfo->Error = -1;
        break;
      } // if

      // Loop through the list
      foreach ($gMASSLIST as $number => $tidvalue) {
        $gVIEWDATA->photoInfo->Select ("tID", $tidvalue);
        $gVIEWDATA->photoInfo->FetchArray ();

        $movecriteria = array ("photoSets_tID" => $gVIEWDATA->tID);
        $gVIEWDATA->photoInfo->MoveWithin (UP, $gVIEWDATA->photoInfo->sID, "sID", $movecriteria);

        // Move in the selected list.
        if (!$gVIEWDATA->photoInfo->Error) $gMASSLIST[$number] = $gVIEWDATA->photoInfo->tID;
      } // if
    break;

    case 'MOVE_DOWN':
      // Check if any items were selected.
      if (!$gMASSLIST) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED', $zAPPLE->Context);
        $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
        $gVIEWDATA->photoInfo->Error = -1;
        break;
      } // if

      // Reverse the array when moving specific elements downwards;
      $gMASSLIST = array_reverse ($gMASSLIST);

      // Loop through the list
      foreach ($gMASSLIST as $number => $tidvalue) {
        $gVIEWDATA->photoInfo->Select ("tID", $tidvalue);
        $gVIEWDATA->photoInfo->FetchArray ();

        // Make sure we haven't hit the highest sID.
        $max = $gVIEWDATA->photoInfo->Max ("sID", "photoSets_tID", $gVIEWDATA->tID);
        if ($gVIEWDATA->photoInfo->sID < $max) {
          $movecriteria = array ("photoSets_tID" => $gVIEWDATA->tID);
          $gVIEWDATA->photoInfo->MoveWithin (DOWN, $gVIEWDATA->photoInfo->sID, "sID", $movecriteria);
        } // if

        // Move in the selected list.
        if (!$gVIEWDATA->photoInfo->Error) $gMASSLIST[$number] = $gVIEWDATA->photoInfo->tID;
      } // if
    break;

    case 'DELETE_ALL':
      // Check if any items were selected.
      if (!$gMASSLIST) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED', $zAPPLE->Context);
        $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
        $gVIEWDATA->photoInfo->Error = -1;
        break;
      } // if

      // Loop through the list
      foreach ($gMASSLIST as $number => $tidvalue) {
        $gVIEWDATA->photoInfo->tID = $tidvalue;

        $gVIEWDATA->photoInfo->Select ("tID", $gVIEWDATA->photoInfo->tID);
        $gVIEWDATA->photoInfo->FetchArray ();

        // Remove the image files.
        $photosetdir = "photos/" . $zFOCUSUSER->Username . "/sets/" . $gVIEWDATA->Directory;

        $ogfile = $photosetdir . "/_og." . $gVIEWDATA->photoInfo->Filename;
        $fnfile = $photosetdir . "/" . $gVIEWDATA->photoInfo->Filename;
        $smfile = $photosetdir . "/_sm." . $gVIEWDATA->photoInfo->Filename;
        $mdfile = $photosetdir . "/_md." . $gVIEWDATA->photoInfo->Filename;
        $lgfile = $photosetdir . "/_lg." . $gVIEWDATA->photoInfo->Filename;

        // Begin transaction.
        $gVIEWDATA->photoInfo->Begin ();

        // Delete record.
        $gVIEWDATA->photoInfo->Delete();

        // Set error message if unable to delete photo set directory.
        if ( (!unlink ($ogfile) ) or 
             (!unlink ($fnfile) ) or 
             (!unlink ($smfile) ) or 
             (!unlink ($mdfile) ) or 
             (!unlink ($lgfile) ) ) {
          global $gPHOTOFILENAME; $gPHOTOFILENAME = $gVIEWDATA->photoInfo->Filename;
          $zSTRINGS->Lookup ('ERROR.FILE', $zAPPLE->Context);
          $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
          $gVIEWDATA->photoInfo->Error = -1;;
        } // if

        // If no errors, commit changes, otherwise rollback.
        if (!$gVIEWDATA->photoInfo->Error) {
          $datalist[$number] = $tidvalue;
          $gVIEWDATA->photoInfo->Commit ();
        } else {
          $gVIEWDATA->photoInfo->Rollback ();
        } // if

      } // if

      // If no errors, output a successful message.
      if (!$gVIEWDATA->photoInfo->Error) {

        // Use proper grammer depending on how many records chosen.
        if (count ($gMASSLIST) == 1) {
          // Look up the name of the deleted photoset.
          $gVIEWDATA->photoInfo->Select("tID", $datalist[0]);
          $gVIEWDATA->photoInfo->FetchArray ();

          global $gPHOTOFILENAME; $gPHOTOFILENAME = $gVIEWDATA->photoInfo->Filename;

          $zSTRINGS->Lookup ('MESSAGE.DELETE', $zAPPLE->Context);
          unset ($gPHOTOFILENAME);
        } else {
          $zSTRINGS->Lookup ('MESSAGE.DELETEALL', $zAPPLE->Context);
        } // if
        $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
        unset ($gMASSLIST);

      } // if

      // Adjust the Sort ID (sID) listing.
      $gVIEWDATA->photoInfo->AdjustSort ("sID", "photoSets_tID", $gVIEWDATA->tID);

    break;

    case 'UPLOAD':
      // Check if user has write access;
      if ( ($gFOCUSUSERID != $zAUTHUSER->uID) and 
           ($zLOCALUSER->userAccess->a == TRUE) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $zSTRINGS->Lookup ('ERROR.CANTWRITE', $zAPPLE->Context);
        $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
        $gVIEWDATA->photoInfo->Error = -1;
        break;        
      } // if

      $uploadfile = $_FILES['gNEWPHOTO']['tmp_name'];
      $uploaderror = $_FILES['gNEWPHOTO']['error'];
      // Validate the uploaded file.
      $zIMAGE->Validate ($uploadfile, $uploaderror, $gMAXPHOTOX, $gMAXPHOTOY);

      $photosetdir = "photos/" . $zFOCUSUSER->Username . "/sets/" . $gVIEWDATA->Directory . "/";

      // If photo set directory doesn't exist, create it.
      if (!is_dir ($photosetdir))  $zAPPLE->CreateDirectory ($photosetdir);

      // Strip all spaces out of the filename.
      $filename = str_replace(" ", "", $_FILES['gNEWPHOTO']['name']);

      // Check for special filenames and rename.
      if ($filename == 'profile.jpg') $filename = '_profile.jpg';

      $ogfile = $photosetdir . "/_og." . $filename;
      $fnfile = $photosetdir . "/" . $filename;
      $smfile = $photosetdir . "/_sm." . $filename;
      $mdfile = $photosetdir . "/_md." . $filename;
      $lgfile = $photosetdir . "/_lg." . $filename;

      // Retrieve the image attributes.
      $zIMAGE->Attributes ($_FILES['gNEWPHOTO']['tmp_name']);

      // Convert and save the file.
      if ($zIMAGE->Error != -1) {
        // Set values.
        $gVIEWDATA->photoInfo->Filename = $filename;
        $gVIEWDATA->photoInfo->Width = $zIMAGE->Width;
        $gVIEWDATA->photoInfo->Height = $zIMAGE->Height;
        $gVIEWDATA->photoInfo->userAuth_uID = $zFOCUSUSER->uID;
        $gVIEWDATA->photoInfo->photoSets_tID = $gVIEWDATA->tID;
        $gVIEWDATA->photoInfo->sID = $gVIEWDATA->photoInfo->Max ("sID", "photoSets_tID", $gVIEWDATA->tID) + 1;
        $gVIEWDATA->photoInfo->Tags = '';
        $gVIEWDATA->photoInfo->Description = '';
            
        // Check if image with filename already exists.
        $PHOTOCHECK = new cPHOTOINFORMATION;
        $photocriteria = array ("userAuth_uID"  => $zFOCUSUSER->uID,
                                "photoSets_tID" => $gVIEWDATA->tID,
                                "Filename"      => $gVIEWDATA->photoInfo->Filename);
        $PHOTOCHECK->SelectByMultiple ($photocriteria, "sID");

        if ($PHOTOCHECK->CountResult () == 0) {
          // Create the final photo and save it.
          $zIMAGE->Convert ($_FILES['gNEWPHOTO']['tmp_name']);
          if ( ($zIMAGE->Width > PHOTO_FINAL_WIDTH) or ($zIMAGE->Height > PHOTO_FINAL_HEIGHT) ) 
            $zIMAGE->Resize (PHOTO_FINAL_WIDTH, PHOTO_FINAL_HEIGHT, TRUE, TRUE);
          $zIMAGE->Save ($fnfile, $zIMAGE->Type);
          $zIMAGE->Destroy();
          
          // Create a small thumbnail and save it.
          $zIMAGE->Convert ($_FILES['gNEWPHOTO']['tmp_name']);
          $zIMAGE->Attributes ($_FILES['gNEWPHOTO']['tmp_name']);
          $zIMAGE->ResizeAndCrop (PHOTO_THUMB_SMALL_WIDTH, PHOTO_THUMB_SMALL_HEIGHT);
          $zIMAGE->Save ($smfile, $zIMAGE->Type);
          $zIMAGE->Destroy();
          
          // Create a medium thumbnail and save it.
          $zIMAGE->Convert ($_FILES['gNEWPHOTO']['tmp_name']);
          $zIMAGE->Attributes ($_FILES['gNEWPHOTO']['tmp_name']);
          $zIMAGE->ResizeAndCrop (PHOTO_THUMB_MEDIUM_WIDTH, PHOTO_THUMB_MEDIUM_HEIGHT);
          $zIMAGE->Save ($mdfile, $zIMAGE->Type);
          $zIMAGE->Destroy();
          
          // Create a large thumbnail and save it.
          $zIMAGE->Convert ($_FILES['gNEWPHOTO']['tmp_name']);
          $zIMAGE->Attributes ($_FILES['gNEWPHOTO']['tmp_name']);
          $zIMAGE->ResizeAndCrop (PHOTO_THUMB_LARGE_WIDTH, PHOTO_THUMB_LARGE_HEIGHT);
          $zIMAGE->Save ($lgfile, $zIMAGE->Type);
          $zIMAGE->Destroy();
          
          // Save the original image data.
          move_uploaded_file ($_FILES['gNEWPHOTO']['tmp_name'], $ogfile);
          
          // Create a unique tag for this photo.
          // NOTE: Create a function to test for unique.
          $gVIEWDATA->photoInfo->Hint = $zAPPLE->RandomString (6);

          // Create a temporary class for pulling data from table.
          $matchagainst = new cDATACLASS ($gVIEWDATA->PageContext, $gVIEWDATA->photoInfo->TableName);

          // Select matching info in database.
          $matchagainst->Select ("Hint", $gVIEWDATA->photoInfo->Hint);  

          // Keep looping until no values result.
          while ($matchagainst->CountResult () > 0) {
            $gVIEWDATA->photoInfo->Hint = $zAPPLE->RandomString (6);
            $matchagainst->Select ("Hint", $gVIEWDATA->photoInfo->Hint);  
          } // while
          
          if ($zIMAGE->Error != -1) {
            global $gPHOTOFILENAME;
            $gPHOTOFILENAME = $filename;
            $zSTRINGS->Lookup ('MESSAGE.UPLOADED', $zAPPLE->Context);
            $zIMAGE->Message = $zSTRINGS->Output;
            unset ($gPHOTOFILENAME);

            // Add the reference to the database.
            $gVIEWDATA->photoInfo->Add ();

          } // if
          // Destroy the image resource.
          $zIMAGE->Destroy ();

          unset ($PHOTOCHECK);
        } else {
          global $gPHOTOFILENAME;  $gPHOTOFILENAME = $filename;
          $zSTRINGS->Lookup ('ERROR.EXISTS', $zAPPLE->Context);
          $zIMAGE->Error = -1;
          $zIMAGE->Message = $zSTRINGS->Output;
          unset ($gPHOTOFILENAME);
        } // if

        // Delete the temporary file.
        unlink ($_FILES['gNEWPHOTO']['tmp_name']);

      } else {
        $zSTRINGS->Lookup ('ERROR.UPLOAD', $zAPPLE->Context);
        $zIMAGE->Message = $zSTRINGS->Output;
      } // if

    break;

    case 'SAVE':
      // Check if user has write access;
      if ( ($gFOCUSUSERID != $zAUTHUSER->uID) and 
           ($zLOCALUSER->userAccess->a == TRUE) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $zSTRINGS->Lookup ('ERROR.CANTWRITE', $zAPPLE->Context);
        $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
        $gVIEWDATA->photoInfo->Error = -1;
        break;        
      } // if

      // Synchronize Data
      $gVIEWDATA->photoInfo->Synchronize();

      // Check whether we're adding or updating a record.
      if ($gVIEWDATA->photoInfo->tID == "") {
        // ADD - disabled.  See UPLOAD.
      } else {
        // UPDATE

        // Lookup the new directory name.
        $NEWDATA = new cPHOTOSETS ();
        $NEWDATA->Select ("tID", $gPHOTOSET);
        $NEWDATA->FetchArray ();

        // Lookup old Filename, to rename it.
        $OLDDATA = new cPHOTOINFORMATION ();
        $OLDDATA->Select ("tID", $gVIEWDATA->photoInfo->tID);
        $OLDDATA->FetchArray ();

        $oldsetid = $OLDDATA->photoSets_tID;
        $newsetid = $NEWDATA->tID;

        $photosetdir = "photos/" . $zFOCUSUSER->Username . "/sets/" . $gVIEWDATA->Directory;
      
        $ogfile = $photosetdir . "/_og." . $gVIEWDATA->photoInfo->Filename;
        $fnfile = $photosetdir . "/" . $gVIEWDATA->photoInfo->Filename;
        $smfile = $photosetdir . "/_sm." . $gVIEWDATA->photoInfo->Filename;
        $mdfile = $photosetdir . "/_md." . $gVIEWDATA->photoInfo->Filename;
        $lgfile = $photosetdir . "/_lg." . $gVIEWDATA->photoInfo->Filename;

        $old_ogfile = $photosetdir . "/_og." . $OLDDATA->Filename;
        $old_fnfile = $photosetdir . "/" . $OLDDATA->Filename;
        $old_smfile = $photosetdir . "/_sm." . $OLDDATA->Filename;
        $old_mdfile = $photosetdir . "/_md." . $OLDDATA->Filename;
        $old_lgfile = $photosetdir . "/_lg." . $OLDDATA->Filename;

        // Begin Transaction.
        $gVIEWDATA->photoInfo->Begin ();

        $gVIEWDATA->photoInfo->userAuth_uID = $zFOCUSUSER->uID;
        $gVIEWDATA->photoInfo->photoSets_tID = $gPHOTOSET;
        $gVIEWDATA->photoInfo->sID = SQL_SKIP;
        $gVIEWDATA->photoInfo->Width = SQL_SKIP;
        $gVIEWDATA->photoInfo->Height = SQL_SKIP;
        $gVIEWDATA->photoInfo->Hint = SQL_SKIP;

        $gVIEWDATA->photoInfo->ForeignKey = "photoSets_tID";
        $gVIEWDATA->photoInfo->Sanity();
        $gVIEWDATA->photoInfo->ForeignKey = "userAuth_uID";

        if (!$gVIEWDATA->photoInfo->Error) {
          global $gPHOTOFILENAME;  $gPHOTOFILENAME = $gVIEWDATA->photoInfo->Filename;
          $zSTRINGS->Lookup ('MESSAGE.SAVE', $zAPPLE->Context);
          $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
          unset ($gPHOTOFILENAME);
          $gVIEWDATA->photoInfo->Update();

          // Rename the files.
          if ($old_fnfile != $fnfile) {
            if ( ( !rename ($old_ogfile, $ogfile) ) or 
                 ( !rename ($old_fnfile, $fnfile) ) or
                 ( !rename ($old_smfile, $smfile) ) or
                 ( !rename ($old_mdfile, $mdfile) ) or
                 ( !rename ($old_lgfile, $lgfile) ) ) {
              // Look up the error message.
              global $gPHOTOFILENAME;  $gPHOTOFILENAME = $gVIEWDATA->photoInfo->Filename;
              $zSTRINGS->Lookup ('ERROR.FILE', $zAPPLE->Context);
              $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
              unset ($gPHOTOFILENAME);
  
              // Rollback changes.
              $gVIEWDATA->photoInfo->Rollback ();
              $gACTION = "EDIT";
            } else {
              $gVIEWDATA->photoInfo->Commit ();
              $gACTION = "";

              // Readjust the sID listing for old and new photosets.
              $gVIEWDATA->photoInfo->AdjustSort ("sID", "photoSets_tID", $newsetid);
              $gVIEWDATA->photoInfo->AdjustSort ("sID", "photoSets_tID", $oldsetid);
            } // if
          } else {
            $gVIEWDATA->photoInfo->Commit ();
            $gACTION = "";

            // Readjust the sID listing for old and new photosets.
            $gVIEWDATA->photoInfo->AdjustSort ("sID", "photoSets_tID", $newsetid);
            $gVIEWDATA->photoInfo->AdjustSort ("sID", "photoSets_tID", $oldsetid);
          } // if

        } else {
          $gACTION = "EDIT";
        } // if

        unset ($OLDDATA);
        unset ($NEWDATA);

        // Adjust for a recently moved entry.
        $zAPPLE->AdjustScroll ($zAPPLE->Context, $gVIEWDATA);

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
        $zSTRINGS->Lookup ('ERROR.CANTWRITE', $zAPPLE->Context);
        $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
        $gVIEWDATA->photoInfo->Error = -1;
        break;        
      } // if

      // Synchronize Data
      $gVIEWDATA->photoInfo->Synchronize();
      $gVIEWDATA->photoInfo->Select ("tID", $gVIEWDATA->photoInfo->tID);
      $gVIEWDATA->photoInfo->FetchArray ();

      $photosetid = $gVIEWDATA->photoInfo->photoSets_tID;

      $photosetdir = "photos/" . $zFOCUSUSER->Username . "/sets/" . $gVIEWDATA->Directory;

      $ogfile = $photosetdir . "/_og." . $gVIEWDATA->photoInfo->Filename;
      $fnfile = $photosetdir . "/" . $gVIEWDATA->photoInfo->Filename;
      $smfile = $photosetdir . "/_sm." . $gVIEWDATA->photoInfo->Filename;
      $mdfile = $photosetdir . "/_md." . $gVIEWDATA->photoInfo->Filename;
      $lgfile = $photosetdir . "/_lg." . $gVIEWDATA->photoInfo->Filename;

      // NOTE: CHECK FOR ERRORS BETTER.  SEE DELETEALL ABOVE.
      unlink ($ogfile);
      unlink ($fnfile);
      unlink ($smfile);
      unlink ($mdfile);
      unlink ($lgfile);

      if (!$gVIEWDATA->photoInfo->Error) {
        // Look up the name of the deleted photoset.
        global $gPHOTOFILENAME; $gPHOTOFILENAME = $gVIEWDATA->photoInfo->Filename;

        $zSTRINGS->Lookup ('MESSAGE.DELETE', $zAPPLE->Context);
        $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
        $gVIEWDATA->photoInfo->Delete();
        $gVIEWDATA->photoInfo->AdjustSort ("sID", "photoSets_tID", $photosetid);

        unset ($gPHOTOFILENAME);
      } // if

      // Adjust the Sort ID (sID) listing.
      $gVIEWDATA->photoInfo->AdjustSort ("sID", "photoSets_tID", $gVIEWDATA->tID);

    break;

    default:
    break;
  } // switch

  // PART II: Load the necessary data from the database.
  switch ($gACTION) {

    case 'EDIT':
      $gVIEWDATA->photoInfo->Synchronize();
      $gVIEWDATA->photoInfo->Select ("tID", $gtID);
      $gVIEWDATA->photoInfo->FetchArray();
    break;

    case 'SAVE':
    case 'DELETE':
    default:
    break;
    
  } // switch

  // PART III: Pre-parse the html for the main window. 
  
  // Change the select button if anything is selected.
  if ($zAPPLE->ArrayIsSet ($gMASSLIST) ) $gSELECTBUTTON = 'select_none';

  // Buffer the main listing.
  ob_start ();  

  // Choose an action
  switch ($gACTION) {
    case 'EDIT':
      $SETS = new cPHOTOSETS;
      $SETS->Select ("userAuth_uID", $zFOCUSUSER->uID, "Name");
      global $gSETLIST;
      global $gPHOTOSET;
      if (!$gPHOTOSET) $gPHOTOSET = $gVIEWDATA->photoInfo->photoSets_tID;
      while ($SETS->FetchArray () ) {
        $tID = $SETS->tID;
        $gSETLIST[$tID] = $SETS->Name;
      } // while
      unset ($SETS);
      $gIMAGEHINT = $gVIEWDATA->photoInfo->Hint;

      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photos/edit.aobj", INCLUDE_SECURITY_NONE);
    break;
    case 'NEW':
      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photos/new.aobj", INCLUDE_SECURITY_NONE);
    break;
    case 'UPLOAD':
    case 'DELETE':
    case 'SAVE':
      // Skip to the default.
    default:
      if ( ($gVIEWDATA->photoInfo->Error == 0) or 
           ($gACTION == 'MOVE_DOWN') or
           ($gACTION == 'MOVE_UP') or
           ($gACTION == 'UPLOAD') or
           ($gACTION == 'DELETE_ALL') ) {

        // Load the listing data.
        $photocriteria = array ("userAuth_uID"  => $zFOCUSUSER->uID,
                                "photoSets_tID" => $gVIEWDATA->tID);
  
        $gVIEWDATA->photoInfo->SelectByMultiple ($photocriteria, "sID");

        // Set the scrollstart back to zero if we're changing view modes.
        global $gSWITCHPOSTDATA, $gBACKPOSTDATA;
        $gSWITCHPOSTDATA = $gPOSTDATA;
        $gSWITCHPOSTDATA['SCROLLSTART'][$zAPPLE->Context] = 0;

        // Save the current scroll context.
        $gBACKPOSTDATA['SCROLLSTART'][$zAPPLE->Context] = $gSCROLLSTART[$zAPPLE->Context];

        global $gTARGET;

        $gTARGET = $_SERVER[REQUEST_URI];
        $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photos/" . $viewlocation . "list.top.aobj", INCLUDE_SECURITY_NONE);

        unset ($gSWITCHPOSTDATA);

        // Calculate scroll values.
        $gSCROLLMAX[$zAPPLE->Context] = $gVIEWDATA->photoInfo->CountResult();

        // If we're uploading, jump to the last page.
        if ($gACTION == 'UPLOAD') {

          // Calculate the scroll values.
          $zHTML->CalcScroll ($zAPPLE->Context);
           
          // Jump to the last page.
          if ($gMAXPAGES > $gCURRENTPAGE) $gSCROLLSTART[$zAPPLE->Context] = $gMAXPAGES * $gSCROLLSTEP[$zAPPLE->Context];

        } // if

        // Adjust for a recently deleted entry.
        $zAPPLE->AdjustScroll ($zAPPLE->Context, $gVIEWDATA->photoInfo);

        // Check if any results were found.
        if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
          $zSTRINGS->Lookup ('MESSAGE.NONE', $zAPPLE->Context);
          $gVIEWDATA->photoInfo->Message = $zSTRINGS->Output;
          $gVIEWDATA->photoInfo->Broadcast();
        } // if

        global $gTARGET, $gVIEWTARGET;
        global $gVARIABLES, $gCHECKED;
        
        if ($viewlocation == 'all/') $prefix = "_sm.";
        if ($viewlocation == 'compact/') $prefix = "_sm.";
        if ($viewlocation == 'standard/') $prefix = "_md.";
        if ($viewlocation == 'full/') $prefix = "_lg.";

        // Loop through the list.
        for ($listcount = 0; $listcount < $gSCROLLSTEP[$zAPPLE->Context]; $listcount++) {
         if ($gVIEWDATA->photoInfo->FetchArray()) {
          
          $gCHECKED = FALSE;

          // Target for viewing a photoset.
          $gVIEWTARGET = "/profile/" . $zFOCUSUSER->Username . "/photos/" . $gVIEWDATA->Directory . "/" . $gVIEWDATA->photoInfo->Filename;

          // Target for editin or scrolling through photosets.
          $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/photos/" . $gVIEWDATA->Directory . "/";

          $gVARIABLES = array ("gtID"   => $gVIEWDATA->photoInfo->tID,
                               "gACTION" => "EDIT");
          if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;
          global $gPHOTOLOCATION;
          // Look up first photo filename in list.
          $gPHOTOLOCATION = "photos/" . $zFOCUSUSER->Username . "/sets/" . $gVIEWDATA->Directory . '/' . $prefix . $gVIEWDATA->photoInfo->Filename;
          if (!file_exists ($gPHOTOLOCATION) ) $gPHOTOLOCATION = "/$gTHEMELOCATION/images/error/noimage.png";

          global $gPOPWIDTH, $gPOPHEIGHT;
          $gPOPWIDTH = $gVIEWDATA->photoInfo->Width + 80;
          $gPOPHEIGHT = $gVIEWDATA->photoInfo->Height + 85;
          if ($gPOPWIDTH < 565) $gPOPWIDTH = 565;
          if ($gPOPHEIGHT < 500) $gPOPHEIGHT = 500;

          $gIMAGEHINT = $gVIEWDATA->photoInfo->Hint;

          // NOTE: Pull from database.
          $gTAGSLABEL = "";
          if ($gVIEWDATA->photoInfo->Tags != "") 
            $gTAGSLABEL = 'Tags: ' . $gVIEWDATA->photoInfo->Tags;
          else
            $gTAGSLABEL = OUTPUT_NBSP;

          global $gCOMMENTCOUNT, $gCOMMENTLABEL;

          $gCOMMENTCOUNT = $zCOMMENTS->CountComments ($gVIEWDATA->photoInfo->tID, $zAPPLE->Context);

          if ($gCOMMENTCOUNT > 0) {
            $zSTRINGS->Lookup ('LABEL.COMMENTS', $zAPPLE->Context);
            $gCOMMENTLABEL = $zSTRINGS->Output;
          } else {
            $gCOMMENTLABEL = "";
          } // if
 
          // NOTE: Break this off into a Modulate function to be called by the framework.
          if ( $viewlocation == 'compact/' ) {
            // Determine what we're dividing by.
            if ($viewlocation == 'compact/')  $mod = 4;

            // If no remainder, switch up the alternate tag.
            if ($listcount % $mod == 0) {
              if ($gALTERNATE == 0) {
                 $gALTERNATE = 1;
              } else {
                 $gALTERNATE = 0;
              } // if
            } // if
          } // if

          $gEXTRAPOSTDATA['ACTION'] = "EDIT"; 
          $gEXTRAPOSTDATA['tID']    = $gVIEWDATA->photoInfo->tID;
          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photos/" . $viewlocation  . "list.middle.aobj", INCLUDE_SECURITY_NONE);
          unset ($gEXTRAPOSTDATA['ACTION']); 

         } else {
          break;
         } // if
        } // for
        
        unset ($gVARIABLES);

        $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photos/" . $viewlocation . "list.bottom.aobj", INCLUDE_SECURITY_NONE);

      } elseif ( ($gACTION == 'SAVE') or ($gACTION == 'DELETE') ) {

        if ($gtID) {
          $gIMAGEHINT = $gVIEWDATA->photoInfo->Hint;
          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photos/edit.aobj", INCLUDE_SECURITY_NONE);
        } else {
          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photos/new.aobj", INCLUDE_SECURITY_NONE);
        } // if

      } // if

    break;
  } // switch

  global $bMAINSECTION;

  // Retrieve output buffer.
  $bMAINSECTION = ob_get_clean (); 

  global $gREFERENCEID;
  $gREFERENCEID = $gVIEWDATA->tID;

  // Handle comments.
  if ($gPHOTOLISTING != VIEW_EDITOR) $zCOMMENTS->Handle ();

  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/users/photos.afrw", INCLUDE_SECURITY_NONE);

?>
