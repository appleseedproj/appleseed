<script type="text/javascript" src="/components/example/assets/javascript/example.js"></script>

<section id="example" class="admin-content">
	<h1>Example</h1>
	<p id='example_message'></p>

		<form method="post">
			<nav class="controls"> 
				<ul> 
					<li class="move_up"><button name="task" value="move_up" disabled="disabled">Move Up</button></li> 
					<li class="move_down"><button name="task" value="move_down" disabled="disabled">Move Down</button></li> 
					<li class="delete_all"><button name="task" value="delete_all" >Delete All</button></li> 
					<li class="add_new"><button name="task" value="add" >Add New</button></li> 
				</ul> 
			</nav> 
			<nav class="pagination-amount">
				<select>
					<option value="5">5</option>
				</select>
			</nav>	
			<nav class="select">
				<span>Select Options</span> <a class="tooltip" tip="Select All Items" href="#">Select All</a>, <a href="#">Select None</a>
			</nav>
			<nav class='pagination'>
			</nav>
			<table id="customer-table-body" cellspacing="0"> 
				<thead> 
					<th scope="col" class="nobg">#</th> 
					<th scope="col"><a href="#">Customer Name</th> 
					<th scope="col"><a href="#">Contact Name</th> 
					<th scope="col"><a href="#">Country</th> 
					<th><input type="checkbox" /></th> 
				</thead> 
				<tbody>
					<tr> 
						<th class="Customer_PK" scope="row"></th>
						<td class="CustomerName"></td>
						<td class="ContactName"></td>
						<td class="Country"></td>
						<td class="Masslist"><input type="checkbox" /></td>
					</tr> 
				</tbody>
			</table>
		
			<input type="hidden" name="Context" />
			<nav class="select">
				<span>Select Options</span> <a href="#">Select All</a>, <a href="#">Select None</a>
			</nav>
			<nav class='pagination'>
			</nav>
			<nav class="controls"> 
				<ul> 
					<li class="move_up"><button name="task" value="move_up" disabled="disabled">Move Up</button></li> 
					<li class="move_down"><button name="task" value="move_down" disabled="disabled">Move Down</button></li> 
					<li class="delete_all"><button name="task" value="delete_all" >Delete All</button></li> 
					<li class="add_new"><button name="task" value="add" >Add New</button></li> 
				</ul> 
			</nav> 
			<nav class="pagination-amount">
				<select>
					<option value="5">5</option>
				</select>
			</nav>	
		</form>
</section>