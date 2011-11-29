{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">
	
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>
		
	<ul class="menuleft insidecol">
		<li {if $view->action eq 'index'}class="on"{/if}>{$tr->link('Users', '/users/')}</li>
		<li {if $view->action eq 'viewUser' && (empty($userdetail))}class="on"{/if}>{$tr->link('New user', '/users/viewUser')}</li>
		<li {if $view->action eq 'groups'}class="on"{/if}>{$tr->link('User groups', '/users/groups')}</li>
	</ul>

	{$view->element('user_module_perms')}

</div>