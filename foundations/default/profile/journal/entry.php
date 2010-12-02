<!DOCTYPE html>
<html lang="en">

<head>
	
	<!-- Meta -->
	<meta charset="utf-8" />

	<!-- Title -->
	<title><?php echo __("Journal"); ?></title>
	
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

<body id="appleseed">

  	<?php $zApp->Components->Go ( "system" ); ?>
	
	<div class="clear"></div>

	<!-- System Message -->
	<header id="appleseed-header">
 		<?php $zApp->Components->Go ( "header" ); ?>
 	</header>
 	
	<div id="appleseed-logo"></div>
	
	<div id="appleseed-container" class="container_16">
	
    	<div id="appleseed-profile" class="container_16">
    		<div id="appleseed-profile-status" class="container_16">
    			<div id="status-container" class="grid_12 push_4">
					<?php $zApp->Components->Go ( "profile", "status", "status" ); ?>
    			</div>
    		</div>
	       	<div id="appleseed-profile-menu" class="container_16">
	       		<nav id="profile-tabs" class="grid_9 push_4">
					<?php $zApp->Components->Go ( "profile", "tabs", "tabs" ); ?>
		       	</nav>
		       	<div id="profile-search" class="grid_3 push_4">
					<?php $zApp->Components->Go ( "search", "search", "local" ); ?>
				</div>
			</div>
       
			<div id="appleseed-profile-main" class="grid_16">
				<div id="appleseed-profile-info" class="grid_4 alpha">
					<div id="profile-photo">
					  	<?php $zApp->Components->Go ( "photos", "profile", "profile" ); ?>
					</div>
					<div id="profile-contact">
					  	<?php $zApp->Components->Go ( "profile", "contact", "contact" ); ?>
					</div>
					<div id="profile-summary">
					  	<?php $zApp->Components->Go ( "profile", "summary", "summary" ); ?>	
					</div>
					<div id="profile-invites">
					  	<?php $zApp->Components->Go ( "user", "invites", "invites" ); ?>	 
					</div>
					<div id="profile-mutual">
					  	<?php $zApp->Components->Go ( "friends", "mutual", "summary" ); ?>	 
					</div>
					<div id="profile-reject">
					  	<?php $zApp->Components->Go ( "profile", "reject", "reject" ); ?>
					</div>
				</div>
				<div id="appleseed-profile-content" class="grid_12 omega">
					<?php $zApp->Components->Go ( "journal", "entry", "entry" ); ?>
				</div>
			</div>
		</div>
        
    </div>

	<div class="clear"></div>
	
    <footer id="appleseed-footer" class="container_16">
 		<?php $zApp->Components->Go ( "footer" ); ?>
 	</footer>
 	
	<div class="clear"></div>
	
	<?php $zApp->Components->Go ( "system", "system", null, "data" ); ?>
	
</body>
</html>