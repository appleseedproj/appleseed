<!DOCTYPE html>
<html lang="en">

<head>
  	<?php $zApp->Components->Go ( 'system', 'head', 'head' ); ?>
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
				</div>
				<div id="appleseed-profile-content" class="grid_12 omega">
					<?php $zApp->Components->Go ( "friends", "friends", "requests" ); ?>
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
