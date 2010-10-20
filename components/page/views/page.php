<script type="text/javascript" src="/components/page/assets/javascript/page.js"></script>
<section id="profile-page">
	<form method="post" class="post">
		<input type="hidden" name="Task" value="Share">
		<input type="hidden" name="Context" value="">
		<textarea placeholder="What Is On Your Mind" class="content" name="Content"></textarea>
		<button class="submit">Submit Post</button>
		<section class="privacy">
		</section>
	</form>
	
	<p class="page-message"></p>
	
	<ul class="list">
		<li class="item">
			<a class="owner-icon-link" href="http://lost.appleseed/profile/abed/"><img class="owner-icon" src="http://community.appleseed/?_social=true&_task=user.icon&_request=abed&_width=64&_height=64"></a>
			<span class="info">
				<p class="content-full"><a class="owner-link" rel="abed@community.appleseed">abed@community.appleseed</a><span class="content"></span></p>
				<abbr class="stamp">4 minutes ago</abbr>
				<span class="actions">
					<a class='add-comment'>Add Comment</a>
					<a class='like'>Like</a>
				</span>
			</span>
			<form class="delete" method="post">
				<input type="hidden" name="Task" value="Remove" />
				<input type="hidden" name="Context" />
				<input type="hidden" name="Identifier" />
				<button class="delete-post">Delete Post</button>
			</form>
		</li>
	</ul>
</section>
