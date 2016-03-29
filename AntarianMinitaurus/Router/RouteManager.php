<?php
namespace AntarianMinitaurus\Router;

use AntarianMinitaurus\Debug\Debug;
use AntarianMinitaurus\Debug\DebugConfig;

class RouteManager
{
	/**
	 * creates error page with message if in development
	 *
	 * @param $uri
	 * @param $code
	 * @param $message
	 */
	public static function errorPage($uri, $code, $message)
	{
		if ($code == '404') {
			header("Location: http://" . $uri);
			header('HTTP/1.0 404 Not Found');
			echo "<h1>404 Not Found</h1>";
			echo "<p>".$message."</p>";
			exit();
		}
	}

	/**
	 * Convert nice URL to call target controller and method in specified module
	 *
	 * @param $module
	 * @param $controller
	 * @param $action
	 * @param $params
	 */
	public static function mvcProcess($module, $controller, $action, $params)
	{
		// must be fully qualified namespace with class for dynamic names, because of PHP compile order
		// php.net/manual/en/language.namespaces.dynamic.php
		$controller = ucfirst($module) . "\\Controller\\" . ucfirst($controller) . "Controller";

		// create method name from second parameter
		$action = $action . "Action";

		// explode params to array
		if (!empty($params))
			$params = explode('/', $params);

		// instantiate object and call defined method
		$controllerObject = new $controller();

		try
		{
			// call method ($action) in class ($controllerObject) with selected parameters ($params)
			RouterConfig::safeCallUserFuncArray(array($controllerObject, $action), $params);

		} catch(\ErrorException $e)
		{
			// if dev environment display error on screen
			if (APPLICATION_ENV == DebugConfig::DEV_ENV)
				print(Debug::generateCallTrace($e));
			else
				// handle the error
				RouteManager::errorPage($_SERVER['REQUEST_URI'], 404, '');
		}
	}

	/**
	 * Convert classic URL to call target controller and method in specified module
	 *
	 * @param $module
	 * @param $controller
	 * @param $action
	 * @param $params
	 */
	public static function mvcProcessOld($module, $controller, $action, $params)
	{
		// must be fully qualified namespace with class for dynamic names, because of PHP compile order
		// php.net/manual/en/language.namespaces.dynamic.php
		$module = ucfirst( str_replace("?module=", "", $module) );
		$controller = ucfirst( str_replace("&controller=", "", $controller) );
		$controller = $module . "\\Controller\\" . $controller . "Controller";

		// create method name from second parameter
		$action = str_replace("&action=", "", $action);
		$action = $action . "Action";

		// explode params to array
		if (!empty($params))
		{
			$params = explode('&', $params);
			unset($params[0]);
			$params = array_map(function($item){
				if (!empty($item))
					return substr($item, strpos($item, '=')+1);
			}, $params);
		}

		// instantiate object and call defined method
		$controllerObject = new $controller();

		try
		{
			// call method ($action) in class ($controllerObject) with selected parameters ($params)
			RouterConfig::safeCallUserFuncArray(array($controllerObject, $action), $params);

		} catch(\ErrorException $e)
		{
			// if dev environment display error on screen
			if (APPLICATION_ENV == DebugConfig::DEV_ENV)
				print(Debug::generateCallTrace($e));
			else
				// handle the error
				RouteManager::errorPage($_SERVER['REQUEST_URI'], 404, '');
		}
	}
}