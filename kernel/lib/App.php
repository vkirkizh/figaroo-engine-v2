<?php

class App {

	protected $time_exec = -1;
	protected $referer = '';
	protected $session_name;
	protected $session_id;
	protected $session_key;
	protected $urls = array();
	protected $lang;

	protected $vpath = array();
	protected $vpathStr = "";

	public function time_exec() {return $this->time_exec;}
	public function set_time_exec($time) {$this->time_exec = $time;}
	public function referer() {return $this->referer;}
	public function session_name() {return $this->session_name;}
	public function session_id() {return $this->session_id;}
	public function session_key() {return $this->session_key;}

	public function jsUrl() {return $this->urls['js'];}
	public function cacheUrl() {return $this->urls['cache'];}
	public function tmplUrl() {return $this->urls['tmpl'];}
	public function tmplImgUrl() {return $this->urls['tmpl_img'];}
	public function tmplCssUrl() {return $this->urls['tmpl_css'];}
	public function tmplJsUrl() {return $this->urls['tmpl_js'];}

	public function vpathFirst() {return @$this->vpath[0];}
	public function vpathSecond() {return @$this->vpath[1];}
	public function vpathThird() {return @$this->vpath[2];}
	public function vpathGet($num) {return @$this->vpath[$num - 1];}
	public function vpathAll() {return $this->vpath;}
	public function vpathStr() {return $this->vpathStr;}
	public function current() {return URL . $this->vpathStr . '/';}

	protected static $instance = null;
	public static function &get() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	private function __clone() {}
	protected function __construct() {

		$this->referer = @$_SESSION['HTTP_REFERER'];
		$this->urls['js'] = URL . "js/";
		$this->urls['cache'] = URL . "cache/";
		$this->urls['tmpl']    = CONTENT_URL;
		$this->urls['tmpl_img'] = CONTENT_URL . "img/";
		$this->urls['tmpl_css'] = CONTENT_URL . "css/";
		$this->urls['tmpl_js']  = CONTENT_URL . "js/";

		if (!in_array(SERVER_NAME, Settings::get()->main->domains))
			redirect(PROTOCOL . '://' . Settings::get()->main->domains[0] . $_SERVER['REQUEST_URI'], 301);

		if (!defined('FIGAROO_SCRIPTING')) {
			$this->vpath = preg_replace("#^" . preg_quote(URL, "#") . "#uis", "", SERVER_URL . urldecode($_SERVER['REQUEST_URI']));
			if (preg_match("#.*[^/]$#uis", $this->vpath) && STR::strpos($_SERVER['REQUEST_URI'], '?') === false) redirect("/{$this->vpath}/", 301);
			$this->vpath = preg_replace("#(/?)(\?.*)?$#uis", "", $this->vpath);
			$this->vpath = preg_replace("#[^0-9A-Za-z._\\-/]#uis", "", $this->vpath);
			$this->vpath = explode("/", $this->vpath);
			$this->vpathStr = implode("/", $this->vpath);
		}

		session_name(COOKIE_PREF . 'session_id');
		$this->session_name = session_name();
		if (isset($_REQUEST[$this->session_name]) && (!is_string($_REQUEST[$this->session_name]) || !@$_REQUEST[$this->session_name])) die();
		session_start();
		$this->session_id = session_id();

		$this->session_key = htmlsec(COOKIE::get('session_key'));
		$this->session_key = $this->session_key ? $this->session_key : getRndStr(32);
		COOKIE::set('session_key', $this->session_key);

	}

	public function sessionCheck() {
		return $this->session_key === trim(htmlsec((string)@$_REQUEST['session_key']));
	}

	public function redirect($url = '/', $code = 200) {
		return redirect($url, $code);
	}
	public function refresh() {
		return redirect('/' . ($this->vpathStr ? $this->vpathStr . '/' : ''), 200);
	}

}
