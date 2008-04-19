<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: application.php                         CREATED: 05-04-2006 + 
  // | LOCATION: /code/include/classes/BASE/        MODIFIED: 05-04-2006 +
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
  // | DESCRIPTION:  Application class definitions. Reusable functions   |
  // |               not specifically tied to Appleseed.                 |
  // +-------------------------------------------------------------------+

  class cAPPLICATION {

    var $Database;

    function cAPPLICATION ($pDATABASE = NULL) {

      // Override which database to use.
      $this->Database = $pDATABASE;

      // Create global caching class.
      global $zCACHE;
      $zCACHE = new cBASEDATACACHE();
      
    } // Constructor

    // Initialize application.
    function Initialize () {

      // Make sure we're not initializing twice.
      global $gINITIALIZED;
      if ($gINITIALIZED) {
        return (TRUE);
      } else {
        $gINITIALIZED = TRUE;
      } // if
  
      // Connect to the database.
      $this->DBConnect ();
  
      // Global Variables
      global $gSETTINGS, $gLOGINSESSION, $gSITETITLE, $gSITEURL;
      global $gFOCUSUSERID, $gFOCUSUSERNAME;
      global $gPROFILEREQUEST, $gPROFILEACTION, $gPROFILESUBACTION;
      global $gDEBUG;
      global $gACTION;
  
      // Initialize classes
      global $zSTRINGS, $zOPTIONS, $zLOGS, $zHTML, $zXML;
      global $zFOCUSUSER, $zLOCALUSER, $zIMAGE;
      global $zDEBUG;
  
      $zSTRINGS = new cSYSTEMSTRINGS;
      $zOPTIONS = new cSYSTEMOPTIONS;
      $zLOGS    = new cSYSTEMLOGS;
  
      $zIMAGE   = new cIMAGE;
      $zHTML    = new cHTML;
      $zXML     = new cXML;
  
      $zLOCALUSER = new cUSERAUTHORIZATION;
      
      $zDEBUG = new cDEBUG;
      
      $zDEBUG->BenchmarkStart ('SITE');
      
      // Capture all errors and warnings.
      set_error_handler (array ($zDEBUG, 'HandleError'));
  
      // Strip all slashes from POST data.
      foreach ($_POST as $key => $value) {
       
        // Put the global variable in local scope.
        global $$key;
        $$key = $_POST[$key];
      
        // Strip slashes off of post variable.
        if (is_array ($$key) ) {
          foreach ($$key as $k => $v) {
            // Must create a reference to $$key, instead of using directly.
            $array = &$$key;
            $array[$k] = stripslashes ($v);
          } // foreach
        } else {
           $$key = stripslashes ($value);
        } // if
      
      } //foreach
  
      // Strip all slashes from GET data.
      foreach ($_GET as $key => $value) {
       
        // Put the global variable in local scope.
        global $$key;
        $$key = $_GET[$key];
      
        // Strip slashes off of post variable.
        if (is_array ($$key) ) {
          foreach ($$key as $k => $v) {
            // Must create a reference to $$key, instead of using directly.
            $array = &$$key;
            $array[$k] = stripslashes ($v);
          } // foreach
        } else {
           $$key = stripslashes ($value);
        } // if
      
      } //foreach
  
      // Check for gLOGINSESSION cookie.
      $gLOGINSESSION = isset($_COOKIE["gLOGINSESSION"]) ? 
                              $_COOKIE["gLOGINSESSION"] : "";
  
      // Pull zLOCALUSER info from database.
      if ($gLOGINSESSION) {
  
        $zLOCALUSER->userSession->Select ("Identifier", $gLOGINSESSION);
        $zLOCALUSER->userSession->FetchArray ();
        $zLOCALUSER->uID = $zLOCALUSER->userSession->userAuth_uID;
  
        $zLOCALUSER->Select ("uID", $zLOCALUSER->uID);
        $zLOCALUSER->FetchArray ();
  
        // $zLOCALUSER->Access ();

        // Global variables
        $this->SetTag ('AUTHUSERID', $zLOCALUSER->uID);
        $this->SetTag ('AUTHUSERNAME', $zLOCALUSER->Username);
  
      } // if
  
      // Load site title and url into global variable.
      $zSTRINGS->Lookup ('BROWSER.TITLE', $zAPPLE->Context);
      $gSITETITLE = $zSTRINGS->Output;

      // Modify gACTION from BUTTONNAME
      $gACTION = strtoupper ($gACTION);
      $gACTION = str_replace (' ', '_', $gACTION);
      
    } // Initialize

    // Generate a random seed.
    function MakeSeed () {
      list($usec, $sec) = explode(' ', microtime());
      return (float) $sec + ((float) $usec * 100000);
    } // MakeSeed

    // Parse through special tags.
    function ParseTags ($pPARSEDATA) {

      return ($pPARSEDATA);
    } // ParseTags

    // Strip all special tags.
    function RemoveTags ($pPARSEDATA) {

      return ($pPARSEDATA);
    } // RemoveTags

    // Generate a random string of characters.
    function RandomString ($pSTRINGSIZE) {
  
      // Generate a random 32-byte string.
      mt_srand($this->MakeSeed ());
  
      $return_string = ""; $return_count = 0;
      for ($return_count = 0; $return_count < $pSTRINGSIZE; $return_count++) {
        $randval_num = mt_rand(48, 57);
        $randval_alpha = mt_rand(65, 90);
  
        // Randomly choose either the alpha or the numeric value.
        $eitheror = mt_rand (0, 2);
        if ($eitheror == 0) 
          $randval = $randval_num;
        else
          $randval = $randval_alpha;
        
        $charval = chr($randval);
        $return_string .= "$charval";
      }
  
      return ($return_string);
    } // RandomString

    // Set global variables with default values.
    function SetGlobals () {
 
      // Make sure we're not initializing twice.
      global $gGLOBALIZED;
      if ($gGLOBALIZED) {
        return (TRUE);
      } else {
        $gGLOBALIZED = TRUE;
      } // if
  
      global $gERRORMSG, $gERRORTITLE;
  
      global $gSITEDOMAIN;
  
      global $gUSERJOURNALTAB, $gUSERPHOTOSTAB;
      global $gUSERENEMIESTAB, $gUSERFRIENDSTAB;
      global $gUSERMESSAGESTAB, $gUSERINFOTAB;
      global $gUSEROPTIONSTAB;

      global $gCONTENTARTICLESVIEWTAB;
      global $gCONTENTARTICLESSUBMITTAB;
      global $gCONTENTARTICLESQUEUETAB;
  
      global $gFRAMELOCATION, $gTHEMELOCATION;
      global $gUSERTHEME, $gFRAMEWORK;
      global $gFRAMELOCATION, $gTHEMELOCATION;
  
      global $gSTRINGS;
  
      global $gSITETITLE, $gSITEURL;
      global $gREFRESHWAIT;
      global $gPAGETITLE, $gPAGESUBTITLE;
  
      global $gSCROLLSTART, $gSCROLLMAX, $gSCROLLSTEP;
      global $gMAXPAGES, $gCURRENTPAGE;
  
      global $gBROADCASTUNIQUE;
  
      global $gLOGINSESSION;
  
      global $gPOSTDATA, $gEXTRAPOSTDATA;
  
      global $gSETTINGS;
  
      global $gDBERROR;
  
      global $gALTERNATE;
      
      global $gSELECTBUTTON;
  
      global $gDBLINK;
  
      global $gADMINEMAIL;
  
      global $gACTION, $gSORT, $gMASSLIST;
  
      // Define constants.  Do not modify.
      define ("UP",  "UP");
      define ("DOWN", "DOWN");
      define ("ON", "ON");
      define ("OFF", "OFF");
  
      define ("YES", "YES");
      define ("NO", "NO");
  
      define ("OLDER", "OLDER");
      define ("NEWER", "NEWER");
  
      define ("DYNAMIC", "DYNAMIC");
      define ("STATIC", "STATIC");
  
      define ("DISABLED", "disabled");
      define ("ENABLED", "enabled");

      define ("INCLUDE_SECURITY_NONE",  "0");
      define ("INCLUDE_SECURITY_BASIC", "1");
      define ("INCLUDE_SECURITY_FULL",  "2");
  
      define ("FORMAT_NONE", "0");    // No Formatting
      define ("FORMAT_ASD", "1");     // ASD Tags Only
      define ("FORMAT_BASIC", "2");   // Basic HTML
      define ("FORMAT_EXT", "3");     // Extended HTML
      define ("FORMAT_SECURE", "4");  // Secure HTML
      define ("FORMAT_UN", "5");      // Unprocessed
      define ("FORMAT_VIEW", "6");    // Viewable
  
      define ("OUTPUT_SCREEN",  "0");
      define ("OUTPUT_BUFFER",  "1");
  
      define ("SCROLL_PAGES",   "SCROLL_PAGES");
      define ("SCROLL_NOFIRST", "SCROLL_NOFIRST");
      define ("SCROLL_SPECIAL", "SCROLL_SPECIAL");
  
      define ("SKIP_UNIQUE",   FALSE);
      define ("CHECK_UNIQUE",  TRUE);
  
      define ("SQL_SKIP", '*!');
      define ("SQL_NOW", '@!');
      define ("SQL_NOT", '^!');
  
      define ("OUTPUT_NBSP",  "&nbsp;");

      define ("DELETED_COMMENT",  "__deleted_comment__");

      define ("ANONYMOUS",  "__anonymous__");
      
      // Site specific settings.  Modify according to your site.  
      $gADMINEMAIL = 'admin@domain';
      $gSITEDOMAIN = "localhost";
  
      $gSETTINGS['UserTheme'] = '';
      $gSETTINGS['Framework'] = '';
      $gSETTINGS['Language'] = 'en';
      $gSETTINGS['CascadeStrings'] = ON;
      $gSETTINGS['UseInvites'] = ON;
      $gSETTINGS['InviteAmount'] = 2;
  
      $gUSERTHEME = $gSETTINGS['UserTheme'];
      $gFRAMEWORK = $gSETTINGS['Framework'];
  
      $gFRAMELOCATION = ".";
      $gTHEMELOCATION = ".";

      // Set the basic string values.
      $gSTRINGS['en'] = array (
          'ERROR.PAGE - ' => array (
                                      'Formatting' => 0,
                                      'Output' => "An error occurred on this page."),
          'ERROR.NOTNULL - ' => array (
                                      'Formatting' => 0,
                                      'Output' => "Do not leave this field blank."),
          'ERROR.INTEGER - ' => array (
                                      'Formatting' => 0,
                                      'Output' => "This field must be a number value."),
      );
  
      // Appleseed BASE specific.  Do not modify.

      $gERRORMSG = ''; $gERRORTITLE = 'ERROR';
  
      $gALTERNATE = 0;
  
      $gSELECTBUTTON = '';
  
      $gREFRESHWAIT = 0;
  
      if (!isset ($gSCROLLSTART)) $gSCROLLSTART  = Array ();
      if (!isset ($gSCROLLSTEP)) $gSCROLLSTEP  = Array ();
      if (!isset ($gSCROLLMAX)) $gSCROLLMAX  = Array ();
  
      $gBROADCASTUNIQUE = '';
  
      // Unset sensitive variables.
      unset ($gSCROLLLINK);
  
      unset ($gDBERROR);
      unset ($gPAGESUBTITLE);
      unset ($gPAGETITLE); unset ($gPAGESUBTITLE);
      unset ($gDBLINK);
  
      unset ($gPOSTDATA); unset ($gEXTRAPOSTDATA);
  
      // Set the error reporting to 'all';
      // error_reporting(E_ALL);
  
      // Set the error reporting to 'none';
      // error_reporting(E_NONE);
  
      // Set the error reporting to fatal errors;
      error_reporting(E_ERROR);
  
    
    } // SetGlobals
  
    // Load basic configuraton data from file.
    function LoadSiteData () {
      global $gFRAMELOCATION;
      global $gERRORTITLE, $gERRORMSG;

      $settings_file = 'data/site.adat';
      
      // Display error message if database settings file doesn't exist.
      if ( !file_exists($settings_file)){
        $gERRORTITLE = "SITE ERROR";
        $gERRORMSG = "Could Not Load Settings File.";
        $this->IncludeFile ("$gFRAMELOCATION/objects/site/error.site.aobj", 
                  INCLUDE_SECURITY_NONE);
        die;
      } // if
  
      // Load the database connection information.
      $settings = file ($settings_file); 
  
      // Initialize Variables.
      $setting_un = null;
      $setting_pw = null;
      $setting_db = null;
      $setting_pre = null;
      $setting_host = null;
      $setting_ver = null;
      
      // Loop through each line.
      foreach ($settings as $setting) {
  
        // Split the line into two parts, type and ethod.
        list ($settingtype, $settingmethod) = split (":", $setting,2);
  
        // Create a php variable using the resulting data.
        $settingidentifier = 'setting_' . $settingtype;
        $$settingidentifier = rtrim (ltrim ($settingmethod));
  
      } // foreach

      global $gCONNECT, $gTABLEPREFIX;
      $gCONNECT['username'] = $setting_un;
      $gCONNECT['password'] = $setting_pw;
      $gCONNECT['database'] = $setting_db;
      $gCONNECT['host'] = $setting_host;
      $gTABLEPREFIX = $setting_pre;
      
      global $gAPPLESEEDVERSION;
      $gAPPLESEEDVERSION = $setting_ver;
      
      global $gSITEURL;
      $gSITEURL = $setting_url;
  
      global $gSITEDOMAIN;
      $gSITEDOMAIN = str_replace('http://', '', $gSITEURL); 
      $gSITEDOMAIN = str_replace('www.', '', $gSITEDOMAIN); 
      $gSITEDOMAIN = rtrim ($gSITEDOMAIN, '/');
  
    } // LoadSiteData

    function DBConnect () {
      global $gDBLINK, $gFRAMELOCATION;
      global $gERRORTITLE, $gERRORMSG;

      global $gCONNECT;
  
      // Load data from site.adat
      $this->LoadSiteData ();

      // If we already have a database link, return from function.
      if ($gDBLINK) return (0);
  
      // Open a persistent connection to the mysql server.
      if ($gDBLINK = mysql_pconnect ($gCONNECT['host'], $gCONNECT['username'], $gCONNECT['password'])) {
      } else {
        $gERRORTITLE = "DATABASE ERROR";
        $gERRORMSG = "Could Not Connect To Database.";
        $this->IncludeFile ("$gFRAMELOCATION/objects/site/error.connect.aobj", INCLUDE_SECURITY_NONE);
        die;
      } // if
  
      // Check if we're manually overriding which database to use.
      if ($this->Database) $gCONNECT['database'] = $this->Database;

      // Select the database to use.
      if (mysql_select_db($gCONNECT['database'])) {
      } else {
        $gERRORTITLE = "DATABASE ERROR";
        $gERRORMSG = "Could Not Access The Database.";
        $this->IncludeFile ("$gFRAMELOCATION/objects/site/error.connect.aobj", INCLUDE_SECURITY_NONE);
        die;
      } // if
  
      return (1);
    } // DBConnect

    // Adjust for a recently deleted entry.
    function AdjustScroll ($pCONTEXT, $pDATACLASS) {

      global $gSCROLLSTART, $gSCROLLSTEP, $gCURRENTPAGE, $gSCROLLMAX, $gPOSTDATA;

      if ($gSCROLLSTART[$pCONTEXT] > 0) {
        if ($gSCROLLSTART[$pCONTEXT] < $gSCROLLMAX[$pCONTEXT]) {
          $pDATACLASS->Seek ($gSCROLLSTART[$pCONTEXT]);
        } else {
          $gSCROLLSTART[$pCONTEXT] = $gSCROLLSTART[$pCONTEXT] - $gSCROLLSTEP[$pCONTEXT];
          if ($gSCROLLSTART[$pCONTEXT] < 0) $gSCROLLSTART[$pCONTEXT] = 0;
          $gCURRENTPAGE = ceil ($gSCROLLSTART[$pCONTEXT] / $gSCROLLSTEP[$pCONTEXT]) + 1;
          $pDATACLASS->Seek ($gSCROLLSTART[$pCONTEXT]);
          $gPOSTDATA['SCROLLSTART'] = array ($pCONTEXT => $gSCROLLSTART[$pCONTEXT]);
        } // if
      } // if

    } // AdjustScroll

    // Include based on a specified security setting.
    function IncludeFile ($pFILENAME, $pSECURITY = INCLUDE_SECURITY_BASIC, $pOUTPUT = OUTPUT_SCREEN) {
  
      $return = 0;

      // Check if the file exists.
      if (!file_exists ($pFILENAME) ) {
        echo "<!-- NOT FOUND: $pFILENAME -->";

           global $gADMINEMAIL;
           global $zLOCALUSER, $zAPPLE;

           $body = "\n" .
                   "A file was not found.  Please double check to make sure " .
                   "that it is available.\n\n" .
                   "User - " . $zLOCALUSER->Username . "\n" .
                   "Context - " . $zAPPLE->Context . "\n" .
                   "File - " . $pFILENAME . "\n\n" .
                   "- APPLESEED AUTOMATED EMAIL";
           $headers = 'From: "Appleseed Error" <error@appleseedproject.org>' . "\r\n" .
                      'Reply-To: error@appleseedproject.org' . "\r\n" .
                      'X-Mailer: PHP/' . phpversion();

           //mail ($gADMINEMAIL, "File Not Found", $body, $headers);
        return (FALSE);
      } // if
  
      switch ($pSECURITY) {
        case INCLUDE_SECURITY_NONE:
          // Make sure all globals are within scope.
          foreach ($GLOBALS as $globalid => $globalvalue) {
            global $$globalid;
          } // foreach
  
          // No security, just basic include.
          if ($pOUTPUT == OUTPUT_SCREEN) {
            include ($pFILENAME);
          } elseif ($pOUTPUT == OUTPUT_BUFFER) {
  
            ob_start ();
            include ($pFILENAME);
            $result = ob_get_clean ();
  
            // Return the result.
            return ($result);
          } // if
        break;
  
        case INCLUDE_SECURITY_BASIC:
          // Open the file for reading.
          $filedata = file($pFILENAME);
    
          $return = "";
  
          // Loop through the file data, parse tags, and echo.
          foreach($filedata as $line) {
  
            $line = $this->ParseTags ($line);
  
            if ($pOUTPUT == OUTPUT_SCREEN) {
              echo $line;
              $return = 0;
            } elseif ($pOUTPUT == OUTPUT_BUFFER) {
              $return .= $line;
            } // if
  
          } // foreach
        break;
  
        case INCLUDE_SECURITY_FULL:
        default:
          // Open the file for reading.
          $filedata = file($pFILENAME);
    
          // Loop through the file data, parse tags, and echo.
          foreach($filedata as $line)
          {
            $line = $this->RemoveTags ($line);
            if ($pOUTPUT == OUTPUT_SCREEN) {
              echo $line;
              $return = 0;
            } elseif ($pOUTPUT == OUTPUT_BUFFER) {
              $return .= $line;
            } // if
          } // foreach
        break;
      } // switch
      
      return ($return);
  
    } // IncludeFile
  
    // Format a string according to the specified method.
    function Format ($pPARSEDATA, $pFORMATTING) {

      // Format accordingly
      switch ($pFORMATTING) {
        case FORMAT_NONE:
          // Remove all formatting.
          $pPARSEDATA = $this->RemoveTags ($pPARSEDATA);
          $pPARSEDATA = strip_tags ($pPARSEDATA);
        break;
  
        case FORMAT_ASD:
          // Only allow ASD tags, no HTML.
          $pPARSEDATA = $this->ParseTags ($pPARSEDATA);
          $pPARSEDATA = strip_tags ($pPARSEDATA);
        break;
  
        case FORMAT_BASIC:
          // Allow basic HTML and ASD tags.  Convert new lines to <br>.
          $pPARSEDATA = strip_tags ($pPARSEDATA, "<asd><a><br><p><b><i><u><em><tt><li><ol><ul><strong><img><blockquote>");
          $pPARSEDATA = str_replace("\n", "<br />", $pPARSEDATA);
          $pPARSEDATA = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $pPARSEDATA);
          $pPARSEDATA = $this->ParseTags ($pPARSEDATA);
        break;
  
        case FORMAT_EXT:
          // Allow all HTML and ASD tags
          $pPARSEDATA = $this->ParseTags ($pPARSEDATA);
          $pPARSEDATA = str_replace("\n", "<br />", $pPARSEDATA);
          $pPARSEDATA = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $pPARSEDATA);
        break;
  
        case FORMAT_SECURE:
          // Allow HTML, but remove all ASD tags.
          $pPARSEDATA = $this->RemoveTags ($pPARSEDATA);
          $pPARSEDATA = str_replace("\n", "<br />", $pPARSEDATA);
          $pPARSEDATA = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $pPARSEDATA);
        break;
  
        case FORMAT_UN:
          // Unprocessed.
        break;
  
        case FORMAT_VIEW:
          // Change tags into a viewable form.
          $pPARSEDATA = str_replace("<", "&lt;", $pPARSEDATA);
          $pPARSEDATA = str_replace("<", "&gt;", $pPARSEDATA);
        break;
      } // switch
  
      return ($pPARSEDATA);
  
    } // Format

    function StripTags ($pPARSEDATA, $pALLOWED) {
    } // StripTags
  
    // Calculate an age based on a birthday.
    function CalculateAge ($pBIRTHDAY) {
  
      // Adopted from php.net comments.
      // Original Author: squid at anime-sanctuary dot net
  
      $currentyear = date("Y");
      $currentmonth = date("m");
      $currentday = date("d");           
  
      $birthyear = substr ($pBIRTHDAY, 0, 4);
      $birthmonth = substr ($pBIRTHDAY, 5, 2);
      $birthday = substr ($pBIRTHDAY, 8, 2);           
                                                    
      if ($currentmonth > $birthmonth || 
         ($birthmonth == $currentmonth && $currentday >= $birthday) ) {
        $finalage = $currentyear - $birthyear;
      } else {
        $finalage = $currentyear - $birthyear - 1;
      } // if   
  
      return ($finalage);
  
    } // CalculateAge
  
    // Convert an input string to a FLOAT or an INTEGER.
    function ConvertType ($pVARIABLE) {
      // Adopted from php.net comments.
      // Original Author: unknown
  
      // Check if vaue is numeric.
      if( is_numeric( $pVARIABLE ) ) {
        // Check to see if different typed versions are equal.
        if( (float)$pVARIABLE != (int)$pVARIABLE ) {
          // Float
          return (float)$pVARIABLE;
        } else {
          // Int
          return (int)$pVARIABLE;
        } // if
      } // if
      
      // Boolean
      if( $pVARIABLE == "true" ) return TRUE;
      if( $pVARIABLE == "false" ) return FALSE;

      return ($pVARIABLE);
    } // ConvertType

    // Check for a valid email address 
    function CheckEmail ($pEMAIL) {
      // Adopted from ilovjackdaniels.com sample code.
      // Original Author: dave@ilovejackdaniels.com
  
      // Remove trailing white space.
      $pEMAIL = ltrim (rtrim ($pEMAIL));
  
      // Check that there's one @ symbol, and that the lengths are right
      if (!ereg("[^@]{1,64}@[^@]{1,255}", $pEMAIL)) {
        // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
        return false;
      } // if
  
      // Split it into sections to make life easier
      $email_array = explode("@", $pEMAIL);
      $local_array = explode(".", $email_array[0]);
  
      for ($i = 0; $i < sizeof($local_array); $i++) {
         if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
          return false;
        } // if
      } // if
  
      // Check if domain is IP. If not, it should be valid domain name
      if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { 
        $domain_array = explode(".", $email_array[1]);
  
        if (sizeof($domain_array) < 2) {
            // Not enough parts to domain
            return false; 
        } // if
  
        for ($i = 0; $i < sizeof($domain_array); $i++) {
          if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
            return false;
          } // if
        } // for
      } // if
  
      return TRUE;
    } // CheckEmail

    // Remove the extension from a filename.
    function RemoveExtension ($pFILENAME) { 
  
      $ext = strrchr($pFILENAME, '.'); 
  
      if($ext !== FALSE) { 
        $pFILENAME = substr($pFILENAME, 0, -strlen($ext)); 
      } // if
  
      return ($pFILENAME); 
    } // RemoveExtension

    // Removes a directory and everything within it
    function RemoveDirectory ($pTARGET, $pRECURSIVE = FALSE) {
      // Adopted from www.php.net/rmdir
      // Original Author: makarenkoa@ukrpost.net
    
      // If the directory doesn't exist, just return.
      if (!file_exists ($pTARGET) ) return (TRUE);

      $exceptions = array('.','..');
  
      $sourcedir = @opendir ($pTARGET);
  
      // Check if we can open the target directory.
      if (!$sourcedir) return FALSE;
    
      // Loop through file list.
      while (FALSE !== ($sibling = readdir ($sourcedir) ) ) {
    
        if (!in_array ($sibling, $exceptions) ) {
    
          $object = str_replace ('//', '/', $pTARGET . '/' . $sibling);
  
          // Recursively remove directory.
          if (is_dir($object) and ($pRECURSIVE) ) $this->RemoveDirectory ($object);
    
          // Remove file.
          if (is_file ($object) ) {
            $result = @unlink($object);
          } // if
        } // if
      } // while
    
      // Close target directory.
      closedir($sourcedir);
    
      // If directory removes OK, return TRUE.
      if ($result = @rmdir($pTARGET) ) return TRUE;
    
      return FALSE;
  
    } // RemoveDirectory

    // Recursively creates a directory.
    function CreateDirectory ($pDIRECTORY) {

      if (!file_exists($pDIRECTORY)) {

        $this->CreateDirectory( dirname ($pDIRECTORY) );
  
        $result = mkdir ($pDIRECTORY, 0777);
      } // if
  
      return ($result);
  
    } // CreateDirectory

    // Check to see if an array has any values set.
    function ArrayIsSet ($pARRAY) {
  
      // Loop through the given array and check for values.
      if (is_array ($pARRAY) ) {
        foreach ($pARRAY as $key => $value) {
          if (isset ($value) ) return (TRUE);
        } // foreach
      } // if
  
      return (FALSE);
  
    } // ArrayIsSet
  
    // Send an email
    function SendMail ($pFROM, $pTO, $pSUBJECT, $pBODY, $pISHTML = TRUE) {

      // Create extra headers.
      $headers = "";
    
      if ($pISHTML) {
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
      } // if
  
      $headers .= "From: $pFROM \r\n";
      $headers .= "Reply-To: $pFROM \r\n";
  
      // Send mail invite to user.
      mail ($pTO, $pSUBJECT, $pBODY, $headers);
  
      return (0);
  
    } // SendMail
  
    // Splits a block of text into 78 columns and inserts line breaks.
    function QuoteReply ($pSTRING, $pAUTHORFULLNAME, $pAUTHORUSERNAME, $pAUTHORDOMAIN, $pMESSAGEDATE) {
   
      // Wasn't aware there already was a function to do this.  Rock.
      global $zSTRINGS;

      global $gAUTHORFULLNAME, $gAUTHORUSERNAME, $gAUTHORDOMAIN, $gMESSAGEDATE;

      $gAUTHORFULLNAME = $pAUTHORFULLNAME;
      $gAUTHORDOMAIN = $pAUTHORDOMAIN;
      $gAUTHORUSERNAME = $pAUTHORUSERNAME;
      $gMESSAGEDATE = $pMESSAGEDATE;

      $zSTRINGS->Lookup ("LABEL.QUOTE", "USER.MESSAGES");
      $title = $zSTRINGS->Output;
      
      $text = strip_tags($pSTRING);
      $text = wordwrap ($text, 65, "\n");
      $text = str_replace("\n", "\n> ", $text);

      // Add caption
      $wrapped = $title . "> " . $text;

      $wrapped = html_entity_decode ($wrapped);
      return ($wrapped);
      
    } // QuoteReply

    function StripExtension ($pFILENAME) {

      $extension = strrchr($pFILENAME, '.');

      if($extension !== false) {
        $pFILENAME = substr($pFILENAME, 0, -strlen($extension));
      } // if

      return $pFILENAME;

    } // StripExtension

    // End Application.
    function End () {
      global $zDEBUG, $zJANITOR;
      
      // Perform system maintenance.
      $zJANITOR->Maintenance ();
      
      // Echo the debug information.
      $zDEBUG->DisplayDebugInformation();
      
      exit;

    } // End
  
    // Application is terminated.
    function Abort ($pERRORMESSAGE = NULL) {

      // A woman's right to choose is non-negotiable.
      // http://www.naral.org

      exit ($pERRORMESSAGE);

    } // Abort

    // Generate a password.
    // Originally from bestcodingpractices.com
    function GeneratePassword($mask) {

      // Mask Rules
      // # - digit
      // C - Caps Character (A-Z)
      // c - Small Character (a-z)
      // X - Mixed Case Character (a-zA-Z)
      // ! - Custom Extended Characters

      $extended_chars = "!@#$%&*";

      $length = strlen($mask);

      $pwd = '';

      for ($c=0;$c<$length;$c++) {
        $ch = $mask[$c];
        switch ($ch) {
          case '#':
            $p_char = rand(0,9);
          break;
          case 'C':
            $p_char = chr(rand(65,90));
          break;
          case 'c':
            $p_char = chr(rand(97,122));
          break;
          case 'X':
            do {
              $p_char = rand(65,122);
            } while ($p_char > 90 && $p_char < 97);
            $p_char = chr($p_char);
          break;
          case '!':
            $p_char = $extended_chars[rand(0,strlen($extended_chars)-1)];
          break;
        } // switch
        $pwd .= $p_char;
      } // for

      return $pwd;

    } // GeneratePassword
  
    // Assign an ASD Tag value.
    function SetTag ($pTAGNAME, $pTAGVALUE) {

      $this->Tags[$pTAGNAME] = $pTAGVALUE;

      return (TRUE);
    } // SetTag

    // Remove an ASD Tag value.
    function UnsetTag ($pTAGNAME) {

      unset ($this->Tags[$pTAGNAME]);

      return (TRUE);
    } // UnsetTag

    // Return an ASD Tag value.
    function GetTag ($pTAGNAME) {
      return ($this->Tags[$pTAGNAME]);
    } // GetTag

  } // cAPPLICATION

?>
