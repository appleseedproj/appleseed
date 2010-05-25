<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: index.php                               CREATED: 04-12-2007 + 
  // | LOCATION: /                                  MODIFIED: 04-19-2008 +
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
  // | DESCRIPTION:  Default Appleseed Installer                         |
  // +-------------------------------------------------------------------+
  
  // Turn off error reporting.
  error_reporting (0);

  // HANDLE AJAX REQUEST.
  if ($_POST['gACTION'] == 'CHECK') {
  	$host = $_POST['gHOST']; $database = $_POST['gDATABASE']; $username = $_POST['gUSERNAME']; $password = $_POST['gPASSWORD'];
  	if (!$db = @mysql_connect($host, $username, $password)) { echo "0"; exit; }
  	if (!@mysql_select_db($database, $db)) { echo "0"; exit; }
  	echo "1"; exit;
  } // if
  
  $INSTALL = new cINSTALL;
  
  $INSTALL->PreLoadSiteData ();
  
  $gDATABASE = ($_POST['gDATABASE']) ? $_POST['gDATABASE'] : $gDATABASE;
  $gUSERNAME = ($_POST['gUSERNAME']) ? $_POST['gUSERNAME'] : $gUSERNAME;
  $gPASSWORD = ($_POST['gPASSWORD']) ? $_POST['gPASSWORD'] : $gPASSWORD;
  $gPREFIX = ($_POST['gPREFIX']) ? $_POST['gPREFIX'] : $gPREFIX;
  $gHOST = ($_POST['gHOST']) ? $_POST['gHOST'] : $gHOST;
  $gHOST = ($gHOST) ? $gHOST : 'localhost';
  $gDOMAIN = ($_POST['gDOMAIN']) ? $_POST['gDOMAIN'] : $gDOMAIN;
  $gDOMAIN = ($gDOMAIN) ? $gDOMAIN : 'http://' . $_SERVER['HTTP_HOST'];
  $gUPGRADE = $_POST['gUPGRADE'];
  $gADMINUSER = ($_POST['gADMINUSER']) ? $_POST['gADMINUSER'] : 'Admin';
  $gADMINPASS = $_POST['gADMINPASS'];
  $gADMINPASSCONFIRM = $_POST['gADMINPASSCONFIRM'];
  
  $gSTAMP = '_' . date ('mdy_His', strtotime ('now'));
  
  // Add http:// to Domain if not already there.
  if (strtolower(substr ($gDOMAIN, 0, 7)) != 'http://') $gDOMAIN = 'http://' . $gDOMAIN;
  
  $INSTALL->CheckForSubDirectory ();
  $INSTALL->CheckPHPVersion ();
  $INSTALL->CheckMysqlClientVersion ();
  $INSTALL->CheckMagicQuotes ();
  $INSTALL->CheckRegisterGlobals ();
  $INSTALL->CheckPhotoDirectory ();
  $INSTALL->CheckAttachmentDirectory ();
  $INSTALL->CheckSiteData ();
  $INSTALL->CheckHtaccessFinal ();
  
  if (!$INSTALL->ProcessPost ()) {
    $CurrentStep = 1;
  } else {
    $CurrentStep = 2;
  } // if
  
  if ($INSTALL->CheckError()) {
    $submit_disabled = "disabled=disabled";
    $submit_label = "Cannot Continue";
  } else {
    $submit_disabled = NULL;
    $submit_label = "Continue";
  } // if
  
/*---------------------------------------------------------------------*/
?>

<html>
<title>Appleseed Install Script</title>

