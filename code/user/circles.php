<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: circles.php                             CREATED: 01-01-2005 + 
  // | LOCATION: /code/user/                        MODIFIED: 04-11-2007 +
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
  // | VERSION:      0.7.3                                               |
  // | DESCRIPTION:  Displays circles section of user profile page.      |
  // | WRAPPED BY:   /code/user/main.php                                 |
  // +-------------------------------------------------------------------+

  // Include necessary files
  require_once ('code/include/classes/friends.php'); 

  // Check if user has necessary access to this page.
  if ($gFOCUSUSERID != $zAUTHUSER->uID) {
    // Error out if user does not have access privileges.
    if ($zLOCALUSER->userAccess->a == FALSE) {
      $zAPPLE->IncludeFile ('code/site/error/403.php', INCLUDE_SECURITY_NONE);
      $zAPPLE->End();
    } // if
  } // if

  // Create a warning message if user has no write access.
  if ( ($zAUTHUSER->uID != $gFOCUSUSERID) and
       ($zLOCALUSER->userAccess->a == TRUE) and
       ($zLOCALUSER->userAccess->w == FALSE) ) {
    $zLOCALUSER->Message = __("Write Access Denied");
    $zLOCALUSER->Error = 0;
  } // if

  // Classes.
  global $zCIRCLES;

  // Buffers.
  global $bMAINSECTION;

  // Variables.
  global $gCIRCLEVIEWTYPE, $gCIRCLEVIEW, $gCIRCLEVIEWADMIN;
  global $gCIRCLEDATA;

  // Deprecate CIRCLEVIEW.
  if ($gCIRCLEVIEWADMIN) $gCIRCLEVIEW = $gCIRCLEVIEWADMIN;

  // Create the data class.
  $zCIRCLES = new cFRIENDCIRCLES ($zAPPLE->Context);
  
  // Set the scroll step.
  $gSCROLLSTEP[$zAPPLE->Context] = 20;

  // Display the select all button by default.
  $gSELECTBUTTON = 'select_all';

  // Set which tab to highlight.
  $gUSERFRIENDSTAB = '';

  // Buffer the main listing.
  ob_start ();  

  // Take Appropriate Action.
  switch ($gACTION) {

    case 'SELECT_ALL':
      $gSELECTBUTTON = 'select_none';
    break;

    case 'SELECT_NONE':
      $gMASSLIST = array ();
      $gSELECTBUTTON = 'select_all';
    break;

    case 'DELETE':
      $zCIRCLES->Select ("tID", $gtID);
      $zCIRCLES->FetchArray ();

      // Delete Circle.
      $zCIRCLES->Delete ();

      if (!$zCIRCLES->Error) {
        $zCIRCLES->Message = __("Record Deleted");
      } // if

    break;

    case 'MOVE_UP':
      // Check if user has write access;
      if ( ($gFOCUSUSERID != $zAUTHUSER->uID) and 
           ($zLOCALUSER->userAccess->a == TRUE) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $zCIRCLES->Message = __("Write Access Denied");
        $zCIRCLES->Error = -1;
        break;        
      } // if

      // Check if any items were selected.
      if (!$gMASSLIST) {
        $zCIRCLES->Message = __("None Selected");
        $zCIRCLES->Error = -1;
        break;
      } // if

      // Loop through the list
      foreach ($gMASSLIST as $number => $tidvalue) {
        $zCIRCLES->Select ("tID", $tidvalue);
        $zCIRCLES->FetchArray ();

        $movecriteria = array ("userAuth_uID" => $zFOCUSUSER->uID);
        $zCIRCLES->MoveWithin (UP, $zCIRCLES->sID, "sID", $movecriteria);

        // Move in the selected list.
        if (!$zCIRCLES->Error) $gMASSLIST[$number] = $zCIRCLES->tID;
      } // if
    break;

    case 'MOVE_DOWN':
      // Check if user has write access;
      if ( ($gFOCUSUSERID != $zAUTHUSER->uID) and 
           ($zLOCALUSER->userAccess->a == TRUE) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $zCIRCLES->Message = __("Write Access Denied");
        $zCIRCLES->Error = -1;
        break;        
      } // if

      // Check if any items were selected.
      if (!$gMASSLIST) {
        $zCIRCLES->Message = __("None Selected");
        $zCIRCLES->Error = -1;
        break;
      } // if

      // Reverse the array when moving specific elements downwards;
      $gMASSLIST = array_reverse ($gMASSLIST);

      // Loop through the list
      foreach ($gMASSLIST as $number => $tidvalue) {
        $zCIRCLES->Select ("tID", $tidvalue);
        $zCIRCLES->FetchArray ();

        // Make sure we haven't hit the highest sID.
        $max = $zCIRCLES->Max ("sID", "userAuth_uID", $zFOCUSUSER->uID);
        if ($zCIRCLES->sID < $max) {
          $movecriteria = array ("userAuth_uID" => $zFOCUSUSER->uID);
          $zCIRCLES->MoveWithin (DOWN, $zCIRCLES->sID, "sID", $movecriteria);
        } // if

        // Move in the selected list.
        if (!$zCIRCLES->Error) $gMASSLIST[$number] = $zCIRCLES->tID;
      } // if
    break;

    case 'DELETE_ALL':
      // Check if user has write access;
      if ( ($gFOCUSUSERID != $zAUTHUSER->uID) and 
           ($zLOCALUSER->userAccess->a == TRUE) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $zCIRCLES->Message = __("Write Access Denied");
        $zCIRCLES->Error = -1;
        $gMASSLIST = array ();
        break;        
      } // if

      // Check if any items were selected.
      if (!$gMASSLIST) {
        $zCIRCLES->Message = __("None Selected");
        $zCIRCLES->Error = -1;
        break;
      } // if

      // Loop through the list
      foreach ($gMASSLIST as $number => $tidvalue) {
        $zCIRCLES->tID = $tidvalue;

        $zCIRCLES->Select ("tID", $zCIRCLES->tID);
        $zCIRCLES->FetchArray ();

        // Delete Circle.
        $zCIRCLES->Delete ();

      } // foreach

      if (!$zCIRCLES->Error) {
        $zCIRCLES->Message = __("Records Deleted");
      } // if

    break;

    case 'NEW':
    break;

    case 'SAVE':
      // Check if user has write access;
      if ( ($gFOCUSUSERID != $zAUTHUSER->uID) and 
           ($zLOCALUSER->userAccess->a == TRUE) and
           ($zLOCALUSER->userAccess->w == FALSE) ) {
        $zCIRCLES->Message = __("Write Access Denied");
        $zCIRCLES->Error = -1;
        break;        
      } // if

      // Synchronize Data
      $zCIRCLES->Synchronize();

      if ($zCIRCLES->tID == "") {
        // ADD
        $zCIRCLES->tID = 1;
        $zCIRCLES->sID = $zCIRCLES->Max ("sID", "userAuth_uID", $zFOCUSUSER->uID) + 1;
        $zCIRCLES->userAuth_uID = $zFOCUSUSER->uID;
        $zCIRCLES->Sanity();

        global $gCIRCLENAME;
        $gCIRCLENAME = $zCIRCLES->Name;
  
        if (!$zCIRCLES->Error) {
          $zCIRCLES->Message = __("Record Added");
          $zCIRCLES->Add();
          $gACTION = "";
        } // if
      } else {
        // UPDATE
        $zCIRCLES->sID = SQL_SKIP;
        $zCIRCLES->userAuth_uID = SQL_SKIP;
        $zCIRCLES->Sanity();
  
        global $gCIRCLENAME;
        $gCIRCLENAME = $zCIRCLES->Name;
  
        if (!$zCIRCLES->Error) {
          $zSTRINGS->Lookup ('MESSAGE.SAVE', $zAPPLE->Context);
          $zCIRCLES->Message = $zSTRINGS->Output;
          $zCIRCLES->Update();
          $gACTION = "";
        } // if
      } // if
  
    break;

    default:
    break;
  } // switch

  // PART II: Load the necessary data from the database.
  switch ($gACTION) {

    case 'EDIT':
      $zCIRCLES->Synchronize();
      $zCIRCLES->Select ("tID", $gtID);
      $zCIRCLES->FetchArray();
    break;

    default:
      // Create the friend listing.
      $circlecriteria = array ("userAuth_uID" => $zFOCUSUSER->uID);
      $zCIRCLES->SelectByMultiple ($circlecriteria, "sID");
    break;
    
  } // switch

  switch ($gACTION) {
    case 'EDIT':
      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/circles/edit.aobj", INCLUDE_SECURITY_NONE);
    break;
    case 'NEW':
      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/circles/new.aobj", INCLUDE_SECURITY_NONE);
    break;

    default:
      if ( ($zCIRCLES->Error == 0) or 
           ($gACTION == 'MOVE_DOWN') or
           ($gACTION == 'MOVE_UP') or
           ($gACTION == 'DELETE_ALL') ) {
  
        $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/circles/editor/list.top.aobj", INCLUDE_SECURITY_NONE);
  
        // Calculate scroll values.
        $gSCROLLMAX[$zAPPLE->Context] = $zCIRCLES->CountResult();
    
        // Adjust for a recently deleted entry.
        $zAPPLE->AdjustScroll ('user.friends.circles', $zCIRCLES);
  
        // Check if any results were found.
        if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
          $zSTRINGS->Lookup ('MESSAGE.NONE', $zAPPLE->Context);
          $zCIRCLES->Message = $zSTRINGS->Output;
          $zCIRCLES->Broadcast();
        } // if
    
        global $gLISTCOUNT;
      
        // Counter for switching up Alternate.
        $switchcount = 0;
  
        global $gCHECKED;
        global $gFRIENDSICON, $gFRIENDFULLNAME, $gFRIENDNAME;
    
        $gPOSTDATA[$gCIRCLEVIEWTYPE] = $gCIRCLEVIEW;
  
        // Loop through the list.
        for ($gLISTCOUNT = 0; $gLISTCOUNT < $gSCROLLSTEP[$zAPPLE->Context]; $gLISTCOUNT++) {
         if ($zCIRCLES->FetchArray()) {
    
          global $gTARGET;
          $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/circles/";
    
          $gPOSTDATA['ACTION'] = "EDIT";
          $gPOSTDATA['CIRCLEVIEW'] = CIRCLE_EDITOR;
          $gPOSTDATA['CIRCLEVIEWADMIN'] = CIRCLE_EDITOR;
          $gPOSTDATA['tID'] = $zCIRCLES->tID;

          $gCHECKED = FALSE;
          // Select 
          if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;

          global $gFRIENDSCOUNT, $gCOUNTLABEL;
          $zCIRCLESLIST = new cFRIENDCIRCLESLIST ();

          $zCIRCLESLIST->Select ("friendCircles_tID", $zCIRCLES->tID);
          $gFRIENDSCOUNT = $zCIRCLESLIST->CountResult();

          $zSTRINGS->Lookup ('LABEL.COUNT', $zAPPLE->Context);
          $gCOUNTLABEL = $zSTRINGS->Output;
    
          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/circles/editor/list.middle.aobj", INCLUDE_SECURITY_NONE);

          unset ($gPOSTDATA['ACTION']);
          unset ($gPOSTDATA['tID']);
    
          unset ($gFRIENDSCOUNT);
    
         } else {
          break;
         } // if
        } // for
    
        $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/circles/";
  
        $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/circles/editor/list.bottom.aobj", INCLUDE_SECURITY_NONE);
  
      } elseif ( ($gACTION == 'SAVE') or ($gACTION == 'DELETE') ) {
        if ($gtID) {
          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/circles/edit.aobj", INCLUDE_SECURITY_NONE);
        } else {
          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/circles/new.aobj", INCLUDE_SECURITY_NONE);
        } // if
      } // if
    break;
  } // switch

  // Retrieve output buffer.
  $bMAINSECTION = ob_get_clean (); 

  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/users/circles.afrw", INCLUDE_SECURITY_NONE);

  // End application.
  $zAPPLE->End ();

?>
