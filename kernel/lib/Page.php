<?php

class Page {
	protected $keywords = '';
	protected $description = '';
	protected $navigation;
	protected $page_num = false;
	protected $http_error = 0;
	protected $mode = '';
	protected $ctrl = null;
	protected $tpl = null;
	protected $indexing = true;
	protected $cacheID = '';
	protected $Smarty;
	protected $exec_dir = '';
	protected $nojscss = false;

	const contentCSS = 1, adminCSS = 2;
	const kernelJS = 0, contentJS = 1, adminJS = 2;

	protected $arrayCSS = array(), $arrayCSShash = array();
	protected $arrayJS = array(), $arrayJShash = array();

	protected static $instance = null;
	public static function &get() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	private function __clone() {}

	protected function __construct() {
		$this->clearNavigation();
		define('SMARTY_DIR', KERNEL_DIR . 'smarty/');
		require_once(SMARTY_DIR . 'Smarty.class.php');
		$this->Smarty = new Figaroo_Smarty;
		autoGzip();
		$this->Smarty->register_function('start_javascript', array(&$this, 'smartyStartJavascript'), false);
		$this->Smarty->register_outputfilter(array(&$this, 'insertJSCSS'));
	}

	public function setNoJsCss() {
		$this->nojscss = true;
	}

	protected function addJSCSS() {
		$this->addCSS(self::contentCSS, 'common.css');

		switch ($this->mode) {
			case 'admin':
				$this->addCSS(self::contentCSS, 'jquery-ui.css');
				$this->addCSS(self::contentCSS, 'admin.css');
			break;
			default:
				$this->addCSS(self::contentCSS, 'main.css');
			break;
		}
		$this->addCSS(self::contentCSS, 'jquery.*.css');

		$this->addJS(self::kernelJS, 'application.js');

		$this->addJS(self::kernelJS, 'jquery.js');
		$this->addJS(self::kernelJS, 'jquery.*.js');

		$this->addJS(self::kernelJS, 'datetime.js');
		$this->addJS(self::kernelJS, 'common.js');

		switch ($this->mode) {
			case 'admin':
				$this->addJS(self::kernelJS, 'jquery-ui.js');
				$this->addJS(self::contentJS, 'admin.js');
			break;
			default:
				$this->addJS(self::contentJS, 'main.js');
			break;
		}

		$this->addJS(self::kernelJS, 'JsHttpRequest.js');
	}

	public function mode($mode = null) {
		if ($mode !== null) {
			$this->mode = in_array($mode, array('admin', 'index')) ? $mode : 'page';
		}
		return $this->mode;
	}
	public function isIndex() {
		return $this->mode == 'index';
	}

	public function exec($tpl) {
		$tpl = preg_replace("#[^a-z0-9_]+#uis", "", $tpl);
		$this->tpl = $this->exec_dir . $tpl;
		$this->exec_dir .= $tpl . '/';
		$this->assign('PAGE_TEMPLATE', TPLS_DIR . $this->tpl . '.tpl');
		if (file_exists(CTRLS_DIR . $this->tpl . '.php')) {
			include_once(CTRLS_DIR . $this->tpl . '.php');
			$ctrl = 'Controller_' . str_replace('/', '_', $this->tpl);
			if (!class_exists($ctrl, false))
				throw new Page_ExecException("Class not exists: {$ctrl}");
			$this->ctrl = new $ctrl();
			$args = func_get_args();
			array_shift($args);
			call_user_func_array(array(&$this->ctrl, 'exec'), $args);
		}
	}

	public function display() {
		$cacheID = $this->tpl . ($this->cacheID ? '|' . $this->cacheID : '');
		$this->addJSCSS();
		switch ($this->mode) {
			case 'admin':
				$this->Smarty->display('admin.tpl', $cacheID, 'admin');
			break;
			default:
				$this->Smarty->display('main.tpl', $cacheID, 'content');
			break;
		}
	}

