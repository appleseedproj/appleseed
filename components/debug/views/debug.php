<script type="text/javascript" src="/components/debug/assets/javascript/debug.js"></script>

<!-- Debug -->
<div id="appleseed-debug" class="container_16">
	<div id="debug-menu" class="container_16">
		<div id="debug-tabs" class="grid_16">
			<nav>
				<ul>
					<li class="ui-tabs-selected" ><a href="#debug-warnings">Warnings</a></li>
					<li><a href="#debug-queries">SQL Queries</a></li>
					<li><a href="#debug-network">Network</a></li>
					<li><a href="#debug-memory">Memory</a></li>
					<li><a href="#debug-benchmarks">Benchmarks</a></li>
				</ul>
			</nav>
			
		</div>
	</div>
	<div id="debug-container" class="container_16">
		<div id="debug-main" class="grid_16">
		
			<section id="debug-warnings">
				<h1>Warnings</h1>
				<h2 id="warnings-system-total"></h2>
				<table>
					<thead>
						<th>#</th>
						<th>Warning</th>
						<th>File</th>
						<th>Line#<th>
					</thead>
					<tbody>
						<tr>
							<td class='debug-warnings-id'></td>
							<td class='debug-warnings-warning'></td>
							<td class='debug-warnings-file'></td>
							<td class='debug-warnings-line'></td>
						</tr>
					</tbody>
				</table>
			</section>
			
			<section id="debug-network">
				<h1>Network</h1>
				<table>
					<thead>
						<th>#</th>
						<th>Request</th>
					</thead>
					<tbody>
						<tr>
							<td class='debug-network-id'></td>
							<td class='debug-network-data'>
								<span class='debug-network-request'></span>
								<span class='debug-network-return'></span>
							</td>
						</tr>
					</tbody>
				</table>
			</section>
			
			<section id="debug-queries">
				<h1>SQL Queries</h1>
				<h2 id="queries-system-total"></h2>
				<table>
					<thead>
						<th>#</th>
						<th>Query</th>
						<th>Class</th>
						<th>Table</th>
					</thead>
					<tbody>
						<tr>
							<td class='debug-queries-id'></td>
							<td class='debug-queries-query'></td>
							<td class='debug-queries-class'></td>
							<td class='debug-queries-table'></td>
						</tr>
					</tbody>
				</table>
			</section>
			
			<section id="debug-memory">
				<h1>Memory</h1>
				<h2 id="memory-system-total"></h2>
				<table>
					<thead>
						<th>#</th>
						<th>Controller</th>
						<th>Component</th>
						<th>Instance</th>
						<th>View</th>
						<th>Memory</th>
					</thead>
					<tbody>
						<tr>
							<td class='debug-memory-id'></td>
							<td class='debug-memory-controller'></td>
							<td class='debug-memory-component'></td>
							<td class='debug-memory-instance'></td>
							<td class='debug-memory-view'></td>
							<td class='debug-memory-amount'></td>
						</tr>
					</tbody>
				</table>
			</section>
			
			<section id="debug-benchmarks">
				<h1>Benchmarks</h1>
				<h2 id="benchmarks-system-total"></h2>
				<table>
					<thead>
						<th>#</th>
						<th>Controller</th>
						<th>Component</th>
						<th>Instance</th>
						<th>View</th>
						<th>Time</th>
					</thead>
					<tbody>
						<tr>
							<td class='debug-benchmark-id'></td>
							<td class='debug-benchmark-controller'></td>
							<td class='debug-benchmark-component'></td>
							<td class='debug-benchmark-instance'></td>
							<td class='debug-benchmark-view'></td>
							<td class='debug-benchmark-time'></td>
						</tr>
					</tbody>
				</table>
			</section>
		</div>
	</div>
</div>