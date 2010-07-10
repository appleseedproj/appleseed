<!--
	/* @philosophy We weren't kidding when we said views were dumb.  
	 * @philosophy Note the distinct lack of php calls or logic.
	 * @philosophy Since building out the form is DOM based, properly id/class'ing your elements is important.
	 *
	 * @tutorial All data and logic are handled through DOM processing.
	 * @tutorial The view itself is flat html, with no PHP or javascript.
	 * @tutorial The view is a skeleton for the controller to manipulate based on the model.
	 * @tutorial You can either modify the view before displaying to the browser (using the HTML class) or after (using JQuery).
	 
	 * @tutorial Labels, paragraphs, and legends are automatically translated to use the cLanguage (__) calls.
	 */
-->
<section id="general_form">
	<form method="post">
		<input type="hidden" name="Customer_PK" />
		<fieldset>
			<legend>Customers</legend>
			<p>Description of form's purpose would be here.</p>
			
			<fieldset>
				<legend>Edit</legend>
				<p>Some text entry inputs</p>
				<table>
					<tbody>
						<tr>
							<th><label for="text">Customer Name <em>*</em></label></th>
							<td><input type="text" name="CustomerName" /></td>
						</tr>
						<tr>
							<th><label for="text">Contact First Name <em>*</em></label></th>
							<td><input type="text" name="ContactFirstName" /></td>
						</tr>
						<tr>
							<th><label for="text">Contact Last Name <em>*</em></label></th>
							<td><input type="text" name="ContactLastName" /></td>
						</tr>
						<tr>
							<th><label for="text">Phone </label></th>
							<td><input type="text" name="Phone" /></td>
						</tr>
						<tr>
							<th><label for="text">Address 1 </label></th>
							<td><input type="text" name="AddressLine1" /></td>
						</tr>
						<tr>
							<th><label for="text">Address 2 </label></th>
							<td><input type="text" name="addressLine2" /></td>
						</tr>
						<tr>
							<th><label for="text">City </label></th>
							<td><input type="text" name="City" /></td>
						</tr>
						<tr>
							<th><label for="text">State </label></th>
							<td><input type="text" name="State" /></td>
						</tr>
						<tr>
							<th><label for="text">Country </label></th>
							<td><input type="text" name="Country" /></td>
						</tr>
						<tr>
							<th><label for="text">Postal Code </label></th>
							<td><input type="text" name="PostalCode" /></td>
						</tr>
						<tr>
							<th><label for="select">Sales Rep</label></th>
							<td>
								<select name="SalesRep_Employee_FK">
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<p>
				<input type="submit" name="task" value="Save" />
				<input type="submit" name="task" value="Apply" />
				<input type="submit" name="task" value="Cancel" />
			</p>
			
		</fieldset>
	</form>
</section>
<!-- OLD FORM (FOR REFERENCE)
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
				<input type="submit" name="task" value="Save" />
				<input type="submit" name="task" value="Apply" />
				<input type="submit" name="task" value="Cancel" />
			</p>
			
		</fieldset>
	</form>
</section>
-->