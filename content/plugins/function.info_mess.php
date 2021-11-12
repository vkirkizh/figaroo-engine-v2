<?php

function smarty_function_info_mess($params, &$smarty) {
	$text = @$params['text'];
	$type = @$params['type'];
	if (!in_array($type, array('error', 'some'))) return '';
	return '<div id="info_mess" class="' . $type . '-message" ' . (!$text ? 'style="display: none;"' : '') . '>' . $text . '</div>';
}
