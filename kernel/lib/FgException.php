<?php

class FgException extends Exception {
	protected $name = 'Figaroo Exception';
	public function __construct($e = null, $code = 0, $file = '', $line = 0) {
		if (is_object($e)) {
			$this->name = 'Unknown Exception';
			$class = get_class($e);
			if ($class && $class != 'Exception') {
				$this->name .= ': ' . $class;
			}
			$str = $e->getMessage();
			$file = $e->getFile();
			$line = $e->getLine();
			$code = $e->getCode();
		}
		else {
			$str = $e;
		}
		parent::__construct($str, $code);
		if ($file) $this->file = $file;
		if ($line) $this->line = $line;
	}
	public function getName() {
		return $this->name;
	}
	public function __toString() {
		return self::decorate($this->getName(), $this->getMessage(), $this->getFile(), $this->getLine());
	}
	public static function decorate($name, $text, $file, $line) {
		$src = ($file ? $file . ($line ? ', line ' . $line : '') : 'unknown file');

		if (!FIGAROO_DEBUGGING) {
			$url = !empty($_SERVER['GATEWAY_INTERFACE']) ? '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '';

			if (!FG_DEV_EMAIL) return '';

			$id = md5(FG_DEV_EMAIL . '::' . $name . '::' . $text . '::' . $src);

			$cnt = 1;
			$file = TMP_DIR . $id . '.error';
			$flag = false;
			if (file_exists($file) && filemtime($file) > time() - FG_SEND_ERRORS) {
				$cnt = file_get_contents($file) + 1;
				if (in_array($cnt, array(1, 5, 10, 50, 100, 500, 1000))) {
					$flag = true;
				}
			} else {
				$flag = true;
			}
			file_put_contents($file, $cnt);
			$min = ceil(FG_SEND_ERRORS / 60);

			if ($flag) {
				$sbj = '[ERROR] ' . $name . ', ' . $text . ' (' . $src . ')';

				$msg = join('', array(
					$name . "\n\n",
					($url ? $url . "\n\n" : ''),
					($cnt > 1 ? "[!!!] {$cnt} times last {$min} min." . "\n\n" : ''),
					$text . "\n\n",
					$src . "\n\n",
					'-> GET:' . "\n" . print_r($_GET, 1) . "\n",
					'-> POST:' . "\n" . print_r($_POST, 1) . "\n",
					'-> COOKIE:' . "\n" . print_r($_COOKIE, 1) . "\n",
					'-> SERVER:' . "\n" . print_r($_SERVER, 1) . "\n",
				));

				@mail(
					FG_DEV_EMAIL,
					'=?UTF-8?B?' . base64_encode($sbj) . '?=',
					$msg,
					join("\r\n", array(
						'MIME-Version: 1.0',
						'Content-type: text/plain; charset=UTF-8',
						'From: ' . FG_DEV_EMAIL,
						'Message-Id: ' . $id . '@fg_error',
					))
				);
			}

			return '';
		}

		while (ob_get_level() > 1) ob_end_clean();

		header('Content-Type: text/plain; charset=UTF-8');
		header('Content-Encoding: none');

		return join("\n\n", array(
			'=== ' . $name . ' ===',
			$text,
			$src,
		)) . "\n\n";
	}
}
