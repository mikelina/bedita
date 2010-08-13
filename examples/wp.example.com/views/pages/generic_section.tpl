<div id="container">
	<div id="content" role="main">

	{if !empty($section.contentRequested) || (!empty($section.childContents) && count($section.childContents) == 1 && $section.toolbar.dim > 1)}
		<div id="post-{$section.currentContent.id}" class="post-{$section.currentContent.id} page type-page hentry">
									<h1 class="entry-title">{$section.currentContent.title}</h1>

			
			<div class="entry-meta">
				<span class="meta-prep meta-prep-author">Posted on</span> <a href="http://localhost/workspace/wordpress/?p=1" title="12:51 pm" rel="bookmark"><span class="entry-date">{$section.currentContent.publication_date|date_format:"%B %e, %Y"}</span></a> <span class="meta-sep">by</span> <span class="author vcard"><a class="url fn n" href="" title="View all posts by bato">{$section.currentContent.author|default:$section.currentContent.UserCreated.realname}</a></span>
			</div><!-- .entry-meta -->

			<div class="entry-content">
				{$section.currentContent.body}
			</div><!-- .entry-content -->

			<div class="entry-utility">
				{if !empty($section.currentContent.Category)}
				<span class="cat-links">
					<span class="entry-utility-prep entry-utility-prep-cat-links">Posted in</span> 
					{foreach from=$section.currentContent.Category item="cat" name="fccat"}
						{$cat.label}{if !$smarty.foreach.fccat.last},{/if}
					{/foreach}
				</span>
				<span class="meta-sep">|</span>
				{/if}

				{if !empty($section.currentContent.Tag)}
				<span class="tag-links">
					<span class="entry-utility-prep entry-utility-prep-tag-links">Tagged</span>
					{foreach from=$section.currentContent.Tag item="tag" name="fctag"}
						<a href="{$html->url('/tag/')}{$tag.name|replace:" ":"+"}">{$tag.label}</a>{if !$smarty.foreach.fctag.last},{/if}
					{/foreach}
				</span>
				<span class="meta-sep">|</span>
				{/if}

				Bookmark the <a href="{$html->url($section.currentContent.canonicalPath)}" title="Permalink to {$section.currentContent.title}" rel="bookmark">permalink</a>.
			</div><!-- .entry-utility -->

		</div><!-- #post-## -->

		<div id="nav-below" class="navigation">
			{if !empty($previousItem)}
				<div class="nav-previous"><a href="{$html->url($previousItem.canonicalPath)}" ><span class="meta-nav">&larr;</span> {$previousItem.title}</a>
				</div>
			{/if}

			{if !empty($nextItem)}
				<div class="nav-next"><a href="{$html->url($nextItem.canonicalPath)}" >{$nextItem.title} <span class="meta-nav">&rarr;</span></a>
				</div>
			{/if}
		</div><!-- #nav-below -->

		<div id="comments">
			{$view->element("show_comments")}
		</div><!-- #comments -->


	<!-- list of items -->
	{elseif !empty($section.childContents)}

		{assign_associative var="options" items=$section.childContents toolbar=$section.toolbar}
		{$view->element("list_items", $options)}
		
	{else}
		<p>No items yet.</p>
	{/if}

	</div><!-- #content -->

</div><!-- #container -->

{$view->element('right_column')}


	
