<!DOCTYPE html>
<html lang="en">

<head>
  	<?php $zApp->Components->Go ( 'system', 'head', 'head' ); ?>
</head>

<body id="appleseed">
	
  	<?php $zApp->Components->Go ( "system" ); ?>

	<div class="clear"></div>

	<!-- System Message -->
	
	<!-- Header -->
	<header id="appleseed-header">
 		<?php $zApp->Components->Go ( "header" ); ?>
 	</header>

	<div id="appleseed-logo"></div>
	
	<div id="appleseed-container" class="container_16">
	
    	<div id="appleseed-admin" class="container_16">
	       	<div id="appleseed-admin-menu" class="container_16">
	       		<nav id="admin-tabs" class="grid_9 push_4">
					<?php $zApp->Components->Go ( "user", "admin", "admin.tabs" ); ?>
		       	</nav>
		       	<div id="admin-search" class="grid_3 push_4">
					<?php $zApp->Components->Go ( "search", "search", "local" ); ?>
				</div>
			</div>
       
			<div id="appleseed-admin-main" class="grid_16">
				<div id="appleseed-admin-main-menu" class="grid_4 alpha">
					<section id="admin-main-menu">
						<?php $zApp->Components->Go ( "admin", "menu", "menu" ); ?>
					</section>
				</div>
				<div id="appleseed-admin-content" class="grid_12 omega">
  					<section class="admin-content">
						<?php $zApp->Components->Go ( "user", "admin", "admin.configuration" ); ?>
					</section>
				</div>
			</div>
		</div>
        
    </div>

	<div class="clear"></div>
    
    <footer id="appleseed-footer" class="container_16">
 		<?php $zApp->Components->Go ( "footer" ); ?>
 	</footer>
 	
</body>
</html>
