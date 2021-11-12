<?php

function smarty_function_breadcrumbs($params, &$smarty) {
	return Page::get()->breadcrumbs();
}
