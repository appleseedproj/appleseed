<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: main.php                                CREATED: 02-11-2005 + 
  // | LOCATION: /code/admin/                       MODIFIED: 04-11-2007 +
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
  // | DESCRIPTION:  Main administration page.                           |
  // +-------------------------------------------------------------------+

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
  require_once ('legacy/code/include/classes/privacy.php'); 
  require_once ('legacy/code/include/classes/messages.php'); 
  require_once ('legacy/code/include/classes/friends.php'); 
  require_once ('legacy/code/include/classes/users.php'); 
  require_once ('legacy/code/include/classes/auth.php'); 
  require_once ('legacy/code/include/classes/search.php'); 

  // Create the Application class.
  $zAPPLE = new cAPPLESEED ();
  
  // Set Global Variables (Put this at the top of wrapper scripts)
  $zAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zAPPLE->Initialize("admin.control.update", TRUE);

  // Add javascript to top of page.
  $zHTML->AddScript ("admin/system/update.js");
  
  // Set which switch to highlight.
  global $gSelectedSwitch;
  $gSelectedSwitch['admin_control'] = 'selected';

  // Set which tab to highlight.
  global $gSelectedTab;
  $gSelectedTab['admin_control_update'] = 'selected';

  // Load security settings for the current page.
  $zLOCALUSER->Access (FALSE, FALSE, FALSE);
  
  // Load admin strings into cache.
  cLanguage::Load ('en-US', 'system.admin.lang');

  // Create the update class.
  $zUPDATE = new cSYSTEMUPDATE ();

  // Check to see if user has read access for this area.
  if ($zLOCALUSER->userAccess->r == FALSE) {
    $zAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
    $zAPPLE->End();
  } // if
  
  // Set the page title.
  $gPAGESUBTITLE = ' - Admin';
  
  // Set default server to update.appleseedproject.org
  if (!$gSERVER) $gSERVER = 'update.appleseedproject.org';
  
  // Load list of servers.
  $gSERVERLISTING = $zUPDATE->GetServerListing();
  
  // Load list of versions
  global $gVERSIONLISTING;
  $gVERSIONLISTING = $zUPDATE->GetVersionListing($gSERVER);
  
  $gOFFICIALLATEST = $zAPPLE->GetNodeVersion ($gSERVER);
  
  // Temporary
  $gOFFICIALLATEST = '0.7.3';
  
  // If we're not specifying the version, select the latest.
  if (!$gVERSION) $gVERSION = $gOFFICIALLATEST;
  
  global $bRESULTS, $bRESULT;
  $bRESULTS = NULL; $bRESULT = NULL;
  
  global $gRESULT;
  $gRESULT = NULL;
  
  if ($zAPPLE->CheckVersion ($gAPPLESEEDVERSION, $gOFFICIALLATEST)) {
  	$zAPPLE->SetTag ('NEWVERSION', $gOFFICIALLATEST);
  	$zAPPLE->SetTag ('OLDVERSION', $gAPPLESEEDVERSION);
    $zUPDATE->Message = __("An update is available");
  }
 
  // Choose which action to take.
  switch ($gACTION) {
  	case 'CHOOSE':
  	  if ($zUPDATE->AddServer ($gADDSERVER)) {
  	    $gSERVER = $gADDSERVER;
  	    $gADDSERVER = NULL;
  	  } // if
      // Reload list of servers.
      $gSERVERLISTING = $zUPDATE->GetServerListing();
      $gVERSIONLISTING = $zUPDATE->GetVersionListing($gSERVER);
      $gOFFICIALLATEST = $zAPPLE->GetNodeVersion ($gSERVER);
      $gVERSION = $gOFFICIALLATEST;
  	break;
  	case 'ADD':
  	break;
  	case 'REMOVE':
  	  if ($gSERVER == 'update.appleseedproject.org') {
        $zSTRINGS->Lookup ('ERROR.CANTDELETE', $zAPPLE->Context);
        $zUPDATE->Message = $zSTRINGS->Output;
        $zUPDATE->Error = -1;
  	  } else {
  	    $zUPDATE->RemoveServer ($gSERVER);
  	  } // if
      // Reload list of servers.
      $gSERVERLISTING = $zUPDATE->GetServerListing();
      $gSERVER = 'update.appleseedproject.org';
      $gVERSIONLISTING = $zUPDATE->GetVersionListing($gSERVER);
      $gOFFICIALLATEST = $zAPPLE->GetNodeVersion ($gSERVER);
  	break;
  	case 'CANCEL':
  	  $gADDSERVER = NULL;
  	break;
  	case 'CONTINUE':
  	  // If the version we're selecting is greater than the latest available.
      if ($zAPPLE->CheckVersion ($gOFFICIALLATEST, $gVERSION)) {
      	$zAPPLE->SetTag ('VERSION', $gVERSION);
      	$zAPPLE->SetTag ('SERVER', $gSERVER);
        $zSTRINGS->Lookup ('ERROR.INVALIDVERSION', $zAPPLE->Context);
        $zUPDATE->Message = $zSTRINGS->Output;
        $zUPDATE->Error = -1;
        break;
      } // if
      
      // Step 1: Check if directory tree, backup directory, and tmp directory are writeable.
      
      $temporary = $zAPPLE->GetTemporaryDirectory();
      $backup = $gBACKUPDIRECTORY;
      
      // Step 1a: Check if temporary directory is writeable;
      if (!is_writable($temporary)) {
        $zSTRINGS->Lookup ('ERROR.TEMPORARY', $zAPPLE->Context);
        $gRESULT = $zSTRINGS->Output;
        $bRESULT .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.error.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      	break;
      } else {
        $zSTRINGS->Lookup ('RESULT.TEMPORARY', $zAPPLE->Context);
        $gRESULT = $zSTRINGS->Output;
        $bRESULT .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.message.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } // if
      
      // Step 1b: Check if backup directory is writeable;
      if (!is_writable($backup)) {
        $zSTRINGS->Lookup ('ERROR.BACKUP', $zAPPLE->Context);
        $gRESULT = $zSTRINGS->Output;
        $bRESULT .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.error.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      	break;
      } else {
        $zSTRINGS->Lookup ('RESULT.BACKUP', $zAPPLE->Context);
        $gRESULT = $zSTRINGS->Output;
        $bRESULT .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.message.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      } // if
      
      // Step 1c: Check if directory tree is writable.
      $unwritable = $zUPDATE->CheckDirectoryTree ();
      
      if (count($unwritable) > 0) {
      	foreach ($unwritable as $unwrite) {
      	  $zAPPLE->SetTag ('UNWRITABLE', $unwrite);
          $zSTRINGS->Lookup ('ERROR.UNWRITABLE', $zAPPLE->Context);
          $gRESULT = $zSTRINGS->Output;
          $bRESULT .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.error.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      	} // foreach
      	break;
      } // if
      
      // Step 2: Shut down website for maintenance.
      $oldShutdownState = $zAPPLE->GetShutdown ();
      $zAPPLE->SetShutdown (ADMIN_ONLY);
      
      $zSTRINGS->Lookup ('RESULT.STARTSHUTDOWN', $zAPPLE->Context);
      $gRESULT = $zSTRINGS->Output;
      $bRESULT .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.message.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      // Step 3: Retrieve latest reference tree.
  	  $latestReference = $zUPDATE->NodeFileListing($gSERVER, $gVERSION);
  	  if (!$latestReference) {
        $zSTRINGS->Lookup ('ERROR.LATESTTREE', $zAPPLE->Context);
        $gRESULT = $zSTRINGS->Output;
        $bRESULT .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.error.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        $zAPPLE->SetShutdown ($oldShutdownState);
      	break;
  	  } else {
        $zSTRINGS->Lookup ('RESULT.LATESTTREE', $zAPPLE->Context);
        $gRESULT = $zSTRINGS->Output;
        $bRESULT .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.message.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
  	  } // if
      
      // Step 4: Retrieve current reference tree.
  	  $currentReference = $zUPDATE->NodeFileListing($gSERVER, $gAPPLESEEDVERSION);
  	  if (!$currentReference) {
        $zSTRINGS->Lookup ('ERROR.CURRENTTREE', $zAPPLE->Context);
        $gRESULT = $zSTRINGS->Output;
        $bRESULT .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.error.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        $zAPPLE->SetShutdown ($oldShutdownState);
        break;
  	  } else {
        $zSTRINGS->Lookup ('RESULT.CURRENTTREE', $zAPPLE->Context);
        $gRESULT = $zSTRINGS->Output;
        $bRESULT .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.message.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
  	  } // if
  	  
      // Step 5a: Create file backup directories.
      if (!$zUPDATE->CreateBackupDirectories($currentReference)) {
        $zAPPLE->SetShutdown ($oldShutdownState);
        break;
  	  } // if
  	  
      // Step 5b: Create new directories.
      if (!$zUPDATE->CreateNewDirectories($latestReference)) {
        $zAPPLE->SetShutdown ($oldShutdownState);
        break;
  	  } // if
  	  
      // Step 6: Merge files.
      $zUPDATE->Merge ($currentReference, $latestReference, $gSERVER, $gVERSION);
      
      // Step 7: Create database backup.
      
      // Step 8: Merge database.
      
      // Step 9: Restore appleseed node.
      $zAPPLE->SetShutdown ($oldShutdownState);
      $zSTRINGS->Lookup ('RESULT.ENDSHUTDOWN', $zAPPLE->Context);
      $gRESULT = $zSTRINGS->Output;
      $bRESULT .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.result.message.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
      
      $zUPDATE->Message = __("System updated successfully");
  	break;
  	default:
  	break;
  } // switch
  
  // If any operation results, display them.
  if ($bRESULT) $bRESULTS = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/admin/control/update.results.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
  
  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/admin/control/update.afrw", INCLUDE_SECURITY_NONE);
  
  // End the application.
  $zAPPLE->End ();

?> 
