<?php

/*
$config['server_name'] = 'localhost';
$config['db_name'] = 'database';
$config['login'] = 'root';
$config['password'] = '';
$config['table_prefix'] = 'f_';
$config['charset'] = 'utf8';
*/

class Db_MySQL {

	protected $link;
	protected $config = array();
	protected $sql_times = 0;
	protected $prefix = '';
	protected $tmp = null;
	protected $debug = false;

	public function __construct($input = null) {

		if (!$input) throw new Db_MySQL_Exception(-1, "Input data is empty", 'mysql connect');

		if (is_array($input)) {
			$this->config['server_name'] = @$input['server_name'];
			$this->config['db_name'] = @$input['db_name'];
			$this->config['login'] = @$input['login'];
			$this->config['password'] = @$input['password'];
			$this->config['table_prefix'] = @$input['table_prefix'];
			$this->config['charset'] = @$input['charset'];
		}

		elseif (is_string($input)) {
			$input = preg_replace('/\0/s', '', $input);
			if (!file_exists($input)) throw new Db_MySQL_Exception(-1, "File is not exists: {$input}", 'mysql connect');
			@require_once($input);
			$this->config['server_name'] = @$config['server_name'];
			$this->config['db_name'] = @$config['db_name'];
			$this->config['login'] = @$config['login'];
			$this->config['password'] = @$config['password'];
			$this->config['table_prefix'] = @$config['table_prefix'];
			$this->config['charset'] = @$config['charset'];
		}

		else {
			throw new Db_MySQL_Exception(-1, "Wrong input data", 'mysql connect');
		}

		$this->connect();

	}
	protected function __clone() {}

	public function __destruct () {
		@$this->link->close();
	}

	protected function connect() {

		if (!preg_match("#^[a-zA-Z0-9\\-_]*$#uis", (string)$this->config['table_prefix']))
			throw new Db_MySQL_Exception(-1, "Wrong prefix of table: {$this->config['table_prefix']}", 'mysql connect');
		$this->prefix = $this->config['table_prefix'];

		$this->link = @new mysqli($this->config['server_name'], $this->config['login'], $this->config['password'], $this->config['db_name']);
		if ($this->link->connect_errno) {
			throw new Db_MySQL_Exception($this->link->connect_errno, $this->link->connect_error, 'mysql connect');
		}

		if (!@$this->link->set_charset($this->config['charset'])) {
			throw new Db_MySQL_Exception($this->link->errno, $this->link->error, 'mysql set charset');
		}

	}

	public function debug() {
		$this->debug = true;
	}

	public function query() {

		$args = func_get_args();
		if (count($args) == 0) throw new Db_MySQL_Exception(-1, "Empty args", 'mysql query');

		$query = (string)array_shift($args);
		if (!$query) throw new Db_MySQL_Exception(-1, "Empty query", 'mysql query');
		$query = ' ' . $query . ' ';

		if (defined('MYSQL_NO_CACHE')) {
			$query = preg_replace('#^(\s*SELECT )#uis', '$1 SQL_NO_CACHE ', $query);
		}

		$this->tmp = $args;
		$query = preg_replace_callback("/(\\?(_|a|da?|ia?|fa?|#|s|l|b|x)?)/uis", array(&$this, "makePlaceholders"), $query);
		$this->tmp = null;
		$query = trim($query);

		if ($this->debug) {
			$queryDebug = preg_replace("#^\s+#uim", "", $query);
			debug($queryDebug);
			$this->debug = false;
		}

		if (@!$this->link->ping()) {
			sleep(1);
			$this->connect();
		}

		$time = microtime(1);
		$res = @$this->link->query($query);
		$time = (microtime(1) - $time);
		$dbt = debug_backtrace();
		$query_log = str_replace(array("\n", "\r"), ' ', preg_replace('#^\s+#uim', '', $query));
		$query_time = sprintf('%05.2f', $time);

		if ($res === false) {
			throw new Db_MySQL_Exception($this->link->errno, $this->link->error, $query);
		}

		$this->sql_times++;

		if ($res === true) {
			if ($this->link->insert_id) {
				return $this->link->insert_id;
			} else {
				return $this->link->affected_rows;
			}
		}

		return new Db_MySQL_ResultSet($res);

	}

