<div data-role="page">

	<div data-role="header">
		<h1>{t}Documents{/t}</h1>
		<a href="#" data-rel="back" data-icon="arrow-l" data-iconpos="notext">{t}Back{/t}</a>
	</div><!-- /header -->

	<div data-role="content">

  {strip}
		{$view->element('list_objects')}
  {/strip}

	</div><!-- /content -->
	{$view->element('footer')}
</div><!-- /page -->