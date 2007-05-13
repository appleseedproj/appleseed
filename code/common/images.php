<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: images.php                              CREATED: 12-31-2004 + 
  // | LOCATION: /code/common/                      MODIFIED: 04-11-2007 +
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
  // | DESCRIPTION:  This file uses mod_rewrite to push out an image     |
  // | file if requested.  Uses MySQL for user authentication.           |
  // +-------------------------------------------------------------------+

  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Determine side door location of image.
  $requested_pic = $_SERVER[REQUEST_URI];
  // NOTE:  No long storing files outside of public_html directory
  // $pic_location = "../sidedoor" . $requested_pic;
  $pic_location = $requested_pic;

  // Remove beginning '/' off location.
  if ($pic_location[0] == '/') $pic_location[0] = '';

  // Step 0: Check if file exists.

  $filename = $_SERVER['DOCUMENT_ROOT'] . $_SERVER[REQUEST_URI];
  if (!file_exists ($filename)) {
    chdir ("code/site/error/");
    include ("404.php");
    exit(0); 
  } // if (file_exists)

  // Step 1: Check if user is hotlinking.

  // Step 2: Check if user has proper access.

  // Include BASE API classes.
  require_once ('code/include/classes/BASE/application.php'); 
  require_once ('code/include/classes/BASE/debug.php'); 
  require_once ('code/include/classes/base.php'); 
  require_once ('code/include/classes/system.php'); 
  require_once ('code/include/classes/BASE/remote.php'); 
  require_once ('code/include/classes/BASE/xml.php'); 

  // Include Appleseed classes.
  require_once ('code/include/classes/friends.php'); 
  require_once ('code/include/classes/appleseed.php'); 
  require_once ('code/include/classes/privacy.php'); 
  require_once ('code/include/classes/messages.php'); 
  require_once ('code/include/classes/users.php'); 
  require_once ('code/include/classes/photo.php'); 
  require_once ('code/include/classes/comments.php'); 
  require_once ('code/include/classes/auth.php'); 
  require_once ('code/include/classes/search.php'); 

  // Create the Application class.
  $zAPPLE = new cAPPLESEED ();
  
  // Set Global Variables (Put this at the top of wrapper scripts)
  $zAPPLE->SetGlobals ();

  // Initialize Appleseed.
  $zAPPLE->Initialize("common.images", TRUE);

  list ($null, $ROOTDIR, $gOWNER, $NULL, $gDIRECTORY, $gFILENAME) = split ('/', $requested_pic);

  // This is an uploaded user photo album, so double check security.
  if ($ROOTDIR == 'photos') {
    // Get the owner's user ID.
    $zFOCUSUSER->Select ("Username", $gOWNER);
    $zFOCUSUSER->FetchArray ();

    // Get the photoset ID.
    $zPHOTOSET = new cPHOTOSETS;
    $setcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                          "Directory"    => $gDIRECTORY);
    $zPHOTOSET->SelectByMultiple ($setcriteria);
    $zPHOTOSET->FetchArray ();
    $photosetid = $zPHOTOSET->tID;

    // Get the photo sort ID.
    $filename = str_replace ("_th.", "", $gFILENAME);
    $photocriteria = array ("photoSets_tID" => $zPHOTOSET->tID,
                            "Filename"      => $filename);
    $zPHOTOSET->photoInfo->SelectByMultiple ($photocriteria);
    $zPHOTOSET->photoInfo->FetchArray ();
    $photosortid = $zPHOTOSET->photoInfo->sID;

    // Check if album is hidden or blocked for this user.
    $gPRIVACYSETTING = $zPHOTOSET->photoPrivacy->Determine ("zFOCUSUSER", "zAUTHUSER", "photoSets_tID", $photosetid);

    $allowedfilename = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
    $blockedfilename = $_SERVER['DOCUMENT_ROOT'] . "/$gTHEMELOCATION/images/error/block.png";

    unset ($zPHOTOSET);
    
    switch ($gPRIVACYSETTING) {
      case PRIVACY_BLOCK:
        if ( ($zLOCALUSER->userAccess->r == FALSE) and ($zLOCALUSER->uID != $zFOCUSUSER->uID)  ) {
          // Check if the first thumbnail was requested, otherwise show error image.
          if (strstr ($_SERVER[REQUEST_URI], "_th.") and
             ($photosortid == 1) ) {
            $zIMAGE->Show ($allowedfilename);
          } else {
            $zIMAGE->Show ($blockedfilename);
          } // if
        } else {
          // Display the image.
          $zIMAGE->Show ($allowedfilename);
        } // if
        exit(0);
      break;

      case PRIVACY_HIDE:
        if ( ($zLOCALUSER->userAccess->r == FALSE) and ($zLOCALUSER->uID != $zFOCUSUSER->uID)  ) {
          // Display the block image.
          $zIMAGE->Show ($blockedfilename);
        } else {
          // Display the image.
          $zIMAGE->Show ($allowedfilename);
        } // if
        exit(0);
      break;

      default:
        $zIMAGE->Show ($allowedfilename);
        exit(0);
      break;
    } // switch
  } // if

?>
