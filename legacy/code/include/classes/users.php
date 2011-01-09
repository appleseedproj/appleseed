<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: users.php                               CREATED: 04-04-2005 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 04-19-2008 +
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
  // | VERSION:      0.7.9
  // | DESCRIPTION.  User class definitions.                             |
  // +-------------------------------------------------------------------+

  require_once ("legacy/code/include/classes/BASE/user.php");
 
  // User class.
  class cOLDUSER extends cOLDUSERAUTHORIZATION {

    var $uID, $Username, $Pass, $Invite, $Verification, $Standing;
    var $userSession, $userProfile, $userAccess, $userAnswers;
    var $userInformation;
    var $Cascade;

    function cOLDUSER ($pDEFAULTCONTEXT = '') {
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
      $this->Cascade = array ('userSession', 'userAccess', 'userProfile', 
                              'userInformation', 'userIcons', 'userInvites',
                              'userSettings', 'userGroups');
 
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
      $this->userSession      = new cUSERSESSIONS ($pDEFAULTCONTEXT);
      $this->userProfile      = new cUSERPROFILE ($pDEFAULTCONTEXT);
      $this->userAccess       = new cUSERACCESS ($pDEFAULTCONTEXT);
      $this->userSettings     = new cUSERSETTINGS ($pDEFAULTCONTEXT);
      $this->userAnswers      = new cUSERANSWERS ($pDEFAULTCONTEXT);
      $this->userInformation  = new cUSERINFORMATION ($pDEFAULTCONTEXT);
      $this->userInvites      = new cUSERINVITES ($pDEFAULTCONTEXT);
      $this->userIcons        = new cUSERICONS ($pDEFAULTCONTEXT);
      $this->userGroups       = new cUSERGROUPS ($pDEFAULTCONTEXT);

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();

    } // Constructor

    // Initialize a new user.
    function Initialize ($pCREATERELATIONSHIP = TRUE) {

      global $zOLDAPPLE;
      global $gVALUE;
      global $gSITEDOMAIN;

      // Create the default user icon.
      $this->userIcons->userAuth_uID = $this->uID;
      $this->userIcons->Filename = NO_ICON;
      $this->userIcons->Keyword = "(no icon)";
      $this->userIcons->Comments = "";
      $this->userIcons->Add();

      // Create the photosets directory.
      $photosetdir = "_storage/legacy/photos/" . $this->Username . "/sets/";
      $zOLDAPPLE->CreateDirectory ($photosetdir);

      // Create the icons directory.
      $icondir = "_storage/legacy/photos/" . $this->Username . "/icons/";
      $zOLDAPPLE->CreateDirectory ($icondir);

      if ($pCREATERELATIONSHIP) {
        // Create the default friend relationship.
      	$INVITE = new cUSERINVITES ();
      	$INVITE->Select ("Value", $gVALUE);
      	$INVITE->FetchArray();
	 
      	$USER = new cOLDUSER();
      	$USER->Select ("uID", $INVITE->userAuth_uID);
      	$USER->FetchArray();

      	$FRIEND = new cFRIENDINFORMATION();
      	$FRIEND->Request ($this->Username, $gSITEDOMAIN, $USER->Username, $gSITEDOMAIN, FALSE);
      	$FRIEND->Approve ($this->Username, $gSITEDOMAIN, $USER->Username, $gSITEDOMAIN, FALSE);
      } // if

      unset ($INVITE);
      unset ($USER);
      unset ($FRIEND);

      return (TRUE);
    } // Initialize

    // Save user's Profile Questions & Answers.
    function SaveQuestions () {

      global $gANSWERS;

      // Load the configurable questions
      $QUESTIONSLIST = new cUSERQUESTIONS ();

      $criterialist = array ("Language" => "en",
                             "Visible"  => TRUE );   
      $QUESTIONSLIST->SelectByMultiple ($criterialist);

      // Set the context for error messages.
      $this->userAnswers->PageContext = 'USER.OPTIONS';

      $QuestionCount = 0;
      while ($QUESTIONSLIST->FetchArray ()) {
        // Select the user question.
        $questionid = $QUESTIONSLIST->tID;

        // Query for question id + user id.
        $answercriteria = array ("userAuth_uID"      => $this->uID,
                                 "userQuestions_tID" => $questionid); 
        $this->userAnswers->SelectByMultiple ($answercriteria);
        $this->userAnswers->FetchArray ();

          // Assign the new answer
        $this->userAnswers->userAuth_uID = $this->uID;
        $this->userAnswers->userQuestions_tID = $questionid;
        if ($QUESTIONSLIST->TypeOf == QUESTION_CHECKLIST) {
          $labelname = 'g' . $QUESTIONSLIST->Concern;
          $pusharray = array ();

          if ($$labelname) {
            foreach ($$labelname as $key => $value) {
              $pusharray[$key] = $value;
            } // foreach
          } // if
          $QuestionCount--;
          $this->userAnswers->Answer = join (",", $pusharray);
        } else {
          $this->userAnswers->Answer = $gANSWERS[$QuestionCount];
        } // if

        // Count the results
        $exists = $this->userAnswers->CountResult ();

        // If data exists, update it.  Otherwise, add it.
        if ($exists) {
          $this->userAnswers->Update();
        } else {
          $this->userAnswers->Add();
        } // if
        $gDEBUG['echostatement'] = FALSE;

        $QuestionCount++;
      } // while

      // Push userAnswers message to front.
      if ( ($this->userAnswers->Error) AND (!$this->Error) ) {
        $this->Error = $this->userAnswers->Error;
        $this->Message = $this->userAnswers->Message;
      } // if

      // Load the success message.
      if (!$this->Error) {
        $this->Message = __("Record Updated");
      } // 

      return (TRUE);
    } // SaveQuestions

    function SaveGeneral () {

      global $zHTML;

      global $gUSERNAME, $gDESCRIPTION, $gGENDER, $gEMAIL;
      global $gZIPCODE, $gFULLNAME, $gALIAS;

      // Load the saved message.
      $this->Message = __("Record Updated");

      $oldphotodir = "_storage/legacy/photos/" . $this->Username;
      $newphotodir = "_storage/legacy/photos/" . $gUSERNAME;

      // Start transactions.
      $this->Begin ();

      // Push data from POST to FOCUSUSER.
      $this->userProfile->Description = $gDESCRIPTION;
      $this->userProfile->Birthday = $zHTML->JoinDate ("BIRTHDAY");
      $this->userProfile->Gender = $gGENDER;
      $this->userProfile->Email = $gEMAIL;
      $this->userProfile->Zipcode = $gZIPCODE;
      $this->userProfile->Fullname = $gFULLNAME;
      $this->userProfile->Alias = $gALIAS;

      $saveusername = $this->Username;
      $this->Username = SQL_SKIP;

      // Set the message string context for this page.
      $this->userProfile->PageContext = "USER.OPTIONS";

      // Run a quick sanity check.
      $this->userProfile->Sanity ();

      // Update all data if no errors found.
      if (!$this->userProfile->Error) {

        // Update profile information.
        $this->userProfile->Update ();

        // Determine whether to commit or rollback.
        if (!$this->Error) {

          // Commit changes.
          $this->Commit ();

          // Rename the user's photo directory.
          rename ($oldphotodir, $newphotodir);
        } else {
          $this->Rollback ();
        } // if

      } // if

      // Push userProfile message to front.
      if ( ($this->userProfile->Error) AND (!$this->Error) ) {
        $this->Error = $this->userProfile->Error;
        $this->Message = $this->userProfile->Message;
      } // if

      $this->Username = $saveusername;

      // NOTE:  When changing username, must rename photos directory.

      return (TRUE);
    } // SaveGeneral

    function SaveIcons () {

      global $zICON, $zOLDAPPLE;

      global $gKEYWORD, $gCOMMENTS;
      global $gUSERICONX, $gUSERICONY;

      // NOTE: Do I still need this?
      global $gBROADCASTUNIQUE;

      $uploadfile = $_FILES['gUSERICON']['tmp_name'];
      $uploaderror = $_FILES['gUSERICON']['error'];

      // Check if a file was uploaded.
      if ($uploadfile) {
        // Validate the uploaded file.
        $zICON->Validate ($uploadfile, $uploaderror, $gUSERICONX, $gUSERICONY);

        $location = "_storage/legacy/photos/" . $this->Username . "/icons/";

        // If icon directory doesn't exist, create it.
        if (!is_dir ($location)) $zOLDAPPLE->CreateDirectory ($location);

        $iconfile = $location . $_FILES['gUSERICON']['name'];

        // Retrieve the image attributes.
        $zICON->Attributes ($_FILES['gUSERICON']['tmp_name']);

        // Convert and save the file.
        if ($zICON->Error != -1) {
          // Set values.
          $this->userIcons->Filename = $_FILES['gUSERICON']['name'];
          $this->userIcons->Keyword = $zOLDAPPLE->RemoveExtension ($_FILES['gUSERICON']['name']);
          $this->userIcons->Comments = '';
              
          // Check if image with filename already exists.
          $ICONCHECK = new cUSERICONS;
          $iconcriteria = array ("userAuth_uID" => $this->uID,
                                 "Filename"     => $this->userIcons->Filename);
          $ICONCHECK->SelectByMultiple ($iconcriteria);

          // Reset error if we're not constraining based on size.
          // NOTE: Make this an admin option.
          $zICON->Errorlist['image'] = NULL;

          if ($ICONCHECK->CountResult () == 0) {
            $zICON->Convert ($_FILES['gUSERICON']['tmp_name']);

            global $gUSERICONX, $gUSERICONY;
            $zICON->Resize ($gUSERICONX, $gUSERICONY);
            $zICON->Error = 0;

            $zICON->Save ($iconfile, $zICON->Type);
            if ($zICON->Error != -1) {
              $zICON->Message = __("File uploaded");

              // Add the reference to the database.
              $this->userIcons->Add ();

              // Remove the NO_ICON default if it exists.
  
              $defaultcriteria = array ("userAuth_uID" => $this->uID,
                                        "Filename" => NO_ICON);
              $this->userIcons->SelectByMultiple ($defaultcriteria);
              $this->userIcons->FetchArray ();
  
              // If we find a NO_ICON, delete it.
              if ($this->userIcons->CountResult () > 0) {
                $this->userIcons->Delete();
              } // if

            } // if
            // Destroy the image resource.
            $zICON->Destroy ();

            unset ($ICONCHECK);
          } else {
            global $gICONFILENAME;  $gICONFILENAME = $_FILES['gUSERICON']['name'];
            $zICON->Error = -1;
            $zICON->Message = __("File Exists", array ( "filename" => $gICONFILENAME ) );
            unset ($gICONFILENAME);
          } // if

          // Delete the temporary file.
          unlink ($_FILES['gUSERICON']['tmp_name']);
            
        } else {
          $zICON->Message = __("Upload Error");
        } // if
      } // if

      foreach ($gKEYWORD as $TID => $keyword) {
        // Synchronize the form data.
        $this->userIcons->tID = $TID;
        $this->userIcons->Keyword = $gKEYWORD[$TID];
        $this->userIcons->Comments = $gCOMMENTS[$TID];
        $this->userIcons->userAuth_uID = $this->uID;
        $this->userIcons->PageContext = "USER.OPTIONS";

        // Skip the filename, don't change it.
        $this->userIcons->Filename = SQL_SKIP;

        // Sanity check all post variables.
        $this->userIcons->Sanity();

        if ($this->userIcons->Error == -1) {
          $gBROADCASTUNIQUE = $this->userIcons->tID;

          // Load the page error.
          $this->userIcons->Message = __("Unable To Save Icon Preferences");

          // Push userIcons message to front.
          if ( ($this->userIcons->Error) AND (!$this->Error) ) {
            $this->Error = $this->userIcons->Error;
            $this->Message = $this->userIcons->Message;
          } // if
        } else {
          $this->userIcons->Update();
          $this->Message = __("Record Updated");
        } // if
      } // foreach

      return (TRUE);
    } // SaveIcons

    function SavePhoto () {

      global $gUSERICONX, $gUSERICONY;

      global $zPHOTO, $zICON, $zOLDAPPLE;

      // User photos directory.
      $location = "_storage/legacy/photos/" . $this->Username . '/';

      // If photos directory doesn't exist, create it.
      if (!is_dir ($location)) $zOLDAPPLE->CreateDirectory ($location);

      $uploadfile = $_FILES['gPROFILEPHOTO']['tmp_name'];

      // Return out if no profile photo was uploaded (for SAVE_ALL).
      if (!$uploadfile) return (FALSE);

      $uploaderror = $_FILES['gPROFILEPHOTO']['error'];
      // Validate the uploaded file.
      $zPHOTO->Validate ($uploadfile, $uploaderror);

      // Convert and save the file.
      if ($zPHOTO->Error != -1) {
        $profilepicture = "_storage/legacy/photos/" . $this->Username . "/profile.jpg";
        $zPHOTO->Convert ($_FILES['gPROFILEPHOTO']['tmp_name']);
        $zPHOTO->Attributes ($_FILES['gPROFILEPHOTO']['tmp_name']);
        global $gPROFILEPHOTOX, $gPROFILEPHOTOY;
        // NOTE: Make the 'resize' an option for sites who don't care
        // about bandwidth and would like wildly different themes.
        $zPHOTO->Resize ($gPROFILEPHOTOX, $gPROFILEPHOTOY, TRUE, TRUE, FALSE);
        $zPHOTO->Save ($profilepicture, IMAGETYPE_JPEG);

        $zPHOTO->Message = __("File uploaded");

        // Check if no default icon exists.
        $defaultcriteria = array ("userAuth_uID" => $this->uID,
                                  "Filename" => NO_ICON);
        $this->userIcons->SelectByMultiple ($defaultcriteria);
        $this->userIcons->FetchArray ();

        // If we find a NO_ICON, delete it.
        if ($this->userIcons->CountResult () > 0) {
          $this->userIcons->Delete();

          $this->userIcons->Filename = $_FILES['gPROFILEPHOTO']['name'];
          $this->userIcons->Keyword = $zOLDAPPLE->RemoveExtension ($_FILES['gPROFILEPHOTO']['name']);
          $this->userIcons->Comments = '';

          // Add the reference to the database.
          $this->userIcons->Add ();

          // Create a default icon from your profile photo.
          $location = "_storage/legacy/photos/" . $this->Username . "/icons/";

          // If icon directory doesn't exist, create it.
          if (!is_dir ($location)) $zOLDAPPLE->CreateDirectory ($location);

          $iconfile = $location . $_FILES['gPROFILEPHOTO']['name'];
          $zICON->Convert ($_FILES['gPROFILEPHOTO']['tmp_name']);
          $zICON->Attributes ($_FILES['gPROFILEPHOTO']['tmp_name']);
          $zICON->Resize ($gUSERICONX, $gUSERICONY);
          $zICON->Save ($iconfile, IMAGETYPE_JPEG);
        } // if

        // Destroy the image resource.
        $zPHOTO->Destroy ();

        // Delete the temporary file.
        unlink ($_FILES['gPROFILEPHOTO']['tmp_name']);

      } else {
        $zPHOTO->Message = __("Upload Error");
      } // if
      
      return (TRUE);
    } // SavePhoto

    function SaveConfig () {

      global $gUSERTHEME, $gDEFAULTTHEME;
      global $gTHEMELOCATION;
      global $gUSERTABSLOCATION, $gUSERTABS;
      $gDEFAULTTHEME = 'default';

      $this->userSettings->Save ("DefaultTheme", $gDEFAULTTHEME);

      global $gUSERTHEME;
      $gUSERTHEME = 'default';
      $gTHEMELOCATION = "legacy/themes/$gUSERTHEME/";
      $gUSERTABSLOCATION = $gTHEMELOCATION . $gUSERTABS;

      if (!$this->userSettings->Error) {
        $this->Message = __("Record Updated");
        $this->Error = 0;
      } else {
        $this->Error = -1;
        $this->Message = __("Unable To Save Journal Preferences");
      } // if

      return (TRUE);
    } // SaveConfig

    function SaveEmails () {

      global $gJOURNAL, $gPROFILE, $gREPLY;

      $this->userSettings->Save ("JournalNotification", $gJOURNAL);
      $this->userSettings->Save ("ProfileNotification", $gPROFILE);
      $this->userSettings->Save ("ReplyNotification", $gREPLY);

      if ($this->userSettings->Error == -1) {
        $this->Error = -1;
        $this->Message = __("Unable To Save Email Preferences");
      } else {
        $this->Error = 0;
        $this->Message = __("Record Updated");
      } // if

      return (TRUE);
    } // SaveEmails
    
    function ChangePassword () {
    	global $zApp;
    	
    	global $gTABLEPREFIX;
    	
    	global $gPASSWORD, $gCONFIRMPASSWORD;
    	
    	if ( !$gPASSWORD ) {
    		return ( FALSE );
    	}
    	
        $userAuth = $gTABLEPREFIX . 'userAuthorization';
      
        $salt = substr(md5(uniqid(rand(), true)), 0, 16);
        $sha512 = hash ("sha512", $salt . $gPASSWORD);
        $newpass = $salt . $sha512;

        $reset_query = "UPDATE %s SET " .
                       "Pass = '%s' WHERE " .
                       "uID = '%s'";
                     
        $reset_query = sprintf ($reset_query, $userAuth, $newpass, $this->uID);
      
        $this->Query ($reset_query);

		$gPASSWORD = null;
		$gCONFIRMPASSWORD = null;
		
    	return ( TRUE );
    }

    function DeleteIcon () {
      global $gSECTION, $gOPTIONGENERAL, $gSECTIONDEFAULT;

      global $zICON;

      // Set the section to 'icons'.
      $gSECTION = "ICONS";
      $on = "gOPTION" . $gSECTION;
      global $$on;
      $$on = 'on';
      $gOPTIONGENERAL = 'off';
      $gSECTIONDEFAULT = $gSECTION;
          
      // NOTE:  Check if user owns this icon.

      // Do not delete if only one icon is left.
      $this->userIcons->Select ("userAuth_uID", $this->uID);

      if ($this->userIcons->CountResult () == 1) {
        $zICON->Message = __("At Least One Icon Required");
        $zICON->Error = -1;;
      } else {
        // Remove icon from database.
        $this->userIcons->Synchronize();
        $this->userIcons->Select ("tID", $this->userIcons->tID);
        $this->userIcons->FetchArray();

        global $gICONFILENAME;
        $gICONFILENAME = $this->userIcons->Filename;
        
        // Delete the image file.
        $filename = "_storage/legacy/photos/" . $this->Username . 
                    "/icons/"  . $this->userIcons->Filename;

        if ($this->userIcons->Filename != NO_ICON) {
          $this->userIcons->Delete();
        } // if

        if (!unlink ($filename)) {
          $zICON->Message = __("Could Not Delete Icon", array ( "filename" => $gICONFILENAME ) );
          $zICON->Error = -1;;
        } else {
          $zICON->Message = __("Record Deleted");
        } // if

        // Update all references to this icon, replace with newest icon.

        // Find the latest icon.
        $USERICON = new cUSERICONS (); 
        $USERICON->Select ("userAuth_uID", $this->uID, "tID DESC");
        $USERICON->FetchArray ();

        $this->userIcons->Replace ($USERICON->Filename); 

        unset ($gICONFILENAME);
        unset ($USERICON);
      } // if

      return (TRUE);
    } // DeleteIcon

  } // cOLDUSER

  // User profile class.
  class cUSERPROFILE extends cDATACLASS {
 
    var $userAuth_uID, $Email, $Fullname, $Description, $Gender;
    var $Zipcode, $Birthday;
 
    function cUSERPROFILE ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'userProfile';
      $this->userAuth_uID = '';
      $this->Email = '';
      $this->Fullname = '';
      $this->Alias = '';
      $this->Description = '';
      $this->Gender = '';
      $this->Birthday = '';
      $this->Zipcode = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'userAuth_uID';
      $this->ForeignKey = 'userAuth_uID';
 
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

        'Email'          => array ('max'        => '128',
                                   'min'        => '6',
                                   'illegal'    => '',
                                   'required'   => '@ .',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'EMAIL'),

        'Fullname'       => array ('max'        => '32',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Alias'          => array ('max'        => '32',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Description'    => array ('max'        => '4096',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Zipcode'        => array ('max'        => '12',
                                   'min'        => '0',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + =',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),
 
        'Gender'         => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Birthday'       => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATE'),
      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;
 
      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor

    // Change a user's password and mail the result.
    function ChangePassword () {
      global $zOLDAPPLE;

      global $gPASSWORD, $gFULLNAME;
      global $gSITEDOMAIN;
      global $gTABLEPREFIX;

      $gPASSWORD = $zOLDAPPLE->GeneratePassword ('##XX#XX!');
      
      $userAuth = $gTABLEPREFIX . 'userAuthorization';
      
      $salt = substr(md5(uniqid(rand(), true)), 0, 16);
      $sha512 = hash ("sha512", $salt . $gPASSWORD);
      $newpass = $salt . $sha512;

      $reset_query = "UPDATE %s SET " .
                     "Pass = '%s' WHERE " .
                     "uID = '%s'";
                     
      $reset_query = sprintf ($reset_query, $userAuth, $newpass, $this->userAuth_uID);
      
      $this->Query ($reset_query);

      if ($this->Error) {
        return (FALSE);
      } // if

      $to = $this->Email;
      $gFULLNAME = $this->Fullname;

      $subject = __( "Password Reset Subject", array ( "domain" => $gSITEDOMAIN ) );

      $body = __("Password Reset Body", array ( "fullname" => $gFULLNAME, "password" => $gPASSWORD ) );

      $from = __("Password Reset From", array ( "domain" => $gSITEDOMAIN ) );

      $headers = "From: $from" . "\r\n" .
                 "Reply-To: $from" . "\r\n" .
                 "X-Mailer: PHP/" . phpversion();

      mail ($to, $subject, $body, $headers);

      unset ($to);
      unset ($subject);
      unset ($body);

      unset ($gPASSWORD);
      unset ($gFULLNAME);

      return (TRUE);

    } // ChangePassword

    // Function to return a display alias if it exists, fullname otherwise.
    function GetAlias () {
      if ($this->Alias) {
        return ($this->Alias);
      } else {
        return ($this->Fullname);
      } // if
    } // GetAlias

  } // cUSERPROFILE

  // User questions class.
  class cUSERQUESTIONS extends cDATACLASS {
 
    var $tID, $FullQuestion, $ShortQuestion, $TypeOf, $Concern, $Visibility;
 
    function cUSERQUESTIONS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'userQuestions';
      $this->tID = '';
      $this->FullQuestion = '';
      $this->ShortQuestion = '';
      $this->TypeOf = '';
      $this->Concern = '';
      $this->Visibility = '';
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
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'FullQuestion'   => array ('max'        => '255',
                                   'min'        => '4',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'ShortQuestion'  => array ('max'        => '64',
                                   'min'        => '4',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'TypeOf'         => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Concern'        => array ('max'        => '255',
                                   'min'        => '',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + =',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Language'       => array ('max'        => '4',
                                   'min'        => '2',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Visibility'     => array ('max'        => '4',
                                   'min'        => '2',
                                   'illegal'    => '',
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

  } // cUSERQUESTIONS

  // User profile answers class.
  class cUSERANSWERS extends cDATACLASS {

    var $tID, $userAuth_uID, $userQuestions_tID, $Answer;
 
    function cUSERANSWERS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'userAnswers';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->userQuestions_tID = '';
      $this->Answer = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'userAuth_uID';
 
      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;
 
      // Create extended field definitions
      $this->FieldDefinitions = array (
        'tID'                 => array ('max'        => '',
                                        'min'        => '',
                                        'illegal'    => '',
                                        'required'   => '',
                                        'relation'   => '',
                                        'null'       => NO,
                                        'sanitize'   => NO,
                                        'datatype'   => 'INTEGER'),

        'userAuth_uID'        => array ('max'        => '',
                                        'min'        => '',
                                        'illegal'    => '',
                                        'required'   => '',
                                        'relation'   => '',
                                        'null'       => NO,
                                        'sanitize'   => NO,
                                        'datatype'   => 'INTEGER'),

        'userQuestions_uID'   => array ('max'        => '',
                                        'min'        => '',
                                        'illegal'    => '',
                                        'required'   => '',
                                        'relation'   => '',
                                        'null'       => NO,
                                        'sanitize'   => NO,
                                        'datatype'   => 'INTEGER'),
     
        'Views'               => array ('max'        => '',
                                        'min'        => '',
                                        'illegal'    => '',
                                        'required'   => '',
                                        'relation'   => '',
                                        'null'       => YES,
                                        'sanitize'   => NO,
                                        'datatype'   => 'INTEGER'),

        'FirstLogin'          => array ('max'        => '',
                                        'min'        => '',
                                        'illegal'    => '',
                                        'required'   => '',
                                        'relation'   => '',
                                        'null'       => YES,
                                        'sanitize'   => NO,
                                        'datatype'   => 'DATETIME'),

        'LastLogin'           => array ('max'        => '',
                                        'min'        => '',
                                        'illegal'    => '',
                                        'required'   => '',
                                        'relation'   => '',
                                        'null'       => YES,
                                        'sanitize'   => NO,
                                        'datatype'   => 'DATETIME'),
      );
      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor

  } // cUSERANSWERS

  // User information class.
  class cUSERINFORMATION extends cDATACLASS {

    var $userAuth_uID, $Views, $FirstLogin, $LastLogin;

    function cUSERINFORMATION ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'userInformation';
      $this->userAuth_uID = '';
      $this->Views = '';
      $this->FirstLogin = '';
      $this->LastLogin = '';
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
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Views'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'FirstLogin'     => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'DATETIME'),

        'LastLogin'      => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'DATETIME'),

        'MessageStamp'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'DATETIME'),

        'FriendStamp'    => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'DATETIME'),

        'OnlineStamp'    => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'DATETIME'),
      );

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor

    function UpdateMessageStamp () {
      $this->Views = SQL_SKIP;
      $this->FirstLogin = SQL_SKIP;
      $this->LastLogin = SQL_SKIP;
      $this->FriendStamp = SQL_SKIP;
      $this->OnlineStamp = SQL_SKIP;

      $this->MessageStamp = SQL_NOW;

      $this->Update ();
    } // UpdateMessageStamp

    function UpdateFriendStamp () {
      $this->Views = SQL_SKIP;
      $this->FirstLogin = SQL_SKIP;
      $this->LastLogin = SQL_SKIP;
      $this->MessageStamp = SQL_SKIP;
      $this->OnlineStamp = SQL_SKIP;

      $this->FriendStamp = SQL_NOW;

      $this->Update ();
    } // UpdateFriendStamp

    function UpdateOnlineStamp () {

      $this->Views = SQL_SKIP;
      $this->FirstLogin = SQL_SKIP;
      $this->LastLogin = SQL_SKIP;
      $this->MessageStamp = SQL_SKIP;
      $this->FriendStamp = SQL_SKIP;

      $this->OnlineStamp = SQL_NOW;

      $this->Update ();
    } // UpdateOnlineStamp

    function CheckOnline () {

      $currently = strtotime ("now");
      $messagestamp = strtotime ($this->OnlineStamp);

      $difference = $currently - $messagestamp;

      if ($difference < 180) return (TRUE);

      return (FALSE);

    } // CheckOnline

  } // cUSERINFORMATION

  // User information class.
  class cUSERINVITES extends cDATACLASS {

    var $userAuth_uID, $Value, $Active, $Amount;

    function cUSERINVITES ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'userInvites';
      $this->userAuth_uID = '';
      $this->Value = '';
      $this->Active = '';
      $this->Email = '';
      $this->Amount = '';
      $this->Stamp = '';
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
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Value'          => array ('max'        => '32',
                                   'min'        => '32',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

        'Active'         => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'BOOLEAN'),

        'Recipient'      => array ('max'        => '128',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'EMAIL'),

        'Amount'         => array ('max'        => '128',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'relation'   => 'calculated',
                                   'datatype'   => 'INTEGER'),

        'Stamp'          => array ('max'        => '',
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

      // Push the Amount field onto list of fieldnames.
      // array_push ($this->FieldNames, "Amount");
      
    } // Constructor

    function CountInvites () {

      // Count the amount of usable invites this user has.
      $invitedefs = array ("userAuth_uID" => $this->userAuth_uID,
                           "Active"       => ACTIVE);
      $this->SelectByMultiple ($invitedefs);
      $this->Amount = $this->CountResult ();

      return (0);

    } // CountInvites

    function ChangeInvites () {
      
      global $zOLDAPPLE;

      // Count how many invites are available.
      $COUNTER = new cUSERINVITES;
      $COUNTER->userAuth_uID = $this->userAuth_uID;
      $COUNTER->CountInvites();
      $oldamount = $COUNTER->Amount;
      unset ($COUNTER);

      $newamount = $this->Amount;

      // Change the amount of Invites a user has.
      if ($newamount > $oldamount) {

        // Add values to the Invites table.
        $difference = $newamount - $oldamount;

        $this->Begin ();
        for ($count = 0; $count < $difference; $count++) {
          $this->Value = $zOLDAPPLE->RandomString (32);
          $this->Stamp = NULL;
          $this->Active = ACTIVE;
          $this->Add();
        } // for
        $this->Commit ();

      } elseif ($newamount < $oldamount) {

        // Subtract values from the Invites table.
        $difference = $oldamount - $newamount;
        
        // Select all tID's that belong to this user. 
        $DINVITE = new cUSERINVITES;
        $deletedefs = array ("userAuth_uID" => $this->userAuth_uID,
                             "Active" => ACTIVE);
        $DINVITE->SelectByMultiple ($deletedefs);

        // Create a list of Invite tID's belonging to this user.
        $dcount = 0;
        while ($DINVITE->FetchArray()) {
          $dlist[$dcount] = $DINVITE->tID;
          $dcount++;
        } // while

        // Loop through the list and stop at the difference limit.
        $DINVITE->Begin ();
        for ($count = 0; $count < $difference; $count++) {
          $DINVITE->tID = $dlist[$count];
          $DINVITE->Delete ();
        } // for
        $DINVITE->Commit ();

        unset ($DINVITE);
      } elseif ($newamount == $oldamount) {
      } // if
    } // ChangeInvites

  } // cUSERINVITES

  // User icons class.
  class cUSERICONS extends cDATACLASS {

    var $tID, $userAuth_uID, $Filename, $Keyword, $Comments;

    function cUSERICONS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'userIcons';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Filename = '';
      $this->Keyword = '';
      $this->Comments = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'userAuth_uID';
 
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

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Filename'       => array ('max'        => '32',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Keyword'        => array ('max'        => '32',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'specific',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Comments'       => array ('max'        => '256',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),
      );

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor

    function BuildIconList ($pUID) {

      global $gICONLIST;

      $this->Select ("userAuth_uID", $pUID);

      if ($this->CountResult () == 0) {
        return (FALSE);
      } // if

      while ($this->FetchArray () ) {
        $gICONLIST[$this->Filename] = $this->Keyword;
      } // while

      return (TRUE);

    } // BuildIconList

    function BuildIconMenu ($pUID) {

      global $gICONLIST;

      if ($this->BuildIconList ($pUID)) {

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

    function GetUserIconID ($pICONFILENAME) {
      $this->Select ("Filename", $pICONFILENAME);
      $this->FetchArray ();

      return ($this->tID);
    } // GetUserIconID

    // Replace occurrances of current icon with replacement icon.
    function Replace ($pREPLACEMENT) {

      global $zFOCUSUSER;

      global $gSITEDOMAIN;
      
      $DATA = new cDATACLASS ();

      $journalquery = "UPDATE journalPost " .
                      "SET userIcons_Filename = $pREPLACEMENT WHERE " .
                      "userAuth_uID = " . $zFOCUSUSER->uID . " AND " .
                      "userIcons_Filename = " . $this->Filename;
      $DATA->Query ($journalquery); 

      // Return FALSE if we get a MySQL error.
      if (mysql_errno()) return (FALSE);

      $commentquery = "UPDATE commentInformation " .
                      "SET Owner_Icon = $pREPLACEMENT WHERE " .
                      "Owner_Username = " . $zFOCUSUSER->Username . " AND " .
                      "Owner_Domain = " . $gSITEDOMAIN . " AND " .
                      "Owner_Icon = " . $this->Filename;
      $DATA->Query ($commentquery); 

      unset ($DATA);

      return (TRUE);

    } // Replace

  } // cUSERICONS

  // User groups class.
  class cUSERGROUPS extends cDATACLASS {

    var $tID, $userAuth_uID, $Name, $Domain;

    function cUSERGROUPS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'userGroups';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Name = '';
      $this->Domain = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForeignKey = 'userAuth_uID';
 
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

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Name'           => array ('max'        => '32',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Domain'         => array ('max'        => '128',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'specific',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),
      );

      // Grab the fields from the database.
      $this->Fields();

    } // Constructor

  } // cUSERGROUPS

?>
