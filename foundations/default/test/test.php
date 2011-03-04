<!DOCTYPE html>
<html lang="en">

<head>
	<?php $zApp->Components->Go ( 'system', 'head' ); ?>
</head>

<body id="appleseed">

	<!-- System Message -->
	<?php $zApp->Components->Go ( "system" ); ?>
	
	<div class="clear"></div>

	<!-- Header -->
	<header id="appleseed-header">
  		<?php $zApp->Components->Go ( "header" ); ?>
	</header>
	
	<div id="appleseed-logo"></div>
	
	<div id="appleseed-container" class="container_16">
	
    	<div id="appleseed-login" class="container_16">
	       	<div id="appleseed-login-menu" class="container_16">
			</div>
       
			<div id="appleseed-login-main" class="grid_16">
				<div id="appleseed-login-info" class="grid_4 alpha">
					<?php $zApp->Components->Go ( "login", "info", "info" ); ?>
        
		 			<?php $zApp->Components->Go ( "appleseed", "appleseed", "donate" ); ?>
		  	
				</div>
				<div id="appleseed-login-content" class="grid_12 omega">
					<?php $zApp->Components->Go ( 'test' ); ?>

					<?php $zApp->Components->Go ( 'test', 'test2', 'test2' ); ?>
				</div>
			</div>
		</div>
        
    </div>

	<div class="clear"></div>
    
    <footer id="appleseed-footer" class="container_16">
		<?php $zApp->Components->Go ( "footer" ); ?>
    </footer>
    
	<div class="clear"></div>
	
</body>
</html>
