<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: groups.php                              CREATED: 11-11-2006 + 
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
  // | DESCRIPTION:  Displays group members of user.                     |
  // | WRAPPED BY:   /code/user/main.php                                 |
  // +-------------------------------------------------------------------+

  // Include necessary files
  require_once ('legacy/code/include/classes/groups.php'); 

  // Set which tab to highlight.
  $gUSERGROUPSTAB = '';
  $this->SetTag ('USERGROUPSTAB', $gUSERGROUPSTAB);

  global $zGROUPS;
  $zGROUPS = new cGROUPINFORMATION ("USER.GROUPS");

  global $bGROUPS;
  $bGROUPS = NULL;

  global $gTARGET;
  $gTARGET = "http://" . $zFOCUSUSER->Domain . "/profile/" . $zFOCUSUSER->Username . "/groups/";

  switch ($gACTION) {
    case 'JOIN_GROUP':
      //if (!$zGROUPS->CheckJoinAccess()) break;
      $zGROUPS->Join ($gGROUPNAME, $gGROUPDOMAIN);
      //if ($zGROUPS->Access == GROUP_ACCESS_OPEN) {
      //  $zSTRINGS->Lookup ("MESSAGE.JOINED");
      //} elseif ($zGROUPS->Access == GROUP_ACCESS_APPROVAL) {
        //$zSTRINGS->Lookup ("MESSAGE.PENDING");
      //} // if
      //$zGROUPS->Message = $zSTRINGS->Output;
    break;
    case 'LEAVE_GROUP':
      $zGROUPS->Leave ($gGROUPNAME, $gGROUPDOMAIN);
      //$zGROUPS->CheckApproval();
    break;
    default:
    break;
  } // switch

  $zFOCUSUSER->userGroups->Select ("userAuth_uID", $zFOCUSUSER->uID);
  
  if ($zFOCUSUSER->userGroups->CountResult() == 0) {
    $zFOCUSUSER->userGroups->Message = __("No Results Found");
    $bGROUPS = $zFOCUSUSER->userGroups->CreateBroadcast ();
  } else {
    $bGROUPS .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/groups/top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
    while ($zFOCUSUSER->userGroups->FetchArray ()) {

      $zGROUPS->Members = NULL;

      // Load information about specific group.
      if (!$zGROUPS->GetInformation ($zFOCUSUSER->userGroups->Name, $zFOCUSUSER->userGroups->Domain)) {
        // If no information was found, continue through the loop.
        continue;
      }

      global $gGROUPLINK;
      $gGROUPLINK = "http://" . $zFOCUSUSER->userGroups->Domain . "/group/" . $zGROUPS->Name . "/";

      global $bOWNERICON;
      $bOWNERICON = OUTPUT_NBSP;

      // Check if the user is the owner of this group.
      if ( ($zFOCUSUSER->userGroups->Domain == $gSITEDOMAIN) and 
           ($zGROUPS->userAuth_uID == $zFOCUSUSER->uID) ) {
         $bOWNERICON = $zAPPLE->IncludeFile ("$gTHEMELOCATION/objects/icons/owner_group.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);
         $bOWNERICON .= $zTOOLTIPS->CreateDisplay ("OWNER");
      } // if

      global $gMEMBERCOUNT;
      global $bMEMBERS;

      if (!$zGROUPS->Members) {
        // Pull member count from local data.
        $gMEMBERCOUNT = $zGROUPS->groupMembers->CountMembers ($zGROUPS->Name);
      } else {
        // Member count already pulled from remote domain.
        $gMEMBERCOUNT = $zGROUPS->Members;
      } // if

      $zSTRINGS->Lookup ("LABEL.MEMBERS");
      $bMEMBERS = $zSTRINGS->Output;

      $zGROUPS->FormatDate ("Stamp");
      if ($zFOCUSUSER->userGroups->Domain != $gSITEDOMAIN) {
        $bGROUPS .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/groups/middle.remote.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } else {
        $bGROUPS .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/groups/middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } // if
    } // while
    $bGROUPS .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/groups/bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
  } // if

  global $gREFERENCEID;
  $gREFERENCEID = $zFOCUSUSER->uID;

  $gSORT = "Posted DESC";

  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/users/groups.afrw", INCLUDE_SECURITY_NONE);

?>
