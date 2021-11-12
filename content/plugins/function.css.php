<?php

function smarty_function_css($params, &$smarty) {
	switch (@$params['type']) {
		case 'admin':
			Page::get()->addCSS(Page::adminCSS, @$params['file']);
		break;
		default:
			Page::get()->addCSS(Page::contentCSS, @$params['file']);
		break;
	}
}
