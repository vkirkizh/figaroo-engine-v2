<?php

function smarty_function_pagination($params, &$smarty) {
	$pages = (string)@$params['pages'];
	$text = (string)@$params['text'];
	if (!$pages) return '';
	return '<div class="pagination"><div class="text">' . $text .'</div>' . $pages . '<div class="fixed"></div></div>' . "\n";
}
