<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: tags.php                                CREATED: 06-07-2007 + 
  // | LOCATION: /code/include/classes/BASE/        MODIFIED: 06-07-2007 +
  // +-------------------------------------------------------------------+
  // | Copyright (c) 2004-2007 Appleseed Project                         |
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
  // | VERSION:      0.7.0                                               |
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
      
      global $gFRAMELOCATION;
      
      $context = $zAPPLE->Context;
     
      $criteria = array ("rID"     => $pREFERENCEID,
                         "Context" => $context);
                         
      if ($pOWNERID) $criteria['userAuth_uID'] = $pOWNERID;
      
      $tagList = $this->TableName;
      $tagInfo = $this->tagInformation->TableName;
      
      // Anonymous user
      $query = "SELECT   $tagInfo.Name, count($tagInfo.Name) as Amount
                FROM     $tagList, $tagInfo
                WHERE    $tagList.rID = $pREFERENCEID
                AND      $tagList.userAuth_uID = $pOWNERID
                AND      $tagList.tagInformation_tID = $tagInfo.tID
                GROUP BY $tagInfo.tID
                ORDER BY $tagInfo.Name ASC
      ";
      
      $this->Query ($query);
      
      $count = 0; $max = 0;
      $results = array ();
      
      while ($this->FetchArray ()) {
        $results[$count]['Name'] = $this->Name;
        $results[$count]['Amount'] = $this->Amount;
        if ($this->Amount > $max) $max = $this->Amount;
        $count++;
      } // while
      $return = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/tags/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      foreach ($results as $count => $data) {
        global $gTAGNAME, $gTAGCLASS, $gTAGLINK;
        $size = floor (($data['Amount'] / $max) * 10);
        $gTAGNAME = strtolower ($data['Name']);
        $gTAGCLASS = 'tagsize' . $size;
        $gTAGLINK = $pLINK . $gTAGNAME . '/';
        $gTAGNAME = str_replace ('_', ' ', $gTAGNAME);
        
        $return .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/tags/middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } // while
      
      $return .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/tags/bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      return ($return);
   } // Display
   
   function Display ($pLINK, $pREFERENCEID, $pOWNERID = NULL) {
     echo $this->CreateDisplay ($pLINK, $pREFERENCEID, $pOWNERID);
   } // Display
   
   // Detect whether the URL has a tag/ appended to it.
   function DetectTags () {
    
      $url_split = split ('tag\/', $_SERVER['REQUEST_URI']);
      $tag = $url_split[1];
      $tag = str_replace ('/', '', $tag);
      $tag = strtolower ($tag);
      
      if ($tag) return ($tag);
     
      return (FALSE);
   } // DetectTags

  } // cTAGLIST
