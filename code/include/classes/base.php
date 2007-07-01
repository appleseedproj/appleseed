<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: base.php                                CREATED: 10-31-2006 + 
  // | LOCATION: /code/include/classes/             MODIFIED: 10-31-2006 +
  // +-------------------------------------------------------------------+
  // | Copyright (c) 2004-2007 Appleseed Project                         |
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
  // | Extends the Appleseed BASE API                                    |
  // | VERSION:      0.7.2                                               |
  // | DESCRIPTION:  Extension of the Base class definition.             |
  // +-------------------------------------------------------------------+

  require_once ("code/include/classes/BASE/base.php");
 
  // Base data class that others extend from.
  class cDATACLASS extends cBASEDATACLASS {

    // Purify all string data.
    function Purify () {

      global $zAPPLE;

      foreach ($this->FieldNames as $fieldname) {
        switch ($this->FieldDefinitions[$fieldname]['datatype']) {
          case 'STRING':
            $this->ASDtoSafe ($fieldname);
            $this->$fieldname = $zAPPLE->Purifier->purify ($this->$fieldname);
            $this->SafeToASD ($fieldname);
          break;

          default:
          break;
        } // if
      } // foreach

    } // Purify

    // Convert ASD tags to tags that HTMLPurifier will skip.
    function ASDToSafe ($pFIELDNAME) {

      $pattern = "/<asd\s+(.*?)\s*\/>/s";
      $replacement = "[@#[asd $1 ]#@]";
      $this->$pFIELDNAME = preg_replace ($pattern, $replacement, $this->$pFIELDNAME);
       
      return (TRUE);
    } // ASDToSafe

    // Convert safe tags back to ASD tags.
    function SafeToASD ($pFIELDNAME) {

      $pattern = "/\[\@\#\[asd\s+(.*?)\s*\]\#\@\]/s";
      $replacement = "<asd $1 />";
      $this->$pFIELDNAME = preg_replace ($pattern, $replacement, $this->$pFIELDNAME);

      return (TRUE);
    } // SafeToASD

    // Extend functionality of BASE::Update () but purify input first.
    function Update ($pUPDATEKEY = NULL, $pUPDATEVALUE = NULL) {
      $this->Purify ();
      parent::Update ($pUPDATEKEY, $pUPDATEVALUE);
    } // Update

    // Extend functionality of BASE::Add () but purify input first.
    function Add () {
      $this->Purify ();
      parent::Add ();
    } // Add

    // Check if user has access to specified message.
    function CheckReadAccess () {
      global $zLOCALUSER;

      $return = FALSE;

      // Check if authuser owns this message.
      if ($zLOCALUSER->uID != $this->userAuth_uID) {

        // Check if we're logged in as Editor or not.
        if ($zLOCALUSER->userAccess->r == TRUE) {
          $return = TRUE;
        } else {
          $return = FALSE;
        } // if
      } else {
        $return = TRUE;
      } // if

      return ($return);
    } // CheckReadAccess

  } // cDATACLASS

?>
