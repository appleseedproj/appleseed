<!--
	/* @philosophy We weren't kidding when we said views were dumb.  
	 * @philosophy Note the distinct lack of php calls.
	 * @philosophy Since building out the form is DOM based, properly id/class'ing your elements is important.
	 *
	 * @tutorial All data and logic are handled through DOM processing.
	 * @tutorial The view itself is flat html, with no PHP or javascript.
	 * @tutorial The view as a skeleton for the controller to manipulate based on the model.
	 * @tutorial You can either modify the view before displaying to the browser or after (using JQuery).
	 */
-->
<section id="general_form">
	<form method="post">
		<fieldset>
			<legend>General Form</legend>
			<p>Description of form's purpose would be here.</p>
			
			<fieldset>
				<legend>Sub-Fieldset 1</legend>
				<p>Some text entry inputs.</p>
				<table>
					<tbody>
						<tr>
							<th><label for="text">Text Input <em>*</em></label></th>
							<td><input type="text" name="text" /></td>
						</tr>
						<tr>
							<th><label for="text">Removed Input </label></th>
							<td><input type="text" name="removed_text" /></td>
						</tr>
						<tr>
							<th><label for="text">Removed Row (This row should not display) </label></th>
							<td><input type="text" name="removed_row" /></td>
						</tr>
						<tr>
							<th><label for="passwd">Password Input</label></th>
							<td><input type="password" name="passwd" /></td>
						</tr>
						<tr>
							<th><label for="text_area">Text Area Input</label></th>
							<td><textarea rows="10" cols="50" name="text_area"></textarea></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			
			<fieldset>
				<legend>Sub-Fieldset 2</legend>
				<p>Some "special" inputs.</p>
				<table>
					<tbody>
						<tr>
							<th><label for="file">File Browser <em>*</em></label></th>
							<td><input type="file" name="file" /></td>
						</tr>
						<tr>
							<th><label for="select">Static Selection Field</label></th>
							<td>
								<select name="StaticSelect">
									<optgroup label="Group 1">
										<option>Thing 1.1</option>
										<option>Thing 1.2</option>
										<option>Thing 1.3</option>
									</optgroup>
									<optgroup label="Group 2">
										<option>Thing 2.1</option>
										<option>Thing 2.2</option>
										<option>Thing 2.3</option>
									</optgroup>
									<optgroup label="Group 3">
										<option>Thing 3.1</option>
										<option>Thing 3.2</option>
										<option>Thing 3.3</option>
									</optgroup>
								</select>
							</td>
						</tr>
						<tr>
							<th><label for="select">Dynamic Selection Field</label></th>
							<td>
								<select name="DynamicSelect">
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			
			<fieldset>
				<legend>Sub-Fieldset 3 <em>*</em></legend>
				<p>Checkboxes and radio buttons.</p>
				<table>
					<tbody>
						<tr>
							<td>
								<input type="checkbox" name="StaticCheck[0]" /> <label for="StaticCheck[0]">Static Checkbox 1</label>
								<input type="checkbox" name="StaticCheck[1]" /> <label for="StaticCheck[1]">Static Checkbox 2</label>
								<input type="checkbox" name="StaticCheck[2]" /> <label for="StaticCheck[2]">Static Checkbox 3</label>
							</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name="DynamicCheck" /> <label for="DynamicCheck">Dynamic Checkbox</label>
							</td>
						</tr>
						<tr>
							<td
								<input type="radio" name="StaticRadio" /> <label for="StaticRadio">Static Radio 1</label>
								<input type="radio" name="StaticRadio" /> <label for="StaticRadio">Static Radio 2</label>
								<input type="radio" name="StaticRadio" /> <label for="StaticRadio">Static Radio 3</label>
							</td>
						</tr>
						<tr>
							<td>
								<input type="radio" name="DynamicRadio" /> <label for="DynamicRadio">Dynamic Radio</label>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			
			<p>
				<input type="submit" name="task" value="Submit" />
				<input type="reset" name="task" value="Reset" />
				<input type="button" name="task" value="Button" />
				<button name="task" value="ButtonTwo">Button Two</button>
			</p>
			
		</fieldset>
	</form>
</section>