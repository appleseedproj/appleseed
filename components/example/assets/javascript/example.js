/**
 * @tutorial Call this function to instantiate the framework.  The parameter is both the id
 * of the element to modify internally, and the name of the class, in this case, "Example".
 * 
 */

jLoader.Initialize( "example" );

/**
 * @tutorial Create the base object, "Example"
 *
 * @tutorial Anything within this function acts as a "Constructor". 
 *
 * @tutorial Since JLoader uses onLoad, you can put functions here 
 * @tutorial which require the full document to be loaded.
 */

jLoader.Example = function ( ) { 

	// Add form validation to the edit form.
	$("#edit_form").validate();
	
}

/*
 * @tutorial Create the base element object, "Anchor"
 * 
 * @tutorial This is a placeholder function, and is never executed.
 */

jLoader.Example.Anchor = function ( ) { }

/**
 * @tutorial Create a function for the "onclick" action.
 * 
 */

jLoader.Example.Anchor.OnClick = function ( pElement, pParent ) {
}
		
jLoader.Example.Form = function ( ) { }

jLoader.Example.Form.OnKeyPress = function ( pElement, pParent ) {
}
