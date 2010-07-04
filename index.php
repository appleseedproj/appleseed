<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: index.php                               CREATED: 04-12-2007 + 
  // | LOCATION: /                                  MODIFIED: 07-03-2010 +
  // +-------------------------------------------------------------------+
  // | Copyright (c) 2004-2010 Appleseed Project                         |
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
  // | VERSION:      0.7.4                                               |
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

	list ( $gCUSTOM ) = explode ('.', $_SERVER['HTTP_HOST']);
  
  $INSTALL = new cINSTALL;
  
  $INSTALL->PreLoadSiteData ();

  $gDATABASE = ($_POST['gDATABASE']) ? $_POST['gDATABASE'] : $gDATABASE;
  $gUSERNAME = ($_POST['gUSERNAME']) ? $_POST['gUSERNAME'] : $gUSERNAME;
  $gPASSWORD = ($_POST['gPASSWORD']);
  $gPREFIX = ($_POST['gPREFIX']) ? $_POST['gPREFIX'] : $gPREFIX;
  $gHOST = ($_POST['gHOST']) ? $_POST['gHOST'] : $gHOST;
  $gHOST = ($gHOST) ? $gHOST : 'localhost';
  $gDOMAIN = ($_POST['gDOMAIN']) ? $_POST['gDOMAIN'] : $gDOMAIN;
  $gDOMAIN = ($gDOMAIN) ? $gDOMAIN : 'http://' . $_SERVER['HTTP_HOST'];
  $gUPGRADE = $_POST['gUPGRADE'];
  $gADMINUSER = ($_POST['gADMINUSER']) ? $_POST['gADMINUSER'] : 'Admin';
  $gADMINPASS = ($_POST['gADMINPASS']);
  $gADMINPASSCONFIRM = ($_POST['gADMINPASSCONFIRM']);
  
  $gSTAMP = '_' . date ('mdy_His', strtotime ('now'));
  
  // Add http:// to Domain if not already there.
  if (strtolower(substr ($gDOMAIN, 0, 7)) != 'http://') $gDOMAIN = 'http://' . $gDOMAIN;
  
  $INSTALL->CheckForSubDirectory ();
  $INSTALL->CheckPHPVersion ();
  $INSTALL->CheckMysqlClientVersion ();
  $INSTALL->CheckRegisterGlobals ();
  $INSTALL->CheckStorageDirectory ();
  $INSTALL->CheckConfigurationWritable ();
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
<head>
	<meta charset="utf-8" />
  <title>Appleseed 0.7.4 Install Script</title>
</head>

