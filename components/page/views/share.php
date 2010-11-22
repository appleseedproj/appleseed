<script type="text/javascript" src="/components/page/assets/javascript/share.js"></script>

<section id="page-share" class="ui-tabs" >
		<ul class="share-tabs"> 
			<li id="share-status-tab" class="ui-tabs-selected"><a href="#share-status">Status</a></li> 
			<li id="share-link-tab"><a href="#share-link">Link</a></li> 
		</ul> 
		<div id="share-status">
			<form method="post" class="post" name="post">
				<input type="hidden" name="Task" value="Share">
				<input type="hidden" name="Context" value="">
				<input type="hidden" name="Type" value="Post">
		
				<textarea placeholder="What Is On Your Mind" class="content" name="Content"></textarea>
				<button class="submit">Submit Post</button>
				<section class="privacy">
				</section>
			</form>
		</div>
		
		<div id="share-link" class="ui-tabs-hide">
			<form method="post" class="post-link" name="post-link">
				<input type="hidden" name="Task" value="Share">
				<input type="hidden" name="Context" value="">
				<input type="hidden" name="Type" value="Link">
		
				<input type="text" placeholder="Enter A Link Url" class="link" name="Link" />
				<span class="loading">Link Loading Message</span>
				<div class="attach">
					<ul class="thumbs">	
						<li class="thumb template"><img class="thumbnail" /></li>
					</ul>
					<input type="hidden" name="LinkThumb" />
					<span class="info">
						<input placeholder="Enter A Link Title" name="LinkTitle" type="text" class="title" value="" />
						<textarea placeholder="Enter A Link Description" name="LinkDescription" class="description" /></textarea>
					</span>
					<span class="thumbs-scroll">
						<button name="scroll-previous" class="scroll-previous">Previous Thumbnail</button>
						<button name="scroll-next" class="scroll-next">Next Thumbnail</button>
					</span>
				</div>
				<textarea placeholder="Enter Link Content" class="link-content" name="LinkContent"></textarea>
				<button class="submit">Submit Post</button>
				<section class="privacy">
				</section>
			</form>
		</div>
		
	</form>
</section>