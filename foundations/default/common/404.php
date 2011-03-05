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
	
    	<div id="appleseed-404" class="container_16">
	       	<div id="appleseed-404-menu" class="container_16">
	       		<nav id="404-tabs" class="grid_9 push_4">
		       	</nav>
			</div>
       
			<div id="appleseed-404-main" class="grid_16">
				<div id="appleseed-404-info" class="grid_4 alpha">
		 			<?php $zApp->Components->Go ( "appleseed", "appleseed", "donate" ); ?>
		  	
				</div>
				<div id="appleseed-404-content" class="grid_12 omega">
					<section id="404-local">
						<?php $zApp->Components->Go ( "system", "404", "404" ); ?>
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
