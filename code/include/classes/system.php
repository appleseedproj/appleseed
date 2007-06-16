<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: system.php                              CREATED: 10-31-2006 + 
  // | LOCATION: /code/include/classes/BASE/        MODIFIED: 10-31-2006 +
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
  // | Extension of the Appleseed BASE API                               |
  // | VERSION:      0.7.0                                               |
  // | DESCRIPTION:  Extends the System class definitions.               |
  // +-------------------------------------------------------------------+

  require_once ("code/include/classes/BASE/system.php");

  // System strings class.
  class cSYSTEMSTRINGS extends cBASESYSTEMSTRINGS {

    // Extend functionality of BASE::Update () but purify input first.
    function Update () {
      // NOTE: Find a centralized way to purify.
      // $this->Purify ();
      parent::Update ();
    } // Update

    // Extend functionality of BASE::Add () but purify input first.
    function Add () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Add ();
    } // Add

  } // cSYSTEMSTRINGS

  // System options class.
  class cSYSTEMOPTIONS extends cBASESYSTEMOPTIONS {

    // Extend functionality of BASE::Update () but purify input first.
    function Update () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Update ();
    } // Update

    // Extend functionality of BASE::Add () but purify input first.
    function Add () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Add ();
    } // Add

  } // cSYSTEMOPTIONS
 
  // System logs class.
  class cSYSTEMLOGS extends cBASESYSTEMLOGS {
     
    // Extend functionality of BASE::Update () but purify input first.
    function Update () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Update ();
    } // Update

    // Extend functionality of BASE::Add () but purify input first.
    function Add () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Add ();
    } // Add

  } // cSYSTEMLOGS

  // System Tooltips class.
  class cSYSTEMTOOLTIPS extends cBASESYSTEMTOOLTIPS {
     
    // Extend functionality of BASE::Update () but purify input first.
    function Update () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Update ();
    } // Update

    // Extend functionality of BASE::Add () but purify input first.
    function Add () {
      // NOTE: Find a centralized way to purify.
      //$this->Purify ();
      parent::Add ();
    } // Add

  } // cSYSTEMTOOLTIPS

  // System Tooltips class.
  class cSYSTEMNODES extends cBASESYSTEMNODES {
     
  } // cSYSTEMNODES

