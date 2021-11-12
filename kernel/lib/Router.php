<?php

class Router {

	protected static $instance = null;
	public static function &get() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	private function __clone() {}
	protected function __construct() {}

	public function pages($paths) {
		$first = App::get()->vpathFirst();
		if ($first == 'admin') {
			define('FIGAROO_ADMIN', true);
			Page::get()->mode('admin');
			Page::get()->exec('admin');
		}
		elseif (!$first) {
			define('INDEX_PAGE', true);
			Page::get()->assign('INDEX_PAGE', true);
			Page::get()->mode('index');
			Page::get()->exec('index');
		}
		elseif (in_array($first, $paths)) {
			Page::get()->mode('page');
			Page::get()->exec($first);
		}
		else {
			Page::get()->mode('page');
			Page::get()->exec('page');
		}
	}

	public function ajax($paths) {
		define('AJAX', true);
		Page::get()->assign('AJAX', AJAX);
		$request = (string)@$_REQUEST['ajax'];
		if (!in_array($request, $paths)) die();
		require_once(CTRLS_DIR . 'ajax/' . $request . '.php');
		$ctrl = "Ajax_Page_{$request}";
		$Ajax = new $ctrl();
		$Ajax->exec();
	}

}
