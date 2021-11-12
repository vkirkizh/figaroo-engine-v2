<?php

function smarty_function_sitemenu($params, &$smarty) {
	global $DB;
	$class = (string)@$params['class'];
	if (!$class) $class = 'menu';
	$place = (string)@$params['place'];
	if (!in_array($place, array('top', 'side'))) $place = '';
	$place_sql = $place ? " `place` = '{$place}' " : " 1 ";
	$adminmode = (bool)@$params['admin'];
	$menu = array();
	$level2 = array();
	$DB->query("SELECT * FROM `?_pages` WHERE (`level` = '0' OR `level` = '1') AND {$place_sql} ORDER BY `order` ASC, `title` ASC")->getAllRows($pages1);
	$DB->query("SELECT * FROM `?_pages` WHERE `level` = '2' AND {$place_sql} ORDER BY `order` ASC, `title` ASC")->getAllRows($pages2);
	$DB->query("SELECT * FROM `?_pages` WHERE `level` = '3' AND {$place_sql} ORDER BY `order` ASC, `title` ASC")->getAllRows($pages3);
	foreach ($pages2 as &$page) {
		$level2[$page['id']] = $page;
	}
	foreach ($pages3 as &$page) {
		$level2[$page['parent']]['pages'][] = $page;
	}
	foreach ($pages1 as &$page) {
		$menu[$page['id']] = $page;
	}
	foreach ($level2 as &$page) {
		$menu[$page['parent']]['pages'][] = $page;
	}
	if (!$adminmode) {
		echo '<ul class="' . $class . '">' . "\n";
		foreach ($menu as &$item) {
			echo '<li><a href="' . URL . ($item['url'] ? $item['url'] . '/' : '') . '">' . $item['link'] . '</a></li>' . "\n";
			if (@$item['pages'] && App::get()->vpathFirst() == $item['index']) {
				echo '<ul>' . "\n";
				foreach ($item['pages'] as &$item2) {
					echo '<li><a href="' . URL . $item2['url'] . '/">' . $item2['link'] . '</a></li>' . "\n";
					if (@$item2['pages'] && App::get()->vpathSecond() == $item2['index']) {
						echo '<ul>' . "\n";
						foreach ($item2['pages'] as &$item3) {
							echo '<li><a href="' . URL . $item3['url'] . '/">' . $item3['link'] . '</a></li>' . "\n";
						}
						echo '</ul>' . "\n";
					}
				}
				echo '</ul>' . "\n";
			}
		}
		echo '</ul>' . "\n";
	}
	else {
		echo '<ul class="' . $class . '">' . "\n";
		foreach ($menu as &$item) {
			echo '<li><a href="' . URL . 'admin/' . ($item['url'] ? 'pages/' . $item['url'] : 'mainpage') . '/">' . $item['link'] . '</a></li>' . "\n";
			if (@$item['pages'] && App::get()->vpathThird() == $item['index']) {
				echo '<ul>' . "\n";
				foreach ($item['pages'] as &$item2) {
					echo '<li><a href="' . URL . 'admin/pages/' . $item2['url'] . '/">' . $item2['link'] . '</a></li>' . "\n";
					if (@$item2['pages'] && App::get()->vpathGet(4) == $item2['index']) {
						echo '<ul>' . "\n";
						foreach ($item2['pages'] as &$item3) {
							echo '<li><a href="' . URL . 'admin/pages/' . $item3['url'] . '/">' . $item3['link'] . '</a></li>' . "\n";
						}
						echo '</ul>' . "\n";
					}
				}
				echo '</ul>' . "\n";
			}
		}
		echo '</ul>' . "\n";
	}
}
