<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: articles.php                            CREATED: 09-06-2006 + 
  // | LOCATION: /code/content/                     MODIFIED: 04-11-2007 +
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
  // | DESCRIPTION:  Article newswire.                                   |
  // +-------------------------------------------------------------------+

  eval(_G); // Import all global variables  
  
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
  require_once ('legacy/code/include/classes/comments.php'); 
  require_once ('legacy/code/include/classes/content.php'); 
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
  $zOLDAPPLE->Initialize("content.articles", TRUE);

  // Load security settings for the current page.
  $zLOCALUSER->Access (FALSE, FALSE, FALSE, '/content/articles/');

  // Create the Articles class.
  global $zARTICLES; 
  $zARTICLES = new cEXTENDEDCONTENTARTICLES  ("content.articles");
  
  // Initialize Articles.
  $zARTICLES->Initialize ();

  // Create the Comments class.
  global $zCOMMENTS;
  $zCOMMENTS = new cCOMMENTINFORMATION ("user.comments");

  // Initialize the comment subsystem.
  $zCOMMENTS->Initialize ();

  global $bARTICLES;

  // Determine which action to take.
  switch ($gACTION) {
    case 'SUBMIT':
     $zARTICLES->Synchronize();
     if (!$zAUTHUSER->Anonymous) {
       $zARTICLES->Submitted_Username = $zAUTHUSER->Username;
       $zARTICLES->Submitted_Domain = $zAUTHUSER->Domain;
     } else {
       $zARTICLES->Submitted_Username = ANONYMOUS;
       $zARTICLES->Submitted_Domain = $gSITEDOMAIN;
     } // if
     $zARTICLES->Verification = ARTICLE_PENDING;
     $zARTICLES->Formatting = FORMAT_EXT;
     $zARTICLES->Stamp = SQL_NOW;
     $zARTICLES->Sanity();
     if ($zARTICLES->Error == 0) {
       $zARTICLES->Add();
       $zARTICLES->Message = __("Article has been submitted");
       $gARTICLEREQUEST = '';
     } else {
       $gARTICLEREQUEST = 'submit';
       $gCONTENTARTICLESSUBMITTAB = '';
       $gCONTENTARTICLESVIEWTAB = '_off';
     } // if

    break;
    default:
    break;
  } // switch

  // Determine which view to take.
  switch (strtoupper($gARTICLEREQUEST)) {
    case 'SUBMIT':
      $bARTICLES = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/submit.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
    break;
    case 'QUEUE':
      $bARTICLES = $zARTICLES->HandleQueue ();
    break;
    default:
      if ($gARTICLEREQUEST) {
        global $gTARGET;
        $gTARGET = "/articles/$gARTICLEREQUEST/";
  
        // Reference ID used for comments.
        global $gREFERENCEID;
        $gREFERENCEID = $gARTICLEREQUEST;
  
        $criteria = array ("tID"          => $gARTICLEREQUEST,
                           "Verification" => ARTICLE_APPROVED);
        $zARTICLES->SelectByMultiple ($criteria);
        $zARTICLES->FetchArray();
        $zARTICLES->FormatVerboseDate ("Stamp");
        global $bARTICLEICON;
        $bARTICLEICON = $zOLDAPPLE->BufferUserIcon ($zARTICLES->Submitted_Username, $zARTICLES->Submitted_Domain);
        $bARTICLES = $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/content/articles/single.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);
        $zCOMMENTS->Handle();
      } else {
        $zARTICLES->BufferArticlesListing ();
      } // if
    break;
  } // switch

  // Include the outline frame.
  $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/frames/content/articles.afrw", INCLUDE_SECURITY_NONE);

  // End the application.
  $zOLDAPPLE->End ();

?>
