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
  require_once ('code/include/classes/BASE/application.php'); 
  require_once ('code/include/classes/BASE/debug.php'); 
  require_once ('code/include/classes/base.php'); 
  require_once ('code/include/classes/system.php'); 
  require_once ('code/include/classes/BASE/remote.php'); 
  require_once ('code/include/classes/BASE/tags.php'); 
  require_once ('code/include/classes/BASE/xml.php'); 

  // Include Appleseed classes.
  require_once ('code/include/classes/appleseed.php'); 
  require_once ('code/include/classes/privacy.php'); 
  require_once ('code/include/classes/messages.php'); 
  require_once ('code/include/classes/friends.php'); 
  require_once ('code/include/classes/users.php'); 
  require_once ('code/include/classes/auth.php'); 
  require_once ('code/include/classes/search.php'); 

  // Create the Application class.
  $zAPPLE = new cAPPLESEED ();
  
  // Set Global Variables (Put this at the top of wrapper scripts)
  $zAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zAPPLE->Initialize("admin.config", TRUE);

  // Load security settings for the current page.
  $zLOCALUSER->Access (FALSE, FALSE, FALSE);

  // Check to see if user has read access for this area.
  if ($zLOCALUSER->userAccess->r == FALSE) {
    $zAPPLE->IncludeFile ('code/site/error/403.php', INCLUDE_SECURITY_NONE);
    $zAPPLE->End();
  } // if
  
  global $zCONFIG;
  $zCONFIG = new cSYSTEMCONFIG ();

  // Set the page title.
  $gPAGESUBTITLE = ' - Admin';
 
  // Set which tab to highlight.
  $gADMINCONFIGSWITCH = '';
  
  // Load configuration options.
  
  global $gSETTINGS, $gTHEMELISTING, $gLANGUAGELISTING;
  
  // Take action.
  switch ($gACTION) {
    case 'SAVE':
      if (strlen($gSUMMARY) > 255) {
        global $gFIELDNAME, $gMAXSIZE;
        $gFIELDNAME = 'SUMMARY';
        $gMAXSIZE = '255';
        $zSTRINGS->Lookup ("ERROR.TOOLONG");
        $zCONFIG->Message = $zSTRINGS->Output;
        $zCONFIG->Error = -1;
        break;
      } // if
      $update = array ();
      $update['NodeSummary'] = $gSUMMARY;  
      $update['Language'] = $gLANGUAGE;  
      $update['Theme'] = $gTHEME;  
      $update['StorageLimit'] = $gSTORAGE;  
      $update['InviteAmount'] = $gINVITES;  
      $zCONFIG->SaveConfiguration ($update);
    break;
  } // switch
  
  $gTHEMELISTING = $zAPPLE->GetThemeList();
  $gLANGUAGELISTING = $zAPPLE->GetLanguageList();
  
  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/admin/config.afrw", INCLUDE_SECURITY_NONE);
  
  // End the application.
  $zAPPLE->End ();

?>
