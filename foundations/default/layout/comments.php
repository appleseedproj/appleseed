<!DOCTYPE html>
<html lang="en">

<head>
  	<?php $zApp->Components->Go ( 'system', 'head', 'head' ); ?>
</head>

<body id="appleseed">

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
					<section id="profile-friends">
						<h1>Michael's Friends <span class="profile-friends-count additional-info">246 friends</span> </h1>
						<ul>
							<li>
								<img class="friends-icon" src="">
								<div class="friends-info">
									<span class="friends-fullname">User #1</span>
									<span class="friends-location">Anywhere, IL</span>
									<p class="friends-status">When some people left Avatar, they had feelings of depression and disconnectedness. This was because they  couldn't stand the fact that they didn't live on the paradise world of Pandora... this is how i feel about Toronto  because of Scott Pilgrim vs The World</p>
									<span class="friends-mutual-count">7 mutual friends</span>
									<a href="#" class="friends-identity">user1@appleseedproject.org</a>
								</div>
							</li>
							<li>
								<img class="friends-icon" src="">
								<div class="friends-info">
									<span class="friends-fullname">User #2</span>
									<span class="friends-location">Nowhere, NV</span>
									<p class="friends-status">Reading Eastern Standard Tribe .epub on the train with Android Lust .mp3, on way to meatspace show. Vinyl boots. Tweeting. #cyberpunk</p>
									<span class="friends-mutual-count">36 mutual friends</span>
									<a href="#" class="friends-identity">user2@appleseedproject.org</a>
								</div>
							</li>
							<li>
								<img class="friends-icon" src="">
								<div class="friends-info">
									<span class="friends-fullname">User #3</span>
									<span class="friends-location">Somewhere, CA</span>
									<p class="friends-status"></p>
									<span class="friends-mutual-count">32 mutual friends</span>
									<a href="#" class="friends-identity">user3@appleseedproject.org</a>
								</div>
							</li>
						</ul>
					</section>
					
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
					<section id="profile-comments">
						<h1>Nested Comments</h1>
						<ol class="comments outer"> 
					
     		               <li> 
     		               	<div class="comment">
     		               		<div class="comment-icon">
									<a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a> 
								</div>
								<div class="comment-content">
									<p><a class="comment-user-link" href="#">Flint Arthur</a> (1) Um... no... that's not what it means. It means that there is an amount of anti-matter/anti-energy that balances the Flint matter/energy; but by no means is that in any sort of coherent arrangement.<br /><br />

It's a very good thing that the amount of anti-matter/anti-energy in the universe balances with the amount of matter/energy. If it wasn't balanced and there was more positive energy/matter... SOMETHING would have had to put that there.</p> 
									<abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr>
									<nav> 
										<ul> 
											<li><a href="#"><span>Reply</span></a></li> 
											<li><a href="#"><span>Hide</span></a></li> 
											<li><a href="#"><span>Delete</span></a></li> 
										</ul> 
									</nav> 
								</div>
							</div>
     		               </li> 
     	               
     	               		<li>
     	               			<ol class="comments nested">
     		               		<li> 
     		               			<div class="comment">
     		               				<div class="comment-icon">
											<a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a> 
										</div>
										<div class="comment-content">
											<p><a class="comment-user-link" href="#">Flint Arthur</a> (2) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p> 
											<abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr>
											<nav> 
												<ul> 
													<li><a href="#"><span>Reply</span></a></li> 
													<li><a href="#"><span>Hide</span></a></li> 
													<li><a href="#"><span>Delete</span></a></li> 
												</ul> 
											</nav> 
										</div>
									</div>
     			              	 </li> 
     			              	 <li>
     			              	 	<ol class="comments nested"> 
     	               					<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (3) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
     	               					<li> 
		     			              	 	<ol class="comments nested"> 
 		    	               					<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (4) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
     	               							<li> 
		     			              	 			<ol class="comments nested"> 
 		    	               							<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (5) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
		     	               							<li> 
				     			              	 			<ol class="comments nested"> 
 				    	               							<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (6) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
		     	               									<li> 
				     			              	 					<ol class="comments nested"> 
 				    	               									<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (7) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
		     	               											<li> 
				     			              	 							<ol class="comments nested"> 
 				    	               											<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (8) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
		     	               													<li> 
				     			              	 									<ol class="comments nested"> 
 				    	               													<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (9) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
				     	               													<li> 
						     			              	 									<ol class="comments nested"> 
 						    	               													<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (10) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
						     	               													<li> 
								     			              	 									<ol class="comments nested"> 
 								    	               													<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (11) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
								     	               													<li> 
										     			              	 									<ol class="comments nested"> 
 										    	               													<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (12) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
										     	               													<li> 
												     			              	 									<ol class="comments nested"> 
 												    	               													<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (13) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
 										    			               												</ol>
 										    			               											</li>
 								    			               												</ol>
 								    			               											</li>
 						    			               												</ol>
 						    			               											</li>
 				    			               												</ol>
 				    			               											</li>
 		    			               												</ol>
 		    			               											</li>
 		    			               										</ol>
 		    			               									</li>
 		    			               								</ol>
 		    			               							</li>
 		    			               						</ol>
 		    			               					</li>
     			               						</ol>
     			               					</li>
     			               				</ol>
     			               			</li>
     	               				</ol>
     			              	 </li>
     			               </ol>
     			               
     			          	</li>
     			          	
     		           </ol>
					</section>
					
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
					<section id="profile-comments-two">
						<h1>Flat Comments</h1>
						<ol class="comments outer"> 
					
     		               <li> 
     		               	<div class="comment">
     		               		<div class="comment-icon">
									<a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a> 
								</div>
								<div class="comment-content">
									<p><a class="comment-user-link" href="#">Flint Arthur</a> (1) Um... no... that's not what it means. It means that there is an amount of anti-matter/anti-energy that balances the Flint matter/energy; but by no means is that in any sort of coherent arrangement.<br /><br />

