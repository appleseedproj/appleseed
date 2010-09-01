<script type="text/javascript" src="/components/login/assets/javascript/login.js"></script>

<section id="login-local">
  	<h1>Login</h1> 
	<p id='local-login-message'></p>
	<form id='userinfo' name='userinfo' action='/login/' method='post'> 
		<fieldset>
		<input type='hidden' name='Task' value='login'> 
		<input type='hidden' name='Context' value=''> 
 
		<div><label for='username'>Username</label><input type='text' name='Username' class='username' maxlength='64' value="" /></div>
		<div><label for='password'>Password</label><input type='password' name='Pass' class='pass' maxlength='16' value="" /></div>
		<div><label for='rememberme'>Remember Me</label><input id='remember' type='checkbox' name='Remember' /></div>
		</fieldset>
 
		<button type='submit' name='Task' value='Login'>Login</button>    
		<button type='submit' name='Task' value='Forgot'>Forgot Password</button> 
	</form>
 
</section>
