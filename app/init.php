<?php

$LIB_VERSION = '5.0.1';
$LIB_DATE = '2012.09.10';

require_once 'config/config.php';

use Fuga\Component\Container;
use Fuga\Component\Cache;
use Fuga\Component\Log\Log;
use Fuga\Component\Util;
use Fuga\Component\Router;
use Fuga\Component\Templating;
use Fuga\CMSBundle\Security\SecurityHandler;
use Fuga\CMSBundle\Security\Controller\SecurityController;
use Fuga\CMSBundle\Controller\ExceptionController;

$se_mask = "/(Yandex|Googlebot|StackRambler|Yahoo Slurp|WebAlta|msnbot)/";
if (preg_match($se_mask,$_SERVER['HTTP_USER_AGENT']) > 0) {
	if (!empty($_GET[session_name()])) {
		header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
		exit();
	}
} else {
	session_start();
}

function exception_handler($exception) 
{	
	// TODO Подключить логирование
	if ($exception instanceof Fuga\Component\Exception\NotFoundHttpException) {
		$controller = new ExceptionController();
		echo $controller->indexAction($exception->getStatusCode(), $exception->getMessage());
	} else {
		// TODO Ругаться красиво, саму ошибку в лог пишем
		$controller = new ExceptionController();
		echo $controller->indexAction(500, $exception->getMessage());
	}
}

function autoloader($className)
{
	global $LIB_DIR, $PRJ_DIR;
	if ($className == 'Smarty') {
		require_once($PRJ_DIR.'/vendor/smarty/Smarty.class.php');
	} else {
		$basePath = $LIB_DIR.'/';
		$className = ltrim($className, '\\');
		$fileName  = '';
		$namespace = '';
		if ($lastNsPos = strripos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		if (file_exists($basePath.$fileName)) {
			require_once $basePath.$fileName;
		} else {
			// LOG + nice error text
			throw new \Exception('Не возможно загрузить класс "'.$fileName.'"');
		}
	}	
}

set_exception_handler('exception_handler');
spl_autoload_register('autoloader');

// ID запрашиваемой страницы
$GLOBALS['cur_page_id'] = preg_replace('/(\/|-|\.|:|\?|[|])/', '_', str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']));

// Кеш
//	$options = array(
//	    'cacheDir' => $PRJ_DIR.'/app/cache/',
//	    'lifeTime' => 24*60*14,
//	    'pearErrorMode' => CACHE_ERROR_DIE
//	);
//	$GLOBALS['cache'] = new Cache($options);
//	if ($data = $GLOBALS['cache']->get($GLOBALS['cur_page_id'])){
//		//echo $data;
//		//exit();
//	}

// Соединение с базой и выполнение запросов
try {
	$className = '\\Fuga\\Component\\DB\\Connector\\'.ucfirst($GLOBALS['DB_TYPE']).'Connector';
	$connection = new $className($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASS'], $GLOBALS['DB_BASE']);
} catch (\Exception $e) {
	throw new \Exception('DB connection type error (DB_TYPE). Possible value: mysql,mysqli. Check DB connection params');
}

$container = new Container();
$container->register('log', new Log());
$container->register('util', new Util());
$container->register('connection', $connection);

// инициализация переменных
$params = array();
$vars = $connection->getItems('GLOBAL_VARS', 'SELECT name, value FROM config_variables');
foreach ($vars as $var) {
	$params[strtolower($var['name'])] = $var['value'];
	$$var['name'] = $var['value'];
}

$params['theme_ref'] = $THEME_REF;

$container->register('smarty', new Smarty());
$container->get('smarty')->template_dir = $PRJ_DIR.'/app/Resources/views/';
$container->get('smarty')->compile_dir = $PRJ_DIR.'/app/cache/smarty/';
$container->get('smarty')->compile_check = true;
$container->get('smarty')->debugging = false;

$container->register(
	'templating', 
	new Templating(
		$container->get('smarty'), 
		array('assignMethod' => 'assign', 'renderMethod' => 'fetch'
)));

$container->get('templating')->setParams($params);

// Включаем Роутер запросов к сайту 
$container->register('router', new Router());

$security = new SecurityHandler();
if (!$security->isAuthenticated() && $security->isSecuredArea()) {
	$controller = new SecurityController();
	echo $controller->loginAction();
	exit;
} elseif (preg_match('/^\/admin\/(logout|forgot|password)/', $_SERVER['REQUEST_URI'], $matches)) {
	$controller = new SecurityController();
	$methodName = $matches[1].'Action';
	echo $controller->$methodName();
	exit;
}
$container->register('security', $security);
$container->initialize();

$container->get('router')->setLanguage();
$container->get('router')->setParams();

if ($_SERVER['SCRIPT_NAME'] != '/restore.php' && file_exists($PRJ_DIR.'/restore.php')) {
	throw new \Exception('Удалите файл restore.php в корне сайта');
}
