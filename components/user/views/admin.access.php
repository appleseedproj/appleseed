<script type="text/javascript" src="/components/user/assets/javascript/access.js"></script>

<h1>User Access Title</h1>
<p id='user-access-message'></p>

<form method="post">
	<input type="hidden" name="Context" />
	<nav class="controls"> 
		<ul> 
			<li class="move_up"><button name="task" value="move_up" disabled="disabled">Move Up</button></li> 
			<li class="move_down"><button name="task" value="move_down" disabled="disabled">Move Down</button></li> 
			<li class="delete_all"><button name="task" value="delete_all" >Delete +</button></li> 
			<li class="add_new"><button name="task" value="add" >Add New</button></li> 
		</ul> 
	</nav> 
	<nav class="pagination-amount">
		<select>
			<option value="5">5</option>
		</select>
	</nav>	
	<nav class="select">
		<span>Select Options</span> <a class="tooltip" tip="Select All Items" href="#">All</a>, <a href="#">None</a>
	</nav>
	<nav class='pagination'>
	</nav>
	<table id="customer-table-body" cellspacing="0"> 
		<thead> 
			<th scope="col" class="nobg">#</th> 
			<th scope="col"><a href="#">Account</th> 
			<th scope="col"><a href="#">Read</th> 
			<th scope="col"><a href="#">Write</th> 
			<th scope="col"><a href="#">Admin</th> 
			<th scope="col"><a href="#">Inheritance</th> 
			<th><input type="checkbox" /></th> 
		</thead> 
		<tbody>
			<tr> 
				<th class="Access_PK" scope="row"></th>
				<td class="Account"></td>
				<td class="Read"></td>
				<td class="Write"></td>
				<td class="Admin"></td>
				<td class="Inheritance"></td>
				<td class="Masslist"><input type="checkbox" /></td>
			</tr> 
		</tbody>
	</table>

	<nav class="select">
		<span>Select Options</span> <a href="#">All</a>, <a href="#">None</a>
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