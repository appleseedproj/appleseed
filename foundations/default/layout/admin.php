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
	<link rel="stylesheet" href="/themes/default/style/admin.css" /> 
	
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
	       		<nav id="admin-tabs" class="grid_9 push_4">
	       				<ul>
	       					<li><a href="#">Options</a></li>
							<li><a href="#">Tooltips</a></li>
							<li class="selected"><a href="#">Nodes</a></li>
							<li><a href="#">Logs</a></li>
							<li><a href="#">Maintenance</a></li>
		       			</ul>
		       	</nav>
		       	<div id="admin-search" class="grid_3 push_4">
					<input type="text" name="search" placeholder="Search..." class="search local"><input type="submit" name="search" value="" class="search-submit local">
				</div>
			</div>
       
			<div id="appleseed-admin-main" class="grid_16">
				<div id="appleseed-admin-main-menu" class="grid_4 alpha">
					<section id="admin-main-menu">
						<h1>Administration</h1>
						<ul>
							<li><a href="#" class="config"><span class="icon"></span>Config</a></li>
							<li><a href="#" class="users"><span class="icon"></span>Users</a></li>
							<li><a href="#" class="content"><span class="icon"></span>Content</a></li>
							<li class="selected" ><a class="system" href="#"><span class="icon"></span>System</a></li>
							<li><a href="#" class="control"><span class="icon"></span>Control</a></li>
						</ul>
					</section>
				</div>
				<div id="appleseed-admin-content" class="grid_12 omega">
  					<section class="admin-content">
  						<h1>Admin Interface</h1>
						<p id='admin-message'></p>
  						<form>
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
