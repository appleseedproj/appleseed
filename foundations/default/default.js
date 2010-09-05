jLoader.Initialize();

// ---[ ]---------------------------------------------------------------------------
jLoader.Document = function ( ) {

	$("#form-elements").validate();
	
}

jLoader.Document.Anchor = function ( ) {
}

jLoader.Document.Anchor.OnClick = function ( pElement ) {
}


function __ ( pText, pData ) {

	var text = sprintfn ( pText, pData );
	return ( text );
}

function sprintfn ( pText, pData ) {

	for ( key in pData ) {
		var pattern = new RegExp ( "\\%" + key + "\\$s", "g" );
		pText = pText.replace( pattern, pData[key]);
	}
	
	return ( pText );
}