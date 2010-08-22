<!DOCTYPE html>
<html lang="en">

<head>
    
    <!-- Meta -->
    <meta charset="utf-8" />

    <!-- Title -->
    <title><?php echo __("Pages Component | Appleseed"); ?></title>
    
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
        
		 	<?php $zApp->Components->Go ( "profile" ); ?>
		 	
		 	<?php $zApp->Components->Go ( "appleseed", "appleseed", "donate" ); ?>
		  	
		</div>
        
        <div id="page_right" class="grid_9">
        
		  	<?php $zApp->Components->Go ( "profile", "menu", "menu" ); ?>
		  	
			<?php $zApp->Components->Go ( "pages", "pages", "pages" ); ?>
        
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
