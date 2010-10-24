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
			<a class="owner-icon-link" href=""><img class="owner-icon" src=""></a>
			<span class="info">
				<p class="content-full"><a class="owner-link" rel=""></a><span class="content"></span></p>
				<abbr class="stamp">4 minutes ago</abbr>
				<span class="actions">
					<a class='add-comment'>Add Comment</a>
					<a class='like'>Like</a>
				</span>
			</span>
			<form class="remove" method="post">
				<input type="hidden" name="Task" value="Remove" />
				<input type="hidden" name="Context" />
				<input type="hidden" name="Identifier" />
				<button class="remove-post">Remove Post</button>
			</form>
		</li>
	</ul>
</section>
