<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: login.php                               CREATED: 01-01-2005 + 
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
  // | VERSION:      0.6.0                                               |
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
  require_once ("code/include/classes/BASE/xml.php");

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
  $zAPPLE->Initialize("site.login", TRUE);

  $target = $_GET['target']; $location = $_GET['location'];
  if (!$target) { 
    $zAPPLE->IncludeFile ('code/site/error/404.php', INCLUDE_SECURITY_NONE);
    $zAPPLE->End();
    exit;
  } // if

  $self = $_SERVER['HTTP_HOST'];

  // Check if requesting user is logged in
  if ($zLOCALUSER->Username) {
    // Set a database marker saying that this user is verified.
    $zVERIFY = new cAUTHVERIFICATION ();

    // Delete existing records for this user.
    $existingcriteria = array ("Username" => $zLOCALUSER->Username,
                               "Domain"   => $target);
    $zVERIFY->DeleteByMultiple ($existingcriteria);
    
    // Delete all records older than 24 hours.
    $deletestatement = "DELETE FROM " . $zVERIFY->TableName . " WHERE Stamp <  DATE_ADD(now(),INTERVAL -1 DAY)";
    $zVERIFY->Query ($deletestatement);

    $zVERIFY->Username = $zLOCALUSER->Username;
    $zVERIFY->Domain = $target;
    $zVERIFY->Verified = TRUE;
    $zVERIFY->Stamp = SQL_NOW;
    $zVERIFY->Active = TRUE;
    $zVERIFY->Address = gethostbyname ($target);
    $zVERIFY->Host = gethostbyaddr ($zVERIFY->Address);
    if (!$zVERIFY->Host) $zVERIFY->Host = gethostbyaddr ($zVERIFY->Address);

    $zVERIFY->Add();

    $directive = '?bounce=' . $zAUTHUSER->Username . '@' . $zAUTHUSER->Domain;
  } else {
    $directive = NULL;
  } // if

  if (!$location) $location = '/';

  $redirect = "http://" . $target .  $location . $directive;

  Header ('Location: ' . $redirect);
  exit;

?>
