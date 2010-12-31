<h1>Reply To Comment</h1> 
<ol class="comments outer"> 
	<li> 
		<div class="comment"> 
			<div class="comment-icon-area"> 
				<a class="comment-icon-link" href="#"><img class="comment-icon" src="" alt="Commenter Icon" /></a> 
			</div> 
			<div class="comment-content"> 
				<div class="comment-area">
					<a class="comment-user-link" href="#"></a>
					<span class="comment-body"></span>
				</div>
				<abbr class="stamp"></abbr> 
				<nav> 
					<ul> 
						<li class="delete-area">
							<form name="delete" method="post">
								<input type="hidden" name="Context" />
								<input type="hidden" name="Task" value="Delete" />
								<input type="hidden" name="Entry_PK" />
								<button class="delete">Delete</button>
							</form>
						</li> 
						<li class="reply-area">
							<form name="reply" method="post">
								<input type="hidden" name="Context" />
								<input type="hidden" name="Task" value="Reply" />
								<input type="hidden" name="Parent_ID" />
								<button class="reply">Reply</button>
							</form>
						</li> 
					</ul> 
				</nav> 
			</div> 
		</div> 
	</li> 
</ol>
<form name="comment" method="post">
	<input type="hidden" name="Context" />
	<input type="hidden" name="Parent_ID" />
	<input type="hidden" name="Task" value="Reply" />

	<textarea name="Body" placeholder="Write A Comment"></textarea>
	<p class="buttons"> 
		<button type="submit" name="Task" value="Save" />Save</button>
		<button type="submit" name="Task" value="Cancel" />Cancel</button>
	</p> 
</form>

