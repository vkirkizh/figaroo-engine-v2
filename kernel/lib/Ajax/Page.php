<?php

abstract class Ajax_Page {
	public final function exec() {
		global $RESULT;
		$method = STR::toLower(preg_replace("#[^0-9A-Za-z_\\-]#uis", "", @$_REQUEST['act']));
		if ($method == 'exec' || !method_exists($this, $method)) die('Ajax undefined method.');
		echo call_user_func(array($this, $method));
	}
}
