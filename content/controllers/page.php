<?php

class Controller_Page extends Figaroo_Controller {
	public function exec() {
		global $DB;
		$index = App::get()->vpathFirst();
		$DB->query("SELECT * FROM `?_pages` WHERE `url` = ? AND `level` = '1' LIMIT 1", $index)->getRow($page);
		if (!$page) return Page::get()->httpError(404);
		Page::get()->addNavigation(unhtmlsec($page['title']), $page['index']);
		Page::get()->setKeywords(unhtmlsec($page['keywords']));
		Page::get()->setDescription(unhtmlsec($page['description']));
		Page::get()->assign('page', $page);
	}
}
