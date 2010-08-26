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
	<link rel="stylesheet" href="/foundations/default/layout/admin.css" /> 
	
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
	
    	<div id="appleseed-admin" class="container_16">
	       	<div id="appleseed-admin-menu" class="container_16">
	       		<div id="admin-tabs" class="grid_9 push_4">
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
		       	<div id="admin-search" class="grid_3 push_4">
					<input type="text" name="search" placeholder="Search..." class="search"><input type="submit" name="search" value="" class="search-submit">
				</div>
			</div>
       
			<div id="appleseed-admin-main" class="grid_16">
				<div id="appleseed-admin-main-menu" class="grid_4 alpha">
					<section id="admin-main-menu">
						<h1>Administration</h1>
						<ul>
							<li><a href="#"><span class="config-icon"></span><span class="admin-menu-link">Config</span></a></li>
							<li><a href="#"><span class="users-icon"></span><span class="admin-menu-link">Users</span></a></li>
							<li><a href="#"><span class="content-icon"></span><span class="admin-menu-link">Content</span></a></li>
							<li class="selected"><a href="#"><span class="system-icon"></span><span class="admin-menu-link">System</span></a></li>
							<li><a href="#"><span class="control-icon"></span><span class="admin-menu-link">Control</span></a></li>
						</ul>
					</section>
				</div>
				<div id="appleseed-admin-content" class="grid_12 omega">
  					<section class="admin-content">
  						<h1>Admin Interface</h1>
						<nav class="controls"> 
							<ul> 
								<li><a href="#">Move Up &uarr;</a></li> 
								<li><a href="#">Move Down &darr;</a></li> 
								<li><a href="#">Delete</a></li> 
							</ul> 
                		</nav> 
                		<nav class="pagination-amount">
                			<select>
                				<option value="5">5</option>
                			</select>
                		</nav>	
                		<nav class="select">
                			Select: <a href="#">All</a>, <a href="#">None</a>
                		</nav>
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
						<table id="mytable" cellspacing="0" summary="The technical specifications of the Apple PowerMac G5 series"> 
							<tr> 
								<th scope="col" abbr="Configurations" class="nobg"><a href="#">#</a></th> 
								<th scope="col" abbr="Dual 1.8"><a href="#">Dual 1.8GHz</a></th> 
								<th scope="col" abbr="Dual 2"><a href="#">Dual 2GHz</a></th> 
								<th scope="col" abbr="Dual 2.5"><a href="#">Dual 2.5GHz</a></th> 
   	                        	<th><input type="checkbox" /></th> 
							</tr> 
							<tr> 
								<th scope="row" abbr="Model" class="spec">1</th> 
								<td>M9454LL/A</td> 
								<td>M9455LL/A</td> 
								<td>M9457LL/A</td> 
     	                       	<td><input type="checkbox" /></td> 
							</tr> 
							<tr class="alt"> 
								<th scope="row" abbr="G5 Processor" class="spec">2</th> 
								<td >Dual 1.8GHz PowerPC G5</td> 
								<td>Dual 2GHz PowerPC G5</td> 
								<td>Dual 2.5GHz PowerPC G5</td> 
								<td><input type="checkbox" /></td> 
							</tr> 
							<tr> 
								<th scope="row" abbr="Frontside bus" class="spec">3</th> 
								<td>900MHz per processor</td> 
								<td>1GHz per processor</td> 
								<td>1.25GHz per processor</td> 
     	                       <td><input type="checkbox" /></td> 
							</tr> 
							<tr class="alt"> 
								<th scope="row" abbr="L2 Cache" class="spec">4</th> 
								<td>512K per processor</td> 
								<td>512K per processor</td> 
								<td>512K per processor</td> 
     	 						<td><input type="checkbox" /></td> 
							</tr> 
							<tr> 
								<th scope="row" abbr="L2 Cache" class="spec">5</th> 
								<td>512K per processor</td> 
								<td>512K per processor</td> 
								<td>512K per processor</td> 
     	 						<td><input type="checkbox" /></td> 
							</tr> 
							<tr class="alt"> 
								<th scope="row" abbr="L2 Cache" class="spec">6</th> 
								<td>512K per processor</td> 
								<td>512K per processor</td> 
								<td>512K per processor</td> 
     	 						<td><input type="checkbox" /></td> 
							</tr> 
							<tr> 
								<th scope="row" abbr="L2 Cache" class="spec">7</th> 
								<td>512K per processor</td> 
								<td>512K per processor</td> 
								<td>512K per processor</td> 
     	 						<td><input type="checkbox" /></td> 
							</tr> 
							<tr class="alt"> 
								<th scope="row" abbr="L2 Cache" class="spec">8</th> 
								<td>512K per processor</td> 
								<td>512K per processor</td> 
								<td>512K per processor</td> 
     	 						<td><input type="checkbox" /></td> 
							</tr> 
							<tr> 
								<th scope="row" abbr="Model" class="spec">9</th> 
								<td>M9454LL/A</td> 
								<td>M9455LL/A</td> 
								<td>M9457LL/A</td> 
     	                       	<td><input type="checkbox" /></td> 
							</tr> 
							<tr class="alt"> 
								<th scope="row" abbr="G5 Processor" class="spec">10</th> 
								<td >Dual 1.8GHz PowerPC G5</td> 
								<td>Dual 2GHz PowerPC G5</td> 
								<td>Dual 2.5GHz PowerPC G5</td> 
								<td><input type="checkbox" /></td> 
							</tr> 
						</table>
                		<nav class="select">
                			Select: <a href="#">All</a>, <a href="#">None</a>
                		</nav>
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
						<nav class="controls"> 
							<ul> 
								<li><a href="#">Move Up &uarr;</a></li> 
								<li><a href="#">Move Down &darr;</a></li> 
								<li><a href="#">Delete</a></li> 
							</ul> 
                		</nav> 
                		<nav class="pagination-amount">
                			<select>
                				<option value="5">5</option>
                			</select>
                		</nav>	
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
