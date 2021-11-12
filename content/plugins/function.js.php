<?php

function smarty_function_js($params, &$smarty) {
	switch (@$params['type']) {
		case 'admin':
			Page::get()->addJS(Page::adminJS, @$params['file']);
		break;
		/*case 'kernel':
			Page::get()->addJS(Page::kernelJS, @$params['file']);
		break;*/
		default:
			Page::get()->addJS(Page::contentJS, @$params['file']);
		break;
	}
}
