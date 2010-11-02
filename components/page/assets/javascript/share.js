jLoader.Initialize( "page-share" );

jLoader.Page_share = function ( ) { 
}

jLoader.Page_share.Checkbox = function ( ) { }

jLoader.Page_share.Checkbox.OnClick = function ( pElement, pParent ) {
	
	element = $(pElement);
	parent = element.parent();
	
	if ( parent.hasClass ( 'everybody' ) ) {
		if (pElement.checked ) {
			$('#page-share .friends input').attr ( 'checked', false );
			$('#page-share .circle input').attr ( 'checked', false );
			$('#page-share .nobody input').attr ( 'checked', false );
		}
	} else if ( parent.hasClass ( 'friends' ) ) {
		if (pElement.checked ) {
			$('#page-share .everybody input').attr ( 'checked', false );
			$('#page-share .circle input').attr ( 'checked', false );
			$('#page-share .nobody input').attr ( 'checked', false );
		}
	} else if ( parent.hasClass ( 'circle' ) ) {
		if (pElement.checked ) {
			$('#page-share .everybody input').attr ( 'checked', false );
			$('#page-share .friends input').attr ( 'checked', false );
			$('#page-share .nobody input').attr ( 'checked', false );
		}
	} else if ( parent.hasClass ( 'nobody' ) ) {
		if (pElement.checked ) {
			$('#page-share .everybody input').attr ( 'checked', false );
			$('#page-share .friends input').attr ( 'checked', false );
			$('#page-share .circle input').attr ( 'checked', false );
		}
	}
	
	elements = $("#page-share .privacy-list .item" );
	
	checked = false;
	for ( e = 0; e < elements.length; e++ ) {
		if ( $(':checkbox', elements[e] ).attr('checked' ) ) checked = true;
	}
	
	if ( !checked ) {
		$('#page-share .nobody input').attr ( 'checked', true );
	}
	
	return ( true );
}