<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: messages.php                            CREATED: 02-11-2005 + 
  // | LOCATION: /code/user/                        MODIFIED: 11-07-2006 +
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
  // | DESCRIPTION:  User message center.                                |
  // | WRAPPED BY:   /code/user/main.php                                 |
  // +-------------------------------------------------------------------+

  // Check if user has necessary access to this page.
  if ($zFOCUSUSER->uID != $zLOCALUSER->uID) {
    // Error out if user does not have access privileges.
    if ($zLOCALUSER->userAccess->a == FALSE) {
      $zOLDAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
      $zOLDAPPLE->End();
    } // if
  } // if

  if ( ($gACTION == 'EDIT_LABELS') or ($gACTION == 'NEW_LABEL') or
       ($gACTION == 'DELETE_LABEL') or ($gACTION == 'SAVE_LABEL') ) {
    $zOLDAPPLE->IncludeFile ("legacy/code/user/labels.php", INCLUDE_SECURITY_NONE);
    $zOLDAPPLE->End();
  } // if

  $zFOCUSUSER->userInformation->UpdateMessageStamp ();

  // Declare Global Variables.
  global $gFOLDERID;
  global $gLABELDATA, $gLABELVALUE;
  global $gFOLDERSELECT;
  global $gMESSAGELOCATION, $gBACKTARGET;
  global $gLABELLIST;  
  global $gTARGET, $gLABELSTARGET;
  global $guID;

  // Classes.
  global $zMESSAGE;

  // Add javascript to top of page.
  $zHTML->AddScript ("user/messages.js");

  // Buffers.
  global $bMAINSECTION;
  global $bLABELLISTING;

  // Set which tab to display.
  $gUSERMESSAGESTAB = '';
  $this->SetTag ('USERMESSAGESTAB', $gUSERMESSAGESTAB);

  // NOTE: Temporary
  $zOLDAPPLE->Context = 'user.messages';

  // Set the step amount for scrolling;
  $gSCROLLSTEP[$zOLDAPPLE->Context] = 10;

  // Create the data class.
  $zMESSAGE = new cMESSAGE ();
  
  // Initialize messages class.
  $zMESSAGE->Initialize ();

  // Set the target.
  if ($gPROFILESUBACTION) $subaction = $gPROFILESUBACTION . '/';
  $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/messages/" . $subaction;

  // Update the message count.
  global $gNEWMESSAGES;
  $gNEWMESSAGES = $zMESSAGE->CountNewMessages ();

  if ($gAUTOSUBMITACTION) $gACTION = $gAUTOSUBMITACTION;

  // Set the post data to move back and forth.
  $gPOSTDATA = Array ("SCROLLSTART"     => $gSCROLLSTART,
                      "SORT"            => $gSORT);

  if ($gPROFILESUBACTION == '') {
    $gPROFILESUBACTION = 'inbox';
    $gMESSAGELOCATION = 'INBOX';
  } // if

  $gMESSAGELOCATION = strtoupper ($gPROFILESUBACTION);

  $gBACKTARGET = 'profile/' . $zFOCUSUSER->Username . '/messages/' . $gPROFILESUBACTION . '/';
  $gLABELSTARGET = 'profile/' . $zFOCUSUSER->Username . '/messages/';

  $gPAGESUBTITLE .= " - " . $gMESSAGELOCATION; 

  $gSORT = "Stamp DESC";

  $gSELECTBUTTON = 'Select All';

  // STEP 1: Take appropriate action.

  switch ($gACTION) {
    case 'ARCHIVE':
      $zMESSAGE->SelectMessage ($gIDENTIFIER);
      $zMESSAGE->LoadDraft ();
      $zMESSAGE->MoveToArchive (); 
    break;

    case 'DELETE_FOREVER':
      $zMESSAGE->SelectMessage ($gIDENTIFIER);
      $zMESSAGE->DeleteForever (); 
    break;

    case 'TRASH':
      $zMESSAGE->SelectMessage ($gIDENTIFIER);
      $zMESSAGE->LoadDraft ();
      $zMESSAGE->MoveToTrash (); 
    break;

    case 'MOVE_INBOX':
      $zMESSAGE->SelectMessage ($gIDENTIFIER);
      $zMESSAGE->MoveToInbox (); 
    break;

    case 'SPAM':
      $zMESSAGE->SelectMessage ($gIDENTIFIER);
      $zMESSAGE->ReportSpam (); 
    break;

    case 'NOT_SPAM':
      $zMESSAGE->SelectMessage ($gIDENTIFIER);
      $zMESSAGE->NotSpam (); 
    break;

    case 'SELECT_ALL':
      $gSELECTBUTTON = 'Select None';
    break;

    case 'SELECT_NONE':
      $gMASSLIST = array ();
      $gSELECTBUTTON = 'Select All';
    break;

    case 'LABELS':
    break;

    case 'DELETE_FOREVER_ALL':
      $zMESSAGE->DeleteListForever ($gMASSLIST); 
      $gMASSLIST = array ();
      $gSELECTBUTTON = 'Select All';
    break;

    case 'SPAM_ALL':
      $zMESSAGE->ReportListAsSpam ($gMASSLIST); 
      $gMASSLIST = array ();
      $gSELECTBUTTON = 'Select All';
    break;
       
    case 'MOVE_INBOX_ALL':
      $zMESSAGE->MoveListToInbox ($gMASSLIST); 
      $gMASSLIST = array ();
      $gSELECTBUTTON = 'Select All';
    break;
       
    case 'TRASH_ALL':
      $zMESSAGE->MoveListToTrash ($gMASSLIST); 
      $gMASSLIST = array ();
      $gSELECTBUTTON = 'Select All';
    break;
       
    case 'ARCHIVE_ALL':
      $zMESSAGE->MoveListToArchive ($gMASSLIST); 
      $gMASSLIST = array ();
      $gSELECTBUTTON = 'Select All';
    break;
       
    case 'READ':
      if ($gREADACTION == "READ_ALL") {
        $zMESSAGE->MarkListAsRead ($gMASSLIST); 
        if ($gMASSLIST) $gSELECTBUTTON = 'Select None';
      } // if
      if ($gREADACTION == "UNREAD_ALL") {
        $zMESSAGE->MarkListAsUnread ($gMASSLIST); 
        if ($gMASSLIST) $gSELECTBUTTON = 'Select None';
      } // if
    break;

    case 'LABEL_ALL':
      // NOTE:  Find a way to resolve conflict between global and POST variable.
      $gLABELVALUE = $_REQUEST['gLABELVALUE'];
      $zMESSAGE->AddLabelToList ($gMASSLIST);
    break;

    case 'UNREAD':
      $zMESSAGE->SelectMessage ($gIDENTIFIER);
      $zMESSAGE->LoadDraft ();
      $zMESSAGE->MarkAsUnread (); 
    break;

    case 'LABEL':
      $zMESSAGE->SelectMessage ($gIDENTIFIER);
      $zMESSAGE->LoadDraft ();
      $gLABELVALUE = $_REQUEST['gLABELVALUE'];
      $zMESSAGE->Label ($gLABELVALUE);
    break;

    case 'CANCEL':
      if (!$gIDENTIFIER) {
        $gACTION = "";
        break;
      } // if
    case 'VIEW':
    // NOTE: ADD ERROR HANDLING.
      if (!$zMESSAGE->SelectMessage ($gIDENTIFIER)) {
        // Message could not be retrieved, default to listing.
        $gACTION = NULL;
      } else { 
        $zMESSAGE->LoadDraft ();
        $zMESSAGE->MarkAsRead ();
      } // if
    break;

    case 'SEND':
      // NOTE: Use Sanity () function and standard error responses here.
      if (!$gRECIPIENTADDRESS) {
        // No recipient was provided.  Show error.
        $gACTION = 'SEND_MESSAGE';
        $zMESSAGE->Message = __( "Unable To Send Message" );
        $zMESSAGE->Errorlist['recipientaddress'] = __ ( "No Recipients Provided" );
        $zMESSAGE->Error = -1;
      } elseif (!$gBODY) {
        // No body text was provided.  Show error.
        $gACTION = 'SEND_MESSAGE';
        $zMESSAGE->Message = __( "Unable To Send Message" );
        $zMESSAGE->Errorlist['body'] = __( "Message Body Is Empty" );
        $zMESSAGE->Error = -1;
      } else {
        if (!$zMESSAGE->Send ($gRECIPIENTADDRESS, $gSUBJECT, $gBODY)) {
          $gACTION = 'SEND_MESSAGE';
        } // if;
      } // if
    break;

  } // switch

  // Choose viewing method.
  switch ($gACTION) {
    case 'UNREAD':
    case 'LABEL':
    case 'CANCEL':
    case 'VIEW':

      // Reload the folder/label list to account for update.
      $bLABELLISTING = $zMESSAGE->BufferLabelList ();
      $zMESSAGE->CountNewInFolders ();
      $zMESSAGE->DetermineCurrentFolder ();

      global $bLABELSMARK;
      $bLABELSMARK = $zMESSAGE->CreateLabelLinks ($zMESSAGE->Identifier);

      // Format the date stamp to be human readable.
      $zMESSAGE->FormatVerboseDate ("Stamp");

      // Get the sender's UID for replies.
      $guID = $zMESSAGE->Sender_uID;

      $gLABELDATA = $zMESSAGE->CreateSpecificLabelMenu ();

      $messageview = 'inbox';

      switch ($zMESSAGE->Location) {
        case FOLDER_INBOX:
          $messageview = 'inbox';
        break;
        case FOLDER_SENT:
          global $bRECIPIENTLIST;
          $bRECIPIENTLIST = $zMESSAGE->BufferRecipientList ();
          $messageview = 'sent';
        break;
        case FOLDER_DRAFTS:
          $messageview = 'drafts';
        break;
        case FOLDER_ARCHIVE:
          $messageview = 'all';
        break;
        case FOLDER_SPAM:
          $messageview = 'spam';
        break;
        case FOLDER_TRASH:
          $messageview = 'trash';
        break;
        default:
        break;
      } // switch

      $bMAINSECTION = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/$messageview/view.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
    break;

    case 'SEND_MESSAGE':

      $bLABELLISTING = $zMESSAGE->BufferLabelList ();
      $zMESSAGE->CountNewInFolders ();
      $zMESSAGE->DetermineCurrentFolder ();

      global $bRECIPIENT;

      if ($gRECIPIENTNAME) {
        $bRECIPIENT = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/recipient.known.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } else {
        $bRECIPIENT = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/recipient.unknown.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } // if

      $bMAINSECTION = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/compose.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
 
      unset ($bRECIPIENT);

    break;
    case 'REPLY':

      $bLABELLISTING = $zMESSAGE->BufferLabelList ();
      $zMESSAGE->CountNewInFolders ();
      $zMESSAGE->DetermineCurrentFolder ();

      global $bRECIPIENT;

      // Select the message we're replying to.
      $zMESSAGE->SelectMessage ($gIDENTIFIER);

      global $gRECIPIENTNAME, $gRECIPIENTDOMAIN;
      $gRECIPIENTNAME = $zMESSAGE->Sender_Username;
      $gRECIPIENTDOMAIN = $zMESSAGE->Sender_Domain;

      list ($senderfullname, $null) = $zOLDAPPLE->GetUserInformation ($zMESSAGE->Sender_Username, $zMESSAGE->Sender_Domain);

      global $gBODY;
      $zMESSAGE->FormatVerboseDate ("Stamp");
      $gBODY = $zOLDAPPLE->QuoteReply ($zMESSAGE->Body, $senderfullname, $zMESSAGE->Sender_Username, $zMESSAGE->Sender_Domain, $zMESSAGE->fStamp);

      global $gSUBJECT;
      $gSUBJECT = $zMESSAGE->Subject;
      $regarding = __( "Re" );
      if (substr ($gSUBJECT, 0, 4) != $regarding)
        $gSUBJECT = $regarding . $gSUBJECT;

      $bRECIPIENT = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/recipient.known.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      $bMAINSECTION = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/reply.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
 
      unset ($bRECIPIENT);

    break;
    case 'FORWARD':
      $bLABELLISTING = $zMESSAGE->BufferLabelList ();
      $zMESSAGE->CountNewInFolders ();
      $zMESSAGE->DetermineCurrentFolder ();

      global $bRECIPIENT;

      // Select the message we're replying to.
      $zMESSAGE->SelectMessage ($gIDENTIFIER);

      $zMESSAGE->messageRecipient->Select ("messageStore_tID", $zMESSAGE->tID);
      $addresses = array ();
      while ($zMESSAGE->messageRecipient->FetchArray ()) {
        $addresses[] = $zMESSAGE->messageRecipient->Username . '@' . $zMESSAGE->messageRecipient->Domain;
      }
      
      list ($username, $domain) = explode ('@', $addresses[0]);
      
      unset ($addresses);
      
      list ($senderfullname, $null) = $zOLDAPPLE->GetUserInformation ($username, $domain);

      global $gBODY;
      $zMESSAGE->FormatVerboseDate ("Stamp");
      $gBODY = $zOLDAPPLE->QuoteReply ($zMESSAGE->Body, $senderfullname, $username, $domain, $zMESSAGE->fStamp);

      global $gSUBJECT;
      $gSUBJECT = $zMESSAGE->Subject;
      $regarding = __( "Fwd" );
      if (substr ($gSUBJECT, 0, 4) != $regarding)
        $gSUBJECT = $regarding . $gSUBJECT;

      $bRECIPIENT = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/recipient.unknown.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      $bMAINSECTION = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/forward.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
 
      unset ($bRECIPIENT);
    break;
    case 'COMPOSE':
      $bLABELLISTING = $zMESSAGE->BufferLabelList ();
      $zMESSAGE->CountNewInFolders ();
      $zMESSAGE->DetermineCurrentFolder ();

      global $bRECIPIENT;

      $bRECIPIENT = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/recipient.unknown.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      $bMAINSECTION = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/compose.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
 
      unset ($bRECIPIENT);
    break;
    case 'SEND':
    case 'SAVE_DRAFT':
      if ($gACTION != 'SEND') 
        $zMESSAGE->SaveDraft ($gRECIPIENTADDRESS, $gSUBJECT, $gBODY);
    case 'ARCHIVE':
    case 'ARCHIVE_ALL':
    case 'CANCEL_DRAFT':
    case 'TRASH':
    case 'MOVE_INBOX':
    case 'DELETE_FOREVER':
    case 'DELETE_FOREVER_ALL':
    case 'SELECT_ALL':
    case 'SELECT_NONE':
    case 'SPAM':
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
      $zMESSAGE->DetermineCurrentFolder ();

      $bMAINSECTION = $zMESSAGE->LoadMessages ();
      if ($bMAINSECTION == FALSE) {
        $zOLDAPPLE->IncludeFile ('legacy/code/site/error/404.php', INCLUDE_SECURITY_NONE);
        $zOLDAPPLE->End();
      } // if

      if ( ($zMESSAGE->Error == 0) or 
           ($gACTION == 'ARCHIVE_ALL') or 
           ($gACTION == 'READ') or 
           ($gACTION == 'LABEL_ALL') or 
           ($gACTION == 'SPAM_ALL') or 
           ($gACTION == 'MOVE_INBOX_ALL') or 
           ($gACTION == 'DELETE_FOREVER_ALL') ) {

      } elseif ( ($gACTION == 'SAVE') or ($gACTION == 'DELETE') ) {
        if ($gtID) {
          $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/edit.aobj", INCLUDE_SECURITY_NONE);
        } else {
          $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/new.aobj", INCLUDE_SECURITY_NONE);
        } // if
      } // if
    break;
  } // switch


  // Include the outline frame.
  $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/frames/users/messages.afrw", INCLUDE_SECURITY_NONE);
  
  // End Application
  $zOLDAPPLE->End();

?>
