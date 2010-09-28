jLoader.Initialize( "profile-friends" );

jLoader.Profile_friends = function ( ) { 

  $("#profile-friends form button").hide();
  
}

jLoader.Profile_friends.Anchor = function ( ) { }

jLoader.Profile_friends.Anchor.OnClick = function ( pElement, pParent ) {
}
		
jLoader.Profile_friends.Form = function ( ) { }

jLoader.Profile_friends.Form.OnKeyPress = function ( pElement, pParent ) {
}

jLoader.Profile_friends.Select = function ( ) { }

jLoader.Profile_friends.Select.OnChange = function ( pElement, pParent ) { 
	$(pElement).parents("form").submit()
	
	return ( true );
}

