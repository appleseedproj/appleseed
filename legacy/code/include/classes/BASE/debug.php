<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: debug.php                               CREATED: 04-11-2007 +
  // | LOCATION: /code/include/classes/BASE/        MODIFIED: 04-11-2007 +
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
  // | Part of the Appleseed BASE API                                    |
  // | VERSION:      0.7.9                                               |
  // | DESCRIPTION:  Debug class definitions. Debugging functions for    |
  // |               developers.                                         |
  // +-------------------------------------------------------------------+

  // Debug class.
  class cOLDDEBUG {
    var $StatementCount;
    var $StatementList;
    var $ErrorList;
    var $ErrorCount;
    var $BenchmarkStart;
    var $BenchmarkStop;
    var $BenchmarkTotal;

    function cOLDDEBUG () {
      
      $this->BenchmarkStart = array ();
      $this->BenchmarkStop = array ();
      $this->BenchmarkTotal = array ('STATEMENT' => 0, 'SITE' => 0);
      
      $this->StatementList = array ();
      $this->StatementCount = 0;
      
      $this->ErrorList = array ();
      $this->ErrorCount = 0;
      
      return (TRUE);
    } // Constructor

    function RememberStatement ($statement, $classname, $benchmark) {
      // Return out if user does not have proper access.

      if (!$statement) return false;
      $this->StatementList[$this->StatementCount]['statement'] = $statement;
      $this->StatementList[$this->StatementCount]['benchmark'] = $benchmark;
      $this->StatementList[$this->StatementCount]['class'] = "$classname";

      $this->StatementCount++;

      return (TRUE);

    } // RememberStatement

    function DisplayStatementList () {
      global $gFRAMELOCATION;
      
      global $zOLDAPPLE;
      
      global $gCOUNT, $gSTATEMENT, $gCLASS, $gBENCHMARK;
      $gCOUNT = NULL; $gSTATEMENT = NULL; $gCLASS = NULL;
      
      $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/statement.top.aobj", INCLUDE_SECURITY_NONE);
      $oddeven = "even";
      foreach ($this->StatementList as $gCOUNT => $info) {
        // Switch the class for every other row.
        $gSTATEMENT = $info['statement'];
        $gCLASS = $info['class'];
        $gBENCHMARK = $info['benchmark'];
        $oddeven = ($oddeven == "even") ? "odd" : "even";
        $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/statement.middle.$oddeven.aobj", INCLUDE_SECURITY_NONE);
      } // foreach
      $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/statement.bottom.aobj", INCLUDE_SECURITY_NONE);
      
      return (TRUE);
    } // DisplayStatementList
    
    function DisplayErrorList () {
      
      global $gFRAMELOCATION;
      
      global $zOLDAPPLE;
      
      global $gCOUNT, $gSTRING, $gTYPE, $gFILE, $gLINE;
      $gCOUNT = NULL; $gSTRING = NULL; $gTYPE = NULL; $gFILE = NULL; $gLINE = NULL;
      
      $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/errors.top.aobj", INCLUDE_SECURITY_NONE);
      if (count($this->ErrorList) > 0) {
        $oddeven = "even";
        foreach ($this->ErrorList as $gCOUNT => $info) {
          // Switch the class for every other row.
          $gSTRING = $info['string'];
          $gTYPE = $info['type'];
          $gFILE = $info['file'];
          $gLINE = $info['line'];
          $oddeven = ($oddeven == "even") ? "odd" : "even";
          $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/errors.middle.$oddeven.aobj", INCLUDE_SECURITY_NONE);
        } // foreach
      } else {
        $this->Broadcast ('No Errors Or Warnings Found.');
      } // if
      $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/errors.bottom.aobj", INCLUDE_SECURITY_NONE);
      
      return (TRUE);
    } // DislayErrorList

    // Copy the cBASEDATACLASS::Broadcast function.
    function Broadcast ($pMESSAGE, $pERROR = 0) {
      $TEMP = new cBASEDATACLASS ();
      $TEMP->Error = $pERROR;
      $TEMP->Message = $pMESSAGE;
      $TEMP->Broadcast();
      unset ($TEMP);
      return (TRUE);
    } // Broadcast
    
    function DisplayDebugInformation () {
      
      global $gFRAMELOCATION;
      
      global $zLOCALUSER, $zOLDAPPLE;
      
      $zLOCALUSER->Access (FALSE, FALSE, FALSE, "/developer/");

      // Return out if user does not have proper access.
      if ($zLOCALUSER->userAccess->r == FALSE) return (FALSE);

      // Top of Debug element.
      $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/top.aobj", INCLUDE_SECURITY_NONE);
      
      // Display total page benchmark.
      $this->DisplayTotalBenchmark ();
      
      // Display error listing
      $this->DisplayErrorList ();
      
      // Display queued listing of SQL statements.
      $this->DisplayStatementList ();
      
      // Bottom of Debug element.
      $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/bottom.aobj", INCLUDE_SECURITY_NONE);
      
      return (TRUE);
      
    } // DisplayDebugInformation
    
    function DisplayTotalBenchmark () {
      global $zOLDAPPLE;
      global $gFRAMELOCATION;
      global $gTOTALTIME, $gSQLTIME, $gMEMORYUSE;
      
      
      $this->BenchmarkStop ('SITE');
      $gTOTALTIME = $this->Benchmark ('SITE');
      $gSQLTIME = $this->BenchmarkTotal['STATEMENT'];
      $gMEMORYUSE = number_format (memory_get_usage() / 1048756, 6). ' mb';
      
      $zOLDAPPLE->IncludeFile ("$gFRAMELOCATION/objects/debug/benchmark.aobj", INCLUDE_SECURITY_NONE);
      
      return (TRUE);
    } // DisplayTotalBenchmark
    
    function BenchmarkStart ($pIDENTIFIER) {
      $this->BenchmarkStart[$pIDENTIFIER] = $this->GetMicroTime ();
      
      return (TRUE);
    } // BenchmarkStart
    
    function BenchmarkStop ($pIDENTIFIER) {
      $this->BenchmarkStop[$pIDENTIFIER] = $this->GetMicroTime ();
      
      return (TRUE);
    } // BenchmarkStop
    
    function Benchmark ($pIDENTIFIER) {
      
      // Haven't started and stopped the timer properly.
      if ((!$this->BenchmarkStart[$pIDENTIFIER]) or (!$this->BenchmarkStop[$pIDENTIFIER])) return (FALSE);
      $benchmark = $this->BenchmarkStop[$pIDENTIFIER] - $this->BenchmarkStart[$pIDENTIFIER];
      $benchmark = round ($benchmark, 6);
      
      $this->BenchmarkTotal[$pIDENTIFIER] += $benchmark;
      
      return $benchmark;
      
    } // Benchmark
    
    // Get the full value of MicroTime
    function GetMicroTime () {
      
      list($m, $s) = explode(" ", microtime()); 
      $return = (float)$s + (float)$m; 
      
      return $return;
    } // GetMicroTime
    
    function HandleError ($errno, $errstr, $errfile, $errline) {
      
      $this->ErrorList[$this->ErrorCount]['string'] = $errstr;
      $this->ErrorList[$this->ErrorCount]['file'] = $errfile;
      $this->ErrorList[$this->ErrorCount]['line'] = $errline;
      
      switch ($errno) {
        case E_ERROR:
          $this->ErrorList[$this->ErrorCount]['type'] = 'ERROR';
        break;

        case E_USER_WARNING:
          $this->ErrorList[$this->ErrorCount]['type'] = 'ASD WARNING';
        break;

        case E_WARNING:
          $this->ErrorList[$this->ErrorCount]['type'] = 'WARNING';
        break;
        
        case E_NOTICE:
          $this->ErrorList[$this->ErrorCount]['type'] = 'NOTICE';
        break;
        
        default:
          $this->ErrorList[$this->ErrorCount]['type'] = 'UNKNOWN';
        break;
      } // switch
      
      $this->ErrorCount ++;
      
      /* Don't execute PHP internal error handler */
      return (TRUE);
      
    } // HandleError
  } // cOLDDEBUG
