<script type="text/javascript" src="/components/system/assets/javascript/admin.nodes.js"></script>
<h1>Edit Node Title</h1>
<p id='system-nodes-message'></p>
<p>Edit Node Description</p>

<form id="system-nodes-edit" method="post">
	<input type="hidden" name="tID" />
	<input type="hidden" name="Context" />
	
	<fieldset>
		<legend id='edit-subtitle'>Edit Node Subtitle</legend>
		<label for="Entry">Entry<em>*</em></label><input class="required" type="text" name="Entry" />
		<label for="Trust">Trust<em>*</em></label>
			<select id="node-trusted" type="text" name="Trust" />
				<option value="10">Trusted</option>
				<option value="20">Blocked</option>
			</select>
		<label for="EndStamp">End Stamp<em>*</em></label><input class="required" type="text" name="EndStamp" />
		<label>Never Expires</label> 
		<input type="checkbox" name="Never" />
		<label for="Share">Share<em>*</em></label>
			<select id="node-trusted" type="text" name="Share" />
				<option  value="10">Public</option>
				<option  value="20">Trusted</option>
				<option  value="30">Private</option>
			</select>
		<label>Inheritance</label> 
		<input type="checkbox" name="Inherit" />
		<label>Callback</label> 
		<input type="checkbox" name="Callback" />
		<label>Source</label> 
		<input type="text" disabled="disabled" name="Source" />
	</fieldset>
	<p>
		<input type="submit" name="task" value="Save" />
		<input type="submit" name="task" value="Apply" />
		<input type="submit" name="task" class="cancel" value="Cancel" />
	</p>
		
</form>