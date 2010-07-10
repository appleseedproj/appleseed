<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: asd.php                                 CREATED: 12-31-2004 + 
  // | LOCATION: /code/site/                        MODIFIED: 04-11-2007 +
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
  // | DESCRIPTION:  ASD Network traffic hub.                            |
  // +-------------------------------------------------------------------+
  
  eval( GLOBALS ); // Import all global variables  
  
  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Suppress warning reports.
  error_reporting (E_ERROR);
  
  // Retrieve variables from POST.
  $gACTION = $_POST['gACTION'];
  $gVERSION = $_POST['gVERSION'];
  $gDOMAIN = $_POST['gDOMAIN'];
  
  // Determine which server to load.
  $versions = explode ('.', $gVERSION);
  $major = $versions[0]; $minor = $versions[1]; $micro = $versions[2];
  if (!$minor) $minor = 0; if (!$micro) $micro = 0;
  
  // Load list of available server versions.
  $handle = opendir('legacy/code/include/classes/asd/');
  while (false !== ($file = readdir($handle))) {
  		$file_exts = explode ('.', $file);
  		if ($file_exts[count($file_exts)-1] != 'php') continue;
  		$serverVersions[] = $file;
  } // if
  
  if (!file_exists ('legacy/code/include/classes/asd/' . $gVERSION . '.php')) {
  	// Loop through and find the latest version.
  	foreach ($serverVersions as $serverVersion) {
  	  $versions = explode ('.', $serverVersion);
  	  $serverMajor = $versions[0]; $serverMinor = $versions[1]; $serverMicro = $versions[2];
 	  if (!$serverMinor) $serverMinor = 0; if (!$serverMicro) $serverMicro = 0;
  	  if ($serverMajor > $major) continue; 
  	  if (($serverMajor == $major) and ($serverMinor > $minor)) continue; 
  	  if (($serverMajor == $major) and ($serverMinor == $minor) and ($serverMicro > $micro)) continue; 
  	  $useVersion = $serverVersion;
  	} // foreach
  } else {
  	$useVersion = $gVERSION . '.php';
  } // if
  
  // Default to the earliest known server version if no version was supplied, and error out.
  if (!$useVersion) {
  	require_once ("legacy/code/include/classes/asd/0.7.3.php"); 
  	$zSERVER = new cSERVER ();
  	echo $zSERVER->NoVersion();
  	exit;
  } // if
  
  // Include Lightweight Server Classes
  require_once ("legacy/code/include/classes/asd/$useVersion"); 
  
  // Create the Server class.
  $zSERVER = new cSERVER ();

  switch (strtoupper ($gACTION)) {
    case 'SITE_VERSION':
    case 'ASD_SITE_VERSION':
      echo $zSERVER->SiteVersion ();
      exit;
    break;
    
    case 'USER_INFORMATION':
    case 'ASD_USER_INFORMATION':
      echo $zSERVER->UserInformation ();
      exit;
    break;
    
    case 'TOKEN_CHECK':
    case 'ASD_TOKEN_CHECK':
      echo $zSERVER->TokenCheckLocal ();
      exit;
    break;
    
    case 'FRIEND_STATUS':
    case 'ASD_FRIEND_STATUS':
      echo $zSERVER->FriendStatus ();
      exit;
    break;
    
    case 'FRIEND_REQUEST':
    case 'ASD_FRIEND_REQUEST':
      echo $zSERVER->FriendRequest ();
      exit;
    break;
    
    case 'FRIEND_CANCEL':
    case 'ASD_FRIEND_CANCEL':
      echo $zSERVER->FriendCancel ();
      exit;
    break;
    
    case 'FRIEND_DENY':
    case 'ASD_FRIEND_DENY':
      echo $zSERVER->FriendDeny ();
      exit;
    break;
    
    case 'FRIEND_DELETE':
    case 'ASD_FRIEND_DELETE':
      echo $zSERVER->FriendDelete ();
      exit;
    break;
    
    case 'FRIEND_APPROVE':
    case 'ASD_FRIEND_APPROVE':
      echo $zSERVER->FriendApprove ();
      exit;
    break;
    
    case 'LOGIN_CHECK':
    case 'ASD_LOGIN_CHECK':
      echo $zSERVER->LoginCheck ();
      exit;
    break;
    
    case 'ICON_LIST':
    case 'ASD_ICON_LIST':
      echo $zSERVER->IconList ();
      exit;
    break;
    
    case 'MESSAGE_RETRIEVE':
    case 'ASD_MESSAGE_RETRIEVE':
      echo $zSERVER->MessageRetrieve ();
      exit;
    break;
    
    case 'MESSAGE_NOTIFY':
    case 'ASD_MESSAGE_NOTIFY':
      echo $zSERVER->MessageNotify ();
      exit;
    break;
    
    case 'GROUP_JOIN':
    case 'ASD_GROUP_JOIN':
      echo $zSERVER->GroupJoin ();
      exit;
    break;
    
    case 'GROUP_LEAVE':
    case 'ASD_GROUP_LEAVE':
      echo $zSERVER->GroupLeave ();
      exit;
    break;
    
    case 'GROUP_INFORMATION':
    case 'ASD_GROUP_INFORMATION':
      echo $zSERVER->GroupInformation ();
      exit;
    break;
    
    case 'UPDATE_NODE_NETWORK':
    case 'ASD_UPDATE_NODE_NETWORK':
      echo $zSERVER->UpdateNodeNetwork ();
      exit;
    break;
    
    case 'NODE_INFORMATION':
    case 'ASD_NODE_INFORMATION':
      echo $zSERVER->NodeInformation ();
      exit; 
    break;
    
    case 'TRUSTED_LIST':
    case 'ASD_TRUSTED_LIST':
      echo $zSERVER->TrustedList ();
      exit;
    break;
    
    
    default:
    break;
  } // switch

?>
