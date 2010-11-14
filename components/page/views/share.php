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
					<input placeholder="Enter A Link Title" name="LinkTitle" type="text" class="title" value="" />
					<textarea placeholder="Enter A Link Description" name="LinkDescription" class="description" /></textarea>
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