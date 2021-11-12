<?php

class Logger {
	protected static $loggers = array();
	protected $filename;
	protected $records = array();

	protected function __construct($logger) {
		$this->filename = $logger;
	}

	public static function get($logger) {
		if (!$logger) throw new Logger_GetException("Empty logger name");
		if (!preg_match("#^[0-9a-zA-Z][0-9a-zA-Z\\-_]+$#uis", (string)$logger)) throw new Logger_GetException("Wrong logger name: {$logger}");
		if (!isset(self::$loggers[$logger])) self::$loggers[$logger] = new Logger($logger);
		return self::$loggers[$logger];
	}

	public function log($text) {
		$this->records[] = date('d.m.Y H:i:s').' | '.$text."\n";
	}

	public function __destruct() {
		$fp = fopen(LOG_DIR . $this->filename . '.log', 'a+t');
		@flock($fp, LOCK_EX);
		if (!$fp) die();
		$content = '';
		while (!feof($fp)) $content .= fgets($fp);
		ftruncate($fp, 0);
		fseek($fp, 0, SEEK_SET);
		foreach ($this->records as $record) {
			fputs($fp, $record);
		}
		fputs($fp, $content);
		fclose($fp);
	}

	private function __clone() {}

}

abstract class Logger_Exception extends FgException {protected $name = 'Logger Exception';}
	class Logger_GetException extends Logger_Exception {protected $name = 'Logger Get Exception';}
