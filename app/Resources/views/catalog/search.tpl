{if $products}
<table class="product-table" cellpadding="0" cellspacing="0" border="0">
	{foreach from=$products item=item name=product}
	{if $smarty.foreach.product.iteration == 1}<tr>{/if}
		<td class="product-content">
			<div class="product-image pull-left">
				<a href="{raURL node=catalog method=stuff prms=$item.id}">{if $item.small_imagenew}<img src="{$item.small_imagenew}">{else}<img src="/img/noimage_small.jpg">{/if}</a>
			</div>
			<div class="product-description">
				<div class="product-title"><a href="{raURL node=catalog method=stuff prms=$item.id}"><span>{$item.name}</span></a></div>	
				<div class="product-producer"><a href="{raURL node=catalog method=brand prms=$item.producer_id}">{$item.producer_id_name}</a> ({$item.producer_id_country})</div>
				<div class="product-producer"><a href="{raURL node=catalog method=index prms=$item.category_id}">{$item.category_id_title}</a></div>
				{if $item.discount_price != '0.00'}
				<div class="product-price-no">{$item.price} руб.</div>
				<div class="product-price">{if count($prices)}от {/if}<span id="price_{$item.id}">{$item.discount_price}</span> руб.</div>
				{else}
				<div class="product-price">{if count($prices)}от {/if}<span id="price_{$item.id}">{$item.price}</span> руб.</div>
				{/if}
				<input type="hidden" id="amount_{$item.id}" value="1">
				<a class="btn btn-warning btn-large" href="javascript:addCartItem({$item.id})">Купить</a>
				{if count($prices)}
				<div class="product-sizes">
				<h5>Размерный ряд</h5> 
				<select class="span35" id="product_price_{$item.id}" onchange="setPrice({$item.id})">
				<option rel="{if $item.discount_price == '0.00'}{$item.price}{else}{$item.discount_price}{/if}" value="0">...</option>
				{foreach from=$prices item=price}
				<option rel="{$price.price}" value="{$price.id}">Размер: {$price.size_id_name}{if $price.color_id}, цвет: {$price.color_id_name}{/if} - {$price.price|number_format:2:',':' '} руб.</option>
				{/foreach}
				</select>
				</div>
				{else}
				<input type="hidden" value="0" id="product_price_{$item.id}">                        
				{/if}
			</div>
			<div class="clearfix"></div>
		</td>
	{if $smarty.foreach.product.iteration % 2 == 0}</tr><tr>{/if}
	{/foreach}
</table>
{$paginator->render()}
{else}
	<form class="form-search" action="{raURL node=catalog method=search}" method="get">
		<input type="text" name="text" value="{$text}" autocomplete="off" placeholder="Поиск" class="input-xlarge search-query">
		<input type="submit" class="btn" value="Поиск">
	</form>
	<h4>Ничего не найдено</h4>
{/if}
