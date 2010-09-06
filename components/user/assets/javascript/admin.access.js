jLoader.Initialize( "user-access-edit" );

jLoader.User_access_edit = function ( pElement ) { 

	// Add form validation to the edit form.
	$("#user-access-edit").validate();
	
}

jLoader.Initialize( "user-access-list" );

jLoader.User_access_list = function ( pElement ) {  }
jLoader.User_access_list.Checkbox = function ( pElement ) {  }

jLoader.User_access_list.Checkbox.OnClick = function ( pElement ) { 

	$('#user-access-message').text ( "" );
	$('#user-access-message').removeClass ( 'error message' );

	var classNames = pElement.className.split ( ' ' ); 
	
	if ( jQuery.inArray ( 'select-toggle', classNames ) > -1 ) {
		var masslist = $('#user-access-list .masslist-checkbox');
		
		for ( m in masslist ) {
			masslist[m].checked = pElement.checked;
		}
		
	}
	
	return ( true );
}

jLoader.User_access_list.Anchor = function ( pElement ) {  }

jLoader.User_access_list.Anchor.OnClick = function ( pElement ) { 

	$('#user-access-message').text ( "" );
	$('#user-access-message').removeClass ( 'error message' );

	var classNames = pElement.className.split ( ' ' ); 
	var masslist = $('#user-access-list .masslist-checkbox');
	
	if ( jQuery.inArray ( 'select-all', classNames ) > -1 ) {
		for ( m in masslist ) {
			masslist[m].checked = true;
		}
	} else if ( jQuery.inArray ( 'select-none', classNames ) > -1 ) {
		for ( m in masslist ) {
			masslist[m].checked = false;
		}
	}
	
	return ( true );
}

jLoader.User_access_list.Button = function ( pElement ) {  }

jLoader.User_access_list.Button.OnClick = function ( pElement ) { 

	$('#user-access-message').removeClass ( 'error message' );
	$('#user-access-message').text ( "" );

	jTranslations[pElement.id] = "user";

	var classNames = pElement.className.split ( ' ' ); 
	
	if ( jQuery.inArray ( 'delete-all', classNames ) > -1 ) {
	
		var masslist = $('#user-access-list .masslist-checkbox');
		
		var count = 0;
		
		for ( m = 0; m < masslist.length; m++ ) {
			if ( masslist[m].checked == true ) count++;
		}
		
		if ( count == 0 ) {
			$('#user-access-message').text ( __( "None Selected" ) );
			$('#user-access-message').addClass ( 'error' );
			return ( false );
		}
		
		return ( confirm ( __( "Are You Sure You Want To Delete", { "count":count } ) ) );
	}
	
	return ( true );
}