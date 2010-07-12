<section id="admin_page">

	<form method="post">
		<input type="hidden" name="Context" />

		<h1>Example Customers Title</h1>

		<p>Example Customers Description</p>

		<nav>
			<ul>
				<li><button name="task" value="move_up">Move Up</button></li>
				<li><button name="task" value="move_down">Move Down</button></li>
				<li><button name="task" value="delete_all">Delete All</button></li>
			</ul>
		</nav>

		<table>
			<thead id="customer_table_head">
				<tr>
					<th scope="col"><a>#</a></th>
					<th scope="col"><a>Customer Name</a></th>
					<th scope="col"><a>Contact Name</a></th>
					<th scope="col"><a>Country</a></th>
					<th scope="col"><input type="checkbox" /></th>
				</tr>
			</thead>
			<tbody id="customer_table_body">
				<tr>
					<td class="Customer_PK" scope="row"><a></a></td>
					<td class="CustomerName"><a></a></td>
					<td class="ContactName"><a></a></td>
					<td class="Country"><a></a></td>
					<td class="Masslist"><input type="checkbox" /></td>
				</tr>
			</tbody>
		</table>

		<nav>
			<ul>
				<li><button name="task" value="move_up">Move Up</button></li>
				<li><button name="task" value="move_down">Move Down</button></li>
				<li><button name="task" value="delete_all">Delete All</button></li>
			</ul>
		</nav>

	</form>
</section>