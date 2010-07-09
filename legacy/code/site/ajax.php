<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: ajax.php                                CREATED: 05-01-2007 + 
  // | LOCATION: /code/site/                        MODIFIED: 05-10-2007 +
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
  // | DESCRIPTION:  AJAX Request traffic hub.                           |
  // +-------------------------------------------------------------------+

  eval( GLOBALS ); // Import all global variables  
  
  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include Lightweight Server Classes
  require_once ('legacy/code/include/classes/asd/0.7.3.php'); 
  
  // Suppress warning reports.
  error_reporting (E_ERROR);
  
  $gACTION = $_POST['gACTION'];
  
  // Create the Server class.
  $zAJAX = new cAJAX ();
     
  //decode incoming JSON string
  $JSON = $zAJAX->JSON->decode(file_get_contents('php://input'));
  
  $gACTION = $JSON->action;
  
  switch ($gACTION) {
    case 'AJAX_GET_USER_INFORMATION':
  
      $username = $JSON->username;
      $domain = $JSON->domain;
      
      // Get user information in JSON format.
      $result = $zAJAX->GetUserInformation ($username, $domain);
      
      if (!$result) {
        // Return null values.
        $val['result'] = null;
        $val['fullname'] = null;
        $result = $zAJAX->JSON->encode ($val);
      } // if
      
      echo $result;
      
      exit;
    break;
    
    case 'AJAX_GET_STRING':
      $pString = $JSON->string;
      $string = __($pString);
      echo $string; 
      exit;
    break;
    default:
    break;
  } // switch

?>
