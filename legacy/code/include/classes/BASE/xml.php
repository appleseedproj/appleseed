<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: xml.php                                 CREATED: 04-24-2007 + 
  // | LOCATION: /code/include/classes/BASE/        MODIFIED: 04-24-2007 +
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
  // | VERSION:      0.7.7                                               |
  // | DESCRIPTION.  BASE XML parsing class.                             |
  // +-------------------------------------------------------------------+

  class cOLDXML {

    var $Parser;
    var $Reference;
    var $Data;
    var $CharacterData;
    var $CurrentItem;
    var $CurrentTag;

    var $ErrorMessage;
    var $ErrorLine;

    // cOLDXML Constructor.
    function cOLDXML () {

      $CurrentItem = 0;
      $CurrentTag = '';

    } // Constructor

    // Parse XML data into data structures.
    function Parse ($pXMLDATA) {

      $this->Parser = xml_parser_create();

      xml_set_object ($this->Parser, $this);
      xml_parser_set_option($this->Parser,XML_OPTION_SKIP_WHITE,1);
      xml_parser_set_option($this->Parser,XML_OPTION_CASE_FOLDING,0);
      //NOTE: Causes problems.
      //xml_set_element_handler ($this->Parser, "OpenElement", "CloseElement");
      //xml_set_character_data_handler ($this->Parser, "Character");

      xml_parse_into_struct($this->Parser, 
                            $pXMLDATA, 
                            $this->Data,
                            $this->Reference) or $this->CreateError ();
      xml_parser_free($this->Parser);

      if ($this->ErrorLine) 
        return (FALSE);
      else
        return (TRUE);
    } // Parse

    function OpenElement ($parser, $tagName, $attributes = NULL) {
       $this->CurrentTag = $tagName;
       $this->CurrentItem = count ($this->Data);

       return (TRUE);
    } // OpenElement

    function CloseElement ($parser, $tagName) {

      $this->CurrentTag = NULL;

      $item = $this->CurrentItem;
      $tag = $this->CurrentTag;
      $ref = &$this->Data;

      $ref[$item]['value'] = $this->CharacterData;

      unset ($this->CharacterData);

      return (TRUE);
    } // CloseElement

    // Character Handler
    function Character ($parser, $data) {

      $item = $this->CurrentItem;
      $tag = $this->CurrentTag;
      $ref = &$this->Data;
      if (isset ($data)) {
        if (isset ($ref[$item]['value'])) {
          //$ref[$item]['value'] .= $data;
          $this->CharacterData .= $data;
        } else {
          //$ref[$item]['value'] = $data;
          $this->CharacterData = $data;
        } // if
      } // if
      
      return (TRUE);
    } // Character

    // Assign XML errors to class variables.
    function CreateError () {
      $this->ErrorMessage = xml_error_string ($this->Parser);
      $this->ErrorLine = xml_get_current_line_number ($this->Parser);

      return (TRUE);
    } // CreateError
    
    // Free the XML Parser.
    function Free () {

      xml_parser_free($this->Parser);

      return (TRUE);

    } // Free

    // Retrieve the value of a specific element name.
    function GetValue ($pELEMENTNAME, $pELEMENTNUMBER) {

      // Look up the element reference.
      $element = $this->Reference[$pELEMENTNAME][$pELEMENTNUMBER];

      // Look up the value.
      $return = $this->Data[$element]['value'];

      return ($return);
    } // GetValue

    // Retrieve the id of a specific element name.
    function GetId ($pELEMENTNAME, $pELEMENTNUMBER) {

      // Look up the element reference.
      $element = $this->Reference[$pELEMENTNAME][$pELEMENTNUMBER];

      // Look up the id.
      $return = $this->Data[$element]['attributes']['id'];

      return ($return);
    } // GetId

    // Count the number of instances of a particular XML element.
    function GetNumberOfElements ($pELEMENTNAME) {

      // Start the counter.
	    $counter = 0;

      // Loop through the list of references with that element name.
      foreach ($this->Reference[$pELEMENTNAME] as $id => $reference) {

        // Only count "open" and "complete" tags. Skip if "close" tag.
        if ($this->Data[$reference]['type'] == 'close') continue;

        // Increment counter.
        $counter++;

      } // foreach

      return ($counter);
    } // GetNumberOfElements

    function ErrorData ($pTITLE) { 

      global $gERRORTITLE;

      $gERRORTITLE = $pTITLE;

      $this->Load ("legacy/code/include/data/xml/error.xml");
      $return = $this->Data;

      return ($return);
    } // ErrorData
    
    // Load and parse an XML template file.
    function Load ($pFILENAME) {
      
      if (!file_exists ($pFILENAME)) return (FALSE);
      
      $data = implode ("", file ($pFILENAME));
      // Parse through the %% tags
      $pattern = "/%(\w+?)%/si";
      preg_match_all ($pattern, $data, $tagvalues);
  
      foreach ($tagvalues[1] as $tagval) {
        $pval = "g" . strtoupper ($tagval);
        global $$pval;
        $pattern = "/%$tagval%/";
        if (!isset ($$pval)) $$pval = "<!-- (unknown tag: $pval) -->";
        $data = preg_replace ($pattern, $$pval, $data);
      } // foreach
      
      $this->Data = $data;
      
      return (TRUE);
      
    } // Load

  } // cOLDXML
