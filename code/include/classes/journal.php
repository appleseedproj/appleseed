<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: journal.php                             CREATED: 07-25-2005 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 07-25-2005 +
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
  // | DESCRIPTION.  Photo class definitions.                            |
  // +-------------------------------------------------------------------+

  // Photo sets class.
  class cJOURNALPOST extends cDATACLASS {
 
    // Keys
    var $tID, $userAuth_uID; 
    
    // Variables
    var $userIcons_Filename, $Posted, $Stamp, $Content;
    var $Title, $Subtitle, $Notification, $Tags;
    var $Cascade;

    // Classes
    var $journalPrivacy;

    function cJOURNALPOST ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'journalPost';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->userIcons_Filename = '';
      $this->Posted = '';
      $this->fPosted = '';
      $this->Stamp = '';
      $this->Content = '';
      $this->Title = '';
      $this->Subtitle = '';
      $this->Notification = '';
      $this->Tags = '';
      $this->Error = 0;
      $this->Message = '';
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

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

   'userIcons_Filename'  => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

        'Posted'         => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),

        'Stamp'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),

        'Content'        => array ('max'        => '65535',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Title'          => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Notification'   => array ('max'        => '',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Tags'           => array ('max'        => '128',
                                   'min'        => '0',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + = ',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'FILENAME'),

        'Description'    => array ('max'        => '4096',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Keywords'       => array ('max'        => '128',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

      );

      // Internal class references.
      $this->journalPrivacy   = new cJOURNALPRIVACY ($pDEFAULTCONTEXT);

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
 
    function DeleteList ($pLIST) {
      global $zSTRINGS;

      foreach ($pLIST as $count => $refid) {
        $this->tID = $refid;
        $this->Delete ();
      } // if

      if ($this->Error == -1) {
        $zSTRINGS->Lookup ('ERROR.DELETEALL');
        $this->Message = $zSTRINGS->Output;
        return (FALSE);
      } else {
        $zSTRINGS->Lookup ('MESSAGE.DELETEALL');
        $this->Message = $zSTRINGS->Output;
      } // if
     
      return (TRUE);
    } // DeleteList

    function GetId ($pDIRECTION) {

      global $zFOCUSUSER, $zAUTHUSER;
      global $zAPPLE;

      $TEMPJOURNAL = new cJOURNALPOST ();

      $currentid = $this->tID;

      if ($pDIRECTION == NEWER) {
        $sort = "";
        $direction = ">";
      } else {
        $sort = "DESC";
        $direction = "<";
      } // if

      $where = "userAuth_uID = " . $zFOCUSUSER->uID . 
               " and Posted " . $direction . " '" . $this->Posted . "' order by Posted " . $sort;

      $TEMPJOURNAL->SelectWhere ($where);
      $TEMPJOURNAL->FetchArray ();

      // Check if journal is hidden or blocked for this user.
      $gPRIVACYSETTING = $TEMPJOURNAL->journalPrivacy->Determine ("zFOCUSUSER", "zAUTHUSER", "journalPost_tID", $TEMPJOURNAL->tID);

      // Keep pulling from the database until we find a non-hidden/blocked entry.
      while ($TEMPJOURNAL->CountResult () > 0) {

        // Found an available entry.  Break.
        if ($zAPPLE->CheckSecurity ($gPRIVACYSETTING) == FALSE) break;

        $where = "userAuth_uID = " . $zFOCUSUSER->uID . 
                 " and Posted " . $direction . " '" . $TEMPJOURNAL->Posted . "' order by Posted " . $sort;

        $TEMPJOURNAL->SelectWhere ($where);
        $TEMPJOURNAL->FetchArray ();

        // Check if journal is hidden or blocked for this user.
        $gPRIVACYSETTING = $TEMPJOURNAL->journalPrivacy->Determine ("zFOCUSUSER", "zAUTHUSER", "journalPost_tID", $TEMPJOURNAL->tID);

      } // while

      $resultid = $TEMPJOURNAL->tID;

      $result = $TEMPJOURNAL->CountResult ();

      unset ($TEMPJOURNAL);

      if ( ($resultid == $currentid) or 
           ($result == 0) ) {
        return (FALSE);
      } // if

      return ($resultid);

    } // GetID

    // Select the older journal entry.
    function GetOlderId () {

      return ($this->GetId (OLDER));

    } // GetOlderId

    // Select the newer journal entry.
    function GetNewerId () {

      return ($this->GetId (NEWER));

    } // GetNewerId

    function JournalScroll ($pTARGET, $pFOOTNOTE) {

      global $zAPPLE;

      $olderid = $this->GetOlderId ();
      $newerid = $this->GetNewerId ();

      global $gSCROLLCLASS;
   
      $gSCROLLCLASS = "journal";

      global $gPOSTDATA;
      global $gACTION;

      $gPOSTDATA['ACTION'] = $gACTION;

      global $gTARGET;
      global $gTHEMELOCATION;

      $gPOSTDATA['tID'] = $newerid;

      if ($newerid) {
        $gTARGET = $pTARGET . $newerid . "/";
        $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/tabs/common/newer.aobj");
      } else {
        $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/tabs/common/newer.off.aobj");
      } // if

      global $gFOOTNOTE;
      $gFOOTNOTE = $pFOOTNOTE;
      $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/tabs/common/footnote.special.aobj");

      $gPOSTDATA['tID'] = $olderid;

      if ($olderid) {
        $gTARGET = $pTARGET . $olderid . "/";
        $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/tabs/common/older.aobj");
      } else {
        $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/tabs/common/older.off.aobj");
      } // if

      // Restore the global target variable.
      $gTARGET = $pTARGET;

      return (TRUE);
       
    } // JournalScroll

  } // cJOURNALPOST

  // Photo privacy class.
  class cJOURNALPRIVACY extends cPRIVACYCLASS {
 
    // Keys
    var $tID, $userAuth_uID, $photoSets_tID, $friendCircles_sID, $Access;

    // Variables
    var $Filename, $Width, $Height;

    function cJOURNALPRIVACY ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'journalPrivacy';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->journalPost_tID = '';
      $this->friendCircles_sID = '';
      $this->Access = '';
      $this->Error = 0;
      $this->Message = '';
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

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

      'journalPost_tID'  => array ('max'        => '',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

     'friendCircles_sID' => array ('max'        => '',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Access'         => array ('max'        => '1024',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
 
  } // cJOURNALPRIVACY

?>
