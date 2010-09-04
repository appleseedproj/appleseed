jLoader.Initialize( "admin-system-update" );

jLoader.Admin_system_update = function ( ) { 
}

jLoader.Admin_system_update.Select = function ( ) { }

jLoader.Admin_system_update.Select.OnChange = function ( pElement, pParent ) {
	var form = $("form#update");

	$("form#update input[name='Task']").setValue ( "Refresh" );
	
	form.submit();
	
}