<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: labels.php                              CREATED: 02-11-2005 + 
  // | LOCATION: /code/user/                        MODIFIED: 02-16-2006 +
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
  // | DESCRIPTION:  Message label center.                               |
  // | WRAPPED BY:   /code/user/messages.php                             |
  // +-------------------------------------------------------------------+

  // Include necessary files
  require_once ('code/include/classes/messages.php'); 

  // Check if user has necessary access to this page.
  if ($gFOCUSUSERID != $gAUTHUSERID) {
    // Error out if user does not have access privileges.
    if ($zLOCALUSER->userAccess->a == FALSE) {
      $zAPPLE->IncludeFile ('code/site/error/403.php', INCLUDE_SECURITY_NONE);
      $zAPPLE->End();
    } // if
  } // if

  // Declare Global Variables.
  global $gFOLDERID;
  global $gFOLDERSELECT;
  global $gMESSAGELOCATION, $gBACKTARGET;
  global $gLABELLIST;  
  global $gTARGET, $gLABELSTARGET;
  global $guID;
  global $gSCROLLSTEP;  $gSCROLLSTEP['user.messages.labels'] = 20;

  // Classes.
  global $zMESSAGE;

  // Buffers.
  global $bMAINSECTION;
  global $bLABELLISTING;
  global $bCOMPOSEBUTTON, $bFORWARDBUTTON;

  // Set which tab to display.
  $gUSERMESSAGESTAB = '';

  // Create the data class.
  $zMESSAGE = new cMESSAGE ("USER.MESSAGES.LABELS");

  // Buffer the main listing.
  ob_start ();  

  // Set the post data to move back and forth.
  $gPOSTDATA = Array ("SCROLLSTART"     => $gSCROLLSTART['user.messages.labels'],
                      "SORT"            => $gSORT);

  // Check if IMAP is available for general email functions.
  if ($gSETTINGS['IMAP.AVAILABLE'] == TRUE) {
    $bCOMPOSEBUTTON = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/compose.new.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
  } else {
    $bCOMPOSEBUTTON = '';
  } // if

  $gLABELSTARGET = 'profile/' . $zFOCUSUSER->Username . '/messages/';

  $gSORT = "Label DESC";

  // STEP 1: Take appropriate action.

  switch ($gACTION) {
    case 'LABELS':
    break;

    case 'NEW_LABEL':
     $zSTRINGS->Lookup ("LABEL.NEW", "USER.MESSAGES.LABELS");
     $zMESSAGE->messageLabels->Label = $zSTRINGS->Output;
     $zMESSAGE->messageLabels->userAuth_uID = $zFOCUSUSER->uID;
     $zMESSAGE->messageLabels->Add ();
    break;

    case 'DELETE_LABEL':
     global $gtID;
     $zMESSAGE->messageLabels->tID = $gtID;
     $zMESSAGE->messageLabels->Delete ();
    break;

    case 'SAVE_LABEL':
      $updateid = $gtID;
      $zMESSAGE->messageLabels->tID = $gtID;
      $zMESSAGE->messageLabels->userAuth_uID = $zFOCUSUSER->uID;
      $zMESSAGE->messageLabels->Label = $gLABEL;
      $zMESSAGE->messageLabels->Sanity ();
      if ($zMESSAGE->messageLabels->Error == 0) {
        $zMESSAGE->messageLabels->Update ();
        $gLABEL = "";
      } // if
    break;

  } // switch

  // Choose viewing method.
  switch ($gACTION) {
    case 'NEW_LABEL':
    case 'SAVE_LABEL':
    case 'DELETE_LABEL':
    default:

      $listingview = 'inbox';

      switch ($gPROFILESUBACTION) {
        case 'inbox':
        case 'sent':
        case 'drafts':
        case 'all':
        case 'spam':
        case 'trash':
          $listingview = $gPROFILESUBACTION;
        break;
        default:
          $listingview = 'label';
        break;
      } // switch

      $bLABELLISTING = $zMESSAGE->BufferLabelList ();
      $zMESSAGE->CountNewInFolders ();

      if ($zMESSAGE->messageLabels->LoadLabels () == FALSE) {
        $zAPPLE->IncludeFile ('code/site/error/403.php', INCLUDE_SECURITY_NONE);
        $zAPPLE->End();
      } // if

      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/labels/list.top.aobj", INCLUDE_SECURITY_NONE);

      // Calculate scroll values.
      $gSCROLLMAX['user.messages.labels'] = $zMESSAGE->messageLabels->CountResult();

      // Adjust for a recently deleted entry.
      $zAPPLE->AdjustScroll ('users.labels', $zMESSAGE->messageLabels);

      // Check if any results were found.
      if ($gSCROLLMAX['user.messages.labels'] == 0) {
        $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.MESSAGES.LABELS');
        $zMESSAGE->messageLabels->Message = $zSTRINGS->Output;
        $zMESSAGE->messageLabels->Broadcast();
      } // if

      global $gMESSAGESTAMP, $gMESSAGESTANDING;

      global $gCHECKED;

      if ($gLABEL) $gLABELSAVE = $gLABEL;

      // Loop through the list.
      $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/messages/";
      for ($listcount = 0; $listcount < $gSCROLLSTEP['user.messages.labels']; $listcount++) {
       if ($zMESSAGE->messageLabels->FetchArray()) {

         // Load sender username from user table in case username has changed.
         $SENDER = new cUSER ();
         $SENDER->Select ("uID", $zMESSAGE->messageLabels->Sender_uID);
         $SENDER->FetchArray ();
         $zMESSAGE->messageLabels->Sender_Username = $SENDER->Username;
         unset ($SENDER);
      
         global $gEXTRAPOSTDATA;
         $gEXTRAPOSTDATA['ACTION'] = "VIEW"; 
         $gEXTRAPOSTDATA['tID']    = $zMESSAGE->messageLabels->tID;
         global $gMESSAGECOUNT;

         global $gtID;
         $gtID = $zMESSAGE->messageLabels->tID;

         $gMESSAGECOUNT = $zMESSAGE->messageLabelList->CountInLabel ($zMESSAGE->messageLabels->tID);

         global $gFIELDERROR;
         $gFIELDERROR = "";
         if ( ($zMESSAGE->messageLabels->Error == -1) and ($updateid == $zMESSAGE->messageLabels->tID) ) {
           $zMESSAGE->messageLabels->Label = $gLABELSAVE;
           $gLABEL = $gLABELSAVE;
           $gFIELDERROR = $zMESSAGE->messageLabels->CreateBroadcast ("field", "Label");
         } else { 
           $gLABEL = $zMESSAGE->messageLabels->Label;
         } // if
 
         global $gCONFIRMDELETE;
         $zSTRINGS->Lookup ('CONFIRM.DELETE', 'USER.MESSAGES.LABELS'); 
         $gCONFIRMDELETE = $zSTRINGS->Output;
         $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/labels/list.middle.aobj", INCLUDE_SECURITY_NONE);
         unset ($gEXTRAPOSTDATA);
         unset ($gCONFIRMDELETE);

       } else {
        break;
       } // if
      } // for

      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/labels/list.bottom.aobj", INCLUDE_SECURITY_NONE);
      $zHTML->Scroll ($gTARGET, 'editlabels', 'users.labels');

    break;
  } // switch

  // Retrieve output buffer.
  $bMAINSECTION = ob_get_clean (); 
  
  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/users/labels.afrw", INCLUDE_SECURITY_NONE);

?>
