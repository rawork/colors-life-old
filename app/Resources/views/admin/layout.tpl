<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache">
<title>Управление сайтом - {$prj_name}</title>
<link href="{$theme_ref}/css/calendar-mos.css" type="text/css" rel="stylesheet">
<link href="{$theme_ref}/css/colorPicker.css" type="text/css" rel="stylesheet">
<link href="{$theme_ref}/css/style.css" type="text/css" rel="stylesheet">
<script type="text/javascript">
var prj_ref = '{$prj_ref}';
var theme_ref = '{$theme_ref}';
var state = '{$state}';
</script>
<script src="{$prj_ref}/js/jquery.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/jquery_ui/ui.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/jquery_ui/dimensions.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/jquery_ui/draggable.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/jquery_ui/dimmer.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/jquery_ui/colorPicker.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/jquery_ui/jquery.MultiFile.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/jquery_ui/jquery.form.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/jquery_ui/jquery.blockUI.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/admin_tools.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/admin.functions.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/editor/tiny_mce_gzip.js" type="text/javascript"></script> 
<script src="{$prj_ref}/admin/editor/tmcegzip_init.js" type="text/javascript"></script> 
<script src="{$prj_ref}/admin/editor/tmce_init.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/calendar.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/calendar-ru.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/calendar-setup.js" type="text/javascript"></script>
<script src="{$prj_ref}/admin/js/mctabs.js" type="text/javascript"></script>
</head>
<body bgcolor="#FFFFFF">
<table id="mainbody" width="100%" style="height:100%" border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="182"><img src="{$theme_ref}/img/0.gif" width="30" height="42" border="0"></td>
          <td width="100%"> <table width="100%" style="height:100%" border="0" cellpadding="0" cellspacing="0">
              <tr> 
                <td align="left" valign="middle" nowrap>
                  <div class="up-text">
                    <a href="http://{$prj_name}.{$prj_zone}" target="_blank">{$prj_name}.{$prj_zone}</a><br>
                  </div>
                  </td>
              </tr>
            </table></td>
          <td><table border="0" cellspacing="0" cellpadding="3">
              <tr> 
				{foreach key=k from=$langs item=language}
                {if $k > 0}<td>|</td>{/if}
                {if $language.name == $lang}
                <td><div class="lang-active">{$language.name}</div></td>
                {else}
                <td><div class="lang-noactive"><a href="#" onClick="document.frmLang.lang.value = '{$language.name}';document.frmLang.submit();">{$language.name}</a></div></td>
                {/if}
                {/foreach}
                <td><img src="{$theme_ref}/img/0.gif" width="10" height="3" border="0"></td>
                <td style="padding-right: 10px;white-space: nowrap;"><strong>{$user}</strong> / <a href="/admin/logout" title="Выйти из Управление сайтом">Выйти</a></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td valign="top" width="100%">
	<div class="contextmenu2">
	<table align="center" cellpadding="0" cellspacing="0" border="0" class="contextmenu2">
	<tr class="top">
<td class="left"><div class="empty"></div></td>
<td><div class="empty"></div></td>
<td class="right"><div class="empty"></div></td>
</tr>
<tr>
<td class="left"><div class="empty"></div></td>
<td class="content">
<table cellpadding="2" cellspacing="0" border="0">
<tr>
<td><div class="section-separator first"></div></td>
<td align="center"><a href="javascript:getComponentList('content', '{$module}');" class="context-button">Структура и контент</a></td>
<td><div class="section-separator"></div></td>
<td align="center"><a href="javascript:getComponentList('service', '{$module}');" class="context-button">Сервисы</a></td>
<td><div class="section-separator"></div></td>
<td align="center"><a href="javascript:getComponentList('settings', '{$module}');" class="context-button">Настройки</a></td>
<td><div class="section-separator"></div></td>
<td align="center"><a href="javascript:fileBrowser('file');" class="context-button">Файловый менеджер</a></td>
</tr>
</table>
</td>
<td class="right"><div class="empty"></div></td>
</tr>
<tr class="bottom">
<td class="left"><div class="empty"></div></td>
<td><div class="empty"></div></td>
<td class="right"><div class="empty"></div></td>
</tr>

<tr class="bottom-all">
<td class="left"><div class="empty"></div></td>
<td><div class="empty"></div></td>
<td class="right"><div class="empty"></div></td>
</tr>
</table></div>
	  </td>
  </tr>
  <tr> 
    <td height="100%" valign="top"> <table width="100%" style="height:100%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="225" height="100%" valign="top" class="leftmenu"> <table width="100%" style="height:100%" border="0" cellpadding="0" cellspacing="0">
              <tr> 
                <td bgcolor="#FAFAFA"><img src="{$theme_ref}/img/0.gif" width="20" height="1"></td>
                <td width="100%" height="100%" valign="top" bgcolor="#FAFAFA"> 
                  <br>
                  <div id="componentMenu">{include file='admin/mainmenu.tpl'}</div>
				   <form name="frmLang" method="post"><input type="hidden" name="lang" value="ru"></form>	
				  </td>
              </tr>
            </table>
			<br>
			<img src="{$theme_ref}/img/0.gif" width="225" height="1" />
			</td>
			<td valign="top" class="main">
		  		<!-- bottom -->
            {if $module eq ''}
            <table width="100%" border="0">
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
			  <!-- bottom -->
			  <br>
			  <img src="{$theme_ref}/img/0.gif" width="755" height="1" />
            </td>
        </tr>
      </table></td>
  </tr>
  <tr>
  <td class="copyright">Управление сайтом &copy; 2000-2012</td>
</table>
<script type="text/javascript">
window.admin_menu = new PopupMenu("admin_menu");
</script>
<div id="admin_menu">
<table cellpadding="0" cellspacing="0" border="0">
<tr><td class="popupmenu">
<table cellpadding="0" cellspacing="0" border="0" id="admin_menu_items">
<tr><td></td></tr>
</table>
</td></tr>
</table>
</div>
<div id="waiting"><table border="0" cellpadding="3" cellspacing="0"><tr>
<td><img src="{$theme_ref}/img/loading.gif" border="0"></td><td>Обработка запроса...</td>
</tr></table></div>
<div id="popup_tree"></div>
</body>
</html>