// Appleseed::JLoader
// Automatically injects functionality into the DOM using an object-oriented approach
// Version 0.2
// Copyright (c) 2007-2010 Michael Chisari, michael.chisari@gmail.com
// Available under the GNU General Public License

// JLoader class

_ElementList = new Array;

_JLoader = function () {
}

_JLoader.prototype.Initialize = function ( pTarget ) {

	if (!pTarget) pTarget = "document";
	
	_ElementList.push ( pTarget );
	
	return ( true );
}

_JLoader.prototype.Load = function ( ) {

	for ( e = 0; e < _ElementList.length; e++ ) {
	
		if ( _ElementList[e] == "document" ) {
			var _Element = document;
		} else {
			var _Element = document.getElementById ( _ElementList[e] );
		}
		
		_ElementList[e] = _ElementList[e].replace ( "-", "_" );
		
		// Constructor
		Class = _ElementList[e].UCWords ();
		if ( typeof window['jLoader'][Class] == "function" ) {
			window['jLoader'][Class] ();
		}
		
		// @todo: Find a way to trigger a warning.
		if ( _Element == null ) continue;
		
		var elements = _Element.getElementsByTagName ( "*" );
		
		var target = _ElementList[e] ;
		for ( e2 = 0; e2 < elements.length; e2++ ) {
			jLoader.AttachEvents ( target, elements[e2] );
		} 
	
	}

	return (true);
}

jLoader = new _JLoader ();

// Initialization
var _jloader_initialized = false;

/* for Mozilla/Opera9 */
if (document.addEventListener) {
    document.addEventListener("DOMContentLoaded", jLoader.Load, false);
    _jloader_initialized = true;
} // if

/* for Internet Explorer */
/*@cc_on @*/
/**@if (@_win32)
	document.write("<script id=__ie_onload defer src=javascript:void(0)><\/script>");
	var script = document.getElementById("__ie_onload");
	script.onreadystatechange = function() {
		if (this.readyState == "complete") {
			if (!_jloader_initialized) jLoader.Load(); // call the onload handler
		    _jloader_initialized = true;
		}
	};
/*@end @*/

/* for Safari */
if (/WebKit/i.test(navigator.userAgent)) { // sniff
	var _timer = setInterval(function() {
		if (/loaded|complete/.test(document.readyState)) {
			if (!_jloader_initialized) jLoader.Load(); // call the onload handler
			_jloader_initialized = true;
		}
	}, 10);
} // if

/* for other browsers */
if (!_jloader_initialized) window.onload = jLoader.Load();

