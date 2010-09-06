<script type="text/javascript" src="/components/user/assets/javascript/admin.access.js"></script>

<h1>User Access Title</h1>
<p id='user-access-message'></p>

<form id="user-access-list" method="post">
	<input type="hidden" name="Context" />
	<nav class="controls"> 
		<ul> 
			<li><button class="move-up" name="task" value="move_up" disabled="disabled">Move Up</button></li> 
			<li><button class="move-down" name="task" value="move_down" disabled="disabled">Move Down</button></li> 
			<li><button class="delete-all" name="task" value="delete_all" >Delete All</button></li> 
			<li><button class="add-new" name="task" value="add" >Add New</button></li> 
		</ul> 
	</nav> 
	<nav class="pagination-amount">
	</nav>	
	<nav class="select">
		<span>Select Options</span> <a class="select-all" href="#">All</a>, <a class="select-none" href="#">None</a>
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
			<th><input class="select-toggle checked" type="checkbox" /></th> 
		</thead> 
		<tbody>
			<tr> 
				<th class="Access_PK" scope="row"></th>
				<td class="Account"></td>
				<td class="Read"></td>
				<td class="Write"></td>
				<td class="Admin"></td>
				<td class="Inheritance"></td>
				<td class="Masslist"><input class='masslist-checkbox' type="checkbox" /></td>
			</tr> 
		</tbody>
	</table>

	<nav class="select">
		<span>Select Options</span> <a class="select-all" href="#">All</a>, <a class="select-none" href="#">None</a>
	</nav>
	<nav class='pagination'>
	</nav>
	<nav class="controls"> 
		<ul> 
			<li><button class="move-up" name="task" value="move_up" disabled="disabled">Move Up</button></li> 
			<li><button class="move-down" name="task" value="move_down" disabled="disabled">Move Down</button></li> 
			<li><button class="delete-all" name="task" value="delete_all" >Delete All</button></li> 
			<li><button class="add-new" name="task" value="add" >Add New</button></li> 
		</ul> 
	</nav> 
	<nav class="pagination-amount">
	</nav>	
</form>