	public function fetch($tpl) {
		$cacheID = $tpl . ($this->cacheID ? '|' . $this->cacheID : '');
		switch ($this->mode) {
			case 'admin':
				return $this->Smarty->fetch($tpl, $cacheID, 'admin');
			break;
			default:
				return $this->Smarty->fetch($tpl, $cacheID, 'content');
			break;
		}
	}

	public function keywords() {return $this->keywords;}
	public function description() {return $this->description;}
	public function setKeywords($text = null) {
		$this->keywords = htmlsec($text !== null ? str_replace(array("\n", "\r", "\t"), ' ', $text) : '');
	}
	public function setDescription($text = null) {
		$this->description = htmlsec($text !== null ? str_replace(array("\n", "\r", "\t"), ' ', $text) : '');
	}
	public function lastModified($time = null) {
		if ($time == null)
			header('Last-Modified: ' . gmdate('r') . ' GMT');
		elseif (preg_match("#^[0-9]+$#uis", $time))
			header('Last-Modified: ' . gmdate('r', $time) . ' GMT');
		else
			header('Last-Modified: ' . gmdate('r', dateAndTime($time)) . ' GMT');
		if (@$_SERVER['HTTP_IF_MODIFIED_SINCE']) {
			$if_modified_since = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
			if ($if_modified_since && $if_modified_since >= $time) {
				header('HTTP/1.1 304 Not Modified');
				die();
			}
		}
	}

	public function getIndexing() {
		return $this->indexing;
	}
	public function allowIndexing() {
		$this->indexing = true;
	}
	public function disallowIndexing() {
		$this->indexing = false;
	}

	public function isCached($cacheID = '') {
		$cacheID = self::checkCacheID($cacheID);
		if (!$cacheID) $cacheID = $this->cacheID;
		$cacheID = $this->tpl . ($cacheID ? '|' . $cacheID : '');
		switch ($this->mode) {
			case 'admin':
				return $this->Smarty->is_cached('admin.tpl', $cacheID, 'admin');
			break;
			default:
				return $this->Smarty->is_cached('main.tpl', $cacheID, 'content');
			break;
		}
	}
	public function clearCache($cacheID, $mode) {
		$cacheID = self::checkCacheID($cacheID);
		switch ($mode) {
			case 'admin':
				return $this->Smarty->clear_cache('admin.tpl', $cacheID, 'admin');
			break;
			default:
				return $this->Smarty->clear_cache('main.tpl', $cacheID, 'content');
			break;
		}
	}
	public function cacheOff() {
		$this->Smarty->caching = false;
	}
	public function setCacheID($cacheID) {
		$this->cacheID = self::checkCacheID($cacheID);
	}
	public function getCacheID() {
		return $this->cacheID;
	}
	private static function checkCacheID($cacheID) {
		return str_replace('/', '|', preg_replace("#[^a-z0-9\-/]+#uis", "", STR::toLower($cacheID)));
	}

	public function assign($name, $value) {
		$this->Smarty->assign($name, $value);
	}
	public function assignRef($name, &$value) {
		$this->Smarty->assign_by_ref($name, $value);
	}

	public function httpError($errNum = null) {
		if ($errNum !== null) {
			$this->http_error = (int)$errNum;
			$this->mode = 'error';
			$this->exec_dir = '';
			$this->exec('_error' . $this->http_error);
		}
		return $this->http_error;
	}

	public function vpathStr() {
		$url = array(); foreach ($this->navigation as $item) if ($item['url']) $url[] = $item['url'];
		return URL . ( $url ? join('/', $url) . '/' : '') . ($this->page_num !== false ? $this->page_num . '/' : '');
	}

