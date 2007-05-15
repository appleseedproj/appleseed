<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: privacy.php                             CREATED: 05-04-2006 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 05-04-2006 +
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
  // | VERSION:      0.7.0                                               |
  // | DESCRIPTION:  Privacy class definition.                           |
  // +-------------------------------------------------------------------+

  // Privacy class.
  class cPRIVACYCLASS extends cDATACLASS {
    var $Error;
    var $Errorlist;
    var $Message;
    var $Result;
    var $PageContext;
    var $TableName;
    var $LastIncrement;
    var $FieldNames;
    var $FieldCount;
    var $ErrorList;
    var $FieldDefinitions;
    var $PrimaryKey;
    var $ForeignKey;

    var $friendCircles_sID;
    var $Access;

    // Determine the privacy level of the authorized user.
    function Determine ($pLOCKUSER, $pKEYUSER, $pFOREIGNKEY, $pFOREIGNVAL) {

      // Owner of section being viewed.
      global $$pLOCKUSER;

      // User attempting to view section.
      global $$pKEYUSER;

      $ptable = $this->TableName;

      if ($$pKEYUSER->Anonymous) {
        // Anonymous user
        $query = "SELECT   MIN(" . $ptable . ".Access) AS FinalAccess " .
                 "FROM     " . $ptable . ", userAuthorization,  " .
                 "         friendCircles, friendCirclesList, friendInformation " .
                 "         WHERE    " . $ptable . ".userAuth_uID = userAuthorization.uID " .
                 "         AND      userAuthorization.uID= " . $$pLOCKUSER->uID .
                 "         AND      " . $ptable . ".friendCircles_sID = " . USER_EVERYONE .
                 "         AND      " . $ptable . "." . $pFOREIGNKEY . " = " . $pFOREIGNVAL;

      } else {
        // Logged in User
        $query = "SELECT   MIN(" . $ptable . ".Access) AS FinalAccess " .
                 "FROM     " . $ptable . ", userAuthorization,  " .
                 "         friendCircles, friendCirclesList, friendInformation " .
                 "         WHERE    " . $ptable . ".userAuth_uID = userAuthorization.uID " .
                 "         AND      friendCircles.userAuth_uID = userAuthorization.uID " .
                 "         AND      userAuthorization.uID= " . $$pLOCKUSER->uID .
                 "         AND      " . $ptable . ".friendCircles_sID = friendCircles.sID  " .
                 "         AND      " . $ptable . "." . $pFOREIGNKEY . " = " . $pFOREIGNVAL .
                 "         AND      friendCircles.tID = friendCirclesList.friendCircles_tID " .
                 "         AND      friendInformation.Username = '" . $$pKEYUSER->Username . "'" .
                 "         AND      friendInformation.tID = friendCirclesList.friendInformation_tID";

      } // if

      // Select privacy settings.
      $this->Query ($query);
      $this->FetchArray ();

      $result = $this->FinalAccess;

      // If no result was returned, user is not a friend.
      if ($result == NULL) {
        $query = "SELECT   MIN(" . $ptable . ".Access) AS FinalAccess " .
                 "FROM     " . $ptable . ", userAuthorization,  " .
                 "         friendCircles, friendCirclesList, friendInformation " .
                 "         WHERE    " . $ptable . ".userAuth_uID = userAuthorization.uID " .
                 "         AND      friendCircles.userAuth_uID = userAuthorization.uID " .
                 "         AND      userAuthorization.uID= " . $$pLOCKUSER->uID .
                 "         AND      (" . $ptable . ".friendCircles_sID = " . USER_LOGGEDIN .
                 "         OR       " . $ptable . ".friendCircles_sID = " . USER_EVERYONE . ") " .
                 "         AND      " . $ptable . "." . $pFOREIGNKEY . " = " . $pFOREIGNVAL .
                 "         AND      friendCircles.tID = friendCirclesList.friendCircles_tID " .
                 "         AND      friendInformation.tID = friendCirclesList.friendInformation_tID";
        // Select privacy settings.
        $this->Query ($query);
        $this->FetchArray ();
  
        $result = $this->FinalAccess;

      } // if 

      return ($result);

    } // Determine

    function BufferOptions ($pREFERENCEFIELD, $pREFERENCEID, $pSTYLE) {

      global $zSTRINGS, $zFOCUSUSER, $zHTML, $zAPPLE;
      global $gFRAMELOCATION;

      global $gPRIVACYSTYLE;
 
      $gPRIVACYSTYLE = $pSTYLE;

      $returnbuffer = "";

      // Create the privacy options list.
      $returnbuffer = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/common/privacy.top.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      // Everyone
      $zSTRINGS->Lookup ('LABEL.EVERYONE', 'USER.PRIVACY');
      $privacycriteria = array ("userAuth_uID"    => $zFOCUSUSER->uID,
                                "friendCircles_sID" => 1000,
                                $pREFERENCEFIELD  => $pREFERENCEID);

      $this->SelectByMultiple ($privacycriteria);
      $this->FetchArray ();
      $returnbuffer .= $this->PrivacyOptions ($zSTRINGS->Output, 1000, $this->Access, PRIVACY_RESTRICT); 

      // Logged in users.
      $zSTRINGS->Lookup ('LABEL.LOGGEDIN', 'USER.PRIVACY');
      $privacycriteria = array ("userAuth_uID"    => $zFOCUSUSER->uID,
                                "friendCircles_sID" => 2000,
                                $pREFERENCEFIELD  => $pREFERENCEID);
                                
      $this->SelectByMultiple ($privacycriteria);
      $this->FetchArray ();
      $returnbuffer .= $this->PrivacyOptions ($zSTRINGS->Output, 2000, $this->Access, PRIVACY_SCREEN); 

      $CIRCLES = new cFRIENDCIRCLES;

      // Loop through friend circles list.
      $CIRCLES->Select ("userAuth_uID", $zFOCUSUSER->uID);
      while ($CIRCLES->FetchArray () ) {
        $privacycriteria = array ("userAuth_uID"    => $zFOCUSUSER->uID,
                                  "friendCircles_sID" => $CIRCLES->sID,
                                  $pREFERENCEFIELD  => $pREFERENCEID);
                                
        $this->Access = NULL;
        $this->SelectByMultiple ($privacycriteria);
        $this->FetchArray();
        $returnbuffer .= $this->PrivacyOptions ($CIRCLES->Name, $CIRCLES->sID, $this->Access, PRIVACY_ALLOW); 
      } // while

      $returnbuffer .= $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/common/privacy.bottom.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      return ($returnbuffer);

    } // BufferOptions

    // Output the privacy options list row.
    function PrivacyOptions ($pLABEL, $pCIRCLESID, $pSELECTED, $pDEFAULT = PRIVACY_ALLOW) {
      global $gCIRCLESID, $gLABEL, $gSELECTED;
      global $gFRAMELOCATION;

      global $gPRIVACYALLOW, $gPRIVACYSCREEN, $gPRIVACYRESTRICT, $gPRIVACYBLOCK, $gPRIVACYHIDE;
      $gPRIVACYALLOW = FALSE; $gPRIVACYSCREEN = FALSE;
      $gPRIVACYRESTRICT = FALSE; $gPRIVACYBLOCK = FALSE;
      $gPRIVACYHIDE = FALSE;

      global $zAPPLE;

      switch ($pDEFAULT) {
        case PRIVACY_SCREEN:
          $gPRIVACYSCREEN = TRUE;
        break;

        case PRIVACY_RESTRICT:
          $gPRIVACYRESTRICT = TRUE;
        break;

        case PRIVACY_BLOCK:
          $gPRIVACYBLOCK = TRUE;
        break; 

        case PRIVACY_HIDE:
          $gPRIVACYHIDE = TRUE;
        break; 

        case PRIVACY_ALLOW:
        default:
          $gPRIVACYALLOW = TRUE;
        break; 

      } // switch

      $gSELECTED = $pSELECTED;
      $gLABEL = $pLABEL;
      $gCIRCLESID = $pCIRCLESID;

      $return = $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/common/privacy.middle.aobj", INCLUDE_SECURITY_NONE, OUTPUT_BUFFER);

      unset ($gCIRCLESID);
      unset ($gLABEL);

      unset ($gPRIVACYALLOW); unset ($gPRIVACYSCREEN); 
      unset ($gPRIVACYRESTRICT); unset ($gPRIVACYBLOCK); 

      return ($return);
    } // PrivacyOptions

    function SaveSettings ($pPRIVACY, $pREFERENCEFIELD, $pREFERENCEID) {

      global $zFOCUSUSER;

      $TARGETDATA = new cDATACLASS;
      $TARGETDATA->TableName = $this->TableName;

      // Update the privacy settings.
      foreach ($pPRIVACY as $sID => $Access) {
        $this->friendCircles_sID = $sID;

        $this->friendCircles_sID = $sID;
        $this->$pREFERENCEFIELD = $pREFERENCEID;
        $this->userAuth_uID = $zFOCUSUSER->uID;
        $this->Access = $Access;

        //Find the table ID of the exact record we're updating.
        $targetcriteria = array ("userAuth_uID"      => $zFOCUSUSER->uID,
                                 $pREFERENCEFIELD    => $pREFERENCEID,
                                 "friendCircles_sID" => $sID);

        $TARGETDATA->SelectByMultiple ($targetcriteria);
        $TARGETDATA->FetchArray ();
        $this->tID = $TARGETDATA->tID;

        // Check whether we're updating or adding a record.
        if ($TARGETDATA->CountResult () > 0) {
          $this->Update ();
        } else {
          $this->Add ();
        } // if

      } // foreach

      unset ($TARGETDATA);

      return (TRUE);

    } // SaveSettings

  } // cPRIVACYCLASS

?>
