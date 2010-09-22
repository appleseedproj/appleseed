<h1>Remote Login</h1>
<p id='remote-login-message'></p>

<form id='userinfo' name='userinfo' action='/login/remote/' method='post'> 
	<fieldset>
		<input type='hidden' name='Task' value='remote'> 
		<input type='hidden' name='Context' value=''> 
		<input type='hidden' name='Redirect' value=''> 
		<div><label for="Identity">Identity</label><input type="text" name="Identity" /></div>
	</fieldset>
	<button type='submit' name='Task' value='Remote'>Remote Login</button> 
</form>
