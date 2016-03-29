<?php
use AntarianMinitaurus\Debug\DebugConfig;
use AntarianMinitaurus\Debug\Debug;
use AntarianMinitaurus\Router\RouterConfig;
use AntarianMinitaurus\Router\RegexRouter;
use AntarianMinitaurus\Router\RouteManager;

// define location of index.php as BASEDIR constant for easier manipulation with files
defined('BASEDIR') || define("BASEDIR", dirname(__FILE__));

// for using directory separator you must have installed Directories - PHP file system related extensions
// if you don't have it installed, replace DIRECTORY_SEPARATOR by "/" for Linux or "\\" for Windows
defined('DIRSEP') || define("DIRSEP", DIRECTORY_SEPARATOR);

// autoload classes through autoload function
spl_autoload_register( function($class)
{
	// convert namespace to full file path
	$class = BASEDIR . DIRSEP . str_replace('\\', DIRSEP, $class) . '.php';
	try
	{
		// check if file exist
		if (!file_exists($class))
		{
			throw new RuntimeException($class . ' file does not exist.');
		} else
		{
			require_once($class);
		}
	} catch(\Exception $e)
	{
		// if dev environment display error on screen
		if (APPLICATION_ENV == DebugConfig::DEV_ENV)
			print(Debug::generateCallTrace($e));

	}
});

// define application environment constant value from .htaccess or production otherwise
defined('APPLICATION_ENV') || define('APPLICATION_ENV',
(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : DebugConfig::PROD_ENV));
// load debug settings
DebugConfig::load();

// define if application use apache mod_rewrite
$mod_rewrite = (getenv('APPLICATION_REWRITE') ? true : false);
$mod_rewrite = (!$mod_rewrite && in_array('mod_rewrite', apache_get_modules()) ? true : false);
defined('APPLICATION_REWRITE') || define('APPLICATION_REWRITE',
($mod_rewrite ? RouterConfig::MODRW_ENBL : RouterConfig::MODRW_DIS));

// create regex router
$router = new RegexRouter();

// define some routes - order is important (FIFO method)

// routes with mod_rewrite enabled

// for every route of type /module/controller/action/ and everything after is /param_name/param_value/param...
$router->route('/^\/(\w+)\/(\w+)\/(\w+)\/?(.*)\/?$/', function ($module, $controller, $action, $params)
{
	RouteManager::mvcProcess($module, $controller, $action, $params);
});
// route for homepage
$router->route('/^\/$/', function ()
{
	RouteManager::mvcProcess("main", "main", "homepage", null);
});

// routes with mod_rewrite disabled

// for every route of type ?module=&controller=&action= and everything after is &param_name=...
$router->route('/^\/?index\.php(\?module\=[^&]+)(\&controller\=[^&]+)(\&action\=[^&]+)(([\&]{1}[^=]+\=[^&]+)*)$/', function ($module, $controller, $action, $params)
{
	RouteManager::mvcProcessOld($module, $controller, $action, $params);
});
// route for homepage
$router->route('/^(\/?index\.php)*$/', function ()
{
	RouteManager::mvcProcessOld("main", "main", "homepage", null);
});

// for every other route return 404
$router->route('/(.*)/', function()  { RouteManager::errorPage($_SERVER['REQUEST_URI'], 404, 'Page not found.'); });

// execute router with uri as parameter
$router->execute($_SERVER['REQUEST_URI']);