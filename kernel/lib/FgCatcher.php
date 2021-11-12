<?php

final class FgCatcher {
	public function __construct() {
		$catcher = new FgCatcherInner();
		set_error_handler(array($catcher, 'errorHandler'));
		register_shutdown_function(array($catcher, 'fatalHandler'));
	}
	public function __destruct() {
		restore_error_handler();
	}
}

final class FgCatcherInner {
	public function errorHandler($errno, $errstr, $errfile, $errline) {
		if (!error_reporting()) return;
		if (!($errno & error_reporting())) return;
		$types = array(
			'E_ERROR', 'E_WARNING', 'E_PARSE', 'E_NOTICE',
			'E_CORE_ERROR', 'E_CORE_WARNING',
			'E_COMPILE_ERROR', 'E_COMPILE_WARNING',
			'E_USER_ERROR', 'E_USER_WARNING', 'E_USER_NOTICE',
			'E_STRICT',
			'E_RECOVERABLE_ERROR',
			'E_DEPRECATED', 'E_USER_DEPRECATED',
		);
		$className = 'E_EXCEPTION';
		foreach ($types as $t) {
			$e = constant($t);
			if ($errno & $e) {
				$className = $t;
				break;
			}
		}
		throw new $className($errstr, $errno, $errfile, $errline);
	}
	public function fatalHandler() {
		$error = error_get_last();
		if (!$error || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING))) return;

		print FgException::decorate('PHP Fatal Error', $error['message'], $error['file'], $error['line']);
		exit();
	}
}

class E_EXCEPTION extends FgException {protected $name = 'PHP Error Exception';}
class E_CORE_ERROR extends E_EXCEPTION {protected $name = 'PHP CORE_ERROR';}
	class E_CORE_WARNING extends E_CORE_ERROR {protected $name = 'PHP CORE_WARNING';}
	class E_COMPILE_ERROR extends E_CORE_ERROR {protected $name = 'PHP COMPILE_ERROR';}
		class E_COMPILE_WARNING extends E_COMPILE_ERROR {protected $name = 'PHP COMPILE_WARNING';}
	class E_ERROR extends E_CORE_ERROR {protected $name = 'PHP ERROR';}
		class E_RECOVERABLE_ERROR extends E_ERROR {protected $name = 'PHP RECOVERABLE_ERROR';}
			class E_PARSE extends E_RECOVERABLE_ERROR {protected $name = 'PHP PARSE';}
				class E_WARNING extends E_PARSE {protected $name = 'PHP WARNING';}
					class E_NOTICE extends E_WARNING {protected $name = 'PHP NOTICE';}
						class E_STRICT extends E_NOTICE {protected $name = 'PHP STRICT';}
							class E_DEPRECATED extends E_STRICT {protected $name = 'PHP DEPRECATED';}
		class E_USER_ERROR extends E_ERROR {protected $name = 'PHP USER_ERROR';}
			class E_USER_WARNING extends E_USER_ERROR {protected $name = 'PHP USER_WARNING';}
				class E_USER_NOTICE extends E_USER_WARNING {protected $name = 'PHP USER_NOTICE';}
					class E_USER_DEPRECATED extends E_USER_NOTICE {protected $name = 'PHP USER_DEPRECATED';}
