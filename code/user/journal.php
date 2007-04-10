<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: journal.php                             CREATED: 01-01-2005 + 
  // | LOCATION: /code/user/                        MODIFIED: 03-18-2006 +
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
  // | DESCRIPTION:  Displays journal section of user profile page.      |
  // | WRAPPED BY:   /code/user/main.php                                 |
  // +-------------------------------------------------------------------+

  // Include necessary files
  require_once ('code/include/classes/comments.php'); 
  require_once ('code/include/classes/journal.php'); 

  // Classes.
  global $zJOURNAL;

  // Create the data class.
  $zJOURNAL = new cJOURNALPOST ($zAPPLE->Context);

  global $gJOURNALVIEW, $gJOURNALVIEWADMIN;

  // Reference ID used for comments.
  global $gREFERENCEID;

  // Deprecate JOURNALVIEW.
  if ($gJOURNALVIEWADMIN) $gJOURNALVIEW = $gJOURNALVIEWADMIN;

  // Set the post data to move back and forth.
  $gPOSTDATA = Array ("SCROLLSTART[$zAPPLE->Context]"       => $gSCROLLSTART[$zAPPLE->Context],
                      "SORT"              => $gSORT,
                      "JOURNALVIEW"       => $gJOURNALVIEW,
                      "COMMENTVIEW"       => $gCOMMENTVIEW);

  global $zCOMMENTS;
  $zCOMMENTS = new cCOMMENTINFORMATION ("USER.COMMENTS");

  // Initialize the comment subsystem.
  $zCOMMENTS->Initialize ();
  
  $gSORT = "Posted DESC";

  $gSCROLLSTEP[$zAPPLE->Context] = 10;

  global $zLOCALUSER;

  // Create a warning message if user has no write access.
  if ( ($gAUTHUSERID != $gFOCUSUSERID) and
       ($zLOCALUSER->userAccess->a == TRUE) and
       ($zLOCALUSER->userAccess->w == FALSE) ) {
    $zSTRINGS->Lookup ('ERROR.CANTWRITE', 'USER.JOURNAL');
    $zLOCALUSER->Message = $zSTRINGS->Output;
    $zLOCALUSER->Error = 0;
  } // if

  // Check if a specific ID is being requested in the URL.
  $profiledata = split ("/", $gPROFILEREQUEST);
  $requestid = $profiledata[2];

  // If the journal view isn't set, default to 'single'.
  if (!$gJOURNALVIEW) $gJOURNALVIEW = JOURNAL_VIEW_SINGLE;

  // If we have an ID requested in the URL, set the defaults.
  if ($requestid) {
    // Assign which journal to attach comments to.
    $gREFERENCEID = $requestid;

  } // if

  // Set default journal view type.
  global $gJOURNALVIEWTYPE;
  $gJOURNALVIEWTYPE = "JOURNALVIEW";

  // Set the journal view to ADMIN if user has access.
  if ( ($gAUTHUSERID == $gFOCUSUSERID) or
       ($zLOCALUSER->userAccess->a == TRUE) ) {

    // Load the ADMIN view menu.
    global $gJOURNALVIEWTYPE;
    $gJOURNALVIEWTYPE = "JOURNALVIEWADMIN";

  } // if

  // Set which tab to highlight.
  $gUSERJOURNALTAB = '';

  global $gTARGET;
  $gTARGET = "profile/" . $zFOCUSUSER->Username . "/journal/";

  global $bMAINSECTION;
  
  // Buffer the main listing.
  ob_start ();  

  // Display the select all button by default.
  $gSELECTBUTTON = 'select_all';

  // STEP 1: Take appropriate action.
  switch ($gACTION) {
    case 'SELECT_ALL':
      $gSELECTBUTTON = 'select_none';
    break;

    case 'SELECT_NONE':
      $gMASSLIST = array ();
      $gSELECTBUTTON = 'select_all';
    break;

    case 'EDIT':
    case 'NEW':
      global $bPRIVACYOPTIONS;
      $bPRIVACYOPTIONS = $zJOURNAL->journalPrivacy->BufferOptions ("journalPost_tID", $gtID, "journal");
    break;

    case 'SAVE':
     if($gtID) {
       $zJOURNAL->Synchronize ();
       $zJOURNAL->userAuth_uID = $zFOCUSUSER->uID;
       $zJOURNAL->userIcons_Filename = $gUSERICON;
       $posted = $zHTML->JoinDate ("POSTED");
       $zJOURNAL->Posted = $posted;
       $zJOURNAL->Stamp = SQL_SKIP;
       $zJOURNAL->Sanity ();
       if ($zJOURNAL->Error == 0) {
         $zJOURNAL->Update ();
         $zJOURNAL->journalPrivacy->SaveSettings ($gPRIVACY, "journalPost_tID", $zJOURNAL->tID);
       } // if
     } else {
       $zJOURNAL->Synchronize ();
       $zJOURNAL->userAuth_uID = $zFOCUSUSER->uID;
       $zJOURNAL->userIcons_Filename = $gUSERICON;
       $posted = $zHTML->JoinDate ("POSTED");
       $zJOURNAL->Posted = $posted;
       $zJOURNAL->Stamp = SQL_NOW;
       $zJOURNAL->Sanity ();
       if ($zJOURNAL->Error == 0) {
         $zJOURNAL->Add ();
         $zJOURNAL->AutoIncremented ();
         $zJOURNAL->journalPrivacy->SaveSettings ($gPRIVACY, "journalPost_tID", $zJOURNAL->LastIncrement);
       } // if
     } // if
    break;

    case 'DELETE':
      $zJOURNAL->Synchronize ();
      $zJOURNAL->Delete ();

      if ($zJOURNAL->Error != -1) {
        $zSTRINGS->Lookup ('MESSAGE.DELETE', 'USER.JOURNAL');
        $zJOURNAL->Message = $zSTRINGS->Output;
      } // if
    break;

    case 'DELETE_ALL':
      $zJOURNAL->DeleteList ($gMASSLIST); 
      $gMASSLIST = array ();
      $gSELECTBUTTON = 'select_all';
    break;

    case 'COMMENT_READ':
    case 'COMMENT_ADD':
    case 'VIEW':
    break;
 
    default:
    break;

  } // switch

  // Choose viewing method.
  switch ($gACTION) {
    case 'COMMENT_READ':
    case 'COMMENT_ADD':
    case 'VIEW':

      if ($gREFERENCEID) $gtID = $gREFERENCEID;
      $zJOURNAL->Select ("tID", $gtID);
      $zJOURNAL->FetchArray ();

      // Check if entry is hidden or blocked for this user.
      $gPRIVACYSETTING = $zJOURNAL->journalPrivacy->Determine ("zFOCUSUSER", "zLOCALUSER", "journalPost_tID", $zJOURNAL->tID);

      if ($zAPPLE->CheckSecurity ($gPRIVACYSETTING) == TRUE) {
         $zAPPLE->IncludeFile ('code/site/error/403.php', INCLUDE_SECURITY_NONE);
         $zAPPLE->End();
      } // if

      global $bUSERICON;
      global $gTAGS;

      $bUSERICON = $zAPPLE->BufferUserIcon ($zFOCUSUSER->Username, $gSITEDOMAIN, $zJOURNAL->userIcons_Filename);

      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/view/top.aobj", INCLUDE_SECURITY_NONE);
      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/view/middle.aobj", INCLUDE_SECURITY_NONE);
      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/view/bottom.aobj", INCLUDE_SECURITY_NONE);

      // Need to scroll through individual entries.
      $zJOURNAL->JournalScroll ($gTARGET, $zJOURNAL->fPosted, $olderid, $newerid);
    break;

    case 'EDIT':
      $zFOCUSUSER->userIcons->BuildIconMenu ($zFOCUSUSER->uID);
      $zJOURNAL->Select ("tID", $gtID);
      $zJOURNAL->FetchArray ();

      global $gPOSTEDLIST;
      $gPOSTEDLIST = $zHTML->SplitDate ($zJOURNAL->Posted);

      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/edit.aobj", INCLUDE_SECURITY_NONE);
    break;

    case 'NEW':
      $zFOCUSUSER->userIcons->BuildIconMenu ($zFOCUSUSER->uID);

      global $gPOSTEDLIST;
      $currentstamp = strtotime ("now");
      $currently = date ("Y-m-d h:i:s", $currentstamp);
      $gPOSTEDLIST = $zHTML->SplitDate ($currently);

      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/new.aobj", INCLUDE_SECURITY_NONE);
    break;

    case 'SAVE':
    case 'DELETE':
    default:

      switch ($gJOURNALVIEW) {

        case JOURNAL_VIEW_ADMIN:
          // Create the journal listing.
          $zJOURNAL->Select ("userAuth_uID", $zFOCUSUSER->uID, $gSORT);

          if ( ($zJOURNAL->Error == 0) or 
             ($gACTION == 'DELETE.ALL') ) {

            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/editor/list.top.aobj", INCLUDE_SECURITY_NONE);

            // Calculate scroll values.
            $gSCROLLMAX[$zAPPLE->Context] = $zJOURNAL->CountResult();

            // Adjust for a recently deleted entry.
            $zAPPLE->AdjustScroll ('users.journal', $zJOURNAL);

            // Check if any results were found.
            if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
              $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.JOURNAL');
              $zJOURNAL->Message = $zSTRINGS->Output;
              $zJOURNAL->Broadcast();
            } // if

            global $gMESSAGESTAMP, $gMESSAGESTANDING;

            global $gCHECKED;

            $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/journal/";

            global $gLISTCOUNT;

            // Loop through the list.
            for ($gLISTCOUNT = 0; $gLISTCOUNT < $gSCROLLSTEP[$zAPPLE->Context]; $gLISTCOUNT++) {
             if ($zJOURNAL->FetchArray()) {

              $gCHECKED = FALSE;
              if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;

              $zJOURNAL->FormatDate ("Posted");

              global $gCOMMENTCOUNT, $gCOUNT;

              $COMMENTS = new cCOMMENTINFORMATION ();
    
              $commentcriteria = array ("rID"          => $zJOURNAL->tID,
                                        "Context"      => $zAPPLE->Context);
              $COMMENTS->SelectByMultiple ($commentcriteria);
              $gCOMMENTCOUNT = $COMMENTS->CountResult ();

              $zSTRINGS->Lookup ("LABEL.COUNT", "USER.JOURNALS");
              $gCOUNT = "";
              if ($gCOMMENTCOUNT > 0) $gCOUNT = $zSTRINGS->Output;

              unset ($gCOMMENTCOUNT);

              global $gEXTRAPOSTDATA;
              $gEXTRAPOSTDATA['ACTION'] = "EDIT";
              $gEXTRAPOSTDATA['tID'] = $zJOURNAL->tID;
              $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/editor/list.middle.aobj", INCLUDE_SECURITY_NONE);
              unset ($gEXTRAPOSTDATA);

             } else {
              break;
             } // if
            } // for

            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/editor/list.bottom.aobj", INCLUDE_SECURITY_NONE);

          } elseif ( ($gACTION == 'SAVE') or ($gACTION == 'DELETE') ) {
            $zFOCUSUSER->userIcons->BuildIconMenu ($zFOCUSUSER->uID);
            if ($gtID) {
              $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/edit.aobj", INCLUDE_SECURITY_NONE);
            } else {
              $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/new.aobj", INCLUDE_SECURITY_NONE);
            } // if
          } // if
        break;
        case JOURNAL_VIEW_LISTING:
          
          // Create the journal listing.
          $zJOURNAL->Select ("userAuth_uID", $zFOCUSUSER->uID, $gSORT);

          if ( ($zJOURNAL->Error == 0) or 
             ($gACTION == 'DELETE.ALL') ) {

            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/listing/list.top.aobj", INCLUDE_SECURITY_NONE);

            // Calculate scroll values.
            $gSCROLLMAX[$zAPPLE->Context] = $zJOURNAL->CountResult();

            // Adjust for a recently deleted entry.
            $zAPPLE->AdjustScroll ('users.journal', $zJOURNAL);

            // Check if any results were found.
            if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
              $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.JOURNAL');
              $zJOURNAL->Message = $zSTRINGS->Output;
              $zJOURNAL->Broadcast();
            } // if

            global $gMESSAGESTAMP, $gMESSAGESTANDING;

            global $gLISTCOUNT;

            // Loop through the list.
            for ($gLISTCOUNT = 0; $gLISTCOUNT < $gSCROLLSTEP[$zAPPLE->Context]; $gLISTCOUNT++) {
             if ($zJOURNAL->FetchArray()) {

              // Check if entry is hidden or blocked for this user.
              $gPRIVACYSETTING = $zJOURNAL->journalPrivacy->Determine ("zFOCUSUSER", "zLOCALUSER", "journalPost_tID", $zJOURNAL->tID);

              // Adjust for a hidden entry.
              if ( $zAPPLE->AdjustHiddenScroll ($gPRIVACYSETTING, $zAPPLE->Context) ) continue;

              $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/journal/" . $zJOURNAL->tID . "/";

              global $gPROTECTED;

              // Show the appropriate icon if entry is not public.
              switch ($gPRIVACYSETTING) {
                case PRIVACY_SCREEN:
                  $gPROTECTED = $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/screen.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
                break;

                case PRIVACY_RESTRICT:
                  $gPROTECTED = $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/restrict.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
                break;

                case PRIVACY_BLOCK:
                  $gPROTECTED = $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/block.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
                break;

                case PRIVACY_ALLOW:
                case PRIVACY_HIDE:
                default:
                  $gPROTECTED = OUTPUT_NBSP;
                break;
              } // switch

              $zJOURNAL->FormatDate ("Posted");

              global $gCOMMENTCOUNT, $gCOUNT;

              $COMMENTS = new cCOMMENTINFORMATION ();
    
              $gCOMMENTCOUNT = $COMMENTS->CountComments ($zJOURNAL->tID, $zAPPLE->Context);

              $zSTRINGS->Lookup ("LABEL.COUNT", "USER.JOURNALS");
              $gCOUNT = OUTPUT_NBSP;
              if ($gCOMMENTCOUNT > 0) $gCOUNT = $zSTRINGS->Output;

              unset ($gCOMMENTCOUNT);

              global $gEXTRAPOSTDATA;
              $gEXTRAPOSTDATA['ACTION'] = "VIEW";
              $gEXTRAPOSTDATA['tID'] = $zJOURNAL->tID;

              // NOTE: Maybe it would be better to have a Summary field?
              $zJOURNAL->Content = substr ($zJOURNAL->Content, 0, 512); // . "\'\"";
              // Check if album is blocked.
              if ( ($gPRIVACYSETTING == PRIVACY_BLOCK) and
                   ($zLOCALUSER->userAccess->r == FALSE) and
                   ($zLOCALUSER->uID != $zFOCUSUSER->uID)  ) {
                $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/listing/list.middle.block.aobj", INCLUDE_SECURITY_NONE);
              } else {
                $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/listing/list.middle.aobj", INCLUDE_SECURITY_NONE);
              } // if

              unset ($gEXTRAPOSTDATA);

             } else {
              break;
             } // if
            } // for

            $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/journal/";

            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/listing/list.bottom.aobj", INCLUDE_SECURITY_NONE);
            // $zHTML->Scroll ($gTARGET, 'journal', 'users.journal', SCROLL_PAGEOF);

          } elseif ( ($gACTION == 'SAVE') or ($gACTION == 'DELETE') ) {
            if ($gtID) {
              $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/edit.aobj", INCLUDE_SECURITY_NONE);
            } else {
              $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/new.aobj", INCLUDE_SECURITY_NONE);
            } // if
          } // if
        break;
        case JOURNAL_VIEW_MULTIPLE:

          // Create the journal listing.
          $zJOURNAL->Select ("userAuth_uID", $zFOCUSUSER->uID, $gSORT);

          if ( ($zJOURNAL->Error == 0) or 
             ($gACTION == 'DELETE.ALL') ) {

            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/multiple/top.aobj", INCLUDE_SECURITY_NONE);

            // Calculate scroll values.
            $gSCROLLMAX[$zAPPLE->Context] = $zJOURNAL->CountResult();

            // Adjust for a recently deleted entry.
            $zAPPLE->AdjustScroll ('users.journal', $zJOURNAL);

            // Check if any results were found.
            if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
              $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.JOURNAL');
              $zJOURNAL->Message = $zSTRINGS->Output;
              $zJOURNAL->Broadcast();
            } // if

            global $gMESSAGESTAMP, $gMESSAGESTANDING;

            global $bUSERICON;

            global $gLISTCOUNT;
            global $gTARGETID;
            global $gPROTECTED;

            // Loop through the list.
            for ($gLISTCOUNT = 0; $gLISTCOUNT < $gSCROLLSTEP[$zAPPLE->Context]; $gLISTCOUNT++) {
             if ($zJOURNAL->FetchArray()) {

              // Check if entry is hidden or blocked for this user.
              $gPRIVACYSETTING = $zJOURNAL->journalPrivacy->Determine ("zFOCUSUSER", "zLOCALUSER", "journalPost_tID", $zJOURNAL->tID);

              // Adjust for a hidden entry.
              if ( $zAPPLE->AdjustHiddenScroll ($gPRIVACYSETTING, $zAPPLE->Context) ) continue;

              // Adjust for a blocked entry.
              if ( $zAPPLE->AdjustBlockedScroll ($gPRIVACYSETTING, $zAPPLE->Context) ) continue;

              $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/journal/";
              $gTARGETID = $zJOURNAL->tID;

              $bUSERICON = $zAPPLE->BufferUserIcon ($zFOCUSUSER->Username, $gSITEDOMAIN, $zJOURNAL->userIcons_Filename);

              // Show the appropriate icon if entry is not public.
              switch ($gPRIVACYSETTING) {
                case PRIVACY_SCREEN:
                  $gPROTECTED = $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/screen.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
                break;

                case PRIVACY_RESTRICT:
                  $gPROTECTED = $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/restrict.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
                break;

                case PRIVACY_BLOCK:
                  $gPROTECTED = $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/block.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
                break;

                case PRIVACY_ALLOW:
                case PRIVACY_HIDE:
                default:
                  $gPROTECTED = OUTPUT_NBSP;
                break;
              } // switch

              $zJOURNAL->FormatDate ("Posted");

              global $gCOMMENTCOUNT, $gCOUNT;

              $COMMENTS = new cCOMMENTINFORMATION ();

              $commentcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                                        "rID"          => $zJOURNAL->tID,
                                        "Context"      => $zAPPLE->Context);
              $COMMENTS->SelectByMultiple ($commentcriteria);
              $gCOMMENTCOUNT = $COMMENTS->CountResult ();

              $zSTRINGS->Lookup ("LABEL.COUNT", "USER.JOURNALS");
              $gCOUNT = OUTPUT_NBSP;
              if ($gCOMMENTCOUNT > 0) $gCOUNT = $zSTRINGS->Output;

              unset ($gCOMMENTCOUNT);

              global $gEXTRAPOSTDATA;
              $gEXTRAPOSTDATA['ACTION'] = "VIEW";
              $gEXTRAPOSTDATA['tID'] = $zJOURNAL->tID;

              $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/multiple/middle.aobj", INCLUDE_SECURITY_NONE);

              unset ($gEXTRAPOSTDATA);

             } else {
              break;
             } // if
            } // for

            $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/journal/";

            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/multiple/bottom.aobj", INCLUDE_SECURITY_NONE);
            // Note: use for limiting number of multiple entries.
            // $zHTML->Scroll ($gTARGET, 'journal', 'users.journal', SCROLL_PAGEOF);

          } elseif ( ($gACTION == 'SAVE') or ($gACTION == 'DELETE') ) {
            if ($gtID) {
              $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/edit.aobj", INCLUDE_SECURITY_NONE);
            } else {
              $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/new.aobj", INCLUDE_SECURITY_NONE);
            } // if
          } // if
        break;
        case JOURNAL_VIEW_DEFAULT:
          $gJOURNALVIEW = JOURNAL_VIEW_SINGLE;
        case JOURNAL_VIEW_SINGLE:
        default:

          if ( ($requestid) or ($gREFERENCEID) ) {
            if (!$requestid) $requestid = $gREFERENCEID;

            // View the requested journal.
            $zJOURNAL->Select ("tID", $requestid);
            $zJOURNAL->FetchArray ();

            $countjournals = $zJOURNAL->CountResult ();

            // Check if entry is hidden or blocked for this user.
            $gPRIVACYSETTING = $zJOURNAL->journalPrivacy->Determine ("zFOCUSUSER", "zLOCALUSER", "journalPost_tID", $zJOURNAL->tID);

            if ($zAPPLE->CheckSecurity ($gPRIVACYSETTING) == TRUE) {
               $zAPPLE->IncludeFile ('code/site/error/403.php', INCLUDE_SECURITY_NONE);
               $zAPPLE->End();
            } // if

          } else {
            // View the latest journal.
            $zJOURNAL->Select ("userAuth_uID", $zFOCUSUSER->uID, $gSORT);
            $zJOURNAL->FetchArray ();

            // Grab the total amount of journal entries.
            $countjournals = $zJOURNAL->CountResult ();

            // Check if entry is hidden or blocked for this user.
            $gPRIVACYSETTING = $zJOURNAL->journalPrivacy->Determine ("zFOCUSUSER", "zLOCALUSER", "journalPost_tID", $zJOURNAL->tID);

            // Loop through until we find an open entry.
            while ($zAPPLE->CheckSecurity ($gPRIVACYSETTING) == TRUE) {

              // Subtract the journal entry that is hidden.
              $countjournals--;

              // Break out of the loop if we're out of entries.
              if ($countjournals == 0); break;
              
              // Grab the next available entry.
              $zJOURNAL->FetchArray ();

              // Grab the new privacy settings.
              $gPRIVACYSETTING = $zJOURNAL->journalPrivacy->Determine ("zFOCUSUSER", "zLOCALUSER", "journalPost_tID", $zJOURNAL->tID);
            } // if

            $gREFERENCEID = $zJOURNAL->tID;

          } // if

          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/single/top.aobj", INCLUDE_SECURITY_NONE);

          // Display a message if no journals have been posted.
          if ($countjournals == 0) {
            $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.JOURNAL');
            $zJOURNAL->Message = $zSTRINGS->Output;
            $zJOURNAL->Broadcast ();

          } else {

            global $bUSERICON;
            global $gTAGS;

            $bUSERICON = $zAPPLE->BufferUserIcon ($zFOCUSUSER->Username, $gSITEDOMAIN, $zJOURNAL->userIcons_Filename);

            $gTAGS = $zJOURNAL->Tags;
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/single/middle.aobj", INCLUDE_SECURITY_NONE);
          } // if

          $gPOSTDATA['JOURNALVIEW'] = $gJOURNALVIEW;
          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/journal/single/bottom.aobj", INCLUDE_SECURITY_NONE);
          $zJOURNAL->JournalScroll ($gTARGET, $zJOURNAL->fPosted, $olderid, $newerid);
        break;
      } // switch
      
    break;
  } // switch

  // Retrieve output buffer.
  $bMAINSECTION = ob_get_clean (); 

  if ( ( ($gJOURNALVIEW == JOURNAL_VIEW_SINGLE) AND ($zJOURNAL->CountResult() != 0)) OR
       ($gACTION == "VIEW") ) $zCOMMENTS->Handle ();

  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/users/journal.afrw", INCLUDE_SECURITY_NONE);
  
?>
