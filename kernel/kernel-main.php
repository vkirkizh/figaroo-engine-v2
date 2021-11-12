<?php

define('FIGAROO', true);

define('FIGAROO_VERSION', 'v.2.2');

define('TIME_START', microtime(true));
set_time_limit(30);

if (FIGAROO_DEBUGGING) {
	ini_set('display_errors', true);
} else {
	ini_set('display_errors', false);
}

ini_set('log_errors', PHP_LOG_ERRORS);

error_reporting(ERROR_REPORTING);

ob_start();

setlocale(LC_ALL, '');
date_default_timezone_set('Europe/Moscow');

if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

define('DIR', dirname(dirname(__FILE__)) . '/');

define('KERNEL_DIR', DIR . 'kernel/');

define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/');

define('CACHE_DIR', DIR . 'cache/');

define('CONTENT_DIR', DIR . 'content/');

define('CTRLS_DIR', CONTENT_DIR . 'controllers/');

define('TPLS_DIR', CONTENT_DIR . 'templates/');

define('CONFIG_DIR', KERNEL_DIR . 'configs/');

define('ADMIN_DIR', CONTENT_DIR);

define('TMP_DIR', KERNEL_DIR . 'tmp/');

define('LOG_DIR', KERNEL_DIR . 'logs/');

define('SEPARATOR', defined('COMSPEC') ? ';' : ':');

ini_set('include_path', DIR . SEPARATOR . ini_get('include_path'));

define('SERVER_NAME', $_SERVER['SERVER_NAME']);

define('PROTOCOL', (@$_SERVER['HTTPS'] || @$_SERVER['HTTP_X_HTTPS'] || @$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? 'https' : 'http');

define('SERVER_URL', PROTOCOL . '://' . $_SERVER['SERVER_NAME']);

define('URL', SERVER_URL . preg_replace("#^(.*)/(.*?)\.php$#uis", "$1/", $_SERVER['SCRIPT_NAME']));

define('CACHE_URL', URL . 'cache/');

define('CONTENT_URL', URL . 'content/');

define('COOKIE_URL', preg_replace("#^https?://(?:[^/]+?)/(.*)$#uis", "/$1", URL));

define('REQUEST_METHOD', @$_SERVER['REQUEST_METHOD']);

if (REQUEST_METHOD != 'GET' && REQUEST_METHOD != 'POST') exit();

function load_class($class_name) {
	if (class_exists($class_name, false)) return;
	$class_name = str_replace('_', '/', $class_name);
	$class_name = str_replace('\\', '/', $class_name);
    require_once(KERNEL_DIR . 'lib/' . $class_name . '.php');
}

spl_autoload_register(function($class_name) {
	return load_class($class_name);
});

load_class('FUNC');
load_class('STR');

Settings::get();

define('COOKIE_PREF', Settings::get()->main->cookiePref);

function script_work_time() {
	return microtime(true) - TIME_START;
}
