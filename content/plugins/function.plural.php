<?php

function smarty_function_plural($params, &$smarty) {
	$n = (string)@$params['n'];
	$v1 = (string)@$params['v1'];
	$v2 = (string)@$params['v2'];
	$v3 = (string)@$params['v3'];
	return make_plural($n, $v1, $v2, $v3);
}