	public function queryPart() {

		$args = func_get_args();
		if (count($args) == 0) throw new Db_MySQL_Exception(-1, "Empty args", 'queryPart()');

		$query = (string)array_shift($args);
		if (!$query) throw new Db_MySQL_Exception(-1, "Empty query", 'queryPart()');
		$query = ' ' . $query . ' ';
		$this->tmp = $args;
		$query = preg_replace_callback("/(\\?(_|a|da?|ia?|fa?|#|s|l|b|x)?)/uis", array(&$this, "makePlaceholders"), $query);
		$this->tmp = null;
		$query = trim($query);

		return ' ' . $query . ' ';

	}

	protected function makePlaceholders($m) {
		$type = $m[1];
		$value = null;
		if ($type != '?_') {
			if (!$this->tmp) throw new Db_MySQL_Exception(-1, "Empty args", 'makePlaceholders()');
			$value = array_shift($this->tmp);
		}
		switch ($type) {
			case '?_':
				return $this->prefix;
			break;
			case '?':
				return "'".$this->escape($value)."'";
			break;
			case '?a':
				if (!is_array($value)) throw new Db_MySQL_Exception(-1, "Wrong placeholder value type ({$type})", 'makePlaceholders()');
				if (!$value) throw new Db_MySQL_Exception(-1, "Empty placeholder value ({$type})", 'makePlaceholders()');
				$res = array();
				foreach ($value as $key => $subvalue) {
					$subvalue = "'".$this->escape($subvalue)."'";
					if (is_string($key)) $subvalue = "`".$this->escape($key)."` = ".$subvalue;
					$res[] = $subvalue;
				}
				return implode(", ", $res);
			break;
			case '?d':
			case '?i':
				return intval($value);
			break;
			case '?da':
				if (!is_array($value)) throw new Db_MySQL_Exception(-1, "Wrong placeholder value type ({$type})", 'makePlaceholders()');
				if (!$value) throw new Db_MySQL_Exception(-1, "Empty placeholder value ({$type})", 'makePlaceholders()');
				$res = array();
				foreach ($value as $key => $subvalue) {
					$subvalue = intval($subvalue);
					if (is_string($key)) $subvalue = "`".$this->escape($key)."` = ".$subvalue;
					$res[] = $subvalue;
				}
				return implode(", ", $res);
			break;
			case '?ia':
				if (!is_array($value)) throw new Db_MySQL_Exception(-1, "Wrong placeholder value type ({$type})", 'makePlaceholders()');
				if (!$value) $value = array(0);
				$res = array();
				foreach ($value as $key => $subvalue) {
					$subvalue = intval($subvalue);
					if (is_string($key)) $subvalue = "`".$this->escape($key)."` = ".$subvalue;
					$res[] = $subvalue;
				}
				return implode(", ", $res);
			break;
			case '?f':
				return str_replace(',', '.', doubleval($value));
			break;
			case '?fa':
				if (!is_array($value)) throw new Db_MySQL_Exception(-1, "Wrong placeholder value type ({$type})", 'makePlaceholders()');
				if (!$value) throw new Db_MySQL_Exception(-1, "Empty placeholder value ({$type})", 'makePlaceholders()');
				$res = array();
				foreach ($value as $key => $subvalue) {
					$subvalue = str_replace(',', '.', doubleval($subvalue));
					if (is_string($key)) $subvalue = "`".$this->escape($key)."` = ".$subvalue;
					$res[] = $subvalue;
				}
				return implode(", ", $res);
			break;
			case '?b':
				return (bool)$value ? "'1'" : "'0'";
			break;
			case '?#':
				if (!$value) throw new Db_MySQL_Exception(-1, "Empty placeholder value ({$type})", 'makePlaceholders()');
				return '`'.$this->escape($value, true).'`';
			break;
			case '?s':
				return $this->escape($value);
			break;
			case '?l':
				$value = str_replace(array('%', '_'), array('\\%', '\\_'), $value);
				return $this->escape($value);
			break;
			case '?x':
				return $value;
			break;
		}
		throw new Db_MySQL_Exception(-1, "Wrong placeholder type: {$type}", 'makePlaceholders()');
	}

