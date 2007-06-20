<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: user.php                                CREATED: 05-05-2006 + 
  // | LOCATION: /code/include/classes/BASE/        MODIFIED: 05-05-2006 +
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
  // | Part of the Appleseed BASE API                                    |
  // | VERSION:      0.7.0                                               |
  // | DESCRIPTION:  User class definitions. Reusable functions not      |
  // |               specifically tied to Appleseed.                     |
  // +-------------------------------------------------------------------+
 
  // User Authorization class.
  class cUSERAUTHORIZATION extends cBASEDATACLASS {

    var $uID, $Username, $Pass, $Invite, $Verification, $Standing;
    var $userSession, $userProfile, $userAccess, $userAnswers;
    var $userInformation;
    var $Cascade;

    function cUSERAUTHORIZATION ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'userAuthorization';
      $this->uID = '';
      $this->Username = '';
      $this->Pass = '';
      $this->Invite = '';
      $this->Verification = '';
      $this->Standing = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->FieldNames = '';
      $this->PrimaryKey = 'uID';
      $this->ForeignKey = '';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'uID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Username'       => array ('max'        => '32',
                                   'min'        => '4',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + = %20',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Pass'           => array ('max'        => '64',
                                   'min'        => '6',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'password',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Standing'       => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => '',
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

        'Verification'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => '',
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

      );

      // Internal class references.
      $this->userSession      = new cUSERSESSIONS;
      $this->userAccess       = new cUSERACCESS;

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
      
      unset ($this->Cascade);
 
    } // Constructor

    // Authenticate user based on Username and Password
    function Authenticate () {

      $un = strtolower ($this->Username);
      $pw = $this->Pass;

      $clause = "Username = '$un' AND Pass=PASSWORD('$pw')";

      $this->SelectWhere ($clause);
      $this->FetchArray();

      if ( $this->CountResult () == 0) {
        $ERRORSTR = new cSYSTEMSTRINGS;
        $ERRORSTR->Lookup ('ERROR.FAILED', 'SITE.LOGIN');
        $this->Message = $ERRORSTR->Output;
        $this->Error = -1;
        unset ($ERRORSTR);
      } // if

      return (0);

    } // Authenticate

    // Initialize a new user.
    function Initialize () {

      return (TRUE);
    } // Initialize

    // Determine access level for user.
    function Access ($pREADBIT = FALSE, $pWRITEBIT = FALSE, $pADMINBIT = FALSE, $pLOCATION = "") {

      // Use current location if not specified.
      if (!$pLOCATION) $pLOCATION = $_SERVER['REQUEST_URI'];

      // Load security settings from userAccess.
      $securitydefs = array ("userAuth_uID" => $this->uID,
                             "Location"     => $pLOCATION);

      $this->userAccess->SelectByMultiple ($securitydefs);
      $this->userAccess->FetchArray();

      // If no entries were found, go backwards for inheritance.
      if ( ($this->userAccess->CountResult () == 0) and ($pLOCATION != '/') ) {

        // Remove top directory off of Location.
        $currentLocation = strrchr (rtrim($pLOCATION, "/"), "/");
        $currentLocationpos = strpos ($pLOCATION, $currentLocation);
        $parentLocation = substr($pLOCATION, 0, $currentLocationpos + 1);

        // Use recursive call of this function.
        $classname = get_class ($this);
        $parentAccess = new $classname;
        $parentAccess->Select ('uID', $this->uID);
        $parentAccess->FetchArray ();
        $parentAccess->Access ($this->userAccess->r, 
                               $this->userAccess->w,
                               $this->userAccess->a,
                               $parentLocation);

        if ($parentAccess->userAccess->Inheritance) {
          // Inherit parent values.
          $this->userAccess->Location = $pLOCATION;
          $this->userAccess->Inheritance = $parentAccess->userAccess->Inheritance;
          $this->userAccess->r = $parentAccess->userAccess->r;
          $this->userAccess->w = $parentAccess->userAccess->w;
          $this->userAccess->a = $parentAccess->userAccess->a;
        } else {
          // Use default values;
          $this->userAccess->Location = $pLOCATION; 
          $this->userAccess->r = $pREADBIT; 
          $this->userAccess->w = $pWRITEBIT; 
          $this->userAccess->a = $pADMINBIT; 
        } // if

        unset ($parentAccess);
      } // if 

      return (0);

    } // Access

  } // cUSERAUTHORIZATION

  // User sessions class.
  class cUSERSESSIONS extends cBASEDATACLASS {

    var $userAuth_uID, $Identifier, $Stamp, $Address, $Host;
 
    function cUSERSESSIONS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'userSessions';
      $this->userAuth_uID = '';
      $this->Identifier = '';
      $this->Stamp = '';
      $this->Address = '';
      $this->Host = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'userAuth_uID';
      $this->ForeignKey = 'userAuth_uID';
 
      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Identifier'     => array ('max'        => '',
                                   'min'        => '',
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
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),

        'Address'        => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => '',
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

        'Host'           => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => '',
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

      );

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor

    // Create the session.
    function Create ($pREMEMBER, $pSESSIONNAME = "gLOGINSESSION") {

      // This bible was placed here by a Gideon.
      // R.I.P. William Melvin Hicks, 1961 - 1994. 
      global $zAPPLE;

      global $gSITEDOMAIN;

      // Delete all records older than 1 month.
      $deletestatement = "DELETE FROM " . $this->TableName . " WHERE Stamp <  DATE_ADD(now(),INTERVAL -1 MONTH)";
      $this->Query ($deletestatement);
                  
      // Delete all sessions with this user id.
      $this->Delete();
  
      // Create a random session string.
      $session_string = $zAPPLE->RandomString (32);
  
      // Set the cookie with that string.
      if ($pREMEMBER) {
       // Set for 30 days.
       setcookie ($pSESSIONNAME, $session_string, 
                  time()+60*60*24*30, '/') 
         or $session_string = "";
      } else {
       // Set for when browser closes.
       setcookie ($pSESSIONNAME, $session_string, 0, '/')
         or $session_string = "";
      } // if
  
      // Get the current time stamp.
      $current_time = gettimeofday();
  
      // Add session into the database.
      $this->Identifier = $session_string;
      $this->Stamp = SQL_NOW;
      $this->Address = $_SERVER['REMOTE_ADDR'];
      $this->Host = $_SERVER['REMOTE_HOST'];
      if (!$this->Host) $this->Host = gethostbyaddr ($this->Address);
  
      $this->Add();

      // Return session string.
      return ($session_string);
    } // Create 
 
    function Destroy ($pSESSIONNAME = "gLOGINSESSION") {

      global $gSITEDOMAIN;

      $this->Select ("Identifier", $this->Identifier);
      $this->FetchArray();

      // Delete all sessions with global identifier.
      $this->Delete ();
 
      // Set the cookie with that string.
      setcookie ($pSESSIONNAME, $this->Identifier, time() - 3600, '/');
  
      return (0);
 
    } // Destroy

  } // cUSERSESSIONS
  
  // User access class.
  class cUSERACCESS extends cBASEDATACLASS {

    var $tID, $userAuth_uID, $Location, $r, $w, $a, $e, $Inheritance;
 
    function cUSERACCESS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'userAccess';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Location = '';
      $this->r = '';
      $this->w = '';
      $this->e = '';
      $this->a = '';
      $this->Inheritance = '';
      $this->r = '';
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
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Location'       => array ('max'        => '4000',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'r'              => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'w'              => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'e'              => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'a'              => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Inheritance'    => array ('max'        => '',
                                   'min'        => '',
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

  } // cUSERACCESS

  // User settings class.
  class cUSERSETTINGS extends cBASEDATACLASS {

    var $tID, $userAuth_uID, $Identifier, $Value, $SettingsCache;
 
    function cUSERSETTINGS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'userSettings';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Identifier = '';
      $this->Value = '';
      $this->SettingsCache = array ('DefaultTheme' => NULL);
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
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Identifier'     => array ('max'        => '32',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Value'          => array ('max'        => '',
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

    // Load Settings into Cache.
    function Load () {
      
      // Select current users settings.
      $this->Select ("userAuth_uID", $this->userAuth_uID);

      // Loop through results.
      while ($this->FetchArray ()) {
        $this->SettingsCache[$this->Identifier] = $this->Value;
      } // while

      return (TRUE);
    } // Load

    // Get the value from the database. 
    function Get ($pIDENTIFIER, $pCACHED = TRUE) {

      if ($pCACHED) {

        // Load from the cache.
        return ($this->SettingsCache[$pIDENTIFIER]);

      } else {
       
        // Load directly from the database.
        $criteria = array ("userAuth_uID" => $this->userAuth_uID,
                           "Identifier"   => $pIDENTIFIER);
        $this->SelectByMultiple ($criteria);
        $this->FetchArray ();
        return ($this->Value);

      } // if

      return (FALSE);
    } // Get

    // Set a settings value.
    function Set ($pIDENTIFIER, $pVALUE) {

      // Set the value in the cached list only.
      $this->SettingsCache[$pIDENTIFIER] = $pVALUE;

      return (TRUE);
    } // Set

    // Save a settings value to the database.
    function Save ($pIDENTIFIER, $pVALUE) {

      // Check if user ID is set.
      if (!$this->userAuth_uID) return (FALSE);

      $criteria = array ("userAuth_uID" => $this->userAuth_uID,
                         "Identifier"   => $pIDENTIFIER);
      $this->SelectByMultiple ($criteria);
      $this->FetchArray ();

      // Assign new value.
      $this->Value = $pVALUE;

      if ($this->CountResult () == 0) {
        $this->Identifier = $pIDENTIFIER;
        $this->Add ();
      } else {
        $this->Update ();
      } // if

      $this->SettingsCache[$this->Identifier] = $this->Value;

      if ($this->Error) return (FALSE);

      return (TRUE);

    } // Save

  } // cUSERSETTINGS

?>
