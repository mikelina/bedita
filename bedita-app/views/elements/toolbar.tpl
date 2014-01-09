{strip}
<div class="head">
	
	<div class="toolbar" style="white-space:nowrap">
		
		<h2>
			{if !empty($title)}
			
				{$title}
			
			{elseif !empty($sectionSel)}
			
				{t}{$itemName|default:$moduleName}{/t} in “ 
				<span style="color:white" class="evidence">{$sectionSel.title}</span> ”
			
			{elseif !empty($pubSel)}
				
				{t}{$itemName|default:$moduleName}{/t} in “ 
				<span style="color:white" class="evidence">{$pubSel.title}</span> ”
			
			{else}
			
				{t}all {$itemName|default:$moduleName}{/t}
			
			{/if}
			
			
			{if !empty($stringSearched)}
			
				{if preg_match('/^\*.+\*$/', $stringSearched)}
					&nbsp; {t}matching the query containing{/t}
				{elseif preg_match('/^\*/', $stringSearched)}
					&nbsp; {t}matching the query ending by{/t}
				{elseif preg_match('/\*$/', $stringSearched)}
					&nbsp; {t}matching the query beginning by{/t}
				{else}
					&nbsp; {t}matching the query{/t}
				{/if}

				: “ <span style="color:white" class="evidence">{$stringSearched|regex_replace:['/^\*/', '/\*$/']:''}</span> ”
				
			{/if}
			
		</h2>
		
		
		<table>
		<tr>
			{if ($view->viewVars.module_modify eq '1') && empty($noitem)} 
			<td>
				<a href="{$html->url('/')}{$currentModule.url}/view">{t}Create new{/t} &nbsp;
				{if !empty($itemName)}
					{t}{$itemName}{/t}
				{else}
					{assign var=leafs value=$conf->objectTypes.leafs}
					{foreach from=$conf->objectTypes item=type key=key}	
						{if ( in_array($type.id,$leafs.id) && is_numeric($key) && $type.module_name == $currentModule.name )}
							{t}{$type.model|lower}{/t} 
						{/if}
					{/foreach}
				{/if}
				</a>
			</td>
			{/if}
			<td>
			<span class="evidence">{$beToolbar->size()} &nbsp;</span> {t}{$itemName|default:$moduleName}{/t}
			</td>
			
			<td>
				{$beToolbar->first('page','','page')}
				<span class="evidence"> {$beToolbar->current()} </span> 
				{t}of{/t}  &nbsp;
				<span class="evidence">
					{if ($beToolbar->pages()) > 0}
					{$beToolbar->last($beToolbar->pages(),'',$beToolbar->pages())}
					{else}1{/if}
				</span>
			</td>
			
			<td>{$beToolbar->next('next','','next')}  <span class="evidence"> &nbsp;</span></td>
			
			<td> {$beToolbar->prev('prev','','prev')}  <span class="evidence"> &nbsp;</span></td>
			
			<!--
			<td> 
			
				<form action="{$html->url('/')}{$moduleName}/index{if !empty($sectionSel)}/id:{$sectionSel.id}{/if}" method="post">				
				<span>{t}search{/t}</span> : <span class="evidence"> &nbsp;</span>
				<input type="text" name="searchstring" value="{$stringSearched|default:""}"/>
				
				<input type="submit" value="{t}go{/t}"/>
				</form>
				
			</td>
			-->
		</tr>
		</table>

	</div>

</div> 
{/strip}
