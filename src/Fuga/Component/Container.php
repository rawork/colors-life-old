<?php

namespace Fuga\Component;

use Fuga\CommonBundle\Security\SecurityHandler;

class Container 
{
	private $tables;
	private $modules = array();
	private $ownmodules = array();
	private $controllers = array();
	private $templateVars = array();
	private $services = array();
	private $managers = array();
	private $tempmodules = array();
	
	public function __construct() {
		$this->tempmodules = array(
			'user' => array(
				'name'  => 'user',
				'title' => 'Пользователи',
				'ctype' => 'system',
				'entitites' => array(
					array(
						'name' => 'user-user',
						'title' => 'Список пользователей'
					),
					array(
						'name' => 'user-group',
						'title' => 'Группы пользователей'
					),
					array(
						'name' => 'user-address',
						'title' => 'Адреса доставки'
					)
				)
			),
			'template' => array(
				'name'  => 'template',
				'title' => 'Шаблоны',
				'ctype' => 'system',
				'entitites' => array(
					array(
						'name' => 'template-template',
						'title' => 'Шаблоны'
					),
					array(
						'name' => 'template-rule',
						'title' => 'Правила шаблонов'
					),
				)
			),
			'config' => array(
				'name'	=> 'config',
				'title' => 'Настройки',
				'ctype' => 'system',
				'entitites' => array(
					array(
						'name' => 'config-module',
						'title' => 'Модули'
					),
					array(
						'name' => 'config-variable',
						'title' => 'Переменные'
					),
					array(
						'name' => 'config-backup',
						'title' => 'Обслуживание'
					),
				)
			),
			'table' => array(
				'name'	=> 'table',
				'title' => 'Таблицы',
				'ctype' => 'system',
				'entitites' => array(
					array(
						'name' => 'table-table',
						'title' => 'Таблицы'
					),
					array(
						'name' => 'table-field',
						'title' => 'Поля'
					),
				)
			),
			'maillist' => array(
				'name'  => 'maillist',
				'title' => 'Подписка',
				'ctype' => 'service',
				'entitites' => array()
			),
			'form' => array(
				'name'  => 'form',
				'title' => 'Формы',
				'ctype' => 'service',
				'entitites' => array()
			),
			'vote' => array(
				'name'  => 'vote',
				'title' => 'Голосование',
				'ctype' => 'service',
				'entitites' => array()
			),
		);
	}
	
	public function initialize() {
		$this->tables = $this->getAllTables();
	}

	public function getModule($name) {
		if (empty($this->modules[$name])) {
			throw new \Exception('Модуль '.$name.' отсутствует'); 
		}
		
		return  $this->modules[$name];
	}

	public function getModules() {
		if (!$this->ownmodules) {
			if ($this->get('security')->isSuperuser()) {
				$this->ownmodules = $this->modules;
			} elseif ($user = $this->get('security')->getUser($this->get('util')->_sessionVar('user'))) {
				$this->ownmodules = $this->tempmodules;
				if (!$user['is_admin']) {
					unset($this->ownmodules['user'], $this->ownmodules['template'], $this->ownmodules['table']);
				}
				$sql = 'SELECT id, sort, name, title, \'content\' AS ctype 
					FROM config_module WHERE id IN ('.$user['rules'].') ORDER BY sort, title';
				$stmt = $this->get('connection1')->prepare($sql);
				$stmt->execute();
				$this->ownmodules = array_merge($this->ownmodules, $stmt->fetchAll());
			}
		}
		
		return $this->ownmodules;
	}
	
	public function getModulesByState($state) {
		$modules = array();
		foreach ($this->getModules() as $module) {
			if ($state == $module['ctype']) {
				$modules[$module['name']] = $module;
			}
		}
		
		return $modules;
	}
	
	private function getAllTables() {
		$ret = array();
		$this->modules = $this->tempmodules;
		$sql = "SELECT id, sort, name, title, 'content' AS ctype FROM config_module ORDER BY sort, title";
		$stmt = $this->get('connection1')->prepare($sql);
		$stmt->execute();
		while ($module = $stmt->fetch()) {
			$this->modules[$module['name']] = array(
				'name'  => $module['name'],
				'title' => $module['title'],
				'ctype' => $module['ctype'],
				'entities' => array()
			);
		}
		foreach ($this->modules as $module) {
			try {
				$className = 'Fuga\\CommonBundle\\Model\\'.ucfirst($module['name']);
				$model = new $className();
				foreach ($model->tables as $table) {
					$table['is_system'] = true;
					$ret[$table['component'].'_'.$table['name']] = new DB\Table($table);
				}
			} catch (Exception\AutoloadException $e) {
				
			}
		}
		$sql = "SELECT t.*,m.name as component 
				FROM table_table t 
				JOIN config_module m ON t.module_id=m.id 
				WHERE t.publish=1 ORDER BY t.sort";
		$stmt = $this->get('connection1')->prepare($sql);
		$stmt->execute();
		$tables = $stmt->fetchAll();
		foreach ($tables as $table) {
			$ret[$table['component'].'_'.$table['name']] = new DB\Table($table);
		}
		return $ret;
	}

