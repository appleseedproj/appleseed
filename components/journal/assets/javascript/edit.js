jLoader.Initialize( "profile-journal-edit" );

jLoader.Profile_journal_edit = function ( ) { 
	
	$.getScript('/components/journal/assets/javascript/textile.js');
	
}

jLoader.Profile_journal_edit.Button = function ( ) { }

jLoader.Profile_journal_edit.Textarea = function ( ) { }

jLoader.Profile_journal_edit.Textarea.OnKeyUp = function ( pElement, pParent ) { 
	element = $( pElement );
	preview = $('#profile-journal-edit .preview')
	
	// Remove all html elements
	value = element.val().replace(/<\/?[^>]+>/gi, '').replace(/\n/gi, " <br /> ");
	
	// Translate Textile markup and set the preview
	preview.html ( superTextile ( value ) )
}

jLoader.Profile_journal_edit.Text = function ( ) { }

jLoader.Profile_journal_edit.Text.OnKeyUp = function ( pElement, pParent ) { 
	element = $( pElement );
	
	preview_title = $('#profile-journal-edit .preview-title')
	preview_url = $('#profile-journal-edit .preview-url')
	
	
	value = element.val().replace(/<\/?[^>]+>/gi, '');
	url_value = element.val().replace(/<\/?[^>]+>/gi, '').replace ( /\s/g, "-" ).toLowerCase();
	
	// Translate Textile markup and set the preview
	preview_title.text ( value );
	preview_url.text ( url_value );
}