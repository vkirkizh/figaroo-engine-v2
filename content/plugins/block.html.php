<?php

function smarty_block_html($params, $content, &$smarty) {
	if (is_null($content)) return;
	$text = '';
	$text .= '<!DOCTYPE html>' . "\n";
	$text .= '<html>' . "\n";
	$text .= $content;
	$text .= '</html>' . "\n";
	return $text;
}
