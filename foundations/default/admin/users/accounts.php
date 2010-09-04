<!DOCTYPE html>
<html lang="en">

<head>
	
	<!-- Meta -->
	<meta charset="utf-8" />

	<!-- Title -->
    <title><?php echo __("Admin | Appleseed"); ?></title>
	
	<!-- Links -->
	<link rel="stylesheet" href="/themes/default/style/html5reset-1.4.1.css" /> 
	<link rel="stylesheet" href="/themes/default/style/fonts-min.css" /> 
	<link rel="stylesheet" href="/themes/default/style/960.css" /> 
	<link rel="stylesheet" href="/themes/default/style/default.css" /> 
	<link rel="stylesheet" href="/themes/default/style/theme.css" /> 
	<link rel="stylesheet" href="/themes/default/style/admin.css" /> 
	
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
   	
</head>

<body id="www-website-com">
	
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
						<?php $zApp->Components->Go ( "user", "admin", "admin.accounts" ); ?>
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