	public function getTable($name) {
		if (isset($this->tables[$name])) {
			return $this->tables[$name];
		} else {
			throw new \Exception('Таблица "'.$name.'" не найдена');
		}
	}

	public function getTables($moduleName) {
		$tables = array();
		foreach ($this->tables as $table) {
			if ($table->moduleName == $moduleName)
				$tables[$table->tableName] = $table;
		}
		return $tables;
	}
	
	public function getPrev($table, $id, $link = 'parent_id') {
		return $this->getTable($table)->getPrev($id, $link);
	}

	public function getItem($table, $criteria = 0, $sort = null, $select = null) {
		return $this->getTable($table)->getItem($criteria, $sort, $select);
	}

	public function getItems($table, $criteria = null, $sort = null, $limit = null, $select = null, $detailed = true) {
		$options = array('where' => $criteria, 'order_by' => $sort, 'limit' => $limit, 'select' => $select);
		$this->getTable($table)->select($options);
		return $this->getTable($table)->getNextArrays($detailed);
	}

	public function getItemsRaw($sql) {
		$ret = array();
		if (!preg_match('/(delete|truncate|update|insert|drop|alter)+/i', $sql)) {
			$stmt = $this->get('connection1')->prepare($sql);
			$stmt->execute();
			$items = $stmt->fetchAll();
			foreach ($items as $item) {
				if (isset($item['id'])) {
					$ret[$item['id']] = $item;
				} else {
					$ret[] = $item;
				}
			}
		}
		return $ret;
	}

	public function getItemRaw($sql) {
		$ret = null;
		if (!preg_match('/(delete|truncate|update|insert|drop|alter)+/i', $sql)) {
			$stmt = $this->get('connection1')->prepare($sql);
			$stmt->execute();
			$ret = $stmt->fetch();
		}
		return $ret;
	}

	public function count($table, $criteria = '') {
		return $this->getTable($table)->count($criteria);
	}

	public function addItem($class, $values) {
		return $this->getTable($class)->insert($values);
		
	}

	public function addItemGlobal($class) {
		return $this->getTable($class)->insertGlobals();
	}

	public function updateItem($table, $values, $criteria) {
		return $this->getTable($table)->update($values, $criteria);
	}

	public function deleteItem($table, $query) {
		if ($ids = $this->delRel($table, $this->getItems($table, !empty($query) ? $query : '1<>1'))) {
			return $this->getTable($table)->delete('id IN ('.$ids.')');
		} else {
			return false;
		}	
	}

	public function delRel($table, $items = array()) {
		$ids0 = '';
		foreach ($items as $item) {
			if ($this->getTable($table)->params['is_system']) {
				foreach ($this->tables as $t) {
					if ($t->moduleName != 'user' && $t->moduleName != 'template' && $t->moduleName != 'page') {
						foreach ($t->fields as $field) {
							$ft = $t->createFieldType($field);
							if (stristr($ft->params['type'], 'select') && $ft->params['l_table'] == $table) {
								$this->deleteItem($t->dbName(), $ft->getName().'='.$item['id']);
							}
							$ft->free();
						}
					}
				}
			}
			foreach ($this->getTable($table)->fields as $field) {
				$ft = $this->getTable($table)->createFieldType($field, $item);
				if ($ft->params['type'] == 'image' || $ft->params['type'] == 'file' || $ft->params['type'] == 'template') {
					@unlink($GLOBALS['PRJ_DIR'].$item[$ft->getName()]);
					if (isset($ft->params['sizes'])) {
						$path_parts = pathinfo($GLOBALS['PRJ_DIR'].$item[$ft->getName()]);
						$asizes = explode(',', $ft->params['sizes']);
						foreach ($asizes as $sz) {
							$asz = explode('|', $sz);
							if (sizeof($asz) == 2) {
								@unlink($path_parts['dirname'].'/'.$asz[0].'_'.$path_parts['basename']);	
							}
						}
					}
				}
				$ft->free();
			}
			$ids0 .= ($ids0 ? ',' : '').$item['id'];
		}
		return $ids0;
	}

	public function copyItem($table, $id = 0, $times = 1) {
		$entity = $this->getItem($table, $id);
		if ($entity) {
			for ($i = 1; $i <= $times; $i++)
				$this->getTable($table)->insertArray($entity);
			return true;
		} else {
			return false;
		}
	}

