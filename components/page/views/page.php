<script type="text/javascript" src="/components/page/assets/javascript/page.js"></script>
<section id="profile-page">
	<form method="post" class="post">
		<input type="hidden" name="Task" value="Share">
		<input type="hidden" name="Context" value="">
		<textarea placeholder="What Is On Your Mind" class="comments" name="Comments"></textarea>
		<button class="submit">Submit Post</button>
		<div class="privacy">
			<h2 class="privacy-message">Who can see this?</h2>
			<ul>
				<li class="everybody"><input name="Privacy['everybody']" type="checkbox"><label for="privacy[]">Everybody Has Access</label></li>
				<li class="friends"><input name="Privacy['friends']" type="checkbox"><label for="privacy[]">Friends Only</label></li>
				<li class="circle"><input name="Privacy['baltimore']" type="checkbox"><label for="privacy[]">Baltimore</label></li>
			</ul>
		</div>
	</form>
	
	<p class="page-message"></p>
	
	<ul class="list">
		<li class="item">
			<a class="owner-icon-link" href="http://lost.appleseed/profile/abed/"><img class="owner-icon" src="http://community.appleseed/?_social=true&_task=user.icon&_request=abed&_width=64&_height=64"></a>
			<p class="comment"><a class="owner-link" rel="abed@community.appleseed">abed@community.appleseed</a><span class="content">This is a comment that is very long, probably longer than will end up being allowed. This is a comment that is very long, probably longer than will end up being allowed. This is a comment that is very long, probably longer than will end up being allowed. This is a comment that is very long, probably longer than will end up being allowed.</span></p>
			<abbr class="stamp">4 minutes ago</abbr>
			<span class="actions">
				<a class='add-comment'>Add Comment</a>
				<a class='like'>Like</a>
			</span>
			<form class="delete">
				<button class="delete-post">Delete Post</button>
			</form>
		</li>
	</ul>
</section>
