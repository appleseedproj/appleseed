<?php
  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: index.php                               CREATED: 04-12-2007 + 
  // | LOCATION: /                                  MODIFIED: 04-13-2007 +
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
  // | VERSION:      0.7.0                                               |
  // | DESCRIPTION:  Default Appleseed Installer                         |
  // +-------------------------------------------------------------------+
  
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
  $gADMINUSER = ($_POST['gADMINUSER']) ? $_POST['gADMINUSER'] : 'Admin';
  $gADMINPASS = $_POST['gADMINPASS'];
  $gADMINPASSCONFIRM = $_POST['gADMINPASSCONFIRM'];
  
  // Add http:// to Domain if not already there.
  if (strtolower(substr ($gDOMAIN, 0, 7)) != 'http://') $gDOMAIN = 'http://' . $gDOMAIN;
  
  $INSTALL->CheckForSubDirectory ();
  $INSTALL->CheckPHPVersion ();
  $INSTALL->CheckMysqlVersion ();
  $INSTALL->CheckMagicQuotes ();
  $INSTALL->CheckRegisterGlobals ();
  $INSTALL->CheckPhotoDirectory ();
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
 div.container { float:left; clear:both; width:800px; background-color:#fafafa; border:1px solid #cccccc; border-right:2px solid #999999; border-bottom:2px solid #999999; -moz-border-radius:12px; }
 div#check, div#completed, div#site, div#database {  float:left; clear:both; width:590px; margin:10px 100px; padding:5px; border:1px solid #cccccc; -moz-border-radius:12px; }
 p { float:left; clear:both; width:100%; border-bottom:1px solid #aaaaaa; }
 p.information { border:none; }
 p.done { border:none; border-top:1px solid #aaaaaa; margin-top:10px; padding-top:10px; }
 p.final { border:none; width:400px; margin:0 150px; }
 p.final a { text-decoration:none; float:left; clear:both; font-size:14px; color:#00ff00; background:#ccffcc; padding:2px 4px; -moz-border-radius:12px; }
 span.label, label { float:left; width:270px; margin:5px 0 5px 100px; }
 input, textarea { width:150px; margin:5px 50px 5px 0; font:10px Arial; color:#8c9095; background:#ecf0f5; vertical-align:top; border:1px solid #ccd0d5; padding:1px 3px; -moz-border-radius:12px; }
 input:hover, select:hover, textarea:hover { background:#fafafa; color:#4c5055; border-color:#acb0b5 } 
 input.submit { float:right; width:auto; padding:2px 10px; margin:20px 98px; font-weight:bold; }
 span.done, span.yes, span.no { float:left; width:8px; font-weight:bold; text-align:center; margin:5px 0px 5px 130px; padding:1px 3px; -moz-border-radius:12px; }
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

<?php if ($CurrentStep == 1) $INSTALL->ViewStepOne (); ?>
<?php if ($CurrentStep == 2) $INSTALL->ViewStepTwo (); ?>

<?php
/*---------------------------------------------------------------------*/

// Installation Class
class cINSTALL {
  
  function CheckPHPVersion () {
    global $Error, $ErrorMark;
    
    // Check for PHP version 4.1.0 or higher.
    $required = '4.1.0';
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
  
  function CheckMysqlVersion () {
    global $Error, $ErrorMark;
    
    $version = $this->GetMysqlVersion();
    
    $VersionArray = explode ('.', $version);
    $main = $VersionArray[0];
    $minor = $VersionArray[1];
    $micro = $VersionArray[2];
    
    if ($main > 5) {
      $Error['mysql_version'] = FALSE;
      $ErrorMark['mysql_version'] = "<span class='yes'>Y</span>";
    } elseif ($main == 5) {
      // Add sub-checks for minor versions.
      $Error['mysql_version'] = FALSE;
      $ErrorMark['mysql_version'] = "<span class='yes'>Y</span>";
    } else {
      $Error['mysql_version'] = TRUE;
      $ErrorMark['mysql_version'] = "<span class='no'>N</span>";
    } // if
    
    return (TRUE);
  } // CheckMysqlVersion
  
  function GetMysqlVersion () {
    $output = shell_exec('mysql -V');
    preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
    
    return ($version[0]);
  } // GetMysqlVersion
  
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
  
  function WriteSiteData ($pDATABASE, $pUSERNAME, $pPASSWORD, $pPREFIX, $pHOST, $pDOMAIN) {
    $site_data = "data/site.adat";
    
    $filedata = "db:$pDATABASE\n" .
                "un:$pUSERNAME\n" .
                "pw:$pPASSWORD\n" .  
                "pre:$pPREFIX\n" .  
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
  
  function ImportData ($pUSERNAME, $pPASSWORD, $pHOST, $pDATABASE, $gPREFIX) {
    global $ErrorString;
    
    $sql_install = "install.sql";
    
    // Open file for reading.
    if (!$sql_file = fopen($sql_install, 'r')) {
      return (FALSE);
    } // if
    
    $sql = NULL;
    while (!feof($sql_file)) {
        $sql .= fgets($sql_file, 4096);
    } // while
    
    $sql = str_replace ("%PREFIX%", $gPREFIX, $sql);
    
    $final_sql_filename = tempnam("/tmp", "asdsql");
    $final_sql_file = fopen ($final_sql_filename, "w");
    
    fwrite ($final_sql_file, $sql);
    
    $message = shell_exec("mysql -h $pHOST -u $pUSERNAME -p$pPASSWORD $pDATABASE < $final_sql_filename");
    fclose ($final_sql_file);
    if ($message) {
      $ErrorString = "MYSQL ERROR: " . $message;
      return (FALSE);
    } 
    
    return (TRUE);
  } // ImportData
  
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
    
    if (!is_readable ("install.sql")) {
      $ErrorString = "Could not open install.sql for reading.";
      return (FALSE);
    } // if
    
    return (TRUE);
  } // ValidateForm
  
  function ProcessPost () {
    global $MysqlLink;
    
    // Check if form has not been submitted.
    if (empty ($_POST)) return (FALSE);
    
    // Check if form input validates.
    if (!$this->ValidateForm()) return (FALSE);
    
    global $gDATABASE, $gUSERNAME, $gPASSWORD, $gPREFIX, $gHOST, $gDOMAIN;
    global $gADMINUSER, $gADMINPASS;
    
    if (!$this->WriteSiteData ($gDATABASE, $gUSERNAME, $gPASSWORD, $gPREFIX, $gHOST, $gDOMAIN)) return (FALSE);
    if (!$this->WriteHtaccess ()) return (FALSE);
    if (!$this->ImportData ($gUSERNAME, $gPASSWORD, $gHOST, $gDATABASE, $gPREFIX)) return (FALSE);
    if (!$this->UpdateAdminUserPass ($gADMINUSER, $gADMINPASS)) return (FALSE);
    
    return (TRUE);
  } // ProcessPost
  
  function CheckError () {
    global $Error, $ErrorString;
    
    if ($ErrorString) $ErrorString = "<p class='error'>$ErrorString</p>";
    
    if (in_array(TRUE, $Error)) {
      return (TRUE);
    } else {
      return (FALSE);
    } // if
    
    return (TRUE);
  } // CheckError
  
  function ViewStepOne () {
    
    global $Error, $ErrorString, $ErrorMark;
    global $gDATABASE, $gUSERNAME, $gPASSWORD, $gPREFIX, $gHOST, $gDOMAIN;
    global $gADMINUSER, $gADMINPASS, $gADMINPASSCONFIRM;
    global $submit_label, $submit_disabled;
    
    ?>
        
    <body>
     <div id='install'>
      <div class='caption'>APPLESEED INSTALL v0.7.0</div>
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
        
        <span class='label'>Is data/site.adat writable?</span>
        <?php echo $ErrorMark['site_data']; ?>
        
        <span class='label'>Is .htaccess writable?</span>
        <?php echo $ErrorMark['htaccess_final']; ?>
        
        <span class='label'>PHP version 4.1.0 or higher? (Running <?php echo phpversion(); ?>)</span>
        <?php echo $ErrorMark['php_version']; ?>
        
        <span class='label'>Mysql version 5.0 or higher? (Running <?php echo $this->GetMysqlVersion (); ?>)</span>
        <?php echo $ErrorMark['mysql_version']; ?>
       </div>
       
       <form id='main' name='main' method='POST'>
       <div id='database'>
        <p class='title'>Database Settings</p>
        <p class='information'>Enter your database information in the following fields.</p>
         <label for='gHOST'>DB Host Name:</label>
         <input type='text' name='gHOST' value='<?php echo $gHOST; ?>' />
         
         <label for='gDATABASE'>DB Name:</label>
         <input type='text' name='gDATABASE' value='<?php echo $gDATABASE; ?>' />
         
         <label for='gUSERNAME'>DB Username:</label>
         <input type='text' name='gUSERNAME' value='<?php echo $gUSERNAME; ?>' />
         
         <label for='gPASSWORD'>DB Password:</label>
         <input type='password' name='gPASSWORD' value='' />
         
         <label for='gPREFIX'>DB Table Prefix:</label>
         <input type='text' name='gPREFIX' value='<?php echo $gPREFIX; ?>' />
     
       </div> <!-- #database -->
       
       <div id='site'>
        <p class='title'>Site Settings</p>
        <p class='information'>Enter your site setup information.</p>
         <label for='gDOMAIN'>Site Domain:</label>
         <input type='text' name='gDOMAIN' value='<?php echo $gDOMAIN; ?>' />
         
         <label for='gADMINUSER'>Default Admin Username:</label>
         <input type='text' name='gADMINUSER' value='<?php echo $gADMINUSER; ?>' />
     
         <label for='gADMINPASS'>Default Admin Password:</label>
         <input type='password' maxlength=20 name='gADMINPASS' value='' />
     
         <label for='gADMINPASSCONFIRM'>Default Admin Password (Confirm):</label>
         <input type='password' maxlength=20 name='gADMINPASSCONFIRM' value='' />
     
       </div> <!-- #site -->
         <input type='submit' name='submit' class='submit' <?php echo $submit_disabled; ?> value="<?php echo $submit_label; ?>" />
       </form> <!-- #main -->
   
      </div> <!-- .container -->
     </div> <!-- .install -->
     <div id='copyright'>
      Copyleft &copy; 2004-2007 by the Appleseed Collective. All Rights Reversed.
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
        
        <span class='label'>Is data/site.adat writable?</span>
        <?php echo $ErrorMark['site_data']; ?>
        
        <span class='label'>Is .htaccess writable?</span>
        <?php echo $ErrorMark['htaccess_final']; ?>
        
        <span class='label'>PHP version 4.1.0 or higher? (Running <?php echo phpversion(); ?>)</span>
        <?php echo $ErrorMark['php_version']; ?>
        
        <span class='label'>Mysql version 5.0 or higher? (Running <?php echo $this->GetMysqlVersion (); ?>)</span>
        <?php echo $ErrorMark['mysql_version']; ?>
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
      Copyleft &copy; 2004-2007 by the Appleseed Collective. All Rights Reversed.
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
