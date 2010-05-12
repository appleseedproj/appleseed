<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: remote.php                              CREATED: 08-19-2006 + 
  // | LOCATION: /code/include/classes/BASE/        MODIFIED: 08-19-2006 +
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
  // | VERSION:      0.7.3                                               |
  // | DESCRIPTION:  Remote class definitions. Functions for connecting  |
  // |               to external hosts through HTTP.                     |
  // +-------------------------------------------------------------------+

  class cREMOTE {

    var $Host;
    var $Return;

    function cREMOTE ($pHOST) {

      $this->Host = $pHOST;
      $this->Return = NULL;

      return (TRUE);

    } // Constructor
    
    // Send a post request to a host, and store the return information.
    function Post ($pDATALIST, $pTIMEOUT = 2) {

      $parameters = null;
      $data = null;
      
      // NOTE:  Possibly use external class to cover these functions?

      foreach ($pDATALIST as $key => $value) {
        $parameters .= $key . '=' . $value . '&';
      } // foreach

      $parameters = rtrim ($parameters, '&');

      $path = "/asd/"; // path to cgi, asp, php program

      // Open a socket and set timeout to 2 seconds.
      $fp = fsockopen($this->Host, 80, $errno, $errstr, $pTIMEOUT);

      // Return FALSE if no resource was created.
      if (!$fp) return (FALSE);

      fputs($fp, "POST $path HTTP/1.0\r\n");
      fputs($fp, "Host: " . $this->Host . "\r\n");
      fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
      fputs($fp, "Content-length: " . strlen($parameters) . "\r\n");
      fputs($fp, "Connection: close\r\n\r\n");
      fputs($fp, $parameters);

      while (!feof($fp)) {
        $data .= fgets($fp,128);
      } // while

      fclose($fp);

      // Check the header for a valid HTTP response.
      if ( preg_match("/200 OK/", $data, $regs ) ) {
        // strip header
        $this->Return = substr(strstr($data,"\r\n\r\n"),4);

        return (TRUE);
      } else {
        // HTTP error response, return FALSE.
        return (FALSE);
      } // if
    } // Post

  } // cREMOTE

?>
