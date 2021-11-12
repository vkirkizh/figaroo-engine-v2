<?php

if (!(@$_SERVER['PHP_AUTH_USER'] == Settings::get()->main->login && @$_SERVER['PHP_AUTH_PW'] == Settings::get()->main->password)) {
	header("WWW-Authenticate: Basic realm=\"Restricted Area\"");
	header("HTTP/1.1 401 Unauthorized");
	die('Защищённый регион. Введите имя пользователя и пароль.');
}

class Ajax_Page_Admin extends Ajax_Page {
}
