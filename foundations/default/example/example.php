<!DOCTYPE html>
<html lang="en">

<head>
	
	<!-- Meta -->
	<meta charset="utf-8" />

	<!-- Title -->
    <title><?php echo __("Example Component | Appleseed"); ?></title>
	
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
	
 	<?php $zApp->Components->Go ( "header" ); ?>

	<div id="appleseed-logo"></div>
	
	<div id="appleseed-container" class="container_16">
	
    	<div id="appleseed-admin" class="container_16">
	       	<div id="appleseed-admin-menu" class="container_16">
	       		<div id="admin-tabs" class="grid_9 push_4">
	       			<nav>
	       				<ul>
	       					<li class="selected"><a href="/example/">Example</a></li>
		       			</ul>
		       		</nav>
		       	</div>
		       	<div id="admin-search" class="grid_3 push_4">
					<input type="text" name="search" placeholder="Search..." class="search local"><input type="submit" name="search" value="" class="search-submit local">
				</div>
			</div>
       
			<div id="appleseed-admin-main" class="grid_16">
				<div id="appleseed-admin-main-menu" class="grid_4 alpha">
					<section id="admin-main-menu">
						<h1>Example</h1>
					</section>
				</div>
				<div id="appleseed-admin-content" class="grid_12 omega">
		 			<?php
				  	/*
				  	 * @tutorial Parameters:  
				  	 * @tutorial string pComponent, string pController, string pView, string pTask, array pData
				  	 * 
				  	 * @tutorial You can shorten a component call by putting pData earlier than 
				  	 * @tutorial it should be.  For instance:
				  	 * 
				  	 * @tutorial ->Go ( "example", array ( "Key" => "Value" ) );
				  	 * 
				  	 * @tutorial The system will detect the array being passed, and call the example 
				  	 * @tutorial component with the default controller, view, and task.
				  	 */
				  	?>
					<?php $zApp->Components->Go ( "example", "example", "example" ); ?>
				</div>
			</div>
		</div>
        
    </div>

	<div class="clear"></div>
    
 	<?php $zApp->Components->Go ( "footer" ); ?>

</body>
</html>