<?php $zApp->Components->Go ( "login", "logout", "logout", "logout" ); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  	<?php $zApp->Components->Go ( 'system', 'head', 'head' ); ?>
</head>

<body id="appleseed">

	<?php $zApp->Components->Go ( "login", "logout", "click", "click" ); ?>

</html>

<?php $zApp->Components->Go ( "system", "system", null, "data" ); ?>
