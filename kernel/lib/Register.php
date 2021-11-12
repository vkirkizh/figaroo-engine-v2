<?php

class Register {
	protected $_vars;

	protected static $instance = null;
	public static function &get() {
		if (self::$instance === null) {
			self::$instance = new Register();
		}
		return self::$instance;
	}
	private function __clone() {}

	protected function __construct() {
		global $DB;
		$DB->query("SELECT `index`, `value` FROM `?_values` WHERE 1")->getAllRows($rows);
		$this->_vars = array();
		foreach ($rows as &$row) {
			$this->_vars[$row['index']] = $row['value'];
		}
	}

	public function __get($name) {
		if ($name{0} == '_') return null;
		return isset($this->_vars[$name]) ? $this->_vars[$name] : null;
	}

}
