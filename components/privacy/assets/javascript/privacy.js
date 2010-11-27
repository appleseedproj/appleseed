jLoader.Initialize( "appleseed" );

jLoader.Appleseed = function ( ) { }

jLoader.Appleseed.Checkbox = function ( ) { }

jLoader.Appleseed.Checkbox.OnClick = function ( pElement, pParent ) {
	
	element = $(pElement);
	parent = element.parent();
	container = parent.parent().parent();
	
	if ( !container.hasClass ( 'privacy' ) ) return ( false );
	
	if ( parent.hasClass ( 'everybody' ) ) {
		if ( !pElement.checked ) pElement.checked = true;
	
		if (pElement.checked ) {
			container.find ( '.friends input' ).attr ( 'checked', false );
			container.find ( '.circle input' ).attr ( 'checked', false );
			container.find ( '.nobody input' ).attr ( 'checked', false );
		}
	} else if ( parent.hasClass ( 'friends' ) ) {
		if ( !pElement.checked ) pElement.checked = true;
	
		if (pElement.checked ) {
			container.find ( '.everybody input').attr ( 'checked', false );
			container.find ( '.circle input').attr ( 'checked', false );
			container.find ( '.nobody input').attr ( 'checked', false );
		}
	} else if ( parent.hasClass ( 'circle' ) ) {
		if (pElement.checked ) {
			container.find ( '.everybody input').attr ( 'checked', false );
			container.find ( '.friends input').attr ( 'checked', false );
			container.find ( '.nobody input').attr ( 'checked', false );
		}
	} else if ( parent.hasClass ( 'nobody' ) ) {
		if ( !pElement.checked ) pElement.checked = true;
	
		if (pElement.checked ) {
			container.find ( '.everybody input').attr ( 'checked', false );
			container.find ( '.friends input').attr ( 'checked', false );
			container.find ( '.circle input').attr ( 'checked', false );
		}
	}
	
	elements = container.find ( ".privacy-list .item" );
	
	checked = false;
	for ( e = 0; e < elements.length; e++ ) {
		if ( $(':checkbox', elements[e] ).attr('checked' ) ) checked = true;
	}
	
	if ( !checked ) {
		container.find ( '.nobody input').attr ( 'checked', true );
	}
	
	return ( true );
} 
