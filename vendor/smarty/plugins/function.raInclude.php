<?php
/**
 * Smarty plugin
 * @package Web2b
 * @subpackage plugins
 */

/**
 * Smarty {raInclude} function plugin
 *
 * Type:     function<br>
 * Name:     raInclude<br>
 * Purpose:  initialize overlib
 * @author   Roman Alyakrytskiy
 * @param array
 * @param Smarty
 * @return string
 */

function smarty_function_raInclude($params, &$smarty) {
	if (empty($params['var'])) {
		$smarty->trigger_error('raInclude: Не указан параметр: var');
	} else {
		if ($item = $GLOBALS['container']->getItem('page_block',"name='{$params['var']}' AND publish=1")) {
			return $item['content'];
		} else {
			return '';
		}
	}
}

?>
