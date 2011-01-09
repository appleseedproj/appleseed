<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: photo.php                               CREATED: 01-01-2005 + 
  // | LOCATION: /code/user/                        MODIFIED: 05-30-2007 +
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
  // | DESCRIPTION:  Displays a single photo requested from a photoset.  |
  // | WRAPPED BY:   /code/user/photos.php                               |
  // +-------------------------------------------------------------------+

  // Include necessary files
  require_once ('legacy/code/include/classes/photo.php'); 
  require_once ('legacy/code/include/classes/comments.php'); 

  global $gFILENAME;

  global $gDIRECTORY;
  list ($gDIRECTORY, $gFILENAME) = explode ('/', $gPROFILESUBACTION);
  
  $zOLDAPPLE->SetContext("user.photo");

  global $gPHOTOLOCATION;
  $gPHOTOLOCATION = "_storage/legacy/photos/" . $zFOCUSUSER->Username . "/sets/" . $gDIRECTORY . "/" . $gFILENAME;
  
  // Set link to original photo.
  $zOLDAPPLE->SetTag ('ORIGINALLINK', "_storage/legacy/photos/" . $zFOCUSUSER->Username . "/sets/" . $gDIRECTORY . "/_og." . $gFILENAME);
  
  global $gVIEWDATA;
  $gVIEWDATA = new cPHOTOSETS ($zOLDAPPLE->Context);
  
  // Load data about this photoset.
  $photosetcriteria = array ("userAuth_uID" => $zFOCUSUSER->uID,
                             "Directory"    => $gDIRECTORY);
  $gVIEWDATA->SelectByMultiple ($photosetcriteria);
  $gVIEWDATA->FetchArray ();

  // Check if photoset is hidden or blocked for this user.
  $gPRIVACYSETTING = $gVIEWDATA->photoPrivacy->Determine ("zFOCUSUSER", "zAUTHUSER", "photoSets_tID", $gVIEWDATA->tID);

  // Exit out if user is unauthorized.
  if ( ( ($gPRIVACYSETTING == PRIVACY_BLOCK) or ($gPRIVACYSETTING == PRIVACY_HIDE) ) and
       ($zLOCALUSER->userAccess->r == FALSE) and ($zLOCALUSER->uID != $zFOCUSUSER->uID)  ) {
    $zOLDAPPLE->IncludeFile ('legacy/code/site/error/403.php', INCLUDE_SECURITY_NONE);
    $zOLDAPPLE->End();
  } // if

  // Set which tab to highlight.
  $gUSERPHOTOSTAB = '';
  $this->SetTag ('USERPHOTOSTAB', $gUSERPHOTOSTAB);

  // Load data about this file.
  $photocriteria = array ("photoSets_tID" => $gVIEWDATA->tID,
                          "Filename"      => $gFILENAME);
  $gVIEWDATA->photoInfo->SelectByMultiple ($photocriteria);
  $gVIEWDATA->photoInfo->FetchArray ();
  
  global $gTAGREFERENCE;
  $gTAGREFERENCE = $gVIEWDATA->photoInfo->tID;

  global $gREFERENCEID;
  $gREFERENCEID = $gVIEWDATA->photoInfo->tID;

  // Invalid filename, file not found in database.
  if ($gVIEWDATA->photoInfo->CountResult() == 0) {
    $zOLDAPPLE->IncludeFile ('legacy/code/site/error/404.php', INCLUDE_SECURITY_NONE);
    $zOLDAPPLE->End();
  } // if

  // Create the comment manipulation class.
  global $zCOMMENTS;
  $zCOMMENTS = new cCOMMENTINFORMATION ("USER.COMMENTS");
           
  // Initialize the commenting subsystem.
  $zCOMMENTS->Initialize ();
  
  // Create a warning message if user has no write access.
  global $gPHOTOWIDTH, $gPHOTOHEIGHT;
  global $gCOMMENTBOXWIDTH;

  $gPHOTOWIDTH = $gVIEWDATA->photoInfo->Width;
  $gPHOTOHEIGHT = $gVIEWDATA->photoInfo->Height;

  $gCOMMENTBOXWIDTH = $gPHOTOWIDTH;
  if ($gCOMMENTBOXWIDTH < 476) $gCOMMENTBOXWIDTH = 476;
  if ($gCOMMENTBOXWIDTH > 800) $gCOMMENTBOXWIDTH = 800;

  // Create the scrollthrough list.
  global $gSCROLLTARGET, $gDESCRIPTION, $gKEYWORDS;
  $gTARGETPREFIX = "/profile/" . $zFOCUSUSER->Username . "/photos/" . $gVIEWDATA->Directory . "/";
  $gDESCRIPTION = $gVIEWDATA->photoInfo->Description;
  $gTAGS = $gVIEWDATA->photoInfo->Tags;

  // Handle the tagging actions.
  $zTAGS->Context = $zOLDAPPLE->Context . '.TAGS';
  $zTAGS->Handle($gVIEWDATA->photoInfo->tID, 'user.photo', 
                 "/profile/$zFOCUSUSER->Username/photos/$gVIEWDATA->Directory/" . $gVIEWDATA->photoInfo->Filename . '#tags');

  $gVIEWDATA->photoInfo->Select ("photoSets_tID", $gVIEWDATA->tID, "sID");
  $gSCROLLMAX[$zOLDAPPLE->Context] = $gVIEWDATA->photoInfo->CountResult();
  
  global $gTARGET;
  $gTARGET = $_SERVER[REQUEST_URI];
  
  // Loop through all files in the photoset.
  $count = 0;
  while ($gVIEWDATA->photoInfo->FetchArray () ) {
    $gSCROLLTARGET[$count] = $gTARGETPREFIX . $gVIEWDATA->photoInfo->Filename;
    if ($gFILENAME == $gVIEWDATA->photoInfo->Filename) {
      $gSCROLLSTART[$zOLDAPPLE->Context] = $count;
    }
    $count++;
  } // while

  $gSCROLLSTEP[$zOLDAPPLE->Context] = 1;

  // Set the post data to move back and forth.
  $gPOSTDATA = Array ("SCROLLSTART"       => $gSCROLLSTART[$zOLDAPPLE->Context],
                      "SORT"              => $gSORT,
                      "PHOTOLISTING"      => $gPHOTOLISTING,
                      "COMMENTVIEW"       => $gCOMMENTVIEW);

  global $bMAINSECTION;

  global $gTAGLINK;
  $gTAGLINK = 'http://' . $gSITEDOMAIN . '/profile/' . $zFOCUSUSER->Username . '/photos/tag/';
  
  $bMAINSECTION = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/user/photo/main.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

  // Handle comments.
  $zCOMMENTS->Handle ();

  // Include the outline frame.
  $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/frames/users/photo.afrw", INCLUDE_SECURITY_NONE);

?>
