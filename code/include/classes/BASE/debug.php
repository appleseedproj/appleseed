<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: debug.php                               CREATED: 04-11-2007 +
  // | LOCATION: /code/include/classes/BASE/        MODIFIED: 04-11-2007 +
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
  // | Part of the Appleseed BASE API                                    |
  // | VERSION:      0.7.0                                               |
  // | DESCRIPTION:  Debug class definitions. Debugging functions for    |
  // |               developers.                                         |
  // +-------------------------------------------------------------------+

  // Debug class.
  class cDEBUG {
    var $StatementCount;
    var $StatementList = array ();

    function cDEBUG () {
      $this->StatementCount = 0;
      
      return (TRUE);
    } // Constructor

    function RememberStatement ($statement, $classname = NULL) {
      // Return out if user does not have proper access.

      if ($classname) {
        if (!$statement) return false;
        $this->StatementList[$this->StatementCount]['statement'] = $statement;
        $this->StatementList[$this->StatementCount]['class'] = "$classname";
      } else {
        $this->StatementList[$this->StatementCount] = $statement;
      } // if

      $this->StatementCount++;

      return (TRUE);

    } // RememberStatement

    function DisplayStatementList () {
      global $gFRAMELOCATION;
      
      global $zAPPLE;
      
      global $gCOUNT, $gSTATEMENT, $gCLASS;
      $gCOUNT = NULL; $gSTATEMENT = NULL; $gCLASS = NULL;
      
      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/statement.top.aobj", INCLUDE_SECURITY_NONE);
      $oddeven = "even";
      foreach ($this->StatementList as $gCOUNT => $info) {
        // Switch the class for every other row.
        $gSTATEMENT = $info['statement'];
        $gCLASS = $info['class'];
        $oddeven = ($oddeven == "even") ? "odd" : "even";
        $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/statement.middle.$oddeven.aobj", INCLUDE_SECURITY_NONE);
      } // foreach
      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/statement.bottom.aobj", INCLUDE_SECURITY_NONE);
      
      return (TRUE);
    } // DisplayStatementList
    
    function DisplayDebugInformation () {
      
      global $gFRAMELOCATION;
      
      global $zLOCALUSER, $zAPPLE;
      
      $zLOCALUSER->Access (FALSE, FALSE, FALSE, "/developer/");

      // Return out if user does not have proper access.
      if ($zLOCALUSER->userAccess->r == FALSE) return (FALSE);


      // Top of Debug element.
      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/top.aobj", INCLUDE_SECURITY_NONE);
      
      // Display queued listing of SQL statements.
      $this->DisplayStatementList ();
      
      // Bottom of Debug element.
      $zAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/bottom.aobj", INCLUDE_SECURITY_NONE);
      
      
      return (TRUE);
      
    } // DisplayDebugInformation
  }
