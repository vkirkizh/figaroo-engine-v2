<?php

class Config extends ConfigSection {
	protected $_changedvars = array();
	protected $_file;

	public function __construct($file) {
		$this->_file = $file;
		$vars = @readIniFile($file);
		if ($vars === false) throw new Config_ConstructException("Error while reading file: {$file}");
		foreach ($vars as $key => &$item) {
			if (is_array($item)) {
				$this->set($key, new ConfigSection($this, $key));
				foreach ($item as $key2 => &$value) {
					$this->$key->set($key2, $value);
				}
			} else {
				$this->set($key, $item);
			}
		}
	}

	protected function change($name) {
		$this->_changedvars[] = $name;
	}

	public function __destruct() {
		if ($this->_changedvars) {
			debug($this->_changedvars);
			writeIniFile($this->_file, $this->toArray());
		}
	}
}

class ConfigSection {
	protected $_vars = array();
	protected $_parent, $_key;

	protected function __construct($parent = null, $key) {
		$this->_parent = $parent;
		$this->_key = $key;
	}

	protected function change($name) {
		if ($this->_parent) $this->_parent->change($this->_key . '::' . $name);
	}

	protected function toArray() {
		$vars = array();
		foreach ($this->_vars as $key => &$item) {
			if (is_object($item)) {
				$vars[$key] = $item->toArray();
			} else {
				$vars[$key] = $item;
			}
		}
		return $vars;
	}

	public function __get($name) {
		if ($name{0} == '_') throw new ConfigSection_Exception("Getting value of private property: {$name}");
		return isset($this->_vars[$name]) ? $this->_vars[$name] : null;
	}

	public function __set($name, $value) {
		if ($name{0} == '_') throw new ConfigSection_Exception("Setting value of private property: {$name}");
		if (!isset($this->_vars[$name]) || $this->_vars[$name] != $value) $this->change($name);
		$this->_vars[$name] = $value;
	}

	protected function set($name, $value) {
		if ($name{0} == '_') throw new ConfigSection_Exception("Setting value of private property: {$name}");
		$this->_vars[$name] = $value;
	}

}

abstract class Config_Exception extends FgException {protected $name = 'Config Exception';}
	class Config_ConstructException extends Config_Exception {protected $name = 'Config Construct Exception';}
	class ConfigSection_Exception extends Config_Exception {protected $name = 'ConfigSection Exception';}
