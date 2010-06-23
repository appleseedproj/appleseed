<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: comments.php                            CREATED: 09-05-2005 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 09-05-2005 +
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
  // | DESCRIPTION.  Comments class definitions.                         |
  // +-------------------------------------------------------------------+

  // Photo sets class.
  class cCOMMENTINFORMATION extends cDATACLASS {
 
    // Keys
    var $tID, $rID, $userAuth_uID, $parent_tID;
    
    // Variables
    var $Subject, $Body, $Stamp;

    function cCOMMENTINFORMATION ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'commentInformation';
      $this->tID = '';
      $this->rID = '';
      $this->Context = '';
      $this->CommentContext = '';
      $this->userAuth_uID = '';
      $this->parent_tID = '';
      $this->Subject = '';
      $this->Body = '';
      $this->Stamp = '';
      $this->Error = 0;
      $this->Message = '';
      $this->PageContext = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'userAuth_uID';
 
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

        'rID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Context'        => array ('max'        => '32',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'parent_tID'     => array ('max'        => '',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Subject'        => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Body'           => array ('max'        => '4096',
                                   'min'        => '1',
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


        'Owner_Username' => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Owner_Domain'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Owner_Icon'     => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Owner_Address'  => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
 
    function Save () {

      global $gPARENTID;
      global $gUSERICON;
      global $gREFERENCEID;
      global $zAUTHUSER, $zFOCUSUSER;
      global $gCOMMENTVIEW;
      global $gSITEDOMAIN;

      global $zOLDAPPLE;
      global $HTTP_SERVER_VARS;

      $this->Synchronize ();

      $this->Context = $zOLDAPPLE->Context;

      if ($gPARENTID) {
        $this->parent_tID = $gPARENTID;
      } else {
        $this->parent_tID = 0;
      } // if
      if ($zAUTHUSER->Anonymous) {
        // Anonymous User.
        $this->Owner_Icon = NO_ICON;
        $this->Owner_Username = ANONYMOUS; 
        $this->Owner_Domain = $gSITEDOMAIN;
        $this->Owner_Address = $HTTP_SERVER_VARS['REMOTE_ADDR'];
      } else {
        // Logged In User.
        $this->Owner_Icon = $gUSERICON;
        $this->Owner_Username = $zAUTHUSER->Username;
        $this->Owner_Domain = $zAUTHUSER->Domain;
        $this->Owner_Address = $HTTP_SERVER_VARS['REMOTE_ADDR'];
      } // if
      $this->rID = $gREFERENCEID;
      $this->Stamp = SQL_NOW;

      $this->Sanity ();

      if ($this->Error == 0) {
        
        $this->Add ();

        switch (strtoupper($zOLDAPPLE->Context)) {
          case 'USER.INFO':
            // Send email.
            // Disabled: 04-27-2007
            // $this->NotifyProfile ($zFOCUSUSER->userProfile->Email, 
            //                       $zFOCUSUSER->userProfile->GetAlias (), 
            //                       $zFOCUSUSER->Username,
           //                        $zAUTHUSER->Fullname);
          break;
          case 'USER.JOURNAL':
            // Send email.
            // Disabled: 04-27-2007
            // $this->NotifyJournal ($zFOCUSUSER->userProfile->Email, 
            //                       $zFOCUSUSER->userProfile->GetAlias (), 
            //                       $zFOCUSUSER->Username,
            //                       $zAUTHUSER->Fullname);
          break;
          case 'CONTENT.ARTICLES':
            // Disabled: 04-27-2007
            // $this->NotifyArticle ($zAUTHUSER->Fullname);
          break;
        } // switch

        // Send a notification to the parent we're replying to.
        if ($this->parent_tID !== 0) {
          // Select the parent information
          $this->Select ("tID", $this->parent_tID);
          $this->FetchArray();

          // Make sure we haven't already sent a notification.
          // Make sure we're not replying to our own comment.
          if ( ($this->Owner_Username != $zAUTHUSER->Username) or
               ($this->Owner_Domain != $gSITEDOMAIN) ) {

            // Don't bother with a reply to an anonymous comment.
            if ($this->Owner_Username != ANONYMOUS) {
              $FRIEND = new cFRIENDINFORMATION();
              $FRIEND->Username = $this->Owner_Username;
              $FRIEND->Domain = $this->Owner_Domain;
              list ($fullname, $online, $email) = $FRIEND->GetUserInformation();

              // Disabled: 04-27-2007
              // $this->NotifyReply ($email, $fullname, $this->Owner_Username, $zAUTHUSER->Fullname);

              unset ($FRIEND);
            } // if
            
          } // if
        } // if

      } // if

      $gCOMMENTACTION = "";

    } // Save

    function AddForm () {

      global $zOLDAPPLE;

      global $gFRAMELOCATION;

      global $gREADDATA, $gADDDATA;

      global $gCOMMENTVIEWFLAG;
      global $gCOMMENTTARGET;
      global $zLOCALUSER;

      global $gREFERENCEID;

      global $gREPLYDATA;
      global $gPARENTID;
      global $gSUBJECT;

      global $zSTRINGS, $zHTML;

      $gCOMMENTTARGET = $_SERVER[REQUEST_URI];
      $zOLDAPPLE->SetTag ('COMMENTTARGET', $gCOMMENTTARGET);

      $replyfile = "new";

      if ($gPARENTID) {

        $replyfile = "reply";
        
        global $gPARENTAUTHOR, $gPARENTBODY, $gPARENTSUBJECT;
        global $zHTML;
        global $zSTRINGS;
        global $zPARENT;

        $gREPLYDATA = array ("COMMENTACTION" => "ADD",
                             "PARENTID" => $gPARENTID,
                             "REFERENCEID" => $gREFERENCEID);

        $zPARENT = new cCOMMENTINFORMATION ($this->PageContext);
        $zPARENT->Select ("tID", $gPARENTID);
        $zPARENT->FetchArray ();
        $gPARENTBODY = $zPARENT->Body;
        $gPARENTSUBJECT = $zPARENT->Subject;
        $zSTRINGS->Lookup ('LABEL.SUBJECTPREFIX', 'USER.COMMENTS');

        // Check if the subject field has been modified by user.
        if (!$gSUBJECT) {
         // Check to see if we haven't already added the prefix.
         if (strpos ($gPARENTSUBJECT, $zSTRINGS->Output, 0) === 0) {
           // Just inherit the parent post subject.
           $gSUBJECT = $gPARENTSUBJECT;
         } else {
           // Add the prefix to the subject.
           $gSUBJECT = $zSTRINGS->Output . $gPARENTSUBJECT;
         } // if
        } // if

        $gPARENTAUTHOR = $zHTML->CreateUserLink ($zPARENT->Owner_Username, $zPARENT->Owner_Domain);
      } else {
        $gREPLYDATA = array ("COMMENTACTION" => "ADD",
                             "PARENTID" => 0,
                             "REFERENCEID" => $gREFERENCEID);

        $replyfile = "new";
        
      } // if

      global $zAUTHUSER;
      // Create the icons list.
      if (!$zAUTHUSER->Remote) {
        $zLOCALUSER->userIcons->BuildIconMenu ($zLOCALUSER->uID);
      } else {
        $zAUTHUSER->BuildIconMenu ();
      } // if

      $result = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/comments/$gCOMMENTVIEWFLAG/$replyfile.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      $zOLDAPPLE->UnsetTag ('COMMENTTARGET');

      return ($result);

    } // AddForm

    function Top () {

      global $gFRAMELOCATION;
      global $gCOMMENTVIEWFLAG;

      global $gREADDATA, $gADDDATA, $gPOSTDATA;

      global $gSCROLLSTART;

      global $gCOMMENTTARGET;

      global $zOLDAPPLE;

      if (isset ($gSCROLLSTART[$this->CommentContext]))
        $gPOSTDATA["SCROLLSTART[" . $this->CommentContext . "]"] = $gSCROLLSTART[$this->CommentContext];

      $gCOMMENTTARGET = $_SERVER['REQUEST_URI'];
      $zOLDAPPLE->SetTag ('COMMENTTARGET', $gCOMMENTTARGET);

      $result = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/comments/$gCOMMENTVIEWFLAG/main.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      $zOLDAPPLE->UnsetTag ('COMMENTTARGET');

      return ($result);

    } // Top

    function Listing ($pREFERENCEID, $pPARENTID, $pTHREADID = NULL) {

      global $zOLDAPPLE;

      global $zSTRINGS, $zHTML;

      global $zAUTHUSER;
      global $zLOCALUSER, $zFOCUSUSER;

      global $gFRAMELOCATION;

      global $gCOMMENTVIEW, $gCOMMENTVIEWFLAG;

      global $gPOSTDATA;

      global $gREPLYDATA;

      global $gREPLYLABEL, $gPARENTLABEL, $gTHREADLABEL;

      global $bDELETEBUTTON, $bADDRESS;

      global $gCONTINUEFLAG;

      global $gSCROLLSTEP, $gSCROLLSTART, $gSCROLLMAX;
      global $gSCROLLCOUNT;

      $gREPLYLABEL = __("Reply");

      $gPARENTLABEL = __("Parent");

      $gTHREADLABEL = __("Thread");

      $returnbuffer = "";

      if ($pTHREADID) {
        $commentcriteria = array ("tID"          => $pTHREADID,
                                  "rID"          => $pREFERENCEID,
                                  "Context"      => $zOLDAPPLE->Context);
      } else {
        $commentcriteria = array ("parent_tID"   => $pPARENTID,
                                  "rID"          => $pREFERENCEID,
                                  "Context"      => $zOLDAPPLE->Context);
      } // if

      // For the profile comments, show the latest first.
      $gSORT = "Stamp ASC";
      if ($gCOMMENTVIEW == COMMENT_VIEW_PROFILE) $gSORT = "Stamp DESC";

      $this->SelectByMultiple ($commentcriteria, $gSORT);

      // Count the maximum number of comments attached.

      $context = $this->CommentContext;
      $gSCROLLMAX[$context] = $this->CountComments ($pREFERENCEID, $zOLDAPPLE->Context);

      // Check if no comments have been found.
      if ( ($this->CountResult () == 0) and ($pPARENTID == 0) ) {

        ob_start ();
        
        $this->Message = __("No Results Found");
        $this->Broadcast();

        $returnbuffer = ob_get_clean ();

        return ($returnbuffer);
      } // if

      $start = $gSCROLLSTART[$this->CommentContext];
      $max = $gSCROLLSTART[$this->CommentContext] + $gSCROLLSTEP[$this->CommentContext];

      while ($this->FetchArray ()) {

        $gSCROLLCOUNT[$this->CommentContext]++;

        $current = $gSCROLLCOUNT[$this->CommentContext];
        // We've hit the max number of comments for a page.
        if ($current > $max) {
          return ($returnbuffer);
        } // if

        // See if we're listing a parent or child comment.
        $nestflag = "first";
        if ($pPARENTID != 0) $nestflag = "inner";

        // Check if the comment has been deleted.
        $deletedflag = NULL;
        if ($this->Subject == DELETED_COMMENT) $deletedflag = "deleted.";

        global $gTARGET;

        global $gTHEMELOCATION;

        global $gCOMMENTSUBJECT, $gCOMMENTBODY, $gCOMMENTDATE, $gCOMMENTTIME;
        global $gCOMMENTAUTHOR, $gCOMMENTTHREAD, $gCOMMENTBYLINE;
        global $gCOMMENTADDRESS;
        global $gCOMMENTICON, $gCOMMENTICONX, $gCOMMENTICONY;
        global $gCOMMENTLINK, $gCOMMENTSTAMP;
        global $gCOMMENTCHECKED, $gCOMMENTACTION;
        
        global $gPARENTDATA, $gTHREADDATA;

        global $bCOMMENTICON, $bONLINENOW;

        $gCOMMENTCHECKED = FALSE;
        // Select 
        if ($gCOMMENTACTION == 'SELECT_ALL') $gCOMMENTCHECKED = TRUE;

        // Generic
        $gCOMMENTSUBJECT = $this->Subject;
        $gCOMMENTBODY = $zOLDAPPLE->Format ($this->Body, FORMAT_BASIC);
        
        $gCOMMENTAUTHOR = $zHTML->CreateUserLink ($this->Owner_Username, $this->Owner_Domain, FALSE);
        $gCOMMENTADDRESS = $this->Owner_Address;
        global $gCOMMENTAUTHORFULLNAME;
        
        $bONLINENOW = OUTPUT_NBSP;

        // If user activity in the last 3 minutes, consider them online.
        list ($gCOMMENTAUTHORFULLNAME, $online) = $zOLDAPPLE->GetUserInformation($this->Owner_Username, $this->Owner_Domain);
        if ($online) {
          $bONLINENOW = $zOLDAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/onlinenow.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
        } // if

        $bCOMMENTICON = $zOLDAPPLE->BufferUserIcon ($this->Owner_Username, $this->Owner_Domain, $this->Owner_Icon);

        $gREPLYDATA = $gPOSTDATA;
        $gREPLYDATA["COMMENTACTION"] = "ADD";
        $gREPLYDATA["PARENTID"] = $this->tID;
        $gREPLYDATA["REFERENCEID"] = $pREFERENCEID;

        $gTHREADDATA = $gPOSTDATA;
        $gTHREADDATA["STARTINGID"] = $this->tID;
        $gTHREADDATA["REFERENCEID"] = $pREFERENCEID;
        unset ($gTHREADDATA["SCROLLSTART[" . $this->CommentContext . "]"]);

        $gPARENTDATA = $gPOSTDATA;
        $gPARENTDATA["STARTINGID"]  = $this->parent_tID;
        $gPARENTDATA["REFERENCEID"] = $pREFERENCEID;
        unset ($gPARENTDATA["SCROLLSTART[" . $this->CommentContext . "]"]);

        $stamp = strtotime ($this->Stamp);
        $gCOMMENTDATE = date ("M j, Y", $stamp);
        $gCOMMENTTIME = date ("g:i a", $stamp);
        $gCOMMENTSTAMP = __("Time Stamp", array ( 'date' => $gCOMMENTDATE, 'time' => $gCOMMENTTIME ) );

        // Threaded -specific
        $gCOMMENTLINK = $zHTML->CreateLink ($gTARGET . "#comments", $this->Subject, $gTHREADDATA);
        $gCOMMENTTHREAD = __("Thread Label", array ( 'link' => $gCOMMENTLINK, 'author' => $gCOMMENTAUTHOR, 'date' => $gCOMMENTDATE, 'time' => $gCOMMENTTIME ) );

        // Compact -specific
        $gCOMMENTBYLINE = __("Byline", array ( 'author' => $gCOMMENTAUTHOR ) );

        /* */

        //Count how many children this comment has.
        $COMMENTINFO = new cCOMMENTINFORMATION ();
        $targetcriteria = array ("parent_tID"   => $this->tID,
                                 "rID"          => $pREFERENCEID,
                                 "Context"      => $zOLDAPPLE->Context);
        $COMMENTINFO->SelectByMultiple ($targetcriteria);
        $countchildren = $COMMENTINFO->CountResult ();
        unset ($COMMENTINFO);

        $bDELETEBUTTON = OUTPUT_NBSP;
        $bADDRESS = OUTPUT_NBSP;
        if ($this->CheckCommentAccess () ) {

          global $gPOSTDATA;
          $gPOSTDATA['tID'] = $this->tID;
          $gPOSTDATA['COMMENTACTION'] = "DELETE";
          $gPOSTDATA["REFERENCEID"] = $pREFERENCEID;

          global $gTARGETID; 
          $gTARGETID = "";
          if ($countchildren > 0) $gTARGETID = $this->tID;

          $bDELETEBUTTON = $zHTML->CreateButton ('Delete', __("Confirm Delete"), ENABLED, "DELETE", "COMMENTACTION");
          $bADDRESS = "(" . $gCOMMENTADDRESS . ")";

          unset ($gPOSTDATA['tID']);
          unset ($gPOSTDATA['COMMENTACTION']);

        } // if

        global $bPARENTBUTTON;
        $bPARENTBUTTON = NULL;
        if ($this->parent_tID) $bPARENTBUTTON = $zHTML->CreateButton ('Parent', NULL, ENABLED, NULL, "COMMENTACTION");

        global $bTHREADBUTTON;
        $bTHREADBUTTON = NULL;
        if ($countchildren > 0) $bTHREADBUTTON = $zHTML->CreateButton ('Thread', NULL, ENABLED, NULL, "COMMENTACTION");

        global $gCOMMENTID;
        $gCOMMENTID = $this->tID;

        if ($current > $start) {
          $returnbuffer .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/comments/$gCOMMENTVIEWFLAG/$deletedflag$nestflag.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

          $RECURSE = new cCOMMENTINFORMATION ($zOLDAPPLE->Context);
          $RECURSE->CommentContext = $this->CommentContext;

          $returnbuffer .= $RECURSE->Listing ($pREFERENCEID, $this->tID);

          $returnbuffer .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/comments/$gCOMMENTVIEWFLAG/$deletedflag$nestflag.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } else {
          $RECURSE = new cCOMMENTINFORMATION ($zOLDAPPLE->Context);
          $RECURSE->CommentContext = $this->CommentContext;

          $returnbuffer .= $RECURSE->Listing ($pREFERENCEID, $this->tID);
        } // if

      } // while

      return ($returnbuffer);

    } // Listing

    function CheckCommentAccess  () {
      global $zAUTHUSER, $zLOCALUSER, $zFOCUSUSER;
      // if Comment Ownership or Editor or Context Ownership 
      if ( ( ($zAUTHUSER->Username == $this->Owner_Username) and 
             ($zAUTHUSER->Domain == $this->Owner_Domain) ) or 
           ($zLOCALUSER->userAccess->e == TRUE) or
           ( ($zLOCALUSER->uID == $zFOCUSUSER->uID) and 
             ($zLOCALUSER->Username) ) ) {
        return (TRUE);
      } // if

      return (FALSE);
    } // CheckCommentAccess 

    function Bottom () {

      global $gFRAMELOCATION;
      global $gCOMMENTVIEWFLAG;

      global $zOLDAPPLE;

      $result = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/comments/$gCOMMENTVIEWFLAG/main.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($result);

    } // Bottom

    // Determine which mode we are viewing in, and set flags accordingly.
    function Flags () {
      global $gCOMMENTVIEWFLAG;

      global $gCOMMENTVIEW;

      global $zAUTHUSER, $zLOCALUSER, $gFOCUSUSERNAME;

      switch ($gCOMMENTVIEW) {
        case COMMENT_VIEW_PROFILE:
          $gCOMMENTVIEWFLAG = "profile";
        break;
        case COMMENT_VIEW_EDITOR:
    
          // Set the default view.
          $gCOMMENTVIEWFLAG = "nested";

          // Check if user is admin or is viewing their own page.
          if ( ($gFOCUSUSERNAME == $zAUTHUSER->Username) and
               ($gSITEDOMAIN == $zAUTHUSER->Domain) ) {
            $gCOMMENTVIEWFLAG = "editor";
          } else {
            if ($zLOCALUSER->userAccess->a == TRUE) {
              $gCOMMENTVIEWFLAG = "editor";
            } // if
          } // if

        break;

        case COMMENT_VIEW_DEFAULT:
          $gCOMMENTVIEWFLAG = "nested";
        break;

        case COMMENT_VIEW_NESTED:
          $gCOMMENTVIEWFLAG = "nested";
        break; 

        case COMMENT_VIEW_THREADED:
          $gCOMMENTVIEWFLAG = "threaded";
        break; 

        case COMMENT_VIEW_FLAT:
          $gCOMMENTVIEWFLAG = "flat";
        break; 

        case COMMENT_VIEW_COMPACT:
          $gCOMMENTVIEWFLAG = "compact";
        break;

        default:
          $gCOMMENTVIEWFLAG = 'nested';
          $gCOMMENTVIEW = COMMENT_VIEW_NESTED;
        break;

      } // switch
    } // Flags

    // Handle the workflow for the comments box.
    function Handle () {
      global $zOLDAPPLE, $zSTRINGS;

      global $bCOMMENTS;

      global $gCOMMENTACTION;
      global $gCOMMENTADDTAB, $gCOMMENTREADTAB;
      global $gCOMMENTSELECTBUTTON;
      global $gCOMMENTMASSLIST;
      global $gSTARTINGID;

      global $gREFERENCEID;

      $bCOMMENTS = "";

      global $gREADDATA, $gADDDATA;
   
      $gADDDATA   = array ("COMMENTACTION" => "ADD",
                           "REFERENCEID" => $gREFERENCEID);
      $gREADDATA  = array ("COMMENTACTION" => "READ",
                           "REFERENCEID" => $gREFERENCEID);

      // Change the select button if anything is eelected.
      if ($zOLDAPPLE->ArrayIsSet ($gCOMMENTMASSLIST) ) $gCOMMENTSELECTBUTTON = 'Select None';

      // PART I: Take Action
      switch ($gCOMMENTACTION) {
        case 'SUBMIT':
          $this->Save ();
          if ($this->Error == 0) {
            $gCOMMENTACTION = "";
          } else {
            $gCOMMENTACTION = "ADD";
          } // if
        break;

        case 'DELETE':
          global $gtID;
          if ($gtID) {
            $this->Select ("parent_tID", $gtID);
            $this->FetchArray ();

            if ($this->CountResult () == 0) {
              // No child comments.  Delete.
              $this->Synchronize ();
              $this->Delete ();
            } else {
              // Child comments exist.  Create placemark.
              $this->Select ("tID", $gtID);
              $this->FetchArray ();
              $this->Subject = DELETED_COMMENT;
              $this->Owner_Icon = NULL;
              $this->Owner_Username = NULL;
              $this->Owner_Domain = NULL;
              $this->Body = NULL;
              $this->Update ();
            } // if
            $this->CleanUp ();
          } // if
        break;

        case 'DELETE_ALL':
          // Check if any items were selected.
          if (!$gCOMMENTMASSLIST) {
            $this->Message = __("None Selected");
            $this->Error = -1;
            break;
          } // if

          foreach ($gCOMMENTMASSLIST as $count => $tid) {
            $this->Select ("parent_tID", $tid);
            $this->FetchArray ();

            if ($this->CountResult () == 0) {
              // No child comments.  Delete.
              $this->tID = $tid;
              $this->Delete ();
            } else {
              // Child comments exist.  Create placemark.
              $this->Select ("tID", $tid);
              $this->FetchArray ();
              $this->Subject = DELETED_COMMENT;
              $this->Owner_Icon = NULL;
              $this->Owner_Username = NULL;
              $this->Owner_Domain = NULL;
              $this->Body = NULL;
              $this->Update ();
            } // if
            $this->CleanUp ();
          } // if
          $gCOMMENTMASSLIST = array ();
          $gCOMMENTSELECTBUTTON = 'Select All';
        break;

        case 'SELECT_ALL':
          $gCOMMENTSELECTBUTTON = 'Select None';
        break;

        case 'SELECT_NONE':
          $gCOMMENTMASSLIST = array ();
          $gCOMMENTSELECTBUTTON = 'Select All';
        break;

      } // switch
    
      $zOLDAPPLE->SetTag ('COMMENTREADTAB', $gCOMMENTREADTAB); 
      $zOLDAPPLE->SetTag ('COMMENTADDTAB', $gCOMMENTADDTAB); 

      // PART II: Parse the HTML.
      switch ($gCOMMENTACTION) {
        case 'EDIT':
          $bCOMMENTS = "";
        break;
    
        case 'ADD':
          global $zAUTHUSER;
          $gCOMMENTADDTAB = "";
          $zOLDAPPLE->SetTag ('COMMENTADDTAB', $gCOMMENTADDTAB); 
          $bCOMMENTS .= $this->AddForm ($gREFERENCEID);
        break;
    
        default:
          $gCOMMENTREADTAB = "";
          $zOLDAPPLE->SetTag ('COMMENTREADTAB', $gCOMMENTREADTAB); 
          $bCOMMENTS .= $this->Top ();
          $start = 0;
          if ($gSTARTINGID) $start = $gSTARTINGID;
          $bCOMMENTS .= $this->Listing ($gREFERENCEID, 0, $start);
          $bCOMMENTS .= $this->Bottom ();
        break;
      } // switch

      $zOLDAPPLE->UnsetTag ('COMMENTREADTAB', $gCOMMENTREADTAB); 
      $zOLDAPPLE->UnsetTag ('COMMENTADDTAB', $gCOMMENTADDTAB); 

      return (true);

    } // Handle

    // Initialize the commenting subsystem.
    function Initialize () {

      global $zAUTHUSER, $zLOCALUSER, $zOLDAPPLE;

      global $gCOMMENTVIEW, $gCOMMENTVIEWTYPE, $gCOMMENTVIEWADMIN; 
      global $gPOSTDATA;
      global $gFOCUSUSERNAME;
      global $gSITEDOMAIN;
      global $gCOMMENTSELECTBUTTON;
      global $gSCROLLSTEP, $gSCROLLSTART, $gSCROLLMAX;
      global $gCONTINUEFLAG;

      $gCONTINUEFLAG = FALSE;

      $this->Context = $zOLDAPPLE->Context;
      $this->CommentContext = 'user.comments';
      
      $gSCROLLSTEP[$this->CommentContext] = 20;
      
      $gCOMMENTVIEWTYPE = "COMMENTVIEW";

      // Display the select all button by default.
      $gCOMMENTSELECTBUTTON = 'Select All';

      // Depracate the commentview.
      if ($gCOMMENTVIEWADMIN != "") {
        $gCOMMENTVIEW = $gCOMMENTVIEWADMIN;
      } // if
      $gCOMMENTVIEWADMIN = $gCOMMENTVIEW;
      $gPOSTDATA['COMMENTVIEW'] = $gCOMMENTVIEW;

      // Check if user is admin or is viewing their own page.
      if ( ($gFOCUSUSERNAME == $zAUTHUSER->Username) and
           ($gSITEDOMAIN == $zAUTHUSER->Domain) ) {
        $gCOMMENTVIEWTYPE = "COMMENTVIEWADMIN";
      } else {
        if ($zLOCALUSER->userAccess->e == TRUE) {
          $gCOMMENTVIEWTYPE = "COMMENTVIEWADMIN";
       } // if
      } // if

      // Determine which mode we are viewing in, and set flags accordingly.
      $this->Flags ();
    } // Initialize

    // Count the comments attached to certain Context/Reference ID
    function CountComments ($pRID, $pCONTEXT) {

      $COUNT = new cCOMMENTINFORMATION ();

      $commentcriteria = array ("rID"          => $pRID,
                                "Context"      => $pCONTEXT);

      $COUNT->SelectByMultiple ($commentcriteria);

      $total = $COUNT->CountResult ();

      unset ($COUNT);

      return ($total);

    } // CountComments

    // Notify the user that a comment has been replied to.
    function NotifyReply ($pEMAIL, $pCOMMENTEDUSER, $pCOMMENTEDUSERNAME, $pCOMMENTINGUSER) {
      global $zSTRINGS, $zOLDAPPLE, $zFOCUSUSER;

      // Return if comment notification is turned off.
      if ($zFOCUSUSER->userSettings->Get ("ReplyNotification") == NOTIFICATION_OFF) {
        return (FALSE);
      } // 

      if (!$pCOMMENTINGUSER) {
        $zSTRINGS->Lookup ('LABEL.ANONYMOUS.FULLNAME', $this->Context);
        $pCOMMENTINGUSER = $zSTRINGS->Output;
      } // if

      global $gCOMMENTINGUSER, $gCOMMENTEDUSER;
      $gCOMMENTINGUSER = $pCOMMENTINGUSER;
      $gCOMMENTEDUSER = $pCOMMENTEDUSER;

      global $gINFOURL, $gSITEURL;

      switch (strtoupper($this->Context)) {
        case 'USER.JOURNAL':
          $gINFOURL = $gSITEURL . "/profile/" . $pCOMMENTEDUSERNAME . "/journal/" . $this->rID . "/";
        break;
        case 'CONTENT.ARTICLES':
          $gINFOURL = $gSITEURL . "/articles/" . $this->rID . "/";
        break;
        default:
          $gINFOURL = "Not Determined Yet.  Context: $this->Context";
        break;
      } // switch

      $to = $pEMAIL;

      $zSTRINGS->Lookup ('MAIL.SUBJECT', 'USER.COMMENTS');
      $subject = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.BODY', 'USER.COMMENTS');
      $body = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROM', 'USER.COMMENTS');
      $from = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROMNAME', 'USER.COMMENTS');
      $fromname = $zSTRINGS->Output;

      $zOLDAPPLE->Mailer->From = $from;
      $zOLDAPPLE->Mailer->FromName = $fromname;
      $zOLDAPPLE->Mailer->Body = $body;
      $zOLDAPPLE->Mailer->Subject = $subject;
      $zOLDAPPLE->Mailer->AddAddress ($to);
      $zOLDAPPLE->Mailer->AddReplyTo ($from);

      $zOLDAPPLE->Mailer->Send();

      $zOLDAPPLE->Mailer->ClearAddresses();

      unset ($to);
      unset ($subject);
      unset ($body);

      return (TRUE);

    } // NotifyReply

    // Notify the user that a comment has been added.
    function NotifyArticle () {
      global $zSTRINGS, $zOLDAPPLE, $zFOCUSUSER, $zAUTHUSER;

      $ARTICLE = new cCONTENTARTICLES();
      $ARTICLE->Select ("tID", $this->rID);
      $ARTICLE->FetchArray();

      $USER = new cUSER ();

      // Return if comment notification is turned off.
      // NOTE: This requires an XML request.
      // if ($zFOCUSUSER->userSettings->Get ("ArticleNotification") == NOTIFICATION_OFF) {
        // return (FALSE);
      // } // 

      // Don't send a message if a user is commenting on their own article.
      if ( ($ARTICLE->Submitted_Username == $zAUTHUSER->Username) and
           ($ARTICLE->Submitted_Domain == $zAUTHUSER->Domain) ) return (FALSE);

      // In the event of an anonymous articles submitter, skip step.
      if ($ARTICLE->Submitted_Username == ANONYMOUS) break;

      $zFOCUSUSER->Username = $ARTICLE->Submitted_Username;

      $FRIEND = new cFRIENDINFORMATION();
      $FRIEND->Username = $ARTICLE->Submitted_Username;
      $FRIEND->Domain = $ARTICLE->Submitted_Domain;
      list ($fullname, $online, $email) = $FRIEND->GetUserInformation();

      $zFOCUSUSER->userProfile->Email = $email;
      $zFOCUSUSER->userProfile->Fullname = $fullname;

      global $gCOMMENTINGUSER, $gCOMMENTEDUSER;

      $to = $email;
      $gCOMMENTEDUSER = $fullname;

      $FRIEND->Username = $zAUTHUSER->Username;
      $FRIEND->Domain = $zAUTHUSER->Domain;
      list ($gCOMMENTINGUSER, $online, $email) = $FRIEND->GetUserInformation();

      global $gINFOURL, $gSITEURL;
      $gINFOURL = $gSITEURL . "/articles/" . $this->rID . "/#comments";

      $zSTRINGS->Lookup ('MAIL.SUBJECT', 'CONTENT.ARTICLES.COMMENTS');
      $subject = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.BODY', 'CONTENT.ARTICLES.COMMENTS');
      $body = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROM', 'CONTENT.ARTICLES.COMMENTS');
      $from = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROMNAME', 'CONTENT.ARTICLES.COMMENTS');
      $fromname = $zSTRINGS->Output;

      $zOLDAPPLE->Mailer->From = $from;
      $zOLDAPPLE->Mailer->FromName = $fromname;
      $zOLDAPPLE->Mailer->Body = $body;
      $zOLDAPPLE->Mailer->Subject = $subject;
      $zOLDAPPLE->Mailer->AddAddress ($to);
      $zOLDAPPLE->Mailer->AddReplyTo ($from);

      $zOLDAPPLE->Mailer->Send();

      $zOLDAPPLE->Mailer->ClearAddresses();

      unset ($to);
      unset ($subject);
      unset ($body);

      return (TRUE);

    } // NotifyArticle

    // Notify the user that a comment has been added.
    function NotifyJournal ($pEMAIL, $pCOMMENTEDUSER, $pCOMMENTEDUSERNAME, $pCOMMENTINGUSER) {
      global $zSTRINGS, $zOLDAPPLE, $zFOCUSUSER, $zAUTHUSER;

      // Don't send a message if a user is commenting on their own journal.
      if ( ($zFOCUSUSER->Username == $zAUTHUSER->Username) and
           ($gSITEDOMAIN == $zAUTHUSER->Domain) ) return (FALSE);

      // Return if comment notification is turned off.
      if ($zFOCUSUSER->userSettings->Get ("JournalNotification") == NOTIFICATION_OFF) {
        return (FALSE);
      } // 

      global $gCOMMENTINGUSER, $gCOMMENTEDUSER;
      $gCOMMENTINGUSER = $pCOMMENTINGUSER;
      $gCOMMENTEDUSER = $pCOMMENTEDUSER;

      global $gINFOURL, $gSITEURL;
      $gINFOURL = $gSITEURL . "/profile/" . $pCOMMENTEDUSERNAME . "/journal/";

      $to = $pEMAIL;

      $zSTRINGS->Lookup ('MAIL.SUBJECT', 'USER.JOURNAL.COMMENTS');
      $subject = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.BODY', 'USER.JOURNAL.COMMENTS');
      $body = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROM', 'USER.JOURNAL.COMMENTS');
      $from = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROMNAME', 'USER.JOURNAL.COMMENTS');
      $fromname = $zSTRINGS->Output;

      $zOLDAPPLE->Mailer->From = $from;
      $zOLDAPPLE->Mailer->FromName = $fromname;
      $zOLDAPPLE->Mailer->Body = $body;
      $zOLDAPPLE->Mailer->Subject = $subject;
      $zOLDAPPLE->Mailer->AddAddress ($to);
      $zOLDAPPLE->Mailer->AddReplyTo ($from);

      $zOLDAPPLE->Mailer->Send();

      $zOLDAPPLE->Mailer->ClearAddresses();

      unset ($to);
      unset ($subject);
      unset ($body);

      return (TRUE);

    } // NotifyJournal

    // Notify the user that a comment has been added.
    function NotifyProfile ($pEMAIL, $pCOMMENTEDUSER, $pCOMMENTEDUSERNAME, $pCOMMENTINGUSER) {
      global $zSTRINGS, $zOLDAPPLE, $zFOCUSUSER, $zAUTHUSER;

      // Return if comment notification is turned off.
      if ($zFOCUSUSER->userSettings->Get ("ProfileNotification") == NOTIFICATION_OFF) {
        return (FALSE);
      } // 

      // Don't send a message if a user is commenting on their own profile.
      if ( ($zFOCUSUSER->Username == $zAUTHUSER->Username) and
           ($gSITEDOMAIN == $zAUTHUSER->Domain) ) return (FALSE);

      global $gCOMMENTINGUSER, $gCOMMENTEDUSER;
      $gCOMMENTINGUSER = $pCOMMENTINGUSER;
      $gCOMMENTEDUSER = $pCOMMENTEDUSER;

      global $gINFOURL, $gSITEURL;
      $gINFOURL = $gSITEURL . "/profile/" . $pCOMMENTEDUSERNAME . "/info/#comments";

      $to = $pEMAIL;

      $zSTRINGS->Lookup ('MAIL.SUBJECT', 'USER.INFO.COMMENTS');
      $subject = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.BODY', 'USER.INFO.COMMENTS');
      $body = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROM', 'USER.INFO.COMMENTS');
      $from = $zSTRINGS->Output;

      $zSTRINGS->Lookup ('MAIL.FROMNAME', 'USER.INFO.COMMENTS');
      $fromname = $zSTRINGS->Output;

      $zOLDAPPLE->Mailer->From = $from;
      $zOLDAPPLE->Mailer->FromName = $fromname;
      $zOLDAPPLE->Mailer->Body = $body;
      $zOLDAPPLE->Mailer->Subject = $subject;
      $zOLDAPPLE->Mailer->AddAddress ($to);
      $zOLDAPPLE->Mailer->AddReplyTo ($from);

      $zOLDAPPLE->Mailer->Send();

      $zOLDAPPLE->Mailer->ClearAddresses();

      unset ($to);
      unset ($subject);
      unset ($body);

      return (TRUE);

    } // NotifyProfile

    // Goes through and removes all deleted comments with no children.
    function CleanUp () {
      global $zOLDAPPLE;

      global $gREFERENCEID;

      $criteria = array ("rID"     => $gREFERENCEID,
                         "Context" => $zOLDAPPLE->Context,
                         "Subject" => DELETED_COMMENT);
      $this->SelectByMultiple ($criteria);

      // Break out if no deleted comments left.  
      if ($this->CountResult() == 0) return (TRUE);

      while ($this->FetchArray ()) {
        $COMMENTCHILDREN = new cCOMMENTINFORMATION ();
        $childcriteria = array ("parent_tID" => $this->tID,
                                "rID"        => $gREFERENCEID,
                                "Context"    => $zOLDAPPLE->Context);
        $COMMENTCHILDREN->SelectByMultiple ($childcriteria);

        // If no children, delete.
        if ($COMMENTCHILDREN->CountResult () == 0) {
          $this->Delete ();
          $this->CleanUp ();
        } // if
        unset ($COMMENTCHILDREN);
      } // while
    } // CleanUp

  } // cCOMMENTINFORMATION

?>
