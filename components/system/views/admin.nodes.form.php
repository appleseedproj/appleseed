<script type="text/javascript" src="/components/system/assets/javascript/admin.nodes.js"></script>
<h1>Edit Node Title</h1>

<p id='system-nodes-message'></p>
<p>Edit Node Description</p>

<form id="system-nodes-edit" method="post">
	<input type="hidden" name="Node_PK" />
	<input type="hidden" name="Context" />
	
	<fieldset>
		<legend id='edit-subtitle'>Edit Node Subtitle</legend>
		<label for="Domain">Domain<em>*</em></label><input class="required" type="text" name="Domain" />
		<label for="Trust">Trust<em>*</em></label>
			<select id="node-trusted" type="text" name="Trust" />
				<option value="trusted">Trusted</option>
				<option value="discovered">Discovered</option>
				<option value="blocked">Blocked</option>
			</select>
		<label for="Expires">Expires<em>*</em></label><input type="text" name="Expires" />
		<label>Never Expires</label> 
		<input type="checkbox" name="Never" />
		<label for="Access">Access<em>*</em></label>
			<select id="node-trusted" type="text" name="Access" />
				<option  value="public">Public</option>
				<option  value="trusted">Trusted</option>
				<option  value="private">Private</option>
			</select>
		<label>Inheritance</label> 
		<input type="checkbox" name="Inherit" />
		<label>Callback</label> 
		<input type="checkbox" name="Callback" />
		<label>Source</label> 
		<input type="text" disabled="disabled" name="Source" />
	</fieldset>
	<p>
		<button name="task" value="Save">Save</button>
		<button name="task" value="Apply">Apply</button>
		<button name="task" class="cancel" value="Cancel">Cancel</button>
	</p>
		
</form>