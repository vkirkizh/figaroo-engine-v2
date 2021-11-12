<?php

class COOKIE {
	private function __construct() {}
	public static function set($name, $value, $time = 31536000) {
		setcookie(COOKIE_PREF . $name, $value, time() + $time, COOKIE_URL);
		$_COOKIE[COOKIE_PREF . $name] = $value;
	}
	public static function get($name) {
		$value = isset($_COOKIE[COOKIE_PREF . $name]) ? (string)$_COOKIE[COOKIE_PREF . $name] : false;
		$value = preg_replace('/\0/uis', '', $value);
		return $value;
	}
	public static function delete($name) {
		setcookie(COOKIE_PREF . $name, '', 0, COOKIE_URL);
		unset($_COOKIE[COOKIE_PREF . $name]);
	}
}
