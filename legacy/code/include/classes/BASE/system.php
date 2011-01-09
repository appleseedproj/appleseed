<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: system.php                              CREATED: 02-25-2005 + 
  // | LOCATION: /code/include/classes/BASE/        MODIFIED: 04-25-2005 +
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
  // | DESCRIPTION:  System class definitions. Reusable functions not    |
  // |               specifically tied to Appleseed.                     |
  // +-------------------------------------------------------------------+

  // System strings class.
  class cBASESYSTEMSTRINGS extends cBASEDATACLASS {

    var $tID, $Title, $Output, $Context, $Formatting, $Language;

    function cBASESYSTEMSTRINGS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'systemStrings';
      $this->tID = '';
      $this->Title = '';
      $this->Output = '';
      $this->Context = '';
      $this->Formatting = 0;
      $this->Language = 0;
      $this->Cache = array ();
      $this->PageContext = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->FieldNames = '';
      $this->PrimaryKey = 'tID';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Title'          => array ('max'        => '64',
                                   'min'        => '3',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + =',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Output'         => array ('max'        => '4000',
                                   'min'        => '2',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Context'        => array ('max'        => '64',
                                   'min'        => '4',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + =',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => '',
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

        'Formatting'     => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => '',
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Language'       => array ('max'        => '2',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
   } // Constructor

   function Display ($pTITLE, $pCONTEXT = NULL) {

     // Look up the string.
     $this->Lookup ($pTITLE, $pCONTEXT);
 
     // Echo the result.
     echo $this->Output;
 
     return ($this->Error);
   } // Display

   function Lookup ($pTITLE, $pCONTEXT = NULL) {
     
     global $gSETTINGS;
     global $zOLDAPPLE;

     $pTITLE = strtoupper ($pTITLE);
     $pCONTEXT = strtoupper ($pCONTEXT);

     // Use the page context if no context is specified.
     if (!$pCONTEXT) $pCONTEXT = $zOLDAPPLE->Context;

     // Set Output to NULL.
     unset ($this->Output);

     // If the entry is already cached, return cached entry.
     if (isset($this->Cache[$pTITLE][$pCONTEXT]['Value'])) {
       $this->Output = $this->Cache[$pTITLE][$pCONTEXT]['Value'];
       $this->Formatting = $this->Cache[$pTITLE][$pCONTEXT]['Formatting'];
       $this->Output = $zOLDAPPLE->Format ($this->Output, $this->Formatting);
       return ($this->Error);
     } // if

     if ($gSETTINGS['CascadeStrings'] == 'ON') {
       // Load String using cascaded values.
       $contexts = $this->CascadeContext ($pCONTEXT);

       // NOTE:  Find a more abstracted way to build this query.
       $uppertitle = strtoupper ($pTITLE);
       $cascade_query = "SELECT * FROM $this->TableName WHERE UPPER(Title) = '$uppertitle' AND Language='" . $gSETTINGS['Language'] . "'";

       // If a context list is specified, encapsulate OR statements in parenthesis.
       if (count($contexts) > 0) {
         $cascade_query .= " AND (";
       } // if

       // Loop through the cascaded context list.
       foreach ($contexts as $number => $context) {
         $uppercontext = strtoupper ($context);
         // Create query to drill down possible contexts.
         $cascade_query .= " UPPER(Context) = '$uppercontext' ";
         if ($number < count($contexts) - 1) 
           $cascade_query .= " OR "; 
         else
           $cascade_query .= "OR Context IS NULL) "; 
       } // foreach

       // Choose the most specific entry first.
       $cascade_query .= "ORDER BY Context DESC";

       $this->Query ($cascade_query);
     } else {
       // Load specific strings.
       $criteria = array ("UPPER(Title)"   => strtoupper ($pTITLE),
                          "UPPER(Context)" => strtoupper ($pCONTEXT),
                          "Language" => $gSETTINGS['Language']);
       $this->SelectByMultiple ($criteria);
     } // if

     $this->FetchArray();

     // If no output found, show an error and mail the administrator.
     if (!$this->Output) {
       $this->Output = "(unknown string: $pTITLE -> $pCONTEXT)";
       // NOTE: Replace/remove this eventually.
       global $gADMINEMAIL;
       global $zAUTHUSER, $zOLDAPPLE;

       $body = "\n" .
               "A string was not found.  Please double check to make sure " .
               "that it is available in the Strings database.\n\n" .
               "User - " . $zAUTHUSER->Username . "\n" .
               "Query - " . $cascade_query . "\n" .
               "Context - " . $zOLDAPPLE->Context . "\n" .
               "String - " . $this->Output . "\n\n" .
               "- APPLESEED AUTOMATED EMAIL";
       $headers = 'From: "Appleseed Error" <error@appleseedproject.org>' . "\r\n" .
                  'Reply-To: error@appleseedproject.org' . "\r\n" .
                  'X-Mailer: PHP/' . phpversion();

       //mail ($gADMINEMAIL, $this->Output, $body, $headers);
     } else {
       // Otherwise, cache the value.
       $this->Cache[$pTITLE][$pCONTEXT]['Value'] = $this->Output;
       $this->Cache[$pTITLE][$pCONTEXT]['Formatting'] = $this->Formatting;
     } // if

     $this->Output = $zOLDAPPLE->Format ($this->Output, $this->Formatting);

     return ($this->Error);

   } // Lookup

   function CascadeContext ($pCONTEXT) {

     // Split by '.'
     $contextlist_array = explode ('.', $pCONTEXT);
     
     // Loop through results.
     $list_array = $contextlist_array;
     foreach ($contextlist_array as $number => $context) {
       $contextlist[$number] = join ('.', $list_array);
       array_pop ($list_array);
     } // foreach
     $contextlist[$number+1] = "";
     
     return ($contextlist);
   } // CascadeContext

  } // cBASESYSTEMSTRINGS

  // System options class.
  class cBASESYSTEMOPTIONS extends cBASEDATACLASS {

    var $tID, $Concern, $Label, $Value, $Chosen;
 
    function cBASESYSTEMOPTIONS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'systemOptions';
      $this->tID = '';
      $this->Concern = '';
      $this->Label = '';
      $this->Value = '';
      $this->Chosen = 0;
      $this->Object = "";
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Concern'        => array ('max'        => '64',
                                   'min'        => '3',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Label'          => array ('max'        => '64',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Value'          => array ('max'        => '64',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Chosen'         => array ('max'        => '2',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => '',
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;
 
      // Grab the fields from the database.
      $this->Fields();
  
    } // Constructor

    // NOTE: Rewrite this function based on code from Checklist.
    function Menu ($pCONCERN, $pSELECTED = "", $pLABEL = "", $pAUTOSUBMIT = FALSE) {
 
      if (!$pLABEL) $pLABEL = $pCONCERN;
      unset ($this->Object);
 
      if ($pLABEL[0] == 'g') {
       $classname = strtolower (substr ($pLABEL, 1, strlen ($pLABEL)-1));
      } else {
       $classname = strtolower ($pLABEL);
      } // if

      if (substr ($classname, strlen ($classname) - 2, 2) == '[]') {
        $classname = substr ($classname, 0, strlen ($classname) - 2);
      } // if

      // Add a 'g' at the beginning to signify a global variable.
      if ($pLABEL[0] != 'g') $pLABEL = 'g' . $pLABEL;
      $concernGlobal = $vname;

      if ($pAUTOSUBMIT) $autosubmit = " onChange='JavaScript:submit();' ";
      
      $this->Select ("Concern", $pCONCERN);
      $this->Object .= "<span class='$classname'>";
      $this->Object .= "<select class='$classname' name='" . $pLABEL . "' $autosubmit >\n";
  
      if ($this->CountResult() == 0) {
        $this->Object = "No Options Found";
        return (-1);
      } // if

      global $$concernGlobal;
      while ($this->FetchArray()) {
        $hValue = htmlspecialchars ($this->Value);
  
        $disabled = "";
        if (substr ($this->Label, 0, 2) == MENU_DISABLED) {
          $this->Label = substr_replace ($this->Label, "", 0, 2);
          $disabled = "disabled";
        } // if

        if ($$concernGlobal) {
          // Using a post variable
          if ($this->Value == $$concernGlobal) {
            $this->Object .= "<option $disabled selected value=\"$hValue\">" .
                              "$this->Label</option>\n";
          } else {
            $this->Object .= "<option $disabled value=\"$hValue\">" .
                             "$this->Label</option>\n";
          } // if
        } elseif ($pSELECTED != "") {
          // Using a selected value.
          if ($this->Value == "$pSELECTED" ) {
            $this->Object .= "<option $disabled selected value=\"$hValue\">" .
                              "$this->Label</option>\n";
          } else {
            $this->Object .= "<option $disabled value=\"$hValue\">" .
                             "$this->Label</option>\n";
          } // if
        } else {
          // Using a default value.
          if ($this->Chosen == 1) {
            $this->Object .= "<option $disabled selected value=\"$hValue\">" .
                              "$this->Label</option>\n";
          } else {
            $this->Object .= "<option $disabled value=\"$hValue\">" .
                             "$this->Label</option>\n";
          } // if
        } // if

      } // while
      $this->Object .= "</select>\n";
      $this->Object .= "</span> <!-- .$classname -->";

      echo $this->Object;

    } // Menu

    // Output an html checklist.
    function Checklist ($pCONCERN, $pSELECTED = "", $pINPUTNAME = "") {
 
      if (!$pINPUTNAME) $pINPUTNAME = $pCONCERN;
      $globalConcern = 'g' . $pINPUTNAME;
 
      // Reset the Object property.
      unset ($this->Object);
  
       // Add a 'g' at the beginning to signify a global variable.
      if ($pINPUTNAME[0] != 'g') $pINPUTNAME = 'g' . $pINPUTNAME . '[]';
  
      // Load the Concern values.
      $this->Select ("Concern", $pCONCERN);
  
      // Return if no options were found.
      if ($this->CountResult() == 0) {
        $this->Object = "No Options Found";
        return (-1);
      } // if
  
      // Determine which dataset to use.
      global $$globalConcern;
      if ($$globalConcern) {
        // Use the global post variable.
        $checkedarray = $$globalConcern;
      } elseif ($pSELECTED) {
        // Use the function's specified parameter.
        $checkedarray = $pSELECTED;
      } // if
  
      // Flip the array for easier management.
      if ($checkedarray) $checkedarray = array_flip ($checkedarray);
  
      // Loop through the Concern results.
      while ($this->FetchArray()) {
        $hValue = htmlspecialchars ($this->Value);
  
        // Check to see if we're using pre-determined data.
        if ($checkedarray) {
          // In case the value is '0', this makes it non-null.
          $selvalue = $checkedarray[$hValue];
          $selvalue = "$selvalue";
            
          // Check whether item is selected or not.
          if ($selvalue != "") {
            $checked = "checked";
          } else {
            $checked = "";
          } // if
 
          $this->Object .= "<input type='checkbox' name='$pINPUTNAME' " .
                           " value='$hValue' $checked /> &nbsp;$this->Label<br />\n";
        } else {
          $this->Object .= "<input type='checkbox' name='$pINPUTNAME' " .
                           " value='$hValue' /> &nbsp;$this->Label<br />\n";
        } // if
      } // while
  
      echo $this->Object;
 
      return (0);
    } // Checklist

    function Radio () {
    } // Radio

    // Return a label pull from options list. 
    function Label ($pCONCERN, $pVALUE) {

      // Create query defitions array.
      $definitions = array ("Concern" => $pCONCERN,
                            "Value"   => $pVALUE);
 
      // Set back to null.
      unset ($this->Label);

      // Fetch the data.
      $this->SelectByMultiple ($definitions);
      $this->FetchArray ();
 
      $returnLabel = $this->Label;
  
      return ($returnLabel);
    } // Label
 
  } // cBASESYSTEMOPTIONS
 
  // System logs class.
  class cBASESYSTEMLOGS extends cBASEDATACLASS {
 
    var $tID, $userAuth_uID, $Entry, $Stamp, $Severity, $Chosen, $Location;

    function cBASESYSTEMLOGS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'systemLogs';
      $this->tID = '';
      $this->userAuth_uID = '';
      $this->Entry = '';
      $this->Stamp = '';
      $this->Severity = 0;
      $this->Location = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForiegnKey = 'userAuth_uID';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'userAuth_uID'   => array ('max'        => '',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Entry'          => array ('max'        => '255',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Stamp'          => array ('max'        => '64',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),

        'Severity'       => array ('max'        => '64',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Location'       => array ('max'        => '128',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
 
  } // cBASESYSTEMLOGS

  // System tooltips class.
  class cBASESYSTEMTOOLTIPS extends cBASEDATACLASS {

    var $tID, $Title, $Output, $Context, $Formatting, $Language;

    function cBASESYSTEMTOOLTIPS ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'systemTooltips';
      $this->tID = '';
      $this->Title = '';
      $this->Output = '';
      $this->Context = '';
      $this->Formatting = 0;
      $this->Language = 0;
      $this->Cache = array ();
      $this->PageContext = '';
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->FieldNames = '';
      $this->PrimaryKey = 'tID';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Title'          => array ('max'        => '64',
                                   'min'        => '3',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + =',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Output'         => array ('max'        => '4000',
                                   'min'        => '2',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Context'        => array ('max'        => '64',
                                   'min'        => '4',
                                   'illegal'    => '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + =',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => '',
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

        'Formatting'     => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => '',
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Language'       => array ('max'        => '2',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => NO,
                                   'datatype'   => 'STRING'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
   } // Constructor

   function CreateDisplay ($pTITLE, $pCONTEXT = NULL) {
     global $gSETTINGS, $gTHEMELOCATION;

     global $zOLDAPPLE;

     // Use the page context if no context is specified.
     if (!$pCONTEXT) $pCONTEXT = $zOLDAPPLE->Context;

     // If the entry is already cached, return cached entry.
     if (isset($this->Cache[$pTITLE][$pCONTEXT]['Value'])) {
       $this->Output = $this->Cache[$pTITLE][$pCONTEXT]['Value'];
       $this->Formatting = $this->Cache[$pTITLE][$pCONTEXT]['Formatting'];
       $this->Output = $zOLDAPPLE->Format ($this->Output, $this->Formatting);
       return ($this->Error);
     } // if

     if ($gSETTINGS['CascadeTooltips'] == 'ON') {
       // Load String using cascaded values.
       $contexts = $this->CascadeContext ($pCONTEXT);

       // NOTE:  Find a more abstracted way to build this query.
       $uppertitle = strtoupper ($pTITLE);
       $cascade_query = "SELECT * FROM $this->TableName WHERE UPPER(Title) = '$uppertitle' AND Language='" . $gSETTINGS['Language'] . "'";

       // If a context list is specified, encapsulate OR statements in parenthesis.
       if (count($contexts) > 0) {
         $cascade_query .= " AND (";
       } // if

       // Loop through the cascaded context list.
       foreach ($contexts as $number => $context) {
         $uppercontext = strtoupper ($context);
         // Create query to drill down possible contexts.
         $cascade_query .= " UPPER(Context) = '$uppercontext' ";
         if ($number < count($contexts) - 1) 
           $cascade_query .= " OR "; 
         else
           $cascade_query .= "OR Context IS NULL) "; 
       } // foreach

       // Choose the most specific entry first.
       $cascade_query .= "ORDER BY Context DESC";

       $this->Query ($cascade_query);
     } else {
       // Load specific strings.
       $criteria = array ("UPPER(Title)"   => strtoupper ($pTITLE),
                          "UPPER(Context)" => strtoupper ($pCONTEXT),
                          "Language" => $gSETTINGS['Language']);
       $this->SelectByMultiple ($criteria);
     } // if

     if ($this->CountResult() == 0) return (FALSE);

     $this->FetchArray();
     
     $zOLDAPPLE->SetTag ('TOOLTIP', $this->Output);

     // Load the tooltip from the themes directory.
     $output = $zOLDAPPLE->IncludeFile ("$gTHEMELOCATION/objects/tooltips/default.aobj", INCLUDE_SECURITY_BASIC, OUTPUT_BUFFER);

     return ($output);

   } // CreateDisplay

   function Display ($pTITLE, $pCONTEXT = NULL) {
     
     echo $this->CreateDisplay ($pTITLE, $pCONTEXT);

     return (TRUE);
   } // Display

   function CascadeContext ($pCONTEXT) {

     // Split by '.'
     $contextlist_array = explode ('.', $pCONTEXT);
     
     // Loop through results.
     $list_array = $contextlist_array;
     foreach ($contextlist_array as $number => $context) {
       $contextlist[$number] = join ('.', $list_array);
       array_pop ($list_array);
     } // foreach
     $contextlist[$number+1] = "";
     
     return ($contextlist);
   } // CascadeContext


 } // cBASESYSTEMTOOLTIPS

  class cBASESYSTEMNODES extends cBASEDATACLASS {
 
    var $tID, $Entry, $Trust, $Stamp, $EndStamp, $Share, $Source, $Inherit, $Callback;

    function cBASESYSTEMNODES ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'systemNodes';
      $this->tID = '';
      $this->tID = '';
      $this->Entry = ''; 
      $this->Trust = ''; 
      $this->Stamp = ''; 
      $this->EndStamp = ''; 
      $this->Share = ''; 
      $this->Source = ''; 
      $this->Inherit = '';
      $this->Callback = ''; 
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForiegnKey = 'userAuth_uID';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Entry'          => array ('max'        => '255',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Stamp'          => array ('max'        => '64',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),

        'EndStamp'       => array ('max'        => '64',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),

        'Share'          => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Source'         => array ('max'        => '64',
                                   'min'        => '0',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),
                                   
        'Inherit'        => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

        'Callback'       => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => YES,
                                   'sanitize'   => NO,
                                   'datatype'   => 'INTEGER'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
    
    function Blocked ($pUSERNAME, $pDOMAIN) {
      
      // domain.com              = blocks domain.com and all subdomains.
      // *.domain.com            = same as above
      // *.com                   = blocks all .com domains
      // *.subdomain.domain.com  = blocks subdomain.domain.com and all (sub-)subdomains.
      // user@domain.com         = blocks specific user at a domain.
      // ###.###.###.###         = blocks specific ip address
      // ###.###.###.*           = blocks C block.
      
      $address = $_SERVER['REMOTE_ADDR'];
      $host = gethostbyaddr ($address);
      $split_host = explode ('.', $address);
      $cblock = $split_host[0] . '.' . $split_host[1] . '.' . $split_host[2];
      
      $systemNodes = $this->TablePrefix . "systemNodes";
      
      // First check if the user exists.
      $sql_statement = "
        SELECT $systemNodes.tID as tID,
               $systemNodes.Entry as Entry,
               $systemNodes.Trust as Trust
        FROM   $systemNodes
        WHERE  $systemNodes.Entry LIKE '#%s'
        OR     $systemNodes.Entry LIKE '%s#'
        AND    ( $systemNodes.EndStamp > NOW()
                 OR     $systemNodes.EndStamp = '0000-00-00 00:00:00' )
      ";
      $sql_statement = sprintf ($sql_statement,
                                mysql_real_escape_string ($pDOMAIN),
                                mysql_real_escape_string ($cblock));
                                
      $sql_statement = str_replace ('#', '%', $sql_statement);
      
      $this->Query ($sql_statement);
      
      if (!$this->CountResult()) {
        // No entries were found.  Site is not blocked.
        return (FALSE);
      } // if
      
      // Loop through the entries.
      while ($this->FetchArray()) {
      
        $entry = $this->Entry;
        $trust = $this->Trust;
      
        // Check to see if we're looking for an ip address.
        if ($entry == $address) {
          // If we're trusting ip address.
          if ($trust == 10) return (FALSE);
          
          // If we're blocking address.
          return ("ERROR.BLOCKED.ADDRESS");
        } // if
        
        // Check to see if we're looking for a C-block of addresses.
        if ($entry == $cblock . '.*') {
          // If we're trusting ip address.
          if ($trust == 10) return (FALSE);
          
          // If we're blocking address.
          return ("ERROR.BLOCKED.ADDRESS");
        } // if
        
        // Check to see if we're looking for a domain.
        if ( ($entry == $pDOMAIN) or 
             ($entry == '*.' . $pDOMAIN) ) {
          // If we're trusting domain.
          if ($trust == 10) return (FALSE);
          
          // If we're blocking domain.
          return ("ERROR.BLOCKED");
        } // if
        
        // Check to see if we're looking for a subdomain.
        list ($null, $subentry) = explode ('.', $pDOMAIN, 2);
        if ($entry == '*.' . $subentry) {
          // If we're trusting subdomain.
          if ($trust == 10) return (FALSE);
          
          // If we're blocking subdomain.
          return ("ERROR.BLOCKED");
        } // if
        
        // Check to see if we're looking for a specific user at this address.
        if (strpos ($entry, '@') === TRUE) {
          list ($username, $domain) = explode ('@', $entry);
          if ($username == $pUSERNAME) {
             // If we're trusting user.
             if ($trust == 10) return (FALSE);
          
             // If we're blocking user.
             return ("ERROR.BLOCKED.USER");
          } // if
        } // if
        
      } // while
      
      // If we get to this point, then activity is accepted.
      return (TRUE);
    } // Blocked
 
  } // cBASESYSTEMNODES
  
  class cBASESYSTEMMAINTENANCE extends cBASEDATACLASS {
 
    var $tID, $Action, $Stamp, $Time;

    function cBASESYSTEMMAINTENANCE ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'systemMaintenance';
      $this->tID = '';
      $this->Action = ''; 
      $this->Stamp = ''; 
      $this->Time = ''; 
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForiegnKey = 'userAuth_uID';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Action'         => array ('max'        => '255',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Stamp'          => array ('max'        => '64',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'DATETIME'),

        'Time'           => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),
      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
 
  } // cBASESYSTEMMAINTENANCE
  
  class cBASESYSTEMCONFIG extends cBASEDATACLASS {
 
    var $tID, $Action, $Stamp, $Time;

    function cBASESYSTEMCONFIG ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'systemConfig';
      $this->tID = '';
      $this->Action = ''; 
      $this->Stamp = ''; 
      $this->Time = ''; 
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForiegnKey = 'userAuth_uID';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Concern'        => array ('max'        => '255',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

        'Value'          => array ('max'        => '255',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
 
  } // cBASESYSTEMCONFIG
  
  class cBASESYSTEMUPDATE extends cBASEDATACLASS {
 
    var $tID, $Action, $Stamp, $Time;

    function cBASESYSTEMUPDATE ($pDEFAULTCONTEXT = '') {
      global $gTABLEPREFIX;

      $this->TableName = $gTABLEPREFIX . 'systemUpdate';
      $this->tID = '';
      $this->Action = ''; 
      $this->Stamp = ''; 
      $this->Time = ''; 
      $this->Error = 0;
      $this->Message = '';
      $this->Result = '';
      $this->PrimaryKey = 'tID';
      $this->ForiegnKey = 'userAuth_uID';
 
      // Create extended field definitions
      $this->FieldDefinitions = array (

        'tID'            => array ('max'        => '',
                                   'min'        => '',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => 'unique',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'INTEGER'),

        'Server'         => array ('max'        => '255',
                                   'min'        => '1',
                                   'illegal'    => '',
                                   'required'   => '',
                                   'relation'   => '',
                                   'null'       => NO,
                                   'sanitize'   => YES,
                                   'datatype'   => 'STRING'),

      );

      // Assign context from paramater.
      $this->PageContext = $pDEFAULTCONTEXT;

      // Grab the fields from the database.
      $this->Fields();
 
    } // Constructor
 
  } // cBASESYSTEMUPDATE
  
