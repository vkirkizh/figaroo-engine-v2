<?php

function smarty_block_form($params, $content, &$smarty) {
	if (is_null($content)) return;
	$onsubmit = (string)@$params['onsubmit'];
	$action = (string)@$params['action'];
	$path = App::get()->vpathStr();
	if (!$action) $action = URL . ($path ? $path . '/' : '');
	$name = (string)@$params['name'];
	if (!$name) $name = 'frm';
	$method = STR::toLower((string)@$params['method']);
	if ($method != 'get') $method = 'post';
	$text  = '<form action="' . $action . '" ' . ($method == "get" ? 'method="get"' : 'method="post" enctype="multipart/form-data"') . ' id="' . $name . '" name="' . $name . '" onsubmit="' . $onsubmit . '">' . "\n";
	if ($method == 'post') $text .= '<input type="hidden" name="session_key" id="session_key" value="' . App::get()->session_key() . '" />' . "\n";
	$text .= $content . "\n";
	$text .= '</form>' . "\n";
	return $text;
}