	public function setTitle($text) {
		$this->title = $text;
	}
	public function title() {
		global $Brand;
		if ($this->title) return $this->title;
		$title = array();
		for ($i = count($this->navigation) - 1; $i >= 0; $i--) {
			$item = &$this->navigation[$i];
			$text = str_replace('|', '¦', htmlsec($item['name']));
			if ($this->page_num && !$i) {
				$text .= ' ' . str_replace('{N}', $this->page_num, htmlsec(Settings::get()->design->title_page_num));
			}
			$title[] = $text;
		}
		$title = join(' &laquo; ', $title);
		return ($title ? $title . ' | ' : '') . htmlsec($Brand ? str_replace("%BRAND%", $Brand['name'], Settings::get()->main->subtitle) : (!defined('AUTOGROUPBY') ? Settings::get()->main->title : Settings::get()->main->title2));
	}
	public function breadcrumbs() {
		if ($this->isIndex())
			return Settings::get()->breadcrumbs->text ? htmlsec(Settings::get()->breadcrumbs->text) : '';
		$breadcrumbs = '';
		$url = '';
		$last = count($this->navigation) - 1;
		foreach ($this->navigation as $i => &$item) {
			$breadcrumbs .= '&nbsp;&raquo; ';
			$name = str_replace(' ', '&nbsp;', htmlsec($item['name']));
			if ($item['url'] === null || $i == $last) {
				$breadcrumbs .= $name;
			} else {
				$url .= $item['url'] . '/';
				$breadcrumbs .= '<a href="' . URL . $url . '">' . $name . '</a>';
			}
		}
		if ($this->page_num) {
			$breadcrumbs .= ' ' . str_replace(array('{N}', ' ', '—'), array($this->page_num, '&nbsp;', '&mdash;'), htmlsec(Settings::get()->design->title_page_num));
		}
		return '<a href="' . URL . '">' . htmlsec(Settings::get()->breadcrumbs->link) . '</a>' . $breadcrumbs;
	}
	public function addNavigation($name, $url = null) {
		$this->navigation[] = array('name' => $name, 'url' => $url);
	}
	public function clearNavigation() {
		$this->navigation = array();
	}
	public function addPageNum($page, $total_pages) {
		$page = abs(intval($page));
		if ($page > 0 && $page != $total_pages) $this->page_num = $page;
	}

	public function insertJSCSS($output, &$smarty) {
		if ($this->nojscss) return $output;
		$output = str_replace('<FIGAROO:CSS>', $this->insertCSS() . "\n", $output);
		$output = str_replace('<FIGAROO:HEAD_JS>', '<script src="' . URL . 'js/datetime.js" type="text/javascript"></script>' . "\n", $output);
		$output = str_replace('<FIGAROO:INSERT_JS>', $this->insertJS() . "\n", $output);
		$output = preg_replace("#\n+#uis", "\n", $output);
		return $output;
	}

	public static function smartyStartJavascript($params, &$smarty) {
		$text  = "<script type=\"text/javascript\">\n";
		$text .= "var URL = '" . URL . "',\n";
		$text .= " tmplUrl = '" . App::get()->tmplUrl() . "',\n";
		$text .= " current_year = '" . date('Y') . "',\n";
		$text .= " fg_now = new Date(),\n";
		$text .= " session_name = '" . App::get()->session_name() . "',\n";
		$text .= " session_id = '" . App::get()->session_id() . "',\n";
		$text .= " session_key = '" . App::get()->session_key() . "';\n";
		$text .= "</script>\n";
		return $text;
	}

	public function addCSS($type, $file) {
		if (!in_array($type . '::' . $file, $this->arrayCSShash)) {
			$this->arrayCSS[] = array('file' => $file, 'type' => $type);
			$this->arrayCSShash[] = $type . '::' . $file;
		}
	}

	public function addJS($type, $file) {
		if (!in_array($type . '::' . $file, $this->arrayJShash)) {
			$this->arrayJS[] = array('file' => $file, 'type' => $type);
			$this->arrayJShash[] = $type . '::' . $file;
		}
	}

