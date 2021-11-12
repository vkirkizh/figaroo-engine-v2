<?php

class Figaroo_Smarty extends Smarty {
	public function __construct() {

		$this->debugging = false;
		$this->debugging_ctrl = FIGAROO_DEBUGGING;
		$this->error_reporting = E_ALL & ~E_NOTICE;

		$this->template_dir = TPLS_DIR;
		$this->compile_dir = CACHE_DIR . 'smarty/compiled';
		$this->config_dir = CONTENT_DIR . 'configs';
		$this->cache_dir = CACHE_DIR . 'smarty/cached';
		$this->plugins_dir = array(SMARTY_DIR . 'plugins', CONTENT_DIR . 'plugins');

		$this->caching = Settings::get()->templates->caching;
		$this->cache_lifetime = Settings::get()->templates->cacheLifetime;
		$this->compile_check = Settings::get()->templates->compileCheck;

		$this->security = false;
		$this->secure_dir = array(CONTENT_DIR);
		$this->trusted_dir = array(TPLS_DIR);
		$this->security_settings['PHP_HANDLING'] = false;
		$this->security_settings['IF_FUNCS'] = array();
		$this->security_settings['INCLUDE_ANY'] = false;
		$this->security_settings['PHP_TAGS'] = false;
		$this->security_settings['MODIFIER_FUNCS'] = array();
		$this->security_settings['ALLOW_CONSTANTS'] = false;
		$this->php_handling = SMARTY_PHP_QUOTE;

		$this->use_sub_dirs = true;

		$this->register_block('dynamic', array('Figaroo_Smarty', 'smartyDynamicBlock'), false);

	}

	public static function smartyDynamicBlock($param, $content, &$smarty) {
		return $content;
	}

}
