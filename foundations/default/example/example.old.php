<!DOCTYPE html>
<html lang="en">

<head>
    
    <!-- Meta -->
    <meta charset="utf-8" />

    <!-- Title -->
    <title><?php echo __("Example Component | Appleseed"); ?></title>
    
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

    <header class="container_12">
    
    </header>

    <div class="clear"></div>

    <div id="page" class="container_12">
    
        <div id="page_left" class="grid_3">
        
		</div>
        
        <div id="page_right" class="grid_9">
        
		  	<?php $zApp->Components->Go ( "profile", "menu", "menu" ); ?>
		  	
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