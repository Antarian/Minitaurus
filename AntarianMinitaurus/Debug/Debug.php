<?php
namespace AntarianMinitaurus\Debug;

class Debug
{
	/**
	 * method to change error to exception, for better use in try/catch
	 *
	 * @param $severity
	 * @param $message
	 * @param $file
	 * @param $line
	 * @throws \ErrorException
	 */
	public static function exceptionErrorHandler($severity, $message, $file, $line)
	{
		if (!(error_reporting() & $severity))
		{
			// This error code is not included in error_reporting
			return;
		}
		throw new \ErrorException($message, 0, $severity, $file, $line);
	}

	/**
	 * handler for uncaught exceptions
	 *
	 * @param $exception \Exception
	 */
	public static function exceptionHandler(\Exception $exception)
	{
		// if dev environment display error on screen
		if (APPLICATION_ENV == DebugConfig::DEV_ENV)
			print(Debug::generateCallTrace($exception));
	}

	/**
	 * adding more description to exceptions
	 *
	 * @param \Exception $e
	 * @return string
	 */
	public static function generateCallTrace(\Exception $e)
	{
		$result[] =
			"Msg: " . $e->getMessage() . PHP_EOL .
			"File: " . $e->getFile() . " (" . $e->getLine() . ")" . PHP_EOL .
			"Backtrace: ";

		$result[] = print_r($e->getTraceAsString(), true);

		$result = implode(PHP_EOL, $result);

		// not development, then output goes to log file
		if (APPLICATION_ENV != DebugConfig::DEV_ENV)
			return $result;

		// html output to screen
		return nl2br($result);
	}
}