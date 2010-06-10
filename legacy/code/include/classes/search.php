<?php
/*
 * Created on May 11, 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */



class cSEARCH {
  function Ask () {
    global $zAPPLE;
    
    global $gFRAMELOCATION;
    $zAPPLE->IncludeFile ($gFRAMELOCATION . "objects/common/search/ask.aobj", INCLUDE_SECURITY_NONE);
  } // Ask
} // cSEARCH
