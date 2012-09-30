<!DOCTYPE html>
<html>
  <head>
    <title>Управление сайтом - {$prj_name}.{$prj_zone}</title>
    <link href="{$prj_ref}/bundles/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="{$theme_ref}/css/default.css" rel="stylesheet">
	<link href="{$theme_ref}/css/colorpicker.css" type="text/css" rel="stylesheet">
	<link href="{$theme_ref}/css/calendar-mos.css" rel="stylesheet">
	<link href="{$prj_ref}/bundles/treeview/jquery.treeview.css" rel="stylesheet">
	<script type="text/javascript">
		
	var prj_ref = '{$prj_ref}';
	var theme_ref = '{$theme_ref}';
	var state = '{$state}';
	{literal}
	var calendars = {};
	function addCalendar(name, time) {
		calendars[name] = time;
	}
	{/literal}
	</script>
	
  </head>
  <body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" style="margin-left: 20px;" href="http://{$prj_name}.{$prj_zone}" target="_blank">{$prj_name}.{$prj_zone}</a>
				<ul class="nav">
				<li class="divider-vertical"></li>
				{foreach from=$states key=stateName item=stateTitle}  
				<li{if state == stateName} class="active"{/if}><a href="javascript:getComponentList('{$stateName}', '{$module}')">{$stateTitle}</a></li>
				<li class="divider-vertical"></li>
				{/foreach}
				<li><a href="javascript:fileBrowser('file');" class="context-button">Файловый менеджер</a></li>
				<li class="divider-vertical"></li>
				<li><a href="#">{foreach from=$languages item=language}
					{if $language.name == $currentLanguage}
						<span class="label label-info">{$language.name}</span>
					{else}
						<span class="label pointer-view" onclick="setLanguage('{$language.name}')">{$language.name}</span>
					{/if}
					{/foreach}</a>
				</li>
				<li class="divider-vertical"></li>
				<li><a href="/admin/logout" title="Выйти из Управление сайтом"><img src="{$theme_ref}/img/0.gif" width="10" height="3" border="0"> Выйти</a></li>
				</ul>
			</div>
		</div>
	</div>  
	<div class="container-fluid">
	<div class="row-fluid">
		<div class="span9 vertical-padding">
			<div class="well" id="componentMenu">{include file='admin/mainmenu.tpl'}</div>
			<form name="frmLang" method="post"><input type="hidden" name="lang" value="ru"></form>
		</div>
		<div class="span31 vertical-padding left-padding">
			{if $module eq ''}
				<table>
				<tr>
				<td> {foreach from=$modules item=mod}
				<div style="float: left;padding: 5px">
					<table cellspacing="0">
					<tr>
						<td align="center"><a href="{$mod.ref}"><img border="0" src="{$mod.icon}"></a></td>
					</tr>
					<tr>
						<td align="center"><a href="{$mod.ref}">{$mod.title}</a></td>
					</tr>
					</table>
				</div>
				{/foreach} </td>
				</tr>
				</table>
			{else}
			{$content}
			{/if}
		</div>
	</div>
	</div>
	<div class="navbar navbar-fixed-bottom admin-copyright">Управление сайтом &copy; 2000-2012</div>	
	<div id="waiting" class="closed"><img src="{$theme_ref}/img/loading.gif">Обработка запроса...</div>
	<div class="modal closed" id="modalDialog" tabindex="-1" role="dialog" aria-labelledby="popupTitle" aria-hidden="true">
		<div class="modal-header admin-modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h5 id="popupTitle"></h5>
		</div>
		<div class="modal-body" id="popupContent"></div>
		<div class="modal-footer admin-modal-footer" id="popupButtons"></div>
	</div>
	<script src="{$prj_ref}/js/jquery.js"></script>
    <script src="{$prj_ref}/bundles/bootstrap/js/bootstrap.js"></script>
	<script src="{$prj_ref}/bundles/treeview/lib/jquery.cookie.js"></script>
	<script src="{$prj_ref}/bundles/treeview/jquery.treeview.js"></script>
	<script src="{$theme_ref}/js/jquery.colorpicker.js"></script>
	<script src="{$prj_ref}/bundles/multifile/jquery.MultiFile.js"></script>
	<script src="{$prj_ref}/bundles/multifile/jquery.form.js"></script>
	<script src="{$prj_ref}/bundles/multifile/jquery.blockUI.js"></script>
	<script src="{$theme_ref}/js/admin.js"></script>
	<script src="{$theme_ref}/editor/tiny_mce_gzip.js"></script> 
	<script src="{$theme_ref}/editor/tmcegzip_init.js"></script> 
	<script src="{$theme_ref}/editor/tmce_init.js"></script>
	<script src="{$theme_ref}/js/calendar.js"></script>
	<script src="{$theme_ref}/js/calendar-ru.js"></script>
	<script src="{$theme_ref}/js/calendar-setup.js"></script>
  </body>
</html>