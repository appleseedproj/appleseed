<script type="text/javascript" src="/components/page/assets/javascript/share.js"></script>

<section id="page-share" class="ui-tabs" >
	<form method="post" class="post">
		<input type="hidden" name="Task" value="Share">
		<input type="hidden" name="Context" value="">
		
		<ul class="share-tabs"> 
			<li id="share-status-tab" class="ui-tabs-selected"><a href="#share-status">Status</a></li> 
			<li id="share-link-tab"><a href="#share-link">Link</a></li> 
		</ul> 
		<div id="share-status">
			<textarea placeholder="What Is On Your Mind" class="content" name="Content"></textarea>
		</div>
		<div id="share-link" class="ui-tabs-hide">
			<input type="text" placeholder="Enter A Link Url" class="link" name="Link" />
			<span class="loading">Link Loading Message</span>
			<div class="attach">
				<ul class="thumbs">	
					<li class="thumb template"><img class="thumbnail" /></li>
				</ul>
				<input type="hidden" name="LinkThumb" />
				<span class="info">
					<input name="LinkTitle" type="text" class="title" value="This is a title" />
					<textarea name="LinkDescription" class="description" />Driven by Lisa Jackson, the EPA continues to take historic steps forward in leading America toward a cleaner and more sustainable future, including the recent release of industrial emissions guidelines, a first for the country. But resounding victories for the GOP in last week's elections have broug</textarea>
				</span>
				<span class="thumbs-scroll">
					<button name="scroll-previous" class="scroll-previous">Previous Thumbnail</button>
					<button name="scroll-next" class="scroll-next">Next Thumbnail</button>
				</span>
			</div>
			<textarea placeholder="Enter Link Content" class="link-content" name="LinkContent"></textarea>
		</div>
		<button class="submit">Submit Post</button>
		
		<section class="privacy">
		</section>
	</form>
</section>