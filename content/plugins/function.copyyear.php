<?php

function smarty_function_copyyear($params, &$smarty) {
	$year = (int)@$params['year'];
	$nol = (bool)@$params['nol'];
	$cur = date("Y");
	if ($year < 1900 || $year >= $cur) return $cur . (!$nol ? '&nbsp;г.' : '');
	return $year . '-' . $cur . (!$nol ? '&nbsp;гг.' : '');
}
