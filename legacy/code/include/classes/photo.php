<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: photo.php                               CREATED: 07-25-2005 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 07-25-2005 +
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
  // | DESCRIPTION.  Photo class definitions.                            |
  // +-------------------------------------------------------------------+

  // Photo sets class.
  class cPHOTOSETS extends cDATACLASS {
 
    // Keys
    var $tID, $userAuth_uID; 
    
    // Variables
    var $Name, $Directory, $Description;
    var $Cascade;

    // Classes
    var $photoInfo;

    function cPHOTOSETS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'photoSets';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Name = '';
      $this->Directory = '';
      $this->Description = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'userAuth_uID';
      // $this->Cascade = array ('photoInfo', 'photoThumbs'); 
 
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

        'Name'           => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Directory'      => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + = %20',
                                   'required'   => '',
                                   'relation'   => 'specific',
                                   'null'       => NO,
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
      );

      // Internal class references.
      $this->photoInfo        = new cPHOTOINFORMATION ($pDEFAULTCONTEXT);
      $this->photoPrivacy     = new cPHOTOPRIVACY ($pDEFAULTCONTEXT);

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
 
    function GetExifData () {
      
      global $zFOCUSUSER, $zOLDAPPLE;
      
      global $gFRAMELOCATION;
      
      $exiflocation = "photos/" . $zFOCUSUSER->Username . "/sets/" . $this->Directory . "/_og." . $this->photoInfo->Filename;
          
      if (!function_exists('exif_read_data')) return (FALSE);
      
      $exif = exif_read_data($exiflocation, 'ANY_TAG');
      
      if (!$exif) {
        global $gNOEXIF;
        $gNOEXIF = __("No EXIF Data");
        $return = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photos/exif/none.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        return ($return);
      } // if
   
      $return = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photos/exif/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      $final['MAKE'] = $exif['Make'];
      $final['MODEL'] = $exif['Model'];
      $final['DIMENSIONS'] = $exif['ExifImageWidth'] . "x" . $exif['ExifImageLength'];
      $final['OWNER'] = $exif['OwnerName'];
      $final['COMMENT'] = $exif['COMPUTED']['UserComment'];
      $final['COPYRIGHT'] = $exif['COMPUTED']['Copyright'];
      $final['PHOTOGRAPHER'] = $exif['COMPUTED']['Copyright.Photographer'];
      $timestamp = strtotime($exif['DateTimeOriginal']);
      $final['STAMP'] = date("F j, Y, g:i a", $timestamp);
      
      foreach ($final as $key => $value) {
        global $gEXIFLABEL, $gEXIFDATA;
        $gEXIFLABEL = __("EXIF " . ucwords ( $key ) );
        $gEXIFDATA = $value;
        if (!$value) continue;
        $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photos/exif/middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } // foreach
      
      $return .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photos/exif/bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      return ($return);
    } // GetExifData
    
    function BufferLatestPhotos () {
      global $zOLDAPPLE, $zAUTHUSER;
      
      global $gSITEDOMAIN, $gFRAMELOCATION, $gTABLEPREFIX;
      global $gIMG, $gIMGSRC, $gDIRECTORY, $gDIRECTORYSRC, $gOWNER, $gOWNERSRC, $gSTAMP, $gDESCRIPTION;
      
      $buffer = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/site/latest/photos/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      $USER = new cUSER();
      $userAuth = $gTABLEPREFIX . "userAuthorization";
      $userSessions = $gTABLEPREFIX . "userSessions";
      $authSessions = $gTABLEPREFIX . "authSessions";
      $photoSets = $gTABLEPREFIX . "photoSets";
      $photoInfo = $gTABLEPREFIX . "photoInformation";
      $photoPrivacy = $gTABLEPREFIX . "photoPrivacy";
      $friendCircles = $gTABLEPREFIX . "friendCircles";
      $friendCirclesList = $gTABLEPREFIX . "friendCirclesList";
      $friendInfo = $gTABLEPREFIX . "friendInformation";
      
      if ($zAUTHUSER->Anonymous) {
        $sql_statement = "
          SELECT   MIN($photoPrivacy.Access) AS FinalAccess,
                   $photoInfo.Filename
          FROM     $photoSets, $photoPrivacy, $userAuth, $photoInfo
          WHERE    $photoPrivacy.userAuth_uID = $userAuth.uID
          AND      $photoSets.userAuth_uID = $userAuth.uID
          AND      $photoPrivacy.friendCircles_sID = %s
          AND      $photoPrivacy.photoSets_tID = $photoSets.tID
          GROUP BY $photoInfo.Filename
        ";
        $sql_statement = sprintf ($sql_statement,
                                  mysql_real_escape_string (USER_EVERYONE));
      } else {
        
      } // if
                             
      $this->photoInfo->Select (NULL, NULL, 'Stamp LIMIT 48');
      
      while ($this->photoInfo->FetchArray ()) {
        $this->Select ('tID', $this->photoInfo->photoSets_tID);
        $this->FetchArray();
        $USER->Select ("uID", $this->userAuth_uID);
        $USER->FetchArray();
        $gIMG = '/profile/' . $USER->Username . '/photos/' . $this->Directory . '/' . $this->photoInfo->Filename;
        $gIMGSRC = '/photos/' . $USER->Username . '/sets/' . $this->Directory . '/' . '_sm.' . $this->photoInfo->Filename;
        $gOWNER = $USER->userProfile->GetAlias();
        $gOWNERSRC = '/profile/' . $USER->Username;
        $gDIRECTORY = $this->Name;
        $gDIRECTORYSRC = '/photos/' . $USER->Username . '/sets/' . $this->Directory . '/';
        $gSTAMP = $this->photoInfo->FormatDate ('Stamp');
        $gSTAMP = $this->photoInfo->fStamp;
        $gDESCRIPTION = $this->photoInfo->Description;
        $buffer .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/site/latest/photos/middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } // while
      
      $buffer .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/site/latest/photos/bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      return ($buffer);
    } // BufferLatestPhotos
 
  } // cPHOTOSETS

  // Photo information class.
  class cPHOTOINFORMATION extends cDATACLASS {
 
    // Keys
    var $tID, $userAuth_uID, $photoSets_tID;

    // Variables
    var $Filename, $Width, $Height, $ThumbWidth, $ThumbHeight, $Description;

    // Classes
    var $Comments;

    function cPHOTOINFORMATION ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'photoInformation';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->photoSets_tID = '';
      $this->Filename = '';
      $this->Width = '';
      $this->Height = '';
      $this->ThumbWidth = '';
      $this->ThumbHeight = '';
      $this->Description = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'userAuth_uID';
 
      // Internal class references.
      $this->Comments        = new cCOMMENTINFORMATION ($pDEFAULTCONTEXT);

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

        'sID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'photoSets_tID'  => array ('max'        => '',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Filename'       => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + = %20',
                                   'required'   => '',
                                   'relation'   => 'specific',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'FILENAME'),

        'Width'          => array ('max'        => '2048',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Height'         => array ('max'        => '2048',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'ThumbWidth'     => array ('max'        => '1024',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'ThumbHeight'    => array ('max'        => '1024',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Description'    => array ('max'        => '4096',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
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
 
    } // Constructor
    
  } // cPHOTOINFORMATION

  // Photo privacy class.
  class cPHOTOPRIVACY extends cPRIVACYCLASS {
 
    // Keys
    var $tID, $userAuth_uID, $photoSets_tID, $friendCircles_sID, $Access;

    // Variables
    var $Filename, $Width, $Height;

    function cPHOTOPRIVACY ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'photoPrivacy';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->photoSets_tID = '';
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

        'photoSets_tID'  => array ('max'        => '',
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

       'Access'          => array ('max'        => '1024',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Height'         => array ('max'        => '1024',
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
 
  } // cPHOTOPRIVACY

?>
