<!DOCTYPE html>
<html lang="en">

<head>
    
    <!-- Meta -->
    <meta charset="utf-8" />

    <!-- Title -->
    <title><?php echo __("Layout"); ?></title>
    
    <!-- Links -->
    <?php $zApp->Theme->UseStyles (); ?>
    
    <!-- Javascript --> 
    <!--[if IE]>
    <script src="/themes/default/style/html5.js"></script>
    <![endif]-->
    
</head>

<body id="www-website-com">

	<!-- System Message -->
	<div id="sys_message" class="container_12"> 
		<p> System Message </p> 
	</div> 

    <header class="container_12">
    
    	Header
    	
    </header>
    
    <style>
    	div.grid960 { 
    		background-color:#82b22c; 
    		color:#fafafa;
    		margin-bottom:10px; 
    		text-align:center;
    	}
    	header#appleseed_header {
    		width:100%;
    		text-align:center;
    		color:#d9fa9e;
    		background-color:#406300;
    	}
    </style>
    
    <header id="appleseed_header">
    	<p>Test</p>
    </header>
    
    <!-- 960 Grid Test -->
    <div class="container_12">
   		<div class="grid_6 push_3 grid960">
			<p>logo</p>
		</div>
	
		<div class="grid_3 pull_6 grid960">
			<p>text column</p>
		</div>
	
		<div class="grid_3 grid960">
			<p>text column</p>
		</div>
	
		<div class="grid_12 grid960">
			<p>big box</p>
		</div>
		
		<div class="grid_3 grid960">
			<p>profile</p>
		</div>
		
		<div class="grid_4 push_3 grid960">
			<p>profile</p>
		</div>
	</div>

	<div class="clear"></div>
    
    <div id="page" class="container_12">
    
        <div id="page_left" class="grid_3">
        
        	left
        
		</div>
        
        <div id="page_right" class="grid_9">
        
        	<div>
        		tabs
        	</div>
		  	
		  	<div>
		  		content
		  	</div>
        </div>
        
    </div>

    <footer class="container_12">
    
    	Footer 
    	
    </footer>

</body>
</html>

