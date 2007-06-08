<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: logs.php                                CREATED: 02-11-2005 + 
  // | LOCATION: /code/admin/system/                MODIFIED: 04-11-2007 +
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
  // | DESCRIPTION:  Logs administration page.                           |
  // +-------------------------------------------------------------------+

  // Change to document root directory
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include BASE API classes.
  require_once ('code/include/classes/BASE/application.php'); 
  require_once ('code/include/classes/BASE/debug.php'); 
  require_once ('code/include/classes/base.php'); 
  require_once ('code/include/classes/system.php'); 
  require_once ('code/include/classes/BASE/remote.php'); 
  require_once ('code/include/classes/BASE/tags.php'); 
  require_once ('code/include/classes/BASE/xml.php'); 

  // Include Appleseed classes.
  require_once ('code/include/classes/appleseed.php'); 
  require_once ('code/include/classes/privacy.php'); 
  require_once ('code/include/classes/friends.php'); 
  require_once ('code/include/classes/messages.php'); 
  require_once ('code/include/classes/users.php'); 
  require_once ('code/include/classes/auth.php'); 
  require_once ('code/include/classes/search.php'); 

  // Create the Application class.
  $zAPPLE = new cAPPLESEED ();
  
  // Set Global Variables (Put this at the top of wrapper scripts)
  $zAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zAPPLE->Initialize("admin.system.logs", TRUE);

  // Create local classes.
  $ADMINDATA = new cSYSTEMLOGS ($zAPPLE->Context);

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
  $gPOSTDATA = Array ("CRITERIA"        => $gCRITERIA,
                      "SCROLLSTART"     => $gSCROLLSTART,
                      "SORT"            => $gSORT);

  // Set which switch to highlight.
  $gADMINSYSTEMSWITCH = '';

  // Set which tab to highlight.
  $gADMINSYSTEMLOGSTAB = '';

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
      foreach ($gMASSLIST as $number => $tidvalue) {
        $ADMINDATA->tID = $tidvalue;
        $ADMINDATA->Move(UP, $tidvalue);
        // Move in the selected list.
        if (!$ADMINDATA->Error) $gMASSLIST[$number] = $tidvalue - 1;
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
      foreach ($gMASSLIST as $number => $tidvalue) {
        $ADMINDATA->tID = $tidvalue;
        $ADMINDATA->Move(DOWN, $tidvalue);
        // Move in the selected list.
        if (!$ADMINDATA->Error) $gMASSLIST[$number] = $tidvalue + 1;
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
      foreach ($gMASSLIST as $number => $tidvalue) {
        $ADMINDATA->tID = $tidvalue;
        $ADMINDATA->Delete();
        if (!$ADMINDATA->Error) $datalist[$number] = $tidvalue;
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

      // Synchronize Data
      $ADMINDATA->Synchronize();
      if ($ADMINDATA->tID == "") {
        $ADMINDATA->tID = 1;
        $ADMINDATA->Sanity();
        if (!$ADMINDATA->Error) {
          $ADMINDATA->Add();
          global $gDATAID;  $gDATAID = $ADMINDATA->LastIncrement;
          $zSTRINGS->Lookup ('MESSAGE.NEW', $zAPPLE->Context);
          $ADMINDATA->Message = $zSTRINGS->Output;
          unset ($gDATAID);
        } // if
      } else {
        $ADMINDATA->Sanity();
        if (!$ADMINDATA->Error) {
          global $gDATAID;  $gDATAID = $ADMINDATA->tID;
          $zSTRINGS->Lookup ('MESSAGE.SAVE', $zAPPLE->Context);
          $ADMINDATA->Message = $zSTRINGS->Output;
          unset ($gDATAID);
          $ADMINDATA->Update();
        } // if
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
      $ADMINDATA->Delete();
      if (!$ADMINDATA->Error) {
        global $gDATAID;  $gDATAID = $ADMINDATA->tID;
        $zSTRINGS->Lookup ('MESSAGE.DELETE', $zAPPLE->Context);
        $ADMINDATA->Message = $zSTRINGS->Output;
        unset ($gDATAID);
        $ADMINDATA->Update();
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
      $ADMINDATA->Select ("tID", $ADMINDATA->tID);
      $ADMINDATA->FetchArray();
    break;

    case 'SELECT_ALL':
    case 'DELETE_ALL':
    case 'MOVE_UP':
    case 'MOVE_DOWN':
    case 'SAVE':
    case 'DELETE':
    default:
      if ($gCRITERIA) {
        $ADMINDATA->SelectByAll($gCRITERIA, $gSORT, 1);

        // If only one result, jump right to edit form.
        if ( ($ADMINDATA->CountResult() == 1) AND ($gACTION == 'SEARCH') ) {
          // Fetch the data.
          $ADMINDATA->FetchArray();
          $ADMINDATA->Select ("tID", $ADMINDATA->tID);
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
        $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/system/logs/edit.aobj", INCLUDE_SECURITY_NONE);
      break;
      case 'NEW':
        $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/system/logs/new.aobj", INCLUDE_SECURITY_NONE);
      break;
      case 'SELECT_ALL':
      case 'DELETE_ALL':
      case 'MOVE_UP':
      case 'MOVE_DOWN':
      case 'SAVE':
        // Skip to the default.
      default:
        if ( ($ADMINDATA->Error == 0) OR 
             ($gACTION == 'DELETE_ALL') OR 
             ($gACTION == 'MOVE_UP') OR
             ($gACTION == 'MOVE_DOWN') ) {

          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/system/logs/list.top.aobj", INCLUDE_SECURITY_NONE);

          // Calculate scroll values.
          $gSCROLLMAX[$zAPPLE->Context] = $ADMINDATA->CountResult();

          // Adjust for a recently deleted entry.
          $zAPPLE->AdjustScroll ('admin.system.logs', $ADMINDATA);

          // Check if any results were found.
          if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
            $zSTRINGS->Lookup ('MESSAGE.NONE', $zAPPLE->Context);
            $ADMINDATA->Message = $zSTRINGS->Output;
            $ADMINDATA->Broadcast();
          } // if

          // Loop through the list.
          $target = "_admin/system/logs/";
          for ($listcount = 0; $listcount < $gSCROLLSTEP[$zAPPLE->Context]; $listcount++) {
           if ($ADMINDATA->FetchArray()) {
            $output = $zAPPLE->Format ($ADMINDATA->Output, $ADMINDATA->Formatting);
            if ($gACTION == 'SELECT_ALL') $checked = TRUE;

            $gEXTRAPOSTDATA['ACTION'] = "EDIT"; 
            $gEXTRAPOSTDATA['tID']    = $ADMINDATA->tID;
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/system/logs/list.middle.aobj", INCLUDE_SECURITY_NONE);
            unset ($gEXTRAPOSTDATA);
           } else {
            break;
           } // if
          } // for

          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/system/logs/list.bottom.aobj", INCLUDE_SECURITY_NONE);

        } elseif ( ($gACTION == 'SAVE') or ($gACTION == 'DELETE') ) {
          if ($gtID) {
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/system/logs/edit.aobj", INCLUDE_SECURITY_NONE);
          } else {
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/system/logs/new.aobj", INCLUDE_SECURITY_NONE);
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
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/admin/system/logs.afrw", INCLUDE_SECURITY_NONE);

  // End the application.
  $zAPPLE->End ();

?>
