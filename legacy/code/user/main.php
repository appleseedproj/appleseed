<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: main.php                                CREATED: 02-11-2005 +
  // | LOCATION: /code/user/                        MODIFIED: 04-11-2007 +
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
  // | DESCRIPTION:  This file uses mod_rewrite to present a requested   |
  // | user profile if requested.                                        |
  // +-------------------------------------------------------------------+

  eval(_G); // Import all global variables  
  
  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include BASE API classes.
  require_once ('legacy/code/include/classes/BASE/application.php');
  require_once ('legacy/code/include/classes/BASE/debug.php');
  require_once ('legacy/code/include/classes/base.php');
  require_once ('legacy/code/include/classes/system.php');
  require_once ('legacy/code/include/classes/BASE/remote.php');
  require_once ('legacy/code/include/classes/BASE/tags.php'); 
  require_once ('legacy/code/include/classes/BASE/xml.php');

  // Include Appleseed classes.
  require_once ('legacy/code/include/classes/appleseed.php');
  require_once ('legacy/code/include/classes/friends.php');
  require_once ('legacy/code/include/classes/groups.php');
  require_once ('legacy/code/include/classes/messages.php');
  require_once ('legacy/code/include/classes/privacy.php');
  require_once ('legacy/code/include/classes/users.php');
  require_once ('legacy/code/include/classes/auth.php');
  require_once ('legacy/code/include/classes/search.php'); 

  // Create the Application class.
  $zOLDAPPLE = new cAPPLESEED ();

  // Set Global Variables (Put this at the top of wrapper scripts)
  $zOLDAPPLE->SetGlobals ();
  
  // Initialize Appleseed.
  $zOLDAPPLE->Initialize("user", TRUE);
  
  // Add javascript to top of page.
  $zHTML->AddScript ("user/main.js");

  // Set the context based on which profile action was requested.
  $zOLDAPPLE->SetContext("user." . $gPROFILEACTION);

  // Error out if username does not exist in database.
  if (!$zFOCUSUSER->uID) {
   $zOLDAPPLE->IncludeFile ('legacy/code/site/error/404.php', INCLUDE_SECURITY_NONE);
   $zOLDAPPLE->End();
  } // if

  global $gFOCUSFULLNAME;

  // Load admin security settings for the current page.
  $zLOCALUSER->Access (FALSE, FALSE, FALSE, '/admin/users/');
  
  global $gUSERTABS, $gUSERTABSLOCATION;
  global $gTHEMELOCATION;

  global $bCONTACTBOX;
  $bCONTACTBOX = NULL;

  // Check if focus user is logged in, or if administrator is viewing.
  if ($zAUTHUSER->uID == $gFOCUSUSERID) {
    // Authorized user is viewing.
    $gUSERTABS = "/objects/tabs/users/focus.aobj";
    $gUSERTABSLOCATION = $gTHEMELOCATION . $gUSERTABS;

    // Buffer the Invite box.
    global $bINVITEBOX;

    // Calculate the amount of invites available.
    global $gINVITECOUNT;
    $zFOCUSUSER->userInvites->CountInvites ();
    $gINVITECOUNT = $zFOCUSUSER->userInvites->Amount;

    // Process invite action.
    if ($gACTION == "INVITE") {

      // Start buffering.
      ob_start ();

      // Synchronize with global POST variables.
      $invitedefs = array ("userAuth_uID" => $zFOCUSUSER->uID,
                           "Active"       => ACTIVE);
      $zFOCUSUSER->userInvites->SelectByMultiple ($invitedefs);
      $zFOCUSUSER->userInvites->FetchArray ();

      // Overwrite with new recipient.
      $zFOCUSUSER->userInvites->Recipient = $gRECIPIENT;

      // Set the page context for error reporting.
      $zFOCUSUSER->userInvites->PageContext = 'USER.INVITE';

      // Sanity check for valid email address.
      $zFOCUSUSER->userInvites->Sanity ();

      // Check if this user has already been invited.
      $CHECKINVITED = new cUSERINVITES ();
      $checkdefs = array ("Recipient"  => $zFOCUSUSER->userInvites->Recipient,
                          "Active"     => PENDING);
      $CHECKINVITED->SelectByMultiple ($checkdefs);
      $CHECKINVITED->FetchArray ();

      // Check if this user is already a member.
      $CHECKEXISTS = new cUSERPROFILE ();
      $checkdefs = array ("Email"  => $zFOCUSUSER->userInvites->Recipient);
      $CHECKEXISTS->SelectByMultiple ($checkdefs);
      $CHECKEXISTS->FetchArray ();

      // If so, create an error message.
      if ( ($CHECKINVITED->Recipient == $zFOCUSUSER->userInvites->Recipient) or
           ($CHECKEXISTS->Email == $zFOCUSUSER->userInvites->Recipient) ) {
        $zSTRINGS->Lookup ("ERROR.DUPLICATE", "USER.INVITE");
        $zFOCUSUSER->userInvites->Message = $zSTRINGS->Output;
        $zFOCUSUSER->userInvites->Error = -1;
      } // if

      if ($zFOCUSUSER->userInvites->Error) {

        // Return to form, present error.
        $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/profile/invite.aobj", INCLUDE_SECURITY_NONE);

      } else {

        global $gSITEURL;
        global $gINVITEDBY, $gINVITEURL;

        $gINVITEDBY = ucwords ($zLOCALUSER->userProfile->GetAlias ());
        $gINVITEURL = $gSITEURL . "/join/" . $zFOCUSUSER->userInvites->Value;

        $zSTRINGS->Lookup ('MAIL.SUBJECT', 'USER.INVITE');
        $subject = $zSTRINGS->Output;

        $zSTRINGS->Lookup ('MAIL.BODY', 'USER.INVITE');
        $body = $zSTRINGS->Output;

        $zSTRINGS->Lookup ('MAIL.FROM', 'USER.INVITE');
        $from = $zSTRINGS->Output;

        $headers = "From: $from" . "\r\n" .
                   "Reply-To: $from" . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        mail ($zFOCUSUSER->userInvites->Recipient, $subject, $body, $headers);

        // Update invite information.
        $zFOCUSUSER->userInvites->Stamp = SQL_NOW;
        $zFOCUSUSER->userInvites->Active = PENDING;
        $zFOCUSUSER->userInvites->Update ();

        // Success message.
        $zSTRINGS->Lookup ("MESSAGE.SENT", "USER.INVITE");
        $zFOCUSUSER->userInvites->Message = $zSTRINGS->Output;

        $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/profile/invite.confirm.aobj", INCLUDE_SECURITY_NONE);

      } // if

      // Retrieve Buffer.
      $bINVITEBOX = ob_get_clean ();

    } else {

      $bINVITEBOX = $zOLDAPPLE->BufferInviteBox ($gINVITECOUNT);

    } // if

  } else {

    if ($zLOCALUSER->userAccess->a == TRUE) {
      // Administrator is logged in.
      $gUSERTABS = "/objects/tabs/users/focus.aobj";
      $gUSERTABSLOCATION = "$gTHEMELOCATION/objects/tabs/users/focus.aobj";
    } else {
      // Standard user is logged in.
      $gUSERTABS = "/objects/tabs/users/main.aobj";
      $gUSERTABSLOCATION = "$gTHEMELOCATION/objects/tabs/users/main.aobj";
    } // if

    // Check if a user is logged in.
    if (!$zAUTHUSER->Anonymous) {
      // Buffer the contact box.
      $bCONTACTBOX = $zOLDAPPLE->BufferContactBox ();
    } // if
  } // if


  // Check to see if the main profile photo exists.
  $username = $zFOCUSUSER->Username;

  global $gPROFILEPICTURE;
  $gPROFILEPICTURE = "photos/$username/profile.jpg";

  if (!file_exists ($gPROFILEPICTURE)) {
    $gPROFILEPICTURE = "$gTHEMELOCATION/images/profile/noprofile.gif";
  } else {
    $gPROFILEPICTURE = "photos/$username/profile.jpg";
  } // if

  // Load the profile questions, unless headed to options or messages page.
  if ( ($gPROFILEACTION != 'options') and
       ($gPROFILEACTION != 'messages') ) {
    $zOLDAPPLE->Profile ();
  } // if

  // Set the page title.
  $gPAGESUBTITLE = ' - ' . $zFOCUSUSER->Username;
  
  // Reroute to proper location.
  switch ($gPROFILEACTION) {

    case "journal":
      $zOLDAPPLE->IncludeFile ('legacy/code/user/journal.php', INCLUDE_SECURITY_NONE);
    break;

    case "photos":
      $zOLDAPPLE->IncludeFile ('legacy/code/user/photosets.php', INCLUDE_SECURITY_NONE);
    break;

    case "circles":
      $zOLDAPPLE->Context = 'user.friends.circles';
      $zOLDAPPLE->IncludeFile ('legacy/code/user/circles.php', INCLUDE_SECURITY_NONE);
    break;

    case "friends":
      $zOLDAPPLE->IncludeFile ('legacy/code/user/friends.php', INCLUDE_SECURITY_NONE);
    break;

    case "info":
      $zOLDAPPLE->IncludeFile ('legacy/code/user/info.php', INCLUDE_SECURITY_NONE);
    break;

    case "messages_two":
      $zOLDAPPLE->IncludeFile ('legacy/code/user/messages_two.php', INCLUDE_SECURITY_NONE);
    break;

    case "groups":
      $zOLDAPPLE->IncludeFile ('legacy/code/user/groups.php', INCLUDE_SECURITY_NONE);
    break;

    case "messages":
      $zOLDAPPLE->IncludeFile ('legacy/code/user/messages.php', INCLUDE_SECURITY_NONE);
    break;

    case "options":
      $zOLDAPPLE->IncludeFile ('legacy/code/user/options.php', INCLUDE_SECURITY_NONE);
    break;

    case "summary":
      $zOLDAPPLE->IncludeFile ('legacy/code/user/summary.php', INCLUDE_SECURITY_NONE);
    break;

    case "":
      // Grab user's default page from database.
      $zOLDAPPLE->SetContext ("user.summary");
      $zOLDAPPLE->IncludeFile ('legacy/code/user/summary.php', INCLUDE_SECURITY_NONE);
    break;

    default:
      $zOLDAPPLE->IncludeFile ('legacy/code/site/error/404.php', INCLUDE_SECURITY_NONE);
    break;
  } // switch
  
  // End the application.
  $zOLDAPPLE->End ();

?>
