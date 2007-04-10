<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: messages.php                            CREATED: 01-29-2006 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 11-08-2006 +
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
  // | VERSION:      0.2.2                                               |
  // | DESCRIPTION.  Message class definitions.                          |
  // +-------------------------------------------------------------------+

  // Message Meta-class.
  class cMESSAGE extends cDATACLASS {

    var $messageInformation;
    var $messageNotification;
    var $messageStore;
    var $messageLabelList;
    var $messageLabels;

    function cMESSAGE ($pDEFAULTCONTEXT = NULL) {
      
      $this->messageInformation = new cMESSAGEINFORMATION ($pDEFAULTCONTEXT);
      $this->messageNotification = new cMESSAGENOTIFICATION ($pDEFAULTCONTEXT);
      $this->messageStore = new cMESSAGESTORE ($pDEFAULTCONTEXT);
      $this->messageLabelList = new cMESSAGELABELLIST ($pDEFAULTCONTEXT);
      $this->messageLabels = new cMESSAGELABELS ($pDEFAULTCONTEXT);

    } // Constructor
    
    function CountNewMessages () {
      global $zLOCALUSER, $zAPPLE, $zHTML, $zSTRINGS;

      $NotificationTable = $this->messageNotification->TableName;
      $InformationTable = $this->messageInformation->TableName;

      $zLOCALUSER->userInformation->Select ("userAuth_uID", $zLOCALUSER->uID);
      $zLOCALUSER->userInformation->FetchArray ();
      $stamp = $zLOCALUSER->userInformation->MessageStamp;

      // Count notifications.
      $query = "SELECT COUNT($NotificationTable.tID) " .
               "AS     CountResult " .
               "FROM   $NotificationTable " .
               "WHERE  Standing = " . MESSAGE_UNREAD . " " .
               "AND    Location = " . FOLDER_INBOX . " " . 
               "AND    userAuth_uID = " . $zLOCALUSER->uID . " " . 
               "AND    Stamp > '" . $stamp . "'";

      $this->Query ($query);
      $this->FetchArray();
      $total = $this->CountResult;

      // Count stored messages.
      $query = "SELECT COUNT($InformationTable.tID) " .
               "AS     CountResult " .
               "FROM   $InformationTable " .
               "WHERE  Standing = " . MESSAGE_UNREAD . " " .
               "AND    Location = " . FOLDER_INBOX . " " . 
               "AND    userAuth_uID = " . $zLOCALUSER->uID . " " .
               "AND    Received_Stamp > '" . $stamp . "'";

      $this->Query ($query);
      $this->FetchArray();

      // Add for total.
      $total += $this->CountResult;

      if ($total) {
        global $gMESSAGECOUNT;
        $gMESSAGECOUNT = $total;
        $zSTRINGS->Lookup ('LABEL.NEWMESSAGES');
        $username = $zLOCALUSER->Username;
        $return = $zHTML->CreateLink ("profile/$username/messages/", $zAPPLE->ParseTags ($zSTRINGS->Output));
      } else {
        $return = OUTPUT_NBSP;
      } // if

      return ($return);

    } // CountNewMessages

    // Count new messages in each folder.
    function CountNewInFolders () {

      global $gFOLDERSELECT, $gFOLDERCOUNT;
      global $zFOCUSUSER;

      // Set the folder highlighting to 'normal' by default.
      $gFOLDERSELECT['INBOX'] = 'normal'; $gFOLDERSELECT['SENT'] = 'normal';
      $gFOLDERSELECT['DRAFTS'] = 'normal'; $gFOLDERSELECT['ALL'] = 'normal';
      $gFOLDERSELECT['SPAM'] = 'normal';  $gFOLDERSELECT['TRASH'] = 'normal';

      // Count the number of new messages in Inbox.
      $inboxcount = $this->CountNewInInbox ();
      if ($inboxcount == 0) {
        $gFOLDERCOUNT['INBOX'] = '';
      } else {
        $gFOLDERCOUNT['INBOX'] = '(' . $inboxcount  . ')';
      } // if

      // Count the number of new messages in Spam.
      $spamcount = $this->CountNewInSpam ();
      if ($spamcount == 0) {
        $gFOLDERCOUNT['SPAM'] = '';
      } else {
        $gFOLDERCOUNT['SPAM'] = '(' . $spamcount  . ')';
      } // if

      // Count the number of new messages in Drafts.
      $draftscount = $this->CountNewInDrafts ();
      if ($draftscount == 0) {
        $gFOLDERCOUNT['DRAFTS'] = '';
      } else {
        $gFOLDERCOUNT['DRAFTS'] = '(' . $draftscount  . ')';
      } // if
    } // CountNewInFolders

    // Count New Messages In Trash
    function CountNewInTrash () {
      global $zFOCUSUSER;

      $InformationTable = $this->messageInformation->TableName;

      // Count stored messages.
      $query = "SELECT COUNT($InformationTable.tID) " .
               "AS     CountResult " .
               "FROM   $InformationTable " .
               "WHERE  $InformationTable.Standing = " . MESSAGE_UNREAD . " " .
               "AND    $InformationTable.Location = " . FOLDER_TRASH . " " . 
               "AND    userAuth_uID = " . $zFOCUSUSER->uID;

      $this->Query ($query);
      $this->FetchArray();

      // Add for total.
      $total = $this->CountResult;

      return ($total);

    } // CountNewInTrash

    // Count New Messages In Drafts
    function CountNewInDrafts () {
      global $zFOCUSUSER;

      $StoreTable = $this->messageStore->TableName;

      // Count stored messages.
      $query = "SELECT COUNT($StoreTable.tID) " .
               "AS     CountResult " .
               "FROM   $StoreTable " .
               "WHERE  $StoreTable.Standing = " . MESSAGE_UNREAD . " " .
               "AND    $StoreTable.Location = " . FOLDER_DRAFTS . " " . 
               "AND    userAuth_uID = " . $zFOCUSUSER->uID;

      $this->Query ($query);
      $this->FetchArray();

      // Add for total.
      $total = $this->CountResult;

      return ($total);

    } // CountNewInDrafts

    // Count New Messages In Inbox
    function CountNewInInbox () {
      global $zFOCUSUSER;

      $NotificationTable = $this->messageNotification->TableName;
      $InformationTable = $this->messageInformation->TableName;

      // Count notifications.
      $query = "SELECT COUNT($NotificationTable.tID) " .
               "AS     CountResult " .
               "FROM   $NotificationTable " .
               "WHERE  $NotificationTable.Standing = " . MESSAGE_UNREAD . " " .
               "AND    userAuth_uID = " . $zFOCUSUSER->uID;

      $this->Query ($query);
      $this->FetchArray();
      $total = $this->CountResult;

      // Count stored messages.
      $query = "SELECT COUNT($InformationTable.tID) " .
               "AS     CountResult " .
               "FROM   $InformationTable " .
               "WHERE  $InformationTable.Standing = " . MESSAGE_UNREAD . " " .
               "AND    $InformationTable.Location = " . FOLDER_INBOX . " " . 
               "AND    userAuth_uID = " . $zFOCUSUSER->uID;

      $this->Query ($query);
      $this->FetchArray();

      // Add for total.
      $total += $this->CountResult;

      return ($total);

    } // CountNewInInbox

    // Count New Messages In Spam
    function CountNewInSpam () {
      global $zFOCUSUSER;

      $NotificationTable = $this->messageNotification->TableName;
      $InformationTable = $this->messageInformation->TableName;

      // Count notifications.
      $query = "SELECT COUNT($NotificationTable.tID) " .
               "AS     CountResult " .
               "FROM   $NotificationTable " .
               "WHERE  $NotificationTable.Standing = " . MESSAGE_UNREAD . " " .
               "AND    userAuth_uID = " . $zFOCUSUSER->uID;

      $this->Query ($query);
      $this->FetchArray();
      $total = $this->CountResult;

      // Count stored messages.
      $query = "SELECT COUNT($InformationTable.tID) " .
               "AS     CountResult " .
               "FROM   $InformationTable " .
               "WHERE  $InformationTable.Standing = " . MESSAGE_UNREAD . " " .
               "AND    $InformationTable.Location = " . FOLDER_INBOX . " " . 
               "AND    userAuth_uID = " . $zFOCUSUSER->uID;

      $this->Query ($query);
      $this->FetchArray();

      // Add for total.
      $total += $this->CountResult;

      return ($total);

    } // CountNewInSpam

    // Determine which of the folders is currently selected.
    function DetermineCurrentFolder () {
      global $gPROFILESUBACTION, $gFOLDERSELECT;
      global $gFOLDERID;

      $gFOLDERID = '';

      // Determine which section of mail we're looking at.
      switch ($gPROFILESUBACTION) {
        case '':
        case 'inbox':
          $gFOLDERSELECT['INBOX'] = "selected";  
          $gFOLDERID = FOLDER_INBOX;
        break;

        case 'sent':
          $gFOLDERSELECT['SENT'] = "selected";  
          $gFOLDERID = FOLDER_SENT;
        break;

        case 'drafts':
          $gFOLDERSELECT['DRAFTS'] = "selected";  
          $gFOLDERID = FOLDER_DRAFTS;
        break;

        case 'all':
          $gFOLDERSELECT['ALL'] = "selected";  
        break;

        case 'spam':
          $gFOLDERSELECT['SPAM'] = "selected";  
          $gFOLDERID = FOLDER_SPAM;
        break;

        case 'trash':
          $gFOLDERSELECT['TRASH'] = "selected";  
          $gFOLDERID = FOLDER_TRASH;
        break; 
    
        default:
          $gFOLDERSELECT[$gSUBPROFILEACTION] = "selected";  
        break;
      } // switch

    } // DetermineCurrentFolder

    function LoadMessages () {
      global $gFOLDERID, $zFOCUSUSER;
      global $gPROFILESUBACTION, $gSORT;
      global $gLABELNAME;

      $returnbuffer = NULL;
  
      switch ($gFOLDERID) {
        case FOLDER_INBOX:
          $returnbuffer = $this->BufferInbox ();
          return ($returnbuffer);
        break;
        case FOLDER_SENT:
          $returnbuffer = $this->BufferSent ();
          return ($returnbuffer);
        break;
        case FOLDER_DRAFTS:
          $returnbuffer = $this->BufferDrafts ();
          return ($returnbuffer);
        break;
        case FOLDER_TRASH:
          $returnbuffer = $this->BufferTrash ();
          return ($returnbuffer);
        break;
        case FOLDER_SPAM:
          $returnbuffer = $this->BufferSpam ();
          return ($returnbuffer);
        break;
        default:
        break;
      } // switch

      if ($gFOLDERID) {
      } elseif ($gPROFILESUBACTION == 'all') {

        // Select all new and archived messages.
        $returnbuffer = $this->BufferAll ();

        return ($returnbuffer);

      } else {

        $returnbuffer = $this->BufferLabel ();
        return ($returnbuffer);

      } // if

      return (FALSE);

    } // LoadMessages

    function BufferLabel () {
      global $zFOCUSUSER, $zAPPLE, $zHTML, $zSTRINGS;

      global $gFRAMELOCATION, $gLABELNAME, $gPROFILESUBACTION; 

      global $gTARGET, $gSCROLLSTEP, $gSCROLLMAX, $gSORT;

      global $gLABELDATA;

      global $gMESSAGESTAMP, $gMESSAGESTANDING;

      global $gACTION, $gCHECKED;

      global $gSENDERNAME, $gSENDERONLINE;

      $gLABELNAME = $gPROFILESUBACTION;
      $labelcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                              "Label"        => $gLABELNAME);

      $this->messageLabels->SelectByMultiple ($labelcriteria);
      $this->messageLabels->FetchArray ();

      if ($this->messageLabels->CountResult () == 0) {
        return (FALSE);
      } // if

      $labelid = $this->messageLabels->tID;

      $NotificationTable = $this->messageNotification->TableName;
      $InformationTable = $this->messageInformation->TableName;

      $statement_left  = "(SELECT $NotificationTable.tID, " .
                         "        $NotificationTable.userAuth_uID, " .
                         "        $NotificationTable.Sender_Username, " .
                         "        $NotificationTable.Sender_Domain, " .
                         "        $NotificationTable.Subject, " .
                         "        $NotificationTable.Identifier, " .
                         "        $NotificationTable.Stamp, " .
                         "        $NotificationTable.Standing, " .
                         "        $NotificationTable.Location " .
                         " FROM   $NotificationTable, messageLabelList " .
                         " WHERE  messageLabelList.Identifier = $NotificationTable.Identifier " .
                         " AND    $NotificationTable.userAuth_uID = " . $zFOCUSUSER->uID . " " .
                         " AND    $NotificationTable.Location != " . FOLDER_SENT . " " .
                         " AND    $NotificationTable.Location != " . FOLDER_DRAFTS . " " .
                         " AND    $NotificationTable.Location != " . FOLDER_TRASH . " " .
                         " AND    $NotificationTable.Location != " . FOLDER_SPAM . " " .
                         " AND    messageLabelList.messageLabels_tID = " . $labelid . ")";
      $statement_right = "(SELECT $InformationTable.tID, " .
                         "        $InformationTable.userAuth_uID, " .
                         "        $InformationTable.Sender_Username, " .
                         "        $InformationTable.Sender_Domain, " .
                         "        $InformationTable.Subject, " .
                         "        $InformationTable.Identifier, " .
                         "        $InformationTable.Received_Stamp AS Stamp, " .
                         "        $InformationTable.Standing, " .
                         "        $InformationTable.Location " .
                         " FROM   $InformationTable, messageLabelList " .
                         " WHERE  messageLabelList.Identifier = $InformationTable.Identifier " .
                         " AND    $InformationTable.userAuth_uID = " . $zFOCUSUSER->uID . " " .
                         " AND    $InformationTable.Location != " . FOLDER_SENT . " " .
                         " AND    $InformationTable.Location != " . FOLDER_DRAFTS . " " .
                         " AND    $InformationTable.Location != " . FOLDER_TRASH . " " .
                         " AND    $InformationTable.Location != " . FOLDER_SPAM . " " .
                         " AND    messageLabelList.messageLabels_tID = " . $labelid . ")";
      $query = $statement_left . " UNION " . $statement_right . " ORDER BY Stamp DESC";

      $this->Query ($query);

      // Calculate scroll values.
      $gSCROLLMAX[$zAPPLE->Context] = $this->CountResult();

      // Adjust for a recently deleted entry.
      $zAPPLE->AdjustScroll ('users.messages', $this);

      // Check if any results were found.
      if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
        $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/label/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.MESSAGES');
        $this->Message = $zSTRINGS->Output;
        $returnbuffer .= $this->CreateBroadcast();
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/label/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        return ($returnbuffer);
      } // if

      $returnbuffer = NULL;
      $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/label/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      // Loop through the list.
      $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/messages/" . $gPROFILESUBACTION . "/";
      for ($listcount = 0; $listcount < $gSCROLLSTEP[$zAPPLE->Context]; $listcount++) {
       if ($this->FetchArray()) {

        list ($gSENDERNAME, $gSENDERONLINE) = $zAPPLE->GetUserInformation($this->Sender_Username, $this->Sender_Domain);

        // No Fullname information found.  Using username.
        if (!$gSENDERNAME) $gSENDERNAME = $this->Sender_Username;

        $gCHECKED = FALSE;
        if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;

        $this->Sender_Username = $this->Sender_Username;
        $this->Sender_Domain = $this->Sender_Domain;
        $this->Subject = $this->Subject;
        $this->Stamp = $this->Stamp;
        $this->Standing = $this->Standing;
        $this->FormatDate ("Stamp");

        global $bINBOXMARK;
        $bINBOXMARK = NULL;

        if ( ($this->Location == FOLDER_INBOX) and 
             ($gFOLDERID != FOLDER_INBOX) ) {
          $bINBOXMARK = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/mark.inbox.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // if

        global $bLABELSMARK; 

        $this->messageLabelList->Select ("Identifier", $this->Identifier);
        if ($this->messageLabelList->CountResult () == 0) {
          $bLABELSMARK = NULL;
        } else {
          $labelarray = array ();

          while ($this->messageLabelList->FetchArray ()) {
            $this->messageLabels->Select ("tID", $this->messageLabelList->messageLabels_tID);
            $this->messageLabels->FetchArray ();
            if ($this->messageLabels->tID == $labelid) continue;
            array_push ($labelarray, $this->messageLabels->Label);
          } // while

          global $gLABELLISTING;
          $gLABELLISTING = join (", ", $labelarray);

          $bLABELSMARK = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/mark.labels.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // if

        $gMESSAGESTANDING = "";
        if ($this->Standing == MESSAGE_UNREAD) $gMESSAGESTANDING = "_new";

        global $gPOSTDATA;
        $gPOSTDATA['ACTION'] = "VIEW";
        $gPOSTDATA['IDENTIFIER'] = $this->Identifier;
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/label/list.middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        unset ($gPOSTDATA['ACTION']);
        unset ($gPOSTDATA['IDENTIFIER']);

       } else {
        break;
       } // if
      } // for

      $gLABELDATA = $this->CreateFullLabelMenu ();

      global $gREADDATA, $gREADACTION;
      $gREADDATA = array ("Z" => MENU_DISABLED . "Mark As:",
                          "READ_ALL" => "&nbsp;Read+",
                          "UNREAD_ALL" => "&nbsp;Unread+");
      $gREADACTION = 'Z';

      $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/label/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($returnbuffer);
      return ($returnbuffer);

    } // BufferLabel

    function BufferInbox () {
      global $zFOCUSUSER, $zAPPLE, $zHTML, $zSTRINGS;

      global $gFRAMELOCATION; 

      global $gTARGET, $gSCROLLSTEP, $gSCROLLMAX, $gSORT;

      global $gLABELDATA;

      global $gMESSAGESTAMP, $gMESSAGESTANDING;

      global $gACTION, $gCHECKED;

      global $gSENDERNAME, $gSENDERONLINE;

      $statement_left  = "(SELECT tID, userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Standing, Stamp FROM messageNotification WHERE userAuth_uID = " . $zFOCUSUSER->uID . " AND Location = " . FOLDER_INBOX . ") ";
      $statement_right = "(SELECT tID, userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Standing, Received_Stamp AS Stamp FROM messageInformation WHERE Location = " . FOLDER_INBOX . " AND userAuth_uID = " . $zFOCUSUSER->uID . ") ";
      $query = $statement_left . " UNION " . $statement_right;
      $query .= " ORDER BY Stamp DESC";

      $this->Query ($query);

      $returnbuffer = NULL;
      $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/inbox/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      // Calculate scroll values.
      $gSCROLLMAX[$zAPPLE->Context] = $this->CountResult();

      // Adjust for a recently deleted entry.
      $zAPPLE->AdjustScroll ('user.messages', $this);

      // Check if any results were found.
      if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
        $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/inbox/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.MESSAGES');
        $this->Message = $zSTRINGS->Output;
        $returnbuffer .= $this->CreateBroadcast();
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/inbox/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        return ($returnbuffer);
      } // if

      // Loop through the list.
      for ($listcount = 0; $listcount < $gSCROLLSTEP[$zAPPLE->Context]; $listcount++) {
       if ($this->FetchArray()) {

        list ($gSENDERNAME, $gSENDERONLINE) = $zAPPLE->GetUserInformation($this->Sender_Username, $this->Sender_Domain);

        // No Fullname information found.  Using username.
        if (!$gSENDERNAME) $gSENDERNAME = $this->Sender_Username;

        $gCHECKED = FALSE;
        if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;

        $this->Sender_Username = $this->Sender_Username;
        $this->Sender_Domain = $this->Sender_Domain;
        $this->Subject = $this->Subject;
        $this->Stamp = $this->Stamp;
        $this->Standing = $this->Standing;
        $this->FormatDate ("Stamp");

        $bINBOXMARK = NULL;

        global $bLABELSMARK; 

        $this->messageLabelList->Select ("Identifier", $this->Identifier);
        if ($this->messageLabelList->CountResult () == 0) {
          $bLABELSMARK = NULL;
        } else {
          $labelarray = array ();

          while ($this->messageLabelList->FetchArray ()) {
            $this->messageLabels->Select ("tID", $this->messageLabelList->messageLabels_tID);
            $this->messageLabels->FetchArray ();
            array_push ($labelarray, $this->messageLabels->Label);
          } // while

          global $gLABELLISTING;
          $gLABELLISTING = join (", ", $labelarray);

          $bLABELSMARK = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/mark.labels.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // if

        $gMESSAGESTANDING = "";
        if ($this->Standing == MESSAGE_UNREAD) $gMESSAGESTANDING = "_new";

        global $gPOSTDATA;
        $gPOSTDATA['ACTION'] = "VIEW";
        $gPOSTDATA['IDENTIFIER'] = $this->Identifier;
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/list.middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        unset ($gPOSTDATA['ACTION']);
        unset ($gPOSTDATA['IDENTIFIER']);

       } else {
        break;
       } // if
      } // for

      $gLABELDATA = $this->CreateFullLabelMenu ();

      global $gREADDATA, $gREADACTION;
      $gREADDATA = array ("Z" => MENU_DISABLED . "Mark As:",
                          "READ_ALL" => "&nbsp;Read+",
                          "UNREAD_ALL" => "&nbsp;Unread+");
      $gREADACTION = 'Z';

      $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/inbox/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($returnbuffer);
    } // BufferInbox

    function BufferSent () {

      global $zFOCUSUSER, $zAPPLE, $zHTML, $zSTRINGS;

      global $gFRAMELOCATION; 

      global $gTARGET, $gSCROLLSTEP, $gSCROLLMAX, $gSORT;

      global $gLABELDATA;

      global $gMESSAGESTAMP, $gMESSAGESTANDING;

      global $gACTION, $gCHECKED;

      global $gSENDERNAME, $gSENDERONLINE;

      $query = "SELECT tID, userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Standing, Stamp FROM messageStore where userAuth_uID = " . $zFOCUSUSER->uID . " AND Location = " . FOLDER_SENT;
      $query .= " ORDER BY Stamp DESC";

      $this->Query ($query);

      $returnbuffer = NULL;
      $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/sent/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      // Calculate scroll values.
      $gSCROLLMAX[$zAPPLE->Context] = $this->CountResult();

      // Adjust for a recently deleted entry.
      $zAPPLE->AdjustScroll ('users.messages', $this);

      // Check if any results were found.
      if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
        $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/sent/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.MESSAGES');
        $this->Message = $zSTRINGS->Output;
        $returnbuffer .= $this->CreateBroadcast();
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/sent/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        return ($returnbuffer);
      } // if

      // Loop through the list.
      for ($listcount = 0; $listcount < $gSCROLLSTEP[$zAPPLE->Context]; $listcount++) {
       if ($this->FetchArray()) {

        list ($gSENDERNAME, $gSENDERONLINE) = $zAPPLE->GetUserInformation($this->Sender_Username, $this->Sender_Domain);

        // No Fullname information found.  Using username.
        if (!$gSENDERNAME) $gSENDERNAME = $this->Sender_Username;

        $gCHECKED = FALSE;
        if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;

        $this->Sender_Username = $this->Sender_Username;
        $this->Sender_Domain = $this->Sender_Domain;
        $this->Subject = $this->Subject;
        $this->Stamp = $this->Stamp;
        $this->Standing = $this->Standing;
        $this->FormatDate ("Stamp");

        $bINBOXMARK = NULL;

        global $bLABELSMARK; 

        $this->messageLabelList->Select ("Identifier", $this->Identifier);
        if ($this->messageLabelList->CountResult () == 0) {
          $bLABELSMARK = NULL;
        } else {
          $labelarray = array ();

          while ($this->messageLabelList->FetchArray ()) {
            $this->messageLabels->Select ("tID", $this->messageLabelList->messageLabels_tID);
            $this->messageLabels->FetchArray ();
            array_push ($labelarray, $this->messageLabels->Label);
          } // while

          global $gLABELLISTING;
          $gLABELLISTING = join (", ", $labelarray);

          $bLABELSMARK = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/mark.labels.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // if

        $gMESSAGESTANDING = "";
        if ($this->Standing == MESSAGE_UNREAD) $gMESSAGESTANDING = "_new";

        global $gPOSTDATA;
        $gPOSTDATA['ACTION'] = "VIEW";
        $gPOSTDATA['IDENTIFIER'] = $this->Identifier;
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/sent/list.middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        unset ($gPOSTDATA['ACTION']);
        unset ($gPOSTDATA['IDENTIFIER']);

       } else {
        break;
       } // if
      } // for

      $gLABELDATA = $this->CreateFullLabelMenu ();

      global $gREADDATA, $gREADACTION;
      $gREADDATA = array ("Z" => MENU_DISABLED . "Mark As:",
                          "READ_ALL" => "&nbsp;Read+",
                          "UNREAD_ALL" => "&nbsp;Unread+");
      $gREADACTION = 'Z';

      $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/sent/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($returnbuffer);
    } // BufferSent

    function BufferDrafts () {

      global $zFOCUSUSER, $zAPPLE, $zHTML, $zSTRINGS;

      global $gFRAMELOCATION; 

      global $gTARGET, $gSCROLLSTEP, $gSCROLLMAX, $gSORT;

      global $gLABELDATA;

      global $gMESSAGESTAMP, $gMESSAGESTANDING;

      global $gACTION, $gCHECKED;

      global $gSENDERNAME, $gSENDERONLINE;

      $query = "SELECT tID, userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Standing, Stamp FROM messageStore where userAuth_uID = " . $zFOCUSUSER->uID . " AND Location = " . FOLDER_DRAFTS;
      $query .= " ORDER BY Stamp DESC";

      $this->Query ($query);

      $returnbuffer = NULL;
      $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/drafts/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      // Calculate scroll values.
      $gSCROLLMAX[$zAPPLE->Context] = $this->CountResult();

      // Adjust for a recently deleted entry.
      $zAPPLE->AdjustScroll ('users.messages', $this);

      // Check if any results were found.
      if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
        $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/drafts/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.MESSAGES');
        $this->Message = $zSTRINGS->Output;
        $returnbuffer .= $this->CreateBroadcast();
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/drafts/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        return ($returnbuffer);
      } // if

      // Loop through the list.
      for ($listcount = 0; $listcount < $gSCROLLSTEP[$zAPPLE->Context]; $listcount++) {
       if ($this->FetchArray()) {

        list ($gSENDERNAME, $gSENDERONLINE) = $zAPPLE->GetUserInformation($this->Sender_Username, $this->Sender_Domain);

        // No Fullname information found.  Using username.
        if (!$gSENDERNAME) $gSENDERNAME = $this->Sender_Username;

        $gCHECKED = FALSE;
        if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;

        $this->Sender_Username = $this->Sender_Username;
        $this->Sender_Domain = $this->Sender_Domain;
        $this->Subject = $this->Subject;
        $this->Stamp = $this->Stamp;
        $this->Standing = $this->Standing;
        $this->FormatDate ("Stamp");

        $bINBOXMARK = NULL;

        global $bLABELSMARK; 

        $this->messageLabelList->Select ("Identifier", $this->Identifier);
        if ($this->messageLabelList->CountResult () == 0) {
          $bLABELSMARK = NULL;
        } else {
          $labelarray = array ();

          while ($this->messageLabelList->FetchArray ()) {
            $this->messageLabels->Select ("tID", $this->messageLabelList->messageLabels_tID);
            $this->messageLabels->FetchArray ();
            array_push ($labelarray, $this->messageLabels->Label);
          } // while

          global $gLABELLISTING;
          $gLABELLISTING = join (", ", $labelarray);

          $bLABELSMARK = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/mark.labels.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // if

        $gMESSAGESTANDING = "";
        if ($this->Standing == MESSAGE_UNREAD) $gMESSAGESTANDING = "_new";

        global $gPOSTDATA;
        $gPOSTDATA['ACTION'] = "VIEW";
        $gPOSTDATA['IDENTIFIER'] = $this->Identifier;
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/drafts/list.middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        unset ($gPOSTDATA['ACTION']);
        unset ($gPOSTDATA['IDENTIFIER']);

       } else {
        break;
       } // if
      } // for

      $gLABELDATA = $this->CreateFullLabelMenu ();

      global $gREADDATA, $gREADACTION;
      $gREADDATA = array ("Z" => MENU_DISABLED . "Mark As:",
                          "READ_ALL" => "&nbsp;Read+",
                          "UNREAD_ALL" => "&nbsp;Unread+");
      $gREADACTION = 'Z';

      $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/drafts/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($returnbuffer);
    } // BufferDrafts

    function BufferTrash () {
      global $zFOCUSUSER, $zAPPLE, $zHTML, $zSTRINGS;

      global $gFRAMELOCATION; 

      global $gTARGET, $gSCROLLSTEP, $gSCROLLMAX, $gSORT;

      global $gLABELDATA;

      global $gMESSAGESTAMP, $gMESSAGESTANDING;

      global $gACTION, $gCHECKED;

      global $gSENDERNAME, $gSENDERONLINE;

      $query = "SELECT tID, userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Standing, Received_Stamp AS Stamp, Location FROM messageInformation WHERE userAuth_uID = " . $zFOCUSUSER->uID . " AND Location = " . FOLDER_TRASH;
      $query .= " ORDER BY Stamp DESC";

      $this->Query ($query);

      $returnbuffer = NULL;
      $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/trash/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      // Calculate scroll values.
      $gSCROLLMAX[$zAPPLE->Context] = $this->CountResult();

      // Adjust for a recently deleted entry.
      $zAPPLE->AdjustScroll ('users.messages', $this);

      // Check if any results were found.
      if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
        $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/trash/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.MESSAGES');
        $this->Message = $zSTRINGS->Output;
        $returnbuffer .= $this->CreateBroadcast();
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/trash/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        return ($returnbuffer);
      } // if

      // Loop through the list.
      for ($listcount = 0; $listcount < $gSCROLLSTEP[$zAPPLE->Context]; $listcount++) {
       if ($this->FetchArray()) {

        list ($gSENDERNAME, $gSENDERONLINE) = $zAPPLE->GetUserInformation($this->Sender_Username, $this->Sender_Domain);

        // No Fullname information found.  Using username.
        if (!$gSENDERNAME) $gSENDERNAME = $this->Sender_Username;

        $gCHECKED = FALSE;
        if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;

        $this->Sender_Username = $this->Sender_Username;
        $this->Sender_Domain = $this->Sender_Domain;
        $this->Subject = $this->Subject;
        $this->Stamp = $this->Stamp;
        $this->Standing = $this->Standing;
        $this->FormatDate ("Stamp");

        global $bINBOXMARK; 
        $bINBOXMARK = NULL;

        global $bLABELSMARK; 

        $this->messageLabelList->Select ("Identifier", $this->Identifier);
        if ($this->messageLabelList->CountResult () == 0) {
          $bLABELSMARK = NULL;
        } else {
          $labelarray = array ();

          while ($this->messageLabelList->FetchArray ()) {
            $this->messageLabels->Select ("tID", $this->messageLabelList->messageLabels_tID);
            $this->messageLabels->FetchArray ();
            array_push ($labelarray, $this->messageLabels->Label);
          } // while

          global $gLABELLISTING;
          $gLABELLISTING = join (", ", $labelarray);

          $bLABELSMARK = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/mark.labels.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // if

        $gMESSAGESTANDING = "";
        if ($this->Standing == MESSAGE_UNREAD) $gMESSAGESTANDING = "_new";

        global $gPOSTDATA;
        $gPOSTDATA['ACTION'] = "VIEW";
        $gPOSTDATA['IDENTIFIER'] = $this->Identifier;
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/list.middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        unset ($gPOSTDATA['ACTION']);
        unset ($gPOSTDATA['IDENTIFIER']);

       } else {
        break;
       } // if
      } // for

      $gLABELDATA = $this->CreateFullLabelMenu ();

      global $gREADDATA, $gREADACTION;
      $gREADDATA = array ("Z" => MENU_DISABLED . "Mark As:",
                          "READ_ALL" => "&nbsp;Read+",
                          "UNREAD_ALL" => "&nbsp;Unread+");
      $gREADACTION = 'Z';

      $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/trash/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($returnbuffer);
    } // BufferTrash

    function BufferAll () {
      global $zFOCUSUSER, $zAPPLE, $zHTML, $zSTRINGS;

      global $gFRAMELOCATION; 

      global $gTARGET, $gSCROLLSTEP, $gSCROLLMAX, $gSORT;

      global $gLABELDATA;

      global $gMESSAGESTAMP, $gMESSAGESTANDING;

      global $gACTION, $gCHECKED;

      global $gSENDERNAME, $gSENDERONLINE;

      $statement_left  = "(SELECT tID, userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Standing, Stamp, Location FROM messageNotification WHERE userAuth_uID = " . $zFOCUSUSER->uID . " AND Location != " . FOLDER_SPAM . " ) ";
      $statement_right = "(SELECT tID, userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Standing, Received_Stamp AS Stamp, Location FROM messageInformation WHERE Location != " . FOLDER_TRASH . " AND userAuth_uID = " . $zFOCUSUSER->uID . " AND Location != " . FOLDER_SPAM . ") ";
      $query = $statement_left . " UNION " . $statement_right;
      $query .= " ORDER BY Stamp DESC";

      $this->Query ($query);

      $returnbuffer = NULL;
      $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/all/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      // Calculate scroll values.
      $gSCROLLMAX[$zAPPLE->Context] = $this->CountResult();

      // Adjust for a recently deleted entry.
      $zAPPLE->AdjustScroll ('users.messages', $this);

      // Check if any results were found.
      if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
        $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/all/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.MESSAGES');
        $this->Message = $zSTRINGS->Output;
        $returnbuffer .= $this->CreateBroadcast();
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/all/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        return ($returnbuffer);
      } // if

      // Loop through the list.
      for ($listcount = 0; $listcount < $gSCROLLSTEP[$zAPPLE->Context]; $listcount++) {
       if ($this->FetchArray()) {

        list ($gSENDERNAME, $gSENDERONLINE) = $zAPPLE->GetUserInformation($this->Sender_Username, $this->Sender_Domain);

        // No Fullname information found.  Using username.
        if (!$gSENDERNAME) $gSENDERNAME = $this->Sender_Username;

        $gCHECKED = FALSE;
        if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;

        $this->Sender_Username = $this->Sender_Username;
        $this->Sender_Domain = $this->Sender_Domain;
        $this->Subject = $this->Subject;
        $this->Stamp = $this->Stamp;
        $this->Standing = $this->Standing;
        $this->FormatDate ("Stamp");

        global $bINBOXMARK; 
        $bINBOXMARK = NULL;

        if ( ($this->Location == FOLDER_INBOX) and 
             ($gFOLDERID != FOLDER_INBOX) ) {
          $bINBOXMARK = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/mark.inbox.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // if

        global $bLABELSMARK; 

        $this->messageLabelList->Select ("Identifier", $this->Identifier);
        if ($this->messageLabelList->CountResult () == 0) {
          $bLABELSMARK = NULL;
        } else {
          $labelarray = array ();

          while ($this->messageLabelList->FetchArray ()) {
            $this->messageLabels->Select ("tID", $this->messageLabelList->messageLabels_tID);
            $this->messageLabels->FetchArray ();
            array_push ($labelarray, $this->messageLabels->Label);
          } // while

          global $gLABELLISTING;
          $gLABELLISTING = join (", ", $labelarray);

          $bLABELSMARK = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/mark.labels.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // if

        $gMESSAGESTANDING = "";
        if ($this->Standing == MESSAGE_UNREAD) $gMESSAGESTANDING = "_new";

        global $gPOSTDATA;
        $gPOSTDATA['ACTION'] = "VIEW";
        $gPOSTDATA['IDENTIFIER'] = $this->Identifier;
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/list.middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        unset ($gPOSTDATA['ACTION']);
        unset ($gPOSTDATA['IDENTIFIER']);

       } else {
        break;
       } // if
      } // for

      $gLABELDATA = $this->CreateFullLabelMenu ();

      global $gREADDATA, $gREADACTION;
      $gREADDATA = array ("Z" => MENU_DISABLED . "Mark As:",
                          "READ_ALL" => "&nbsp;Read+",
                          "UNREAD_ALL" => "&nbsp;Unread+");
      $gREADACTION = 'Z';

      $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/all/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($returnbuffer);
    } // BufferAll

    function BufferSpam () {
      global $zFOCUSUSER, $zAPPLE, $zHTML, $zSTRINGS;

      global $gFRAMELOCATION; 

      global $gTARGET, $gSCROLLSTEP, $gSCROLLMAX, $gSORT;

      global $gLABELDATA;

      global $gMESSAGESTAMP, $gMESSAGESTANDING;

      global $gACTION, $gCHECKED;

      global $gSENDERNAME, $gSENDERONLINE;

      $statement_left  = "(SELECT tID, userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Standing, Stamp FROM messageNotification WHERE userAuth_uID = " . $zFOCUSUSER->uID . " AND Location = " . FOLDER_SPAM . ") ";
      $statement_right = "(SELECT tID, userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Standing, Received_Stamp AS Stamp FROM messageInformation WHERE Location = " . FOLDER_SPAM . " AND userAuth_uID = " . $zFOCUSUSER->uID . ") ";
      $query = $statement_left . " UNION " . $statement_right;
      $query .= " ORDER BY Stamp DESC";

      $this->Query ($query);

      $returnbuffer = NULL;
      $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/spam/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      // Calculate scroll values.
      $gSCROLLMAX[$zAPPLE->Context] = $this->CountResult();

      // Adjust for a recently deleted entry.
      $zAPPLE->AdjustScroll ('users.messages', $this);

      // Check if any results were found.
      if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
        $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/spam/list.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.MESSAGES');
        $this->Message = $zSTRINGS->Output;
        $returnbuffer .= $this->CreateBroadcast();
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/spam/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        return ($returnbuffer);
      } // if

      // Loop through the list.
      for ($listcount = 0; $listcount < $gSCROLLSTEP[$zAPPLE->Context]; $listcount++) {
       if ($this->FetchArray()) {

        list ($gSENDERNAME, $gSENDERONLINE) = $zAPPLE->GetUserInformation($this->Sender_Username, $this->Sender_Domain);

        // No Fullname information found.  Using username.
        if (!$gSENDERNAME) $gSENDERNAME = $this->Sender_Username;

        $gCHECKED = FALSE;
        if ($gACTION == 'SELECT_ALL') $gCHECKED = TRUE;

        $this->Sender_Username = $this->Sender_Username;
        $this->Sender_Domain = $this->Sender_Domain;
        $this->Subject = $this->Subject;
        $this->Stamp = $this->Stamp;
        $this->Standing = $this->Standing;
        $this->FormatDate ("Stamp");

        $bINBOXMARK = NULL;

        global $bLABELSMARK; 

        $this->messageLabelList->Select ("Identifier", $this->Identifier);
        if ($this->messageLabelList->CountResult () == 0) {
          $bLABELSMARK = NULL;
        } else {
          $labelarray = array ();

          while ($this->messageLabelList->FetchArray ()) {
            $this->messageLabels->Select ("tID", $this->messageLabelList->messageLabels_tID);
            $this->messageLabels->FetchArray ();
            array_push ($labelarray, $this->messageLabels->Label);
          } // while

          global $gLABELLISTING;
          $gLABELLISTING = join (", ", $labelarray);

          $bLABELSMARK = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/mark.labels.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // if

        $gMESSAGESTANDING = "";
        if ($this->Standing == MESSAGE_UNREAD) $gMESSAGESTANDING = "_new";

        global $gPOSTDATA;
        $gPOSTDATA['ACTION'] = "VIEW";
        $gPOSTDATA['IDENTIFIER'] = $this->Identifier;
        $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/list.middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        unset ($gPOSTDATA['ACTION']);
        unset ($gPOSTDATA['IDENTIFIER']);

       } else {
        break;
       } // if
      } // for

      $gLABELDATA = $this->CreateFullLabelMenu ();

      global $gREADDATA, $gREADACTION;
      $gREADDATA = array ("Z" => MENU_DISABLED . "Mark As:",
                          "READ_ALL" => "&nbsp;Read+",
                          "UNREAD_ALL" => "&nbsp;Unread+");
      $gREADACTION = 'Z';

      $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/spam/list.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($returnbuffer);
    } // BufferSpam

    function SelectAllMessages () {

      global $zFOCUSUSER, $gSORT;

      $statement_left  = "(SELECT tID, userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Standing, Stamp FROM messageNotification) ";
      $statement_right = "(SELECT tID, userAuth_uID, Sender_Username, Sender_Domain, Identifier, Subject, Standing, Received_Stamp AS Stamp FROM messageInformation) ";
      $query = $statement_left . " UNION " . $statement_right;
      $query .= " ORDER BY Stamp DESC";

      $this->Query ($query);

      return (0);
    } // SelectAllMessages

    function LocateMessage ($pIDENTIFIER) {
      global $zFOCUSUSER;

      // Check messageNotification
      $query = "SELECT tID FROM " . $this->messageNotification->TableName . " WHERE Identifier = '$pIDENTIFIER' AND userAuth_uID = " . $zFOCUSUSER->uID;
      $this->Query ($query);
      if ($this->CountResult () > 0) return ('messageNotification');
      // Check messageInformation
      $query = "SELECT tID FROM " . $this->messageInformation->TableName . " WHERE Identifier = '$pIDENTIFIER' AND userAuth_uID = " . $zFOCUSUSER->uID;
      $this->Query ($query);
      if ($this->CountResult () > 0) return ('messageInformation');
      // Check messageStore
      $query = "SELECT tID FROM " . $this->messageStore->TableName . " WHERE Identifier = '$pIDENTIFIER' AND userAuth_uID = " . $zFOCUSUSER->uID;
      $this->Query ($query);
      if ($this->CountResult () > 0) return ('messageStore');
    } // LocateMessage

    function SelectMessage ($pIDENTIFIER) {

      $classlocation = $this->LocateMessage ($pIDENTIFIER);
      $this->Identifier = $pIDENTIFIER;

      if ($classlocation == 'messageNotification') {
        if (!$this->RetrieveMessage ()) {
          return (FALSE);
        } // if
      } elseif ($classlocation == 'messageInformation') {
        // Message is an archive.
        $this->messageInformation->Select ("Identifier", $pIDENTIFIER);
        $this->messageInformation->FetchArray();
        $this->tID = $this->messageInformation->tID;
        $this->Subject = $this->messageInformation->Subject;
        $this->Body = $this->messageInformation->Body;
        $this->Stamp = $this->messageInformation->Received_Stamp;
        $this->Location = $this->messageInformation->Location;
        $this->Identifier = $this->messageInformation->Identifier;
        $this->FormatDate ("Stamp");
        $this->Sender_Username = $this->messageInformation->Sender_Username;
        $this->Sender_Domain = $this->messageInformation->Sender_Domain;
        
        // Check for corresponding local sent message and mark as read.
        $MESSAGE = new cMESSAGESTORE ();
        $MESSAGE->Select ("Identifier", $pIDENTIFIER);
        $MESSAGE->FetchArray ();
        $MESSAGE->Standing = MESSAGE_READ;
        $MESSAGE->Update ();
        unset ($MESSAGE);
      } elseif ($classlocation == 'messageStore') {
        // Message is in sent folder.
        $this->messageStore->Select ("Identifier", $pIDENTIFIER);
        $this->messageStore->FetchArray();
        $this->tID = $this->messageStore->tID;
        $this->Subject = $this->messageStore->Subject;
        $this->Body = $this->messageStore->Body;
        $this->Stamp = $this->messageStore->Stamp;
        $this->Location = $this->messageStore->Location;
        $this->Identifier = $this->messageStore->Identifier;
        $this->Standing = $this->messageStore->Standing;
        $this->FormatDate ("Stamp");
        $this->Sender_Username = $this->messageStore->Sender_Username;
        $this->Sender_Domain = $this->messageStore->Sender_Domain;
      } // if

      return (TRUE);

    } // SelectMessage

    function LoadDraft () {
      global $zAPPLE;

      if ($this->Location == FOLDER_DRAFTS) {
        global $gRECIPIENTNAME, $gRECIPIENTDOMAIN;
        global $gBODY, $gSUBJECT, $gtID;
        global $bRECIPIENT;
        global $gFRAMELOCATION;
        global $gRECIPIENTADDRESS;

        $gRECIPIENTNAME = $this->Sender_Username;
        $gRECIPIENTDOMAIN = $this->Sender_Domain;
        $gRECIPIENTADDRESS = $gRECIPIENTNAME . '@' . $gRECIPIENTDOMAIN;
        $gtID = $this->tID;
        $gBODY = html_entity_decode ($this->Body);
        $gSUBJECT = $this->Subject;

        $bRECIPIENT = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/recipient.unknown.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } // if
    } // LoadDraft

    // Mark The Current Message As Read.
    function MarkAsRead () {

      // Do not change the status of a Draft message.
      if ($this->Location == FOLDER_DRAFTS) return (TRUE);

      $classlocation = $this->LocateMessage ($this->Identifier);
 
      // Check if user owns this message.
      if ($this->$classlocation->CheckReadAccess () == FALSE) {
        global $gMESSAGEID;
        $gMESSAGEID = $this->tID;
        $zSTRINGS->Lookup ('ERROR.ACCESS');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if 

      // Save some cycles by only updating if it's currely UNREAD.
      if ($this->$classlocation->Standing == MESSAGE_UNREAD) {
        $this->$classlocation->Standing = MESSAGE_READ;
        $this->$classlocation->Update ();
      } // if

    } // MarkAsRead
    
    // Mark The Current Message As Unread.
    function MarkAsUnread () {

      // Do not change the status of a Draft message.
      if ($this->Location == FOLDER_DRAFTS) return (FALSE);

      global $zSTRINGS;

      // Check if user owns this message.
      if ($this->CheckReadAccess () == FALSE) {
        global $gMESSAGEID;
        $gMESSAGEID = $this->tID;
        $zSTRINGS->Lookup ('ERROR.ACCESS');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if 

      $classlocation = $this->LocateMessage ($this->Identifier);

      // Save some cycles by only updating if it's currely READ.
      if ($this->$classlocation->Standing == MESSAGE_READ) {
        $this->$classlocation->Standing = MESSAGE_UNREAD;
        $this->$classlocation->Update ();
      } // if

      $zSTRINGS->Lookup ('MESSAGE.UNREAD');
      $this->Message = $zSTRINGS->Output;

      return (TRUE);

    } // MarkAsUnread


    function MarkListAsRead ($pDATALIST) {
      global $zSTRINGS;

      if (count ($pDATALIST) == 0) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if

      foreach ($pDATALIST as $key => $id) {

        $classlocation = $this->LocateMessage ($id);

        // Select the message in question.
        $this->$classlocation->Select ("Identifier", $id);
        $this->$classlocation->FetchArray ();

        // Check if user owns this message.
        if ($this->$classlocation->CheckReadAccess () == FALSE) {
          global $gMESSAGEID;
          $gMESSAGEID = $id;
          $zSTRINGS->Lookup ('ERROR.ACCESS');
          $this->Message = $zSTRINGS->Output;
          $this->Error = -1;
          continue;
        } // if 

        $this->$classlocation->tID = $id;
        $this->$classlocation->Standing = MESSAGE_READ;

        $this->$classlocation->userAuth_uID = SQL_SKIP;
        $this->$classlocation->Identifier = SQL_SKIP;
        $this->$classlocation->Sender_Fullname = SQL_SKIP;
        $this->$classlocation->Sender_Username = SQL_SKIP;
        $this->$classlocation->Sender_Domain = SQL_SKIP;
        $this->$classlocation->Subject = SQL_SKIP;
        $this->$classlocation->Body = SQL_SKIP;
        $this->$classlocation->Sent_Stamp = SQL_SKIP;
        $this->$classlocation->Received_Stamp = SQL_SKIP;
        $this->$classlocation->Stamp = SQL_SKIP;
        $this->$classlocation->Location = SQL_SKIP;

        $this->$classlocation->Update ("Identifier", $id);

      } // if

      return (TRUE);

    } // MarkListAsRead

    function MarkListAsUnread ($pDATALIST) {

      global $zSTRINGS;
    
      if (count ($pDATALIST) == 0) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if

      foreach ($pDATALIST as $key => $id) {

        $classlocation = $this->LocateMessage ($id);

        // Select the message in question.
        $this->$classlocation->Select ("tID", $id);
        $this->$classlocation->FetchArray ();

        // Check if user owns this message.
        if ($this->$classlocation->CheckReadAccess () == FALSE) {
          global $gMESSAGEID;
          $gMESSAGEID = $id;
          $zSTRINGS->Lookup ('ERROR.ACCESS');
          $this->$classlocation->Message = $zSTRINGS->Output;
          $this->$classlocation->Error = -1;
          continue;
        } // if 

        $this->$classlocation->tID = $id;
        $this->$classlocation->Standing = MESSAGE_UNREAD;

        $this->$classlocation->userAuth_uID = SQL_SKIP;
        $this->$classlocation->Sender_Username = SQL_SKIP;
        $this->$classlocation->Sender_Domain = SQL_SKIP;
        $this->$classlocation->Identifier = SQL_SKIP;
        $this->$classlocation->Subject = SQL_SKIP;
        $this->$classlocation->Body = SQL_SKIP;
        $this->$classlocation->Sent_Stamp = SQL_SKIP;
        $this->$classlocation->Received_Stamp = SQL_SKIP;
        $this->$classlocation->Stamp = SQL_SKIP;
        $this->$classlocation->Location = SQL_SKIP;

        $this->$classlocation->Update ("Identifier", $id);

      } // if

      return (TRUE);

    } // MarkListAsUnread

    function CreateLabelLinks ($pIDENTIFIER) {

      global $zHTML, $zFOCUSUSER;

      $this->messageLabelList->Select ("Identifier", $pIDENTIFIER);

      $labellist = array ();

      while ($this->messageLabelList->FetchArray ()) {
        $this->messageLabels->Select ("tID", $this->messageLabelList->messageLabels_tID);
        $this->messageLabels->FetchArray ();
        $label = $zHTML->CreateLink ("profile/" . $zFOCUSUSER->Username . "/messages/" . $this->messageLabels->Label . "/", $this->messageLabels->Label);
        array_push ($labellist, $label);
      } // while

      $labellistfinal = join (', ', $labellist);
 
      return ($labellistfinal);

    } // CreateLabelLinks

    function Label ($pLABELVALUE) {
      global $zSTRINGS;

      $checkcriteria = array ("Identifier" => $this->Identifier,
                              "messageLabels_tID" => $pLABELVALUE);
      $this->messageLabelList->SelectByMultiple ($checkcriteria); 
      $this->messageLabelList->FetchArray ();

      if ($this->messageLabelList->CountResult () == 0) {
        $this->messageLabelList->Identifier = $this->Identifier;
        $this->messageLabelList->messageLabels_tID = $pLABELVALUE;
        $this->messageLabelList->Add ();

        $this->messageLabels->Select ("tID", $pLABELVALUE);
        $this->messageLabels->FetchArray ();

        global $gAPPLYLABELNAME; 

        $gAPPLYLABELNAME = $this->messageLabels->Label;
         
        $zSTRINGS->Lookup ('MESSAGE.APPLY');

        $this->messageLabels->Message = $zSTRINGS->Output;
 
        unset ($gAPPLYLABELNAME);
  
      } else {
        $this->messageLabelList->Select ("Identifier", $gIDENTIFIER);
        $this->FetchArray ();
        $this->messageLabelList->Delete ();

        $this->messageLabels->Select ("tID", $pLABELVALUE);
        $this->messageLabels->FetchArray ();

        global $gREMOVELABELNAME; 

        $gREMOVELABELNAME = $this->messageLabels->Label;
       
        $zSTRINGS->Lookup ('MESSAGE.REMOVE');

        $this->messageLabels->Message = $zSTRINGS->Output;

        unset ($gREMOVELABELNAME);
  
      } // if

      return (TRUE);
    } // Label

    function AddLabelToList ($pDATALIST) {
      global $gLABELVALUE, $gSELECTBUTTON;
      global $zSTRINGS;

      $labelaction = substr ($gLABELVALUE, 0, 1);
      if ( ($labelaction == 'r') or
           ($labelaction == 'a') ) {
        $gLABELVALUE = substr ($gLABELVALUE, 1, strlen ($gLABELVALUE));
      } // if

      if (count ($pDATALIST) == 0) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if

      foreach ($pDATALIST as $key => $ID) {
        $checkcriteria = array ("Identifier" => $ID,
                                "messageLabels_tID" => $gLABELVALUE);
        $this->messageLabelList->SelectByMultiple ($checkcriteria); 
        $this->messageLabelList->FetchArray ();

        if ( ($this->messageLabelList->CountResult () == 0) and
             ($labelaction == 'a') ) {
          // Add the label.
          $this->messageLabelList->Identifier = $ID;
          $this->messageLabelList->messageLabels_tID = $gLABELVALUE;
          $this->messageLabelList->Add ();
        } elseif ($labelaction == 'r') {
          // Remove the label.
          $this->messageLabelList->Delete ();
        } // if
      } // foreach
      if ($pDATALIST) $gSELECTBUTTON = 'select_none';

      $this->messageLabels->Select ("tID", $gLABELVALUE);
      $this->messageLabels->FetchArray ();

      global $gAPPLYLABELNAME; 

      $gAPPLYLABELNAME = $this->messageLabels->Label;
           
      $zSTRINGS->Lookup ('MESSAGE.APPLY');

      $this->messageLabels->Message = $zSTRINGS->Output;

      unset ($gLABELVALUE);
      unset ($gAPPLYLABELNAME);
    } // AddLabelToList

    function CreateFullLabelMenu () {

      global $zFOCUSUSER, $zSTRINGS;
      global $gLABELVALUE;

      $this->messageLabels->Select ("userAuth_uID", $zFOCUSUSER->uID, "Label ASC");
  
      $applyarray = array ();
      $removearray = array ();

      // Create the list of available labels.
      if ($this->messageLabels->CountResult () == 0) {

      } else {

        $foundnewlabels = TRUE;

        $zSTRINGS->Lookup ("LABEL.APPLY", "USER.MESSAGES.LABELS");

        // Start the menu list at '1'.
        $applyarray = array ("X" => MENU_DISABLED . $zSTRINGS->Output);

        $zSTRINGS->Lookup ("LABEL.REMOVE", "USER.MESSAGES.LABELS");
        $removearray = array ("Z" => MENU_DISABLED . $zSTRINGS->Output);

        $gLABELVALUE = 'X';

        // Loop through the list of labels.
        while ($this->messageLabels->FetchArray ()) {
          $applyarray['a' . $this->messageLabels->tID] = "&nbsp; " . $this->messageLabels->Label;
          $removearray['r' . $this->messageLabels->tID] = "&nbsp; " . $this->messageLabels->Label;
        } // while
        $returnarray = array_merge ($applyarray, $removearray);
      } // if

      $gLABELVALUE = 'X';

      return ($returnarray);

    } // CreateFullLabelMenu

    // Buffer the label menu for a specific message.
    function CreateSpecificLabelMenu () {

      global $zFOCUSUSER, $zSTRINGS;
      global $gLABELVALUE;

      $excludelist = array ();

      // Select the labels which are attached to this message.
      $this->messageLabelList->Select ("Identifier", $this->Identifier);

      $sort = "Label ASC";

      // NOTE: A JOIN statement would be faster.

      if ($this->messageLabelList->CountResult () == 0) {
        // Select all labels.
        $labelcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID);   
        $this->messageLabels->SelectByMultiple ($labelcriteria, "Label", $sort);

      } else {
        // Exclude found labels.
        while ($this->messageLabelList->FetchArray ()) {
          array_push ($excludelist, $this->messageLabelList->messageLabels_tID);
        } // while
        $excludestring = join (" AND tID <>", $excludelist);
        $excludestring = "userAuth_uID = $zFOCUSUSER->uID " .
                         "AND tID <>" . $excludestring;
        $this->messageLabels->SelectWhere ($excludestring, $sort);
      } // if
  
      $returnarray = array ();

      // Create the list of available labels.
      if ($this->messageLabels->CountResult () == 0) {

      } else {

        $foundnewlabels = TRUE;

        $zSTRINGS->Lookup ('LABEL.APPLY', 'USER.MESSAGES.LABELS');

        // Start the menu list at '1'.
        $returnarray = array ("X" => MENU_DISABLED . $zSTRINGS->Output);

        $gLABELVALUE = 'X';

        // Loop through the list of labels.
        while ($this->messageLabels->FetchArray ()) {
          $returnarray[$this->messageLabels->tID] = "&nbsp; " . $this->messageLabels->Label;
        } // while

      } // if

      // Create the list of removable labels.
      if (count ($excludelist) == 0) {
      } else {
        
        $zSTRINGS->Lookup ('LABEL.REMOVE');

        if ($foundnewlabels) {
          $returnarray["Y"] = MENU_DISABLED . "&nbsp;";
        } // if

        $returnarray["Z"] = MENU_DISABLED . $zSTRINGS->Output;

        $removestring = join (" OR tID =", $excludelist);
        $removestring = "tID =" . $removestring;
        $this->messageLabels->SelectWhere ($removestring, $sort);

        while ($this->messageLabels->FetchArray ()) {
          $returnarray[$this->messageLabels->tID] = "&nbsp; " . $this->messageLabels->Label;
        } // while

      } // if

      if ($foundnewlabels) {
        $gLABELVALUE = 'X';
      } else {
        $gLABELVALUE = 'Z';
      } // if 


      return ($returnarray);

    } // CreateSpecificLabelMenu

    function RetrieveMessage () {
      global $zXML, $zAPPLE, $zFOCUSUSER;

      // Message is a remote notification.
      $this->messageNotification->Select ("Identifier", $this->Identifier);
      $this->messageNotification->FetchArray ();

      // Retrieve message data.
      $zREMOTE = new cREMOTE ($this->messageNotification->Sender_Domain);
      $datalist = array ("gACTION"          => "MESSAGE_RETRIEVE",
                         "gSENDER_USERNAME" => $zFOCUSUSER->Username,
                         "gIDENTIFIER"      => $this->messageNotification->Identifier);
      $zREMOTE->Post ($datalist);

      $zXML->Parse ($zREMOTE->Return);

      $errorcode = $zXML->GetValue ("code", 0);
      $body = $zXML->GetValue ("body", 0);

      //  NOTE: It's important to have good error checking for remote messages.
      switch ($errorcode) {
        case 3000:
          $this->Error = -1;
          $this->Message = "This message could not be retrieved.";
          return (FALSE);
        break;
        default:
          $this->tID = $this->messageNotification->tID;
          $this->Subject = $zAPPLE->Purifier->Purify ($zXML->GetValue ("subject", 0));
          $this->Body = html_entity_decode ($zAPPLE->Purifier->Purify ( $zXML->GetValue ("body", 0)));
          $this->Stamp = ucwords ($zXML->GetValue ("stamp", 0));
          $this->Identifier = $this->messageNotification->Identifier;
          $this->Location = $this->messageNotification->Location;
          $this->FormatDate ("Stamp");
          $this->Sender_Username = $this->messageNotification->Sender_Username;
          $this->Sender_Domain = $this->messageNotification->Sender_Domain;
        break;
      } // switch

      return (TRUE);
    } // RetrieveMessage

    function MoveToInbox () {
      global $zSTRINGS, $zFOCUSUSER;

      // Check if user owns this message.
      if ($this->CheckReadAccess () == FALSE) {
        global $gMESSAGEID;
        $gMESSAGEID = $this->tID;
        $zSTRINGS->Lookup ('ERROR.ACCESS');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if 

      $classlocation = $this->LocateMessage ($this->Identifier);

      switch ($classlocation) {
        case 'messageInformation':
          // Select existing message with this Identifier.
          $this->messageInformation->Select ("Identifier", $this->Identifier);
          $this->messageInformation->FetchArray ();
          $this->messageInformation->Location = FOLDER_INBOX;
          $this->messageInformation->Update ();
        break;
      } // switch

      $zSTRINGS->Lookup ('MESSAGE.INBOX');
      $this->Message = $zSTRINGS->Output;

    } // MoveToInbox

    function MoveToArchive () {
      global $zSTRINGS, $zFOCUSUSER;

      // Check if user owns this message.
      if ($this->CheckReadAccess () == FALSE) {
        global $gMESSAGEID;
        $gMESSAGEID = $this->tID;
        $zSTRINGS->Lookup ('ERROR.ACCESS');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if 

      $classlocation = $this->LocateMessage ($this->Identifier);

      switch ($classlocation) {
        case 'messageNotification':
          // Remote message
          $this->SaveMessage (FOLDER_ARCHIVE);
        break;
        case 'messageInformation':
          // Select existing message with this Identifier.
          $this->messageInformation->Select ("Identifier", $this->Identifier);
          $this->messageInformation->FetchArray ();
          $this->messageInformation->Location = FOLDER_ARCHIVE;
          $this->messageInformation->Update ();
        break;
      } // switch

      $zSTRINGS->Lookup ('MESSAGE.ARCHIVE');
      $this->Message = $zSTRINGS->Output;

    } // MoveToArchive

    function ReportSpam () {
      global $zSTRINGS, $zFOCUSUSER;

      // Check if user owns this message.
      if ($this->CheckReadAccess () == FALSE) {
        global $gMESSAGEID;
        $gMESSAGEID = $this->tID;
        $zSTRINGS->Lookup ('ERROR.ACCESS');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if 

      $classlocation = $this->LocateMessage ($this->Identifier);

      switch ($classlocation) {
        case 'messageNotification':
          // Select existing message with this Identifier.
          $this->messageNotification->Select ("Identifier", $this->Identifier);
          $this->messageNotification->FetchArray ();
          $this->messageNotification->Location = FOLDER_SPAM;
          $this->messageNotification->Update ();
          // Remote message
        break;
        case 'messageInformation':
          // Select existing message with this Identifier.
          $this->messageInformation->Select ("Identifier", $this->Identifier);
          $this->messageInformation->FetchArray ();
          $this->messageInformation->Location = FOLDER_SPAM;
          $this->messageInformation->Update ();
        break;
      } // switch

      $zSTRINGS->Lookup ('MESSAGE.SPAM');
      $this->Message = $zSTRINGS->Output;

    } // ReportSpam

    function NotSpam () {
      global $zSTRINGS, $zFOCUSUSER;

      // Check if user owns this message.
      if ($this->CheckReadAccess () == FALSE) {
        global $gMESSAGEID;
        $gMESSAGEID = $this->tID;
        $zSTRINGS->Lookup ('ERROR.ACCESS');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if 

      $classlocation = $this->LocateMessage ($this->Identifier);

      switch ($classlocation) {
        case 'messageNotification':
          // Select existing message with this Identifier.
          $this->messageNotification->Select ("Identifier", $this->Identifier);
          $this->messageNotification->FetchArray ();
          $this->messageNotification->Location = FOLDER_INBOX;
          $this->messageNotification->Update ();
          // Remote message
        break;
        case 'messageInformation':
          // Select existing message with this Identifier.
          $this->messageInformation->Select ("Identifier", $this->Identifier);
          $this->messageInformation->FetchArray ();
          $this->messageInformation->Location = FOLDER_ARCHIVE;
          $this->messageInformation->Update ();
        break;
      } // switch

      $zSTRINGS->Lookup ('MESSAGE.NOTSPAM');
      $this->Message = $zSTRINGS->Output;

    } // NotSpam

    function SaveMessage ($pLOCATION) {
      global $zSTRINGS, $zFOCUSUSER;

      // Download Remote message

      // Select any possible existing messages with this Identifier.
      $this->messageInformation->Select ("Identifier", $this->Identifier);

      // Check if a body exists.
      if (!$this->Body) {
        // No message could be retrieved, replace body with error message.
        $this->messageNotification->Select ("Identifier", $this->Identifier);
        $this->messageNotification->FetchArray ();
        $this->messageInformation->userAuth_uID = $zFOCUSUSER->uID;
        $this->messageInformation->Sender_Username = $this->messageNotification->Sender_Username;
        $this->messageInformation->Sender_Domain = $this->messageNotification->Sender_Domain;
        $this->messageInformation->Identifier = $this->messageNotification->Identifier;
        $this->messageInformation->Subject = $this->messageNotification->Subject;
        $zSTRINGS->Lookup ('ERROR.RETRIEVE');
        $this->messageInformation->Body = $zSTRINGS->Output;
        $this->messageInformation->Sent_Stamp = $this->messageNotification->Stamp;
        $this->messageInformation->Received_Stamp = SQL_NOW;
        $this->messageInformation->Standing = $this->messageNotification->Standing;
        $this->messageInformation->Location = $pLOCATION;
      } else {
        // Store information.
        $this->messageInformation->userAuth_uID = $zFOCUSUSER->uID;
        $this->messageInformation->Sender_Username = $this->Sender_Username;
        $this->messageInformation->Sender_Domain = $this->Sender_Domain;
        $this->messageInformation->Identifier = $this->Identifier;
        $this->messageInformation->Subject = $this->Subject;
        $this->messageInformation->Body = $this->Body;
        $this->messageInformation->Sent_Stamp = $this->Stamp;
        $this->messageInformation->Received_Stamp = SQL_NOW;
        $this->messageInformation->Standing = $this->Standing;
        $this->messageInformation->Location = $pLOCATION;
      } // if

      if ($this->messageInformation->CountResult() == 0) {
        // Add new archived message.
        $this->messageInformation->Add ();
      } else {
        // Update existing archived message.
        $this->messageInformation->Update ();
      } // if

      // Delete old notification.
      $this->messageNotification->Select ("Identifier", $this->Identifier);
      $this->messageNotification->FetchArray ();
      $this->messageNotification->Delete ();

      return (TRUE);
    } // SaveMessage

    function MoveToTrash () {
      global $zSTRINGS, $zFOCUSUSER;

      // Check if user owns this message.
      if ($this->CheckReadAccess () == FALSE) {
        global $gMESSAGEID;
        $gMESSAGEID = $this->tID;
        $zSTRINGS->Lookup ('ERROR.ACCESS');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if 

      $classlocation = $this->LocateMessage ($this->Identifier);

      switch ($classlocation) {
        case 'messageNotification':
          $this->SaveMessage (FOLDER_TRASH);
        break;
        case 'messageInformation':
          // Select existing message with this Identifier.
          $this->messageInformation->Select ("Identifier", $this->Identifier);
          $this->messageInformation->FetchArray ();
          $this->messageInformation->Location = FOLDER_TRASH;
          $this->messageInformation->Update ();
        break;
      } // switch

      $zSTRINGS->Lookup ('MESSAGE.TRASH');
      $this->Message = $zSTRINGS->Output;

    } // MoveToTrash

    function MoveListToTrash ($pDATALIST) {

      global $zSTRINGS;

      if (count ($pDATALIST) == 0) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if

      foreach ($pDATALIST as $key => $id) {

        // Select the message in question.
        $this->SelectMessage ($id);

        // Check if user owns this message.
        if ($this->CheckReadAccess () == FALSE) {
          global $gMESSAGEID;
          $gMESSAGEID = $id;
          $zSTRINGS->Lookup ('ERROR.ACCESS');
          $this->Message = $zSTRINGS->Output;
          $this->Error = -1;
          continue;
        } // if 

        $this->MoveToTrash ();
      } // if

      $zSTRINGS->Lookup ('MESSAGE.TRASHALL');
      $this->Message = $zSTRINGS->Output;

      return (TRUE);

    } // MoveListToTrash

    function MoveListToArchive ($pDATALIST) {

      global $zSTRINGS;

      if (count ($pDATALIST) == 0) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if

      foreach ($pDATALIST as $key => $id) {

        // Select the message in question.
        $this->SelectMessage ($id);

        // Check if user owns this message.
        if ($this->CheckReadAccess () == FALSE) {
          global $gMESSAGEID;
          $gMESSAGEID = $id;
          $zSTRINGS->Lookup ('ERROR.ACCESS');
          $this->Message = $zSTRINGS->Output;
          $this->Error = -1;
          continue;
        } // if 

        $this->MoveToArchive ();
      } // if

      $zSTRINGS->Lookup ('MESSAGE.ARCHIVEALL');
      $this->Message = $zSTRINGS->Output;

      return (TRUE);

    } // MoveListToArchive

    function MoveListToInbox ($pDATALIST) {

      global $zSTRINGS;

      if (count ($pDATALIST) == 0) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if

      foreach ($pDATALIST as $key => $id) {

        // Select the message in question.
        $this->SelectMessage ($id);

        // Check if user owns this message.
        if ($this->CheckReadAccess () == FALSE) {
          global $gMESSAGEID;
          $gMESSAGEID = $id;
          $zSTRINGS->Lookup ('ERROR.ACCESS');
          $this->Message = $zSTRINGS->Output;
          $this->Error = -1;
          continue;
        } // if 

        $this->MoveToInbox ();
      } // if

      $zSTRINGS->Lookup ('MESSAGE.INBOXALL');
      $this->Message = $zSTRINGS->Output;

      return (TRUE);

    } // MoveListToInbox

    function ReportListAsSpam ($pDATALIST) {

      global $zSTRINGS;

      if (count ($pDATALIST) == 0) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if

      foreach ($pDATALIST as $key => $id) {

        // Select the message in question.
        $this->SelectMessage ($id);

        // Check if user owns this message.
        if ($this->CheckReadAccess () == FALSE) {
          global $gMESSAGEID;
          $gMESSAGEID = $id;
          $zSTRINGS->Lookup ('ERROR.ACCESS');
          $this->Message = $zSTRINGS->Output;
          $this->Error = -1;
          continue;
        } // if 

        $this->ReportSpam ();
      } // if

      $zSTRINGS->Lookup ('MESSAGE.SPAMALL');
      $this->Message = $zSTRINGS->Output;

      return (TRUE);

    } // ReportListAsSpam


    function Send ($pSENDERUSERNAME = NULL) {
      global $gRECIPIENTADDRESS;
      global $gRECIPIENTNAME, $gRECIPIENTDOMAIN, $gSITEDOMAIN;

      global $zAPPLE, $zSTRINGS;

      if ($gRECIPIENTADDRESS) {
        // Step 1: Check if address is valid.
        if (!$zAPPLE->CheckEmail ($gRECIPIENTADDRESS)) {
          $zSTRINGS->Lookup ("ERROR.UNABLE");
          $this->Message = $zSTRINGS->Output;
          $zSTRINGS->Lookup ("ERROR.INVALID");
          $this->Errorlist['recipientaddress'] = $zSTRINGS->Output;
          $this->Error = -1;
          return (FALSE);
        } // if

        // Step 2: Check if user exists.
        list ($gRECIPIENTNAME, $gRECIPIENTDOMAIN) = split ('\@', $gRECIPIENTADDRESS);
        if (!$zAPPLE->GetUserInformation ($gRECIPIENTNAME, $gRECIPIENTDOMAIN)) {
          $this->Error = -1;
          $zSTRINGS->Lookup ("ERROR.UNABLE");
          $this->Message = $zSTRINGS->Output;
          $zSTRINGS->Lookup ("ERROR.UNKNOWN");
          $this->Errorlist['recipientaddress'] = $zSTRINGS->Output;
          $this->Error = -1;
          return (FALSE);
        } // if

      } else {
        if (!$gRECIPIENTNAME) {
          return (FALSE);
        } // if
      } // if

      if ($gRECIPIENTDOMAIN != $gSITEDOMAIN) {
        $this->SendRemote ($pSENDERUSERNAME);
      } else {
        $this->SendLocal ($pSENDERUSERNAME);
      } // if

      return (TRUE);
    } // Send

    function SendLocal ($pSENDERUSERNAME = NULL) {
      global $gRECIPIENTNAME, $gRECIPIENTDOMAIN;
      global $gSUBJECT, $gBODY;
      global $gSITEDOMAIN;
      global $zFOCUSUSER, $zSTRINGS;
      global $gtID;

      global $zAPPLE;

      // Get the local recipient's User ID.
      $RECIPIENT = new cUSER ();
      $RECIPIENT->Select ("Username", $gRECIPIENTNAME);
      $RECIPIENT->FetchArray ();

      $gRECIPIENTNAME = $RECIPIENT->Username;
      $recipientfullname = $RECIPIENT->userProfile->GetAlias ();
      $gRECIPIENTID = $RECIPIENT->uID;

      if ($gSUBJECT == NULL) {
        $zSTRINGS->Lookup ('LABEL.NOSUBJECT');
        $gSUBJECT = $zSTRINGS->Output;
      } // if

      $identifier = $zAPPLE->RandomString (128);

      // Add the Recieved message.
      $this->messageInformation->userAuth_uID = $gRECIPIENTID;
      if ($pSENDERUSERNAME) {
        $this->messageInformation->Sender_Username = $pSENDERUSERNAME;
        list ($senderfullname, $null) = $zAPPLE->GetUserInformation ($pSENDERUSERNAME, $gSITEDOMAIN);
      } else {
        $this->messageInformation->Sender_Username = $zFOCUSUSER->Username;
        $senderfullname = $zFOCUSUSER->userProfile->GetAlias ();
      } // if
      $this->messageInformation->Sender_Domain = $gSITEDOMAIN;
      $this->messageInformation->Subject = $gSUBJECT;
      $this->messageInformation->Body = $gBODY;
      $this->messageInformation->Identifier = $identifier;
      $this->messageInformation->Received_Stamp = SQL_NOW;
      $this->messageInformation->Sent_Stamp = SQL_NOW;
      $this->messageInformation->Location = FOLDER_INBOX;
      $this->messageInformation->Standing = MESSAGE_UNREAD;

      $this->messageInformation->Add ();

      // Create the Sent copy.
      $this->messageStore->userAuth_uID = $zFOCUSUSER->uID;
      $this->messageStore->Sender_Username = $RECIPIENT->Username;
      $this->messageStore->Sender_Domain = $gSITEDOMAIN;
      $this->messageStore->Subject = $gSUBJECT;
      $this->messageStore->Body = $gBODY;
      $this->messageStore->Identifier = $identifier;
      $this->messageStore->Stamp = SQL_NOW;
      $this->messageStore->Location = FOLDER_SENT;
      $this->messageStore->Standing = MESSAGE_UNREAD;

      $this->messageStore->Add ();

      // If a message table ID is available, then delete draft.
      if ($gtID) {
        $this->messageStore->tID = $gtID;
        $this->messageStore->Delete ();
      } // if

      $zSTRINGS->Lookup ('MESSAGE.SENT');
      $this->Message = $zSTRINGS->Output;

      $this->NotifyMessage ($RECIPIENT->userProfile->Email, $gRECIPIENTNAME, $recipientfullname, $senderfullname);

      unset ($RECIPIENT);
      unset ($gRECIPIENTNAME);
      unset ($gRECIPIENTID);
    } // SendLocal

    function SendRemote ($pSENDERUSERNAME = NULL) {
      global $gRECIPIENTNAME, $gRECIPIENTDOMAIN;
      global $gSUBJECT, $gBODY;
      global $gSITEDOMAIN;
      global $zFOCUSUSER, $zSTRINGS;
      global $gtID;

      global $zAPPLE, $zREMOTE, $zXML;

      $identifier = $zAPPLE->RandomString (128);

      if ($pSENDERUSERNAME) {
        $senderusername = $pSENDERUSERNAME;
        $USER = new cUSER();
        $USER->Select ("Username", $pSENDERUSERNAME);
        $USER->FetchArray ();
        $senderfullname = $USER->userProfile->GetAlias ();
        unset ($USER);
      } else {
        $senderusername = $zFOCUSUSER->Username;
        $senderfullname =  $zFOCUSUSER->userProfile->GetAlias ();
      } // if
      // Send the notification. 
      $zREMOTE = new cREMOTE ($gRECIPIENTDOMAIN);
      $datalist = array ("gACTION"          => "MESSAGE_NOTIFY",
                         "gRECIPIENTNAME"   => $gRECIPIENTNAME,
                         "gSENDER_USERNAME" => $senderusername,
                         "gSENDER_FULLNAME" => $senderfullname,
                         "gSENDER_DOMAIN"   => $gSITEDOMAIN,
                         "gIDENTIFIER"      => $identifier,
                         "gSUBJECT"         => $gSUBJECT);
      $zREMOTE->Post ($datalist);

      $zXML->Parse ($zREMOTE->Return);

      $errorcode = $zXML->GetValue ("code", 0);
      $version = ucwords ($zXML->GetValue ("version", 0));
      $success = ucwords ($zXML->GetValue ("success", 0));

      if ( ($errorcode) or (!$version) or (!$success)) {
        $zSTRINGS->Lookup ("ERROR.UNABLE");
        $this->Message = $zSTRINGS->Output;
        return (FALSE);
      } // if

      // Store the message.
      $this->messageStore->userAuth_uID = $zFOCUSUSER->uID;
      $this->messageStore->Sender_Username = $gRECIPIENTNAME;
      $this->messageStore->Sender_Domain = $gRECIPIENTDOMAIN;
      $this->messageStore->Subject = $gSUBJECT;
      $this->messageStore->Body = $gBODY;
      $this->messageStore->Identifier = $identifier;
      $this->messageStore->Stamp = SQL_NOW;
      $this->messageStore->Location = FOLDER_SENT;
      $this->messageStore->Standing = MESSAGE_UNREAD;

      $this->messageStore->Add ();

      $zSTRINGS->Lookup ('MESSAGE.SENT');
      $this->Message = $zSTRINGS->Output;

      return (TRUE);

    } // SendRemote

    // Notify the user that a message has been sent.
    function NotifyMessage ($pEMAIL, $pRECIPIENTUSERNAME, $pRECIPIENTFULLNAME, $pSENDERNAME) {
      global $zSTRINGS, $zAPPLE;

      global $gSENDERNAME;
      $gSENDERNAME = $pSENDERNAME;

      global $gRECIPIENTFULLNAME;
      $gRECIPIENTFULLNAME = $pRECIPIENTFULLNAME;

      global $gMESSAGESURL, $gSITEURL;
      $gMESSAGESURL = $gSITEURL . "/profile/" . $pRECIPIENTUSERNAME . "/messages/";

      $to = $pEMAIL;

      $zSTRINGS->Lookup ('MAIL.SUBJECT', 'USER.MESSAGES');
      $subject = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.BODY', 'USER.MESSAGES');
      $body = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROM', 'USER.MESSAGES');
      $from = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROMNAME', 'USER.MESSAGES');
      $fromname = $zSTRINGS->Output;

      $zAPPLE->Mailer->From = $from;
      $zAPPLE->Mailer->FromName = $fromname;
      $zAPPLE->Mailer->Body = $body;
      $zAPPLE->Mailer->Subject = $subject;
      $zAPPLE->Mailer->AddAddress ($to);
      $zAPPLE->Mailer->AddReplyTo ($from);

      $zAPPLE->Mailer->Send();

      $zAPPLE->Mailer->ClearAddresses();
      
      unset ($to);
      unset ($subject);
      unset ($body);

      return (TRUE);

    } // NotifyMessage

    function SaveDraft () {
      global $zAPPLE, $zFOCUSUSER, $zSTRINGS;

      global $gIDENTIFIER;
      global $gRECIPIENTNAME, $gRECIPIENTDOMAIN, $gRECIPIENTADDRESS;
      global $gSUBJECT, $gBODY;
      global $gSITEDOMAIN;
      global $gtID;

      if ($gSUBJECT == NULL) {
        $zSTRINGS->Lookup ('LABEL.NOSUBJECT');
        $gSUBJECT = $zSTRINGS->Output;
      } // if

      if ($gRECIPIENTADDRESS) {
        list ($gRECIPIENTNAME, $gRECIPIENTDOMAIN) = split ('\@', $gRECIPIENTADDRESS);
      } // if

      $identifier = $zAPPLE->RandomString (128);

      // Create the Draft.
      $this->messageStore->userAuth_uID = $zFOCUSUSER->uID;
      $this->messageStore->Sender_Username = $gRECIPIENTNAME;
      $this->messageStore->Sender_Domain = $gRECIPIENTDOMAIN;
      $this->messageStore->Subject = $gSUBJECT;
      $this->messageStore->Body = $gBODY;
      $this->messageStore->Identifier = $identifier;
      $this->messageStore->Stamp = SQL_NOW;
      $this->messageStore->Location = FOLDER_DRAFTS;
      $this->messageStore->Standing = MESSAGE_UNREAD;

      if ($gIDENTIFIER) {
        $MESSAGE = new cMESSAGESTORE ();
        $MESSAGE->Select ("Identifier", $gIDENTIFIER);
        $MESSAGE->FetchArray ();
        $this->messageStore->tID = $MESSAGE->tID;
        $this->messageStore->Identifier = SQL_SKIP;
        $this->messageStore->Update ();
        unset ($MESSAGE);
      } else {
        $this->messageStore->Add ();
      } // if

      $zSTRINGS->Lookup ('MESSAGE.SAVED');
      $this->Message = $zSTRINGS->Output;

      unset ($RECIPIENT);
      unset ($gRECIPIENTNAME);
      unset ($gRECIPIENTID);

    } // SaveDraft

    function DeleteForever () {

      global $zSTRINGS;

      // Check if user owns this message.
      if ($this->CheckReadAccess () == FALSE) {
        global $gMESSAGEID;
        $gMESSAGEID = $this->tID;
        $zSTRINGS->Lookup ('ERROR.ACCESS');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if 

      $classlocation = $this->LocateMessage ($this->Identifier);
      $this->$classlocation->tID = $this->tID;
      $this->$classlocation->Delete ();

      $zSTRINGS->Lookup ('MESSAGE.DELETE');
      $this->Message = $zSTRINGS->Output;

      return (TRUE);

    } // DeleteForever

    function DeleteListForever ($pDATALIST) {

      global $zSTRINGS;

      if (count ($pDATALIST) == 0) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED');
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if

      foreach ($pDATALIST as $key => $id) {

        $this->SelectMessage ($id);
        $this->DeleteForever ();

      } // foreach

      global $gMESSAGECOUNT;

      $gMESSAGECOUNT = count ($pDATALIST);
      $zSTRINGS->Lookup ('MESSAGE.DELETEALL');
      $this->Message = $zSTRINGS->Output;

      unset ($gMESSAGECOUNT);

      return (TRUE);

    } // DeleteForever

    // Create the label list buffer.
    function BufferLabelList () {

      global $gLABELNAME;
      global $gLABELSELECT; global $gCOUNTNEWMESSAGES;
      global $gFRAMELOCATION, $gPROFILESUBACTION, $gLABELSELECT;
      global $gLABELNAME;

      global $zFOCUSUSER, $zSTRINGS, $zAPPLE;

      $labelcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID);   
      $this->messageLabels->SelectByMultiple ($labelcriteria, "Label");

      // Check if any labels were found.
      if ($this->messageLabels->CountResult () == 0) {

        // None found.  Output an error.
        $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.MESSAGES.LABELS');
        $this->messageLabelList->Message = $zSTRINGS->Output;

        return (NULL);

      } else {

        $output = "";

        // Buffer the labels list.
        ob_start ();  
    
        $labelcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID);   
        $this->messageLabels->SelectByMultiple ($labelcriteria, "Label");
  
        // Loop through the list of labels.
        while ($this->messageLabels->FetchArray ()) {

          // Push the label name into a global variable.
          $gLABELNAME = $this->messageLabels->Label;
      
          // Count the number of new messages.
          $this->CountNewInLabels ($this->messageLabels->tID);
   
          // Determine whether a label has been selected or not.
          if ($gPROFILESUBACTION == $gLABELNAME) {
            $gLABELSELECT[$gLABELNAME] = 'selected';
          } else {
            $gLABELSELECT[$gLABELNAME] = 'normal';
          } // if
     
          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/messages/label.aobj", INCLUDE_SECURITY_NONE);
        } // while
  
        $output = ob_get_clean();
    
      } // if

      return ($output);

    } // bufferLabelList

    // Count new messages for each label.
    function CountNewInLabels ($pLABELID) {
      global $zFOCUSUSER, $zAPPLE, $zHTML, $zSTRINGS;

      global $gFRAMELOCATION, $gLABELNAME, $gPROFILESUBACTION; 

      global $gCOUNTNEWMESSAGES;

      global $gTARGET, $gSCROLLSTEP, $gSCROLLMAX, $gSORT;

      global $gLABELDATA;

      global $gMESSAGESTAMP, $gMESSAGESTANDING;

      global $gACTION, $gCHECKED;

      global $gSENDERNAME, $gSENDERONLINE;

      $NotificationTable = $this->messageNotification->TableName;
      $InformationTable = $this->messageInformation->TableName;

      $query  = "(SELECT COUNT($NotificationTable.tID) AS CountResult " .
                " FROM   $NotificationTable, messageLabelList " .
                " WHERE  messageLabelList.Identifier = $NotificationTable.Identifier " .
                " AND    $NotificationTable.Standing = " . MESSAGE_UNREAD . " " .
                " AND    $NotificationTable.Location != " . FOLDER_SPAM . " " .
                " AND    $NotificationTable.Location != " . FOLDER_TRASH . " " .
                " AND    $NotificationTable.userAuth_uID = " . $zFOCUSUSER->uID . " " .
                " AND    messageLabelList.messageLabels_tID = " . $pLABELID . ")";

      $this->Query ($query);
      $this->FetchArray();
      $total = $this->CountResult;
      $this->CountResult = 0;

      $query = "(SELECT COUNT($InformationTable.tID) AS CountResult " .
               " FROM   $InformationTable, messageLabelList " .
               " WHERE  messageLabelList.Identifier = $InformationTable.Identifier " .
               " AND    $InformationTable.Standing = " . MESSAGE_UNREAD . " " .
               " AND    $InformationTable.userAuth_uID = " . $zFOCUSUSER->uID . " " .
               " AND    $InformationTable.Location != " . FOLDER_SPAM . " " .
               " AND    $InformationTable.Location != " . FOLDER_TRASH . " " .
               " AND    messageLabelList.messageLabels_tID = " . $pLABELID . ")";

      $this->Query ($query);
      $this->FetchArray();
      $total += $this->CountResult;

      if ($total == 0) {
        $gCOUNTNEWMESSAGES = "";
      } else {
        $gCOUNTNEWMESSAGES = '(' . $total . ')';
      } // if 

      return (TRUE);
 
    } // CountNewInLabels

  } // cMESSAGE

  // Message information class.
  class cMESSAGEINFORMATION extends cDATACLASS {

    var $tID, $userAuth_uID, $Sender_Username, $Sender_Domain, 
        $Subject, $Body, $Received_Stamp, $Sent_Stamp,
        $Location, $Standing;
    var $Cascade;

    function cMESSAGEINFORMATION ($pDEFAULTCONTEXT = '') {
      $this->TableName = 'messageInformation';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Sender_Username = '';
      $this->Sender_Domain = '';
      $this->Subject = '';
      $this->Body = '';
      $this->Sent_Stamp = '';
      $this->Received_Stamp = '';
      $this->Standing = '';
      $this->Location = '';
      $this->PageContext = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->FieldNames = '';
      $this->PrimaryKey = 'tID';
      $this->Cascade = '';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

       'Sender_Username' => array ('max'        => '32',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

         'Sender_Domain' => array ('max'        => '64',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Subject'        => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Body'           => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

        'Sent_Stamp'     => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),

        'Received_Stamp' => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),

        'Standing'       => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Location'       => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),
      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor

  } // cMESSAGEINFORMATION

  // Message Label List class.
  class cMESSAGELABELLIST extends cDATACLASS {

    var $tID, $messageLabels_tID, $messageContent_tID;

    function cMESSAGELABELLIST ($pDEFAULTCONTEXT = '') {
      $this->TableName = 'messageLabelList';
      $this->tID = '';
      $this->messageLabels_tID = '';
      $this->messageContent_tID = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'messageContent_tID';
 
      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;
 
      // Create extended field definitions
      $this->FieldDefinitions = array (
        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

  'messageContent_tID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

   'messageLabels_tID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),
      );

      // Grab the fields from the database.
      $this->Fields();

    } // Constructor

    // Count messages for label.
    function CountInLabel ($pLABELID) {
     
      // Determine how many messages are attached to this label.
      $countcriteria = array ("messageLabels_tID" => $pLABELID);
      $this->SelectByMultiple ($countcriteria);

      $countresult = $this->CountResult ();

      return ($countresult);

    } // CountInLabels

  } // cMESSAGELABELLIST

  // Message labels class.
  class cMESSAGELABELS extends cDATACLASS {

    var $tID, $userAuth_uID, $Label;

    function cMESSAGELABELS ($pDEFAULTCONTEXT = '') {

      $this->TableName = 'messageLabels';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Label = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'messageContent_tID';
 
      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;
 
      // Create extended field definitions
      $this->FieldDefinitions = array (
        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Label'          => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => ', .',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

      );

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor

    function LoadLabels () {
      global $zFOCUSUSER;

      $this->Select ("userAuth_uID", $zFOCUSUSER->uID);

      return (TRUE);

    } // LoadLabels

  } // cMESSAGELABELS

  // Message notification class.
  class cMESSAGENOTIFICATION extends cDATACLASS {

    var $tID, $userAuth_uID, $Sender_Username, $Sender_Domain, 
        $userIcons_Filename, $Subject, $Identifier, $Stamp, $Standing;
    var $Cascade;

    function cMESSAGENOTIFICATION ($pDEFAULTCONTEXT = '') {
      $this->TableName = 'messageNotification';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Sender_Username = '';
      $this->Sender_Domain = '';
      $this->Identifier = '';
      $this->Subject = '';
      $this->Stamp = '';
      $this->PageContext = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->FieldNames = '';
      $this->PrimaryKey = 'tID';
      $this->Cascade = '';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

       'Sender_Username' => array ('max'        => '32',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

         'Sender_Domain' => array ('max'        => '64',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Subject'        => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Identifier'     => array ('max'        => '128',
                                   'min'        => '128',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Stamp'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),

        'Standing'       => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Location'       => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),
      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor

  } // cMESSAGENOTIFICATION

  // Message store class.
  class cMESSAGESTORE extends cDATACLASS {

    var $tID, $userAuth_uID, $Sender_Username, $Sender_Domain, 
        $userIcons_Filename, $Subject, $Identifier, $Stamp;
    var $Cascade;

    function cMESSAGESTORE ($pDEFAULTCONTEXT = '') {
      $this->TableName = 'messageStore';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Sender_Username = '';
      $this->Sender_Domain = '';
      $this->Identifier = '';
      $this->Subject = '';
      $this->Stamp = '';
      $this->PageContext = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->FieldNames = '';
      $this->PrimaryKey = 'tID';
      $this->Cascade = '';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

       'Sender_Username' => array ('max'        => '32',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

         'Sender_Domain' => array ('max'        => '64',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Subject'        => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Identifier'     => array ('max'        => '128',
                                   'min'        => '128',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Stamp'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),
      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
  } // cMESSAGESTORE
?>
