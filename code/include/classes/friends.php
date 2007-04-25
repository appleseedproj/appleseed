<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: friends.php                             CREATED: 01-29-2006 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 01-29-2006 +
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
  // | DESCRIPTION.  Friends class definitions.                          |
  // +-------------------------------------------------------------------+

  // Friend information class.
  class cFRIENDINFORMATION extends cDATACLASS {

    var $tID, $userAuth_uID, $sID, $Username, $Domain, 
        $Verification, $Alias;
    var $InformationCache;
    var $Cascade;

    function cFRIENDINFORMATION ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'friendInformation';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->sID = '';
      $this->Username = '';
      $this->Domain = '';
      $this->Verification = '';
      $this->Alias = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'userAuth_uID';
      $this->Cascade = NULL;
      $this->InformationCache = array ();
 
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

        'sID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Username'       => array ('max'        => '32',
                                   'min'        => '4',
                                   'illegal'    => ', .',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Domain'         => array ('max'        => '128',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DOMAIN'),

        'Verification'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Alias'          => array ('max'        => '128',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

      );

      // Internal class references.
      $this->friendCircles      = new cFRIENDCIRCLES;
      $this->friendCirclesList  = new cFRIENDCIRCLESLIST;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor

    // Notify the user that a friend request has been made.
    function NotifyApproval ($pEMAIL, $pREQUESTEDUSER, $pREQUESTUSER, $pREQUESTEDUSERNAME, $pREQUESTEDDOMAIN = NULL) {
      global $zSTRINGS, $zAPPLE;

      global $gREQUESTUSER, $gREQUESTEDUSER;
      $gREQUESTUSER = $pREQUESTUSER;
      $gREQUESTEDUSER = $pREQUESTEDUSER;

      global $gFRIENDSURL, $gSITEURL;
      if ($pREQUESTEDDOMAIN) {
        $gFRIENDSURL = "http://" . $pREQUESTEDDOMAIN . "/profile/" . $pREQUESTEDUSERNAME . "/friends/";
      } else {
        $gFRIENDSURL = $gSITEURL . "/profile/" . $pREQUESTEDUSERNAME . "/friends/";
      } // if

      $to = $pEMAIL;

      $zSTRINGS->Lookup ('MAIL.SUBJECT', 'USER.FRIENDS.APPROVE');
      $subject = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.BODY', 'USER.FRIENDS.APPROVE');
      $body = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROM', 'USER.FRIENDS.APPROVE');
      $from = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROMNAME', 'USER.FRIENDS.APPROVE');
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

    } // NotifyApproval

    // Notify the user that a friend has been deleted.
    function NotifyDelete ($pEMAIL, $pREQUESTEDUSER, $pREQUESTUSER, $pREQUESTEDUSERNAME) {
      global $zSTRINGS, $zAPPLE;

      global $gREQUESTUSER, $gREQUESTEDUSER;
      $gREQUESTUSER = $pREQUESTUSER;
      $gREQUESTEDUSER = $pREQUESTEDUSER;

      global $gFRIENDSURL, $gSITEURL;
      $gFRIENDSURL = $gSITEURL . "/profile/" . $pREQUESTEDUSERNAME . "/friends/";

      $to = $pEMAIL;

      $zSTRINGS->Lookup ('MAIL.SUBJECT', 'USER.FRIENDS.DELETE');
      $subject = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.BODY', 'USER.FRIENDS.DELETE');
      $body = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROM', 'USER.FRIENDS.DELETE');
      $from = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROMNAME', 'USER.FRIENDS.DELETE');
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

    } // NotifyDelete

    // Notify the user that a friend request has been denied.
    function NotifyDenial ($pEMAIL, $pREQUESTEDUSER, $pREQUESTUSER, $pREQUESTEDUSERNAME) {
      global $zSTRINGS, $zAPPLE;

      global $gREQUESTUSER, $gREQUESTEDUSER;
      $gREQUESTUSER = $pREQUESTUSER;
      $gREQUESTEDUSER = $pREQUESTEDUSER;

      global $gFRIENDSURL, $gSITEURL;
      $gFRIENDSURL = $gSITEURL . "/profile/" . $pREQUESTEDUSERNAME . "/friends/";

      $to = $pEMAIL;

      $zSTRINGS->Lookup ('MAIL.SUBJECT', 'USER.FRIENDS.DENY');
      $subject = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.BODY', 'USER.FRIENDS.DENY');
      $body = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROM', 'USER.FRIENDS.DENY');
      $from = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROMNAME', 'USER.FRIENDS.DENY');
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

    } // NotifyDenial

    // Notify the user that a friend request has been made.
    function NotifyRequest ($pEMAIL, $pREQUESTEDUSER, $pREQUESTUSER, $pREQUESTEDUSERNAME, $pREQUESTEDDOMAIN = NULL) {
      global $zSTRINGS, $zAPPLE;

      global $gREQUESTUSER, $gREQUESTEDUSER;
      $gREQUESTUSER = $pREQUESTUSER;
      $gREQUESTEDUSER = $pREQUESTEDUSER;

      global $gFRIENDSURL, $gSITEURL;
      if ($pREQUESTEDDOMAIN) {
        $gFRIENDSURL = "http://" . $pREQUESTEDDOMAIN . "/profile/" . $pREQUESTEDUSERNAME . "/friends/requests/";
      } else {
        $gFRIENDSURL = $gSITEURL . "/profile/" . $pREQUESTEDUSERNAME . "/friends/requests/";
      } // if

      $to = $pEMAIL;

      $zSTRINGS->Lookup ('MAIL.SUBJECT', 'USER.FRIENDS.REQUEST');
      $subject = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.BODY', 'USER.FRIENDS.REQUEST');
      $body = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROM', 'USER.FRIENDS.REQUEST');
      $from = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROMNAME', 'USER.FRIENDS.REQUEST');
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

    } // NotifyRequest

    function Circle () {
      global $gCIRCLEVALUE, $gtID;
      global $zSTRINGS;

      $this->Select ("tID", $gtID);
      $this->FetchArray ();

      $checkcriteria = array ("friendInformation_tID" => $gtID,
                              "friendCircles_tID" => $gCIRCLEVALUE);
      $this->friendCirclesList->SelectByMultiple ($checkcriteria); 
      $this->friendCirclesList->FetchArray ();

      if ($this->friendCirclesList->CountResult () == 0) {
        $this->friendCirclesList->friendInformation_tID = $gtID;
        $this->friendCirclesList->friendCircles_tID = $gCIRCLEVALUE;
        $this->friendCirclesList->Add ();

        $this->friendCircles->Select ("tID", $gCIRCLEVALUE);
        $this->friendCircles->FetchArray ();

        global $gAPPLYCIRCLENAME; 

        $gAPPLYCIRCLENAME = $this->friendCircles->Name;
         
        $zSTRINGS->Lookup ("MESSAGE.APPLY", $this->PageContext);

        $this->friendCircles->Message = $zSTRINGS->Output;
 
        unset ($gAPPLYCIRCLENAME);
  
      } else {
        $this->friendCirclesList->friendInformation_tID = $gtID;
        $this->friendCirclesList->friendCircles = $gCIRCLEVALUE;
        $this->friendCirclesList->Delete ();

        $this->friendCircles->Select ("tID", $gCIRCLEVALUE);
        $this->friendCircles->FetchArray ();

        global $gREMOVECIRCLENAME; 

        $gREMOVECIRCLENAME = $this->friendCircles->Name;
       
        $zSTRINGS->Lookup ("MESSAGE.REMOVE", $this->PageContext);

        $this->friendCircles->Message = $zSTRINGS->Output;

        unset ($gREMOVECIRCLENAME);
  
      } // if

      unset ($gCIRCLEVALUE);

    } // Circle

    function AddCircleToList ($pDATALIST) {
      global $gCIRCLEVALUE, $gSELECTBUTTON;
      global $zSTRINGS;

      if (count ($pDATALIST) == 0) {
        $zSTRINGS->Lookup ("ERROR.NONESELECTED", $this->PageContext);
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if

      foreach ($pDATALIST as $key => $ID) {
        $checkcriteria = array ("friendInformation_tID" => $ID,
                                "friendCircles_tID" => $gCIRCLEVALUE);
        $this->friendCirclesList->SelectByMultiple ($checkcriteria); 
        $this->friendCirclesList->FetchArray ();

        if ($this->friendCirclesList->CountResult () == 0) {
          $this->friendCirclesList->friendInformation_tID = $ID;
          $this->friendCirclesList->friendCircles_tID = $gCIRCLEVALUE;
          $this->friendCirclesList->Add ();
        } // if
      } // foreach
      if ($pDATALIST) $gSELECTBUTTON = 'select_none';

      $this->friendCircles->Select ("tID", $gCIRCLEVALUE);
      $this->friendCircles->FetchArray ();

      global $gAPPLYCIRCLENAME; 

      $gAPPLYCIRCLENAME = $this->friendCircles->Name;
           
      $zSTRINGS->Lookup ("MESSAGE.APPLY", $this->PageContext);

      $this->friendCircles->Message = $zSTRINGS->Output;

      unset ($gCIRCLEVALUE);
      unset ($gAPPLYCIRCLENAME);
    } // AddCircleToList

    // Return the circles that the current friend belongs to.
    function GetCircles () {

      $this->friendCirclesList->Select ("friendInformation_tID", $this->tID);
       if ($this->friendCirclesList->CountResult () == 0) {
         $circlearray = NULL;
       } else {
         $circlearray = array ();

         while ($this->friendCirclesList->FetchArray ()) {
           $this->friendCircles->Select ("tID", $this->friendCirclesList->friendCircles_tID);
           $this->friendCircles->FetchArray ();
           array_push ($circlearray, $this->friendCircles->Name);
         } // while

       } // if

       return ($circlearray);

    } // GetCircles

    function CreateFullCirclesMenu () {

      global $zFOCUSUSER, $zSTRINGS;
      global $gCIRCLEVALUE;

      $excludelist = array ();

      // Select the circles which are attached to this friend.
      $this->friendCirclesList->Select ("friendInformation_tID", $this->tID);

      $sort = "Name ASC";

      if ($this->friendCirclesList->CountResult () == 0) {
        // Select all circles.
        $circlecriteria = array ("userAuth_uID" => $zFOCUSUSER->uID);   
        $this->friendCircles->SelectByMultiple ($circlecriteria, "Name", $sort);

      } else {
        // Exclude found circles.
        while ($this->friendCirclesList->FetchArray ()) {
          array_push ($excludelist, $this->friendCirclesList->friendCircles_tID);
        } // while
        $excludestring = join (" AND tID <>", $excludelist);
        $excludestring = "userAuth_uID = $zFOCUSUSER->uID " .
                         "AND tID <>" . $excludestring;
        $this->friendCircles->SelectWhere ($excludestring, $sort);
      } // if
  
      $returnarray = array ();

      // Create the list of available circles.
      if ($this->friendCircles->CountResult () == 0) {

      } else {

        $foundnewcircles = TRUE;

        $zSTRINGS->Lookup ("LABEL.APPLY", $this->PageContext);

        // Start the menu list at '1'.
        $returnarray = array ("X" => MENU_DISABLED . $zSTRINGS->Output);

        $gCIRCLEVALUE = 'X';

        // Loop through the list of circles.
        while ($this->friendCircles->FetchArray ()) {
          $returnarray[$this->friendCircles->tID] = "&nbsp; " . $this->friendCircles->Name;
        } // while

      } // if

      // Create the list of removable circles.
      if (count ($excludelist) == 0) {
      } else {
        
        $zSTRINGS->Lookup ("LABEL.REMOVE", $this->PageContext);

        if ($foundnewcircles) {
          $returnarray["Y"] = MENU_DISABLED . "&nbsp;";
        } // if

        $returnarray["Z"] = MENU_DISABLED . $zSTRINGS->Output;

        $removestring = join (" OR tID =", $excludelist);
        $removestring = "tID =" . $removestring;
        $this->friendCircles->SelectWhere ($removestring, $sort);

        while ($this->friendCircles->FetchArray ()) {
          $returnarray[$this->friendCircles->tID] = "&nbsp; " . $this->friendCircles->Name;
        } // while

      } // if

      if ($foundnewcircles) {
        $gCIRCLEVALUE = 'X';
      } else {
        $gCIRCLEVALUE = 'Z';
      } // if 

      return ($returnarray);

    } // CreateFullCirclesMenu

    function LoadFriendsCircle ($pCIRCLEID) {
      global $gCIRCLEVIEW, $zFOCUSUSER;
      global $gSORT;
  
      $circlecriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                               "tID"          => $pCIRCLEID);

      $this->friendCircles->SelectByMultiple ($circlecriteria);
      $this->friendCircles->FetchArray ();

      if ($this->friendCircles->CountResult () == 0) {
        return (FALSE);
      } // if

      $circleid = $this->friendCircles->tID;

      $this->friendCirclesList->Select ("friendCircles_tID", $circleid);

      $friendidarray = array ();
      $friendselectstring = "";

      if ($this->friendCirclesList->CountResult () > 0) {
        while ($this->friendCirclesList->FetchArray ()) {
           array_push ($friendidarray, $this->friendCirclesList->friendInformation_tID);
        } // while

        $friendselectstring = join (" or tID =", $friendidarray);
        $friendselectstring = "(tID =" . $friendselectstring;
        $friendselectstring .= ")";

        $this->SelectWhere ($friendselectstring, $gSORT);
      } else {
        // Nothing found in circleslist, so select nothing.
        $this->SelectWhere ("0 = 1");
      } // if

      return (TRUE);

    } // LoadFriendsCircle

    function BufferCircleView () {
      global $zAPPLE, $zSTRINGS, $zFOCUSUSER;
      global $gSCROLLMAX, $gPOSTDATA;
      global $gCIRCLEVIEWTYPE, $gCIRCLEVIEW;
      global $gTHEMELOCATION;
      global $gFRAMELOCATION;

      // NOTE: Create a function that handles complex joins and puts 
      // the information in the subclasses.

      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/circles/list.top.aobj", INCLUDE_SECURITY_NONE);

      $query = "SELECT friendInformation.*, friendCircles.*, " .
               "friendCircles.tID AS FriendID, friendCircles.Name AS " .
               "FriendName, friendCircles.Description AS FriendDesc, " .
               "friendCirclesList.* FROM friendInformation, " .
               "friendCirclesList, friendCircles WHERE " .
               "friendCirclesList.friendCircles_tID = friendCircles.tID AND " .
               "      friendCirclesList.friendInformation_tID = " .
               "      friendInformation.tID AND " .
               "      friendInformation.userAuth_uID = " . $zFOCUSUSER->uID . 
               " ORDER BY friendCircles.sID, friendInformation.sID";
  
      $this->Query ($query);

      // Calculate scroll values.
      $gSCROLLMAX[$zAPPLE->Context] = $this->CountResult();
  
      // Check if any results were found.
      if ($gSCROLLMAX[$zAPPLE->Context] == 0) {
        $zSTRINGS->Lookup ('MESSAGE.NONE', 'USER.FRIENDS');
        $this->Message = $zSTRINGS->Output;
        $this->Broadcast();
      } // if
  
      global $gLISTCOUNT;
    
      // Counter for switching up Alternate.
      $switchcount = 0;

      global $gCHECKED;
      global $gFRIENDSICON, $gFRIENDFULLNAME, $gFRIENDNAME;

      global $bONLINENOW;
  
      $gPOSTDATA[$gCIRCLEVIEWTYPE] = $gCIRCLEVIEW;

      // NOTE: Set to an ungodly number until we work out joins better.
      $gSCROLLSTEP[$zAPPLE->Context] = 1000;

      $OLDCIRCLE = -1; $CURRENTCIRCLE = -1;
      $BOTTOM = FALSE;

      // Loop through the list.
      for ($gLISTCOUNT = 0; $gLISTCOUNT < $gSCROLLSTEP[$zAPPLE->Context]; $gLISTCOUNT++) {
        if ($this->FetchArray()) {

          $CURRENTCIRCLE = $this->FriendID;
          $this->friendCircles->tID = $this->FriendID;
          $this->friendCircles->Name = $this->FriendName;
          $this->friendCircles->Description = $this->FriendDesc;
  
          if ( ($CURRENTCIRCLE != $OLDCIRCLE) && ($OLDCIRCLE != -1) ) {
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/circles/list.middle.bottom.aobj", INCLUDE_SECURITY_NONE);
          } // if
  
          // Echo out the friend circle header object
          if ($CURRENTCIRCLE != $OLDCIRCLE) {
            $OLDCIRCLE = $CURRENTCIRCLE;
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/circles/list.middle.top.aobj", INCLUDE_SECURITY_NONE);
          } // if
  
          // Check if entry is hidden or blocked for this user.
          // $gPRIVACYSETTING = $this->friendCirclesPrivacy->Determine ("zFOCUSUSER", "zLOCALUSER", "friendCircles_tID", $this->friendCircles->tID);
    
          $USER = new cUSER ($zAPPLE->Context);
    
          $USER->Select ("Username", $this->Username);
          $USER->FetchArray (); 
    
          $bONLINENOW = OUTPUT_NBSP;

          list ($gFRIENDFULLNAME, $online) = $this->GetUserInformation();

          // If user activity in the last 3 minutes, consider them online.
          if ($online) {
            $bONLINENOW = $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/onlinenow.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
          } // if

          $gFRIENDSICON = "http://" . $this->Domain . "/icon/" . $this->Username;
          if ($this->Alias) {
            $gFRIENDNAME = ucwords ($this->Alias);
          } else {
            $gFRIENDNAME = ucwords ($gFRIENDFULLNAME);
          } // if
    
          unset ($USER);
    
          // Adjust for a hidden entry.
          if ( $zAPPLE->AdjustHiddenScroll ($gPRIVACYSETTING, $zAPPLE->Context) ) continue;
    
          global $gTARGET;
          $gTARGET = "http://" . $this->Domain . "/profile/" . $this->Username . "/";
    
          global $gEDITTARGET;
          $gEDITTARGET = "/profile/" . $zFOCUSUSER->Username . "/friends/";
    
          global $gEXTRAPOSTDATA;
          $gEXTRAPOSTDATA['ACTION'] = "EDIT";
          $gEXTRAPOSTDATA['tID'] = $this->tID;
    
          $gCHECKED = FALSE;
          // Select 
  
          $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/circles/list.middle.aobj", INCLUDE_SECURITY_NONE);
    
          unset ($gEXTRAPOSTDATA);

        } else {
          // Skip if no friends were found.
          if ($gSCROLLMAX[$zAPPLE->Context] > 0) {
            $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/circles/list.middle.bottom.aobj", INCLUDE_SECURITY_NONE);
          } // if
          break;
        } // if
      } // for
  
      $gTARGET = "/profile/" . $zFOCUSUSER->Username . "/friends/";
  
      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/friends/circles/list.bottom.aobj", INCLUDE_SECURITY_NONE);

      return (TRUE);

    } // BufferCircleView

    function CountNewFriends () {

      global $gAUTHUSERNAME;
      global $zLOCALUSER, $zSTRINGS, $zHTML, $zAPPLE;

      $zLOCALUSER->userInformation->Select ("userAuth_uID", $zLOCALUSER->uID);
      $zLOCALUSER->userInformation->FetchArray ();
      $stamp = $zLOCALUSER->userInformation->FriendStamp;

      $where = "userAuth_uID = " . $zLOCALUSER->uID . " and " . 
               "Verification = " . FRIEND_REQUESTS . " and " .
               "Stamp > '" . $stamp . "'";

      $this->SelectWhere ($where);
      $count = $this->CountResult ();

      if ($count) {
        global $gFRIENDCOUNT;
        $gFRIENDCOUNT = $count;
        $zSTRINGS->Lookup ("LABEL.NEWFRIENDS", $zAPPLE->Context);
        $return = $zHTML->CreateLink ("profile/$gAUTHUSERNAME/friends/requests/", $zAPPLE->ParseTags ($zSTRINGS->Output));
      } else {
        $return = OUTPUT_NBSP;
      } // if

      return ($return);

    } // CountNewFriends

    function GetUserInformation () {
      global $zREMOTE, $zAPPLE, $zXML;

      global $gSITEDOMAIN;

      if (($this->Domain) and ($this->Domain != $gSITEDOMAIN)) {

        // Pull from cache if available.
        if (isset ($this->InformationCache[$this->Username][$this->Domain])) {
          $fullname = $this->InformationCache[$this->Username][$this->Domain]['FULLNAME'];
          $email = $this->InformationCache[$this->Username][$this->Domain]['EMAIL'];
          $online = $this->InformationCache[$this->Username][$this->Domain]['ONLINE'];
          $return = array ($fullname, $online, $email);
          return ($return);
        } // if

        // Check Online (remote)
        $zREMOTE = new cREMOTE ($this->Domain);
        $datalist = array ("gACTION"   => "GET_USER_INFORMATION",
                           "gUSERNAME" => $this->Username);
        $zREMOTE->Post ($datalist, 1);
        $zXML->Parse ($zREMOTE->Return);

        // If no appleseed version was retrieved, an invalid url was used.
        $version = ucwords ($zXML->GetValue ("version", 0));
        if (!$version) return (FALSE);

        // If no user was found, return FALSE.
        $errorcode = ucwords ($zXML->GetValue ("code", 0));
        if ($errorcode) return (FALSE);

        $fullname = ucwords ($zXML->GetValue ("fullname", 0));
        $email = $zXML->GetValue ("email", 0);

        $online = FALSE;
        if ($zXML->GetValue ("online", 0) == "ONLINE") $online = TRUE;

        // Cache Information
        $this->InformationCache[$this->Username][$this->Domain]['FULLNAME'] = $fullname;
        $this->InformationCache[$this->Username][$this->Domain]['EMAIL'] = $email;
        $this->InformationCache[$this->Username][$this->Domain]['ONLINE'] = $online;

        unset ($zREMOTE);
      } elseif ($this->Username == ANONYMOUS) {
        global $zSTRINGS;
        $zSTRINGS->Lookup ('LABEL.ANONYMOUS.FULLNAME', $this->Context);
        $fullname = $zSTRINGS->Output;
        $email = "";
      } else {

        // Check online (local)
        $USER = new cUSER ($zAPPLE->Context);
   
        $USER->Select ("Username", $this->Username);
        $USER->FetchArray (); 

        // User not found, return false.
        if ($USER->CountResult() == 0) return (FALSE);

        $online = FALSE;
        // If user activity in the last 3 minutes, consider them online.
        if ($USER->userInformation->CheckOnline ()) {
          $online = TRUE;
        } // if

        $fullname = ucwords ($USER->userProfile->GetAlias ());
        $email = $USER->userProfile->Email;

      } // if

      $return = array ($fullname, $online, $email);

      return ($return);

    } // GetUserInformation

    // Process a friend request.
    function Request ($pREQUESTINGUSERNAME, $pREQUESTINGDOMAIN, $pRECEIVINGUSERNAME, $pRECEIVINGDOMAIN, $pNOTIFY = TRUE) {

      global $zSTRINGS;

      global $gSITEDOMAIN;

      global $gCIRCLEVIEWADMIN, $gCIRCLEVIEW;

      // Get Information On The Requesting User.
      $REQUESTINGUSER =  new cUSER ();
      $REQUESTINGUSER->Select ("Username", $pREQUESTINGUSERNAME);
      $REQUESTINGUSER->FetchArray ();

      // Get Information On The Receiving User.
      $RECEIVINGUSER =  new cUSER ();
      $RECEIVINGUSER->Select ("Username", $pRECEIVINGUSERNAME);
      $RECEIVINGUSER->FetchArray ();

      // Step 1: Check if the receiving user has already sent a friend request to this user.

      $friendcheckcriteria = array ("userAuth_uID" => $REQUESTINGUSER->uID,
                                    "Username"     => $RECEIVINGUSER->Username,
                                    "Domain"       => $gSITEDOMAIN);
      $FRIENDCHECK = new cFRIENDINFORMATION ();
      $FRIENDCHECK->SelectByMultiple ($friendcheckcriteria);
      $FRIENDCHECK->FetchArray();

      // User is already a pending friend.
      if ( ($FRIENDCHECK->CountResult() > 0) and ($FRIENDCHECK->Verification == FRIEND_PENDING) ) {
        global $gCIRCLEVIEWADMIN, $gCIRCLEVIEW;
        global $gREQUESTNAME;
        $gREQUESTNAME = $RECEIVINGUSER->userProfile->GetAlias ();
        $zSTRINGS->Lookup ('ERROR.ALREADY.PENDING', 'USER.FRIENDS');
        $this->Error = -1;
        $this->Message = $zSTRINGS->Output;
        unset ($gREQUESTNAME);
        $gCIRCLEVIEWADMIN = CIRCLE_PENDING;
        $gCIRCLEVIEW = CIRCLE_PENDING;
        return (FALSE);
      } // if

      // User is already a verified friend.
      if ( ($FRIENDCHECK->CountResult() > 0) and ($FRIENDCHECK->Verification == FRIEND_VERIFIED) ) {
        global $gCIRCLEVIEWADMIN, $gCIRCLEVIEW;
        global $gREQUESTNAME;
        $gREQUESTNAME = $RECEIVINGUSER->userProfile->GetAlias ();
        $zSTRINGS->Lookup ('ERROR.ALREADY', 'USER.FRIENDS');
        $this->Error = -1;
        $this->Message = $zSTRINGS->Output;
        unset ($gREQUESTNAME);
        return (FALSE);
      } // if

      // Check if this is a long distance relationship.
      if ($pREQUESTINGDOMAIN != $pRECEIVINGDOMAIN) {
        return ($this->LongDistanceRequest ($pREQUESTINGUSERNAME, $pREQUESTINGDOMAIN, $pRECEIVINGUSERNAME, $pRECEIVINGDOMAIN));
      } // if

      // Step 2: Approve if the receiving user has already sent a friend request to this user.
      if ( ($FRIENDCHECK->CountResult() > 0) and ($FRIENDCHECK->Verification == FRIEND_REQUESTS) ) {
        $this->Approve ($pRECEIVINGUSERNAME, $pRECEIVINGDOMAIN, $pREQUESTINGUSERNAME, $pREQUESTINGDOMAIN); 
        return (TRUE);
      } // if

      // Step 3: Create the requesting relationship. 

      // Find the highest Sort ID.
      $statement = "SELECT MAX(sID) FROM friendInformation WHERE userAuth_uID = " . $RECEIVINGUSER->uID;
      $query = mysql_query($statement);
      $result = mysql_fetch_row($query);
      $sortid = $result[0] + 1;

      // Add REQUESTING to RECEIVING.
      $this->userAuth_uID = $RECEIVINGUSER->uID;
      $this->sID = $sortid;
      $this->Username = $REQUESTINGUSER->Username;
      $this->Domain = $gSITEDOMAIN;
      $this->Verification = FRIEND_REQUESTS;
      $this->Stamp = SQL_NOW;

      $this->Add ();

      // Find the highest Sort ID.
      $statement = "SELECT MAX(sID) FROM friendInformation WHERE userAuth_uID = " . $REQUESTINGUSER->uID;
      $query = mysql_query($statement);
      $result = mysql_fetch_row($query);
      $sortid = $result[0] + 1;

      // Add RECEIVING to REQUESTING.
      $this->userAuth_uID = $REQUESTINGUSER->uID;
      $this->sID = $sortid;
      $this->Username = $RECEIVINGUSER->Username;
      $this->Domain = $gSITEDOMAIN;
      $this->Verification = FRIEND_PENDING;
      $this->Stamp = SQL_NOW;

      $this->Add ();

      // Notify requested user.
      if ($pNOTIFY)
        $this->NotifyRequest ($RECEIVINGUSER->userProfile->Email, $RECEIVINGUSER->userProfile->GetAlias (), $REQUESTINGUSER->userProfile->GetAlias (), $RECEIVINGUSER->Username);

      $zSTRINGS->Lookup ('MESSAGE.REQUEST', 'USER.FRIENDS');
      $gCIRCLEVIEWADMIN = CIRCLE_PENDING;
      $gCIRCLEVIEW = CIRCLE_PENDING;
      $this->Error = 0;
      $this->Message = $zSTRINGS->Output;

      // Step 4: Create the recieving relationship.

      unset ($REQUESTINGUSER);
      unset ($RECEIVINGUSER);
      unset ($FRIENDCHECK);

      return (TRUE);
      
    } // Request

    function LongDistanceRequest ($pLOCALUSERNAME, $pLOCALDOMAIN, $pREMOTEUSERNAME, $pREMOTEDOMAIN) {

      global $zSTRINGS, $zXML;

      global $gSITEDOMAIN, $gDOMAIN;

      global $gCIRCLEVIEWADMIN, $gCIRCLEVIEW;

      // Get Information On The Requesting User.
      $REQUESTINGUSER =  new cUSER ();
      $REQUESTINGUSER->Select ("Username", $pLOCALUSERNAME);
      $REQUESTINGUSER->FetchArray ();

      // Find the highest Sort ID.
      $statement = "SELECT MAX(sID) FROM friendInformation WHERE userAuth_uID = " . $zLOCALUSER->uID;
      $query = mysql_query($statement);
      $result = mysql_fetch_row($query);
      $sortid = $result[0] + 1;

      // Step 1: Check remote friend relationship status.
      $status = $this->LongDistanceStatus ($pLOCALUSERNAME, $pLOCALDOMAIN, $pREMOTEUSERNAME, $pREMOTEDOMAIN);
      switch ($status) {
        case FRIEND_VERIFIED:
          // Step 1a: Remotely verified, create verified local record.
          $FRIEND = new cFRIENDINFORMATION ();
          $friendcriteria = array ("userAuth_uID" => $REQUESTINGUSER->uID,
                                   "Username"     => $pREMOTEUSERNAME,
                                   "Domain"       => $pREMOTEDOMAIN);
          $FRIEND->SelectByMultiple ($friendcriteria);
          if ($FRIEND->CountResult() == 0) {
            // No local record exists, so create one and set to verified.
            $FRIEND->userAuth_uID = $REQUESTINGUSER->uID;
            $FRIEND->sID = $sortid;
            $FRIEND->Username = $pREMOTEUSERNAME;
            $FRIEND->Domain = $pREMOTEDOMAIN;
            $FRIEND->Verification = FRIEND_VERIFIED;
            $FRIEND->Stamp = SQL_NOW;
            $FRIEND->Add ();

            global $gNEWFRIEND;
            list ($gNEWFRIEND) = $FRIEND->GetUserInformation ();

            $zSTRINGS->Lookup ('MESSAGE.APPROVED', 'USER.FRIENDS');
            $this->Message = $zSTRINGS->Output;

          } else {
            $FRIEND->FetchArray();
            if ($FRIEND->Verification == FRIEND_VERIFIED) {
              // Friend is already on list.
              global $gREQUESTNAME;
              list ($gREQUESTNAME) = $FRIEND->GetUserInformation ();
  
              $zSTRINGS->Lookup ('ERROR.ALREADY', 'USER.FRIENDS');
              $this->Message = $zSTRINGS->Output;
              $this->Error = -1;
            } else {
              // Update local record.
              $FRIEND->Verification = FRIEND_VERIFIED;
              $FRIEND->Update ();

              global $gNEWFRIEND;
              list ($gNEWFRIEND) = $FRIEND->GetUserInformation ();
  
              $zSTRINGS->Lookup ('MESSAGE.APPROVED', 'USER.FRIENDS');
              $this->Message = $zSTRINGS->Output;
            } // if
          } // if
        break;

        case FRIEND_REQUESTS:
          $FRIEND = new cFRIENDINFORMATION ();

          $pendingcriteria = array ("userAuth_uID" => $REQUESTINGUSER->uID,
                                    "Username"     => $pREMOTEUSERNAME,
                                    "Domain"       => $pREMOTEDOMAIN);
          $FRIEND->SelectByMultiple ($pendingcriteria);

          if ($FRIEND->CountResult() == 0) {
            // No local record exists, so create one and set to verified.
            $FRIEND->userAuth_uID = $REQUESTINGUSER->uID;
            $FRIEND->sID = $sortid;
            $FRIEND->Username = $pREMOTEUSERNAME;
            $FRIEND->Domain = $pREMOTEDOMAIN;
            $FRIEND->Verification = FRIEND_PENDING;
            $FRIEND->Stamp = SQL_NOW;
            $FRIEND->Add ();
          } else {
            // Update local record.
            $FRIEND->FetchArray();
            $FRIEND->Verification = FRIEND_PENDING;
            $FRIEND->Update ();
          } // if

          // Error message since we're awaiting approval on their part.
          $FRIEND->Username = $pREMOTEUSERNAME;
          $FRIEND->Domain = $pREMOTEDOMAIN;
          global $gREQUESTNAME;
          list ($gREQUESTNAME) = $FRIEND->GetUserInformation();
          $zSTRINGS->Lookup ('ERROR.ALREADY.PENDING', 'USER.FRIENDS');
          $this->Error = -1;
          $this->Message = $zSTRINGS->Output;
          unset ($FRIEND);

          $gCIRCLEVIEWADMIN = CIRCLE_PENDING;
          $gCIRCLEVIEW = CIRCLE_PENDING;
        break;

        case FRIEND_PENDING:
          // Step 2a: Remotely pending, so approve remote friend request.
          $result = $this->LongDistanceApprove ($pLOCALUSERNAME, $pLOCALDOMAIN, $pREMOTEUSERNAME, $pREMOTEDOMAIN);
          if ($result == SUCCESS) {
            // Step 2b: If success, approve local friend request.
            $FRIEND = new cFRIENDINFORMATION ();
            $friendcriteria = array ("userAuth_uID" => $REQUESTINGUSER->uID,
                                     "Username"     => $pREMOTEUSERNAME,
                                     "Domain"       => $pREMOTEDOMAIN);
            $FRIEND->SelectByMultiple ($friendcriteria);
            if ($FRIEND->CountResult() == 0) {
              // No local record exists, so create one and set to verified.
              $FRIEND->userAuth_uID = $REQUESTINGUSER->uID;
              $FRIEND->sID = $sortid;
              $FRIEND->Username = $pREMOTEUSERNAME;
              $FRIEND->Domain = $pREMOTEDOMAIN;
              $FRIEND->Verification = FRIEND_VERIFIED;
              $FRIEND->Stamp = SQL_NOW;
              $FRIEND->Add ();
            } else {
              // Update local record.
              $FRIEND->FetchArray();
              $FRIEND->Verification = FRIEND_VERIFIED;
              $FRIEND->Update ();
            } // if

            global $gREQUESTEDUSER;
            list ($gREQUESTEDUSER) = $FRIEND->GetUserInformation ();
            $zSTRINGS->Lookup ('MESSAGE.REQUEST', 'USER.FRIENDS');
            $this->Message = $zSTRINGS->Output;
            unset ($gREQUESTEDUSER);

          } else {
            // Error message due to unsuccessful approval attempt.
            $zSTRINGS->Lookup ('ERROR.FAILED', 'USER.FRIENDS');
            $this->Message = $zSTRINGS->Output;
            $this->Error = -1;
          } // if
        break;

        default:
          // Step 3a: No relationship exists remotely, so add remote friend request.

          // Retrieve token.
          $VERIFY = new cUSERTOKENS ();
          $USER = new cUSER ();
          $USER->Select ("Username", $pLOCALUSERNAME);
          $USER->FetchArray();
          $VERIFY->userAuth_uID = $USER->uID;
          $VERIFY->LoadToken ($pREMOTEDOMAIN);
          $token = $AUTH->Token;
          if (!$token) {
            $VERIFY->CreateToken ($pREMOTEDOMAIN);
            $token = $VERIFY->Token;
          } // if
          unset ($VERIFY);
          unset ($USER);

          $zREMOTE = new cREMOTE ($pREMOTEDOMAIN);
          $datalist = array ("gACTION"   => "ADD_FRIEND_REQUEST",
                             "gTOKEN"    => $token,
                             "gUSERNAME" => $pREMOTEUSERNAME,
                             "gDOMAIN"   => $pLOCALDOMAIN);
          $zREMOTE->Post ($datalist);
        
          $zXML->Parse ($zREMOTE->Return);

          $fullname = $zXML->GetValue ("fullname", 0);
          $result = $zXML->GetValue ("result", 0);

          unset ($zREMOTE);
          if ($result == SUCCESS) {
            // Delete any existing relationships to maintain integrity.
            $deletecriteria = array ("userAuth_uID" => $REQUESTINGUSER->uID,
                                     "Username"     => $pREMOTEUSERNAME,
                                     "Domain"       => $pREMOTEDOMAIN);
            $this->SelectByMultiple ($deletecriteria);
            while ($this->FetchArray()) {
              $this->Delete();
            } // while

            // Step 3b: If success, add local pending request.
            $this->userAuth_uID = $REQUESTINGUSER->uID;
            $this->sID = $sortid;
            $this->Username = $pREMOTEUSERNAME;
            $this->Domain = $pREMOTEDOMAIN;
            $this->Verification = FRIEND_PENDING;
            $this->Stamp = SQL_NOW;

            $this->Add ();

            global $gREQUESTEDUSER;
            $gREQUESTEDUSER = $fullname;
            $zSTRINGS->Lookup ('MESSAGE.REQUEST', 'USER.FRIENDS');
            $this->Message = $zSTRINGS->Output;
            unset ($gREQUESTEDUSER);

          } else {
            // Error message due to unsuccessful request attempt.
            $zSTRINGS->Lookup ('ERROR.FAILED', 'USER.FRIENDS');
            $this->Message = $zSTRINGS->Output;
            $this->Error = -1;
          } // if

          $gCIRCLEVIEWADMIN = CIRCLE_PENDING;
          $gCIRCLEVIEW = CIRCLE_PENDING;

        break;
      } // switch

      return (TRUE);
    } // LongDistanceRequest

    function LongDistanceStatus ($pLOCALUSERNAME, $pLOCALDOMAIN, $pREMOTEUSERNAME, $pREMOTEDOMAIN) {
      global $zREMOTE, $zAPPLE, $zXML;

      global $gSITEDOMAIN;

      $VERIFY = new cUSERTOKENS ();
      $USER = new cUSER ();
      $USER->Select ("Username", $pLOCALUSERNAME);
      $USER->FetchArray();
      $VERIFY->userAuth_uID = $USER->uID;
      $VERIFY->LoadToken ($pREMOTEDOMAIN);
      $token = $VERIFY->Token;
      if (!$token) {
        $VERIFY->CreateToken ($pREMOTEDOMAIN);
        $token = $VERIFY->Token;
      } // if
      
      unset ($VERIFY);
      unset ($USER);

      $zREMOTE = new cREMOTE ($pREMOTEDOMAIN);
      $datalist = array ("gACTION"   => "CHECK_FRIEND_STATUS",
                         "gTOKEN"    => $token,
                         "gUSERNAME" => $pREMOTEUSERNAME,
                         "gDOMAIN" => $pLOCALDOMAIN);
      $zREMOTE->Post ($datalist);
      
      $zXML->Parse ($zREMOTE->Return);

      $status = $zXML->GetValue ("status", 0);

      unset ($zREMOTE);

      return ($status);

    } // LongDistanceStatus

    function Approve ($pREQUESTINGUSERNAME, $pREQUESTINGDOMAIN, $pRECEIVINGUSERNAME, $pRECEIVINGDOMAIN, $pNOTIFY = TRUE) {

      global $zSTRINGS;

      global $gSITEDOMAIN;

      // Check if this is a long distance relationship.
      if ($pREQUESTINGDOMAIN != $pRECEIVINGDOMAIN) {
        return ($this->LongDistanceApprove ($pRECEIVINGUSERNAME, $pRECEIVINGDOMAIN, $pREQUESTINGUSERNAME, $pREQUESTINGDOMAIN));
      } // if
      
      // Get Information On The Requesting User.
      $REQUESTINGUSER =  new cUSER ();
      $REQUESTINGUSER->Select ("Username", $pREQUESTINGUSERNAME);
      $REQUESTINGUSER->FetchArray ();

      // Get Information On The Receiving User.
      $RECEIVINGUSER =  new cUSER ();
      $RECEIVINGUSER->Select ("Username", $pRECEIVINGUSERNAME);
      $RECEIVINGUSER->FetchArray ();

      // Step 1: Check if a request exists.
      $checkcriteria = array ("userAuth_uID"   => $REQUESTINGUSER->uID,
                              "Username"       => $RECEIVINGUSER->Username,
                              "Domain"         => $gSITEDOMAIN,
                              "Verification"    => FRIEND_PENDING);
                              
      $this->SelectByMultiple ($checkcriteria);
      if ($this->CountResult () == 0) { 
        // No corresponding friend request exists, so recreate relationship.
        $this->Deny ($pRECEIVINGUSERNAME, $pRECEIVINGDOMAIN, $pREQUESTINGUSERNAME, $pREQUESTINGDOMAIN, FALSE); 
        $this->Request ($pRECEIVINGUSERNAME, $pRECEIVINGDOMAIN, $pREQUESTINGUSERNAME, $pREQUESTINGDOMAIN); 
        return (TRUE);
      } // if

      // Step 2: Approve request.

      // Update the requested record.
      $approvecriteria = array ("userAuth_uID"   => $REQUESTINGUSER->uID,
                                "Username"       => $RECEIVINGUSER->Username,
                                "Domain"         => $gSITEDOMAIN);
      $this->SelectByMultiple ($approvecriteria);
      $this->FetchArray ();
      $this->Verification = FRIEND_VERIFIED;
      $this->Update ();

      // Update the receiving record.
      $approvecriteria = array ("userAuth_uID"   => $RECEIVINGUSER->uID,
                                "Username"       => $REQUESTINGUSER->Username,
                                "Domain"         => $gSITEDOMAIN);
      $this->SelectByMultiple ($approvecriteria);
      $this->FetchArray ();
      $this->Verification = FRIEND_VERIFIED;
      $this->Update ();

      // Notify requested user.
      if ($pNOTIFY)
        $this->NotifyApproval ($REQUESTINGUSER->userProfile->Email, $RECEIVINGUSER->userProfile->GetAlias (), $REQUESTINGUSER->userProfile->GetAlias (), $REQUESTINGUSER->Username);

      global $gNEWFRIEND;
      $gNEWFRIEND = $REQUESTINGUSER->userProfile->GetAlias ();

      $zSTRINGS->Lookup ('MESSAGE.APPROVED', 'USER.FRIENDS');
      $this->Message = $zSTRINGS->Output;

      unset ($REQUESTINGUSER);
      unset ($RECEIVINGUSER);

      return (TRUE);

    } // Approve
    
    function LongDistanceApprove ($pLOCALUSERNAME, $pLOCALDOMAIN, $pREMOTEUSERNAME, $pREMOTEDOMAIN) {

      global $zSTRINGS, $zXML;

      global $gSITEDOMAIN, $gDOMAIN;

      global $gCIRCLEVIEWADMIN, $gCIRCLEVIEW;

      // Get Information On The Requesting User.
      $REQUESTINGUSER =  new cUSER ();
      $REQUESTINGUSER->Select ("Username", $pLOCALUSERNAME);
      $REQUESTINGUSER->FetchArray ();

      // Find the highest Sort ID.
      $statement = "SELECT MAX(sID) FROM friendInformation WHERE userAuth_uID = " . $zLOCALUSER->uID;
      $query = mysql_query($statement);
      $result = mysql_fetch_row($query);
      $sortid = $result[0] + 1;

      // Step 1: Check remote friend relationship status.
      $status = $this->LongDistanceStatus ($pLOCALUSERNAME, $pLOCALDOMAIN, $pREMOTEUSERNAME, $pREMOTEDOMAIN);
      switch ($status) {
        case FRIEND_VERIFIED:
          // Step 1a: Remotely verified, create verified local record.
          $FRIEND = new cFRIENDINFORMATION ();
          $friendcriteria = array ("userAuth_uID" => $REQUESTINGUSER->uID,
                                   "Username"     => $pREMOTEUSERNAME,
                                   "Domain"       => $pREMOTEDOMAIN);
          $FRIEND->SelectByMultiple ($friendcriteria);
          if ($FRIEND->CountResult() == 0) {
            // No local record exists, so create one and set to verified.
            $FRIEND->userAuth_uID = $REQUESTINGUSER->uID;
            $FRIEND->sID = $sortid;
            $FRIEND->Username = $pREMOTEUSERNAME;
            $FRIEND->Domain = $pREMOTEDOMAIN;
            $FRIEND->Verification = FRIEND_VERIFIED;
            $FRIEND->Stamp = SQL_NOW;
            $FRIEND->Add ();

            global $gNEWFRIEND;
            list ($gNEWFRIEND) = $FRIEND->GetUserInformation ();

            $zSTRINGS->Lookup ('MESSAGE.APPROVED', 'USER.FRIENDS');
            $this->Message = $zSTRINGS->Output;

          } else {
            $FRIEND->FetchArray();
            if ($FRIEND->Verification == FRIEND_VERIFIED) {
              // Friend is already on list.
              global $gREQUESTNAME;
              list ($gREQUESTNAME) = $FRIEND->GetUserInformation ();
  
              $zSTRINGS->Lookup ('ERROR.ALREADY', 'USER.FRIENDS');
              $this->Message = $zSTRINGS->Output;
              $this->Error = -1;
            } else {
              // Update local record.
              $FRIEND->Verification = FRIEND_VERIFIED;
              $FRIEND->Update ();

              global $gNEWFRIEND;
              list ($gNEWFRIEND) = $FRIEND->GetUserInformation ();
  
              $zSTRINGS->Lookup ('MESSAGE.APPROVED', 'USER.FRIENDS');
              $this->Message = $zSTRINGS->Output;
            } // if
          } // if
        break;

        case FRIEND_REQUESTS:
          $FRIEND = new cFRIENDINFORMATION ();

          $pendingcriteria = array ("userAuth_uID" => $REQUESTINGUSER->uID,
                                    "Username"     => $pREMOTEUSERNAME,
                                    "Domain"       => $pREMOTEDOMAIN);
          $FRIEND->SelectByMultiple ($pendingcriteria);

          if ($FRIEND->CountResult() == 0) {
            // No local record exists, so create one and set to verified.
            $FRIEND->userAuth_uID = $REQUESTINGUSER->uID;
            $FRIEND->sID = $sortid;
            $FRIEND->Username = $pREMOTEUSERNAME;
            $FRIEND->Domain = $pREMOTEDOMAIN;
            $FRIEND->Verification = FRIEND_PENDING;
            $FRIEND->Stamp = SQL_NOW;
            $FRIEND->Add ();
          } else {
            // Update local record.
            $FRIEND->FetchArray();
            $FRIEND->Verification = FRIEND_PENDING;
            $FRIEND->Update ();
          } // if

          // Error message since we're awaiting approval on their part.
          $FRIEND->Username = $pREMOTEUSERNAME;
          $FRIEND->Domain = $pREMOTEDOMAIN;
          global $gREQUESTNAME;
          list ($gREQUESTNAME) = $FRIEND->GetUserInformation();
          $zSTRINGS->Lookup ('ERROR.ALREADY.PENDING', 'USER.FRIENDS');
          $this->Error = -1;
          $this->Message = $zSTRINGS->Output;
          unset ($FRIEND);

          $gCIRCLEVIEWADMIN = CIRCLE_PENDING;
          $gCIRCLEVIEW = CIRCLE_PENDING;

        break;

        case FRIEND_PENDING:
          // Step 2a: Remotely pending (correctly), so approve remote friend request.
          
          $VERIFY = new cUSERTOKENS ();
          $USER = new cUSER ();
          $USER->Select ("Username", $pLOCALUSERNAME);
          $USER->FetchArray();
          $VERIFY->userAuth_uID = $USER->uID;
          $VERIFY->LoadToken ($pREMOTEDOMAIN);
          $token = $VERIFY->Token;
          if (!$token) {
            $VERIFY->CreateToken ($pREMOTEDOMAIN);
            $token = $VERIFY->Token;
          } // if
      
          unset ($VERIFY);
          unset ($USER);

          $zREMOTE = new cREMOTE ($pREMOTEDOMAIN);
          $datalist = array ("gACTION"   => "APPROVE_FRIEND_REQUEST",
                             "gTOKEN" => $token,
                             "gUSERNAME" => $pREMOTEUSERNAME,
                             "gDOMAIN" => $pLOCALDOMAIN);
          $zREMOTE->Post ($datalist);
        
          $zXML->Parse ($zREMOTE->Return);

          $fullname = $zXML->GetValue ("fullname", 0);
          $result = $zXML->GetValue ("result", 0);

          unset ($zREMOTE);
          if ($result == SUCCESS) {
            // Step 2b: If success, approve local friend request.
            $FRIEND = new cFRIENDINFORMATION ();
            $friendcriteria = array ("userAuth_uID" => $REQUESTINGUSER->uID,
                                     "Username"     => $pREMOTEUSERNAME,
                                     "Domain"       => $pREMOTEDOMAIN);
            $FRIEND->SelectByMultiple ($friendcriteria);
            if ($FRIEND->CountResult() == 0) {
              // No local record exists, so create one and set to verified.
              $FRIEND->userAuth_uID = $REQUESTINGUSER->uID;
              $FRIEND->sID = $sortid;
              $FRIEND->Username = $pREMOTEUSERNAME;
              $FRIEND->Domain = $pREMOTEDOMAIN;
              $FRIEND->Verification = FRIEND_VERIFIED;
              $FRIEND->Stamp = SQL_NOW;
              $FRIEND->Add ();
            } else {
              // Update local record.
              $FRIEND->FetchArray();
              $FRIEND->Verification = FRIEND_VERIFIED;
              $FRIEND->Update ();
            } // if

            global $gNEWFRIEND;
            $gNEWFRIEND = $fullname;
            $zSTRINGS->Lookup ('MESSAGE.APPROVED', 'USER.FRIENDS');
            $this->Message = $zSTRINGS->Output;
            unset ($gREQUESTEDUSER);

          } else {
            // Error message due to unsuccessful approval attempt.
            $zSTRINGS->Lookup ('ERROR.FAILED', 'USER.FRIENDS');
            $this->Message = $zSTRINGS->Output;
            $this->Error = -1;
          } // if
        break;

        default:
          // Step 3a: No relationship exists remotely, so add remote friend request.
          $VERIFY = new cUSERTOKENS ();
          $USER = new cUSER ();
          $USER->Select ("Username", $pLOCALUSERNAME);
          $USER->FetchArray();
          $VERIFY->userAuth_uID = $USER->uID;
          $VERIFY->LoadToken ($pREMOTEDOMAIN);
          $token = $AUTH->Token;
          if (!$token) {
            $VERIFY->CreateToken ($pREMOTEDOMAIN);
            $token = $VERIFY->Token;
          } // if
          unset ($VERIFY);
          unset ($USER);

          $zREMOTE = new cREMOTE ($pREMOTEDOMAIN);
          $datalist = array ("gACTION"   => "ADD_FRIEND_REQUEST",
                             "gTOKEN"    => $token,
                             "gUSERNAME" => $pREMOTEUSERNAME,
                             "gDOMAIN" => $pLOCALDOMAIN);
          $zREMOTE->Post ($datalist);

          $zXML->Parse ($zREMOTE->Return);

          $fullname = $zXML->GetValue ("fullname", 0);
          $result = $zXML->GetValue ("result", 0);

          unset ($zREMOTE);
          if ($result == SUCCESS) {
            // Delete any existing relationships to maintain integrity.
            $deletecriteria = array ("userAuth_uID" => $REQUESTINGUSER->uID,
                                     "Username"     => $pREMOTEUSERNAME,
                                     "Domain"       => $pREMOTEDOMAIN);
            $this->SelectByMultiple ($deletecriteria);
            while ($this->FetchArray()) {
              $this->Delete();
            } // while

            // Step 3b: If success, add local pending request.
            $this->userAuth_uID = $REQUESTINGUSER->uID;
            $this->sID = $sortid;
            $this->Username = $pREMOTEUSERNAME;
            $this->Domain = $pREMOTEDOMAIN;
            $this->Verification = FRIEND_PENDING;
            $this->Stamp = SQL_NOW;

            $this->Add ();

            global $gREQUESTEDUSER;
            $gREQUESTEDUSER = $fullname;
            $zSTRINGS->Lookup ('MESSAGE.REQUEST', 'USER.FRIENDS');
            $this->Message = $zSTRINGS->Output;
            unset ($gREQUESTEDUSER);

          } else {
            // Error message due to unsuccessful request attempt.
            $zSTRINGS->Lookup ('ERROR.FAILED', 'USER.FRIENDS');
            $this->Message = $zSTRINGS->Output;
            $this->Error = -1;
          } // if

          $gCIRCLEVIEWADMIN = CIRCLE_PENDING;
          $gCIRCLEVIEW = CIRCLE_PENDING;

        break;
      } // switch

      return (TRUE);
    } // LongDistanceApprove

    function Deny ($pREQUESTINGUSERNAME, $pREQUESTINGDOMAIN, $pRECEIVINGUSERNAME, $pRECEIVINGDOMAIN, $pNOTIFY = TRUE) {

      global $zSTRINGS;


      global $gSITEDOMAIN;

      // Check if this is a long distance relationship.
      if ($pREQUESTINGDOMAIN != $pRECEIVINGDOMAIN) {
        return ($this->LongDistanceDeny ($pRECEIVINGUSERNAME, $pRECEIVINGDOMAIN, $pREQUESTINGUSERNAME, $pREQUESTINGDOMAIN));
      } // if

      // Get Information On The Requesting User.
      $REQUESTINGUSER =  new cUSER ();
      $REQUESTINGUSER->Select ("Username", $pREQUESTINGUSERNAME);
      $REQUESTINGUSER->FetchArray ();

      // Get Information On The Receiving User.
      $RECEIVINGUSER =  new cUSER ();
      $RECEIVINGUSER->Select ("Username", $pRECEIVINGUSERNAME);
      $RECEIVINGUSER->FetchArray ();

      // Step 1: Deny request.

      // Delete the requested record.
      $denycriteria = array ("userAuth_uID"   => $REQUESTINGUSER->uID,
                             "Username"       => $RECEIVINGUSER->Username,
                             "Domain"         => $gSITEDOMAIN);
      $this->SelectByMultiple ($denycriteria);
      $this->FetchArray ();
      $this->Delete ();

      // Update the receiving record.
      $denycriteria = array ("userAuth_uID"   => $RECEIVINGUSER->uID,
                             "Username"       => $REQUESTINGUSER->Username,
                             "Domain"         => $gSITEDOMAIN);
      $this->SelectByMultiple ($denycriteria);
      $this->FetchArray ();
      $this->Verification = FRIEND_VERIFIED;
      $this->Delete ();

      // Notify requested user.
      if ($pNOTIFY)
        $this->NotifyDenial ($REQUESTINGUSER->userProfile->Email, $RECEIVINGUSER->userProfile->GetAlias (), $REQUESTINGUSER->userProfile->GetAlias (), $REQUESTINGUSER->Username);

      global $gDENIEDNAME;
      $gDENIEDNAME = $REQUESTINGUSER->userProfile->GetAlias ();

      $zSTRINGS->Lookup ('MESSAGE.DENIED', 'USER.FRIENDS');
      $this->Message = $zSTRINGS->Output;

      unset ($REQUESTINGUSER);
      unset ($RECEIVINGUSER);
      unset ($FRIENDCHECK);

      return (TRUE);

    } // Deny

    function Remove ($pREQUESTINGUSERNAME, $pREQUESTINGDOMAIN, $pRECEIVINGUSERNAME, $pRECEIVINGDOMAIN, $pNOTIFY = TRUE) {

      global $zSTRINGS;

      global $gSITEDOMAIN;

      // Check if this is a long distance relationship.
      if ($pREQUESTINGDOMAIN != $pRECEIVINGDOMAIN) {
        return ($this->LongDistanceRemove ($pREQUESTINGUSERNAME, $pREQUESTINGDOMAIN, $pRECEIVINGUSERNAME, $pRECEIVINGDOMAIN));
      } // if

      // Get Information On The Requesting User.
      $REQUESTINGUSER =  new cUSER ();
      $REQUESTINGUSER->Select ("Username", $pREQUESTINGUSERNAME);
      $REQUESTINGUSER->FetchArray ();

      // Get Information On The Receiving User.
      $RECEIVINGUSER =  new cUSER ();
      $RECEIVINGUSER->Select ("Username", $pRECEIVINGUSERNAME);
      $RECEIVINGUSER->FetchArray ();

      // Step 1: Remove friend.

      // Delete the requested record.
      $denycriteria = array ("userAuth_uID"   => $REQUESTINGUSER->uID,
                             "Username"       => $RECEIVINGUSER->Username,
                             "Domain"         => $gSITEDOMAIN);
      $this->SelectByMultiple ($denycriteria);
      $this->FetchArray ();
      $this->Delete ();

      // Update the receiving record.
      $denycriteria = array ("userAuth_uID"   => $RECEIVINGUSER->uID,
                             "Username"       => $REQUESTINGUSER->Username,
                             "Domain"         => $gSITEDOMAIN);
      $this->SelectByMultiple ($denycriteria);
      $this->FetchArray ();
      $this->Verification = FRIEND_VERIFIED;
      $this->Delete ();

      // Notify requested user.
      if ($pNOTIFY)
        $this->NotifyDelete ($RECEIVINGUSER->userProfile->Email, $REQUESTINGUSER->userProfile->GetAlias (), $RECEIVINGUSER->userProfile->GetAlias (), $RECEIVINGUSER->Username);

      global $gFRIENDUSERNAME;
      $gFRIENDUSERNAME = $RECEIVINGUSER->userProfile->GetAlias ();

      $zSTRINGS->Lookup ('MESSAGE.DELETE', 'USER.FRIENDS');
      $this->Message = $zSTRINGS->Output;

      unset ($REQUESTINGUSER);
      unset ($RECEIVINGUSER);
      unset ($FRIENDCHECK);

      return (TRUE);

    } // Remove

    function RemoveAll ($pMASSLIST) {
      global $gCIRCLEVIEW, $gCIRCLEVIEWADMIN;

      global $zAUTHUSER;

      global $zSTRINGS, $zAPPLE;

      // Check if any items were selected.
      if (!$pMASSLIST) {
        $zSTRINGS->Lookup ('ERROR.NONESELECTED', $zAPPLE->Context);
        $this->Message = $zSTRINGS->Output;
        $this->Error = -1;
        return (FALSE);
      } // if

      // Loop through the list
      foreach ($pMASSLIST as $number => $tidvalue) {
        $this->Select ("tID", $tidvalue);
        $this->FetchArray ();

        $this->Remove ($zAUTHUSER->Username, $zAUTHUSER->Domain, $this->Username, $this->Domain);
      
        if (!$this->Error) {
          $zSTRINGS->Lookup ('MESSAGE.DELETEALL', $zAPPLE->Context);
          $this->Message = $zSTRINGS->Output;
        } // if
      } // foreach

      $gCIRCLEVIEW = CIRCLE_EDITOR; $gCIRCLEVIEWADMIN = CIRCLE_EDITOR;

    } // RemoveAll

    function LongDistanceDeny ($pLOCALUSERNAME, $pLOCALDOMAIN, $pREMOTEUSERNAME, $pREMOTEDOMAIN) {

      global $zSTRINGS, $zXML;

      global $gSITEDOMAIN, $gDOMAIN;

      global $gCIRCLEVIEWADMIN, $gCIRCLEVIEW;

      // Get Information On The Requesting User.
      $REQUESTINGUSER =  new cUSER ();
      $REQUESTINGUSER->Select ("Username", $pLOCALUSERNAME);
      $REQUESTINGUSER->FetchArray ();

      // Find the highest Sort ID.
      $statement = "SELECT MAX(sID) FROM friendInformation WHERE userAuth_uID = " . $zLOCALUSER->uID;
      $query = mysql_query($statement);
      $result = mysql_fetch_row($query);
      $sortid = $result[0] + 1;

      // Step 1: Check remote friend relationship status.
      $status = $this->LongDistanceStatus ($pLOCALUSERNAME, $pLOCALDOMAIN, $pREMOTEUSERNAME, $pREMOTEDOMAIN);
      
      switch ($status) {
        case FRIEND_VERIFIED:
        case FRIEND_REQUESTS:
        case FRIEND_PENDING:
          // Correct
        default:
          
          $VERIFY = new cUSERTOKENS ();
          $USER = new cUSER ();
          $USER->Select ("Username", $pLOCALUSERNAME);
          $USER->FetchArray();
          $VERIFY->userAuth_uID = $USER->uID;
          $VERIFY->LoadToken ($pREMOTEDOMAIN);
          $token = $VERIFY->Token;
          if (!$token) {
            $VERIFY->CreateToken ($pREMOTEDOMAIN);
            $token = $VERIFY->Token;
          } // if
      
          unset ($VERIFY);
          unset ($USER);

          // Send request to delete remote friend.
          $zREMOTE = new cREMOTE ($pREMOTEDOMAIN);
          $datalist = array ("gACTION"   => "DELETE_FRIEND",
                             "gTOKEN" => $token,
                             "gUSERNAME" => $pREMOTEUSERNAME,
                             "gDOMAIN" => $pLOCALDOMAIN);
          $zREMOTE->Post ($datalist);
        
          $zXML->Parse ($zREMOTE->Return);

          $fullname = $zXML->GetValue ("fullname", 0);
          $result = $zXML->GetValue ("result", 0);

          unset ($zREMOTE);
          
          $deletecriteria = array ("userAuth_uID" => $REQUESTINGUSER->uID,
                                   "Username"     => $pREMOTEUSERNAME,
                                   "Domain"       => $pREMOTEDOMAIN);
          $this->SelectByMultiple ($deletecriteria);
          while ($this->FetchArray()) {
            $this->Delete();
          } // while

          global $gDENIEDNAME;
          $gDENIEDNAME = $fullname;
          $zSTRINGS->Lookup ('MESSAGE.DENIED', 'USER.FRIENDS');
          $this->Message = $zSTRINGS->Output;
          unset ($gREQUESTEDUSER);

          $gCIRCLEVIEWADMIN = CIRCLE_REQUESTS;
          $gCIRCLEVIEW = CIRCLE_REQUESTS;

        break;
      } // switch

      return (TRUE);
    } // LongDistanceDeny

  } // cFRIENDINFORMATION

  // Friends circles list class.
  class cFRIENDCIRCLESLIST extends cDATACLASS {

    var $tID, $friendCircles_tID, $friendInformation_tID;

    function cFRIENDCIRCLESLIST ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'friendCirclesList';
      $this->tID = '';
      $this->friendCircles_tID = '';
      $this->friendInformation_tID = '';
      $this->Label = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'friendInformation_tID';
 
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

   'friendCircles_tID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

 'friendInformation_tID' => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => ', .',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),
      );

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor

    // Count friends in circle.
    function CountInCircle ($pCIRCLEID) {
     
      // Determine how many friends are attached to this circle.
      $countcriteria = array ("friendCircles_tID" => $pCIRCLEID);
      $this->SelectByMultiple ($countcriteria);

      $countresult = $this->CountResult ();

      return ($countresult);

    } // CountInCircle

  } // cFRIENDCIRCLESLIST

  // Friends circles class.
  class cFRIENDCIRCLES extends cDATACLASS {

    var $tID, $userAuth_uID, $sID, $Name, $Description;

    function cFRIENDCIRCLES ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'friendCircles';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->sID = '';
      $this->Name = '';
      $this->Description = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'userAuth_uID';
 
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

        'sID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Name'           => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Description'    => array ('max'        => '4096',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),
      );

      // Grab the fields from the database.
      $this->Fields();

    } // Constructor

    function LoadCircles () {
      global $zFOCUSUSER;

      $this->Select ("userAuth_uID", $zFOCUSUSER->uID);

      return (TRUE);

    } // LoadCircles

  } // cFRIENDCIRCLES

?>
