
<h1 onClick="window.location='{$html->url('/events')}'" class="eventi"><a href="{$html->url('/events')}">eventi</a></h1>


<div class="inside">

	<ul class="simpleMenuList" style="margin:0px 0px 10px 0px">
		<li {if $sez=="new"}class="on"{/if}>    <b>&#8250;</b> <a href="{$html->url('/events/frmAdd')}">crea nuovo evento</a></li>
		<li {if $sez=="indice"}class="on"{/if}> <b>&#8250;</b> <a href="{$html->url('/events/index')}">elenco eventi</a></li>
		<li {if $sez=="detail"}class="on"{/if}> <b>&#8250;</b> <a href="{if $firstContent}{$html->url('/documents/frmModify/')}{$firstContent.ID}{else}#{/if}">dettaglio evento </a></li>
		<li {if $sez=="groups"}class="on"{/if}> <b>&#8250;</b> <a href="{$html->url('/documents/frmGroups')}">modifica tipologie </a></li>
	</ul>
	
{if !empty($paginator)}{include file="pagination.tpl" sez="menuSX"}{/if}

{if $sez=="indice"}

	<h2>legenda:</h2>
	<br/>
	<div class="scad" style="border:1px solid gray; border-top:0px; padding:2px; margin-top:-10px; height:12px; width:130px;"> scaduti </div>
	<div class="draft" style="border:1px solid gray; border-top:0px; padding:2px; margin-top:0px; height:12px; width:130px;"> draft </div>
	<div class="off" style="border:1px solid gray; border-top:0px; padding:2px; margin-top:0px; height:12px; width:130px"> off </div>
{/if}

</div>
