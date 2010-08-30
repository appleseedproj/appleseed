<!DOCTYPE html>
<html lang="en">

<head>
	
	<!-- Meta -->
	<meta charset="utf-8" />

	<!-- Title -->
	<title><?php echo __("Layout"); ?></title>
	
	<!-- Links -->
	<link rel="stylesheet" href="/themes/default/style/html5reset-1.4.1.css" /> 
	<link rel="stylesheet" href="/themes/default/style/fonts-min.css" /> 
	<link rel="stylesheet" href="/themes/default/style/960.css" /> 
	<link rel="stylesheet" href="/themes/default/style/default.css" /> 
	<link rel="stylesheet" href="/themes/default/style/theme.css" /> 
	
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

	<!-- System Message -->
	<?php $zApp->Components->Go ( "system" ); ?>
	
	<div class="clear"></div>

	<!-- Header -->
	<header id="appleseed-header">
  		<?php $zApp->Components->Go ( "header" ); ?>
	</header>
	
	<div id="appleseed-logo"></div>
	
	<div id="appleseed-container" class="container_16">
	
    	<div id="appleseed-profile" class="container_16">
	       	<div id="appleseed-profile-menu" class="container_16">
	       		<div id="profile-tabs" class="grid_9 push_4">
	       			<nav>
	       				<ul>
	       					<li class="selected"><a href="#">Login</a></li>
	       					<li><a href="#">Remote</a></li>
		       			</ul>
		       		</nav>
		       	</div>
		       	<div id="profile-search" class="grid_3 push_4">
					<input type="text" name="search" placeholder="Search..." class="search local"><input type="submit" name="search" value="" class="search-submit local">
				</div>
			</div>
       
			<div id="appleseed-profile-main" class="grid_16">
				<div id="appleseed-profile-info" class="grid_4 alpha">
					<?php $zApp->Components->Go ( "login", "info", "info" ); ?>
        
		 			<?php $zApp->Components->Go ( "appleseed", "appleseed", "donate" ); ?>
		  	
				</div>
				<div id="appleseed-profile-content" class="grid_12 omega">
					<section id="profile-form"> 
						<?php $zApp->Components->Go ( "login", "login", "login" ); ?>
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
<!DOCTYPE html>
<html lang="en">

<head>
    
    <!-- Meta -->
    <meta charset="utf-8" />

    <!-- Title -->
    <title><?php echo __("Login Component | Appleseed"); ?></title>
    
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
   	
</head>

<body id="www-website-com">

	<?php $zApp->Components->Go ( "system" ); ?>

    <header class="container_12">
    
  		<?php $zApp->Components->Go ( "header" ); ?>

    </header>

    <div class="clear"></div>

    <div id="page" class="container_12">
    
        <div id="page_left" class="grid_3">
        
			<?php $zApp->Components->Go ( "login", "info", "info" ); ?>
        
		 	<?php $zApp->Components->Go ( "appleseed", "appleseed", "donate" ); ?>
		  	
		</div>
        
        <div id="page_right" class="grid_9">
        
			<?php $zApp->Components->Go ( "login", "login", "login" ); ?>
        
        </div>
        
        <div class="clear"></div>
        
    </div>

    <div class="clear"></div>

    <footer class="container_12">
    
		<?php $zApp->Components->Go ( "footer" ); ?>
        
        <div class="clear"></div>
        
    </footer>

	<?php $zApp->Components->Go ( "debug" ); ?>
        
</body>
</html>
