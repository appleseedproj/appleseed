<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: main.php                                CREATED: 12-31-2004 + 
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
  // | DESCRIPTION:  Main index page.                                    |
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
  require_once ("code/include/classes/BASE/xml.php");

  // Include Appleseed classes.
  require_once ('code/include/classes/appleseed.php'); 
  require_once ('code/include/classes/comments.php'); 
  require_once ('code/include/classes/content.php'); 
  require_once ('code/include/classes/privacy.php'); 
  require_once ('code/include/classes/friends.php'); 
  require_once ('code/include/classes/messages.php'); 
  require_once ('code/include/classes/photo.php'); 
  require_once ('code/include/classes/users.php'); 
  require_once ('code/include/classes/auth.php'); 
  require_once ('code/include/classes/search.php'); 
  
  // Create the Application class.
  $zAPPLE = new cAPPLESEED ();

  // Set Global Variables (Put this at the top of wrapper scripts)
  $zAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zAPPLE->Initialize("site", TRUE);

  // Create the Articles class.
  $zARTICLES = new cEXTENDEDCONTENTARTICLES  ("content.articles");

  // Initialize articles.
  $zARTICLES->Initialize ();

  // Buffer the articles listing.
  $zARTICLES->BufferArticlesListing ();

  // Set "Remember Me" as on by default.
  $gREMEMBER = 'on';
  
  // Buffer the login box
  ob_start (); 

  if ($zAUTHUSER->Anonymous) {
    // Include the login box tab object
    global $gLOCALLOGINTAB, $gREMOTELOGINTAB;
    $gLOCALLOGINTAB = ""; $gREMOTELOGINTAB = "_off";
    // Include the login box object
    $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/site/loginbox.aobj", INCLUDE_SECURITY_NONE);
  } // if

  // Retrieve output buffer.
  $bLOGINBOX = ob_get_clean ();
  
  global $bLATESTPHOTOS;
  $zPHOTOS = new cPHOTOSETS();
  $bLATESTPHOTOS = $zPHOTOS->BufferLatestPhotos();

  global $bLATESTNODES;
  $zNODES = new cCONTENTNODES();
  $bLATESTNODES = $zNODES->BufferLatestNodes();

  $zLOCALUSER->userInvites->CountInvites();
  $gINVITEAMOUNT = $zLOCALUSER->userInvites->Amount;

  // Buffer the invite box
  ob_start (); 
 
  if ( ($zAUTHUSER->Username != '') AND ($gSETTINGS['UseInvites'] == ON) AND 
       ($gINVITEAMOUNT > 0) ) {
    // Include the login box object
    $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/site/invitebox.aobj", INCLUDE_SECURITY_NONE);
  } // if

  // Retrieve output buffer.
  $bINVITEBOX = ob_get_clean ();

  // End buffering.
  ob_end_clean (); 

  // Include the outline frame.
  $zAPPLE->IncludeFile ("$gFRAMELOCATION/frames/site/main.afrw", INCLUDE_SECURITY_NONE);

  // Exit application.
  $zAPPLE->End ();

?>
