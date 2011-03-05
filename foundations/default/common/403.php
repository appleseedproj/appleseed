<!DOCTYPE html>
<html lang="en">

<head>
	
  	<?php $zApp->Components->Go ( 'system', 'head', 'head' ); ?>

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
	       		<nav id="login-tabs" class="grid_9 push_4">
					<?php $zApp->Components->Go ( "login", "tabs", "tabs" ); ?>
		       	</nav>
			</div>
       
			<div id="appleseed-login-main" class="grid_16">
				<div id="appleseed-login-info" class="grid_4 alpha">
					<?php $zApp->Components->Go ( "login", "info", "info" ); ?>
        
		 			<?php $zApp->Components->Go ( "appleseed", "appleseed", "donate" ); ?>
		  	
				</div>
				<div id="appleseed-login-content" class="grid_12 omega">
					<section id="login-local">
						<?php $zApp->Components->Go ( "login", "login", "login", array ( 'force' => true ) ); ?>
					</section>
 
					<section id="login-remote">
						<?php $zApp->Components->Go ( "login", "login", "remote", array ( 'force' => true ) ); ?>
					</section>
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
