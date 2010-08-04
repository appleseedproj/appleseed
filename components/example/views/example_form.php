<script type="text/javascript" src="/components/example/assets/javascript/example.js"></script>

<!--
	/* @philosophy We weren't kidding when we said views were dumb.  
	 * @philosophy Note the distinct lack of php calls or logic.
	 * @philosophy Since building out the form is DOM based, properly id/class'ing your elements is important.
	 *
	 * @tutorial All data and logic are handled through DOM processing.
	 * @tutorial The view itself is flat html, with no PHP or javascript.
	 * @tutorial The view is a skeleton for the controller to manipulate based on the model.
	 * @tutorial You can either modify the view before displaying to the browser (using the HTML class) or 
	 * @tutorial after (using JQuery).
	 
	 * @tutorial Labels, paragraphs, link text, headers, button text, spans and legends are 
	 * @tutorial automatically translated to use the cLanguage (__) calls.
	 */
-->

<section id="example">
	<form id="edit_form" method="post">
		<input type="hidden" name="Customer_PK" />
		<input type="hidden" name="Context" />
		<fieldset>
			<legend id='example_title'>Customers</legend>
			<p id='example_message'></p>
			
			<fieldset>
				<legend id='example_subtitle'>Edit</legend>
				<p>Edit form field description</p>
				<table>
					<tbody>
						<tr>
							<th><label for="CustomerName">Customer Name <em>*</em></label></th>
							<td><input class="required" type="text" name="CustomerName" /></td>
						</tr>
						<tr>
							<th><label for="ContactFirstName">Contact First Name <em>*</em></label></th>
							<td><input class="required" type="text" name="ContactFirstName" /></td>
						</tr>
						<tr>
							<th><label for="ContactLastName">Contact Last Name <em>*</em></label></th>
							<td><input class="required" type="text" name="ContactLastName" /></td>
						</tr>
						<tr>
							<th><label for="Phone">Phone </label></th>
							<td><input type="text" name="Phone" /></td>
						</tr>
						<tr>
							<th><label for="Email">E-Mail <em>*</em></label></th>
							<td><input type="text" name="Email" /></td>
						</tr>
						<tr>
							<th><label for="AddressLine1">Address 1 </label></th>
							<td><input type="text" name="AddressLine1" /></td>
						</tr>
						<tr>
							<th><label for="AddressLine2">Address 2 </label></th>
							<td><input type="text" name="addressLine2" /></td>
						</tr>
						<tr>
							<th><label for="City">City </label></th>
							<td><input type="text" name="City" /></td>
						</tr>
						<tr>
							<th><label for="State">State </label></th>
							<td><input type="text" name="State" /></td>
						</tr>
						<tr>
							<th><label for="Country">Country </label></th>
							<td><input type="text" name="Country" /></td>
						</tr>
						<tr>
							<th><label for="PostalCode">Postal Code </label></th>
							<td><input type="text" name="PostalCode" /></td>
						</tr>
						<tr>
							<th><label for="SalesRep_Employee_FK">Sales Rep</label></th>
							<td>
								<select class='required' name="SalesRep_Employee_FK">
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
			<p>
				<input type="submit" name="task" value="Save" />
				<input type="submit" name="task" value="Apply" />
				<input type="submit" name="task" class="cancel" value="Cancel" />
			</p>
			
		</fieldset>
	</form>
</section>