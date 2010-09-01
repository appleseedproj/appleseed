jLoader.Initialize( "appleseed-login" );

jLoader.Appleseed_login = function ( ) { 

	// Add tabs to debug section
    $("#appleseed-login").tabs();
	
}

jLoader.Initialize( "login-join" );

jLoader.Login_join = function ( ) { 

	// Add form validation to the join form.
	$("#join").validate();
	
}
