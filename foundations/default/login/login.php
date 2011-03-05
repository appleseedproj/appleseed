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
   	<script type="text/javascript" src="/client/default/init/jloader.init.js"></script>
   	<script type="text/javascript" src="/foundations/default/default.js"></script>
   	
	<!-- Load JQuery -->
   	<script type="text/javascript" src="/client/default/include/JQuery-1.4.2/jquery-1.4.2.min.js"></script>
   	
   	<!-- Load JQuery::UI -->
   	<script type="text/javascript" src="/client/default/include/JQuery-1.4.2/plugins/jquery-ui-1.8.2.custom.min.js"></script>
   	
   	<!-- Load JQuery::Validation -->
   	<script type="text/javascript" src="/client/default/include/JQuery-1.4.2/plugins/jquery.validate.js"></script>
	
   	<!-- Load JQuery::Preload -->
   	<script type="text/javascript" src="/client/default/include/JQuery-1.4.2/plugins/jquery.preload-min.js"></script>
   	
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
					<?php $zApp->Components->Go ( "login", "login", "login" ); ?>
 
					<?php $zApp->Components->Go ( "login", "login", "remote" ); ?>

					<?php $zApp->Components->Go ( "login", "login", "join" ); ?>
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
