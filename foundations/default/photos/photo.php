
<!DOCTYPE html>
<html lang="en">

<head>
    
    <!-- Meta -->
    <meta charset="utf-8" />

    <!-- Title -->
    <title><?php echo __("Photos For | Appleseed"); ?></title>
    
    <!-- Links -->
    <?php $zApp->Theme->UseStyles (); ?>
    
    <!-- Javascript --> 
    <!--[if IE]>
    <script src="js/html5.js"></script>
    <![endif]-->
    
</head>

<body id="www-website-com">

    <div id="sys_message" class="container_12">
  		<?php $zApp->Components->Go ( "system" ); ?>
    </div>

    <header class="container_12">
    
  		<?php $zApp->Components->Go ( "header" ); ?>

    </header>

    <div class="clear"></div>

    <div id="page" class="container_12">
    
        <div id="page_left" class="grid_3">
        
		  	<?php $zApp->Components->Go ( "profile" ); ?>
		  	
        </div>
        
        <div id="page_right" class="grid_9">
        
		  	<?php $zApp->Components->Go ( "profile", "menu", "menu" ); ?>
		  	
			<?php $zApp->Components->Go ( "photos", "photos", "photo" ); ?>
        
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