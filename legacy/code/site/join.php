<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: join.php                                CREATED: 07-02-2005 + 
  // | LOCATION: /code/site/                        MODIFIED: 04-11-2007 +
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
  // | DESCRIPTION:  Login code.                                         |
  // +-------------------------------------------------------------------+

  eval( GLOBALS ); // Import all global variables  
  
  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include BASE API classes.
  require_once ('legacy/code/include/classes/BASE/application.php'); 
  require_once ('legacy/code/include/classes/BASE/debug.php'); 
  require_once ('legacy/code/include/classes/base.php'); 
  require_once ('legacy/code/include/classes/system.php'); 
  require_once ('legacy/code/include/classes/BASE/remote.php'); 
  require_once ('legacy/code/include/classes/BASE/tags.php'); 
  require_once ("legacy/code/include/classes/BASE/xml.php");

  // Include Appleseed classes.
  require_once ('legacy/code/include/classes/appleseed.php'); 
  require_once ('legacy/code/include/classes/privacy.php'); 
  require_once ('legacy/code/include/classes/friends.php'); 
  require_once ('legacy/code/include/classes/messages.php'); 
  require_once ('legacy/code/include/classes/users.php'); 
  require_once ('legacy/code/include/classes/auth.php'); 
  require_once ('legacy/code/include/classes/search.php'); 

  // Create the Application class.
  $zOLDAPPLE = new cOLDAPPLESEED ();
  
  // Set Global Variables (Put this at the top of wrapper scripts)
  $zOLDAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zOLDAPPLE->Initialize("site.join", TRUE);

  // Initialize tabs
  global $gLOCALLOGINTAB, $gREMOTELOGINTAB;
  $gLOCALLOGINTAB = ""; $gREMOTELOGINTAB = "_off";

  // Set "Remember Me" as on by default.
  if ($zLOCALUSER->Error == 0) $gREMEMBER = 'on';

  // Set the page title.
  $gPAGESUBTITLE = ' - Join';

  // Refresh if already logged in.
  if ($zAUTHUSER->uID) {

    // Create the meta refresh line.
    $bREFRESHLINE = $zHTML->Refresh ($gSITEURL);

    // Include the outline frame.
    $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/frames/site/loggedin.afrw", INCLUDE_SECURITY_NONE);

    // Exit fully from applicaton.
    $zOLDAPPLE->End();
  } // if

  $gJOINLOCATION = "$gFRAMELOCATION/objects/site/join.aobj";

  // If data has been submitted, sanity check it.
  if ($gACTION == 'SUBMIT') {

    // Set the page context for error reporting.
    $zFOCUSUSER->PageContext = "SITE.JOIN";
    $zFOCUSUSER->userProfile->PageContext = "SITE.JOIN";
    $zFOCUSUSER->userInvites->PageContext = "SITE.JOIN";

    // Synchronize data with global variables.
    $zFOCUSUSER->Synchronize();
    $zFOCUSUSER->userProfile->Synchronize();
    $zFOCUSUSER->userInvites->Synchronize();

    // Turn input sanity checking on for invite value.
    $zFOCUSUSER->userInvites->FieldDefinitions['Value']['sanitize'] = YES;

    // Set the Amount of invites to '0' to satisfy error checking.
    $zFOCUSUSER->userInvites->Amount = 0;

    $invitedef = array ("Value" => $zFOCUSUSER->userInvites->Value,
                        "Recipient" => $zFOCUSUSER->userProfile->Email);
    $zFOCUSUSER->userInvites->SelectByMultiple ($invitedef);
    $zFOCUSUSER->userInvites->FetchArray ();

	if ( $gSETTINGS['UseInvites'] == YES ) {
		
      // Step 0: Check if invite is still valid.
      if ($zFOCUSUSER->userInvites->Active == INACTIVE) {
        $zFOCUSUSER->userInvites->Error = -1;
        $zFOCUSUSER->userInvites->Errorlist['Value'] = __("Expired Invitation");
      } // if
      
      // Step 1: Check if invite and email match.
      if ($zFOCUSUSER->userInvites->CountResult () == 0) {
        // Set the error.
        $zFOCUSUSER->userInvites->Error = -1;
        $zFOCUSUSER->userInvites->Errorlist['Value'] = __("Invalid Invitation");
        // Set the recipient email back to the inputted value.
        $zFOCUSUSER->userInvites->Recipient = $gEMAIL;
      } // if
      
	} // if
    
    // Step 2: Check if passwords match.
    if ($zFOCUSUSER->Pass != $gCONFIRM) {
      // Set the error.
      $zFOCUSUSER->Error = -1;
      $zFOCUSUSER->Errorlist['Pass'] = __( "Passwords Do Not Match" );
    } // if

    // Make sure the user doesn't pick the special Anonymous name.
    if ($zFOCUSUSER->Username == ANONYMOUS) {
      $zFOCUSUSER->Error = -1;
    } // If

    // Step 3: Check if password is not the same as username or email address.
    if ( ($zFOCUSUSER->Pass == $zFOCUSUSER->Username) or
         ($zFOCUSUSER->Pass == $zFOCUSUSER->userProfile->Email) ) {
      // Set the error.
      $zFOCUSUSER->Error = -1;
      $zFOCUSUSER->Errorlist['Pass'] = __( "Invalid Password" );
    } // if

    // Step 4: Check if password is not a dictionary word or easily guessable.

    // NOTE: ADD LATER

    // Sanity check input variables.
    $zFOCUSUSER->Sanity ();
    $zFOCUSUSER->userProfile->Sanity ();
    if (  $gSETTINGS['UseInvites'] == YES ) $zFOCUSUSER->userInvites->Sanity ();

    // Determine if an error was found.
    if ( ($zFOCUSUSER->Error) or ($zFOCUSUSER->userProfile->Error) or
         ($zFOCUSUSER->userInvites->Error) ) {

      $zFOCUSUSER->Message = __( "Error Creating Account" );
      $zFOCUSUSER->Error = -1;

      // Go back to the original form.
      $gJOINLOCATION = "$gFRAMELOCATION/objects/site/join.aobj";

      // Set the password variable to blank.
      $gPASS = "";
    } else {

      // Remove the cascaded entities.
      unset ($zFOCUSUSER->Cascade);

      // Cascade only user profile and information tables.
      $zFOCUSUSER->Cascade = array ("userProfile", "userInformation");

      // NOTE: Use a DEFINED value here.
      $zFOCUSUSER->Standing = 0;
      $zFOCUSUSER->Verification = 0;

      // Create the new account.
      $zFOCUSUSER->Add ();

      // Initialize the new user.
      $zFOCUSUSER->Initialize ();
      
      if (  $gSETTINGS['UseInvites'] == YES ) {
        // Set the old invite to inactive.
        $zFOCUSUSER->userInvites->Active = INACTIVE;
        $zFOCUSUSER->userInvites->Update ();
      }

      // Set a success message.

      $zFOCUSUSER->Error = 0;
      $zFOCUSUSER->Message = __("Your account has been created.");
      
      // Set the password variable to blank.
      $gPASS = "";
      // Show confirmation form.
      $gJOINLOCATION = "$gFRAMELOCATION/objects/site/join.confirm.aobj";
    } // if

  } // if
  
  global $bINVITECODE;
  
  if (  $gSETTINGS['UseInvites'] == YES ) {
    ob_start ();
    
      $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/site/join.invite.aobj", INCLUDE_SECURITY_NONE);
      
    $bINVITECODE = ob_get_clean();
  }

  // Include the outline frame.
  $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/frames/site/join.afrw", INCLUDE_SECURITY_NONE);

  // End the application.
  $zOLDAPPLE->End ();

?>