<style type="text/css" media="screen">
 body { text-align:center; width:800px; font:10px arial; margin:10px auto; } 
 div#copyright { float:left; text-align:center; width:800px; margin:20px 0; }
 div.caption { float:left; width:796px; margin-left:10px; text-align:left; }
 div { text-align:left; }
 div.container { float:left; clear:both; width:800px; background-color:#fafafa; border:1px solid #cccccc; border-right:2px solid #999999; border-bottom:2px solid #999999; }
 div#check, div#completed, div#site, div#database {  float:left; clear:both; width:590px; margin:10px 100px; padding:5px; border:1px solid #cccccc; }
 p { float:left; clear:both; width:100%; border-bottom:1px solid #aaaaaa; }
 p.information { border:none; }
 p.done { border:none; border-top:1px solid #aaaaaa; margin-top:10px; padding-top:10px; }
 p.final { border:none; width:400px; margin:0 150px; }
 p.final a { text-decoration:none; float:left; clear:both; font-size:14px; color:#00ff00; background:#ccffcc; padding:2px 4px; }
 span.label, label { float:left; width:270px; margin:5px 0 5px 100px; }
 input, textarea { width:150px; margin:5px 50px 5px 0; font:10px Arial; color:#4c5055; background:#ecf0f5; vertical-align:top; border:1px solid #ccd0d5; padding:1px 3px; }
 select { float:left; width:150px; }
 input:hover, textarea:hover { background:#fafafa; color:#2a2a2a; border-color:#acb0b5 } 
 input.submit, input.refresh { float:right; width:auto; padding:2px 10px; margin:20px 98px; font-weight:bold; }
 div#confirmDatabaseConnection { float:left; clear:both; width:100%; padding:5px; text-align:center;}
 input#adminPassConfirm { margin-right:0; }
 div#checkPasswords { float:right; width:230px; text-align:right; padding:2px 0px; margin:0px 70px; font-weight:bold; }
 div#upgradeWarning { float:right; width:100%; color:#ff0000; display:none; text-align:right; padding:2px 0px; margin:0px 70px; font-weight:bold; }
 input.checkConnection { width:100px; color:#ff0000; margin:0; padding:0;}
 span.done, span.yes, span.no { float:left; width:8px; font-weight:bold; text-align:center; margin:5px 0px 5px 130px; padding:1px 3px; }
 span.done { width:auto; color:#00ff00; background:#ccffcc; border:1px solid #ccffcc; }
 span.yes { color:#00ff00; background:#ccffcc; border:1px solid #ccffcc; }
 span.no { color:#ff0000; background:#ffcccc; border:1px solid #ffcccc; }
 p.error {
  float:left;
  width:100%;
  color:#ffffff;
  cursor:crosshair;
  border:1px solid #a31a31;
  border-left:none;
  border-right:none;
  padding:1px 0;
  text-align:center;
  font-style:italic;
  background:#cc151a;
 }
</style>

<script>

    <?php if ($submit_disabled) { ?>
    var okPermanent = false;
    <?php } else { ?>
    var okPermanent = true;
    <?php } // if ?>
    
    var okPassword = false;
    var okDatabase = false;
    
    var confirmHost = false;
    var confirmDatabase = false;
    var confirmUsername = false;
    var confirmPassword = false;

	// Primes input elements so javascript degrades properly.
	function initialize() {
	    var inputs = document.getElementsByTagName('input');
	    
	    for ( i = 0; i < inputs.length; i++) {
	    	// Enable the 'Check Connection' button and attach the proper function.
	    	if (inputs[i].className == 'checkConnection') {
	    		inputs[i].disabled = false;
	    		inputs[i].onclick = function() {
	    			checkConnect();
	    			return (false);
	    		} // onclick
	    	} // if
	    	
	    	if ((inputs[i].className == 'gHOST') ||
	    		(inputs[i].className == 'gDATABASE') ||
	    		(inputs[i].className == 'gUSERNAME') ||
	    		(inputs[i].className == 'gPASSWORD')) {
	    			inputs[i].onkeyup = function() {
	    				resetCheck();
	    				return (true);
	    			} // onchange
	    	} // if
	    	
	    	// Add the onChange to each admin password field.
	    	if ((inputs[i].className == 'gADMINPASS') ||
	    		(inputs[i].className == 'gADMINPASSCONFIRM')) {
	    		inputs[i].onfocus = inputs[i].onkeyup = function() {
	    			matchPasswords();
	    			return (false);
	    		} // onclick
	    	} // if
	    	
	    	// Set the submit button to disabled.
	    	if (inputs[i].className == 'submit') {
	    		inputs[i].disabled = true;
	    		inputs[i].value = 'Cannot Continue';
	    		inputs[i].onclick = function() {
	    			finalValidation();
	    		} // if
	    	} // if
	    } // for
	    
	    upgrade = document.getElementById('gUPGRADE');
	    upgrade.onchange = function() {
	    	upgradeWarning();
	    	return (true);
	    } // upgrade
	    
	    matchPasswords();
	    upgradeWarning();
	    
	    return (false);
	} // initialize
	
	function upgradeWarning () {
		upgrade = document.getElementById('gUPGRADE');
		warning = document.getElementById('upgradeWarning');
		
		if (upgrade.value == 0) {
			warning.style.display = '';
		} else {
			warning.style.display = 'block';
		} // if
		
		return (true);
	} // upgradeWarning
	
	function finalValidation() {
	  form = document.forms['main'];
		submit = document.getElementById ('submit');
		
		submit.value = 'Please Wait...';
		submit.style.color = '#8a8a8a';
		submit.disabled = true;

		form.submit();

		return (true);
	} // if
	
	function matchPasswords() {
		submit = document.getElementById ('submit');
		pass = document.getElementById ('adminPass');
		passConfirm = document.getElementById ('adminPassConfirm');
		check = document.getElementById ('checkPasswords');
		
		// If either is null, don't show anything.
		if ( (pass.value == '') || (passConfirm.value == '') ) {
			check.innerHTML = '&nbsp;';
			check.style.color = '#2a2a2a';
			okPassword = false;
			return (false);
		} // if
		
		// If password is less than 6 characters, error.
		if (pass.value.length < 6) {
			check.innerHTML = 'Password must be at least 6 characters!';
			check.style.color = '#ff0000';
			okPassword = false;
			return (false);
		} // if
		
		if (pass.value == passConfirm.value) {
			check.innerHTML = 'Passwords match!';
			check.style.color = '#00ff00';
			okPassword = true;
		} else {
			check.innerHTML = 'Passwords do not match!';
			check.style.color = '#ff0000';
			okPassword = false;
		} // if
		
		if ((okPassword) && (okDatabase) && (okPermanent)) {
    		submit.disabled = false;
	    	submit.value = 'Continue';
		} else {
    		submit.disabled = true;
	    	submit.value = 'Cannot Continue';
		} // if
		
		return (true);
	} // matchPasswords
	
	function checkConnect() {
		var checkButton = document.getElementById('checkConnection');
		var submitButton = document.getElementById('submit');
		
		// Step 0: Create visual indicator something is happening.
		checkButton.style.color = '#4c5055';
		checkButton.style.background = '#ecf0f5';
		checkButton.value = 'Checking...';
		
		// Step 1: Create the query string.
	    var form = document.forms['main'];
	    var host = form.gHOST.value;
	    var database = form.gDATABASE.value;
	    var username = form.gUSERNAME.value;
	    var password = form.gPASSWORD.value;
	    
	    if ((!host) || (!database) || (!username) || (!password)) {
	    	alert ('Fill out database information before checking the connection!');
			checkButton.value = 'Check Connection';
			checkButton.style.color = '#ff0000';
	    	return (false);
	    } // if
	    
	    var queryString = 'gACTION=CHECK&gHOST=' + host + '&gDATABASE=' + database + '&gUSERNAME=' + username + '&gPASSWORD=' + password;
	    var URL = 'index.php';
	    
		// Step 2: Create the ajax variables.
	    var xmlHttpReq = false;
	    if (window.XMLHttpRequest)  {
	        var ajax = this.xmlHttpReq = new XMLHttpRequest();
	    } else if (window.ActiveXObject)  {
	        var ajax = this.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
	    } // if
	    
	    // Step 3: Send the request.
	    ajax.open('POST', URL, true);
	    ajax.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	    ajax.onreadystatechange = function() {
	        if (ajax.readyState == 4)  {
	            if (ajax.responseText == true) {
					checkButton.value = 'Connection OK';
					checkButton.style.color = '#00ff00';
					checkButton.style.background = '#ccffcc';
					
					confirmHost = host;
					confirmDatabase = database;
					confirmUsername = username;
					confirmPassword = password;
					
					okDatabase = true;
					if ((okPassword) && (okDatabase) && (okPermanent)) {
    					submitButton.disabled = false;
	    				submitButton.value = 'Continue';
					} // if
	            } else {
	            	alert ('Connection to database failed!');
					checkButton.value = 'Check Connection';
					checkButton.style.color = '#ff0000';
					okDatabase = false;
    				submitButton.disabled = true;
	    			submitButton.value = 'Cannot Continue';
	            } // if
	        } // if
	    } // if
	    ajax.send(queryString);
		
		return (false);
	} // checkConnect
	
	function resetCheck () {
		var submitButton = document.getElementById('submit');
		var checkButton = document.getElementById('checkConnection');
		
	    var form = document.forms['main'];
	    var host = form.gHOST.value;
	    var database = form.gDATABASE.value;
	    var username = form.gUSERNAME.value;
	    var password = form.gPASSWORD.value;
	    
	    if ( (host == confirmHost) &&
	    	 (database == confirmDatabase) &&
	    	 (username == confirmUsername) &&
	    	 (password == confirmPassword) ) {
			checkButton.value = 'Connection OK';
			checkButton.style.color = '#00ff00';
			checkButton.style.background = '#ccffcc';
			okDatabase = true;
			if ((okPassword) && (okDatabase) && (okPermanent)) {
   				submitButton.disabled = false;
	  			submitButton.value = 'Continue';
			} // if
	     } else {
			checkButton.value = 'Check Connection';
			checkButton.style.color = '#ff0000';
			checkButton.style.background = '#ecf0f5';
			okDatabase = false;
 			submitButton.disabled = true;
			submitButton.value = 'Cannot Continue';
	     } // if
		
		return (true);
	} // resetCheck
</script>

<?php if ($CurrentStep == 1) $INSTALL->ViewStepOne (); ?>
<?php if ($CurrentStep == 2) $INSTALL->ViewStepTwo (); ?>

<?php
/*---------------------------------------------------------------------*/

// Installation Class
class cINSTALL {
  
  function CheckPHPVersion () {
    global $Error, $ErrorMark;
    
    // Check for PHP version 5.0.0 or higher.
    $required = '5.0.0';
    if(!function_exists(version_compare)) {
      $Error['php_version'] = TRUE;
      $ErrorMark['php_version'] = "<span class='no'>N</span>";
    } else {
      if (version_compare ($required, phpversion()) < 0) {
        $Error['php_version'] = FALSE;
        $ErrorMark['php_version'] = "<span class='yes'>Y</span>";
      } else {
        $Error['php_version'] = TRUE;
        $ErrorMark['php_version'] = "<span class='no'>N</span>";
      } // if
    } // if
    
    return (TRUE);
  } // CheckPHPVersion
  
  function CheckMysqlClientVersion () {
    global $Error, $ErrorMark;
    
    $version = $this->GetMysqlClientVersion();
    
    $VersionArray = explode ('.', $version);
    $main = $VersionArray[0];
    $minor = $VersionArray[1];
    $micro = $VersionArray[2];
    
    if ($main > 4) {
      $Error['mysql_client_version'] = FALSE;
      $ErrorMark['mysql_client_version'] = "<span class='yes'>Y</span>";
    } else {
      $Error['mysql_client_version'] = TRUE;
      $ErrorMark['mysql_client_version'] = "<span class='no'>N</span>";
    } // if
    
    return (TRUE);
  } // CheckMysqlClientVersion
  
  function GetMysqlClientVersion () {
  	
  	$return = mysql_get_client_info ();
  	
    return ($return);
  } // GetMysqlClientVersion
  
  function CheckMysqlServerVersion () {
    global $Error, $ErrorMark;
    
    $version = $this->GetMysqlServerVersion();
    
    $VersionArray = explode ('.', $version);
    $main = $VersionArray[0];
    $minor = $VersionArray[1];
    $micro = $VersionArray[2];
    
    if ($main >= 5) {
      $Error['mysql_server_version'] = FALSE;
      $ErrorMark['mysql_server_version'] = "<span class='yes'>Y</span>";
    } else {
      $Error['mysql_server_version'] = TRUE;
      $ErrorMark['mysql_server_version'] = "<span class='no'>N</span>";
    } // if
    
    return (TRUE);
  } // CheckMysqlServerVersion
  
  function GetMysqlServerVersion () {
  	
  	$return = mysql_get_server_info ();
  	
    return ($return);
  } // GetMysqlServerVersion
  
  function CheckMagicQuotes () {
    global $Error, $ErrorMark;
    
    // Check if register_globals is turned off.
    if ((ini_get ('magic_quotes_gpc')) or (ini_get('magic_quotes_runtime'))) {
      $Error['magic_quotes'] = TRUE;
      $ErrorMark['magic_quotes'] = "<span class='no'>N</span>";
    } else {
      $Error['magic_quotes'] = FALSE;
      $ErrorMark['magic_quotes'] = "<span class='yes'>Y</span>";
    } // if
  
    return (TRUE);
  } // CheckMagicQuotes
  
  function CheckRegisterGlobals () {
    global $Error, $ErrorMark;
    
    // Check if register_globals is turned off.
    if (ini_get ('register_globals')) {
      $Error['register_globals'] = TRUE;
      $ErrorMark['register_globals'] = "<span class='no'>N</span>";
    } else {
      $Error['register_globals'] = FALSE;
      $ErrorMark['register_globals'] = "<span class='yes'>Y</span>";
    } // if
  
    return (TRUE);
  } // CheckRegisterGlobals
  
  function CheckAttachmentDirectory () {
    global $Error, $ErrorMark;
    
    // Check if photo directory is writable.
    if (!is_writable (getcwd() . '/attachments/')) {
      $Error['attachment_directory'] = TRUE;
      $ErrorMark['attachment_directory'] = "<span class='no'>N</span>";
    } else {
      $Error['attachment_directory'] = FALSE;
      $ErrorMark['attachment_directory'] = "<span class='yes'>Y</span>";
    } // if
    
    return (TRUE);
  } // CheckAttachmentDirectory
  
  function CheckPhotoDirectory () {
    global $Error, $ErrorMark;
    
    // Check if photo directory is writable.
    if (!is_writable (getcwd() . '/photos/')) {
      $Error['photo_directory'] = TRUE;
      $ErrorMark['photo_directory'] = "<span class='no'>N</span>";
    } else {
      $Error['photo_directory'] = FALSE;
      $ErrorMark['photo_directory'] = "<span class='yes'>Y</span>";
    } // if
    
    return (TRUE);
  } // CheckPhotoDirectory
  
  function CheckSiteData () {
    global $Error, $ErrorMark;
    
    // Check if site.adat file is writable.
    if (!is_writable (getcwd() . '/data/site.adat')) {
      $Error['site_data'] = TRUE;
      $ErrorMark['site_data'] = "<span class='no'>N</span>";
    } else {
      $Error['site_data'] = FALSE;
      $ErrorMark['site_data'] = "<span class='yes'>Y</span>";
    } // if
    
    return (TRUE);
  } // CheckSiteData
  
  function CheckHtaccessFinal () {
    global $Error, $ErrorMark;
    
    // Check if htaccess.final is writable.
    if (!is_writable (getcwd() . '/' . '.htaccess')) {
      $Error['htaccess_final'] = TRUE;
      $ErrorMark['htaccess_final'] = "<span class='no'>N</span>";
    } else {
      $Error['htaccess_final'] = FALSE;
      $ErrorMark['htaccess_final'] = "<span class='yes'>Y</span>";
    } // if
    
    return (TRUE);
  } // CheckHtaccessFinal
  
  function WriteSiteData ($pDATABASE, $pUSERNAME, $pPASSWORD, $pPREFIX, $pVERSION, $pHOST, $pDOMAIN) {
    $site_data = "data/site.adat";
    
    $filedata = "db:$pDATABASE\n" .
                "un:$pUSERNAME\n" .
                "pw:$pPASSWORD\n" .  
                "pre:$pPREFIX\n" .  
                "ver:$pVERSION\n" .
                "host:$pHOST\n" .
                "url:$pDOMAIN";
                
    // Open file for writing.
    if (!$file = fopen($site_data, 'w')) {
      global $Error, $ErrorMark;
      $Error['site_data'] = TRUE;
      $ErrorMark['site_data'] = "<span class='no'>N</span>";
      return (FALSE);
    } // if
    
    fwrite($file, $filedata);
    fclose($file);
    
    return (TRUE);
  } // WriteSiteData
  
  function PreLoadSiteData () {
    global $gDATABASE, $gUSERNAME, $gPASSWORD, $gPREFIX, $gHOST, $gDOMAIN;
    
    $site_data = "data/site.adat";
    
    // Open file for reading.
    if (!$file = fopen($site_data, 'r')) {
      return (FALSE);
    } // if
    
    $buffer = NULL;
    while (!feof($file)) {
        $buffer .= fgets($file, 4096);
    }
    
    $SiteDataArray = split ("\n", $buffer);
    foreach ($SiteDataArray as $line => $value) {
      list ($type, $val) = explode (":", $value, 2);
      $SiteDataValues[$type] = $val;
    } // foreach
    
    $gDATABASE = $SiteDataValues['db'];  
    $gUSERNAME = $SiteDataValues['un']; 
    $gPASSWORD = $SiteDataValues['pw']; 
    $gPREFIX = $SiteDataValues['pre']; 
    $gHOST = $SiteDataValues['host']; 
    $gDOMAIN = $SiteDataValues['url'];
    
    // Set as default.
    if (!$gPREFIX) $gPREFIX = 'asd_';
    
    return (TRUE);
  } // PreLoadSiteData;
  
  function WriteHtaccess () {
    $htaccess_original = "htaccess.original";
    $htaccess_final = ".htaccess";
    
    // Open file for reading.
    if (!$original_file = fopen($htaccess_original, 'r')) {
      return (FALSE);
    } // if
    
    $original = NULL;
    while (!feof($original_file)) {
        $original .= fgets($original_file, 4096);
    }
    
    fclose ($original_file);
    
    // Open file for writing.
    if (!$final_file = fopen($htaccess_final, 'w')) {
      return (FALSE);
    } // if
    
    fwrite($final_file, $original);
    fclose ($final_file);
    
    return (TRUE);
  } // WriteHtaccess
  
  function ImportData ($pUSERNAME, $pPASSWORD, $pHOST, $pDATABASE, $pPREFIX, $pUPGRADE= FALSE) {
    global $ErrorString;
    
    $sql_install = "install.sql";
    
    // Open file for reading.
    if (!$sql_file = fopen($sql_install, 'r')) {
      return (FALSE);
    } // if
    
    @mysql_select_db ($pDATABASE);
    
    $sql = NULL;
    while (!feof($sql_file)) {
        $sql .= fgets($sql_file, 4096);
    } // while
    
    $sql = str_replace ("%PREFIX%", $pPREFIX, $sql);
    
    $sql_lines = preg_split('/[\n\r]+/',$sql);
    $result = true;
    
    // Perform backup first.
    if (($pUPGRADE == 1) or ($pUPGRADE == 2)) {
    	foreach ($sql_lines as $l => $line) {
    		$commands = split (' ', $line);
    		$command = $commands[0];
    		if ($command != 'DROP') continue;
    		$table = str_replace ('`', '', $commands[4]);
    		$table = str_replace (';', '', $table);
    		if (!$this->BackupTable ($table)) {
   				$ErrorString = "Table backup could not be completed";
   				return (FALSE);
    		} // if
    	} // foreach
    } // if 
    
    foreach ($sql_lines as $l => $line) {
    	if ($line == '') continue;
    	$commands = split (' ', $line);
    	$command = $commands[0];
    	switch ($command) {
    		case 'DROP':
    			$table = str_replace ('`', '', $commands[4]);
    			$table = str_replace (';', '', $table);
    			@mysql_query ("LOCK TABLES `$table` WRITE");
    			$result = @mysql_query ($line);
    			@mysql_query ("UNLOCK TABLES");
    		break;
    		case 'CREATE':
    			$table = str_replace ('`', '', $commands[2]);
    			$result = @mysql_query ($line);
    			$tableList[$table] = $table;
    		break;
    		case 'INSERT':
    			$table = str_replace ('`', '', $commands[2]);
   		 		$result = @mysql_query ($line);
    		break;
    	} // switch
    	if (!$result) {
    		$ErrorString = "MYSQL ERROR: " . mysql_error();
    		return (FALSE);
    	} // if
    } // foreach
    
    // Upgrade tables.
    if (($pUPGRADE == 1) or ($pUPGRADE == 2)) {
    	$this->UpgradeTables($tableList);
    } // if
    
    // Delete backup tables if necessary.
    if ($pUPGRADE == 2) {
    	foreach ($sql_lines as $l => $line) {
    		$commands = split (' ', $line);
    		$command = $commands[0];
    		if ($command != 'DROP') continue;
    		$table = str_replace ('`', '', $commands[4]);
    		$table = str_replace (';', '', $table);
    		$this->DropBackupTable ($table);
    	} // foreach
    } // if 
    
    return (TRUE);
  } // ImportData
  
  function UpgradeTables ($pTABLENAMES) {
  	
  	foreach ($pTABLENAMES as $table) {
  		$this->UpgradeTable ($table);
  	} // foreach
  	
  	return (true);
  } // UpgradeTables
  
  function UpgradeTable ($pTABLENAME) {
  	global $gSTAMP;
  	$backupTable = $gSTAMP . '_' . $pTABLENAME;
  	
  	// Grab the fields from the new table.
  	$query = "
		SHOW FIELDS FROM `$pTABLENAME`;
	";
	
	$link = @mysql_query ($query);
	while ($result = @mysql_fetch_object ($link)) {
		$newFields[$result->Field] = $result;
	}
	
  	// Grab the fields from the old table.
  	$query = "
		SHOW FIELDS FROM `$backupTable`;
	";
	
	$link = @mysql_query ($query);
	while ($result = @mysql_fetch_object ($link)) {
		$oldFields[$result->Field] = $result;
	}
	
	// Determine the common fields between them.
	foreach ($newFields as $newFieldName=> $newField) {
		if ($newField->Key == 'PRI') $newPrimaryKey = $newFieldName;
		
		foreach ($oldFields as $oldFieldName => $oldField) {
			if ($oldField->Key == 'PRI') $oldPrimaryKey = $oldFieldName;
			if ($newField == $oldField) {
				$commonFields[$newFieldName] = $oldField;
				$insertFields[] = $oldFieldName;
				$selectFields[] = 'first.' . $oldFieldName;
			} // if
		} // foreach
	} // foreach
	
	// If the primary keys are different, we can't merge.
	if ($oldPrimaryKey != $newPrimaryKey) return (false);
	
	$insert = join (', ', $insertFields);
	$select = join (', ', $selectFields);
	
	// Merge the old data into the new one.
	$query = "
		REPLACE INTO `$pTABLENAME` ($insert) 
		SELECT $select FROM `$backupTable` AS first
	";
	
	$link = @mysql_query ($query);
	
  	return (TRUE);
  } // UpgradeTable
  
  function BackupTable ($pTABLENAME) {
  	global $gSTAMP;
  	$backupTable = $gSTAMP . '_' . $pTABLENAME;
  	
  	$query = "
		DESC `$pTABLENAME`
	";
	
	// Return if table doesn't already exist.
    if (!$link = @mysql_query ($query)) return (TRUE);
  	
  	// Copy the structure.
  	$query = "
	  CREATE TABLE `$backupTable` LIKE `$pTABLENAME`
	";
	
	// Error out if we couldn't backup the table.
    if (!$link = @mysql_query ($query)) {
    	return (FALSE);
    }
  	
  	// Copy the data.
  	$query = "
	  INSERT INTO `$backupTable` SELECT * FROM `$pTABLENAME`
	";
	
	// Error out if we couldn't backup the table.
    if (!$link = @mysql_query ($query)) {
    	return (FALSE);
    }
    
  	return (TRUE);
  } // BackupTable
  
  function DropBackupTable ($pTABLENAME) {
  	global $gSTAMP;
  	$backupTable = $gSTAMP . '_' . $pTABLENAME;
  	
  	$query = "
		DROP TABLE IF EXISTS `$backupTable`
	";
	
    $link = @mysql_query ($query);
  	
  	return (TRUE);
  } // DropBackupTable
  
  function UpdateAdminUserPass ($pADMINUSER, $pADMINPASS) {
    global $gDATABASE, $gPREFIX;
    global $MysqlLink;
    
    $sql = "UPDATE " . $gPREFIX . "userAuthorization SET Username='$pADMINUSER', Pass=PASSWORD('$pADMINPASS') WHERE uID=1";
    
    mysql_select_db ($gDATABASE);
    mysql_query ($sql);
    
    return (TRUE);
  } // UpdateAdminUserPass
  
  function CreateTables () {
  } // CreateTables
  
  function ValidateForm () {
    global $MysqlLink;
    global $Error, $ErrorString;
    
    if (empty ($_POST)) return (FALSE);
    
    global $gDATABASE, $gUSERNAME, $gPASSWORD, $gPREFIX, $gHOST, $gDOMAIN;
    global $gADMINUSER, $gADMINPASS, $gADMINPASSCONFIRM;
    
    // Check if necessary values have been set.
    if (!$gDATABASE) {
      $ErrorString = "No database name was given.";
      return (FALSE);
    } // if
    
    if (!$gHOST) {
      $ErrorString = "No database host name was given.";
      return (FALSE);
    } // if
    
    if (!$gUSERNAME){
      $ErrorString = "No database username was given.";
      return (FALSE);
    } // if 
    
    if (!$gPASSWORD){
      $ErrorString = "No database password was given.";
      return (FALSE);
    } // if 
    
    if (!$gDOMAIN){
      $ErrorString = "No site domain was given.";
      return (FALSE);
    } // if 
    
    if (!$gADMINUSER) {
      $ErrorString = "No admin username was given.";
      return (FALSE);
    } // if
    
    if (!$gADMINPASS) {
      $ErrorString = "No admin password was given.";
      return (FALSE);
    } // if
    
    if (strlen ($gADMINPASS) < 6) {
      $ErrorString = "Administrator password must be at least 6 characters.";
      return (FALSE);
    } // if
    
    if ($gADMINPASS != $gADMINPASSCONFIRM) {
      $ErrorString = "Administrator passwords do not match.";
      return (FALSE);
    } // if
    
    if (!$MysqlLink = @mysql_connect ($gHOST, $gUSERNAME, $gPASSWORD)) {
      $ErrorString = "Could not connect to mysql database.";
      return (FALSE);
    } // if
    
    if (!file_exists ("install.sql")) {
      $ErrorString = "Could not find install.sql for importing.";
      return (FALSE);
    } // if
    
    $this->CheckMysqlServerVersion ();
    if ($Error['mysql_server_version']) {
      $ErrorString = "Selected MySQL server version is too low. (Version is " . $this->GetMysqlServerVersion() . ", Appleseed requires > 5.0)";
      return (FALSE);
    } // if
    
    if (!is_readable ("install.sql")) {
      $ErrorString = "Could not open install.sql for reading.";
      return (FALSE);
    } // if
    
    return (TRUE);
  } // ValidateForm
  
  function ProcessPost () {
    global $MysqlLink;
    
    // Check if form has not been submitted.
    if ((empty ($_POST)) or ($_POST['refresh'] == 'Refresh'))   return (FALSE);
    
    // Check if form input validates.
    if (!$this->ValidateForm()) return (FALSE);
    
    global $gDATABASE, $gUSERNAME, $gPASSWORD, $gPREFIX, $gHOST, $gDOMAIN;
    global $gADMINUSER, $gADMINPASS, $gUPGRADE;
    
    if (!$this->WriteSiteData ($gDATABASE, $gUSERNAME, $gPASSWORD, $gPREFIX, '0.7.3', $gHOST, $gDOMAIN)) return (FALSE);
    if (!$this->WriteHtaccess ()) return (FALSE);
    if (!$this->ImportData ($gUSERNAME, $gPASSWORD, $gHOST, $gDATABASE, $gPREFIX, $gUPGRADE)) return (FALSE);
    if (!$this->UpdateAdminUserPass ($gADMINUSER, $gADMINPASS)) return (FALSE);
    
    return (TRUE);
  } // ProcessPost
  
  function CheckError () {
    global $Error, $ErrorString;
    
    if ($ErrorString) $ErrorString = "<p class='error'>$ErrorString</p>";
    
    if (in_array(TRUE, $Error)) return (TRUE);
    
    return (FALSE);
    
  } // CheckError
  
  function ViewStepOne () {
    
    global $Error, $ErrorString, $ErrorMark;
    global $gDATABASE, $gUSERNAME, $gPASSWORD, $gPREFIX, $gHOST, $gDOMAIN, $gUPGRADE;
    global $gADMINUSER, $gADMINPASS, $gADMINPASSCONFIRM;
    global $submit_label, $submit_disabled;
    
    ?>
        
    <body onload='initialize();'>
     <div id='install'>
      <div class='caption'>APPLESEED INSTALL v0.7.3</div>
      <div class='container'>
      
       <?php echo $ErrorString; ?>
       <div id='check'>
        <p class='title'>System Check</p>
        <p class='information'>This system cannot be installed until the following conditions are met.</p>
        
        <span class='label'>Installed in site root directory? (see documentation)</span>
        <?php echo $ErrorMark['sub_dir']; ?>
        
        <span class='label'>Is magic_quotes turned off?</span>
        <?php echo $ErrorMark['magic_quotes']; ?>
        
        <span class='label'>Is register_globals turned off?</span>
        <?php echo $ErrorMark['register_globals']; ?>
        
        <span class='label'>Is the photos/ directory writable?</span>
        <?php echo $ErrorMark['photo_directory']; ?>
        
        <span class='label'>Is the attachments/ directory writable?</span>
        <?php echo $ErrorMark['attachment_directory']; ?>
        
        <span class='label'>Is data/site.adat writable?</span>
        <?php echo $ErrorMark['site_data']; ?>
        
        <span class='label'>Is .htaccess writable?</span>
        <?php echo $ErrorMark['htaccess_final']; ?>
        
        <span class='label'>PHP version 5.0 or higher? (Running <?php echo phpversion(); ?>)</span>
        <?php echo $ErrorMark['php_version']; ?>
        
        <span class='label'>Mysql version 5.0 or higher? (Client is <?php echo $this->GetMysqlClientVersion (); ?>)</span>
        <?php echo $ErrorMark['mysql_client_version']; ?>
        
       </div>
       <form id='main' name='main' method='POST' action='/'>
        <input type='submit' name='refresh' class='refresh' value="Refresh" />
       
       <div id='database'>
        <p class='title'>Database Settings</p>
        <p class='information'>Enter your database information in the following fields.</p>
         <label for='gHOST'>DB Host Name:</label>
         <input type='text' class='gHOST' name='gHOST' value='<?php echo $gHOST; ?>' />
         
         <label for='gDATABASE'>DB Name:</label>
         <input type='text' class='gDATABASE' name='gDATABASE' value='<?php echo $gDATABASE; ?>' />
         
         <label for='gUSERNAME'>DB Username:</label>
         <input type='text' class='gUSERNAME' name='gUSERNAME' value='<?php echo $gUSERNAME; ?>' />
         
         <label for='gPASSWORD'>DB Password:</label>
         <input type='text' class='gPASSWORD' name='gPASSWORD' value='<?php echo $gPASSWORD; ?>' />
         
         <label for='gPREFIX'>DB Table Prefix:</label>
         <input type='text' name='gPREFIX' value='<?php echo $gPREFIX; ?>' />
     
     	 <div id='confirmDatabaseConnection'>
       	  <input type='submit' id='checkConnection' name='checkConnection' class='checkConnection' value="Check Connection" disabled=disabled />
       	 </div>
     
         <label for='gUPGRADE'>Upgrade if tables exist?</label>
         <select id='gUPGRADE' name='gUPGRADE'>
         	<option <?php if ($gUPGRADE == '0') echo "selected"; ?> value='0'>No (Delete Existing)</option>
         	<option <?php if ($gUPGRADE == '1') echo "selected"; ?> value='1'>Yes (Backup Tables)</option>
         	<option <?php if ($gUPGRADE == '2') echo "selected"; ?> value='2'>Yes (No Backup)</option>
         </select>
         
     	 <div id='upgradeWarning'>
     	 	Upgrading tables is still experimental.  Please use at your own risk.
       	 </div>
         
       </div> <!-- #database -->
       
       <div id='site'>
        <p class='title'>Site Settings</p>
        <p class='information'>Enter your site setup information.</p>
         <label for='gDOMAIN'>Site Domain:</label>
         <input type='text' name='gDOMAIN' value='<?php echo $gDOMAIN; ?>' />
         
         <label for='gADMINUSER'>Default Admin Username:</label>
         <input type='text' name='gADMINUSER' value='<?php echo $gADMINUSER; ?>' />
     
         <label for='gADMINPASS'>Default Admin Password:</label>
         <input type='text' maxlength=20 id='adminPass' class='gADMINPASS' name='gADMINPASS' value='<?php echo $gADMINPASS; ?>' />
     
         <label for='gADMINPASSCONFIRM'>Default Admin Password (Confirm):</label>
         <input type='text' maxlength=20 id='adminPassConfirm' class='gADMINPASSCONFIRM' name='gADMINPASSCONFIRM' value='<?php echo $gADMINPASSCONFIRM; ?>' />
         
         <div id='checkPasswords'>&nbsp;</div>
     
       </div> <!-- #site -->
         <input type='submit' id='submit' name='submit' class='submit' <?php echo $submit_disabled; ?> value="<?php echo $submit_label; ?>" />
       </form> <!-- #main -->
   
      </div> <!-- .container -->
     </div> <!-- .install -->
     <div id='copyright'>
      Copyright &copy; 2004-2010 by Michael Chisari under the <a href='http://www.gnu.org/licenses/gpl-2.0.html'>GNU GPL Version 2</a>. All Rights Reversed.
     </div>
    </body>
    </html> <?php
    
  } // ViewStepOne
  
  function ViewStepTwo () {
    
    global $Error, $ErrorString, $ErrorMark;
    global $gDATABASE, $gUSERNAME, $gPASSWORD, $gPREFIX, $gHOST, $gDOMAIN;
    global $gADMINUSER, $gADMINPASS, $gADMINPASSCONFIRM;
    global $submit_label, $submit_disabled;
    global $MysqlLink;
    
    ?>
        
    <body>
     <div id='install'>
      <div class='caption'>APPLESEED INSTALL v0.7.0</div>
      <div class='container'>
      
       <?php echo $ErrorString; ?>
       <div id='check'>
        <p class='title'>System Check</p>
        <p class='information'>This system cannot be installed until the following conditions are met.</p>
        
        <span class='label'>Installed in root directory? (see documentation)</span>
        <?php echo $ErrorMark['sub_dir']; ?>
        
        <span class='label'>Is magic_quotes turned off?</span>
        <?php echo $ErrorMark['magic_quotes']; ?>
        
        <span class='label'>Is register_globals turned off?</span>
        <?php echo $ErrorMark['register_globals']; ?>
        
        <span class='label'>Is the photos/ directory writable?</span>
        <?php echo $ErrorMark['photo_directory']; ?>
        
        <span class='label'>Is the attachments/ directory writable?</span>
        <?php echo $ErrorMark['attachment_directory']; ?>
        
        <span class='label'>Is data/site.adat writable?</span>
        <?php echo $ErrorMark['site_data']; ?>
        
        <span class='label'>Is .htaccess writable?</span>
        <?php echo $ErrorMark['htaccess_final']; ?>
        
        <span class='label'>PHP version 5.0 or higher? (Running <?php echo phpversion(); ?>)</span>
        <?php echo $ErrorMark['php_version']; ?>
        
        <span class='label'>Mysql version 5.0 or higher? (Running <?php echo $this->GetMysqlClientVersion (); ?>)</span>
        <?php echo $ErrorMark['mysql_client_version']; ?>
       </div> <!-- #check -->
       
      <div id='completed'>
        <p class='title'>Completing Installation</p>
        <p class='information'>Creating database tables and initializing Appleseed.</p>
        
        <span class='label'>Writing site data to file</span>
        <span class='done'>DONE</span>
        
        <span class='label'>Creating .htaccess file</span>
        <span class='done'>DONE</span>
        
        <span class='label'>Creating Appleseed Tables</span>
        <span class='done'>DONE</span>
        
        <span class='label'>Importing Initial Data</span>
        <span class='done'>DONE</span>
        
        <span class='label'>Creating Administrator Account</span>
        <span class='done'>DONE</span>
        
        <p class='done'>
        Your Appleseed installation is now complete!  Be sure to 
        change the permissions on <b>.htaccess</b> and <b>site.adat</b> 
        so that they aren't writable anymore, and delete the install
        script (index.php) from the appleseed root directory. </p>
        
        <p class='final'><a href='<?php echo $gDOMAIN; ?>/login/'>Click Here To Login To Your Appleseed Site</a></p>
        
      </div> <!-- #completed -->
      </div> <!-- .container -->
      
     </div> <!-- .install -->
     <div id='copyright'>
      Copyleft &copy; 2004-2008 by the Appleseed Collective. All Rights Reversed.
     </div>
    </body>
    </html> <?php
    
  } // ViewStepTwo
  
  function CheckForSubDirectory () {
    global $Error, $ErrorMark;
    
    $subdir = $_SERVER['REQUEST_URI'];
    if ($subdir != '/') {
      $Error['sub_dir'] = TRUE;
      $ErrorMark['sub_dir'] = "<span class='no'>N</span>";
    } else {
      $Error['sub_dir'] = FALSE;
      $ErrorMark['sub_dir'] = "<span class='yes'>Y</span>";
    } // if
    
    return (TRUE);
  } // CheckForSubDirectory
  
} // cINSTALL
?>
