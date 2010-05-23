<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: appleseed.php                           CREATED: 09-05-2005 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 01-04-2006 +
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
  // | DESCRIPTION.  Appleseed-specific class.                           |
  // +-------------------------------------------------------------------+

  require_once 'code/include/external/htmlpurifier/HTMLPurifier.auto.php';
  require_once 'code/include/external/phpmailer/class.phpmailer.php';
  require_once 'code/include/external/phpmailer/class.smtp.php';
  
  require_once 'system/language.php';
  
  class cAPPLESEED extends cAPPLICATION {

    var $Context;
    var $PurifierConfig;
    var $Purifier;
    var $Mailer;
    var $JSON;
    var $Tags;

    function cAPPLESEED () {
      
      $this->Context = "";

      $this->PurifierConfig = HTMLPurifier_Config::createDefault();
      $this->PurifierConfig->set('Core.Encoding', 'UTF-8'); //replace with your encoding
      $this->PurifierConfig->set('HTML.XHTML', false); //replace with false if HTML 4.01
      $this->Purifier = new HTMLPurifier($this->PurifierConfig);

      // Eventually remove and replace with PEAR/Mail::Queue.
      $this->Mailer = new PHPMailer();
      
      // Initialize ASD tags array.
      $this->Tags = array ();

      return (TRUE);
    } // Constructor

    // Overwrite inhereted ::Initialize function.
    function Initialize ($pCONTEXT = "", $pREGISTERGLOBALS = FALSE) {
      global $zDEBUG;
  
      $zDEBUG = new cDEBUG;
      
      $zDEBUG->BenchmarkStart ('SITE');
      
      // Capture all errors and warnings.
      set_error_handler (array ($zDEBUG, 'HandleError'));
      
      // Check if system is configured properly.
      $this->RuntimeVerification ();
      
      // Set the context.
      $this->SetContext ($pCONTEXT);
      
      // Load global strings into cache.
      cLanguage::Load ('en-US', 'system.global.lang');

      // Make sure we're not initializing twice.
      global $gINITIALIZED;
      if ($gINITIALIZED) {
        return (TRUE);
      } else {
        $gINITIALIZED = TRUE;
      } // if
  
      // Connect to the database.
      $this->DBConnect ();

      // Create global caching class.
      global $zCACHE;
      $zCACHE = new cBASEDATACACHE();
      
      // Load cache data if we can find it.
      if (file_exists ('code/include/data/cache/fields.acache')) include_once ('code/include/data/cache/fields.acache');
      if (file_exists ('code/include/data/cache/columns.acache')) include_once ('code/include/data/cache/columns.acache');
      
      global $gSETTINGS;
      
      $gSETTINGS = $this->LoadConfiguration ();
      
      global $gUSERTHEME, $gFRAMEWORK;

      $gUSERTHEME = $gSETTINGS['Theme'];
      $this->SetTag ('USERTHEME', $gUSERTHEME);
      $gFRAMEWORK = $gSETTINGS['Framework'];
      
      if (!file_exists ("themes/$gUSERTHEME")) $gUSERTHEME = 'default';
      
      global $gFRAMELOCATION, $gTHEMELOCATION;
  
      $gFRAMELOCATION = "frameworks/$gFRAMEWORK/";
      $gTHEMELOCATION = "themes/$gUSERTHEME/";
  
      // Global Variables
      global $gUSERTHEME;
      global $gLOGINSESSION, $gREMOTELOGINSESSION;
      global $gSITETITLE, $gSITEURL, $gSITEDOMAIN;
      global $gFOCUSUSERID, $gFOCUSUSERNAME;
      global $gFOCUSFULLNAME;
      global $gPROFILEREQUEST, $gPROFILEACTION, $gPROFILESUBACTION;
      global $gACTION;
      
      global $gTABLEPREFIX;
  
      // Initialize classes
      global $zSTRINGS, $zTOOLTIPS, $zOPTIONS, $zLOGS, $zHTML, $zXML, $zSEARCH, $zTAGS;
      
      global $zAUTHUSER, $zREMOTEUSER, $zLOCALUSER, $zFOCUSUSER;
      global $zIMAGE;
      
      $zSTRINGS = new cSYSTEMSTRINGS ();
      $zTOOLTIPS = new cSYSTEMTOOLTIPS ();
      $zOPTIONS = new cSYSTEMOPTIONS ();
      $zLOGS    = new cSYSTEMLOGS ();
  
      $zIMAGE   = new cIMAGE ();
      $zHTML    = new cHTML ();
      $zXML     = new cXML ();
      $zSEARCH  = new cSEARCH ();
      $zTAGS  = new cTAGLIST ();
  
      $zFOCUSUSER = new cUSER ();
      $zLOCALUSER = new cUSER ();
      $zREMOTEUSER = new cAUTHSESSIONS ();

      $zAUTHUSER = new cAUTHUSER ();
      
      // Fake register_globals and magic_slashes functionality.
      // NOTE: Eventually phase out this section.
      if ($pREGISTERGLOBALS) {

        // Strip all slashes from POST data.
        foreach ($_REQUEST as $key => $value) {
       
          // Put the global variable in local scope.
          global $$key;
          $$key = $_REQUEST[$key];
        
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
    
      } // if
  
      // Load site title and url into global variable.
      $zSTRINGS->Lookup ('BROWSER.TITLE', $this->Context);
      $gSITETITLE = $zSTRINGS->Output;
  
      // Check for gLOGINSESSION cookie.
      $gLOGINSESSION = isset($_COOKIE["gLOGINSESSION"]) ? 
                              $_COOKIE["gLOGINSESSION"] : "";
  
      // Check for gLOGINSESSION cookie.
      $gREMOTELOGINSESSION = isset($_COOKIE["gREMOTELOGINSESSION"]) ? 
                                   $_COOKIE["gREMOTELOGINSESSION"] : "";

      // Pull zLOCALUSER info from database.
      // Check if user is bouncing.
      if (!$this->Bounce ()) {
        if ($gLOGINSESSION) {
  
          $zLOCALUSER->userSession->Select ("Identifier", $gLOGINSESSION);
          $zLOCALUSER->userSession->FetchArray ();
          $zLOCALUSER->uID = $zLOCALUSER->userSession->userAuth_uID;
  
          $zLOCALUSER->Select ("uID", $zLOCALUSER->uID);
          if ($zLOCALUSER->CountResult() == 0) {
            // User is anonymous.
            $zSTRINGS->Lookup ('LABEL.ANONYMOUS');
            $zAUTHUSER->Username = $zSTRINGS->Output;
            $zSTRINGS->Lookup ('LABEL.ANONYMOUS.FULLNAME');
            $zAUTHUSER->Fullname = $zSTRINGS->Output;
            $zAUTHUSER->Domain = $gSITEDOMAIN;
            $zAUTHUSER->Remote = FALSE;
            $zAUTHUSER->Anonymous = TRUE;
            $zLOCALUSER->userSession->Destroy ('gLOGINSESSION');
          } else {
            $zLOCALUSER->FetchArray ();
    
            $zLOCALUSER->Access ();
            // Global variables
            $this->SetTag('AUTHUSERNAME', $zLOCALUSER->Username);
            $this->SetTag('AUTHUSERID', $zLOCALUSER->uID);
            $this->SetTag('AUTHDOMAIN', $gSITEDOMAIN);
  
            $zAUTHUSER->uID = $zLOCALUSER->uID;
            $zAUTHUSER->Username = $zLOCALUSER->Username;
            $zAUTHUSER->Fullname = $zLOCALUSER->userProfile->GetAlias ();
            $zAUTHUSER->Domain = $gSITEDOMAIN;
            $zAUTHUSER->Remote = FALSE;
  
            // Update Online Stamp
            $zLOCALUSER->userInformation->UpdateOnlineStamp ();
          } // if
  
        } elseif ($gREMOTELOGINSESSION) {
          
          $zREMOTEUSER->Select ("Identifier", $gREMOTELOGINSESSION);
          $zREMOTEUSER->FetchArray ();
  
          if ($zREMOTEUSER->CountResult() == 0) {
            // User is anonymous.
            $zSTRINGS->Lookup ('LABEL.ANONYMOUS');
            $zAUTHUSER->Username = $zSTRINGS->Output;
            $zSTRINGS->Lookup ('LABEL.ANONYMOUS.FULLNAME');
            $zAUTHUSER->Fullname = $zSTRINGS->Output;
            $zAUTHUSER->Domain = $gSITEDOMAIN;
            $zAUTHUSER->Remote = FALSE;
            $zAUTHUSER->Anonymous = TRUE;
            $zREMOTEUSER->Destroy ('gREMOTELOGINSESSION');
          } else {
            $zAUTHUSER->Username = $zREMOTEUSER->Username;
            $zAUTHUSER->Fullname = $zREMOTEUSER->Fullname;
            $zAUTHUSER->Domain = $zREMOTEUSER->Domain;
            $zAUTHUSER->Remote = TRUE;
            $this->SetTag('AUTHUSERNAME', $zAUTHUSER->Username);
            $this->SetTag('AUTHDOMAIN', $zAUTHUSER->Domain);
          } // if
        } else {
          // User is anonymous.
          $zSTRINGS->Lookup ('LABEL.ANONYMOUS');
          $zAUTHUSER->Username = $zSTRINGS->Output;
          $zSTRINGS->Lookup ('LABEL.ANONYMOUS.FULLNAME');
          $zAUTHUSER->Fullname = $zSTRINGS->Output;
          $zAUTHUSER->Domain = $gSITEDOMAIN;
          $zAUTHUSER->Remote = FALSE;
          $zAUTHUSER->Anonymous = TRUE;
        } // if
      } // if
  
      // Clear "bounce" paramater from URL.
      $this->ClearBounce ();

      // Load user settings into memory cache.
      $zLOCALUSER->userSettings->Load ();

      // Set Site Domain tag.
      $this->SetTag ('SITEDOMAIN', $gSITEDOMAIN);
      
      // Set default theme.
      if ($zLOCALUSER->userSettings->GetTheme()) {
        global $gTHEMELOCATION;
        $gUSERTHEME = $zLOCALUSER->userSettings->GetTheme();
        $this->SetTag ('USERTHEME', $gUSERTHEME);
        $gTHEMELOCATION = "themes/$gUSERTHEME/";
      } // if
      
      // Check whether site is in shutdown state.
      $this->Shutdown ($gSETTINGS['Shutdown']);

      // Pull the focus user information
      if ($gPROFILEREQUEST) {
        // Split the request into username and action
        $profile_array = explode ('/', $gPROFILEREQUEST, 3);
        if (isset($profile_array[0])) $gFOCUSUSERNAME = $profile_array[0];
        if (isset($profile_array[1])) $gPROFILEACTION = $profile_array[1];
        if (isset($profile_array[2])) {
          $gPROFILESUBACTION = $profile_array[2];
          $gPROFILESUBACTION = rtrim ($gPROFILESUBACTION, '/');
        } // if
  
        // Sanity check the username and action.
  
        // Look up the focus user id from the database.
        $zFOCUSUSER->Select ('Username', $gFOCUSUSERNAME);
        $zFOCUSUSER->FetchArray ();
        $zFOCUSUSER->userProfile->Select ('userAuth_uID', $zFOCUSUSER->uID);
        $zFOCUSUSER->userProfile->FetchArray ();
        $zFOCUSUSER->userSettings->Load ();
        $gFOCUSUSERID = $zFOCUSUSER->uID;
        $gFOCUSFULLNAME = $zFOCUSUSER->userProfile->GetAlias ();
        $this->SetTag ('FOCUSFULLNAME', $gFOCUSFULLNAME);
      } // if
  
      global $gADMINEMAIL;
      $gADMINEMAIL = 'admin@' . $gSITEDOMAIN;
      $this->SetTag ('ADMINEMAIL', $gADMINEMAIL);

      $MESSAGES = new cMESSAGE ();
      $this->SetTag ('NEWMESSAGES', $MESSAGES->CountNewMessages ());
  
      unset ($MESSAGES);
  
      $FRIENDS = new cFRIENDINFORMATION ();
      $this->SetTag ('NEWFRIENDS', $FRIENDS->CountNewFriends ()); 
  
      unset ($FRIENDS);

      // Modify gACTION from BUTTONNAME
      $gACTION = strtoupper ($gACTION);
      $gACTION = str_replace (' ', '_', $gACTION);
      
      // Set the timezone.
      $this->SetTimeZone();

      $this->SetTag ('FOCUSUSERNAME', $gFOCUSUSERNAME);
      
      global $zJANITOR;
      $zJANITOR = new cSYSTEMMAINTENANCE();
      
      global $zCACHE;
      
      return (TRUE); 
      
    } // Initialize
    
    // Set the time zone according to user settings or geolocated IP.
    function SetTimeZone () {
      // Get the time
      date_default_timezone_set("America/New_York");
    } // SetTimeZone

    // Check whether system is configured properly.
    function RuntimeVerification () {

      if (file_exists ('index.php')) {
        // Install script still exists.  Warn admin, potential security hazard.  
        trigger_error("Install script (index.php) is still available in the web directory.  " .
                      "This is a potential security hazard.  Please delete index.php.", E_USER_WARNING);
      } // if
      
      if ((ini_get ('magic_quotes_gpc')) or (ini_get('magic_quotes_runtime'))) {
        // Magic Quotes is on.  Exit with error.
        echo "ERROR: magic_quotes is on. Please disable.<br />";
        $this->Abort ();
      } // if

      if (ini_get('register_globals')) {
        // Register globals is on.  Exit with error.
        echo "ERROR: register_globals is on. Please disable.<br />";
        $this->Abort ();
      } // if
      
      if (!is_writable('photos/')) {
        // Photos directory isn't writable.
        echo "ERROR: photos/ directory is not writable.";
        $this->Abort ();
      } // if

      if (!is_writable('attachments/')) {
        // Photos directory isn't writable.
        echo "ERROR: attachments/ directory is not writable.";
        $this->Abort ();
      } // if

      return (TRUE);
    } // RuntimeVerification

    // Remove "bounce" paramater from URL if it exists.
    function ClearBounce () {
      $host = $_SERVER['HTTP_HOST'];
      $self = $_SERVER['REQUEST_URI'];
      
      // Return FALSE if we're not bouncing.
      if (!strstr ($self, '?')) return (FALSE);
      
      list ($NULL, $get) = explode ('?', $self);
      list ($value, $bounce) = explode ('=', $get);
      $get = '?' . $get;

      if ($$value) {
        $redirect = "http://" . $host . $self;
        $redirect = str_replace ($get, NULL, $redirect);

        Header ("Location: " . $redirect);
        exit;
      } // if

      return (FALSE);
    } // ClearBounce

    // Check if user is 'bouncing' persistent remote login.
    function Bounce ($pCONTEXT = "") {
      
      global $zLOCALUSER, $zREMOTEUSER, $zXML;
      global $gLOGINSESSION, $gREMOTELOGINSESSION;
      global $gAPPLESEEDVERSION;

      $host = $_SERVER['HTTP_HOST'];
      $self = $_SERVER['REQUEST_URI'];
      
      // Return FALSE if we're not bouncing.
      if (!strstr ($self, '?')) return (FALSE);
      
      list ($NULL, $get) = explode ('?', $self);
      list ($value, $bounce) = explode ('=', $get);
      $get = '?' . $get;
      
      if (($value == 'bounce') and ($$value)) {
        // Remove any logged in users.
        if ($gLOGINSESSION) $zLOCALUSER->userSession->Destroy ('gLOGINSESSION');
        if ($gREMOTELOGINSESSION) $zREMOTEUSER->Destroy ('gREMOTELOGINSESSION');
        
        list ($username, $domain) = explode ('@', $$value);
        
        $zNODE = new cSYSTEMNODES();
        if ($zNODE->Blocked ($username, $domain) ) {
          $redirect = "http://" . $host . '/login/remote/';
          $redirect = str_replace ($get, NULL, $redirect);

          // Redirect to the location without the home information.
          Header ("Location: " . $redirect);
          exit;
        } // if
      
        // Create the Remote class.
        $zREMOTE = new cREMOTE ($domain);

        $VERIFY = new cAUTHTOKENS ();
        $token = $VERIFY->LoadToken ($username, $domain);
        if (!$token) {
          $token = $VERIFY->CreateToken ($username, $domain);
        } // if
        unset ($VERIFY);

        $datalist = array ("gACTION"   => "ASD_LOGIN_CHECK",
                           "gTOKEN"    => $token,
                           "gUSERNAME" => $username,
                           "gVERSION"  => $gAPPLESEEDVERSION,
                           "gDOMAIN"   => $host);
        $zREMOTE->Post ($datalist);

        $zXML->Parse ($zREMOTE->Return);

        $ip_address = $zXML->GetValue ("address", 0);
        $zREMOTEUSER->Username = $zXML->GetValue ("username", 0);
        $zREMOTEUSER->Fullname = $zXML->GetValue ("fullname", 0);
        $zREMOTEUSER->Domain = $zXML->GetValue ("domain", 0);

        if ($zREMOTEUSER->Username) {
          $zREMOTEUSER->Create (FALSE, "gREMOTELOGINSESSION");
        } // if

        $redirect = "http://" . $host . $self;
        $redirect = str_replace ($get, NULL, $redirect);

        // Redirect to the location without the home information.
        Header ("Location: " . $redirect);
        exit;

      } else {
        return (FALSE);
      } // if

      return (FALSE);
    } // Bounce

    function SetContext ($pCONTEXT) {

      $this->Context = $pCONTEXT;

    } // SetContext

    // Set global variables with default values.
    function SetGlobals () {
  
      // Make sure we're not initializing twice.
      global $gGLOBALIZED;
      if ($gGLOBALIZED) {
        return (TRUE);
      } else {
        $gGLOBALIZED = TRUE;
      } // if
  
      global $gFOCUSUSERID, $gFOCUSUSERNAME, $gPROFILEACTION, $gPROFILESUBACTION;
      global $gERRORMSG, $gERRORTITLE;
  
      global $gUSERJOURNALTAB, $gUSERPHOTOSTAB;
      global $gUSERENEMIESTAB, $gUSERFRIENDSTAB;
      global $gUSERGROUPSTAB;
      global $gUSERMESSAGESTAB, $gUSERINFOTAB;
      global $gUSEROPTIONSTAB;
  
      global $gCOMMENTREADTAB, $gCOMMENTADDTAB;
  
      global $gCONTENTARTICLESVIEWTAB;
      global $gCONTENTARTICLESSUBMITTAB;
      global $gCONTENTARTICLESQUEUETAB;
  
      global $gPROFILEREQUEST;
  
      global $gADMINUSERSACCOUNTSTAB, $gADMINUSERSBILLINGTAB;
      global $gADMINUSERSACCESSTAB, $gADMINUSERSOPTIONSTAB;
      global $gADMINUSERSQUESTIONSTAB;
  
      global $gADMINSYSTEMSTRINGSTAB, $gADMINSYSTEMTOOLTIPSTAB;
      global $gADMINSYSTEMOPTIONSTAB, $gADMINSYSTEMNODESTAB, $gADMINSYSTEMLOGSTAB, $gADMINSYSTEMMAINTENANCETAB;
  
      global $gADMINCONTROLCONFIGTAB, $gADMINCONTROLSECURITYTAB;
      global $gADMINCONTROLSTORAGETAB, $gADMINCONTROLDEFAULTSTAB;
  
      global $gADMINCONTENTARTICLESTAB, $gADMINCONTENTPAGESTAB;
      global $gADMINCONTENTMODULESTAB, $gADMINCONTENTTHEMESTAB;
  
      global $gSITETITLE, $gSITEURL;
      global $gREFRESHWAIT;
      global $gPAGETITLE, $gPAGESUBTITLE;
  
      global $gADMINCONFIGSWITCH, $gADMINUSERSSWITCH, $gADMINGROUPSSWITCH;
      global $gADMINCONTENTSWITCH, $gADMINSYSTEMSWITCH, $gADMINCONTROLSWITCH;
  
      global $gSCROLLSTART, $gSCROLLMAX, $gSCROLLSTEP;
      global $gMAXPAGES, $gCURRENTPAGE;
  
      global $gUSERTABSLOCATION;
  
      global $gPROFILEPHOTOSIZE;
      global $gPROFILEPHOTOX, $gPROFILEPHOTOY;
      global $gUSERICONX, $gUSERICONY, $gUSERICONSIZE;
      global $gMAXICONS, $gMAXPHOTOSETS;
      global $gMAXPHOTOX, $gMAXPHOTOY;
  
      global $gPHOTOTHUMBX, $gPHOTOTHUMBY;
  
      global $gBROADCASTUNIQUE;
  
      global $gLOGINSESSION;
  
      global $gPOSTDATA, $gEXTRAPOSTDATA;
  
      global $gDBERROR;
  
      global $gALTERNATE;
      
      global $gSELECTBUTTON;
  
      global $gDBLINK;
  
      global $gADMINEMAIL;
  
      global $gACTION, $gSORT, $gMASSLIST;

      global $gDEBUG;

      // Define constants.
      define ("UP",  "UP");
      define ("DOWN", "DOWN");
      define ("ON", "ON");
      define ("OFF", "OFF");
      
      // Defined for site shutdown 
      define ("ADMIN_ONLY", "ADMIN_ONLY");
  
      define ("YES", "YES");
      define ("NO", "NO");
  
      define ("OLDER", "OLDER");
      define ("NEWER", "NEWER");
  
      define ("DYNAMIC", "DYNAMIC");
      define ("STATIC", "STATIC");
  
      define ("INACTIVE", "0");
      define ("ACTIVE", "1");
      define ("PENDING", "2");
  
      define ("DISABLED", "disabled");
      define ("ENABLED", "enabled");
      
      define ("PHANTOM_USER_ID", "0");

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
  
      define ("VIEW_DEFAULT", 1);
      define ("VIEW_ALL", 2);
      define ("VIEW_COMPACT", 3);
      define ("VIEW_STANDARD", 4);
      define ("VIEW_FULL", 5);
      define ("VIEW_EDITOR", 6);
  
      define ("COMMENT_VIEW_EDITOR", 1);
      define ("COMMENT_VIEW_DEFAULT", 1);
      define ("COMMENT_VIEW_NESTED", 2);
      define ("COMMENT_VIEW_THREADED", 3);
      define ("COMMENT_VIEW_FLAT", 4);
      define ("COMMENT_VIEW_COMPACT", 5);
      define ("COMMENT_VIEW_PROFILE", 100);

      define ("GROUP_VIEW_EDITOR", 1);
      define ("GROUP_VIEW_DEFAULT", 1);
      define ("GROUP_VIEW_NESTED", 2);
      define ("GROUP_VIEW_THREADED", 3);
      define ("GROUP_VIEW_FLAT", 4);
      define ("GROUP_VIEW_COMPACT", 5);
  
      define ("JOURNAL_VIEW_DEFAULT", 1);
      define ("JOURNAL_VIEW_SINGLE", 2);
      define ("JOURNAL_VIEW_MULTIPLE", 3);
      define ("JOURNAL_VIEW_LISTING", 4);
      define ("JOURNAL_VIEW_ADMIN", 5);
  
      define ("QUESTION_MENU", 0);        // Menu
      define ("QUESTION_CHECKLIST", 1);   // Checklist
      define ("QUESTION_STRING", 2);      // String
      define ("QUESTION_WEBLINK", 3);     // Web Link
      define ("QUESTION_LINKED", 4);      // Linked String
  
      define ("OUTPUT_SCREEN",  "0");
      define ("OUTPUT_BUFFER",  "1");
  
      define ("SCROLL_PAGES",   "SCROLL_PAGES");
      define ("SCROLL_NOFIRST", "SCROLL_NOFIRST");
      define ("SCROLL_SPECIAL", "SCROLL_SPECIAL");
  
      define ("SKIP_UNIQUE",   FALSE);
      define ("CHECK_UNIQUE",  TRUE);
  
      define ("PRIVACY_ALLOW",     "0");
      define ("PRIVACY_SCREEN",    "1");
      define ("PRIVACY_RESTRICT",  "2");
      define ("PRIVACY_BLOCK",     "3");
      define ("PRIVACY_HIDE",      "4");
  
      define ("USER_EVERYONE",     "1000");
      define ("USER_LOGGEDIN",     "2000");
      
      define ("USER_TAG_LIMIT", 3);
      define ("USER_TAG_MAXLENGTH", 16);
  
      define ("SQL_SKIP", '*!');
      define ("SQL_NOW", '@!');
      define ("SQL_NOT", '^!');
      define ("SQL_GT", '%!');
      define ("SQL_LT", '&!');
      define ("SQL_LIKE", '#!');
      
      define ("STAMP_NEVER", '0000-00-00 00:00:00');

      define ("SUCCESS", 1);
  
      define ("FOLDER_INBOX", 1);
      define ("FOLDER_SENT", 2);
      define ("FOLDER_DRAFTS", 3);
      define ("FOLDER_SPAM", 4);
      define ("FOLDER_TRASH", 5);
      define ("FOLDER_ARCHIVE", 6);
  
      define ("MESSAGE_UNREAD", 1);
      define ("MESSAGE_READ", 2);
      define ("MESSAGE_QUEUED", 3);
      
      define ("MENU_DISABLED",  "$!");

      define ("MESSAGE_TYPE_LOCAL", 1);
      define ("MESSAGE_TYPE_REMOTE", 2);
      define ("MESSAGE_TYPE_SENT", 3);
  
      define ("CIRCLE_NEWEST", 'x101');
      define ("CIRCLE_VIEWALL", 'x102');
      define ("CIRCLE_REQUESTS", 'x103');
      define ("CIRCLE_PENDING", 'x104');
      define ("CIRCLE_EDITOR", 'x105');
      define ("CIRCLE_VIEWCIRCLES", 'x106');
  
      define ("FRIEND_VERIFIED", 1);
      define ("FRIEND_REQUESTS",  2);
      define ("FRIEND_PENDING",  3);
      define ("FRIEND_REJECTED", 4);
  
      define ("NO_ICON",  "__noicon__");

      define ("DELETED_COMMENT",  "__deleted_comment__");
      define ("DELETED_GROUP_ENTRY",  "__deleted_group_entry__");
  
      define ("ANONYMOUS",  "__anonymous__");
      
      define ("OUTPUT_NBSP",  "&nbsp;");

      define ("ARTICLE_PENDING",  0);
      define ("ARTICLE_APPROVED",  1);
      define ("ARTICLE_REJECTED",  2);

      define ("NOTIFICATION_ON", 1);
      define ("NOTIFICATION_OFF", 2);

      define ("GROUP_VERIFICATION_PENDING", 10);
      define ("GROUP_VERIFICATION_INVITED", 20);
      define ("GROUP_VERIFICATION_APPROVED", 30);

      define ("GROUP_ACCESS_OPEN", 10);
      define ("GROUP_ACCESS_OPEN_PRIVATE", 20);
      define ("GROUP_ACCESS_APPROVAL_PUBLIC", 30);
      define ("GROUP_ACCESS_APPROVAL_PRIVATE", 40);
      define ("GROUP_ACCESS_INVITE_PUBLIC", 50);
      define ("GROUP_ACCESS_INVITE_PRIVATE", 60);

      define ("GROUP_ACTION_APPROVE", 1);
      define ("GROUP_ACTION_REMOVE", 2);
      
      define ("PHOTO_THUMB_SMALL_WIDTH", 100);
      define ("PHOTO_THUMB_SMALL_HEIGHT", 100);

      define ("PHOTO_THUMB_MEDIUM_WIDTH", 150);
      define ("PHOTO_THUMB_MEDIUM_HEIGHT", 150);

      define ("PHOTO_THUMB_LARGE_WIDTH", 200);
      define ("PHOTO_THUMB_LARGE_HEIGHT", 200);

      $gERRORMSG = ''; $gERRORTITLE = 'ERROR';
      define ("PHOTO_FINAL_WIDTH", 500);
      define ("PHOTO_FINAL_HEIGHT", 1500);
      
      define ("PHOTO_MAX_SIZE", 500000);
      
      define ("NODE_TRUSTED", 10);
      define ("NODE_BLOCKED", 20);
      
      define ("NODE_UNVERIFIED", 0);
      define ("NODE_VERIFIED", 10);
      define ("NODE_INVALID", -10);
      
      define ("NODE_ALL_USERS", '*');
      
      define ("TOKEN_LOCAL", 10);
      define ("TOKEN_REMOTE", 20);
      
      $gERRORMSG = ''; $gERRORTITLE = 'ERROR';
  
      $gCONTENTARTICLESVIEWTAB = '_off';
      $gCONTENTARTICLESSUBMITTAB = '_off';
      $gCONTENTARTICLESQUEUETAB = '_off';

      $gUSERJOURNALTAB = '_off'; $gUSERPHOTOSTAB = '_off';
      $gUSERFRIENDSTAB = '_off'; $gUSERINFOTAB = '_off'; 
      $gUSERMESSAGESTAB = '_off'; $gUSERGROUPSTAB = '_off'; 
      $gUSEROPTIONSTAB = '_off';

      $this->SetTag ('USERJOURNALTAB', $gUSERJOURNALTAB);
      $this->SetTag ('USERPHOTOSTAB', $gUSERPHOTOSTAB);
      $this->SetTag ('USERFRIENDSTAB', $gUSERFRIENDSTAB);
      $this->SetTag ('USERINFOTAB', $gUSERINFOTAB);
      $this->SetTag ('USERMESSAGESTAB', $gUSERMESSAGESTAB);
      $this->SetTag ('USERGROUPSTAB', $gUSERGROUPSTAB);
      $this->SetTag ('USEROPTIONSTAB', $gUSEROPTIONSTAB);
  
      $gADMINUSERSACCOUNTSTAB = '_off'; $gADMINUSERSBILLINGTAB = '_off';
      $gADMINUSERSACCESSTAB = '_off'; $gADMINUSERSOPTIONSTAB = '_off';
      $gADMINUSERSQUESTIONSTAB = '_off';
  
      $gADMINSYSTEMSTRINGSTAB = '_off'; $gADMINSYSTEMTOOLTIPSTAB = '_off'; 
      $gADMINSYSTEMOPTIONSTAB = '_off';  $gADMINSYSTEMNODESTAB = '_off'; 
      $gADMINSYSTEMLOGSTAB = '_off'; $gADMINSYSTEMMAINTENANCETAB = '_off';
  
      $gADMINCONTROLCONFIGTAB = '_off'; $gADMINCONTROLSECURITYTAB = '_off';
      $gADMINCONTROLDEFAULTSTAB = '_off'; $gADMINCONTROLSTORAGETAB = '_off';
  
      $gADMINCONTENTARTICLESTAB = '_off'; $gADMINCONTENTPAGESTAB = '_off';
      $gADMINCONTENTMODULESTAB = '_off'; $gADMINCONTENTTHEMESTAB = '_off';
  
      $gADMINCONFIGSWITCH = '_off'; $gADMINUSERSSWITCH = '_off';  $gADMINGROUPSSWITCH = '_off';
      $gADMINCONTENTSWITCH = '_off'; $gADMINSYSTEMSWITCH = '_off';
      $gADMINCONTROLSWITCH = '_off';
  
      $gCOMMENTREADTAB = "_off"; $gCOMMENTADDTAB = "_off";
  
      $gALTERNATE = 0;
  
      $gSELECTBUTTON = '';
  
      $gREFRESHWAIT = 0;
  
      $gPROFILEPHOTOSIZE = 250000;
      $gPROFILEPHOTOX = 228;
      $gPROFILEPHOTOY = 2000;
  
      $gUSERICONX = 100; $gUSERICONY = 100;
      $gUSERICONSIZE = 250000;
      $gMAXICONS = 15;
  
      $gPHOTOTHUMBX = 100; $gPHOTOTHUMBY = 100;
  
      $gMAXPHOTOX = 2048; 
      $gMAXPHOTOY = 2048;
  
      $gMAXPHOTOSETS = 8;
  
      if (!isset ($gSCROLLSTART)) $gSCROLLSTART  = Array ();
      if (!isset ($gSCROLLSTEP)) $gSCROLLSTEP  = Array ();
      if (!isset ($gSCROLLMAX)) $gSCROLLMAX  = Array ();
  
      $gBROADCASTUNIQUE = '';
  
      $gUSERTABSLOCATION = '';
  
       // Debug settings.
       $gDEBUG['echostatement'] = FALSE;

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
  
    } // SetGlobals;

    // NOTE: Problem when adjusting for security.  SCROLLMAX gets reset to
    // high amount when switching pages.  Same problem as Livejournal?

    // Adjust for a hidden entry.
    function AdjustHiddenScroll ($pPRIVACYSETTING, $pCONTEXT) {

      global $zLOCALUSER, $zFOCUSUSER;
      global $gSCROLLMAX, $gLISTCOUNT;

      // Check if photoset is hidden, and skip.
      if ( ($pPRIVACYSETTING == PRIVACY_HIDE) and
           ($zLOCALUSER->userAccess->r == FALSE) and
           ($zLOCALUSER->uID != $zFOCUSUSER->uID)  ) {

        // Rollback the for counter by 1.
        $gLISTCOUNT--;

        // Subtract the max results value by 1.
        $gSCROLLMAX[$pCONTEXT]--;

        return (TRUE);
      } // if

      return (FALSE);
    } // AdjustHiddenScroll

    // Adjust for a blocked entry.
    function AdjustBlockedScroll ($pPRIVACYSETTING, $pCONTEXT) {

      global $zLOCALUSER, $zFOCUSUSER;
      global $gSCROLLMAX, $gLISTCOUNT;

      // Check if photoset is hidden, and skip.
      if ( ($pPRIVACYSETTING == PRIVACY_BLOCK) and
           ($zLOCALUSER->userAccess->r == FALSE) and
           ($zLOCALUSER->uID != $zFOCUSUSER->uID)  ) {

        // Rollback the for counter by 1.
        $gLISTCOUNT--;

        // Subtract the max results value by 1.
        $gSCROLLMAX[$pCONTEXT]--;

        return (TRUE);
      } // if

      return (FALSE);
    } // AdjustBlockedScroll

    // Check the security on a single entry.
    function CheckSecurity ($pPRIVACYSETTING) {

      global $zLOCALUSER, $zFOCUSUSER;

      if ( ($pPRIVACYSETTING == PRIVACY_HIDE) and
           ($zLOCALUSER->userAccess->r == FALSE) and
           ($zLOCALUSER->uID != $zFOCUSUSER->uID)  ) {
        return (TRUE);
      } // if

      if ( ($pPRIVACYSETTING == PRIVACY_BLOCK) and
           ($zLOCALUSER->userAccess->r == FALSE) and
           ($zLOCALUSER->uID != $zFOCUSUSER->uID)  ) {
        return (TRUE);
      } // if

      return (FALSE);
    } // CheckAvailableScroll

    // Output the profile questions and answers.
    function Profile () {
   
      // Set global variable scope.
      global $zHTML, $zFOCUSUSER, $zSTRINGS, $zOPTIONS;

      global $gFRAMELOCATION;
      global $gQUESTIONSTYLE, $gQUESTIONANSWER;
  
      // Buffer the profile questions.
      ob_start ();  

      // Start with the hardcoded questions
      global $gFOCUSFULLNAME;

      $gQUESTIONSTYLE = 'fullname';
      $zSTRINGS->Lookup ("LABEL.FULLNAME", "USER.PROFILE");
      $gQUESTIONANSWER = $zSTRINGS->Output;
      $this->IncludeFile ("$gFRAMELOCATION/objects/user/profile/question.aobj", INCLUDE_SECURITY_NONE);

      global $gFOCUSGENDER;
      $gFOCUSGENDER = $zOPTIONS->Label ("GENDER", $zFOCUSUSER->userProfile->Gender);
      $this->SetTag ('FOCUSGENDER', $gFOCUSGENDER);
      $gQUESTIONSTYLE = 'gender';
      $zSTRINGS->Lookup ("LABEL.GENDER", "USER.PROFILE");
      $gQUESTIONANSWER = $zSTRINGS->Output;
      $this->IncludeFile ("$gFRAMELOCATION/objects/user/profile/question.aobj", INCLUDE_SECURITY_NONE);
 
      $this->SetTag ('FOCUSAGE', $this->CalculateAge ($zFOCUSUSER->userProfile->Birthday)); 
      $gQUESTIONSTYLE = 'age'; 
      $zSTRINGS->Lookup ("LABEL.AGE", "USER.PROFILE");
      $gQUESTIONANSWER = $zSTRINGS->Output;
      $this->IncludeFile ("$gFRAMELOCATION/objects/user/profile/question.aobj", INCLUDE_SECURITY_NONE);
 
      // Load the configurable questions
      $QUESTIONSLIST = new cUSERQUESTIONS ();

      $criterialist = array ("Language" => "en",
                             "Visible"  => TRUE );   
      $QUESTIONSLIST->SelectByMultiple ($criterialist);

      global $gFOCUSQUESTION, $gFOCUSANSWER;

      // Loop through the questions list.
      while ($QUESTIONSLIST->FetchArray ()) {
        $gFOCUSQUESTION = $QUESTIONSLIST->ShortQuestion;

        // Set the user answer to NULL.
        $zFOCUSUSER->userAnswers->Answer = "";

        // Load the focus user's answer.
        $infocriteria = array ("userAuth_uID"      => $zFOCUSUSER->uID,
                               "userQuestions_tID" => $QUESTIONSLIST->tID);
        $zFOCUSUSER->userAnswers->SelectByMultiple ($infocriteria);
        $zFOCUSUSER->userAnswers->FetchArray ();

        if ($zFOCUSUSER->userAnswers->Answer == "") continue;

        // Determine output depending on type of answer.
        switch ($QUESTIONSLIST->TypeOf) {

          case QUESTION_MENU:
            $gFOCUSANSWER = $zOPTIONS->Label ($QUESTIONSLIST->Concern, $zFOCUSUSER->userAnswers->Answer);
          break;

          case QUESTION_CHECKLIST:
            // Explode the values, load their labels, and implode them together.
            $answerlist = explode (',', $zFOCUSUSER->userAnswers->Answer);
            $answerlabels = array ();
            foreach ($answerlist as $answercount => $answer) {
              $answerlabels[$answercount] = $zOPTIONS->Label ($QUESTIONSLIST->Concern, $answer);
            } // foreach
            $gFOCUSANSWER = implode (", ", $answerlabels);
          break;

          case QUESTION_STRING:
            $gFOCUSANSWER = $zFOCUSUSER->userAnswers->Answer;
          break;

          case QUESTION_WEBLINK:
            // NOTE: Split by commas and then implode so multiple websites can be listed.
            $gFOCUSANSWER = $zFOCUSUSER->userAnswers->Answer;
            $gFOCUSANSWER = $zHTML->NakedLink ($gFOCUSANSWER, $gFOCUSANSWER, "", "__NEW");
          break;

          case QUESTION_LINKED:
            $gFOCUSANSWER = $zFOCUSUSER->userAnswers->Answer;
          break;
        } // switch

        $gQUESTIONSTYLE = 'question' . $QUESTIONSLIST->tID;
        $zSTRINGS->Lookup ("LABEL.QUESTION", "USER.PROFILE");
        $gQUESTIONANSWER = $zSTRINGS->Output;
        $this->IncludeFile ("$gFRAMELOCATION/objects/user/profile/question.aobj", INCLUDE_SECURITY_NONE);
      } // while

      unset ($QUESTIONSLIST);

      // Retrieve output buffer.
      global $bPROFILEQUESTIONS;
      $bPROFILEQUESTIONS = ob_get_clean (); 
 
      $this->UnsetTag ('FOCUSGENDER');

      return (0);

    } // Profile

    // Buffer the contact box
    function BufferContactBox () {

      global $gFRAMELOCATION;

      global $zAUTHUSER;

      $return = $this->IncludeFile ("$gFRAMELOCATION/objects/user/profile/contact.aobj", 
                                    INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      return ($return);

    } // BufferContactBox
    
    function BufferInviteBox ($pINVITECOUNT) {
      global $gFRAMELOCATION;

      // If no invites are available, hide the invite box.
      if ($pINVITECOUNT == 0) {
        $return = "";
      } else {
        $return = $this->IncludeFile ("$gFRAMELOCATION/objects/user/profile/invite.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } // if

      return ($return);
    } // BufferInviteBox

    // Overwrite inhereted ::ParseTags function.
    // Parse through <asd> and %% tags.
    function ParseTags ($pPARSEDATA) {
  
      // Parse through the %% tags
      $pattern = "/%(\w+?)%/si";
      preg_match_all ($pattern, $pPARSEDATA, $tagvalues);
      global $gCOMMENTCOUNT;
  
      foreach ($tagvalues[1] as $tagval) {
        $pattern = "/%$tagval%/";
        if (!isset ($this->Tags[$tagval])) {
          // Trigger a warning.
          trigger_error("Still using old global system for ASD tag '$tagval'.  Move to new Tags() system.", E_USER_WARNING);
          $pval = "g" . strtoupper ($tagval);
          global $$pval;
          if (!isset ($$pval)) $$pval = "<!-- (unknown tag: $$pval) -->";
          $pPARSEDATA = preg_replace ($pattern, $$pval, $pPARSEDATA);
        } else {
          $pPARSEDATA = preg_replace ($pattern, $this->Tags[$tagval], $pPARSEDATA);
        } // if
        $pPARSEDATA = $this->ParseTags ($pPARSEDATA);
      } // foreach
  
      // Parse through the <asd> tags
      $pattern = "/<asd\s+(.*?)\s*\/>/s";
      preg_match_all ($pattern, $pPARSEDATA, $alltags);
  
      // Loop through all the tags
      foreach ($alltags[1] as $tagstring) {
  
        // Escape any slashes, otherwise pattern match errors out.
        $tagstring_quoted = preg_quote ($tagstring, "/");
  
        // Create the tag pattern.
        $tagpattern = "/<asd\s+$tagstring_quoted\s*\/>/s";
  
        // Turn all spaces within quotes into %20's.
        preg_match_all ("/='(.*?)'/", $tagstring, $spacematch);
        foreach ($spacematch[1] as $spacevalue) {
          $spacevalue_quoted = preg_quote ($spacevalue, "/");
          $spacevalue_encoded = preg_replace ("/\s/", "%20", $spacevalue);
          $space_pattern = "/='$spacevalue_quoted'/";
          $space_replace = "='$spacevalue_encoded'";
          $tagstring = preg_replace ($space_pattern, $space_replace, $tagstring);
        } // foreach
  
        // Split the values into pairs.
        $tagpairs = preg_split ("/ +/", $tagstring);
  
        foreach ($tagpairs as $tagkey => $tagvalue) {
          // Split the pairs into associative array values
          $tagdata = explode ('=', $tagvalue);
          $tagname = $tagdata[0]; 
          $value = $tagdata[1];
          $value = preg_replace ("/^'/", "", $value);
          $value = preg_replace ("/'$/", "", $value);
          $tagarray[$tagname] = $value;
        } // foreach
  
        if (!isset ($tagarray['id'])) $tagarray['id'] = null;
        
        switch ($tagarray['id']) {
  
         case "thumb":
           $imagehint = strtoupper ($tagarray['hint']);
  
           $IMAGEDATA = new cDATACLASS ();
  
           $query = "select Filename, Width, Height, photoSets_tID, " .
                    "userAuthorization.Username, photoSets.Directory " .
                    "from photoSets, photoInformation, userAuthorization, " .
                    "userProfile where photoSets.tID = " .
                    "photoInformation.photoSets_tID and " .
                    "userProfile.userAuth_uID = photoInformation.userAuth_uID " .
                    "and userAuthorization.uID = userProfile.userAuth_uID " .
                    "and photoInformation.Hint='$imagehint'"; 
  
           $IMAGEDATA->Query ($query);
           $IMAGEDATA->FetchArray ();
  
           $filename = $IMAGEDATA->Filename;
           $width = $IMAGEDATA->ThumbWidth;
           $height = $IMAGEDATA->ThumbHeight;
           $owner = $IMAGEDATA->Username;
           $photoset = $IMAGEDATA->Directory;
  
           $popwidth = $IMAGEDATA->Width + 60;
           $popheight = $IMAGEDATA->Height + 85;
  
           unset ($IMAGEDATA);
  
           // Determine the photo location.
           $thumblocation = "photos/" . $owner . "/sets/" . $photoset . "/_th." . $filename;
           $targetlocation = "/profile/" . $owner . "/photos/" . $photoset . "/" . $filename;
           
           global $zHTML;
  
           // Check if file exists.
           if (!file_exists ($thumblocation) ) 
             $fval = "(unknown image)";
           else
             $fval = $zHTML->CreatePopup ($targetlocation, "", $popwidth, $popheight, "", $thumblocation, $width, $height);
  
           // Parse the tag out.
           $pPARSEDATA = preg_replace ($tagpattern, $fval, $pPARSEDATA);
         break;
  
         case "image":
           if ($tagarray['hint']) {
             $imagehint = strtoupper ($tagarray['hint']);
  
             $IMAGEDATA = new cDATACLASS ();
  
             $query = "select Filename, Width, Height, photoSets_tID, " .
                      "userAuthorization.Username, photoSets.Directory " .
                      "from photoSets, photoInformation, userAuthorization, " .
                      "userProfile where photoSets.tID = " .
                      "photoInformation.photoSets_tID and " .
                      "userProfile.userAuth_uID = photoInformation.userAuth_uID " .
                      "and userAuthorization.uID = userProfile.userAuth_uID " .
                      "and photoInformation.Hint='$imagehint'"; 
  
             $IMAGEDATA->Query ($query);
             $IMAGEDATA->FetchArray ();
  
             $tagarray['filename'] = $IMAGEDATA->Filename;
             if (!$tagarray['width']) $tagarray['width'] = $IMAGEDATA->Width;
             if (!$tagarray['height']) $tagarray['height'] = $IMAGEDATA->Height;
             $tagarray['owner'] = $IMAGEDATA->Username;
             $tagarray['photoset'] = $IMAGEDATA->Directory;
  
             unset ($IMAGEDATA);
           } // if
  
             // Check if width and height are set, 
             if ($tagarray['width']) $width = "width='$tagarray[width]'";
             if ($tagarray['height']) $height = "height='$tagarray[height]'";
  
             // Determine the photo location.
             $photolocation = "photos/" . $tagarray['owner'] . "/sets/" . $tagarray['photoset'] . "/" . $tagarray['filename'];
  
           // Check if file exists.
           if (!file_exists ($photolocation) ) 
             $fval = "(unknown image)";
           else
             $fval = "<img src='$photolocation' $width $height border='0' />";
  
           // Parse the tag out.
           $pPARSEDATA = preg_replace ($tagpattern, $fval, $pPARSEDATA);
         break;
  
         case 'global':
          // Parse out the global variable.
          $pval = "g" . strtoupper ($tagarray['name']);
          global $$pval;
          if (!isset ($$pval)) {
            $fval = '(unknown global)';
          } else {
            // Modify the case of the string.
            if (!isset ($tagarray['case'])) $tagarray['case'] = null;
        
            switch (strtoupper ($tagarray['case']) ) {
              case "UPPER":
                $fval = strtoupper ($$pval);
              break;
  
              case "LOWER":
                $fval = strtolower ($$pval);
              break;
  
              case "PROPER":
                $fval = ucwords (strtolower ($$pval));
              break;
  
              default:
               $fval = $$pval;
              break;
            } // if
          } // if
          $pPARSEDATA = preg_replace ($tagpattern, $fval, $pPARSEDATA);
          break;
  
         case 'string':
          // Parse out the system string.
          $OBJSTRING = new cSYSTEMSTRINGS;

          if (isset ($tagarray['title'])) $title = strtoupper ($tagarray['title']);
          if (isset ($tagarray['context'])) $context = strtoupper ($tagarray['context']);
          
          // Unless specified, set to the specific context.
          if (!isset ($context)) $context = $this->Context;

          $OBJSTRING->Lookup ($title, $context);
          $output = $OBJSTRING->Output;
          if (!$output) $output = '(unknown string)';
          $output = $this->ParseTags ($output);
  
          // Modify the case of the string.
          if (!isset ($tagarray['case'])) $tagarray['case'] = null;
        
          switch (strtoupper ($tagarray['case']) ) {
            case "UPPER":
              $output = strtoupper ($output);
            break;
  
            case "LOWER":
              $output = strtolower ($output);
            break;
  
            case "PROPER":
              $output = ucwords (strtolower ($output));
            break;
          } // if
          
          $pPARSEDATA = preg_replace ($tagpattern, $output, $pPARSEDATA);
          unset ($OBJSTRING);
          break;
  
         case 'tooltip':
          global $zTOOLTIPS;

          if (!isset ($tagarray['title'])) $tagarray['title'] = NULL;
          if (!isset ($tagarray['context'])) $tagarray['context'] = NULL;

          $title = strtoupper ($tagarray['title']);
          $context = strtoupper ($tagarray['context']);

          $output = $zTOOLTIPS->CreateDisplay ($title, $context);
          $pPARSEDATA = preg_replace ($tagpattern, $output, $pPARSEDATA);
         break;

         case 'link':
          // Parse out the post link.
          global $zHTML;
          if (isset ($tagarray['target'])) $target = $tagarray['target']; else $target = NULL;
          if (isset ($tagarray['text'])) $text = $tagarray['text']; else $text = NULL;
          if (isset ($tagarray['image'])) $image = $tagarray['image']; else $image = NULL;
          if (isset ($tagarray['width'])) $width = $tagarray['width']; else $width = NULL;
          if (isset ($tagarray['height'])) $height = $tagarray['height']; else $height = NULL;
          if (isset ($tagarray['style'])) $style = $tagarray['style']; else $style = NULL;
          if (isset ($tagarray['confirm'])) $confirm = $tagarray['confirm']; else $confirm = NULL;
          $extra = 'g' . $tagarray['extra'];
          global $$extra;
          $output = $zHTML->CreateLink ($target, $text, $$extra, $style, $image, $width, $height, $confirm);
          $pPARSEDATA = preg_replace ($tagpattern, $output, $pPARSEDATA);
          $pPARSEDATA = preg_replace ("/%20/", " ", $pPARSEDATA);
         break;
  
         case "post":
           // Parse out the post data.
           global $gPOSTDATA;
           $HTML = new cHTML;
  
           // Check if gPOSTDATA is empty and supress error.
           if (!isset($gPOSTDATA)) $gPOSTDATA = Array ();
           $output = $HTML->PostData ($gPOSTDATA);
           $pPARSEDATA = preg_replace ($tagpattern, $output, $pPARSEDATA);
         break;
  
         // Tags which do not specify an 'id'.
         default:
  
           // User homepage link tag.
           if (isset($tagarray['user'])) {

             if (strstr ($tagarray['user'], '@')) {
               list ($tagarray['user'], $tagarray['domain']) = explode ('@', $tagarray['user']);
             } // if

             global $gSITEDOMAIN;

             $popup = TRUE;
             if (strtoupper($tagarray['popup']) == 'OFF') $popup = FALSE;

             if (!isset ($tagarray['domain'])) $tagarray['domain'] = $gSITEDOMAIN;
        
             if ($tagarray['domain'] != $gSITEDOMAIN) {
               global $zHTML;
               //NOTE: Remote check if user exists.
               $output = $zHTML->CreateUserLink ($tagarray['user'], $tagarray['domain'], $popup);
             } else {
               if ($tagarray['user'] == ANONYMOUS) {
                 global $zHTML;
                 $output = $zHTML->CreateUserLink (ANONYMOUS, $gSITEDOMAIN, $popup);
               } else {
                 //Check for an unknown user.
                 $CHECKUSER = new cUSER ();
                 $CHECKUSER->Select ("Username", $tagarray['user']);
                 $CHECKUSER->FetchArray();
                 if ($CHECKUSER->CountResult () == 0) {
                   $output = "(unknown user)";
                 } else {
                   global $zHTML;
                   $output = $zHTML->CreateUserLink ($tagarray['user'], $gSITEDOMAIN, $popup);
                 } // if
                 unset ($CHECKUSER);
               } // if
             } // if

             $pPARSEDATA = preg_replace ($tagpattern, $output, $pPARSEDATA);

           } // if
  
           // Group link tag.
           if (isset ($tagarray['group'])) {

             if (strstr ($tagarray['group'], '@')) {
               list ($tagarray['group'], $tagarray['domain']) = explode ('@', $tagarray['group']);
             } // if

             global $gSITEDOMAIN;

             if ($tagarray['domain'] != $gSITEDOMAIN) {
               global $zHTML;
               //NOTE: Remote check if group exists.
               $output = $zHTML->CreateGroupLink ($tagarray['group'], $tagarray['domain']);
             } else {
               //Check for an unknown group.
               $CHECKGROUP = new cGROUPINFORMATION ();
               $CHECKGROUP->Select ("Name", $tagarray['group']);
               $CHECKGROUP->FetchArray();
               if ($CHECKGROUP->CountResult () == 0) {
                 $output = "(unknown group)";
               } else {
                 global $zHTML;
                 $output = $zHTML->CreateGroupLink ($tagarray['group'], $gSITEDOMAIN);
               } // if
               unset ($CHECKGROUP);
             } // if

             $pPARSEDATA = preg_replace ($tagpattern, $output, $pPARSEDATA);

           } // if
  
         break;
        } // switch
        unset ($tagarray);
      } // foreach
      
      return ($pPARSEDATA);
  
    } // ParseTags
  
    // Overwrite inhereted ::ParseTags function.
    // Strip all ASD and %% tags
    function RemoveTags ($pPARSEDATA) {
  
      // Strip the %% tags
      $pattern = "/%(.*?)%/si";
      $pPARSEDATA = preg_replace ($pattern, "", $pPARSEDATA);
  
      // Strip the <asd> tags
      $pattern = "/<asd\s+(.*?)\s*\/>/s";
      $pPARSEDATA = preg_replace ($pattern, "", $pPARSEDATA);
  
      return ($pPARSEDATA);
  
    } // RemoveTags

    function BufferUserIcon ($pUSERNAME, $pDOMAIN, $pSPECIFIC = NULL) {
      global $gTHEMELOCATION;
      global $gSITEDOMAIN;
      global $zAUTHUSER;

      if ($pUSERNAME == ANONYMOUS) {
        $output = $this->IncludeFile ("$gTHEMELOCATION/objects/icons/iconanonymous.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
      } elseif ($pSPECIFIC == NO_ICON) { 
        $output = $this->IncludeFile ("$gTHEMELOCATION/objects/icons/noicon.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
      } else {
        global $gICONSOURCE, $gICONTARGET;
        if (($zAUTHUSER->Domain) and ($pDOMAIN != $zAUTHUSER->Domain)) {
          if ($pDOMAIN != $gSITEDOMAIN) {
            // Redirect to home domain for remote authentication.
            $target = $pDOMAIN;
            $location = "/profile/" . $pUSERNAME . "/";
            $gICONTARGET = "http://" . $zAUTHUSER->Domain . "/login/bounce/?target=" . $target . "&location=" . $location;
          } else {
            $gICONTARGET = "http://" . $pDOMAIN . "/profile/" . $pUSERNAME . "/";
          } // if
        } else {
          $gICONTARGET = "http://" . $pDOMAIN . "/profile/" . $pUSERNAME . "/";
        } // if

        // Check if a specific Icon is requested.
        if ($pSPECIFIC) {

          // Use the specified image through a direct link.
          $gICONSOURCE = "http://" . $pDOMAIN . "/photos/" . $pUSERNAME . "/icons/" . $pSPECIFIC;
          $filename = "photos/$pUSERNAME/icons/$pSPECIFIC";

          // Check if the file exists, if not, use the default.
          if ( ($pDOMAIN == $gSITEDOMAIN) and (!file_exists($filename)) ) $gICONSOURCE = "http://" . $pDOMAIN . "/icon/" . $pUSERNAME . "/";


        } else {
          // Point to the generic "default icon" url.
          $gICONSOURCE = "http://" . $pDOMAIN . "/icon/" . $pUSERNAME . "/";
        } // if

        if ($pDOMAIN != $gSITEDOMAIN) {
          $output = $this->IncludeFile ("$gTHEMELOCATION/objects/icons/iconremote.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
        } else {
          $output = $this->IncludeFile ("$gTHEMELOCATION/objects/icons/iconlocal.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
        } // if
      } // if

      return ($output);
    } // BufferUserIcon;

    function ListDirectories ($pLOCATION) {
      $handle = opendir ($pLOCATION);
      $directories = array ();

      while (false !== ($file = readdir ($handle))) {
        if (is_dir($pLOCATION . $file)) {
          if ($file == '.') continue;
          if ($file == '..') continue;
          $directories[$file] = $file;
        } // if
      } // while

      if (sizeof($directories) === 0) return (FALSE);

      return ($directories);
    } // ListDirectories

    // Get a list of directories in the themes directory.
    function GetThemeList () {

      $return = $this->ListDirectories ("themes/");
      
      // Remove all hidden directories.
      foreach ($return as $count => $theme) {
        if (substr ($theme, 0, 1) == '.') {
          unset ($return[$count]);
        } // if
      } // foreach
      
      return ($return);

    } // GetThemeList
    
    // Get a list of installed language packs.
    function GetLanguageList () {
      
      global $zSTRINGS;
      
      $query = "
        SELECT DISTINCT(Language) AS Language
        FROM   $zSTRINGS->TableName
      ";
      
      $zSTRINGS->Query ($query);
      
      $return = array ();
      while ($zSTRINGS->FetchArray()) {
        $return[$zSTRINGS->Language] = $zSTRINGS->Language;
      } // while
      
      return ($return);
    } // GetLanguageList
    
    function LoadConfiguration () {
      
      global $gTABLEPREFIX;
      
      $return = array();
      
      $zCONFIG = new cSYSTEMCONFIG();
      
      $zCONFIG->Select (NULL, NULL);
      
      while ($zCONFIG->FetchArray()) {
        $return[$zCONFIG->Concern] = $zCONFIG->Value;
      } // while
      
      unset ($zCONFIG);
      
      return ($return);
    } // LoadConfiguration
  
    function GetUserInformation ($pUSERNAME, $pDOMAIN) {

      $FRIEND = new cFRIENDINFORMATION ();
      $FRIEND->Username = $pUSERNAME;
      $FRIEND->Domain = $pDOMAIN;
      $returnarray = $FRIEND->GetUserInformation();
      unset ($FRIEND);

      return ($returnarray);
    } // GetUserInformation
    
    function Shutdown ($pSHUTDOWN) {
    	global $zLOCALUSER;
    	
    	global $gFRAMELOCATION;
    	
      	// If we're viewing the admin area, shutdown doesn't apply.
      	$self = $_SERVER['REQUEST_URI'];
      	if (strstr ($self, '_admin')) return (FALSE);
      	if (strstr ($self, 'login')) return (FALSE);
    	
    	switch ($pSHUTDOWN) {
    		case ON:
  				$this->IncludeFile ("$gFRAMELOCATION/frames/common/shutdown.afrw", INCLUDE_SECURITY_NONE);
    			exit;
    		break;
    		case ADMIN_ONLY:
           		if ($zLOCALUSER->userAccess->a == TRUE) return (FALSE);
  				$this->IncludeFile ("$gFRAMELOCATION/frames/common/shutdown.admin.afrw", INCLUDE_SECURITY_NONE);
    			exit;
    		break;
    		case OFF:
    			return (FALSE);
    		break;
    	} // switch
    } // Shutdown
    
    // Set the current Shutdown status.
    function SetShutdown ($pSHUTDOWN) {
      global $gTABLEPREFIX;
      
      $CONFIG  = new cSYSTEMCONFIG ();
      
  	  switch ($pSHUTDOWN) {
  	  	case ON:
  	  	case OFF:
  	  	case ADMIN_ONLY:
  	  		$query = "
				UPDATE $CONFIG->TableName SET Value = '$pSHUTDOWN' WHERE Concern = 'Shutdown';
            ";
            $CONFIG->Query ($query);
  	  	break;
      } // switch
      
      unset ($CONFIG);
      
      return (TRUE);
    } // SetShutdown
    
    // Get the current Shutdown status.
    function GetShutdown () {
      $CONFIG  = new cSYSTEMCONFIG ();
      
      $CONFIG->Select ('Concern', 'Shutdown');
      $CONFIG->FetchArray();
      
      $return = $CONFIG->Value;
      
      unset ($CONFIG);
      
      return ($return);
    } // GetShutdown

  } // cAPPLESEED
