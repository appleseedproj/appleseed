jLoader.Initialize( "admin-system-update" );

jLoader.Admin_system_update = function ( ) { 

}

jLoader.Admin_system_update.Select = function ( ) { }

jLoader.Admin_system_update.Select.OnChange = function ( pElement, pParent ) {
	var form = $("form#update");

	$("form#update input[name='Task']").setValue ( "Refresh" );
	
	form.submit();
	
}

jLoader.Admin_system_update.Button = function ( pElement, pParent ) { }

jLoader.Admin_system_update.Button.OnClick = function ( pElement, pParent ) { 

	if ( pElement.value == 'Update' ) {
		var server = $("form#update select[name='Server']").getValue();
		var version = $("form#update select[name='Version']").getValue();
		
		if ( ( !server ) || ( !version ) ) {
			alert ( __("Add a valid update server to continue") );
			return ( false );
		}
		
		return ( confirm ( __( "Are you sure?  This will update your system to version %version$s from server %server$s.", { "version":version, "server": server } ) ) );
	}
	
	return ( true );
}
