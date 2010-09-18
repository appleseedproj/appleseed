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
	
   	<!-- Load JQuery::Preload -->
   	<script type="text/javascript" src="/libraries/external/JQuery-1.4.2/plugins/jquery.preload-min.js"></script>
   	
</head>

<body id="www-website-com">

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
					<input type="text" name="search" placeholder="Search..." class="search local"><input type="submit" name="search" value="" class="search-submit local">
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
					<section id="profile-form"> 
						<h1>Form Elements</h1>
						
						<form id="form-elements" action="whatever.php">
							<fieldset> 
								<legend>Tableless Form</legend>
                    
									<div><label>Input #1</label><input class="required" /></div>
									<div><label>Input #2</label><input /></div>
									<div><label>Input #3</label><textarea></textarea></div>
									<div><label>Really long input label #4 testing of how it handles this.</label><textarea></textarea></div>
								</legend>
							</fieldset>
						</form>
						<form method="post" action="#"> 
							<fieldset> 
                    
								<legend>General Form</legend> 
                        
								<p>Description of form's purpose would be here.</p> 

								<fieldset> 
                        
									<legend>Sub-Fieldset 1</legend> 
                            
									<p>Some text entry inputs.</p> 
 
									<label for="text">Text Input <em>*</em></label>
									<input type="text" id="text" />
									
									<label for="passwd">Password Input</label>
									<input type="password" id="passwd" />
									
									<label for="text_area">Text Area Input</label>
									<textarea rows="10" cols="50" id="text_area"></textarea>
									
								</fieldset> 
								
								<fieldset> 
		                        
									<legend>Sub-Fieldset 2</legend> 
                            
									<p>Some "special" inputs.</p> 
 
									<label for="file">File Browser <em>*</em></label>
									<input type="file" id="file" />
									
									<label for="select">Selection Field</label>
									<select id="select"> 
										<optgroup label="Group 1"> 
											<option>Thing 1.1</option> 
											<option>Thing 1.2</option> 
											<option>Thing 1.3</option> 
										</optgroup> 
										<optgroup label="Group 2"> 
											<option>Thing 2.1</option> 
											<option>Thing 2.2</option> 
											<option>Thing 2.3</option> 
										</optgroup> 
										<optgroup label="Group 3"> 
											<option>Thing 3.1</option> 
											<option>Thing 3.2</option> 
											<option>Thing 3.3</option> 
										</optgroup> 
									</select> 
								</fieldset> 
                        
								<fieldset> 
                        
									<legend>Sub-Fieldset 3 <em>*</em></legend> 
                            
									<label>Checkboxes</label> 
									<input type="checkbox" id="checkbox1" name="checkbox_group" /><label class="checkbox" for="checkbox1">Checkbox 1</label> 
									<input type="checkbox" id="checkbox2" name="checkbox_group" /><label class="checkbox" for="checkbox2">Checkbox 2</label> 
									<input type="checkbox" id="checkbox3" name="checkbox_group" /><label class="checkbox" for="checkbox3">Checkbox 3</label> 
								</fieldset>	
								
								<fieldset> 
                        
									<label>Radio Buttons</label> 
									<input type="radio" id="radio1" name="radio_group" /><label class="radio" for="radio3">Radio 1</label> 
									<input type="radio" id="radio2" name="radio_group" /><label class="radio" for="radio3">Radio 2</label> 
									<input type="radio" id="radio3" name="radio_group" /><label class="radio" for="radio3">Radio 3</label> 
									
								</fieldset> 
								
								<p> 
									<input type="submit" value="Submit" /> 
									<input type="reset" value="Reset" /> 
									<input type="button" value="Button" /> 
								</p> 
                        
							</fieldset> 
						</form> 
						
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
	
</body>
</html>