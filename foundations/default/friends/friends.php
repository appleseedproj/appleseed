<!DOCTYPE html>
<html lang="en">

<head>
	
	<!-- Meta -->
	<meta charset="utf-8" />

	<!-- Title -->
	<title><?php echo __("Friends"); ?></title>
	
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
    				<span id="status-name">Michael Chisari</span><span id="status-current">can't. stop.  thinkin' about tomorrow!</span>
    				<!-- <span id="status-name">Michael Chisari</span><span id="status-current">can't. stop.  thinkin' about tomorrow! can't. stop.  thinkin' about tomorrow! can't. stop.  thinkin' about tomorrow! can't. stop.  thinkin' about tomorrow! can't. stop.  thinkin' about tomorrow! can't. stop.  thinkin' about tomorrow! can't. stop.  thinkin' about tomorrow! can't. stop.  thinkin' about tomorrow! can't. stop.  thinkin' about tomorrow!</span> -->
					<abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr>
					<a href="#" class="edit">Edit</a>
    			</div>
    		</div>
	       	<div id="appleseed-profile-menu" class="container_16">
	       		<div id="profile-tabs" class="grid_9 push_4">
	       			<nav>
	       				<ul>
	       					<li><a href="#">News</a></li>
	       					<li><a href="#">Mail</a></li>
	       					<li><a href="#">Options</a></li>
	       					<li><a href="#">Wall</a></li>
							<li><a href="#">Info</a></li>
							<li class="selected"><a href="#">Friends</a></li>
							<li><a href="#">Journals</a></li>
							<li><a href="#">Photos</a></li>
							<li><a href="#">Events</a></li>
							<li><a href="#">Groups</a></li>
							<li class="more"><a href="#">+</a></li>
		       			</ul>
		       		</nav>
		       	</div>
		       	<div id="profile-search" class="grid_3 push_4">
					<?php $zApp->Components->Go ( "search", "search", "local" ); ?>
				</div>
			</div>
       
			<div id="appleseed-profile-main" class="grid_16">
				<div id="appleseed-profile-info" class="grid_4 alpha">
					<div id="profile-photo"><img src="http://sphotos.ak.fbcdn.net/hphotos-ak-snc3/hs223.snc3/21036_243577049405_510304405_3041527_5461526_n.jpg"></div>
					<div id="profile-contact">
						<ul>
							<li><button type="submit" name="Task" value="Friend">Add as a friend</button></li> 
							<li><button type="submit" name="Task" value="Message">Send a message</button></li> 
							<li><button type="submit" name="Task" value="Block">Block this person</button></li> 
						</ul>
					</div>
					<div id="profile-summary">
						<section id="summary-information">
							<h1>Michael's Information</h1>
							
							<h2>Full name</h2>
							<p>Michael Chisari</p>

							<h2>Location</h2>
							<p>Chicago, IL</p>

							<h2>Birthday</h2>
							<p>September 12</p>

							<h2>Gender</h2>
							<p>Male</p>

							<h2>Age</h2>
							<p>30</p>
						</section>
					</div>
					<div id="profile-mutual">
						<section id="mutual-friends">
							<h1>Mutual Friends</h1>
							<ul>
								<li>
									<img src="http://sphotos.ak.fbcdn.net/hphotos-ak-snc4/hs171.snc4/37875_736751926255_18401167_41412055_2880695_s.jpg">
								</li>
								<li>
									<img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg">
								</li>
								<li>
									<img src="http://sphotos.ak.fbcdn.net/hphotos-ak-snc3/hs316.snc3/28457_10150199647515293_527500292_13078983_3735516_s.jpg">
								</li>
							</ul>
						</section>
					</div>
				</div>
				<div id="appleseed-profile-content" class="grid_12 omega">
					<nav class='pagination'>
						<ol>
							<li class='first'><a href="#"><span>First Page</span></a></li>
							<li class='prev'><a href="#"><span>Previous Page</span></a></li>
							<li class='page selected'><a href="#"><span>1</span></a></li>
							<li class='page'><a href="#"><span>2</span></a></li>
							<li class='next'><a href="#"><span>Next Page</span></a></li>
							<li class='last'><a href="#"><span>Last Page</span></a></li>
						</ol>
  					</nav>
					<section id="profile-friends">
						<!--<h1>Michael's Friends <span class="profile-friends-count additional-info">246 friends</span> <span class="mutual-friends-link"><a>Mutual Friends</a></span> </h1>-->
						<h1>Michael's Mutual Friends <span class="profile-friends-count additional-info">246 friends</span> <span class="mutual-friends-link"><a>All Friends</a></span> </h1>
						<p>Lorem Ipsum <a href="">is simply dummy text</a> of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
						<ul>
							<li>
								<img class="friends-icon" src="http://sphotos.ak.fbcdn.net/hphotos-ak-snc3/hs316.snc3/28457_10150199647515293_527500292_13078983_3735516_s.jpg">
								<div class="friends-info">
									<span class="friends-fullname">Charlie Donna</span>
									<span class="friends-location">Chicago, IL</span>
									<p class="friends-status">When some people left Avatar, they had feelings of depression and disconnectedness. This was because they  couldn't stand the fact that they didn't live on the paradise world of Pandora... this is how i feel about Toronto  because of Scott Pilgrim vs The World</p>
									<span class="friends-mutual-count">7 mutual friends</span>
									<span class="friends-add-friend"><button>Add as a friend</button></span>
									<a href="#" class="friends-identity">charlesdonna@appleseedproject.org</a>
								</div>
							</li>
							<li>
								<img class="friends-icon" src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg">
								<div class="friends-info">
									<span class="friends-fullname">Flint Arthur</span> 
									<span class="friends-location">Baltimore, MD</span>
									<p class="friends-status">Reading Eastern Standard Tribe .epub on the train with Android Lust .mp3, on way to meatspace show. Vinyl boots. Tweeting. #cyberpunk</p>
									<span class="friends-mutual-count">36 mutual friends</span>
									<a href="#" class="friends-identity">flint@appleseedproject.org</a>
									
									<div class="friends-editor">
										<span class="friends-circle-add"><select><option disabled="disabled">Friends Circle &darr;</option></select></span>
										<span class="friends-circles">
											<ul>
												<li><span>Baltimore</span> <button>X</button></li>
												<li><span>Geeks</span> <button>X</button></li>
												<li><span>Baltimore</span> <button>X</button></li>
												<li><span>Geeks</span> <button>X</button></li>
												<li><span>Baltimore</span> <button>X</button></li>
												<li><span>Geeks</span> <button>X</button></li>
												<li><span>Baltimore</span> <button>X</button></li>
												<li><span>Geeks</span> <button>X</button></li>
												<li><span>Baltimore</span> <button>X</button></li>
												<li><span>Geeks</span> <button>X</button></li>
											</ul>
										</span>
										<span class='friend-remove'><button>X</button></span>
									</div>
								</div>
							</li>
							<li>
								<img class="friends-icon" src="http://sphotos.ak.fbcdn.net/hphotos-ak-snc4/hs171.snc4/37875_736751926255_18401167_41412055_2880695_s.jpg">
								<div class="friends-info">
									<span class="friends-fullname">Adrienne Ruhf</span>
									<span class="friends-location">Chicago, IL</span>
									<p class="friends-status"></p>
									<span class="friends-mutual-count">32 mutual friends</span>
									<a href="#" class="friends-identity">adjlaru@appleseedproject.org</a>
								</div>
							</li>
						</ul>
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