<style type="text/css" media="screen">
body{font:13px/1.231 arial,helvetica,clean,sans-serif;*font-size:small;*font:x-small;}select,input,button,textarea,button{font:99% arial,helvetica,clean,sans-serif;}table{font-size:inherit;font:100%;}pre,code,kbd,samp,tt{font-family:monospace;*font-size:108%;line-height:100%;}
html { background-color: #fff; color: #000; }

p, nav, li, img { padding: 10px 0 0 0; }

header hgroup h1 { font-size: 200%; }
header hgroup h2 { font-size: 120%; }
header nav, #page nav { width: 100%; min-height: 20px; }
header nav ul li, #page nav ul li, #user_comments nav ol li, #admin_page nav ol li { float: left; padding: 0px 10px 0px 0px; }

#info ul { margin:0 0 0 30px; padding:0; }
#info ul li { margin:0; padding:0; }

table { width: 100%; margin: 10px 0px; }
table th, table td { padding: 5px; text-align: center; }
table th[scope="col"] { border-bottom: 1px solid #000; }

header, section, footer { padding: 10px 0; }

#page { padding: 10px; }
#page section h1 { font-size: 140%; }
#page section li h1 { font-size: 110%; }
#page section hgroup h1 { font-size: 200%; margin: 0px 0px 10px 0px; }
#page section hgroup h2 { font-size: 140%; }

#page fieldset { border: 1px solid #000; padding: 0px 10px 10px 10px; margin: 10px 0px; }
#page fieldset legend { font-weight: bold; padding: 0 4px; }
#page form table th,
#page form table td { vertical-align: top; }
#page form table th { text-align: right; font-weight: bold; }
#page form table td { text-align: left; }
#page fieldset legend em,
#page fieldset label em {
    color: #ff0000;
}

#page_left nav ul li, #page_left nav ol li { float: none; }

time { font-size: 85%; }

span.done, .yes { padding:1px 2px; color:#ffffff; border:1px solid #007700; background-color:#009900; }
.no { padding:1px 2px; color:#ffffff; border:1px solid #770000; background-color:#990000; }

p.error { width:96%; clear:both; padding:3px 2%; margin:2px 0; background-color:#639a00; background-color:#bf4630; color:#f1ffd6; }
i.warning { font-size:12px; font-style:italic; color:#bf4630; }

footer ul li { list-style: none; }
.container_12,.container_16{margin-left:auto;margin-right:auto;width:960px}.grid_1,.grid_2,.grid_3,.grid_4,.grid_5,.grid_6,.grid_7,.grid_8,.grid_9,.grid_10,.grid_11,.grid_12,.grid_13,.grid_14,.grid_15,.grid_16{display:inline;float:left;margin-left:10px;margin-right:10px}.push_1,.pull_1,.push_2,.pull_2,.push_3,.pull_3,.push_4,.pull_4,.push_5,.pull_5,.push_6,.pull_6,.push_7,.pull_7,.push_8,.pull_8,.push_9,.pull_9,.push_10,.pull_10,.push_11,.pull_11,.push_12,.pull_12,.push_13,.pull_13,.push_14,.pull_14,.push_15,.pull_15{position:relative}.container_12 .grid_3,.container_16 .grid_4{width:220px}.container_12 .grid_6,.container_16 .grid_8{width:460px}.container_12 .grid_9,.container_16 .grid_12{width:700px}.container_12 .grid_12,.container_16 .grid_16{width:940px}.alpha{margin-left:0}.omega{margin-right:0}.container_12 .grid_1{width:60px}.container_12 .grid_2{width:140px}.container_12 .grid_4{width:300px}.container_12 .grid_5{width:380px}.container_12 .grid_7{width:540px}.container_12 .grid_8{width:620px}.container_12 .grid_10{width:780px}.container_12 .grid_11{width:860px}.container_16 .grid_1{width:40px}.container_16 .grid_2{width:100px}.container_16 .grid_3{width:160px}.container_16 .grid_5{width:280px}.container_16 .grid_6{width:340px}.container_16 .grid_7{width:400px}.container_16 .grid_9{width:520px}.container_16 .grid_10{width:580px}.container_16 .grid_11{width:640px}.container_16 .grid_13{width:760px}.container_16 .grid_14{width:820px}.container_16 .grid_15{width:880px}.container_12 .prefix_3,.container_16 .prefix_4{padding-left:240px}.container_12 .prefix_6,.container_16 .prefix_8{padding-left:480px}.container_12 .prefix_9,.container_16 .prefix_12{padding-left:720px}.container_12 .prefix_1{padding-left:80px}.container_12 .prefix_2{padding-left:160px}.container_12 .prefix_4{padding-left:320px}.container_12 .prefix_5{padding-left:400px}.container_12 .prefix_7{padding-left:560px}.container_12 .prefix_8{padding-left:640px}.container_12 .prefix_10{padding-left:800px}.container_12 .prefix_11{padding-left:880px}.container_16 .prefix_1{padding-left:60px}.container_16 .prefix_2{padding-left:120px}.container_16 .prefix_3{padding-left:180px}.container_16 .prefix_5{padding-left:300px}.container_16 .prefix_6{padding-left:360px}.container_16 .prefix_7{padding-left:420px}.container_16 .prefix_9{padding-left:540px}.container_16 .prefix_10{padding-left:600px}.container_16 .prefix_11{padding-left:660px}.container_16 .prefix_13{padding-left:780px}.container_16 .prefix_14{padding-left:840px}.container_16 .prefix_15{padding-left:900px}.container_12 .suffix_3,.container_16 .suffix_4{padding-right:240px}.container_12 .suffix_6,.container_16 .suffix_8{padding-right:480px}.container_12 .suffix_9,.container_16 .suffix_12{padding-right:720px}.container_12 .suffix_1{padding-right:80px}.container_12 .suffix_2{padding-right:160px}.container_12 .suffix_4{padding-right:320px}.container_12 .suffix_5{padding-right:400px}.container_12 .suffix_7{padding-right:560px}.container_12 .suffix_8{padding-right:640px}.container_12 .suffix_10{padding-right:800px}.container_12 .suffix_11{padding-right:880px}.container_16 .suffix_1{padding-right:60px}.container_16 .suffix_2{padding-right:120px}.container_16 .suffix_3{padding-right:180px}.container_16 .suffix_5{padding-right:300px}.container_16 .suffix_6{padding-right:360px}.container_16 .suffix_7{padding-right:420px}.container_16 .suffix_9{padding-right:540px}.container_16 .suffix_10{padding-right:600px}.container_16 .suffix_11{padding-right:660px}.container_16 .suffix_13{padding-right:780px}.container_16 .suffix_14{padding-right:840px}.container_16 .suffix_15{padding-right:900px}.container_12 .push_3,.container_16 .push_4{left:240px}.container_12 .push_6,.container_16 .push_8{left:480px}.container_12 .push_9,.container_16 .push_12{left:720px}.container_12 .push_1{left:80px}.container_12 .push_2{left:160px}.container_12 .push_4{left:320px}.container_12 .push_5{left:400px}.container_12 .push_7{left:560px}.container_12 .push_8{left:640px}.container_12 .push_10{left:800px}.container_12 .push_11{left:880px}.container_16 .push_1{left:60px}.container_16 .push_2{left:120px}.container_16 .push_3{left:180px}.container_16 .push_5{left:300px}.container_16 .push_6{left:360px}.container_16 .push_7{left:420px}.container_16 .push_9{left:540px}.container_16 .push_10{left:600px}.container_16 .push_11{left:660px}.container_16 .push_13{left:780px}.container_16 .push_14{left:840px}.container_16 .push_15{left:900px}.container_12 .pull_3,.container_16 .pull_4{left:-240px}.container_12 .pull_6,.container_16 .pull_8{left:-480px}.container_12 .pull_9,.container_16 .pull_12{left:-720px}.container_12 .pull_1{left:-80px}.container_12 .pull_2{left:-160px}.container_12 .pull_4{left:-320px}.container_12 .pull_5{left:-400px}.container_12 .pull_7{left:-560px}.container_12 .pull_8{left:-640px}.container_12 .pull_10{left:-800px}.container_12 .pull_11{left:-880px}.container_16 .pull_1{left:-60px}.container_16 .pull_2{left:-120px}.container_16 .pull_3{left:-180px}.container_16 .pull_5{left:-300px}.container_16 .pull_6{left:-360px}.container_16 .pull_7{left:-420px}.container_16 .pull_9{left:-540px}.container_16 .pull_10{left:-600px}.container_16 .pull_11{left:-660px}.container_16 .pull_13{left:-780px}.container_16 .pull_14{left:-840px}.container_16 .pull_15{left:-900px}.clear{clear:both;display:block;overflow:hidden;visibility:hidden;width:0;height:0}.clearfix:after{clear:both;content:' ';display:block;font-size:0;line-height:0;visibility:hidden;width:0;height:0}* html .clearfix,*:first-child+html .clearfix{zoom:1}
html { background: #c0d895; }
h1, h2 { color: #bf4630; }
a { color: #406300; text-decoration: none; }
a:hover { color: #639a00; text-decoration: underline; }
nav { font-weight: bold; }
header hgroup h1 { color: #406300; }
header hgroup h2 { color: #639a00; }
header hgroup h1 a { background: url('../images/asp_logo_150x66.png') no-repeat; color: #c0d895; font-size: 1px; height: 32px; display: block; }
header hgroup h1 a:hover { background: url('../images/asp_logo_150x66.png') 0px -33px no-repeat; text-decoration: none; color: #c0d895; }
time { background: url('../images/icons/date.png') 2px 2px no-repeat; padding: 4px 0px 0px 22px; min-height: 16px; display: block; }
#user_comments article time { margin-left: 70px; }
li.selected a { text-decoration: underline; color: #bf4630; }
#sys_message { background: #82b22c; color: #d9fa9e; padding: 0px 10px 5px 10px; margin-bottom: 5px; }
#sys_message {
    border-bottom-right-radius: 20px;
    border-bottom-left-radius: 20px;
    -moz-border-radius-bottomright: 20px;
    -moz-border-radius-bottomleft: 20px;
    -webkit-border-bottom-right-radius: 20px;
    -webkit-border-bottom-left-radius: 20px;
}
#sys_message p { text-align: center; padding: 5px 10px 0px 10px; }
#sys_message a { color: #fff; }
#page { background: #f1ffd6; border-radius: 20px; -moz-border-radius: 20px; -webkit-border-radius: 20px; padding: 0px 10px; min-height: 200px; }
#page form fieldset { background: #d9fa9e; border: 1px solid #82b22c; border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; }
#page form fieldset legend { color: #bf4630; }
#page section h1 { font-size: 130%; border-bottom: 1px solid #c0d895; }
#page section h2 { font-size: 120%; }
#page section h3 { font-size: 110%; }

footer { padding-bottom: 40px; }
footer h1 { font-size: 110%; border-bottom: 1px solid #639a00; }
footer nav li, footer section li { margin: 0px 0px 0px 20px; list-style: disc; }
#systemstatus { width:100%; background: #f1ffd6; text-align:center; padding:5px 0px; margin:0 0 10px 0; border-radius: 0 0 20px 20px; -moz-border-radius: 0 0 20px 20px; -webkit-border-radius: 0 0 20px 20px; }
label { margin-top:10px; clear:both; }
select, input, textarea { clear:both; }

textarea { min-width:500px; min-height:160px; }

#user_page ul li { list-style: none; }
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
			okDatabase = true;
			if ((okPassword) && (okDatabase) && (okPermanent)) {
   				submitButton.disabled = false;
	  			submitButton.value = 'Continue';
			} // if
	     } else {
			checkButton.value = 'Check Connection';
			checkButton.style.color = '#ff0000';
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
    $required = '5.2.0';
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
  
  function CheckStorageDirectory () {
    global $Error, $ErrorMark;

		if (!file_exists ( getcwd() . '/_storage/legacy' ) ) {
			if (!mkdir ( getcwd() . '/_storage/legacy' ) ) {
      	$Error['storage_directory'] = TRUE;
      	$ErrorMark['storage_directory'] = "<span class='no'>N</span>";
				return ( FALSE );
			}
		}
    
		if (!file_exists ( getcwd() . '/_storage/legacy/photos' ) ) {
			if (!mkdir ( getcwd() . '/_storage/legacy/photos' ) ) {
      	$Error['storage_directory'] = TRUE;
      	$ErrorMark['storage_directory'] = "<span class='no'>N</span>";
				return ( FALSE );
			}
		}
    
    // Check if photo directory is writable.
    if (!is_writable (getcwd() . '/_storage/')) {
      $Error['storage_directory'] = TRUE;
      $ErrorMark['storage_directory'] = "<span class='no'>N</span>";
			return ( FALSE );
    } else {
      $Error['storage_directory'] = FALSE;
      $ErrorMark['storage_directory'] = "<span class='yes'>Y</span>";
    } // if
    
    return (TRUE);
  } // CheckStorageDirectory
  
  function CheckConfigurationWritable () {
	  global $gCUSTOM;

    global $Error, $ErrorMark;

		if (!file_exists (getcwd() . "/configurations/$gCUSTOM/$gCUSTOM.conf")) {
			if (!file_exists (getcwd() . "/configurations/$gCUSTOM")) {
				if (!mkdir (getcwd() . "/configurations/$gCUSTOM")) {
      		$Error['configurations_directory'] = FALSE;
      		$ErrorMark['configurations_directory'] = "<span class='yes'>Y</span>";
				}
			}
			if (!file_exists (getcwd() . "/configurations/$gCUSTOM/$gCUSTOM.conf")) {
				if (!touch (getcwd() . "/configurations/$gCUSTOM/$gCUSTOM.conf")) {
      		$Error['configurations_directory'] = FALSE;
      		$ErrorMark['configurations_directory'] = "<span class='yes'>Y</span>";
				}
			}
		}
    
    // Check if configuration file is writable.
    if (!is_writable (getcwd() . "/configurations/$gCUSTOM/$gCUSTOM.conf")) {
      $Error['configurations_directory'] = TRUE;
      $ErrorMark['configurations_directory'] = "<span class='no'>N</span>";
    } else {
      $Error['configurations_directory'] = FALSE;
      $ErrorMark['configurations_directory'] = "<span class='yes'>Y</span>";
    } // if
    
    return (TRUE);
  } // CheckConfigurationWritable
  
  function CheckHtaccessFinal () {
    global $Error, $ErrorMark;
   
		// If it doesn't exist, try and create it.
		if (!file_exists (getcwd() . "/.htaccess")) {
			if (!touch (getcwd() . "/.htaccess")) {
     		$Error['htaccess_final'] = FALSE;
     		$ErrorMark['htaccess_final'] = "<span class='yes'>Y</span>";
			}
		}

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
  
  function WriteConfiguration ($pDATABASE, $pUSERNAME, $pPASSWORD, $pPREFIX, $pVERSION, $pHOST, $pDOMAIN) {
		global $gCUSTOM;

    $configurations_directory = "configurations/$gCUSTOM/$gCUSTOM.conf";
    
    $filedata = "; Inherit from default configuration\n" .
								"inherit=\"default\"\n\n" .
								"enabled=\"true\"\n\n" .
								"; db connection\n\n" .
								"db=\"$pDATABASE\"\n" .
                "un=\"$pUSERNAME\"\n" .
                "pw=\"$pPASSWORD\"\n" .  
                "pre=\"$pPREFIX\"\n" .  
                "host=\"$pHOST\"\n" .
                "url=\"$pDOMAIN\"\n\n" .
								"; general config\n" .
                "ver=\"$pVERSION\"\n";

    // Open file for writing.
    if (!$file = fopen($configurations_directory, 'w')) {
      global $Error, $ErrorMark;
      $Error['configurations_directory'] = TRUE;
      $ErrorMark['configurations_directory'] = "<span class='no'>N</span>";
      return (FALSE);
    } // if
    
    fwrite($file, $filedata);
    fclose($file);
    
    return (TRUE);
  } // WriteConfiguration
  
  function PreLoadSiteData () {
		global $gCUSTOM;
    global $gDATABASE, $gUSERNAME, $gPASSWORD, $gPREFIX, $gHOST, $gDOMAIN;
    
    $configurations_directory = "configurations/$gCUSTOM/$gCUSTOM.conf";
    
    // Open file for reading.
    if (!$file = fopen($configurations_directory, 'r')) {
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
    
    $sql_install = "_release/install.sql";
    
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
		// NOTE: Disabled for now.
    //if (($pUPGRADE == 1) or ($pUPGRADE == 2)) {
    	//$this->UpgradeTables($tableList);
    //} // if
    
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

		$salt = substr(md5(uniqid(rand(), true)), 0, 16);
		$sha512 = hash ("sha512", $salt . $pADMINPASS);
		$newpass = $salt . $sha512;

		$tablename = $gPREFIX . 'userAuthorization';

		$reset_query = "UPDATE %s SET Pass = '%s', Username='%s' WHERE uID = 1";

		$sql = sprintf ( $reset_query, $tablename, $newpass, $pADMINUSER );

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
    
    if (!file_exists ("_release/install.sql")) {
      $ErrorString = "Could not find install.sql for importing.";
      return (FALSE);
    } // if
    
    $this->CheckMysqlServerVersion ();
    if ($Error['mysql_server_version']) {
      $ErrorString = "Selected MySQL server version is too low. (Version is " . $this->GetMysqlServerVersion() . ", Appleseed requires > 5.2)";
      return (FALSE);
    } // if
    
    if (!is_readable ("_release/install.sql")) {
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
    
    if (!$this->WriteConfiguration ($gDATABASE, $gUSERNAME, $gPASSWORD, $gPREFIX, '0.7.4', $gHOST, $gDOMAIN)) return (FALSE);
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
    
		$gPREFIX = "asd_";
    $gPASSWORD = null;
  	$gADMINPASS = null;
  	$gADMINPASSCONFIRM = null;
    ?>
        
    <body onload='initialize();'>

      <div id="page" class="container_12">
        <div id="page_left" class="grid_3"> 
					<section id="info">
						<h1>Welcome to Appleseed!</h1> 
						<h2>Open Source + Distributed Social Networking</h2>

						<p> Appleseed is the first open source, fully decentralized social networking software. </p>
							<ul>
							  <li>Protect your privacy</li>
							  <li>Move around without losing friends</li>
							  <li>Support open standards</li>
							</ul>

						<h3>Appleseed Features</h3>
							<ul>
								<li>Connect with Friends!</li>
								<li>Blogging</li>
								<li>Journals</li>
								<li>Photos</li>
								<li>Spam-Resistant E-Mail</li>
								<li>Decentralized</li>
							</ul>
						<h3>New in this release</h3>
							<ul>
								<li>Themes (and a new look!)</li>
								<li>MVC+Plugins Framework</li>
								<li>Internationalization</li>
								<li>Stronger Password Encryption</li>
								<li>Lots of bug fixes</li>
							</ul>

						<h3>Appleseed Project Homepage</h3>
						
						&rarr; <a href="http://opensource.appleseedproject.org">opensource.appleseedproject.org</a>

					</section>
				</div>
        <div id="page_right" class="grid_9">
										         
  	      <section id="install">
            <h1>Appleseed Install v0.7.4</h1>
      
					  <?php echo $ErrorString; ?>
            <form id='main' name='main' method='POST' action='/'>
              <fieldset id='check'>
                <legend>System Check</legend>
                <p class='information'>This system cannot be installed until the following conditions are met.</p>
       
								<table>
									<tbody>
										<tr>
                			<th><span class='label'>Installed in site root directory? (see documentation)</span></th>
                			<td><?php echo $ErrorMark['sub_dir']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>Is register_globals turned off?</span></th>
                			<td><?php echo $ErrorMark['register_globals']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>Is the _storage/ directory writable?</span></th>
                			<td><?php echo $ErrorMark['storage_directory']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>Is the configurations/ directory writable?</span></th>
                			<td><?php echo $ErrorMark['configurations_directory']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>Is .htaccess writable?</span></th>
                			<td><?php echo $ErrorMark['htaccess_final']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>PHP version 5.2 or higher? (Running <?php echo phpversion(); ?>)</span></th>
                			<td><?php echo $ErrorMark['php_version']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>Mysql version 5.0 or higher? (Client is <?php echo $this->GetMysqlClientVersion (); ?>)</span></th>
                			<td><?php echo $ErrorMark['mysql_client_version']; ?></td>
										</tr>

									  <tr>
											<th><input type='submit' name='refresh' class='refresh' value="Refresh" /></th>
										  <td></td>
										</tr>
     
									</tbody>
								</table>
      
              </fieldset>

							<fieldset>
								<legend>Database Settings</legend>
                <p class='information'>
									Enter your database connection settings.  
									<i class='warning'>Warning:  If the database tables already exist, they will be overwritten!</i>
								</p>

								<table>
								  <tbody>
										<tr>
                			<th><label for='gHOST'>DB Host Name:</label></th>
                			<td><input type='text' class='gHOST' name='gHOST' value='<?php echo $gHOST; ?>' /></td>
										</tr>
           
										<tr>
                			<th><label for='gDATABASE'>DB Name:</label></th>
                			<td><input type='text' class='gDATABASE' name='gDATABASE' value='<?php echo $gDATABASE; ?>' /></td>
										</tr>
         
										<tr>
                			<th><label for='gUSERNAME'>DB Username:</label></th>
                			<td><input type='text' class='gUSERNAME' name='gUSERNAME' value='<?php echo $gUSERNAME; ?>' /></td>
										</tr>
         
										<tr>
                			<th><label for='gPASSWORD'>DB Password:</label></th>
                			<td><input type='password' class='gPASSWORD' name='gPASSWORD' value='<?php echo $gPASSWORD; ?>' /></td>
										</tr>
     
										<tr>
                			<th><label for='gPREFIX'>DB Table Prefix:</label></th>
                			<td><input type='text' name='gPREFIX' value='<?php echo $gPREFIX; ?>' /></td>
									  </tr>
									</tbody>
								</table>
    
                <div id='confirmDatabaseConnection'>
   	              <input type='submit' id='checkConnection' name='checkConnection' class='checkConnection' value="Check Connection" disabled=disabled />
   	            </div>
   
                <!-- Disabled for now 
                <label for='gUPGRADE'>Upgrade if tables exist?</label>
                <select id='gUPGRADE' name='gUPGRADE'>
       	          <option <?php if ($gUPGRADE == '0') echo "selected"; ?> value='0'>No (Delete Existing)</option>
     	            <option <?php if ($gUPGRADE == '1') echo "selected"; ?> value='1'>Yes (Backup Tables)</option>
   	              <option <?php if ($gUPGRADE == '2') echo "selected"; ?> value='2'>Yes (No Backup)</option>
                 </select>
	  				-->
						  </fieldset>
     
              <fieldset>
                <legend>Site Settings</legend>
                <p class='information'>Enter your site setup information.</p>

								<table>
								  <tbody>
									  <tr>
                			<th><label for='gDOMAIN'>Site Domain:</label></th>
                			<td><input type='text' name='gDOMAIN' value='<?php echo $gDOMAIN; ?>' /></td>
										</tr>
     
									  <tr>
                			<th><label for='gADMINUSER'>Default Admin Username:</label></th>
                			<td><input type='text' name='gADMINUSER' value='<?php echo $gADMINUSER; ?>' /></td>
										</tr>
   
									  <tr>
                			<th><label for='gADMINPASS'>Default Admin Password:</label></th>
                			<td><input type='password' maxlength=20 id='adminPass' class='gADMINPASS' name='gADMINPASS' value='<?php echo $gADMINPASS; ?>' /></td>
										</tr>
   
									  <tr>
                			<th><label for='gADMINPASSCONFIRM'>Default Admin Password (Confirm):</label></th>
                			<td><input type='password' maxlength=20 id='adminPassConfirm' class='gADMINPASSCONFIRM' name='gADMINPASSCONFIRM' value='<?php echo $gADMINPASSCONFIRM; ?>' /></td>
										</tr>
									</tbody>
								</table>
     
                <div id='checkPasswords'>&nbsp;</div>
   
              </fieldset>

              <input type='submit' id='submit' name='submit' class='submit' <?php echo $submit_disabled; ?> value="<?php echo $submit_label; ?>" onSubmit="document.getElementById('submit').disabled=true;" />

            </form>
	       </section>
         </div>
	     	 <div class="clear"></div>
			 </div>

       <footer class='container_12'>
			  	<p align="center">
        		Copyright &copy; 2004-2010 by Michael Chisari under the <a href='http://www.gnu.org/licenses/gpl-2.0.html'>GNU GPL Version 2</a>. All Rights Reserved.
					</p>
       </footer>
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
      <div id="page" class="container_12">
        <div id="page_left" class="grid_3"> 
					<section id="info">
						<h1>Welcome to Appleseed!</h1> 
						<h2>Open Source + Distributed Social Networking</h2>

						<p> Appleseed is the first open source, fully decentralized social networking software. </p>
							<ul>
							  <li>Protect your privacy</li>
							  <li>Move around without losing friends</li>
							  <li>Support open standards</li>
							</ul>

						<h3>Appleseed Features</h3>
							<ul>
								<li>Connect with Friends!</li>
								<li>Blogging</li>
								<li>Journals</li>
								<li>Photos</li>
								<li>Spam-Resistant E-Mail</li>
								<li>Decentralized</li>
							</ul>
						<h3>New in this release</h3>
							<ul>
								<li>Themes (and a new look!)</li>
								<li>MVC+Plugins Framework</li>
								<li>Internationalization</li>
								<li>Stronger Password Encryption</li>
								<li>Lots of bug fixes</li>
							</ul>

						<h3>Appleseed Project Homepage</h3>
						
						&rarr; <a href="http://opensource.appleseedproject.org">opensource.appleseedproject.org</a>

					</section>
				</div>
        <div id="page_right" class="grid_9">
										         
  	      <section id="install">
            <h1>Appleseed Install v0.7.4</h1>
      
					  <?php echo $ErrorString; ?>
            <form id='main' name='main' method='POST' action='/'>
              <fieldset id='check'>
                <legend>System Check</legend>
       
								<table>
									<tbody>
										<tr>
                			<th><span class='label'>Installed in site root directory? (see documentation)</span></th>
                			<td><?php echo $ErrorMark['sub_dir']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>Is register_globals turned off?</span></th>
                			<td><?php echo $ErrorMark['register_globals']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>Is the _storage/ directory writable?</span></th>
                			<td><?php echo $ErrorMark['storage_directory']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>Is the configurations/ directory writable?</span></th>
                			<td><?php echo $ErrorMark['configurations_directory']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>Is .htaccess writable?</span></th>
                			<td><?php echo $ErrorMark['htaccess_final']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>PHP version 5.2 or higher? (Running <?php echo phpversion(); ?>)</span></th>
                			<td><?php echo $ErrorMark['php_version']; ?></td>
										</tr>
      
										<tr>
                			<th><span class='label'>Mysql version 5.0 or higher? (Client is <?php echo $this->GetMysqlClientVersion (); ?>)</span></th>
                			<td><?php echo $ErrorMark['mysql_client_version']; ?></td>
										</tr>

									</tbody>
								</table>
      
              </fieldset>

      				<fieldset>
        				<legend>Completing Installation</legend>
        				<p class='information'>Creating database tables and initializing Appleseed.</p>
        
								<table>
									<tbody>
										<tr>
        							<th><span class='label'>Writing Configuration to File</span></th>
        							<td><span class='done'>DONE</span></td>
										</tr>
        
										<tr>
        							<th><span class='label'>Creating .htaccess File</span></th>
        							<td><span class='done'>DONE</span></td>
										</tr>
        
										<tr>
        							<th><span class='label'>Creating Appleseed Tables</span></th>
        							<td><span class='done'>DONE</span></td>
										</tr>
        
										<tr>
        							<th><span class='label'>Importing Initial Data</span></th>
        							<td><span class='done'>DONE</span></td>
										</tr>
        
										<tr>
        							<th><span class='label'>Creating Administrator Account</span></th>
        							<td><span class='done'>DONE</span></td>
										</tr>
									</tbody>
								</table>
							</fieldset>
        
        			<p class='done'>
        				Your Appleseed installation is now complete!  
								For security reasons, be sure to change the 
								permissions on <b>.htaccess</b> and 
								<b>configurations/</b> so that they aren't 
								web writable anymore, and delete the install
        				script (index.php) from the appleseed root 
								directory.
							</p>
        
        			<h2 align="center"><a href='<?php echo $gDOMAIN; ?>/login/'>Click Here To Login To Your Appleseed Site</a></h2>
            </form>
	       </section>
         </div>
	     	 <div class="clear"></div>
			 </div>

       <footer class='container_12'>
			  	<p align="center">
        		Copyright &copy; 2004-2010 by Michael Chisari under the <a href='http://www.gnu.org/licenses/gpl-2.0.html'>GNU GPL Version 2</a>. All Rights Reserved.
					</p>
       </footer>
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