_JLoader.prototype.AttachEvents = function ( pTarget, pElement ) {

	element = pElement;
	target = pTarget.UCWords();
	
	var attach = null;
	
	switch (element.tagName) {
		case 'A':
			events = new Array ("onfocus", "onblur", "onclick", "ondblclick", "onmousedown", "onmouseup", "onmouseover", "onmousemove", "onmouseout", "onkeypress", "onkeydown", "onkeyup");
			attach = window['jLoader'][target]['Anchor'];
		break;
		case 'BUTTON':
			events = new Array ("tabindex", "accesskey", "onfocus", "onblur", "onselect", "onchange", "onclick", "ondblclick", "onmousedown", "onmouseup", "onmouseover", "onmousemove", "onmouseout", "onkeypress", "onkeydown", "onkeyup");
			attach = window['jLoader'][target]['Button'];
		break;
		case 'INPUT':
			events = new Array ("tabindex", "accesskey", "onfocus", "onblur", "onselect", "onchange", "onclick", "ondblclick", "onmousedown", "onmouseup", "onmouseover", "onmousemove", "onmouseout", "onkeypress", "onkeydown", "onkeyup");
			switch (element.type) {
				case 'button':
					attach = window['jLoader'][target]['Button'];
				break;
				case 'checkbox':
					attach = window['jLoader'][target]['Checkbox'];
				break;
				case 'file':
					attach = window['jLoader'][target]['File'];
				break;
				case 'hidden':
					attach = window['jLoader'][target]['Hidden'];
				break;
				case 'image':
					attach = window['jLoader'][target]['Image'];
				break;
				case 'password':
					attach = window['jLoader'][target]['Password'];
				break;
				case 'radio':
					attach = window['jLoader'][target]['Radio'];
				break;
				case 'reset':
					attach = window['jLoader'][target]['Reset'];
				break;
				case 'submit':
					attach = window['jLoader'][target]['Submit'];
				break;
				case 'text':
					attach = window['jLoader'][target]['Text'];
				break;
			} // switch
		break;
		case 'LI':
			events = new Array ("onclick", "ondblclick", "onmousedown", "onmouseup", "onmouseover", "onmousemove", "onmouseout", "onkeypress", "onkeydown", "onkeyup"); 
			attach = window['jLoader'][target]['List'];
		break;
		case 'DIV':
			events = new Array ("onclick", "ondblclick", "onmousedown", "onmouseup", "onmouseover", "onmousemove", "onmouseout", "onkeypress", "onkeydown", "onkeyup"); 
			attach = window['jLoader'][target]['Div'];
		break;
		case 'SPAN':
			events = new Array ("onclick", "ondblclick", "onmousedown", "onmouseup", "onmouseover", "onmousemove", "onmouseout", "onkeypress", "onkeydown", "onkeyup");
			attach = window['jLoader'][target]['Span'];
		break;
		case 'P':
			events = new Array ("onclick", "ondblclick", "onmousedown", "onmouseup", "onmouseover", "onmousemove", "onmouseout", "onkeypress", "onkeydown", "onkeyup");
			attach = window['jLoader'][target]['Paragraph'];
		break;
		case 'FORM':
			events = new Array ("onsubmit", "onreset", "onclick", "ondblclick", "onmousedown", "onmouseup", "onmouseover", "onmousemove", "onmouseout", "onkeypress", "onkeydown", "onkeyup");
			attach = window['jLoader'][target]['Form'];
		break;
		case 'SELECT':
			events = new Array ("onfocus", "onblur", "onchange");
			attach = window['jLoader'][target]['Select'];
		break;
		case 'TEXTAREA':
			events = new Array ("onfocus", "onblur", "onselect", "onchange", "onclick", "ondblclick", "onmousedown", "onmouseup", "onmouseover", "onmousemove", "onmouseout", "onkeypress", "onkeydown", "onkeyup");
			attach = window['jLoader'][target]['Textarea'];
		break;
		case 'BODY':
			events = new Array ("onload", "onunload", "onclick", "ondblclick", "onmousedown", "onmouseup", "onmouseover", "onmousemove", "onmouseout", "onkeypress", "onkeydown", "onkeyup");
			attach = window['jLoader'][target]['Body'];
		break;
		default:
			attach = null;
		break;
	} // switch
	
	if (!attach) return (false);
	
	/* START */
	
	jLoader.AttachAction ( "onclick", attach.OnClick, events, element );
	jLoader.AttachAction ( "ondblclick", attach.OnDblClick, events, element );
	
	jLoader.AttachAction ( "onchange", attach.OnChange, events, element );
	jLoader.AttachAction ( "onselect", attach.OnSelect, events, element );
	jLoader.AttachAction ( "onblur", attach.OnBlur, events, element );
	jLoader.AttachAction ( "onfocus", attach.OnFocus, events, element );
	
	jLoader.AttachAction ( "onsubmit", attach.OnSubmit, events, element );
	jLoader.AttachAction ( "onreset", attach.OnReset, events, element );
	
	jLoader.AttachAction ( "onload", attach.OnLoad, events, element );
	jLoader.AttachAction ( "onunload", attach.OnUnload, events, element );
	
	jLoader.AttachAction ( "onmouseover", attach.OnMouseOver, events, element );
	jLoader.AttachAction ( "onmouseout", attach.OnMouseOut, events, element );
	jLoader.AttachAction ( "onmouseup", attach.OnMouseUp, events, element );
	jLoader.AttachAction ( "onmousedown", attach.OnMouseDown, events, element );
	jLoader.AttachAction ( "onmousemove", attach.OnMouseMove, events, element );
	
	jLoader.AttachAction ( "onkeyup", attach.OnKeyUp, events, element );
	jLoader.AttachAction ( "onkeydown", attach.OnKeyDown, events, element );
	jLoader.AttachAction ( "onkeypress", attach.OnKeyPress, events, element );
	
	return ( true );
}

_JLoader.prototype.AttachAction = function ( pAction, pFunction, pEvents, pElement ) {
		
	if ( ( typeof pFunction == "function" ) && ( pEvents.InArray ( pAction ) ) ) {
		if ( pElement[pAction] !== null ) var parent = pElement[pAction];
		
		pElement[pAction] = function ( ) {  
			return ( pFunction ( pElement, parent ) );  
		}   
	}
}

// ---[ Useful Functions ]-----------------------------------------------------------------

// Used to search in an array like php function of same name
Array.prototype.InArray = function(p_val) {
	for(var i = 0, l = this.length; i < l; i++) {
		if(this[i] == p_val) {
			return (true);
		} // if
	} // for
	return (false);
} // InArray

// Used to modify a string into proper case
String.prototype.UCWords = function ( str ) {
	this.toLowerCase();
    return (this + '').replace(/^(.)|\s(.)/g, function ($1) {
        return $1.toUpperCase();
    });
}