It's a very good thing that the amount of anti-matter/anti-energy in the universe balances with the amount of matter/energy. If it wasn't balanced and there was more positive energy/matter... SOMETHING would have had to put that there.</p> 
									<abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr>
									<nav> 
										<ul> 
											<li><a href="#"><span>Reply</span></a></li> 
											<li><a href="#"><span>Hide</span></a></li> 
											<li><a href="#"><span>Delete</span></a></li> 
										</ul> 
									</nav> 
								</div>
							</div>
     		               </li> 
     	               
     	               		<li>
     	               			<ol class="comments ">
     		               		<li> 
     		               			<div class="comment">
     		               				<div class="comment-icon">
											<a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a> 
										</div>
										<div class="comment-content">
											<p><a class="comment-user-link" href="#">Flint Arthur</a> (2) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p> 
											<abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr>
											<nav> 
												<ul> 
													<li><a href="#"><span>Reply</span></a></li> 
													<li><a href="#"><span>Hide</span></a></li> 
													<li><a href="#"><span>Delete</span></a></li> 
												</ul> 
											</nav> 
										</div>
									</div>
     			              	 </li> 
     			              	 <li>
     			              	 	<ol class="comments "> 
     	               					<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (3) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
     	               					<li> 
		     			              	 	<ol class="comments "> 
 		    	               					<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (4) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
     	               							<li> 
		     			              	 			<ol class="comments "> 
 		    	               							<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (5) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
		     	               							<li> 
				     			              	 			<ol class="comments "> 
 				    	               							<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (6) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
		     	               									<li> 
				     			              	 					<ol class="comments "> 
 				    	               									<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (7) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
		     	               											<li> 
				     			              	 							<ol class="comments "> 
 				    	               											<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (8) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
		     	               													<li> 
				     			              	 									<ol class="comments "> 
 				    	               													<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (9) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
				     	               													<li> 
						     			              	 									<ol class="comments "> 
 						    	               													<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (10) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
						     	               													<li> 
								     			              	 									<ol class="comments "> 
 								    	               													<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (11) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
								     	               													<li> 
										     			              	 									<ol class="comments "> 
 										    	               													<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (12) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
										     	               													<li> 
												     			              	 									<ol class="comments "> 
 												    	               													<li>  <div class="comment"> <div class="comment-icon"> <a href="#"><img src="http://photos-b.ak.fbcdn.net/photos-ak-ash1/v282/95/19/662764663/s662764663_1045718_3113.jpg" alt="Commenter Icon" /></a>  </div> <div class="comment-content"> <p><a class="comment-user-link" href="#">Flint Arthur</a> (13) Theremin lecture at baltimore node next wed, 8/25, 7:00 PM</p>  <abbr title="Thursday, August 26, 2010 at 4:19pm" data-date="Thu, 26 Aug 2010 14:19:57 -0700" class="timestamp">4 minutes ago</abbr> <nav>  <ul>  <li><a href="#"><span>Reply</span></a></li>  <li><a href="#"><span>Hide</span></a></li>  <li><a href="#"><span>Delete</span></a></li>  </ul>  </nav>  </div> </div> </li>  
 										    			               												</ol>
 										    			               											</li>
 								    			               												</ol>
 								    			               											</li>
 						    			               												</ol>
 						    			               											</li>
 				    			               												</ol>
 				    			               											</li>
 		    			               												</ol>
 		    			               											</li>
 		    			               										</ol>
 		    			               									</li>
 		    			               								</ol>
 		    			               							</li>
 		    			               						</ol>
 		    			               					</li>
     			               						</ol>
     			               					</li>
     			               				</ol>
     			               			</li>
     	               				</ol>
     			              	 </li>
     			               </ol>
     			               
     			          	</li>
     			          	
     		           </ol>
					</section>
					
				</div>
			</div>
		</div>
        
    </div>

	<div class="clear"></div>
    
    <footer id="appleseed-footer" class="container_16">
    
    	<p> <a href="http://opensource.appleseedproject.org">Appleseed Social Networking Software</a> is Copyright &copy; 2004-2011 by Michael Chisari under the GNU General Public License. All Rights Reserved. </p>
    	
    </footer>
    
	<div class="clear"></div>
	
</body>
</html>
