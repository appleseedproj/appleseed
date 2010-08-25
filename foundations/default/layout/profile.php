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
	
	<!-- Javascript --> 
	<!--[if IE]>
	<script src="/themes/default/style/html5.js"></script>
	<![endif]-->
	
</head>

<body id="www-website-com">

	<style>
		/*
			#406300 - dark green
			#80af1f - green
			#c0d895 - light green
			
			#f1fed5 - light lima
			
			#bf4630 - blood red
			
			#2a2a2a - black
			#8a8a8a - grey
			#fafafa - white
		*/
	
		/* Global */
		html { background: #c0d895; }
		
		a { text-decoration:none; background:none no-repeat left center; }
		
		a { color:#406300; padding-left:8px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position: -96px -45px; }
		a:hover { text-decoration:underline; cursor:pointer; }
		
		div { text-align:left; }
		
		section { font-size:90%; }
		section h1 { color:#bf4630; font-size:120%; border-bottom:1px solid #406300; }
		
		section p { clear:both; }
		
		input.search { width:232px; height:18px; margin:0; padding:2px 4px; border:none; background-color:#fafafa; }
		input.search-submit { width:22px; height:22px; margin:0; padding:0; border:none; color:#fafafa; background-color:#fafafa; background-image:url('/themes/default/images/appleseed-icons.png'); background-position:2px -46px; }
		
		input.search {  -webkit-border-bottom-left-radius:4px; -webkit-border-top-left-radius:4px;  -moz-border-radius-bottomleft:4px; -moz-border-radius-topleft:4px; -border-bottom-left-radius:4px; -border-top-left-radius:4px;  }
		input.search-submit {  -webkit-border-bottom-right-radius:4px; -webkit-border-top-right-radius:4px;  -moz-border-radius-bottomright:4px; -moz-border-radius-topright:4px;  -border-bottom-right-radius:4px; -border-top-right-radius:4px;  }
		
		nav.pagination ol { float:right; clear:both; }
		nav.pagination ol li { list-style-type:none; float:left; }
		nav.pagination ol li button { border:none; background:none; font-size:80%; font-weight:bold; color:#406300; margin:0 1px; padding:0 1px; }
		nav.pagination ol li button:hover { text-decoration:underline; cursor:pointer; }
		nav.pagination ol li.selected button { border-bottom:2px solid #406300; }
		nav.pagination ol li.first,  nav.pagination ol li.prev,  nav.pagination ol li.next,  nav.pagination ol li.last { margin:0 2px; }
		
		.additional-info { margin-left:10px; color:#8a8a8a; font-style:italic; font-weight:normal; font-size:80%; }
			
		/* appleseed-logo */
		#appleseed-logo { position:absolute; margin:32px 0 0 20px; width:150px; height:32px; background: url('/themes/default/images/appleseed-logo.png') no-repeat; display:block; }
		#appleseed-logo:hover { background: url('/themes/default/images/appleseed-logo.png') 0px -33px no-repeat; display:block; }
		
		
		/* System Messages */
		#system-message { }
		#system-message { margin:0; padding:0; width:100%; height:32px; text-align:center; color:#d9fa9e; border-top:1px solid #2a2a2a; border-bottom:1px solid #2a2a2a; background-color:#406300; }
		#system-message p { margin-top:6px; padding:0; font-size:100%; }
		
		#system-message a { color:#f1fed5; padding-left:8px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position: -96px -94px; }
		#system-message a:hover { text-decoration:underline; }
		
		
		/* Header,Footer */
		footer#appleseed-footer, header#appleseed-header { width:100%; height:46px; padding:0; margin:0; text-align:center; color:#d9fa9e; border-top:1px solid #406300; border-bottom:1px solid #406300; background-color:#80af1f; }
		
		/* Footer */
		footer#appleseed-footer, header#appleseed-header { width:100%; height:46px; padding:0; margin:0; text-align:center; color:#d9fa9e; border-top:1px solid #406300; border-bottom:1px solid #406300; background-color:#80af1f; }
		footer#appleseed-footer { color:#c0d895; }
		footer#appleseed-footer a { color:#f1fed5; padding-left:8px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position: -96px -93px; }
		footer#appleseed-footer a:hover { color:#406300; padding-left:8px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position: -96px -45px; }
		footer#appleseed-footer p { margin:3px 0; padding:0; }
		
		/* Header */
		#appleseed-header a { color:#406300; text-decoration:none; }
		#appleseed-header a:hover { color:#406300; text-decoration:underline; }
		
		#header-search { margin-top:14px; }
		
		#header-notifications { margin-top:14px; }
		#header-notifications a { float:left; height:16px; width:24px; overflow:visible; padding:0 0 12px 0; margin:0; background:none; }
		#header-notifications a:hover { text-decoration:none; }
		#header-notifications span { float:left; }
		
		
		#header-notifications span.new-friend { padding:0; width:16px; height:16px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position:-24px -48px; }
		#header-notifications span.new-mail { padding:0; margin-left:0; width:16px; height:16px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position:2px -71px; }
		#header-notifications span.new-notification { padding:0; width:16px; height:16px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position:-48px -48px; }
		
		#header-notifications span.new-friend:hover, #header-notifications span.new-friend.notify { padding:0; width:16px; height:16px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position:-24px -96px; }
		#header-notifications span.new-mail:hover, #header-notifications span.new-mail.notify { padding:0; width:16px; height:16px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position:2px -119px; }
		#header-notifications span.new-notification:hover, #header-notifications span.new-notification.notify { padding:0; width:16px; height:16px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position:-48px -96px; }
		
		#header-notifications span.new-friend-count { clear:both; position:relative; left:6px; top:-6px; margin-right:2px; padding:1px 4px; color:#fafafa; background-color:#bf4630; font-size:80%; }
		#header-notifications span.new-mail-count { clear:both; position:relative; left:6px; top:-6px; margin-right:2px; padding:1px 4px; color:#fafafa; background-color:#bf4630; font-size:80%; }
		#header-notifications span.new-notification-count { clear:both; position:relative; left:6px; top:-6px; margin-right:2px; padding:1px 4px; color:#fafafa; background-color:#bf4630; font-size:80%; }
		
		#header-notifications span.new-friend-count.none { display:none; }
		#header-notifications span.new-mail-count.none { display:none }
		#header-notifications span.new-notification-count.none { display:none }
		
		#header-notifications span.new-friend-count { -webkit-border-radius:6px; -moz-border-radius:6px; -border-radius:6px;  }
		#header-notifications span.new-mail-count { -webkit-border-radius:6px; -moz-border-radius:6px; -border-radius:6px;  }
		#header-notifications span.new-notification-count { -webkit-border-radius:6px; -moz-border-radius:6px; -border-radius:6px;  }
		
		#header-current { }
		#header-current img.current-icon { float:left; position:relative; left:-8px; height:24px; width:24px; margin-top:4px; }
		#header-current span.current-remote { position:relative; top:2px; padding:0; width:16px; height:16px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position:-72px -48px; }
		
		#header-current span { float:left; }
		#header-current span.current-remote { padding:0; width:16px; height:16px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position:-72px -48px; }
		#header-current span.current-identity { margin:0; padding:0; margin-top:16px; max-width:300px; overflow:hidden; text-overflow: ellipsis; -o-text-overflow: ellipsis; }
		#header-current span.current-identity:hover { overflow:visible;text-overflow:none; -o-text-overflow:none; }
		#header-current span.current-identity a { margin:0; padding:2px 2px; background:none; font-size:80%; font-weight:bold; }
		
		#header-links { text-align:right; margin-top:16px; }
		#header-links a { float:left; margin:0; padding:2px 3px; background:none; font-size:85%;}
		
		/* Main */
		#appleseed-profile { float:left; background-color:#f1fed5; min-height:840px; }
		#appleseed-profile-main { }
		#appleseed-profile-menu { height:80px; background-color:#c0d895; border-bottom:1px solid #80af1f; }
		
		#appleseed-profile-info { float:left; position:relative; top:-60px;  }
		#appleseed-profile-content { }
		
		/* profile-info */
		margin:0; padding:0; #profile-photo { }
		#profile-photo img { max-width:220px; border-bottom:1px solid #80af1f; }
		
		/* profile-summary */
		#summary-information h2, p { font-size:90%; }
		#summary-information h2 { margin:0; padding:0; margin-top:10px; }
		#summary-information p { margin:0; padding:0; }
		#summary-information p, #summary-information h2 { margin-left:10px; }
		
		/* profile-tabs */
		#profile-tabs { float:left; position:relative; top:49px; }
		#profile-tabs a { background:none; color:#406300; margin:0; padding:0; }
		#profile-tabs nav ul li { float:left; padding:4px 4px 0 4px; margin:0px 1px; background-color:#80af1f; border:1px solid #406300; }
		#profile-tabs nav ul li a { color:#c0d895; }
		#profile-tabs nav ul li:nth-child(1)   { margin-left:1px; }
		#profile-tabs nav ul li.selected { background-color:#f1fed5; border-bottom:1px solid #f1fed5; }
		#profile-tabs nav ul li.selected a { color:#2a2a2a; }
		#profile-tabs nav ul li:hover { background-color:#f1fed5; border-bottom:1px solid #f1fed5; }
		#profile-tabs nav ul li:hover a { color:#2a2a2a; }
		
		/* profile-search */
		#profile-search { float:left; position:relative; top:58px; text-align:right; }
		#profile-search input.search { font-size:80%; width:130px; height:16px; margin:0; padding:2px 4px; border:none; background-color:#fafafa; }
		#profile-search input.search-submit { width:22px; height:20px; margin:0; padding:0; border:none; color:#fafafa; background-color:#fafafa; background-image:url('/themes/default/images/appleseed-icons.png'); background-position:2px -46px; }
		
		/* profile-contact */
		#profile-contact ul li { font-size:90%; list-style-type:none; margin:0 0 0 10px; padding:0; }
		#profile-contact ul li button { text-decoration:none; border:none; background:none no-repeat left center; }
		#profile-contact ul li button { color:#406300; padding-left:8px; background-image:url('/themes/default/images/appleseed-icons.png'); background-position: -96px -45px; }
		#profile-contact ul li button:hover { text-decoration:underline; cursor:pointer; }
		
		/* profile-mutual */
		#profile-mutual #mutual-friends ul li { list-style-type:none; padding:0; margin:0; }
		#profile-mutual #mutual-friends ul li img { float:left; width:32px; height:32px; padding:0; margin:5px 5px 0 0; }
		
		/* profile-friends */
		#profile-friends ul { float:left; border-bottom:1px solid #c0d895; padding-bottom:10px;}
		#profile-friends ul li { clear:both; list-style-type:none; padding:0; margin:0; }
		#profile-friends ul li img.friends-icon { float:left; clear:none; width:64px; height:64px; padding:0; margin:10px 5px 0 0; border-bottom:1px solid #406300; }
		
		#profile-friends div.friends-info { float:left; width:90%; margin-top:10px; }
		#profile-friends span { font-size:90%; }
		#profile-friends span.friends-fullname { float:left; width:50%; font-weight:bold; }
		#profile-friends span.friends-location { float:right; width:50%; text-align:right; font-weight:bold; }
		#profile-friends p.friends-status { float:left; margin:0; padding:0; width:85%; min-height:41px; font-style:italic; color:#8a8a8a; }
		#profile-friends span.friends-mutual-count { float:right; margin-top:0px; text-align:right; font-style:italic; color:#8a8a8a; }
		#profile-friends a.friends-identity { float:left; background:none; padding:0; font-size:90%; }
		
		/* appleseed-debug */
		#appleseed-debug { margin-top:10px; }
		
		#debug-container { float:left; background-color:#f1fed5; border-top:1px solid #406300; color:#406300; }
		
		#debug-main { }
		
		#debug-tabs { position:relative; top:1px; }
		#debug-tabs a { background:none; color:#406300; margin:0; padding:0; }
		#debug-tabs nav ul li { float:left; padding:4px 4px 0 4px; margin:0px 1px; background-color:#80af1f; border:1px solid #406300; }
		#debug-tabs nav ul li a { color:#c0d895; }
		#debug-tabs nav ul li:nth-child(1)   { margin-left:1px; }
		#debug-tabs nav ul li.selected { background-color:#f1fed5; border-bottom:1px solid #f1fed5; }
		#debug-tabs nav ul li.selected a { color:#2a2a2a; }
		#debug-tabs nav ul li:hover { background-color:#f1fed5; border-bottom:1px solid #f1fed5; }
		#debug-tabs nav ul li:hover a { color:#2a2a2a; }
		
		/* Temporary */
		#temporary-debug-info { display:none; }
		
	</style>
	
	<!-- System Message -->
	<div id="system-message"> 
		<p> Appleseed is early beta software, expect bugs along the way, and send feedback to <a href="mailto:feedback@appleseedproject.org">feedback@appleseedproject.org</a> </p> 
	</div> 
	
	<div class="clear"></div>

	<!-- System Message -->
	<header id="appleseed-header">
		<div id="appleseed-header-content" class="container_16">
			<div id="header-search" class="grid_5"> 
				<input type="text" name="search" placeholder="Search..." class="search"><input type="submit" name="search" value="" class="search-submit">
			</div>
			<div id="header-notifications" class="grid_2">
				<a href="#"><span class="new-friend "></span><span class="new-friend-count ">2</span></a>
				<a href="#"><span class="new-mail"></span><span class="new-mail-count ">9</span></a>
				<a href="#"><span class="new-notification "></span><span class="new-notification-count ">6</span></a>
			</div>
			<div id="header-current" class="grid_6"> 
				<span class="current-remote"></span>
				<img class="current-icon" src="http://sphotos.ak.fbcdn.net/hphotos-ak-snc3/hs223.snc3/21036_243577049405_510304405_3041527_5461526_n.jpg">
				<span class="current-identity"><a href="#">michael.chisari@developer.appleseedproject.org</a></span>
			</div>
			<div id="header-links" class="grid_3">
				<a class="links-news" href="#">News</a>
				<a class="links-profile" href="#">Profile</a>
				<a class="links-options" href="#">Options</a>
				<a class="links-logout" href="#">Logout</a>
			</div>
		</div>
	</header>
	
	<div id="appleseed-logo"></div>
	
	<div id="appleseed-container" class="container_16">
	
    	<div id="appleseed-profile" class="container_16">
	       	<div id="appleseed-profile-menu" class="container_16">
	       		<div id="profile-tabs" class="grid_9 push_4">
	       			<nav>
	       				<ul>
	       					<li><a href="#">Options</a></li>
	       					<li><a href="#">News</a></li>
	       					<li><a href="#">Wall</a></li>
							<li><a href="#">Info</a></li>
							<li class="selected"><a href="#">Friends</a></li>
							<li><a href="#">Journals</a></li>
							<li><a href="#">Photos</a></li>
							<li><a href="#">Events</a></li>
							<li><a href="#">Groups</a></li>
		       			</ul>
		       		</nav>
		       	</div>
		       	<div id="profile-search" class="grid_3 push_4">
					<input type="text" name="search" placeholder="Search..." class="search"><input type="submit" name="search" value="" class="search-submit">
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
					<nav class="pagination">   
						<ol>     
							<li class="first"><button   type="submit" name="gSCROLLSTART[admin.users.questions]" value="FIRST">First</button></li> 
    						<li class="prev"><button   type="submit" name="gSCROLLSTART[admin.users.questions]" value="PREVIOUS">Prev</button></li> 
    						<li class="selected"><button   type="submit" name="gSCROLLSTART[admin.users.questions]" value="-1">1</button></li> 
    						<li><button type="submit" name="gSCROLLSTART[admin.users.questions]" value="9">2</button></li> 
    						<li class="next"><button   type="submit" name="gSCROLLSTART[admin.users.questions]" value="10">Next</button></li> 
    						<li class="last"><button   type="submit" name="gSCROLLSTART[admin.users.questions]" value="10">Last</button></li> 
  						</ol>
  					</nav>
					<section id="profile-friends">
						<h1>Michael's Friends <span class="profile-friends-count additional-info">246 friends</span> </h1>
						<p>Lorem Ipsum <a href="">is simply dummy text</a> of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
						<ul>
							<li>
								<img class="friends-icon" src="http://sphotos.ak.fbcdn.net/hphotos-ak-snc3/hs316.snc3/28457_10150199647515293_527500292_13078983_3735516_s.jpg">
								<div class="friends-info">
									<span class="friends-fullname">Charlie Donna</span>
									<span class="friends-location">Chicago, IL</span>
									<p class="friends-status">When some people left Avatar, they had feelings of depression and disconnectedness. This was because they  couldn't stand the fact that they didn't live on the paradise world of Pandora... this is how i feel about Toronto  because of Scott Pilgrim vs The World</p>
									<span class="friends-mutual-count">7 mutual friends</span>
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
    
    	<p> <a href="http://opensource.appleseedproject.org">Appleseed Social Networking Software</a> is Copyright &copy; 2004-2010 by Michael Chisari under the GNU General Public License. All Rights Reserved. </p>
    	
    </footer>
    
	<div class="clear"></div>
	
	<!-- Debug -->
	<div id="appleseed-debug" class="container_16">
		<div id="debug-menu" class="container_16">
			<div id="debug-tabs" class="grid_16">
				<nav>
					<ul>
						<li><a href="#">Warnings</a></li>
						<li><a href="#">Errors</a></li>
						<li><a href="#">SQL Queries</a></li>
						<li class="selected" ><a href="#">Benchmarks</a></li>
					</ul>
				</nav>
				
			</div>
		</div>
		<div id="debug-container" class="container_16">
			<div id="debug-main" class="grid_16">
			
				<section id="debug-warnings">
					<h1>Warnings</h1>
				</section>
				
				<section id="debug-errors">
					<h1>Errors</h1>
				</section>
				
				<section id="debug-queries">
					<h1>SQL Queries</h1>
				</section>
				
				<section id="debug-benchmarks">
					<h1>Benchmarks</h1>
				</section>
			</div>
		</div>
	</div>
    
</body>
</html>