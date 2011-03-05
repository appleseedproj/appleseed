<script type="text/javascript" src="/components/example/assets/javascript/example.js"></script>

<div style="width: 350px; height: 290px">
</div>

<section id="example" class="admin-content">
	<h1>Example</h1>
	<p id='example_message'></p>

		<form method="post">
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
					<li><button class="move-up" name="task" value="move_up" disabled="disabled">Move Up</button></li> 
					<li><button class="move-down" name="task" value="move_down" disabled="disabled">Move Down</button></li> 
					<li><button class="delete-all" name="task" value="delete_all" >Delete All</button></li> 
					<li><button class="add-new" name="task" value="add" >Add New</button></li> 
				</ul> 
			</nav> 
			<nav class="pagination-amount">
			</nav>	
		</form>
</section>
