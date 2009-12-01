<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: tags.php                                CREATED: 06-07-2007 + 
  // | LOCATION: /code/include/classes/BASE/        MODIFIED: 06-07-2007 +
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
  // | Part of the Appleseed BASE API                                    |
  // | VERSION:      0.7.3                                               |
  // | DESCRIPTION:  Tag class definitions. Reusable functions for       |
  // |               managing tags.                                      |
  // +-------------------------------------------------------------------+

  // Tag information class.
  class cTAGINFORMATION extends cBASEDATACLASS {

    var $tID, $Name;

    function cTAGINFORMATION ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'tagInformation';
      $this->tID = '';
      $this->PageContext = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->FieldNames = '';
      $this->PrimaryKey = 'tID';
 
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

        'Name'           => array ('max'        => '32',
                                   'min'        => '3',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + =',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),
      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
   } // Constructor

  } // cTAGINFORMATION

  // Tag list class.
  class cTAGLIST extends cBASEDATACLASS {

    var $tID, $tagInformation_tID, $rID, $Context, $Username, $Domain, $Stamp;

    function cTAGLIST ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'tagList';
      $this->tID = '';
      $this->Title = '';
      $this->tID = '';
      $this->tagInformation_tID = '';
      $this->userAuth_uID = '';
      $this->rID = ''; 
      $this->Context = ''; 
      $this->Username = ''; 
      $this->Domain = ''; 
      $this->Stamp = '';
      $this->PageContext = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->FieldNames = '';
      $this->PrimaryKey = 'tID';
 
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

 'tagInformation_tID'    => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

 'userAuth_uID'          => array ('max'        => '',
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
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Context'         => array ('max'        => '64',
                                   'min'        => '3',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Username'       => array ('max'        => '64',
                                   'min'        => '3',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Domain'         => array ('max'        => '64',
                                   'min'        => '3',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Stamp'          => array ('max'        => '128',
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
      
      $this->tagInformation = new cTAGINFORMATION ();
 
   } // Constructor
   
   function CreateDisplay ($pLINK, $pREFERENCEID, $pOWNERID = NULL) {
      global $zAPPLE;
      global $zAUTHUSER, $zLOCALUSER, $zFOCUSUSER;
      
      global $gACTION, $gTAGCHECKED;
      
      global $gFRAMELOCATION;
      
      $context = $zAPPLE->Context;
      
      $view = 'read';
      if (!$zAUTHUSER->Anonymous) $view = 'write';
      
      // Check  if user is owner or admin.
      if ( ($zLOCALUSER->uID == $zFOCUSUSER->uID) or
          ( ($zLOCALUSER->userAccess->a == TRUE) and 
            ($zLOCALUSER->userAccess->w == TRUE) ) ) {
        // User has editing access.
        $view = 'edit';
      } // if
      
      $restricted = NULL;
      // Check if user has used up all three tags.
      if ($view == 'write') {
        $criteria = array ("rID"     => $pREFERENCEID,
                           "Context" => $context,
                           "Username"=> $zAUTHUSER->Username,
                           "Domain"  => $zAUTHUSER->Domain);
        $this->SelectByMultiple ($criteria);
        $taggedcount = $this->CountResult();
        
        if ($taggedcount == USER_TAG_LIMIT) {
          $restricted = '.restricted';
        } // if
                         
      } // if
      
      $tagList = $this->TableName;
      $tagInfo = $this->tagInformation->TableName;
      
      // Select from the tag listing.
      $sql_query = "SELECT   $tagInfo.Name, count($tagInfo.Name) AS Amount
                    FROM     $tagList, $tagInfo
                    WHERE    $tagList.rID = $pREFERENCEID
                    AND      $tagList.userAuth_uID = $pOWNERID
                    AND      $tagList.tagInformation_tID = $tagInfo.tID
                    GROUP BY $tagInfo.tID
                    ORDER BY $tagInfo.Name ASC
      ";
      $this->Query ($sql_query);
      
      $count = 0; $max = 10;
      $results = array ();
      
      while ($this->FetchArray ()) {
        $results[$count]['Name'] = $this->Name;
        $results[$count]['Amount'] = $this->Amount;
        $results[$count]['Username'] = $this->Username;
        $results[$count]['Domain'] = $this->Domain;
        if ($this->Amount > $max) $max = $this->Amount;
        $count++;
      } // while
      $return = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/tags/$view/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      global $gTAGPOSTDATA;
      
      foreach ($results as $count => $data) {
        $gTAGCHECKED = FALSE;
        global $gTAGNAME, $gTAGCLASS, $gTAGLINK;
        
        $size = floor (($data['Amount'] / $max) * 10);
        $gTAGNAME = strtolower ($data['Name']);
        $gTAGCLASS = 'tagsize' . $size;
        $gTAGLINK = $pLINK . $gTAGNAME . '/';
        $gTAGNAME = str_replace ('_', ' ', $gTAGNAME);
        
        // Select from the tag listing.
        $name = $data['Name'];
        $username = $zAUTHUSER->Username;
        $domain = $zAUTHUSER->Domain;
        
        $owner = NULL;
        if ($view == 'write') {
          // Check if authuser owns this tag.
          $sql_query = "SELECT   $tagList.Username, $tagList.Domain
                        FROM     $tagList, $tagInfo
                        WHERE    $tagList.rID = $pREFERENCEID
                        AND      $tagList.userAuth_uID = $pOWNERID
                        AND      $tagList.tagInformation_tID = $tagInfo.tID
                        AND      $tagInfo.Name = '$name'
                        AND      $tagList.Username = '$username'
                        AND      $tagList.Domain = '$domain'
                        ORDER BY $tagInfo.Name ASC
          ";
          $this->Query ($sql_query);
          if ($this->CountResult() > 0) $owner = '.owner';
        } // if
        
        $gTAGPOSTDATA['TAGENTRIES'] = $data['Name'];
      
        if ($gACTION == 'TAG_SELECT_ALL') $gTAGCHECKED = TRUE;
        $return .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/tags/$view/middle$owner$restricted.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } // while
      
      $owner = NULL;
      $return .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/tags/$view/bottom$owner$restricted.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      return ($return);
   } // CreateDisplay
   
   function Handle ($pREFERENCEID, $pCONTEXT, $pLINK) {
     global $gTAGENTRIES, $gTAGFORMLINK;
     
     global $gACTION;
     
     global $gTAGSELECTBUTTON, $gTAGMASSLIST;
     
     global $zLOCALUSER, $zFOCUSUSER, $zAUTHUSER;
     global $zSTRINGS;
     
     $gTAGFORMLINK = $pLINK;
     
     $gTAGSELECTBUTTON = 'tag_select_all';
     if (count($gTAGMASSLIST) > 0) $gTAGSELECTBUTTON = "tag_select_none";
     
     if ($zAUTHUSER->Anonymous) return (FALSE);
     
     // Handle the action
     switch ($gACTION) {
       case 'TAG_SELECT_ALL':
         $gTAGSELECTBUTTON = 'tag_select_none';
         return (TRUE);
       break;
       case 'TAG_SELECT_NONE':
         $gTAGMASSLIST = array ();
         $gTAGSELECTBUTTON = 'tag_select_all';
         return (TRUE);
       break;
       case 'TAG_EMPHASIZE_ALL':
         // NOTE!! Check if null mass list and error out.
         foreach ($gTAGMASSLIST as $count => $tagentry) {
           $this->tagInformation->Select ("Name", $tagentry);
           $this->tagInformation->FetchArray();
         
           $this->tagInformation_tID = $this->tagInformation->tID;
           $this->Context = $pCONTEXT;
           $this->userAuth_uID = $zFOCUSUSER->uID; 
           $this->rID = $pREFERENCEID; 
           $this->Username = $zAUTHUSER->Username;
           $this->Domain = $zAUTHUSER->Domain;
           $this->Stamp = SQL_NOW;
           $this->Add ();
         } // if
         
         $zSTRINGS->Lookup ('MESSAGE.EMPHASIZE_ALL', $this->Context);
         $this->Message = $zSTRINGS->Output;
         return (TRUE);
       break;
       case 'TAG_DEEMPHASIZE_ALL':
         // NOTE!! Check if null mass list and error out.
         foreach ($gTAGMASSLIST as $count => $tagentry) {
           $this->tagInformation->Select ("Name", $tagentry);
           $this->tagInformation->FetchArray();
         
           $this->Select ("tagInformation_tID", $this->tagInformation->tID);
           // Make sure we don't delete the last entry.
           if ($this->CountResult() > 1) {
             $this->FetchArray();
             $this->Delete();
           } // if
         } // if
         
         $zSTRINGS->Lookup ('MESSAGE.EMPHASIZE_ALL', $this->Context);
         $this->Message = $zSTRINGS->Output;
         return (TRUE);
       break;
       case 'TAG_DELETE':
         if (!$gTAGENTRIES) return (FALSE);
       
         $this->tagInformation->Select ("Name", $gTAGENTRIES);
         $this->tagInformation->FetchArray();
         $criteria = array ("rID"                   => $pREFERENCEID,
                            "Context"               => $pCONTEXT,
                            "tagInformation_tID"    => $this->tagInformation->tID,
                            "Username"              => $zAUTHUSER->Username,
                            "Domain"                => $zAUTHUSER->Domain);
         $this->SelectByMultiple ($criteria);
         $this->FetchArray();
         $this->Delete();
         $gTAGENTRIES = NULL;
         $zSTRINGS->Lookup ('MESSAGE.DELETE', $this->Context);
         $this->Message = $zSTRINGS->Output;
         return (TRUE);
       break;
       case 'TAG_DELETE_ALL':
         // NOTE!! Check if null mass list and error out.
         foreach ($gTAGMASSLIST as $count => $tagentry) {
           $zSTRINGS->Lookup ('MESSAGE.DELETE_ALL', $this->Context);
           $this->tagInformation->Select ("Name", $tagentry);
           $this->tagInformation->FetchArray();
           $criteria = array ("rID"                   => $pREFERENCEID,
                              "Context"               => $pCONTEXT,
                              "tagInformation_tID"    => $this->tagInformation->tID,
                              "userAuth_uID"          => $zLOCALUSER->uID);
           $this->DeleteByMultiple ($criteria);
           $gTAGENTRIES = NULL;
           $zSTRINGS->Lookup ('MESSAGE.DELETE_ALL', $this->Context);
           $this->Message = $zSTRINGS->Output;
         } // foreach
         
         // NOTE!! Check if null mass list and error out.
         return (TRUE);
       break;
       case 'TAG_EMPHASIZE':
         if (!$gTAGENTRIES) return (FALSE);
         $this->tagInformation->Select ("Name", $gTAGENTRIES);
         $this->tagInformation->FetchArray();
         
         $this->tagInformation_tID = $this->tagInformation->tID;
         $this->Context = $pCONTEXT;
         $this->userAuth_uID = $zFOCUSUSER->uID; 
         $this->rID = $pREFERENCEID; 
         $this->Username = $zAUTHUSER->Username;
         $this->Domain = $zAUTHUSER->Domain;
         $this->Stamp = SQL_NOW;
         $this->Add ();
         $gTAGENTRIES = NULL;
         $zSTRINGS->Lookup ('MESSAGE.EMPHASIZE', $this->Context);
         $this->Message = $zSTRINGS->Output;
         return (TRUE);
       break;
       case 'TAG':
         // Check what the tag limit for this user is.
         if ( ($zLOCALUSER->uID == $zFOCUSUSER->uID) or
              ( ($zLOCALUSER->userAccess->a == TRUE) and 
                ($zLOCALUSER->userAccess->w == TRUE) ) ) {
           // User has FOCUS or ADMIN permissions.  1000 tag limit.
           $limit = 1000;
           $total = 0;
         } else {
          $tagList = $this->TableName;
           // Select how many 
          $query = "SELECT   COUNT($tagList.tID) AS taggedAmount
                    FROM     $tagList
                    WHERE    $tagList.rID = $pREFERENCEID
                    AND      $tagList.userAuth_uID = $zFOCUSUSER->uID
                    AND      $tagList.Username = '$zAUTHUSER->Username'
                    AND      $tagList.Domain = '$zAUTHUSER->Domain'
          ";
          $this->Query ($query);
          $this->FetchArray();
          $limit = USER_TAG_LIMIT;
          $total = $this->taggedAmount;
         } // if
         
         // Allowed deliminators
         $taglist = str_replace (';', ',', $gTAGENTRIES);
         $taglist = str_replace ('-', ',', $taglist);
         $taglist = str_replace (':', ',', $taglist);
         $taglist = str_replace ('+', ',', $taglist);
         
         // Split into an array.
         $taglist = explode (',', $taglist);
         
         // Check if the user has gone over their limit.
         $current = count ($taglist);
         $current += $total; 
         if ($current > $limit) {
           $this->Error = -1;
           $zSTRINGS->Lookup ('ERROR.TOOMANY', $this->Context);
           $this->Message = $zSTRINGS->Output;
           return (FALSE);
         } // if
         
         // Loop through the tags.
         foreach ($taglist as $count => $value) {
           // Strip white spaces off ends.
           $taglist[$count] = ltrim (rtrim ($value));
           
           // Replace all spaces with underscores.
           $taglist[$count] = str_replace (' ', '_', $taglist[$count]);
           
           // Check if the tag is too long
           if (strlen ($taglist[$count]) > USER_TAG_MAXLENGTH) {
             $this->Error = -1;
             $zSTRINGS->Lookup ('ERROR.TOOLONG', $this->Context);
             $this->Message = $zSTRINGS->Output;
             return (FALSE);
           } // if
         } // if 
           
         foreach ($taglist as $count => $value) {
           // Select for existing tags with this name.
           $this->tagInformation->Select ('Name', $taglist[$count]);
           
           if ($this->tagInformation->CountResult() == 0) {
             // Tag doesn't exist, create it.
             $this->tagInformation->Name = $taglist[$count];
             $this->tagInformation->Add();
             $tagInformation_tID = $this->tagInformation->AutoIncremented();
           } else {
             // Tag exists, use it.
             $this->tagInformation->FetchArray();
             $tagInformation_tID = $this->tagInformation->tID;
           } // if
           
           // Add the list record.
           $this->tagInformation_tID = $tagInformation_tID;
           $this->rID = $pREFERENCEID;
           $this->userAuth_uID = $zFOCUSUSER->uID;
           $this->Context = $pCONTEXT;
           $this->Username = $zAUTHUSER->Username;
           $this->Domain = $zAUTHUSER->Domain;
           $this->Stamp = SQL_NOW;
           $this->Add();
         } // if
         
         unset ($gTAGENTRIES);
         
         return (TRUE);
       break;
     } // switch
     
     return (TRUE);
   } // Handle
   
   function Display ($pLINK, $pREFERENCEID, $pOWNERID = NULL) {
     echo $this->CreateDisplay ($pLINK, $pREFERENCEID, $pOWNERID);
   } // Display
   
   // Detect whether the URL has a tag/ appended to it.
   function DetectTags () {
    
      $url_split = explode ('tag\/', $_SERVER['REQUEST_URI']);
      $tag = $url_split[1];
      $tag = str_replace ('/', '', $tag);
      $tag = strtolower ($tag);
      
      if ($tag) return ($tag);
     
      return (FALSE);
   } // DetectTags

  } // cTAGLIST

