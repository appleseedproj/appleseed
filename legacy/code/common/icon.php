<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: icon.php                                CREATED: 12-31-2004 + 
  // | LOCATION: /code/common/                      MODIFIED: 04-11-2007 +
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
  // | VERSION:      0.7.9                                               |
  // | DESCRIPTION:  This file uses mod_rewrite to push out a default    |
  // | icon for a requested user.  Uses MySQL for user authentication.   |
  // +-------------------------------------------------------------------+

  eval( GLOBALS ); // Import all global variables  
  
  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  if (!$gICONUSER) {
    chdir ("legacy/code/site/error/");
    include ("403.php");
    exit(0); 
  } // if

  // Include BASE API classes.
  require_once ('legacy/code/include/classes/BASE/application.php'); 
  require_once ('legacy/code/include/classes/BASE/debug.php'); 
  require_once ('legacy/code/include/classes/base.php'); 
  require_once ('legacy/code/include/classes/system.php'); 
  require_once ('legacy/code/include/classes/BASE/remote.php'); 
  require_once ('legacy/code/include/classes/BASE/tags.php'); 
  require_once ('legacy/code/include/classes/BASE/xml.php'); 

  // Include Appleseed classes.
  require_once ('legacy/code/include/classes/friends.php'); 
  require_once ('legacy/code/include/classes/appleseed.php'); 
  require_once ('legacy/code/include/classes/privacy.php'); 
  require_once ('legacy/code/include/classes/messages.php'); 
  require_once ('legacy/code/include/classes/users.php'); 
  require_once ('legacy/code/include/classes/photo.php'); 
  require_once ('legacy/code/include/classes/comments.php'); 
  require_once ('legacy/code/include/classes/auth.php'); 
  require_once ('legacy/code/include/classes/search.php'); 

  // Create the Application class.
  $zOLDAPPLE = new cOLDAPPLESEED ();
  
  // Set Global Variables (Put this at the top of wrapper scripts)
  $zOLDAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zOLDAPPLE->Initialize("common.images", TRUE);

  $USER = new cOLDUSER();
  $USER->Select ("Username", $gICONUSER);
  $USER->FetchArray ();

  $USER->userIcons->Select ("userAuth_uID", $USER->uID, "tID DESC");
  $USER->userIcons->FetchArray ();

  if ($USER->CountResult () == 0) {
    $returnicon = $gTHEMELOCATION . "/images/icons/unknown.gif";
  } elseif ($USER->userIcons->Filename == NO_ICON) {
    $returnicon = $gTHEMELOCATION . "/images/icons/noicon.gif";
  } else {
    $returnicon = "_storage/legacy/photos/" . $USER->Username . "/icons/" . $USER->userIcons->Filename;
  } // if
  $zIMAGE->Show ($returnicon);

?>