	public function insertCSS() {
		foreach ($this->arrayCSS as &$item) {
			switch ($item['type']) {
				case self::contentCSS:
					$prefix = CONTENT_DIR . 'css/';
				break;
				case self::adminCSS:
					$prefix = ADMIN_DIR . 'css/';
				break;
				default:
					trigger_error('wrong CSS file type' . print_r($this->arrayCSS, 1));
					unset($item);
				break;
			}
			$item = (array)glob($prefix.$item['file']);
		}
		if (FIGAROO_DEVMODE) {
			$res = array();
			foreach ($this->arrayCSS as &$item) {
				foreach ($item as &$file) {
					$path = preg_replace('#^' . preg_quote(DIR, '#') . '#uis', '', $file);
					$upath = str_replace('/', '--', $path);
					$version = filemtime($file);
					$dir = CACHE_DIR . 'static/' . $upath;
					$url = CACHE_URL . 'static/' . preg_replace('#\\.css$#uis', '.v' . $version . '.css', $upath);
					$content = file_get_contents($file);
					file_put_contents($dir, self::cssResolvePaths($file, $content));
					$res[] = '<link rel="stylesheet" href="' . $url . '" type="text/css" media="all" />';
				}
			}
			return join("\n", $res);
		}
		$names = array();
		foreach ($this->arrayCSS as &$mask) {
			if (!is_array($mask)) return;
			$names[] = join('::', $mask);
		}
		$name = md5(join('::', $names));
		$filesmtime = 0;
		foreach ($this->arrayCSS as &$mask) {
			foreach ($mask as &$file) {
				$mtime = filemtime($file);
				if ($mtime > $filesmtime) $filesmtime = $mtime;
			}
		}
		$version = abs(crc32($filesmtime));
		$dir = CACHE_DIR.'static/'.$name.'.css';
		$url = CACHE_URL.'static/'.$name.'.v'.$version.'.css';
		if (FIGAROO_DEBUGGING || @!file_exists($dir) || filemtime($dir) < $filesmtime) {
			$fp = fopen($dir, "w+t");
			foreach ($this->arrayCSS as &$mask) {
				foreach ($mask as &$file) {
					fwrite($fp, self::cssGetContents($file) . "\n");
				}
			}
			@fclose($fp);
		}
		return '<link rel="stylesheet" href="' . $url . '" type="text/css" media="all" />';
	}

	protected static function cssGetContents($src, $processed = array()) {
		$content = @file_get_contents($src);
		$processed[] = realpath($src);
		$content = preg_replace("#\r\?n\r?#uis", "\n", $content);
		$content = preg_replace("#/\*.*?\*/#uis", "", $content);
		$content = preg_replace("#\s+#uis", " ", $content);
		$content = preg_replace("#} ?#uis", "}\n", $content);
		$content = preg_replace("#(:|,|;) #uis", "$1", $content);
		$content = preg_replace("#^\s*|\s*$#uim", "", $content);
		$content = preg_replace("# ?{ ?#uis", "{", $content);
		$content = preg_replace("#;? ?}#uis", "}", $content);
		$content = preg_replace("#([ :])(0)(?:px|pt|em|cm|mm|%)#uis", "$1$2", $content);
		$content = preg_replace("#\s+#uis", " ", $content);
		$content = self::cssResolvePaths($src, $content);
		preg_match_all('/@import\s*(?:url)?\s*\(?([^;]*?)\)?;/ui', $content, $m, PREG_SET_ORDER);
		if (is_array($m)) {
			foreach ($m as $item) {
				list($found, $url) = $item;
				if (!isset($url)) continue;
				$file = trim($url, '\'" ');
				if (preg_match("#^[a-zA-Z]+://#uis", $file)) continue;
				if ($file && file_exists(ROOT_DIR.$file) && !in_array(realpath(ROOT_DIR.$file), $processed)) {
					$content = str_replace($found, self::cssGetContents(ROOT_DIR.$file, $processed) . "\n", $content);
				} else {
					$content = str_replace($found, "", $content);
				}
			}
		}
		return $content;
	}