	public function dropTable($table, $complex = false) {
		if ($complex) {
			$this->get('connection1')->delete('table_field', array('table_id' => $this->getTable($table)->id));
			$this->get('connection1')->delete('table_table', array('name' => $table));
		}
		return $this->get('connection1')->query('DROP TABLE '.$table);
	}

	public function truncateTable($table) {
		return $this->get('connection1')->query('DROP TRUNCATE '.$table);
	}
	
	public function backupDB($filename) {
		$cwd = getcwd();
		chdir(dirname($filename));
		system('mysqldump -u '.$GLOBALS['DB_USER']. ' -p'.$GLOBALS['DB_PASS'].' -h '.$GLOBALS['DB_HOST'].' '.$GLOBALS['DB_BASE'].' > '.basename($filename));
		chdir($cwd);
		return true;
	}
	
	public function getControllerClass($path) {
		list($vendor, $bundle, $name) = explode(':', $path);
		return $vendor.'\\'.$bundle.'Bundle\\Controller\\'.ucfirst($name).'Controller';
	}
	
	public function createController($path) {
		if (!isset($this->controllers[$path])) {
			$className = $this->getControllerClass($path);
			$this->controllers[$path] = new $className();
		}
		return $this->controllers[$path];
	}

	public function callAction($path, $params = array()) {
		list($vendor, $bundle, $name, $action) = explode(':', $path);
		$obj = new \ReflectionClass($this->getControllerClass($path));
		$action .= 'Action'; 	
		if (!$obj->hasMethod($action)) {
			return $this->get('util')->showError('Несуществующая ссылка '.$path);
		}
		return $obj->getMethod($action)->invoke($this->createController($path), $params);	
	}

	public function href($node = '/', $action = 'index', $params = array()) {
		if ($node == '/') {
			return $node;
		}
		$path = array('');
		if ('ru' != $this->get('router')->getParam('locale')) {
			$path[] = $this->get('router')->getParam('locale');
		}
		$path[] = $node;
		if ($action != 'index') {
			$path[] = $action;
		}
		if (count($params)){
			$path = array_merge($path, $params);
		}
		return implode('/', $path);
	}

	public function setVar($name, $value) {
		$this->templateVars[$name] = $value;
	}

	public function getVars() {
		return $this->templateVars;	
	}

	public function register($name, $service) {
		$this->services[$name] = $service;
		return $service;
	}

	public function get($name) {
		if (!isset($this->services[$name])) {
			switch ($name) {
				case 'log':
					$this->services[$name] = new Log\Log();
					break;
				case 'util':
					$this->services[$name] = new Util();
					break;
				case 'templating':
					$this->services[$name] = new Templating\SmartyTemplating();
					break;
				case 'connection1':
					$config = new \Doctrine\DBAL\Configuration();
					$connectionParams = array(
						'dbname'	=> $GLOBALS['DB_BASE'],
						'user'		=> $GLOBALS['DB_USER'],
						'password'	=> $GLOBALS['DB_PASS'],
						'host'		=> $GLOBALS['DB_HOST'],
						'driver'	=> 'pdo_mysql',
						'charset'	=> 'utf8'
					);
					$this->services[$name] = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
					$this->services[$name]->getDatabasePlatform()->registerDoctrineTypeMapping('DECIMAL(14,2)', 'money');
					break;
				case 'filestorage':
					$this->services[$name] = new Storage\FileStorage();
					break;
				case 'imagestorage':
					$this->services[$name] = new Storage\ImageStorageDecorator($this->get('filestorage'));
					break;
				case 'paginator':
					$this->services[$name] = new Paginator($this);
					break;
				case 'mailer':
					$this->services[$name] = new Mailer\Mailer();
					break;
				case 'scheduler':
					$this->services[$name] = new Scheduler\Scheduler();
					break;
				case 'search':
					$this->services[$name] = new Search\SearchEngine($this);
					break;
				case 'router':
					$this->services[$name] = new Router($this);
					break;
				case 'security':
					$this->services[$name] = new SecurityHandler($this);
					break;
				case 'cache':
					global $CACHE_DIR, $CACHE_TTL;
					$options = array(
						'cacheDir' => $CACHE_DIR,
						'lifeTime' => $CACHE_TTL,
						'pearErrorMode' => CACHE_ERROR_DIE
					);
					$this->services[$name] = new Cache\Cache($options);
					break;
			}	
		}
		if (!isset($this->services[$name])) {
			throw new \Exception('Cлужба "'.$name.'" отсутствует');
		}
		
		return $this->services[$name];
	}
	
	public function getManager($path) {
		if (!isset($this->managers[$path])) {
			list($vendor, $bundle, $name) = explode(':', $path);
			$className = $vendor.'\\'.$bundle.'Bundle\\Model\\'.ucfirst($name).'Manager';
			$this->managers[$path] = new $className();
		}

		return $this->managers[$path];
	}
	
	public function isXmlHttpRequest() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH'];
	}

}
