<?php
/* index.php */
// define location of index.php as BASEDIR constant for easier manipulation with files
defined('BASEDIR') || define("BASEDIR", dirname(__FILE__));

// shortening constant from Directories - PHP file system related extensions included in core PHP
defined('DIRSEP') || define("DIRSEP", DIRECTORY_SEPARATOR);

// autoload classes through autoload function
spl_autoload_register(function($class) {
    // convert namespace to full file path
    $class = BASEDIR . DIRSEP . str_replace('\\', DIRSEP, $class) . '.php';
    try {
        // check if file exist
        if (!file_exists($class)) {
            throw new RuntimeException($class . ' file does not exist.');
        } else {
            require_once($class);
        }
    } catch(\Exception $e) {
        // handle the exception and stack trace
        echo nl2br('Error message: ' . PHP_EOL . $e->getMessage() . PHP_EOL);
        echo nl2br('Stack trace:' . PHP_EOL . $e->getTraceAsString() . PHP_EOL);
    }
});

$nonExistingClass = new FirstController();
