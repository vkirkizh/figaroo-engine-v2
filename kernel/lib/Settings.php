<?php

class Settings {
	protected static $instance = null;
	public static function &get() {
		if (self::$instance === null) {
			self::$instance = new Config(CONFIG_DIR . 'settings.ini');
		}
		return self::$instance;
	}
	private function __clone() {}
	protected function __construct() {}
}
