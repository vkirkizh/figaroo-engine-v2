<?php

function smarty_function_random($params, &$smarty) {
	$params = array_values($params);
	return $params[mt_rand(0, count($params) - 1)];
}
