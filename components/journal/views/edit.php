<script type="text/javascript" src="/components/journal/assets/javascript/edit.js"></script>

<section id="profile-journal-edit"> 
	<h1 class="journal-edit-title">Journal Entry Title</h1>
	
	<form method="post" class="journal" name="journal">
		<input type="hidden" name="Context" value="">
		<a class="remove">Remove</a>
		<fieldset> 
			<legend class="add-edit" >Add Journal Entry</legend>
               
			<div><label>Title Label</label><input type="text" name="Title" class="required" /></div>
			<div><label>Body Label</label><textarea name="Body"></textarea></div>
			
			<p class="journal-markup-info">Textile Markup Information</p>
			
			<section class="privacy"></section>
			<p> 
				<button type="submit" name="Task" value="Save" />Save</button>
				<button type="submit" name="Task" value="Cancel" />Cancel</button>
			</p> 
			
		</fieldset>
	</form>
	<div class="journal-preview">
		<h1 class="preview-title">Preview Title</h1>
		<h2 class="preview-url">Preview Url</h2>
		<p class="preview">No Preview Yet</p>
	</div>
</section>