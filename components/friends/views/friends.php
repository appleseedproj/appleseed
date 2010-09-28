<script type="text/javascript" src="/components/friends/assets/javascript/friends.js"></script>
<section id="profile-friends">
	<nav id="profile-friends-tabs" >
	</nav>
	
	<span class="profile-friends-circle-add"><a href="#">Add A New Circle</a></span>
	
	<h1>
		<span class='profile-friends-title'></span>
		<span class="profile-friends-circle-edit"><a href="#">Edit This Circle</a></span>
		<span class="profile-friends-count additional-info"></span>
	</h1>
	<p id='friends-message'></p>
	
	<nav class='pagination'>
	</nav>
	
	<ul class='friends-list'>
		<li class='friends-list-item'>
			<a href="" class="friends-icon-link"><img class="friends-icon" src=""></a>
			<div class="friends-info">
				<span class="friends-fullname"><a class="friends-fullname-link" href=""></a></span>
				<span class="friends-circle-editor">
					<form class="friend-circle-edit" method="post">
						<input type="hidden" name="Task" value="ApplyToCircle">
						<input type="hidden" name="Friend">
						<input type="hidden" name="Context">
						<input type="hidden" name="Current">
						<select class="friend-circle-edit-list" name="Circle">
						</select>
						<button class="friends-circle-edit-save" name="Task" value="Save">Save</button>
					</form>
				</span>
				<span class="friends-location">Location</span>
				<p class="friends-status">Status</p>
				<span class="friends-mutual-count">Mutual Count</span>
				<span class="friends-add-friend"><a class="friends-add-friend-link" href="">Add as a friend</a></span>
				<span class="friends-remove-friend"><a class="friends-remove-friend-link" href="">Remove friend</a></span>
				<a href="" class="friends-identity">username@domain</a>
			</div>
		</li>
	</ul>
	<span class="profile-friends-circle-remove"><a href="#">Remove This Circle</a></span>
</section>