<script type="text/javascript" src="/components/login/assets/javascript/login.js"></script>

<section id="login-local">
  	<h1>Login</h1> 
	<p id='local_login_message'></p>
	<form id='userinfo' name='userinfo' action='/login/' method='post'> 
		<fieldset>
		<input type='hidden' name='Task' value='login'> 
		<input type='hidden' name='Context' value=''> 
 
		<div><label for='username'>Username</label><input type='text' name='Username' class='username' maxlength='64' value="" /></div>
		<div><label for='password'>Password</label><input type='password' name='Pass' class='pass' maxlength='16' value="" /></div>
		<div><label for='rememberme'>Remember Me</label><input id='remember' type='checkbox' name='Remember' checked='checked' /></div>
		</fieldset>
 
		<button type='submit' name='Task' value='Login'>Login</button>    
		<button type='submit' name='Task' value='FORGOT'>Forgot Password</button> 
	</form>
 
</section>
 
<section id="login-remote">
	<h1>Remote Login</h1>
	<p id='remote_login_message'></p>

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
	<p class='content'>Create a new account by filling out the information below.  Once the account is created, you can log in and set up your profile, and upload a profile photo and user icons. </p>
 
	<form name='join' id='join' action='/login/' method='post'> 
		<fieldset>
			<input type='hidden' name='Task' value='join'> 
			<input type='hidden' name='Context' value=''> 
	 
			<div><label for='fullname'>Full Name</label><input type='text' name='Fullname' value="" /></div>
			<div><label for='username'>Username</label><input  type='text' name='Username' value="" /></div>
			<div><label for='email'>Email   </label><input type='text' name='Email' value="" /></div>
			<div><label for='password'>Password</label><input  type='password' name='Pass' value="" /></div>
			<div><label for='confirm'>Confirm Password</label><input  type='password' name='Confirm' value="" /></div>
		</fieldset>
		<button   type='submit' name='Task' value='Join'>Join</button> 
	</form>
 
</section>