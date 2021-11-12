<?php

class Controller_Index extends Figaroo_Controller {
	public function exec() {
		global $DB;

		$DB->query("SELECT * FROM `?_pages` WHERE `level` = 0 LIMIT 1")->getRow($page);
		if ($page) {
			Page::get()->setKeywords(unhtmlsec($page['keywords']));
			Page::get()->setDescription(unhtmlsec($page['description']));
			Page::get()->assign('page', $page);
		}

		Page::get()->assign('now', time());

	}
}
