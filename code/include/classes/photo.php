<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: photo.php                               CREATED: 07-25-2005 + 
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
  class cPHOTOSETS extends cDATACLASS {
 
    // Keys
    var $tID, $userAuth_uID; 
    
    // Variables
    var $Name, $Directory, $Description, $Tags;
    var $Cascade;

    // Classes
    var $photoInfo;

    function cPHOTOSETS ($pDEFAULTCONTEXT = '') {
      $this->TableName = 'photoSets';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Name = '';
      $this->Directory = '';
      $this->Description = '';
      $this->Tags = '';
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

        'Tags'           => array ('max'        => '128',
                                   'min'        => '',
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
 
  } // cPHOTOSETS

  // Photo information class.
  class cPHOTOINFORMATION extends cDATACLASS {
 
    // Keys
    var $tID, $userAuth_uID, $photoSets_tID;

    // Variables
    var $Filename, $Width, $Height, $ThumbWidth, $ThumbHeight, $Description, $Tags;

    // Classes
    var $Comments;

    function cPHOTOINFORMATION ($pDEFAULTCONTEXT = '') {
      $this->TableName = 'photoInformation';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->photoSets_tID = '';
      $this->Filename = '';
      $this->Width = '';
      $this->Height = '';
      $this->ThumbWidth = '';
      $this->ThumbHeight = '';
      $this->Description = '';
      $this->Tags = '';
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

        'Tags'           => array ('max'        => '128',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

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
      $this->TableName = 'photoPrivacy';
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
