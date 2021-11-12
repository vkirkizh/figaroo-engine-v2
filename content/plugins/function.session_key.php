<?php

function smarty_function_session_key($params, &$smarty) {
	return App::get()->session_key();
}
