jLoader.Initialize( "system-nodes-edit" );

jLoader.System_nodes_edit = function ( pElement ) { 

	// Add form validation to the edit form.
	$("#system-nodes-edit").validate();
	
	$("input[name='EndStamp']").datepicker({dateFormat: 'yy-mm-dd 00:00:00'});
	
	if ( $("input[name='Never']")[0].checked ) 
		endstamp =  $("input[name='EndStamp']")[0].disabled = true;
	
}

jLoader.System_nodes_edit.Checkbox = function ( pElement ) { }
jLoader.System_nodes_edit.Checkbox.OnClick = function ( pElement ) { 

	if ( $("input[name='Never']")[0].checked ) 
		endstamp =  $("input[name='EndStamp']")[0].disabled = true;
	else
		endstamp =  $("input[name='EndStamp']")[0].disabled = false;
	
}

jLoader.System_nodes_edit.Button = function ( pElement ) { }
jLoader.System_nodes_edit.Button.OnClick = function ( pElement ) { 

	jTranslations[pElement.id] = "system";
	
	if ( pElement.value == "Cancel" ) {
		return ( confirm ( __( "Are You Sure You Want To Cancel" ) ) );
	}
	
	return ( true );
}

jLoader.Initialize( "system-nodes-list" );

jLoader.System_nodes_list = function ( pElement ) { }
jLoader.System_nodes_list.Checkbox = function ( pElement ) {  }

jLoader.System_nodes_list.Checkbox.OnClick = function ( pElement ) { 


	$('#system-nodes-message').text ( "" );
	$('#system-nodes-message').removeClass ( 'error message' );

	var classNames = pElement.className.split ( ' ' ); 
	
	if ( jQuery.inArray ( 'select-toggle', classNames ) > -1 ) {
		var masslist = $('#system-nodes-list .masslist-checkbox');
		
		for ( m in masslist ) {
			masslist[m].checked = pElement.checked;
		}
		
	}
	
	return ( true );
}

jLoader.System_nodes_list.Anchor = function ( pElement ) {  }

jLoader.System_nodes_list.Anchor.OnClick = function ( pElement ) { 

	$('#system-nodes-message').text ( "" );
	$('#system-nodes-message').removeClass ( 'error message' );
	
	var classNames = pElement.className.split ( ' ' ); 
	var masslist = $('#system-nodes-list .masslist-checkbox');
	
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

jLoader.System_nodes_list.Button = function ( pElement ) {  }

jLoader.System_nodes_list.Button.OnClick = function ( pElement ) { 

	$('#system-nodes-message').removeClass ( 'error message' );
	$('#system-nodes-message').text ( "" );

	jTranslations[pElement.id] = "system";

	var classNames = pElement.className.split ( ' ' ); 
	
	if ( jQuery.inArray ( 'delete-all', classNames ) > -1 ) {
	
		var masslist = $('#system-nodes-list .masslist-checkbox');
		
		var count = 0;
		
		for ( m = 0; m < masslist.length; m++ ) {
			if ( masslist[m].checked == true ) count++;
		}
		
		if ( count == 0 ) {
			$('#system-nodes-message').text ( __( "None Selected" ) );
			$('#system-nodes-message').addClass ( 'error' );
			return ( false );
		}
		
		return ( confirm ( __( "Are You Sure You Want To Delete", { "count":count } ) ) );
	}
	
	return ( true );
}