	protected function escape($str, $isIdent = false) {
		if (!$isIdent)
			return $this->link->real_escape_string((string)$str);
		else
			return str_replace('`', '``', (string)$str);
	}

	public function getSqlTimes() {return $this->sql_times;}

}

class Db_MySQL_ResultSet {

	protected $data = array();
	protected $num_rows = 0;

	public function __construct(&$res) {

		$this->num_rows = $res->num_rows;
		if ($this->num_rows) {
			while ($row = @$res->fetch_assoc()) {
				$this->data[] = $row;
			}
		}

		@$res->free();

	}
	private function __clone() {}

	public function getCell(&$cell, $x = 0, $y = '') {
		$cell = false;
		if (!$this->data) return $this;
		if ($x) {
			if ($y) {
				$cell = @$this->data[$x][$y];
			} else {
				if (@$this->data[$x]) {
					foreach ($this->data[$x] as $v) {
						$cell = $v;
						break;
					}
				}
			}
		} else {
			foreach ($this->data[0] as $v) {
				$cell = $v;
				break;
			}
		}
		return $this;
	}

	public function getRow(&$row, $params_str = "") {
		$row = array();
		if (!$this->data) return $this;
		parse_str($params_str, $params);
		$row = array_shift($this->data);
		self::params_del_prefix($row, @$params['del_prefix']);
		return $this;
	}

	public function getAllRows(&$data, $params_str = "") {
		$data = array();
		if (!$this->data) return $this;
		parse_str($params_str, $params);
		$data = $this->data;
		foreach ($data as &$row) {
			self::params_del_prefix($row, @$params['del_prefix']);
			if (count($row) == 1) {
				$row = array_shift($row);
			}
		}
		return $this;
	}

	public function getRowsNum(&$total_rows) {
		$total_rows = $this->num_rows;
		return $this;
	}

	private static function params_del_prefix(&$row, $prefix) {
		if (!$prefix || !is_array($row)) return;
		$newrow = array();
		foreach ($row as $field_name => &$value) {
			$newrow[preg_replace("#^" . preg_quote($prefix, '#') . "_(.*)$#uis", "$1", $field_name)] = $value;
		}
		$row = $newrow;
	}

}

class Db_MySQL_Exception extends FgException {
	protected $name = 'DB MySQL Exception';

	public function __construct($code, $text, $query) {
		$query = preg_replace("#^\s*|\s*$#uim", '', $query);

		$caller = self::getLastCaller();
		$file = @$caller['file'] ?: 'unknown file';
		$line = @$caller['line'] ?: 0;

		parent::__construct('[' . $code . '] ' . $text . "\n\n" . $query, $code, $file, $line);
	}

	protected function getLastCaller() {
		$trace = debug_backtrace();
		foreach ($trace as &$a) unset($a['object']);

		$seen = 0;
		$smart = array();
		for ($i = 0, $n = count($trace); $i < $n; $i++) {
			$t = $trace[$i];
			if (!$t) continue;

			$next = isset($trace[$i+1]) ? $trace[$i+1] : null;

			if (!isset($t['file'])) {
				$t['over_function'] = $trace[$i+1]['function'];
				$t = $t + $trace[$i+1];
				$trace[$i+1] = null;
			}

			if (++$seen < 2) continue;

			if ($next) {
				$caller = (isset($next['class']) ? $next['class'] . '::' : '') . (isset($next['function']) ? $next['function'] : '');
				if (preg_match("#^(Db_MySQL::.*|Db_MySQL_.*::.*|call_user_func.*|preg_replace_callback)$#uis", $caller)) continue;
			}

			return $t;
			$smart[] = $t;
		}

		return false;
	}

}
