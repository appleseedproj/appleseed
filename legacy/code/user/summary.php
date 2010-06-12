<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: summary.php                             CREATED: 07-11-2007 + 
  // | LOCATION: /code/user/                        MODIFIED: 07-11-2007 +
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
  // | DESCRIPTION:  Displays summary section of user profile page.      |
  // | WRAPPED BY:   /code/user/main.php                                 |
  // +-------------------------------------------------------------------+

  // Include necessary files
  require_once ('legacy/code/include/classes/comments.php'); 

  global $bDESCRIPTION;
  if ($zFOCUSUSER->userProfile->Description) 
    $bDESCRIPTION = $zOLDAPPLE->Format ($zFOCUSUSER->userProfile->Description, FORMAT_EXT);
  else
    $bDESCRIPTION = "NO USER INFO.";

  global $zCOMMENTS;
  $zCOMMENTS = new cCOMMENTINFORMATION ("USER.COMMENTS");

  global $gTARGET;
  $gTARGET = "profile/" . $zFOCUSUSER->Username . "/summary/";

  global $gREFERENCEID;
  $gREFERENCEID = $zFOCUSUSER->uID;

  global $gCOMMENTVIEW;

  // Automatically switch comment view to profile view
  $gCOMMENTVIEW = COMMENT_VIEW_PROFILE;

  // Initialize the comment subsystem.
  $zCOMMENTS->Initialize ();
  
  $gSORT = "Posted DESC";

  $zCOMMENTS->Handle ();

  // Include the outline frame.
  $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/frames/users/summary.afrw", INCLUDE_SECURITY_NONE);

?>
