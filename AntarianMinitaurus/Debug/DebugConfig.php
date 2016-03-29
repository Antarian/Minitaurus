<?php
namespace AntarianMinitaurus\Debug;

/**
 * basic debug configuration class
 */
class DebugConfig
{
	const DEV_ENV = 'development';
	const PROD_ENV = 'production';

	public static function load()
	{
		// in development enable error report for better error detection
		if (APPLICATION_ENV == self::DEV_ENV)
		{
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(-1);
		}

		// setup exception error handler to change errors to exceptions
		set_error_handler(array('\AntarianMinitaurus\Debug\Debug', 'exceptionErrorHandler'));

		// exception handler to uncaught exceptions
		set_exception_handler(array('\AntarianMinitaurus\Debug\Debug', 'exceptionHandler'));
	}
}