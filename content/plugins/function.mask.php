<?php

function smarty_function_mask($params, &$smarty) {
	$var = (string)@$params['var'];
	$true = (string)@$params['true'];
	$false = (string)@$params['false'];
	if ($var) {
		return str_replace('%var%', $var, $true);
	} else {
		return str_replace('%var%', $var, $false);
	}
}
