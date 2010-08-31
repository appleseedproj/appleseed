<?php
/*
 * Created on May 11, 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */



class cOLDSEARCH {
  function Ask () {
    global $zOLDAPPLE;
    
    global $gFRAMELOCATION;
    $zOLDAPPLE->IncludeFile ($gFRAMELOCATION . "objects/common/search/ask.aobj", INCLUDE_SECURITY_NONE);
  } // Ask
} // cOLDSEARCH
