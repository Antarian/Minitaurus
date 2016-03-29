<?php
namespace AntarianMinitaurus\Router;

class RegexRouter
{
	private $routes = array();

	/**
	 * create routes, array of pattern -> callback pairs for routing
	 *
	 * @param $pattern
	 * @param $callback
	 */
	public function route($pattern, $callback)
	{
		$this->routes[$pattern] = $callback;
	}

	/**
	 * executes first matched route in array
	 *
	 * @param $uri
	 * @return mixed
	 */
	public function execute($uri)
	{
		foreach ($this->routes as $pattern => $callback)
		{
			// get route pattern, check it with uri
			if (preg_match($pattern, $uri, $params) === 1)
			{
				// call callback defined in route with selected parameters
				array_shift($params);
				return RouterConfig::safeCallUserFuncArray($callback, array_values($params));
			}
		}
	}
}