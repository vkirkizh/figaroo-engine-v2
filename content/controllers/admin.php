<?php

class Controller_Admin extends Figaroo_Controller {
	public function exec() {
		global $DB;
		Page::get()->addNavigation('Панель управления', 'admin');
		Page::get()->cacheOff();

		if (!(@$_SERVER['PHP_AUTH_USER'] == Settings::get()->main->login && @$_SERVER['PHP_AUTH_PW'] == Settings::get()->main->password)) {
			header("WWW-Authenticate: Basic realm=\"Restricted Area\"");
			header("HTTP/1.1 401 Unauthorized");
			die('Защищённый регион. Введите имя пользователя и пароль.');
		}

		$c = App::get()->vpathSecond();
		switch ($c) {
			case '':
				Page::get()->exec('index');
			break;
			case '':
				Page::get()->exec($c);
			break;
			default:
				return Page::get()->httpError(404);
			break;
		}
	}
}
