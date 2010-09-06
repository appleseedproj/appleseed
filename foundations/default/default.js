jLoader.Initialize();

// ---[ ]---------------------------------------------------------------------------
jLoader.Document = function ( ) {

	jTranslations = new Array ();

	$("#form-elements").validate();
	
}

jLoader.Document.Anchor = function ( ) {
}

jLoader.Document.Anchor.OnClick = function ( pElement ) {
}


function __ ( pText, pData ) {

    var parentElement = arguments.callee.caller.arguments[0];
    
    var translation =  jTranslations[parentElement.id];
    
    var translationElementId = 'appleseed-language-components-' + translation;
    
    var translationElement = document.getElementById ( translationElementId );
    
    translationData = JSON.parse(translationElement.innerText);
    
	var text = sprintfn ( pText, pData );
	return ( text );
}

function sprintfn ( pText, pData ) {

	key = pText.toUpperCase();
	key = key.replace ( / /g, '_' );
	
	jskey = 'JS_' + key;
	
	text = translationData[key];
	
	if (typeof translationData[key] != 'undefined') {
		text = translationData[key];
	} else if ( typeof translationData[jskey] != 'undefined' ) {
		text = translationData[jskey];
	} else {
		return ( pText );
	}
	
	for ( key in pData ) {
		var pattern = new RegExp ( "\\%" + key + "\\$s", "g" );
		text = text.replace( pattern, pData[key]);
	}
	
	return ( text );
}
