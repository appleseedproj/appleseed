<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: redirect.php                            CREATED: 04-11-2006 + 
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
  // | DESCRIPTION:  Content Redirection Page.                           |
  // +-------------------------------------------------------------------+

  eval( GLOBALS ); // Import all global variables  
  
  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include BASE API classes.
  require_once ('legacy/code/include/classes/BASE/application.php'); 
  require_once ('legacy/code/include/classes/BASE/debug.php'); 
  require_once ('legacy/code/include/classes/base.php'); 
  require_once ('legacy/code/include/classes/content.php'); 
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
  $zOLDAPPLE = new cAPPLESEED ();
  
  // Set Global Variables (Put this at the top of wrapper scripts)
  $zOLDAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zOLDAPPLE->Initialize("site.redirect", TRUE);

  // Set "Remember Me" as on by default.
  $gREMEMBER = 'on';

  // Determine what we're requesting.
  $request = ltrim ($_SERVER['REQUEST_URI'], '/');
  $request = rtrim ($request, '/');

  // Step 1: Check if a content page exists.
  $zCONTENTPAGE = new cCONTENTPAGES ($zOLDAPPLE->Context);

  $zCONTENTPAGE->Select ("UPPER(Location)", strtoupper ($request));
  $zCONTENTPAGE->FetchArray ();

  if ($zCONTENTPAGE->CountResult () > 0) {
    $zOLDAPPLE->Context = $zCONTENTPAGE->Context;
    $template = "$gFRAMELOCATION/templates/content/" . $zCONTENTPAGE->Template;
    $zOLDAPPLE->IncludeFile ($template, INCLUDE_SECURITY_NONE);
    $zOLDAPPLE->End ();
  } // if

  // Step 2: Check if a user exists with that name.
  $zFOCUSUSER->Select ('Username', $request);
  $zFOCUSUSER->FetchArray ();
  $gFOCUSUSERID = $zFOCUSUSER->uID;
  $gFOCUSUSERNAME = $zFOCUSUSER->Username;

  // Set the profile request to the discovered username.
  global $gPROFILEREQUEST; $gPROFILEREQUEST = $gFOCUSUSERNAME;

  // Error out if username does not exist in database.
  if ($gFOCUSUSERID) {
   // Since we're redirecting, we'll need to reinitialize with the new info.
   global $gINITIALIZED; $gINITIALIZED = FALSE;
   $zOLDAPPLE->IncludeFile ('legacy/code/user/main.php', INCLUDE_SECURITY_NONE);
   $zOLDAPPLE->End();
  } // if

  // Exit to a 404 page by default.
  $zOLDAPPLE->IncludeFile ('legacy/code/site/error/404.php', INCLUDE_SECURITY_NONE);
  $zOLDAPPLE->End();

?>
