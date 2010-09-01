<script type="text/javascript" src="/components/login/assets/javascript/login.js"></script>

<section id="login-local">
  	<h1>Login</h1> 
	<p id='local-login-message'>Here</p>
	<form id='userinfo' name='userinfo' action='/login/' method='post'> 
		<fieldset>
		<input type='hidden' name='Task' value='login'> 
		<input type='hidden' name='Context' value=''> 
 
		<div><label for='Username'>Username</label><input type='Text' name='Username' class='username' maxlength='64' value="" /></div>
		<div><label for='Password'>Password</label><input type='Password' name='Pass' class='pass' maxlength='16' value="" /></div>
		<div><label for='Remember'>Remember Me</label><input id='Remember' type='checkbox' name='Remember' /></div>
		</fieldset>
 
		<button type='submit' name='Task' value='Login'>Login</button>    
		<button type='submit' name='Task' value='Forgot'>Forgot Password</button> 
	</form>
 
</section>
 
<section id="login-remote">
	<h1>Remote Login</h1>
	<p id='remote-login-message'></p>

	<form id='userinfo' name='userinfo' action='/login/' method='post'> 
		<fieldset>
			<input type='hidden' name='Task' value='remote'> 
			<input type='hidden' name='Context' value=''> 
			<div><label for="Identity">Identity</label><input type="text" name="Identity" /></div>
		</fieldset>
		<button type='submit' name='Task' value='Remote'>Remote Login</button> 
	</form>
</section>

<section id="login-join">
	<h1>Join This Site</h1> 
	<p id='join-login-message'></p>

	<p class='content'>Create a new account by filling out the information below.  Once the account is created, you can log in and set up your profile, and upload a profile photo and user icons. </p>
 
	<form name='join' id='join' action='/login/' method='post'> 
		<fieldset>
			<input type='hidden' name='Task' value='join'> 
			<input type='hidden' name='Context' value=''> 
	 
			<div><label for='Fullname'>Full Name</label><input type='text' name='Fullname' class="required" value="" /></div>
			<div><label for='Username'>Username</label><input  type='text' name='Username' class="required" minlength="8" value="" /></div>
			<div><label for='Email'>Email   </label><input type='text' name='Email' class="required email" value="" /></div>
			<div><label for='Password'>Password</label><input  type='password' name='Pass' class="required" minlength="6" value="" /></div>
			<div><label for='Confirm'>Confirm Password</label><input  type='password' name='Confirm' class="required" minlength="6" value="" /></div>
			<div id="invite-code-requirement"><label for='Invite'>Invite Code</label><input type='text' name='Invite' class="required" value="" /></div>
		</fieldset>
		<button   type='submit' name='Task' value='Join'>Join</button> 
	</form>
 
</section>