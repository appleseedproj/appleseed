<script type="text/javascript" src="/components/user/assets/javascript/admin.access.js"></script>
<h1>Edit Node Title</h1>
<p id='system-nodes-message'></p>
<p>Edit Node Description</p>

<form id="system-nodes-edit" method="post">
	<input type="hidden" name="tID" />
	<input type="hidden" name="Context" />
	
	<fieldset>
		<legend id='edit-subtitle'>Edit Node Subtitle</legend>
		<label for="Account">User Account<em>*</em></label><input class="required" type="text" name="Account" />
		<label for="Location">Location<em>*</em></label><input class="required" type="text" name="Location" />
		<label>Node</label> 
		<input type="checkbox" name="Read" /><label class="checkbox" for="Read">Read</label>
		<input type="checkbox" name="Write" /><label class="checkbox" for="Write">Write</label>
		<input type="checkbox" name="Admin" /><label class="checkbox" for="Admin">Admin</label>
		<label>Inheritance</label> 
		<input type="checkbox" name="Inheritance" /><label class="checkbox" for="Inheritance">Inherit</label>
	</fieldset>
	<p>
		<input type="submit" name="task" value="Save" />
		<input type="submit" name="task" value="Apply" />
		<input type="submit" name="task" class="cancel" value="Cancel" />
	</p>
		
</form>