	protected static function cssResolvePaths($src, $content) {
		$src = dirname($src).'/';
		preg_match_all("#url\s*\(([^;]+?)\)#uis", $content, $m, PREG_SET_ORDER);
		if (is_array($m)) {
			foreach ($m as $item) {
				list($found, $url) = $item;
				$file = false;
				if (!isset($url)) continue;
				$file = trim($url, '\'" ');
				if (preg_match("#^[a-zA-Z]+://#uis", $file)) continue;
				if ($file && $file{0} != '/') {
					$file = realpath($src.$file);
					if ($file) {
						$file = preg_replace("#^".preg_quote(realpath(DIR))."#uis", "", $file);
						$file = URL.substr(str_replace("\\", "/", $file), 1);
						$file = preg_replace("#^".preg_quote(SERVER_URL)."#uis", "", $file);
					}
				}
				if ($file && file_exists(ROOT_DIR.$file)) {
					$content = str_replace($found, "url({$file})", $content);
				} else {
					$content = str_replace($found, 'url()', $content);
				}
			}
		}
		return $content;
	}

	public function insertJS() {
		foreach ($this->arrayJS as &$item) {
			switch ($item['type']) {
				case self::kernelJS:
					$prefix = DIR . 'js/';
				break;
				case self::contentJS:
					$prefix = CONTENT_DIR . 'js/';
				break;
				case self::adminJS:
					$prefix = ADMIN_DIR . 'js/';
				break;
				default:
					trigger_error('wrong JS file type');
					unset($item);
				break;
			}
			$item = (array)glob($prefix.$item['file']);
		}
		if (FIGAROO_DEVMODE) {
			$res = array();
			foreach ($this->arrayJS as &$item) {
				foreach ($item as &$file) {
					$path = preg_replace('#^' . preg_quote(DIR, '#') . '#uis', '', $file);
					$upath = str_replace('/', '--', $path);
					$version = filemtime($file);
					$dir = CACHE_DIR . 'static/' . $upath;
					$url = CACHE_URL . 'static/' . preg_replace('#\\.js$#uis', '.v' . $version . '.js', $upath);
					file_put_contents($dir, file_get_contents($file));
					$res[] = '<script src="' . $url . '" type="text/javascript"></script>';
				}
			}
			return join("\n", $res);
		}

		$names = array();
		foreach ($this->arrayJS as &$mask) {
			if (!is_array($mask)) return;
			$names[] = join('::', $mask);
		}
		$name = md5(join('::', $names));
		$filesmtime = 0;
		foreach ($this->arrayJS as &$mask) {
			foreach ($mask as &$file) {
				$mtime = filemtime($file);
				if ($mtime > $filesmtime) $filesmtime = $mtime;
			}
		}
		$version = abs(crc32($filesmtime));
		$dir = CACHE_DIR . 'static/' . $name . '.js';
		$url = CACHE_URL . 'static/' . $name . '.v' . $version . '.js';
		if (FIGAROO_DEBUGGING || @!file_exists($dir) || filemtime($dir) < $filesmtime) {
			$fp = fopen($dir, "w+t");
			foreach ($this->arrayJS as &$mask) {
				foreach ($mask as &$file) {
					fwrite($fp, self::jsGetContents($file) . "\n");
				}
			}
			@fclose($fp);
		}
		return '<script src="' . $url . '" type="text/javascript"></script>';
	}

	protected static function jsGetContents($src) {
		$content = @file_get_contents($src);
		$content = preg_replace("#\r\?n\r?#uis", "\n", $content);
		$content = preg_replace("#^/\*.*?\*/#uis", "", $content);
		$content = preg_replace("#^//.*#uim", "", $content);
		return $content;
	}
}

abstract class Page_Exception extends FgException {protected $name = 'Page Exception';}
	class Page_ExecException extends Page_Exception {protected $name = 'Page Exec Exception';}
