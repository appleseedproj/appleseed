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
	<link rel="stylesheet" href="/foundations/default/layout/theme.css" /> 
	
	<!-- Javascript --> 
	<!--[if IE]>
	<script src="/themes/default/style/html5.js"></script>
	<![endif]-->
	
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
					<section id="general_form"> 
						<h1>Form Elements</h1>
						
						<form action="whatever.php">
							<fieldset> 
								<legend>Tableless Form</legend>
                    
									<div><label>Input #1</label><input /></div>
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
						
						               <!--
                <p>Description of what to do in this particular section of the admin area.</p> 
                
                <nav> 
                    <ul> 
                        <li><a href="#">Move Up &uarr;</a></li> 
                        <li><a href="#">Move Down &darr;</a></li> 
                        <li><a href="#">Delete</a></li> 
                    </ul> 
                </nav> 
                
                <table> 
                    <thead> 
                        <tr> 
                            <th scope="col"><a href="#"># &uarr;</a></th> 
                            <th scope="col"><a href="#">Name</a></th> 
                            <th scope="col"><a href="#">Type</a></th> 
                            <th scope="col"><a href="#">Lang</a></th> 
                            <th scope="col"><input type="checkbox" /></th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <tr class="odd"> 
                            <td scope="row">1</td> 
                            <td><a href="#">What is your favorite color?</a></td> 
                            <td>String</td> 
                            <td>en</td> 
                            <td><input type="checkbox" /></td> 
                        </tr> 
                        <tr> 
                            <td scope="row">2</td> 
                            <td><a href="#">What is your favorite color?</a></td> 
                            <td>String</td> 
                            <td>en</td> 
                            <td><input type="checkbox" /></td> 
                        </tr> 
                        <tr class="odd"> 
                            <td scope="row">3</td> 
                            <td><a href="#">What is your favorite color?</a></td> 
                            <td>String</td> 
                            <td>en</td> 
                            <td><input type="checkbox" /></td> 
                        </tr> 
                        <tr> 
                            <td scope="row">4</td> 
                            <td><a href="#">What is your favorite color?</a></td> 
                            <td>String</td> 
                            <td>en</td> 
                            <td><input type="checkbox" /></td> 
                        </tr> 
                        <tr class="odd"> 
                            <td scope="row">5</td> 
                            <td><a href="#">What is your favorite color?</a></td> 
                            <td>String</td> 
                            <td>en</td> 
                            <td><input type="checkbox" /></td> 
                        </tr> 
                        <tr> 
                            <td scope="row">6</td> 
                            <td><a href="#">What is your favorite color?</a></td> 
                            <td>String</td> 
                            <td>en</td> 
                            <td><input type="checkbox" /></td> 
                        </tr> 
                        <tr class="odd"> 
                            <td scope="row">7</td> 
                            <td><a href="#">What is your favorite color?</a></td> 
                            <td>String</td> 
                            <td>en</td> 
                            <td><input type="checkbox" /></td> 
                        </tr> 
                        <tr> 
                            <td scope="row">8</td> 
                            <td><a href="#">What is your favorite color?</a></td> 
                            <td>String</td> 
                            <td>en</td> 
                            <td><input type="checkbox" /></td> 
                        </tr> 
                        <tr class="odd"> 
                            <td scope="row">9</td> 
                            <td><a href="#">What is your favorite color?</a></td> 
                            <td>String</td> 
                            <td>en</td> 
                            <td><input type="checkbox" /></td> 
                        </tr> 
                        <tr> 
                            <td scope="row">10</td> 
                            <td><a href="#">What is your favorite color?</a></td> 
                            <td>String</td> 
                            <td>en</td> 
                            <td><input type="checkbox" /></td> 
                        </tr> 
                    </tbody> 
                </table> 
                -->
						
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