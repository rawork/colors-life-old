<?php
	
    $templates_tables = array();
    $templates_tables[] = array(
	'name' => 'templates',
	'component' => 'templates',
	'title' => 'Шаблоны',
	'order_by' => 'name',
	'is_lang' => true,
	'fieldset' => array (
        'name' => array (
            'name' => 'name',
            'title' => 'Название макета',
            'type' => 'string',
            'width' => '95%'
        ),
        'template' => array (
            'name' => 'template',
            'title' => 'Шаблон HTML',
            'type' => 'template'
        )
    ));
    $templates_tables[] = array(
	'name' => 'version',
	'component' => 'templates',
	'title' => 'Версионирование',
	'order_by' => 'credate',
	'is_hidden' => true,
	'fieldset' => array (
		'cls' => array (
            'name' => 'cls',
            'title' => 'Таблица',
            'type' => 'string',
            'width' => '20%',
            'search'=> true
        ),
        'fld' => array (
            'name' => 'fld',
            'title' => 'Поле',
            'type' => 'string',
            'width' => '25%',
            'search'=> true
        ),
        'rc' => array (
            'name' => 'rc',
            'title' => 'Запись',
            'type' => 'number',
            'width' => '25%',
            'search' => true
        ),
		'file' => array (
            'name'  => 'file',
            'title' => 'Файл-версия',
            'type' => 'file',
            'width' => '25%'
        )
    ));
	$templates_tables[] = array(
	'name' => 'rules',
	'component' => 'templates',
	'title' => 'Правила шаблонов',
	'order_by' => 'ord',
	'is_lang' => true,
	'is_sort' => true,
	'fieldset' => array (
        'template_id' => array (
            'name' => 'template_id',
            'title' => 'Шаблон',
            'type' => 'select',
			'l_table' => 'templates_templates',
			'l_field' => 'name',
			'l_lang' => true,
			'width' => '31%',
            'group_update' => true
        ),
        'type' => array (
            'name' => 'type',
            'title' => 'Тип условия',
            'type' => 'enum',
            'select_values' => 'Раздел|F;Параметр URL|U;Период времени|T',
            'width' => '20%',
            'group_update' => true
        ),
        'cond' => array (
            'name' => 'cond',
            'title' => 'Условие',
            'type' => 'string',
            'width' => '20%',
            'group_update' => true
        ),
		'date_beg' => array (
            'name' => 'date_beg',
            'title' => 'Начало показа',
            'type' => 'datetime',
            'width' => '12%'
        ),
		'date_end' => array (
            'name' => 'date_end',
            'title' => 'Конец показа',
            'type' => 'datetime',
            'width' => '12%'
        )
    ));
?>