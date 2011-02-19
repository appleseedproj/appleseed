<script type="text/javascript" src="/components/photos/assets/javascript/add.js"></script>

<section class="profile-photos"> 
	<h1 class="photos-edit-title">Add Photos Title</h1>
    <p class="add-message"></p>
	
	<form method="post" class="photos" name="photos" enctype="multipart/form-data">
		<input type="hidden" name="Context" value="">
		<fieldset> 
			<legend class="add-edit" >Add Photos</legend>
               
			<div class="photoset-edit">
				<div><label>Set Label</label><select name="Set"><option value="-1">Create New Set</option></select></div>
				<div><label>Set Name Label</label><input type="text" name="Name" class="required" /></div>
				<div><label>Set Directory Label</label><input type="text" name="Directory" class="required" /></div>
				<div><label>Set Description Label</label><textarea name="Description"></textarea></div>
				<section class="privacy"></section>
			</div>

			<div><label></label><input type='file' name="Images[]"/></div>

			
			<div><label>Photo Description Label</label><textarea name="Descriptions[]"></textarea></div>
			
			<p> 
				<button type="submit" name="Task" value="Save" />Save</button>
				<button type="submit" name="Task" value="Cancel" />Cancel</button>
			</p> 
			
		</fieldset>
	</form>
</section>
