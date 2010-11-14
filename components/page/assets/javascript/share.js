jLoader.Initialize( "page-share" );

jLoader.Page_share = function ( ) { 
	// Add tabs
    $("#page-share").tabs();
    
    // Disable Enter key
    $("#page-share form").keypress(function(e) {
      if (e.which == 13) {
        return false;
      }
    });
    
    // Capture a paste event, and load
    $("#page-share .link").bind('paste', function(e) {
    	pElement = $("#page-share .link").get(0);
    	currentLink = undefined;
    	setTimeout(function() {
    		jLoader.Page_share.Text.OnBlur( pElement );
    		currentLink = $(pElement).val();
        }, 0);
    });
    
}

jLoader.Page_share.Button = function ( ) { }

jLoader.Page_share.Button.OnClick = function ( pElement, pParent ) { 
	
	element = $(pElement);
	parent = element.parent();
	
	if ( parent.hasClass ( 'thumbs-scroll' ) ) {
    	var current = $('#page-share .attach .selected');
		if ( element.hasClass ( 'scroll-next' ) ) {
	    	if ( current.next().parent().html() ) {
	    		$('#page-share .attach .selected').next().addClass ( 'selected' );
	    		$('#page-share .attach .selected').first().removeClass ( 'selected' );
	    	}
		} else if ( element.hasClass ( 'scroll-previous' ) ) {
	    	if ( current.prev().parent().html() ) {
	    		var current = $('#page-share .attach .selected');
	    		$('#page-share .attach .selected').prev().addClass ( 'selected' );
	    		current.removeClass ( 'selected' );
	    	}
		}
		
	   	var current = $('#page-share .attach .selected');
		
		$('#page-share input[name=LinkThumb]').val ( current.attr ('src') );
	}
	
	return ( false );
}

jLoader.Page_share.Textarea = function ( ) { }
jLoader.Page_share.Textarea.CheckForLink = function ( pElement, pParent ) { 
	
	element = $(pElement);
	
	if ( element.val().toString().search( new RegExp( /^http:\/\//i ) ) == 0 ) {
		$('#page-share').tabs('select', 1);
		$('#page-share .link').val ( element.val() );
		element.val("");
		$('#page-share .link').blur();
		$('#page-share .link-content').focus();
	}
	
}
jLoader.Page_share.Textarea.OnKeyUp = function ( pElement, pParent ) { 
	
	jLoader.Page_share.Textarea.CheckForLink ( pElement, pParent );
}
jLoader.Page_share.Textarea.OnKeyDown = function ( pElement, pParent ) { 
	
	element = $(pElement);
	
	if ( element.hasClass ( 'description' ) ) {
		element.keypress(function(e) {
  	  		if ( (e.which == 13) ) {
    			element.blur();
    		} else {
    			return ( true );
    		}
    	});
	}

	jLoader.Page_share.Textarea.CheckForLink ( pElement, pParent );
}
jLoader.Page_share.Textarea.OnChange = function ( pElement, pParent ) { 
	jLoader.Page_share.Textarea.CheckForLink ( pElement, pParent );
}
jLoader.Page_share.Textarea.OnFocus = function ( pElement, pParent ) { 
	jLoader.Page_share.Textarea.CheckForLink ( pElement, pParent );
}
jLoader.Page_share.Textarea.OnBlur = function ( pElement, pParent ) { 
	jLoader.Page_share.Textarea.CheckForLink ( pElement, pParent );
}
	
jLoader.Page_share.Text = function ( ) { }

jLoader.Page_share.Text.OnFocus = function ( pElement, pParent ) { 
	
	element = $(pElement);
	
	currentLink = element.val();
	
	return ( true );
}

jLoader.Page_share.Text.OnKeyDown = function ( pElement, pParent ) { 
	element = $(pElement);
	
    element.keypress(function(e) {
    	if (e.which == 13) {
    		element.blur();
    	}
    });
}

jLoader.Page_share.Text.OnBlur = function ( pElement, pParent ) {
	
	element = $(pElement);
	
	if ( typeof currentLink != "undefined" ) {
		if ( currentLink == element.val() ) {
			return ( false );
		}
	}
	
	if ( element.hasClass ( 'link' ) ) {
	    $('#page-share .attach').hide();
	    $('#page-share .loading').show();
	    
	    if ( element.val() == "" ) {
	    	$('#page-share .loading').hide();
	    	return ( false );
	    }
	    
	    var jsonUrl = "/api/page/scrape?url=" + element.val();
	    $.getJSON(jsonUrl, function(data) {
	    	
	    	if ( ( !data.title ) && ( !data.description ) ) {
	    		$('#page-share .loading').hide();
	    		return ( false );
	    	}
	    	
	    	// Necessary for converting html entities on input elements.
	    	var title = $('<div />').html(data.title).text();
	    	$('#page-share .attach .title').val ( title );
	    	
	    	$('#page-share .attach .description').html ( data.description );
	    	
	    	if ( typeof item == 'undefined' ) item = $('#page-share .attach .thumb').clone();
    		$('#page-share .attach .thumbs').empty();
	    	
    		if (typeof data.images != 'undefined' ) {
    			$.each( data.images, function(key, value){
	    			item.children().attr('src', value );
	    			$('#page-share .attach .thumbs').append(item.html());
	    		});
	    	
	    		$('#page-share .attach .thumbnail').first().addClass ( 'selected' );
    			$('#page-share .thumbs-scroll').show();
    		} else {
    			$('#page-share .thumbs-scroll').hide();
    		}
	    	
	    	$('#page-share .loading').hide();
	    	$('#page-share .attach').show();
	    });
	}
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