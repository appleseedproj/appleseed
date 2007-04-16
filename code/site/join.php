<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: join.php                                CREATED: 07-02-2005 + 
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
  // | VERSION:      0.6.0
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
  $zAPPLE->Initialize("site.join", TRUE);

  // Initialize tabs
  global $gLOCALLOGINTAB, $gREMOTELOGINTAB;
  $gLOCALLOGINTAB = ""; $gREMOTELOGINTAB = "_off";

  // Set "Remember Me" as on by default.
  if ($zLOCALUSER->Error == 0) $gREMEMBER = 'on';

  // Set the page title.
  $gPAGESUBTITLE = ' - Join';

  // Refresh if already logged in.
  if ($gAUTHUSERID) {

    // Create the meta refresh line.
    $bREFRESHLINE = $zHTML->Refresh ($gSITEURL);

    // Include the outline frame.
    $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/site/loggedin.afrw", INCLUDE_SECURITY_NONE);

    // Exit fully from applicaton.
    $zAPPLE->End();
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

    // Step 0: Check if invite is still valid.
    if ($zFOCUSUSER->userInvites->Active == INACTIVE) {
      $zFOCUSUSER->userInvites->Error = -1;
      $zSTRINGS->Lookup ("ERROR.BADINVITE", "SITE.JOIN");
      $zFOCUSUSER->userInvites->Errorlist['Value'] = $zSTRINGS->Output;
    } // if
    
    // Step 1: Check if invite and email match.
    if ($zFOCUSUSER->userInvites->CountResult () == 0) {
      // Set the error.
      $zFOCUSUSER->userInvites->Error = -1;
      $zSTRINGS->Lookup ("ERROR.BADINVITE", "SITE.JOIN");
      $zFOCUSUSER->userInvites->Errorlist['Value'] = $zSTRINGS->Output;
      // Set the recipient email back to the inputted value.
      $zFOCUSUSER->userInvites->Recipient = $gEMAIL;
    } // if

    // Step 2: Check if passwords match.
    if ($zFOCUSUSER->Pass != $gCONFIRM) {
      // Set the error.
      $zFOCUSUSER->Error = -1;
      $zSTRINGS->Lookup ("ERROR.NOMATCH", "SITE.JOIN");
      $zFOCUSUSER->Errorlist['Pass'] = $zSTRINGS->Output;
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
      $zSTRINGS->Lookup ("ERROR.BADPASS", "SITE.JOIN");
      $zFOCUSUSER->Errorlist['Pass'] = $zSTRINGS->Output;
    } // if

    // Step 4: Check if password is not a dictionary word or easily guessable.

    // NOTE: ADD LATER

    // Sanity check input variables.
    $zFOCUSUSER->Sanity ();
    $zFOCUSUSER->userProfile->Sanity ();
    $zFOCUSUSER->userInvites->Sanity ();

    // Determine if an error was found.
    if ( ($zFOCUSUSER->Error) or ($zFOCUSUSER->userProfile->Error) or
         ($zFOCUSUSER->userInvites->Error) ) {

      $zSTRINGS->Lookup ('ERROR.PAGE', 'SITE.JOIN');
      $zFOCUSUSER->Message = $zSTRINGS->Output;
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
      
      // Set the old invite to inactive.
      $zFOCUSUSER->userInvites->Active = INACTIVE;
      $zFOCUSUSER->userInvites->Update ();

      // Set a success message.

      $zFOCUSUSER->Error = 0;
      $zSTRINGS->Lookup ('MESSAGE.SUCCESS', 'SITE.JOIN');
      $zFOCUSUSER->Message = $zSTRINGS->Output;
      
      // Set the password variable to blank.
      $gPASS = "";
      // Show confirmation form.
      $gJOINLOCATION = "$gFRAMELOCATION/objects/site/join.confirm.aobj";
    } // if

  } // if

  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/site/join.afrw", INCLUDE_SECURITY_NONE);

  // End the application.
  $zAPPLE->End ();

?>
