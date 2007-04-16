<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: login.php                               CREATED: 01-01-2005 + 
  // | LOCATION: /code/site/                        MODIFIED: 04-11-2007 +
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
  // | DESCRIPTION:  Login code.                                         |
  // +-------------------------------------------------------------------+

  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include BASE API classes.
  require_once ('code/include/classes/BASE/application.php'); 
  require_once ('code/include/classes/BASE/debug.php'); 
  require_once ('code/include/classes/base.php'); 
  require_once ('code/include/classes/system.php'); 
  require_once ('code/include/classes/BASE/remote.php'); 

  // Include Appleseed classes.
  require_once ('code/include/classes/appleseed.php'); 
  require_once ('code/include/classes/privacy.php'); 
  require_once ('code/include/classes/friends.php'); 
  require_once ('code/include/classes/messages.php'); 
  require_once ('code/include/classes/users.php'); 
  require_once ('code/include/classes/auth.php'); 

  // Create the Application class.
  $zAPPLE = new cAPPLESEED ();

  // Set Global Variables (Put this at the top of wrapper scripts)
  $zAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zAPPLE->Initialize("site.login", TRUE);

  global $gLOGINBOX;
  $gLOGINBOX = "login";

  // Initialize tabs
  global $gLOCALLOGINTAB, $gREMOTELOGINTAB;
  $gLOCALLOGINTAB = ""; $gREMOTELOGINTAB = "_off";

  $parameters = split ('/', $gLOGINREQUEST);

  if ($parameters[1]) {
    $gLOGINREQUEST = $parameters[0];
    $reference = $parameters[1];
    list ($gREFERENCEUSERNAME, $gREFERENCEDOMAIN) = split ('@', $reference);
  } // if

  switch ($gLOGINREQUEST) {
    case 'check':
      $self = $_SERVER['HTTP_HOST'];
      $check = split ('@', $reference);
      $username = $check[0];
      $host = $check[1];

      // Check if requesting user is logged in
      if ($zLOCALUSER->Username == $username) {
        // Delete all previous records for this user.
        $zVERIFY = new cAUTHVERIFICATION ();

        // Delete existing records for this user.
        $existingcriteria = array ("Username" => $zLOCALUSER->Username,
                                   "Domain"   => $host);
        $zVERIFY->DeleteByMultiple ($existingcriteria);
   
        // Delete all records older than 24 hours.
        $deletestatement = "DELETE FROM " . $zVERIFY->TableName . " WHERE Stamp <  DATE_ADD(now(),INTERVAL -1 DAY)";
        $zVERIFY->Query ($deletestatement);

        // Set a database marker saying that this user is verified.
        $zVERIFY->Username = $username;
        $zVERIFY->Domain = $host;
        $zVERIFY->Verified = TRUE;
        $zVERIFY->Address = $_SERVER['REMOTE_ADDR'];
        $zVERIFY->Host = $_SERVER['REMOTE_HOST'];
        $zVERIFY->Token = $zAPPLE->RandomString (32);
        $zVERIFY->Stamp = SQL_NOW;
        $zVERIFY->Active = TRUE;
        if (!$zVERIFY->Host) $zVERIFY->Host = gethostbyaddr ($zVERIFY->Address);

        $zVERIFY->Add();
      } // if

      // Redirect back to origin.
      $location = 'http://' . $host . '/login/return/' . $username . '@' . $self . '/';
      Header ('Location: ' . $location);
      exit;
    break;
    case 'remote':
      // If we're logging in remotely, turn on remote tab.
      $gLOCALLOGINTAB = "_off"; $gREMOTELOGINTAB = "";
      $gLOGINBOX = "remotelogin";
    break;
    case 'return':
      // Create the Remote class.
      $zREMOTE = new cREMOTE ($gREFERENCEDOMAIN);

      $self = $_SERVER['HTTP_HOST'];
      $self = str_replace ("www.", NULL, $self);

      $datalist = array ("gACTION"   => "CHECK_LOGIN",
                         "gUSERNAME" => $gREFERENCEUSERNAME,
                         "gDOMAIN"   => $self);
      $zREMOTE->Post ($datalist);

      $zXML->Parse ($zREMOTE->Return);

      $ip_address = $zXML->GetValue ("address", 0);
      $zREMOTEUSER->Username = $zXML->GetValue ("username", 0);
      $zREMOTEUSER->Fullname = $zXML->GetValue ("fullname", 0);
      $zREMOTEUSER->Domain = $zXML->GetValue ("domain", 0);
      $zREMOTEUSER->Token = $zXML->GetValue ("token", 0);

      if ($zREMOTEUSER->Username) {
        $zREMOTEUSER->Create (FALSE, "gREMOTELOGINSESSION", $zREMOTEUSER->Token);
      } // if

      $location = $gSITEURL;
      Header ('Location: ' . $location);
      exit;
    break;
  } // switch
  
  // Determine which action to take 
  switch ($gACTION) {
    case 'FORGOT':
      if (!$gUSERNAME) {
        $zSTRINGS->Lookup ('ERROR.USERNAME', 'SITE.LOGIN');
        $zLOCALUSER->Message = $zSTRINGS->Output;
        $zLOCALUSER->Error = -1;
        break;
      } // if

      // Load the user information for that user.
      $zLOCALUSER->Select ("Username", $gUSERNAME);

      // Check if any users were found.
      if ($zLOCALUSER->CountResult() === 0) {
        $zSTRINGS->Lookup ('ERROR.UNKNOWN', 'SITE.LOGIN');
        $zLOCALUSER->Message = $zSTRINGS->Output;
        $zLOCALUSER->Error = -1;
      } else {
        $zLOCALUSER->FetchArray();
        if (!$zLOCALUSER->userProfile->ChangePassword ()) {
          $zSTRINGS->Lookup ('ERROR.UPDATE', 'SITE.LOGIN');
          $zLOCALUSER->Message = $zSTRINGS->Output;
          $zLOCALUSER->Error = -1;
        } else {
          $zSTRINGS->Lookup ('MESSAGE.SENT', 'SITE.LOGIN');
          $zLOCALUSER->Message = $zSTRINGS->Output;
        } // if
      } // if

    break;
    case 'LOGIN':

      // Synchronize post variables.
      $zLOCALUSER->Synchronize ();
  
      // Set the context for error reporting.
      $zLOCALUSER->PageContext = 'SITE.LOGIN';
  
      // Sanity check, but don't check for unique data entries.
      $zLOCALUSER->Sanity(SKIP_UNIQUE);

      // Only Authenticate if no error is found.
      if ( ($zLOCALUSER->Error) or ($zLOCALUSER->Username == ANONYMOUS) ) {
        // Go back to same page.
        $gACTION = "";
      } else {
  
        // Authenticate the user login.
        $zLOCALUSER->Authenticate ();

        // Only set cookie if no error is found.
        if ($zLOCALUSER->Error) {
          // Go back to same page.
          $gACTION = "";
        } else {

          // Determine the status of the account logging in.
          switch ($zLOCALUSER->Standing) {
            case '1':
              $zSTRINGS->Lookup ('ERROR.INACTIVE', 'SITE.LOGIN');
              $zLOCALUSER->Message = $zSTRINGS->Output;
              $zLOCALUSER->Error = -1;
              $gACTION = "";
            break;
    
            case '2':
              $zSTRINGS->Lookup ('ERROR.DISABLED', 'SITE.LOGIN');
              $zLOCALUSER->Message = $zSTRINGS->Output;
              $zLOCALUSER->Error = -1;
              $gACTION = "";
            break;
    
            default:
              // Attempt to create the session cookie.
              $session_id = $zLOCALUSER->userSession->Create ($gREMEMBER);
    
              // Display an error if cookies can't be set.
              if ($session_id == "") {
                $zSTRINGS->Lookup ('ERROR.COOKIE', 'SITE.LOGIN');
                $zLOCALUSER->Message = $zSTRINGS->Output;
                $zLOCALUSER->Error = -1;
                $gACTION = "";
              } else {
   
                // Set the LastLogin to current date/time.
                $zLOCALUSER->userInformation->LastLogin = date ('Y-m-d H:i:s', time());
      
                // Check if the user has logged in before.
                if ($zLOCALUSER->userInformation->FirstLogin == "") {
                  // Set FirstLogin and LastLogin to be the same.
                  $zLOCALUSER->userInformation->FirstLogin = $zLOCALUSER->userInformation->LastLogin;
                  // Add record to the userInformation table.
                  $zLOCALUSER->userInformation->Add();
                } else {
                  // Update the userInformation table.
                  $zLOCALUSER->userInformation->Update();
                } // if
              } // if
            break;
          } // switch
        } // if
      } // if
    break;
    case 'REMOTELOGIN':
      if (!$zAPPLE->CheckEmail ($gLOCATION) ) {
        $zLOCALUSER->Error = -1;
        $zLOCALUSER->Message = "An error occurred while logging in.";
        $zLOCALUSER->Errorlist['Location'] = "This is not a valid address.";
        break;
      } // if

      $info = split ('@', $gLOCATION);
      $username = $info[0];
      $domain = $info[1];

      $self = $_SERVER['HTTP_HOST'];
      $self = str_replace ("www.", NULL, $self);

      $location = 'http://' . $domain . '/login/check/' . $username . '@' . $self . '/';

      // Redirect to Site B to check cookie.
      Header ('Location: ' . $location);
      exit;

    break;
  } // switch

  // Set "Remember Me" as on by default.
  if ($zLOCALUSER->Error == 0) $gREMEMBER = 'on';

  $gJOINLOCATION = "$gFRAMELOCATION/objects/site/join.aobj";

  // Set the page title.
  $gPAGESUBTITLE = ' - Login';

  // Refresh if successful in or if already logged in.
  if ( ($gACTION == "LOGIN") OR ($gAUTHUSERNAME) ) {

    // Refresh to the user's profile page.
    $refreshurl = "/profile/" . $zLOCALUSER->Username . "/";

    // Create the meta refresh line.
    $bREFRESHLINE = $zHTML->Refresh ($refreshurl);

    // Include the outline frame.
    $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/site/loggedin.afrw", INCLUDE_SECURITY_NONE);

    // Exit fully from applicaton.
    $zAPPLE->End();
  } // if

  // Set password to NULL.
  $gPASS = NULL;

  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/site/login.afrw", INCLUDE_SECURITY_NONE);

?>
