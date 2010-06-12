<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: group.php                               CREATED: 11-15-2006 + 
  // | LOCATION: /code/content/                     MODIFIED: 04-11-2007 +
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
  // | DESCRIPTION:  Discussion Groupss.                                 |
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
  require_once ('legacy/code/include/classes/comments.php'); 
  require_once ('legacy/code/include/classes/groups.php'); 
  require_once ('legacy/code/include/classes/privacy.php'); 
  require_once ('legacy/code/include/classes/friends.php'); 
  require_once ('legacy/code/include/classes/messages.php'); 
  require_once ('legacy/code/include/classes/users.php'); 
  require_once ('legacy/code/include/classes/auth.php'); 
  require_once ('legacy/code/include/classes/search.php'); 
  
  // Create the Application class.
  $zOLDAPPLE = new cAPPLESEED ();

  // Set Global Variables (Put this at the top of wrapper scripts)
  $zOLDAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zOLDAPPLE->Initialize("content.group", TRUE);

  // Load security settings for the current page.
  $zLOCALUSER->Access (FALSE, FALSE, FALSE, '/content/group/');

  // Create the Comments class.
  global $zGROUPS;
  $zGROUPS = new cGROUPINFORMATION ("content.group");

  global $bMAINSECTION;

  // Initialize the groups subsystem.
  $zGROUPS->Initialize ();

  global $gGROUPSECTION;
  list ($gGROUPREQUEST, $gGROUPSECTION, $gSTARTINGID) = explode ('/', $gGROUPREQUEST);

  // Check if the group we're looking for exists.
  $zGROUPS->Select ("Name", $gGROUPREQUEST);

  if ($zGROUPS->CountResult () == 0) {
    // No group with that name was found.  Error and exit.
    $zOLDAPPLE->IncludeFile ('legacy/code/site/error/404.php', INCLUDE_SECURITY_NONE);
    $zOLDAPPLE->End();
  } // if

  global $gOPTION;

  // Default to hidden.
  global $gOPTIONGENERAL, $gOPTIONMEMBEREDITOR, $gOPTIONINVITE;

  $gOPTIONGENERAL = 'off'; $gOPTIONMEMBEREDITOR = 'off'; 
  $gOPTIONPENDINGEDITOR = 'off'; $gOPTIONINVITE = 'off'; 

  global $gSECTIONDEFAULT;

  if ($gSECTION) {
    $on = "gOPTION" . $gSECTION;
    $$on = 'on';
    $gSECTIONDEFAULT = $gSECTION;
  } else {
    $gOPTIONGENERAL = 'on';
    $gSECTIONDEFAULT = 'GENERAL';
  } // if

  // Load the group data.
  $zGROUPS->FetchArray();

  global $gSECTIONLOCATION;
  
  switch ($zGROUPS->Access) {
    case GROUP_ACCESS_OPEN:
    case GROUP_ACCESS_OPEN_MEMBERSHIP:
      $gSECTIONLOCATION = "$gTHEMELOCATION/objects/sections/content/group/options.open.aobj";
    break;
    case GROUP_ACCESS_APPROVAL_PUBLIC:
    case GROUP_ACCESS_APPROVAL_PRIVATE:
      $gSECTIONLOCATION = "$gTHEMELOCATION/objects/sections/content/group/options.approval.aobj";
    break;
    case GROUP_ACCESS_INVITE_PUBLIC:
    case GROUP_ACCESS_INVITE_PRIVATE:
      $gSECTIONLOCATION = "$gTHEMELOCATION/objects/sections/content/group/options.invite.aobj";
    break;
  } // switch

  global $gGROUPSTAB;
  $gGROUPSTAB = $zGROUPS->DetermineTabs ();

  global $gTARGET;
  $gTARGET = "http://" . $zAUTHUSER->Domain . "/profile/" . $zAUTHUSER->Username . "/groups/";

  global $gGROUPNAME;
  $gGROUPNAME = $zGROUPS->Name;

  switch ($gACTION) {
    case 'SAVE':
      if (!$zGROUPS->CheckEditorAccess()) {
        $zOLDAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
        $zOLDAPPLE->End();
      } // if
      switch ($gSECTION) {
        case 'GENERAL':
          if ($zGROUPS->SaveGeneral ()) {
            $zGROUPS->Message = __("Record Updated");
            $gGROUPSECTION = 'info';
          } // if
        break;
        case 'PENDINGEDITOR':
          $zGROUPS->SavePendingEditor ();
          $gOPTIONGENERAL = 'off';
          $gOPTIONPENDINGEDITOR = 'on';
          $gSECTIONDEFAULT = 'PENDINGEDITOR';
        break;
        case 'MEMBEREDITOR':
          $zGROUPS->SaveMemberEditor ();
          $gOPTIONGENERAL = 'off';
          $gOPTIONMEMBEREDITOR = 'on';
          $gSECTIONDEFAULT = 'MEMBEREDITOR';
        break;
        case 'INVITE':
          $zGROUPS->ProcessInvites ();
          $gOPTIONGENERAL = 'off';
          $gOPTIONINVITE = 'on';
          $gSECTIONDEFAULT = 'INVITE';
          if (!$zGROUPS->Error) $gINVITES = NULL;
        break;
        default:
        break;
      } // switch
    break;
    case 'SAVE_ALL':
      if (!$zGROUPS->CheckEditorAccess()) {
        $zOLDAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
        $zOLDAPPLE->End();
      } // if
      $zGROUPS->SaveGeneral ();
      $zGROUPS->SaveMemberEditor ();
      $zGROUPS->SavePendingEditor ();
      if ($zGROUPS->Error) {
      } else {
        $zGROUPS->Message = __("Record Updated");
        $gGROUPSECTION = 'info';
      } // if
    break;
    case 'CANCEL':
      $gGROUPSECTION = 'info';
    break;
    case 'DELETE_GROUP':
      if (!$zGROUPS->CheckEditorAccess()) {
        $zOLDAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
        $zOLDAPPLE->End();
      } // if
      // First remove all user memberships.
      $zGROUPS->groupMembers->Select ("groupInformation_tID", $zGROUPS->tID);
      while ($zGROUPS->groupMembers->FetchArray ()) {
        $zGROUPS->Leave ($zAUTHUSER->Username, $zAUTHUSER->Domain);
      } // while
      $zGROUPS->Delete ();
      $gGROUPSECTION = 'deleted';
    break;
  } // switch

  global $bJOINBUTTON;

  // Determine which button (join/leave/none) to show.

  // Check if logged in user owns this group.
  if (!$zGROUPS->CheckEditorAccess()) {
    if (!$zGROUPS->CheckUserAccess()) {
      // Check if user is pending
      if (!$zGROUPS->CheckApproval()) {
        $zSTRINGS->Lookup ("CONFIRM.LEAVE");
        $bJOINBUTTON = $zHTML->CreateButton ("leave_group", $zSTRINGS->Output);
        $zGROUPS->Message = __("Pending approval");
      } else {
        if (!$zGROUPS->CheckJoinAccess()) {
        } else {
          // If not, show join button.
          $bJOINBUTTON = $zHTML->CreateButton ("join_group");
        } // if
      } // if
    } else {
      // If so, and user is approved, show leave button.
      $zSTRINGS->Lookup ("CONFIRM.LEAVE");
      $bJOINBUTTON = $zHTML->CreateButton ("leave_group", $zSTRINGS->Output);
      $zGROUPS->groupMembers->FetchArray ();
      if ($zGROUPS->groupMembers->Verification == GROUP_VERIFICATION_APPROVED)
        $bJOINBUTTON = $zHTML->CreateButton ("leave_group");
    } // if
  } // if

  // If we don't have view access, push directly to info page.
  if (!$zGROUPS->CheckViewAccess()) $gGROUPSECTION = 'info';

  switch ($gGROUPSECTION) {
    case 'info':
      $gTARGET = 'http://' . $zAUTHUSER->Domain . '/profile/' . $zAUTHUSER->Username . '/groups/';
      $gCONTENTGROUPSINFOTAB = "";
      $bMAINSECTION = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/info.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
    break;
    case 'members':
      $gCONTENTGROUPSMEMBERSTAB = "";
      // Buffer the member listing.
      $bMAINSECTION = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/members/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      $bMAINSECTION .= $zGROUPS->BufferMemberList ();
      $bMAINSECTION .= $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/members/bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
    break;
    case 'options':
      if (!$zGROUPS->CheckEditorAccess()) {
        $zOLDAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
        $zOLDAPPLE->End();
      } // if
      global $bMEMBEREDITOR;
      $bMEMBEREDITOR = $zGROUPS->BufferMemberEditor ();
      $bPENDINGEDITOR = $zGROUPS->BufferPendingEditor ();
      $bINVITEEDITOR = $zGROUPS->BufferInviteEditor ();
      $gTARGET = 'http://' . $gSITEDOMAIN . '/group/' . $gGROUPREQUEST . '/options/';
      $gCONTENTGROUPSOPTIONSTAB = "";
      $bMAINSECTION = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/options/main.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
    break;
    case 'deleted';
      if (!$zGROUPS->CheckEditorAccess()) {
        $zOLDAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
        $zOLDAPPLE->End();
      } // if
      $zGROUPS->Message = __("Record Deleted", array ('id' => $ADMINDATA->tID));
      $bMAINSECTION = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/group/deleted.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
    break;
    default:
      $gCONTENTGROUPSTAB = "";
      $gREFERENCEID = $zGROUPS->tID;
      $zGROUPS->Handle ();
    break;
  } // switch

  // Include the outline frame.
  $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/frames/content/group.afrw", INCLUDE_SECURITY_NONE);

  // End the application.
  $zOLDAPPLE->End ();

?>
