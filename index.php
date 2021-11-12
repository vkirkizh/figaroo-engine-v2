<?php

/*
	Дух машины, мой бубен сильнее твоей тупости.
*/

require_once('./kernel/configs/system.php');
require_once('./kernel/kernel-main.php');
require_once('./vendor/autoload.php');

$CATCHER = new FgCatcher();

header('Content-Type: text/html; charset=UTF-8');

try {

	App::get();

	$DB = new Db_MySQL(KERNEL_DIR . 'configs/connect.php');

	Register::get();

	Page::get()->assign('DEBUGGING', FIGAROO_DEBUGGING);
	Page::get()->assign('DEVMODE', FIGAROO_DEVMODE);
	Page::get()->assign('URL', URL);
	Page::get()->assign('PROTOCOL', PROTOCOL);
	Page::get()->assignRef('PAGE', Page::get());
	Page::get()->assignRef('APP', App::get());
	Page::get()->assignRef('CONFIG', Settings::get());
	Page::get()->assignRef('REGISTER', Register::get());

	if (@$_REQUEST['ajax']) {

		if (@$_REQUEST['JsHttpRequest']) {
			define('FG_JS_HTTP_REQUEST', true);
			$JsHttpRequest = new JsHttpRequest("utf-8");
		}

		if (!App::get()->sessionCheck()) die('Ajax access denied.');

		Router::get()->ajax(array(
			'main',
			'admin',
		));

	}
	else {

		if (REQUEST_METHOD == 'POST' && !App::get()->sessionCheck()) die('Access denied.');

		Router::get()->pages(array(
			'test1',
		));

		Page::get()->assign('POSTED', array_function('htmlsec', $_POST));
		Page::get()->assign('SESSION', $_SESSION);

		App::get()->set_time_exec(script_work_time());

		Page::get()->display();

	}
}
catch (FgException $e) {
	print $e;
}
catch (Exception $e) {
	$e = new FgException($e);
	print $e;
}
