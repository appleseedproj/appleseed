<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: content.php                             CREATED: 09-06-2006 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 09-06-2006 +
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
  // | DESCRIPTION:  Extended Content class definitions.                 |
  // +-------------------------------------------------------------------+

  require_once ("legacy/code/include/classes/BASE/content.php");
 
  // Content articles class.
  class cEXTENDEDCONTENTARTICLES extends cCONTENTARTICLES {

    // Convert ASD tags to tags that HTMLPurifier will skip.
    function ASDToSafe ($pFIELDNAME) {

      // NOTE: Find a centralized way to do this.

      $pattern = "/<asd\s+(.*?)\s*\/>/s";
      $replacement = "[@#[asd $1 ]#@]";
      $this->$pFIELDNAME = preg_replace ($pattern, $replacement, $this->$pFIELDNAME);
       
      return (TRUE);
    } // ASDToSafe

    // Convert safe tags back to ASD tags.
    function SafeToASD ($pFIELDNAME) {

      // NOTE: Find a centralized way to do this.

      $pattern = "/\[\@\#\[asd\s+(.*?)\s*\]\#\@\]/s";
      $replacement = "<asd $1 />";
      $this->$pFIELDNAME = preg_replace ($pattern, $replacement, $this->$pFIELDNAME);

      return (TRUE);
    } // SafeToASD

    // Purify all string data.
    function Purify () {
      
      // NOTE: Find a centralized way to do this.

      global $zOLDAPPLE;

      foreach ($this->FieldNames as $fieldname) {
        switch ($this->FieldDefinitions[$fieldname]['datatype']) {
          case 'STRING':
            $this->ASDtoSafe ($fieldname);
            $this->$fieldname = $zOLDAPPLE->Purifier->purify ($this->$fieldname);
            $this->SafeToASD ($fieldname);
          break;

          default:
          break;
        } // if
      } // foreach

    } // Purify

    // Buffer the articles listing.
    function BufferArticlesListing () {
      global $zOLDAPPLE, $zSTRINGS;

      global $gFRAMELOCATION;
      global $gCOMMENTCOUNT;

      global $bCOMMENTCOUNT, $bARTICLES;

      // Select the latest articles.
      $this->Select ("Verification", 1, "Stamp DESC");

      if ($this->CountResult() == 0) {
        $this->Message = __("No Results Found");
      } // if
      
      $bARTICLES = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      // Loop through the results.
      while ($this->FetchArray ()) {
        // Format the date stamp.
        $this->FormatVerboseDate ("Stamp");
   
        global $gUSERNAME;
        $COMMENTS = new cCOMMENTINFORMATION ();
      
        // NOTE: Why do I have to redeclare the scope for this variable to work?
        global $gCOMMENTCOUNT;
        $gCOMMENTCOUNT = $COMMENTS->CountComments ($this->tID, $this->PageContext);
 
        $bCOMMENTCOUNT = __("Comment Count", array ("commentcount" => $gCOMMENTCOUNT));
 
        global $bARTICLEICON;
        $bARTICLEICON = $zOLDAPPLE->BufferUserIcon ($this->Submitted_Username, $this->Submitted_Domain);
     
        $bARTICLES .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
   
        unset ($gCOMMENTCOUNT);
   
      } // while
      
      $bARTICLES .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

    } // BufferArticlesListing
    
    function Initialize () {

      global $zLOCALUSER, $zOLDAPPLE;

      global $gSITEURL;
      global $gACTION, $gTARGET, $gARTICLEREQUEST;

      global $gARTICLESTAB;

      global $gCONTENTARTICLESVIEWTAB, $gCONTENTARTICLESSUBMITTAB;
      global $gCONTENTARTICLESQUEUETAB;
      
      global $gSCROLLSTEP;
      
      $gSCROLLSTEP[$zOLDAPPLE->Context] = 10;

      $gTARGET = $gSITEURL . "/articles/";
    
      // Determine which tab to display.
      switch (strtoupper ($gARTICLEREQUEST) ) {
        case 'SUBMIT':
          $gCONTENTARTICLESSUBMITTAB = '';
        break;
        case 'QUEUE':
          $gCONTENTARTICLESQUEUETAB = '';
        break;
        default:
          $gCONTENTARTICLESVIEWTAB = '';
        break;
      } // switch
    
      $gARTICLESTAB = 'main';

      // Determine which tabs to display;
      if ($zLOCALUSER->userAccess->e == TRUE) $gARTICLESTAB = 'editor';

      return (TRUE);

    } // Initialize

    function HandleQueue () {

      global $zOLDAPPLE, $zLOCALUSER, $zHTML;

      global $gTARGET;
      $gTARGET = "/articles/queue/";

      global $gFRAMELOCATION, $gTHEMELOCATION;

      global $gACTION, $gCRITERIA, $gSCROLLSTEP, $gSCROLLMAX;
      global $gPOSTDATA, $gEXTRAPOSTDATA;
      global $gSORT;

      // Show the Unverified first.
      $gSORT = "Verification,Stamp DESC";

      global $gtID;

      // PART I: Determine appropriate action.
      switch ($gACTION) {

        case 'SAVE':
          // Check if user has write access;
          if ($zLOCALUSER->userAccess->e == FALSE) {
            $ADMINDATA->Message = __("Write Access Denied");
            $this->Error = -1;
            break;        
          } // if

          // Synchronize Data
          $this->Synchronize();

          $this->Submitted_Username = SQL_SKIP;
          $this->Submitted_Domain = SQL_SKIP;
          $this->Formatting = SQL_SKIP;
    
          if ($this->tID == "") {
            $this->Message = __("Error On Page");
          } else {
            $this->Sanity();
            if (!$this->Error) {
              $this->Message = __("Record Updated");
              $this->Update();
            } // if
          } // if
        break;

        case 'EDIT':
        break;

        default:
          global $gPENDING;
          $gPENDING = $this->CountPendingArticles ();
          if ($gPENDING > 0) {
            $this->Message = __("Articles Pending Approval", array ("pending" => $gPENDING));
          } // if
        break;
      } // switch

      // PART II: Load the necessary data from the database.
      switch ($gACTION) {

        case 'EDIT':
          $this->Synchronize();
          $this->Select ("tID", $this->tID);
          $this->FetchArray();

          global $gSUBMITTEDUSERNAME, $gSUBMITTEDDOMAIN;
          $gSUBMITTEDUSERNAME = $this->Submitted_Username;
          $gSUBMITTEDDOMAIN = $this->Submitted_Domain;

          global $gSTAMPLIST;
          $gSTAMPLIST = $zHTML->SplitDate ($this->Stamp);

        break;

        case 'SAVE':
        default:
          if ($gCRITERIA) {
            $this->SelectByAll($gCRITERIA, $gSORT, 1);
    
            // If only one result, jump right to edit form.
            if ( ($this->CountResult() == 1) AND ($gACTION == 'SEARCH') ) {
              // Fetch the data.
              $this->FetchArray();
              $this->Select ("tID", $this->tID);
              $gACTION = 'EDIT';
              $gCRITERIA = ''; $gPOSTDATA['CRITERIA'] = '';
            } // if
          } else {
            $this->Select("", "", $gSORT);
          } // if
        break;
        
      } // switch

      // PART III: Pre-parse the html for the main window. 
      
      // Buffer the main listing.
      ob_start ();  

      if ($zLOCALUSER->userAccess->e == TRUE) {
        // Choose an action
        switch ($gACTION) {
          case 'EDIT':
            $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/queue/edit.aobj", INCLUDE_SECURITY_NONE);
          break;
          case 'SAVE':
            // Skip to the default.
          default:
            if ($this->Error == 0) {
    
              $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/queue/list.top.aobj", INCLUDE_SECURITY_NONE);
    
              // Calculate scroll values.
              $gSCROLLMAX[$zOLDAPPLE->Context] = $this->CountResult();
    
              // Adjust for a recently deleted entry.
              $zOLDAPPLE->AdjustScroll ('content.articles', $this);
    
              // Check if any results were found.
              if ($gSCROLLMAX[$zOLDAPPLE->Context] == 0) {
                $this->Message = __("No Results Found");
                $this->Broadcast();
              } // if

              // Loop through the list.
              for ($listcount = 0; $listcount < $gSCROLLSTEP[$zOLDAPPLE->Context]; $listcount++) {
               if ($this->FetchArray()) {
                $output = $zOLDAPPLE->Format ($this->Full, FORMAT_VIEW);
    
                global $gEXTRAPOSTDATA;
                $gEXTRAPOSTDATA['ACTION'] = "EDIT"; 
                $gEXTRAPOSTDATA['tID']    = $this->tID;

                if ($this->Submitted_Username == ANONYMOUS) {
                  $zSTRINGS->Lookup ("LABEL.ANONYMOUS", $zOLDAPPLE->Context);
                  $this->Submitted_Username = $zSTRINGS->Output;
                } // if

                switch ($this->Verification) {
                  case ARTICLE_PENDING:
                    $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/queue/list.middle.unverified.aobj", INCLUDE_SECURITY_NONE);
                  break;
                  case ARTICLE_APPROVED:
                    $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/queue/list.middle.aobj", INCLUDE_SECURITY_NONE);
                  break;
                  case ARTICLE_REJECTED:
                    $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/queue/list.middle.rejected.aobj", INCLUDE_SECURITY_NONE);
                  break;
                } // switch

                unset ($gEXTRAPOSTDATA);

               } else {
                break;
               } // if
              } // for

              $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/queue/list.bottom.aobj", INCLUDE_SECURITY_NONE);

            } elseif ($gACTION == 'SAVE') {
              if ($gtID) {
                $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/queue/edit.aobj", INCLUDE_SECURITY_NONE);
              } else {
                $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/queue/new.aobj", INCLUDE_SECURITY_NONE);
              } // if
            } // if
          break;
        } // switch
      } else {
        // Access Denied
        $zOLDAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
        $zOLDAPPLE->End();
      } // if

      // Retrieve output buffer.
      $returnoutput = ob_get_clean (); 
  
      // End buffering.
      ob_end_clean (); 

      return ($returnoutput);

    } // HandleQueue

    function CountPendingArticles () {
      
      $this->Select ("Verification", ARTICLE_PENDING);

      return ($this->CountResult ());

    } // CountPendingArticles

    // Extend functionality of BASE::Update () but purify input first.
    function Update () {
      $this->Purify ();
      parent::Update ();
    } // Update

    // Extend functionality of BASE::Add () but purify input first.
    function Add () {
      $this->Purify ();
      parent::Add ();
    } // Add

  } // cEXTENDEDCONTENTARTICLES

  class cCONTENTNODES extends cBASECONTENTNODES {
     
    function BufferLatestNodes () {
      global $zOLDAPPLE, $zAUTHUSER, $zSTRINGS;
      
      global $gSITEDOMAIN, $gFRAMELOCATION, $gTABLEPREFIX;
      global $gNODESUMMARY;
      
      $buffer = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/site/latest/nodes/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      $this->Select ('Verification', NODE_VERIFIED, 'Stamp LIMIT 10');
      
      if ($this->CountResult() == 0) {
        $buffer = NULL;
        return (FALSE);
      } else {
        while ($this->FetchArray ()) {
          $nodedomain = $this->Domain;
          $nodedomainlink =  "http://" . $zAUTHUSER->Domain;
          if ( (!$zAUTHUSER->Anonymous) and ($zAUTHUSER->Domain != $this->Domain) ) {
            $target = $this->Domain;
            $location = "/";
            $nodedomainlink .= "/login/bounce/?target=" . $target . "&location=" . $location;
          } else { 
            $nodedomainlink = 'http://' . $this->Domain;
          } // if
          $gNODESUMMARY = $this->Summary;
          $zOLDAPPLE->SetTag ('NODEDOMAINLINK', $nodedomainlink);
          $zOLDAPPLE->SetTag ('NODEDOMAIN', $nodedomain);
          $zOLDAPPLE->SetTag ('NODEUSERS', $this->Users);
          $buffer .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/site/latest/nodes/middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        } // while
      } // if
      
      $buffer .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/site/latest/nodes/bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      return ($buffer);
    } // BufferLatestNodes
    
  } // cCONTENTNODES
