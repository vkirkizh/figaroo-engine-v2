<?php

class Controller__Error403 extends Figaroo_Controller {
	public function exec() {
		Page::get()->clearNavigation();
		Page::get()->addNavigation('Error 403: Forbidden');
		httpStatus('403 Forbidden');
	}
}
