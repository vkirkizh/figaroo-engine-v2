<?php

function smarty_block_repeat($params, $content, &$smarty) {
	if (is_null($content)) return;
	$n = (int)@$params['n'];
	if ($n <= 0) return '';
	$text = '';
	for ($i = 0; $i < $n; $i++) {
		$text .= $content;
	}
	return $text;
}
