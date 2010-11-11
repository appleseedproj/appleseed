<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: auth.php                                CREATED: 08-20-2006 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 08-20-2006 +
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
  // | VERSION:      0.7.8                                               |
  // | DESCRIPTION.  Session authorization class.                        |
  // +-------------------------------------------------------------------+

  class cAUTHUSER {
    // Variables
    var $Username, $Domain, $Remote, $Anonymous;

    function cAUTHUSER ($pDEFAULTCONTEXT = '') {
      $this->Username = '';
      $this->Domain = '';
      $this->Remote = FALSE;
      $this->Anonymous = FALSE;

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

    } // Constructor

    function BuildIconList () {
      global $zOLDAPPLE;

      global $gICONLIST;
      
      // Select which server to use.
      $useServer = $zOLDAPPLE->ChooseServerVersion ($this->Domain);
      if (!$useServer) return (FALSE);
      
      require_once ('legacy/code/include/classes/asd/' . $useServer);
      
      $CLIENT = new cCLIENT();
      $remotedata = $CLIENT->BuildIconList($this->Username, $this->Domain);
      unset ($CLIENT);
      
      // Convert data into usable form.
      foreach ($remotedata as $icon) {
        $gICONLIST[$icon->Filename] = $icon->Keyword;
      } // foreach
      
      return (TRUE);
    } // BuildIconList

    function BuildIconMenu ($pUID) {

      global $gICONLIST;

      if ($this->BuildIconList ()) {

        // Buffer the icon listing menu.
        ob_start ();

        global $gUSERICON;
        global $bUSERICONLISTING;
        global $zHTML;

        // NOTE: Move to object.
        echo "<p id='icon'>";
        echo __("User Icon");
        echo "</p>";
        $zHTML->Menu ("USERICON", $gICONLIST, "", $gUSERICON);
        $this->Broadcast ("field", "Usericons");

        $bUSERICONLISTING = ob_get_clean ();
      } else {
        return (FALSE);
      } // if

      return (TRUE);

    } // BuildIconMenu

    // Wrapper for base class Broadcast function.
    function Broadcast ($pCLASS = "", $pFIELDERROR = "") {

      $zBASE = new cDATACLASS;
      $zBASE->Error = $this->Error;
      $zBASE->Errorlist = $this->Errorlist;
      $zBASE->Message = $this->Message;
      $zBASE->Broadcast ($pCLASS, $pFIELDERROR);
      unset ($zBASE);

      return (0);
    } // Broadcast

  } // cAUTHUSER

  // Auth session class.
  class cAUTHSESSIONS extends cUSERSESSIONS {
 
    // Keys
    var $tID; 
    
    // Variables
    var $Username, $Domain, $Identifier, $Stamp, $Address, $Host;
    var $Cascade;

    // Classes
    var $photoInfo;

    function cAUTHSESSIONS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'authSessions';
      $this->tID = '';
      $this->Username = '';
      $this->Domain = '';
      $this->Identifier = '';
      $this->Stamp = '';
      $this->Address = '';
      $this->Host = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = '';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Username'       => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Fullname'       => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Domain'         => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

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

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
 
    // Create the session.
    function Create ($pREMEMBER, $pSESSIONNAME = "gLOGINSESSION") {

      // Delete all records older than 24 hours.
      $deletestatement = "DELETE FROM " . $this->TableName . " WHERE Stamp <  DATE_ADD(now(),INTERVAL -1 DAY)";
      $this->Query ($deletestatement);

      // Select any existing auth session information.
      $existingcriteria = array ("Username"  => $this->Username,
                                 "Domain"    => $this->Domain);
      $this->SelectByMultiple ($existingcriteria);
      $this->FetchArray ();
      parent::Create ($pREMEMBER, $pSESSIONNAME);
    } // Create

  } // cAUTHSESSIONS

  // Auth verification class.
  class cAUTHVERIFICATION extends cDATACLASS {
 
    // Keys
    var $tID; 
    
    // Variables
    var $Username, $Domain, $Identifier, $Stamp, $Address, $Host;
    var $Cascade;

    // Classes
    var $photoInfo;

    function cAUTHVERIFICATION ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'authVerification';
      $this->tID = '';
      $this->Username = '';
      $this->Domain = '';
      $this->Identifier = '';
      $this->Stamp = '';
      $this->Address = '';
      $this->Host = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = '';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Username'       => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Domain'         => array ('max'        => '128',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Verified'       => array ('max'        => '1',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

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

        'Token'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => '',
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

        'Active'         => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Stamp'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),
      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor

    // Look up the authentication token.
    function LoadToken ($pUSERNAME, $pREMOTEDOMAIN) {
      $tokencriteria = array ("Username" => $pUSERNAME,
                              "Domain"   => $pREMOTEDOMAIN);
      $this->SelectByMultiple ($tokencriteria, "Stamp");
      $this->FetchArray ();

      $token = $this->Token;

      if ($token) return ($token);

      return (FALSE);
    } // LoadToken
 
  } // cAUTHVERIFICATION
  
  class cAUTHTOKENS extends cBASEDATACLASS {
    var $tID, $Username, $Domain, $Token, $Stamp, $Source;
 
    function cAUTHTOKENS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'authTokens';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Domain = '';
      $this->Token = '';
      $this->Stamp = '';
      $this->Source = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = '';
 
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

        'Username'       => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Domain'         => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Token'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => '',
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

        'Stamp'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),
                                   
        'Source'         => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),
      );

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
    
    function LoadToken ($pUSERNAME, $pDOMAIN) {
      // Load tokens created in the last 30 min from the database.
      $sql_query = "SELECT * FROM $this->TableName " .
                   "WHERE Username = '$pUSERNAME' " .
                   "AND Domain = '$pDOMAIN' " .
                   "AND Stamp > DATE_SUB(now(), INTERVAL 30 MINUTE) " .
                   "AND Source = " . TOKEN_LOCAL;
      $this->Statement = $sql_query;
      $this->Query ($sql_query);
      
      $this->FetchArray ();
      
      return ($this->Token);
    } // LoadToken
    
    function CreateToken ($pUSERNAME, $pDOMAIN) {
      
      global $zOLDAPPLE;
      
      // Load and delete current token.
      $sql_query = "DELETE FROM $this->TableName " .
                   "WHERE userAuth_uID = '$this->userAuth_uID' " .
                   "AND Domain = '$pDOMAIN' " .
                   "AND Source = " . TOKEN_LOCAL;
      $this->Statement = $sql_query;
      $this->Query ($sql_query);
      
      // Create new token information.
      $this->Token = $zOLDAPPLE->RandomString (32);
      $this->Username = $pUSERNAME;
      $this->Domain = $pDOMAIN;
      $this->Source = TOKEN_LOCAL;
      $this->Stamp = SQL_NOW;
      
      // Add new token.
      $this->Add ();
      
      return ($this->Token);
    } // CreateToken
    
  } // cAUTHTOKENS

?>
