<?php

class Controller__Error404 extends Figaroo_Controller {
	public function exec() {
		Page::get()->clearNavigation();
		Page::get()->addNavigation('Error 404: Not found');
		httpStatus('404 Not Found');
	}
}
