<script type="text/javascript" src="/components/login/assets/javascript/login.js"></script>

<section id="login">

	<div id="login_tabs">

	<nav class="tabs"> 
		<ul> 
			<li id="login_local_button"><a href="#login_local">Local</a></li> 
			<li id="login_remote_button"><a href="#login_remote">Remote</a></li> 
		</ul> 
	</nav>

  <div id="login_local">
  <p id='local_login_message'></p>
  <form id='userinfo' name='userinfo' action='/login/' method='post'> 
	<input type='hidden' name='Task' value='login'> 
	<input type='hidden' name='Context' value=''> 
 
   <p id='username'>Username</p>
   <span class='username'><input  type='text' name='Username' class='username' maxlength='64' value="" /></span>   
   <p id='password'>Password</p>
   <span class='pass'><input  type='password' name='Pass' class='pass' maxlength='16' value="" /></span>   
   <p id='rememberme'>Remember Me</p>
   <input id='remember' type='checkbox' name='Remember' checked='checked' /> 
 
    <button   type='submit' name='Task' value='Login'>Login</button>    <button   type='submit' name='Task' value='FORGOT'>Forgot Password</button> 
  </form>
 
  <h1>Join This Site</h1> 
 
 
 <p class='content'> 
   Create a new account by filling out the information below.  Once the account is created, you can log in and set up your profile, and upload a profile photo and user icons. </p> <!-- .content --> 
 
<form name='join' id='join' action='/login/' method='post'> 
	<input type='hidden' name='Task' value='join'> 
	<input type='hidden' name='Context' value=''> 
 
	<p id='fullname'>Full Name</p>
	<input type='text' name='Fullname' value="" /> 
	<p id='username'>Username</p>
	<input  type='text' name='Username' value="" /> 
	<p id='email'>Email   </p>
	<input type='text' name='Email' value="" /> 
	<p id='password'>Password</p>
	<input  type='password' name='Pass' value="" /> 
	<p id='confirm'>Confirm Password</p>
	<input  type='password' name='Confirm' value="" /> 
	<button   type='submit' name='Task' value='Join'>Join</button> 
</form>
 
  </div>
 
		<div id="login_remote">

			<form id='userinfo' name='userinfo' action='/login/remote/' method='post'> 
				<input type='hidden' name='Task' value='remote'> 
				<input type='hidden' name='Context' value=''> 
				<input  type='text' name='Identity' value="" />
				<button type='submit' name='Task' value='Remote'>Remote Login</button> 
			</form>
  
		</div>
  
  </div>
</section>