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
	
	return ( true );
}