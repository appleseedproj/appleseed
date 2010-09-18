<!DOCTYPE html>
<html lang="en">

<head>
	
	<!-- Meta -->
	<meta charset="utf-8" />

	<!-- Title -->
	<title><?php echo __("Login | Appleseed"); ?></title>
	
	<!-- Links -->
    <?php $zApp->Theme->UseStyles (); ?>
	
	<!-- Javascript --> 
	<!--[if IE]>
	<script src="/themes/default/style/html5.js"></script>
	<![endif]-->
	
    <!-- Load JLoader framework -->
   	<script type="text/javascript" src="/libraries/javascript/jloader.init.js"></script>
   	<script type="text/javascript" src="/foundations/default/default.js"></script>
   	
	<!-- Load JQuery -->
   	<script type="text/javascript" src="/libraries/external/JQuery-1.4.2/jquery-1.4.2.min.js"></script>
   	
   	<!-- Load JQuery::UI -->
   	<script type="text/javascript" src="/libraries/external/JQuery-1.4.2/plugins/jquery-ui-1.8.2.custom.min.js"></script>
   	
   	<!-- Load JQuery::Validation -->
   	<script type="text/javascript" src="/libraries/external/JQuery-1.4.2/plugins/jquery.validate.js"></script>
	
   	<!-- Load JQuery::Preload -->
   	<script type="text/javascript" src="/libraries/external/JQuery-1.4.2/plugins/jquery.preload-min.js"></script>
   	
</head>

<body id="www-website-com">

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