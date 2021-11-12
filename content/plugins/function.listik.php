<?php

function smarty_function_listik($params, &$smarty) {
	$data = (array)@$params['data'];
	$item = (string)@$params['item'];
	$delim = (string)@$params['delim'];
	$pre = (string)@$params['pre'];
	$post = (string)@$params['post'];
	$empty = (string)@$params['empty'];
	$begin = (string)@$params['begin'];
	$end = (string)@$params['end'];
	if (!$data) return $empty;
	$text = array();
	foreach ($data as $elem) {
		$text[] = $pre . ($item ? $elem[$item] : $elem) . $post;
	}
	$text = join($delim, $text);
	return $begin . $text . $end;
}
