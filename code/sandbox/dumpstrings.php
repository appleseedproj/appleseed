<?

  // QUICK AND DIRTY SCRIPT TO CREATE THE $gSTRINGS ARRAY.

  // Using this array should increase system performance, since
  // strings won't have to be pulled from the database.

  // This needs to be integrated with the Admin interface.

  // Change to document root directory.
  chdir ($_SERVER['DOCUMENT_ROOT']);

  // Include necessary files
  require_once ('code/include/classes/base.php'); 
  require_once ('code/include/classes/BASE/application.php'); 
  require_once ('code/include/classes/system.php'); 
  require_once ('code/include/classes/remote.php'); 

  require_once ('code/include/classes/auth.php'); 
  require_once ('code/include/classes/appleseed.php'); 
  require_once ('code/include/classes/privacy.php'); 
  require_once ('code/include/classes/messages.php'); 
  require_once ('code/include/classes/users.php'); 


  // Create the Application class.
  $zAPPLE = new cAPPLESEED ();
  
  // Connect to the database.
  $zAPPLE->DBConnect ();

  // Set Global Variables (Put this at the top of wrapper scripts)
  $zAPPLE->SetGlobals ();

  $SYSTEMSTRINGS = new cSYSTEMSTRINGS;
  
  $SYSTEMSTRINGS->SelectWhere ("Language = 'en' ORDER BY Context, Title");

  ob_start ();

  echo "<?\n";
  echo '  global $gSTRINGS;' . "\n\n";
  echo '  $gSTRINGS = array (', "\n";

  while ($SYSTEMSTRINGS->FetchArray ()) {
    echo '    "' . $SYSTEMSTRINGS->Context . " - " . $SYSTEMSTRINGS->Title . '" => array (' . "\n";
    echo '         "Output" => \'' . addslashes ($SYSTEMSTRINGS->Output) . "', \n";
    echo '         "Formatting" => "' . $SYSTEMSTRINGS->Formatting . '"' . " \n";
    echo "    ),\n";
    // echo '"' . $SYSTEMSTRINGS->Title . '" => array (';
    // echo '"' . $SYSTEMSTRINGS->Context, '" => "' . $SYSTEMSTRINGS->Output . '"); );<br />';
  } // while
  echo "  );\n";
  echo "?>";

  $buffer = ob_get_clean ();

  $handle = fopen ("code/include/data/strings.adat", "w+") or die ("can't open!");

  fwrite ($handle, $buffer);

  fclose ($handle);

  echo "Done.";
?>
