

<script type="text/javascript">
<!--
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var urls = Array();
urls['deleteSelected'] = "{$html->url('deleteSelected/')}";
urls['changestatusSelected'] = "{$html->url('changeStatusObjects/')}";
urls['copyItemsSelectedToAreaSection'] = "{$html->url('addItemsToAreaSection/')}";
urls['moveItemsSelectedToAreaSection'] = "{$html->url('moveItemsToAreaSection/')}";
urls['removeFromAreaSection'] = "{$html->url('removeItemsFromAreaSection/')}";
urls['assocObjectsCategory'] = "{$html->url('assocCategory/')}";
urls['disassocObjectsCategory'] = "{$html->url('disassocCategory/')}";
urls['checkSelected'] = "{$html->url('checkMultiUrl/')}";

{literal}
$(document).ready(function(){

	$(".indexlist TD").not(".checklist").not(".go").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );

	$("#deleteSelected").bind("click", function() {
		if(!confirm(message)) 
			return false ;	
		$("#formObject").attr("action", urls['deleteSelected']) ;
		$("#formObject").submit() ;
	});

	$("#assocObjects").click( function() {
		var op = ($('#areaSectionAssocOp').val()) ? $('#areaSectionAssocOp').val() : "copy";
		$("#formObject").attr("action", urls[op + 'ItemsSelectedToAreaSection']) ;
		$("#formObject").submit() ;
	});

	$(".opButton").click( function() {
		$("#formObject").attr("action",urls[this.id]) ;
		$("#formObject").submit() ;
	});
});


{/literal}

//-->
</script>	

	
<form method="post" action="" id="formObject">

	<input type="hidden" name="data[id]"/>


	<table class="indexlist">
	{capture name="theader"}
		<tr>
			<th></th>
			<th>{$beToolbar->order('title', 'Title')}</th>
			<th>{$beToolbar->order('url', 'Url')}</th>
			<th>{$beToolbar->order('http_code', 'check result')}</th>
			<th style="text-align:center">{$beToolbar->order('status', 'Status')}</th>
			<th style="text-align:center">{t}Link{/t}</th>
			<th>{t}Notes{/t}</th>
		</tr>
	{/capture}
		
		{$smarty.capture.theader}
	
		{section name="i" loop=$objects}
		
		<tr class="obj {$objects[i].status}">
			<td class="checklist">
			{if (empty($objects[i].fixed))}
				<input type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" />
			{/if}
			</td>
			<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|truncate:64|default:"<i>[no title]</i>"}</a></td>
			<td>{$objects[i].url|default:''|truncate:48:'(...)':true:true}</td>
			<td>{$objects[i].http_code|default:''}</td>
			<td style="text-align:center">{$objects[i].status}</td>
		<td class="go">
				<input type="button" value="{t}go{/t}" onclick="window.open('{$objects[i].url|default:''}','_blank')" />	
			</td>
			
			<td>{if $objects[i].num_of_editor_note|default:''}<img src="{$html->webroot}img/iconNotes.gif" alt="notes" />{/if}</td>
		</tr>
		
		
		
		{sectionelse}
		
			<tr><td colspan="100" style="padding:30px">{t}No {$moduleName} found{/t}</td></tr>
		
		{/section}
		
{if ($smarty.section.i.total) >= 10}
		
			{$smarty.capture.theader}
			
{/if}


</table>


<br />

{if !empty($objects)}

<div style="white-space:nowrap">
	
	{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')} 
	&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
	&nbsp;&nbsp;&nbsp
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> {t}(un)select all{/t}</label>

	
</div>

<br />

<div class="tab"><h2>{t}Bulk actions on{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>

	<div>
		{t}check urls{/t}: <input id="checkSelected" type="button" value="{t}check selected links{/t}" class="opButton"/>
		<hr />
		
		{t}change status to:{/t}
		<select style="width:75px" id="newStatus" name="newStatus">
			<option value=""> -- </option>
			{html_options options=$conf->statusOptions}
		</select>
		<input id="changestatusSelected" type="button" value=" {t}ok{/t} " class="opButton" />
		
		<hr />

		{if !empty($categories)}
			{t}category{/t}
			<select id="objCategoryAssoc" class="objCategoryAssociation" name="data[category]">
			<option value="">--</option>
			{foreach from=$categories item='category' key='key'}
			<option value="{$key}">{$category}</option>
			{/foreach}
			</select>
			<input id="assocObjectsCategory" type="button" value="{t}Add association{/t}" class="opButton" /> / <input id="disassocObjectsCategory" type="button" value="{t}Remove association{/t}" class="opButton" />
			<hr />
		{/if}

	{if !empty($tree)}

		{assign var='named_arr' value=$view->params.named}
		{if empty($named_arr.id)}
			{t}copy{/t}
		{else}
			<select id="areaSectionAssocOp" name="areaSectionAssocOp" style="width:75px">
				<option value="copy"> {t}copy{/t} </option>
				<option value="move"> {t}move{/t} </option>
			</select>
		{/if}
		&nbsp;{t}to{/t}:  &nbsp;

		<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
		{$beTree->option($tree)}
		</select>

		<input type="hidden" name="data[source]" value="{$named_arr.id|default:''}" />
		<input id="assocObjects" type="button" value=" ok " />
		<hr />

		{if !empty($named_arr)}
		<input id="removeFromAreaSection" type="button" value="{t}Remove selected from section{/t}" />
		<hr/>
		{/if}
	{/if}

		<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
		
		<hr />
{bedev}
		{t}Export to{/t}:
		<select name="export">
			<option>Delicious(XBEL)</option>
			<option>Excel</option>
		</select>
{/bedev}
	</li>
</ul>
	</div>

{/if}

</form>



<br />
<br />
<br />
<br />
	
	



