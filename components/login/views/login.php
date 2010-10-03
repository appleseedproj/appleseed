<section id="login-local">
	<h1>Login</h1> 
	<p id='local-login-message'></p>
	<form id='userinfo' name='userinfo' action='/login/' method='post'> 
	<fieldset>
		<input type='hidden' name='Task' value='login'> 
		<input type='hidden' name='Context' value=''> 
		<input type='hidden' name='Redirect' value=''> 

		<div><label for='Username'>Username</label><input type='Text' name='Username' class='username' maxlength='64' value="" /></div>
		<div><label for='Password'>Password</label><input type='Password' name='Pass' class='pass' maxlength='16' value="" /></div>
		<div><label for='Remember'>Remember Me</label><input id='Remember' type='checkbox' name='Remember' /></div>
		</fieldset>

		<button name='Task' value='Login'>Login</button>    
		<button name='Task' value='Forgot'>Forgot Password</button> 
	</form>
</section>
