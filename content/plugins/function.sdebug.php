<?php

function smarty_function_sdebug($params, &$smarty) {
	foreach ($params as &$var) {
		debug($var);
	}
}
