<!DOCTYPE html>
<html lang="en">

<head>
  	<?php $zApp->Components->Go ( 'system', 'head', 'head' ); ?>
</head>

<body id="appleseed">

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

<?php $zApp->Components->Go ( "system", "system", null, "data" ); ?>
