<?php
namespace AntarianMinitaurus\Router;

class RouterConfig
{
	const MODRW_ENBL = 'enabled';
	const MODRW_DIS = 'disabled';

	/**
	 * safer call of methods and functions
	 *
	 * @param $callback
	 * @param array $params
	 * @return mixed
	 */
	public static function safeCallUserFuncArray($callback, $params = array())
	{
		if (empty($params))
			$params = array();

		// check if callback is valid
		if (!is_callable($callback))
			throw new \InvalidArgumentException("Callback param is invalid");

		// check required parameters
		if (is_array($callback))
			$r = new \ReflectionMethod($callback[0], $callback[1]);
		else
			$r = new \ReflectionFunction($callback);

		if ($r->getNumberOfRequiredParameters() > count($params))
			throw new \InvalidArgumentException("Missing required parameter(s)");

		return call_user_func_array($callback, $params);
	}
}