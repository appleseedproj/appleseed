<script type="text/javascript" src="/components/system/assets/javascript/admin.nodes.js"></script>

<h1>System Nodes Title</h1>
<p id='system-nodes-message'></p>

<form id="system-nodes-list" method="post">
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
			<th scope="col"><a href="#">Entry</th> 
			<th scope="col"><a href="#">Trust</th> 
			<th scope="col"><a href="#">End Stamp</th> 
			<th scope="col"><a href="#">Source</th> 
			<th><input class="select-toggle checked" type="checkbox" /></th> 
		</thead> 
		<tbody>
			<tr> 
				<th class="tID" scope="row"></th>
				<td class="Entry"></td>
				<td class="Trust"></td>
				<td class="EndStamp"></td>
				<td class="Source"></td>
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