{if count($items)}
<h4>От А до Я</h4>
<div class="tag-cloud">
	<ul>
	{foreach from=$items item=item}
	<li><a class="w{$item.weight}" rel="tag" href="{raURL node=articles}?tag={$item.id}">{$item.name}</a></li>
	{/foreach}
	</ul>
	<br><a href="{raURL node=articles method=tags}">все метки</a>
</div>
<div class="clearfix"></div>
{else}
Данные не найдены.
{/if}			