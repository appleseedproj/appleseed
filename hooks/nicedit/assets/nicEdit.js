/** @todo  Find a more elegant way to store this and pass it through the parameters. */
var nicEdit_jLoaderDocumentParent = jLoader.Document;

jLoader.Document = function ( ) { 

	nicEditors.allTextAreas(
		{buttonList : ['bold','italic','underline','link','unlink','image','fontSize','fontFamily'],
		 iconsPath : '/hooks/nicedit/assets/Nicedit-0.9r23/nicEditorIcons.gif'}
	);
	
	nicEdit_jLoaderDocumentParent();
}
