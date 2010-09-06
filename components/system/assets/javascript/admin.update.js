jLoader.Initialize( "admin-system-update" );

jLoader.Admin_system_update = function ( pElement ) { 

}

jLoader.Admin_system_update.Select = function ( ) { }

jLoader.Admin_system_update.Select.OnChange = function ( pElement, pParent ) {
	var form = $("form#update");
	
	$("form#update input[name='Task']").setValue ( "Refresh" );
	
	form.submit();
	
}

jLoader.Admin_system_update.Button = function ( pElement, pParent ) { }

jLoader.Admin_system_update.Button.OnClick = function ( pElement, pParent ) { 

	jTranslations[pElement.id] = "system";

	if ( pElement.value == 'Update' ) {
		var server = $("form#update select[name='Server']").getValue();
		var version = $("form#update select[name='Version']").getValue();
		
		if ( ( !server ) || ( !version ) ) {
			alert ( __("Add a valid update server to continue") );
			return ( false );
		}
		
		if ( confirm ( __( "Are You Sure", { "version":version, "server": server } ) ) ) {
			// Change the text and disable the button
			pElement.innerText = __( "Updating Please Wait" );
			
			$('#update').submit ( function ( ) {
    			$( '#update button' ).attr( 'disabled', 'disabled' );
				$( "form#update input[name='Task']" ).setValue ( "Update" );
			} );
			
			return ( true );
		} else {
			return ( false );
		}
		
	}
	
	return ( true );
}
