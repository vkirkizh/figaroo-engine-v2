<?php

class FUNC {}

function autoGzip() {
	if (1
		&& gzipEnabled()
		&& function_exists('ob_gzhandler')
		&& ob_start('ob_gzhandler')
	) {
		header('Vary: Nogzip');
		return true;
	}
	return false;
}

function gzipEnabled() {
	if (!preg_match('#gzip|deflate#uis', @$_SERVER['HTTP_ACCEPT_ENCODING'])) return false;

	$ua = @$_SERVER['HTTP_USER_AGENT'];
	if ($ua && (0
		|| preg_match('#MSIE [4-6](?:.(?!Opera|SV1))+#uis', $ua)
		|| preg_match('#Chrome/2|Konqueror|Firefox/(?:[0-2]\.|3\.0)#uis', $ua)
	)) return false;

	if (@$_SERVER['HTTP_NOGZIP']) return false;

	return true;
}

function httpCache($allow, $time = 3600) {
	if ($allow) {
		header('Expires: '.gmdate('D, d M Y H:i:s', time() + $time).' GMT');
		header('Cache-Control: private,max-age='.$time.',pre-check='.$time.'');
	}
	else {
		header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: no-store,no-cache,must-revalidate,post-check=0,pre-check=0,max-age=0');
		header('Pragma: no-cache');
	}
}

function makeSeed() {
	list($usec, $sec) = explode(' ', microtime());
	$rnd = intval($sec + $usec * 1000000) ^ SRAND_KEY;
	if (function_exists('getmypid')) $rnd += getmypid();
	elseif (function_exists('posix_getpid')) $rnd += posix_getpid();
	return $rnd;
}

function array_function($func, $array) {
	if (!is_array($array)) return call_user_func($func, $array);
	foreach ($array as $k => $v) {
		if (is_array($v)) $array[$k] = array_function($func, $v);
		else $array[$k] = call_user_func($func, $v);
	}
	return $array;
}

function readIniFile($file) {
	return parse_ini_file($file, true);
}

function writeIniFile($file, $content) {
	if (!is_writable($file) || !is_array($content)) return false;
	$text1 = $text2 = '';
	foreach ($content as $key => &$item) {
		if (is_array($item)) {
			$text2 .= '[' . $key . ']' . "\n";
			foreach ($item as $key2 => &$value) {
				$text2 .= $key2 . ' = ';
				if (is_bool($value) || is_numeric($value) || ctype_xdigit($value)) $text2 .= $value;
				else  $text2 .= '"'.addslashes($value).'"';
				$text2 .= "\n";
			}
			$text2 .= "\n";
		} else {
			$text1 .= $key . ' = ';
			if (is_bool($item) || is_numeric($item) || ctype_xdigit($item)) $text1 .= $item;
			else  $text1 .= '"'.addslashes($item).'"';
			$text1 .= "\n\n";
		}
	}
	file_put_contents($file, $text1 . $text2);
	return true;
}

function fileDownload($filename, $params) {
	if (file_exists($filename)) {
		$mimetype = @$params['mimetype'] ? @$params['mimetype'] : 'application/octet-stream';
		$name = @$params['name'] ? @$params['name'] : basename($filename);
		header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
		header('Content-Type: ' . $mimetype);
		header('Last-Modified: ' . gmdate('r', filemtime($filename)));
		header('ETag: ' . md5(file_get_contents($filename)));
		header('Content-Length: ' . (filesize($filename)));
		header('Connection: close');
		header('Content-Disposition: attachment; filename="' . $name . '";');
		$fp = fopen($filename, 'r');
		while (!feof($fp)) {
			echo fread($fp, 1024);
			flush();
		}
		fclose($fp);
	}
	else {
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		header('Status: 404 Not Found');
		die();
	}
}

function byPageOut($page, $total_pages, $url, $get = array()) {
	if ($total_pages <= 1) return '';
	$url1 =  URL . $url . '/';
	$url2 = '/' . ($get ? '?' .  http_build_query($get) : '');
	if ($page != 1) $pervpage = '<div class="page first"><a href="' . $url1 . '1' . $url2 . '">&#171;</a></div>' . '<div class="page prev"><a href="' . $url1 . ($page - 1) . $url2 . '">&#8249;</a></div>';
	else $pervpage = '<div class="page first"><span>&#171;</span></div>' . '<div class="page prev"><span>&#8249;</span></div>';
	if ($page == 2) $pervpage = '<div class="page first"><a href="' . $url1 . '1' . $url2 . '">&#171;</a></div>' . '<div class="page prev"><span>&#8249;</span></div>';
	if ($page != $total_pages && $total_pages != 0) $nextpage = '<div class="page next"><a href="' . $url1 . ($page + 1) . $url2 . '">&#8250;</a></div>' . '<div class="page last"><a href="' . $url1 . $total_pages . $url2 . '">&#187;</a></div>';
	else $nextpage = '<div class="page next"><span>&#8250;</span></div>' . '<div class="page last"><span>&#187;</span></div>';
	if ($page == $total_pages - 1) $nextpage = ' <div class="page next"><span>&#8250;</span></div>' . '<div class="page last"><a href="' . $url1 . $total_pages . $url2 . '">&#187;</a></div>';
	if($page - 2 > 0) $page2left = '<div class="page"><a href="' . $url1 . ($page - 2) . $url2 . '">' . ($page - 2) . '</a></div>';
	else $page2left = '';
	if($page - 1 > 0) $page1left = '<div class="page"><a href="' . $url1 . ($page - 1) . $url2 . '">' . ($page - 1) . '</a></div>';
	else $page1left = '';
	if($page + 2 <= $total_pages) $page2right = '<div class="page"><a href="' . $url1 . ($page + 2) . $url2 . '">' . ($page + 2) . '</a></div>';
	else $page2right = '';
	if($page + 1 <= $total_pages) $page1right = '<div class="page"><a href="' . $url1 . ($page + 1) . $url2 . '">' . ($page + 1) . '</a></div>';
	else $page1right = '';
	return $pervpage . $page2left . $page1left . '<div class="page current"><span>' . $page . '</span></div>' . $page1right . $page2right . $nextpage;
}

function redirect($url = '/', $code = 200) {
	if (!preg_match("#^[a-z]+://#uis", $url)) {
		$url = URL . preg_replace("/^\//uis", "", $url);
	}
	switch ($code) {
		case 301:
			httpStatus('301 Moved Permanently');
		break;
		case 302:
			httpStatus('302 Found');
		break;
		default:
			httpStatus('200 OK');
		break;
	}
	@header("Location: ".$url);
	die('<html><head><meta name="refresh" content="0; url=' . $url . '"><script>location.href="' . $url . '";</script></head><body><a href="' . $url . '">' . $url . '</a></body></html>');
}

function httpStatus($status) {
	header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status);
	header('Status: ' . $status);
}

function debug() {
	$args = func_get_args();
	if (!FIGAROO_DEBUGGING) return;
	if (!count($args)) return;
	foreach ($args as $n => $var) {
		$var = htmlsec(print_r($var, true));
		echo "<pre>".($var ? $var : ' ')."</pre>\n";
	}
}
function ddebug() {
	$p = func_get_args();
	if (!FIGAROO_DEBUGGING) return;
	call_user_func_array('debug', $p);
	die();
}

function hdebug() {
	$args = func_get_args();
	if (!FIGAROO_DEBUGGING) return;
	if (!count($args)) return;
	foreach ($args as $n => $var) {
		echo print_r($var, true) . "\n\n";
	}
}
function hddebug() {
	$p = func_get_args();
	if (!FIGAROO_DEBUGGING) return;
	call_user_func_array('hdebug', $p);
	die();
}

function writeTime($time, $format = false) {
	if ($format === false) $format = Settings::get()->time->common;
	$time = !is_numeric($time) ? strtotime($time) : $time;
	$text = date($format, $time) . ' ' . date('T');
	$time = time() - $time;
	return '<span class="fg-time" data-time="' . $time . '" data-format="' . htmlsec($format) . '">' . $text . '</span>';
}

function writeDate($time, $format = "d.m.Y") {
	$time = !is_numeric($time) ? strtotime($time) : $time;
	return date($format, $time);
}

function md5Encrypt($plain_text, $password = CRYPT_KEY, $iv_len = 16) {
    $plain_text .= "\x13";
	$n = strlen($plain_text);
    if ($n % 16) $plain_text .= str_repeat("\0", 16 - ( $n % 16 ));
    $i = 0;
    $enc_text = STR::get_rnd_iv($iv_len);
    $iv = substr($password ^ $enc_text, 0, 512);
    while ($i < $n) {
        $block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
        $enc_text .= $block;
        $iv = substr($block.$iv, 0, 512) ^ $password;
        $i += 16;
    }
    $enc_text = base64_encode($enc_text);
    return $enc_text;
}

function md5Decrypt($enc_text, $password = CRYPT_KEY, $iv_len = 16) {
    $enc_text = base64_decode($enc_text);
    $n = strlen($enc_text);
    $i = $iv_len;
    $plain_text = '';
    $iv = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
    while ($i < $n) {
        $block = substr($enc_text, $i, 16);
        $plain_text .= $block ^ pack('H*', md5($iv));
        $iv = substr($block.$iv, 0, 512) ^ $password;
        $i += 16;
    }
    return preg_replace('/\\x13\\x00*$/', '', $plain_text);
}

function mail_utf8($to, $subject = '', $message = '', $header = '', $type = 'plain') {
	if (!in_array($type, array('plain', 'html'))) return;
	$header_ = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/' . $type . '; charset=UTF-8' . "\r\n";
	return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $header_ . $header);
}

function mail_attc($to, $subject, $message, $files, $from_name, $from_email) {
	$subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

	$from = '=?UTF-8?B?' . base64_encode($from_name) . '?= <' . $from_email . '>';

	$boundary = 'figaroo-' . md5(microtime(1));
	$headers = array(
		'MIME-Version: 1.0',
		'From: ' . $from,
		'Content-type: multipart/mixed; boundary="' . $boundary . '"',
	);

	$text = join("\r\n", array(
		'--' . $boundary,
		'Content-Type: text/html; charset="UTF-8"',
		'Content-Transfer-Encoding: base64',
	)) . "\r\n\r\n";
	$text .= base64_encode($message);
	$text .= "\r\n\r\n";

	if ($files) {
		$attc = array();
		$files = (array)$files;
		foreach ($files as $file) {
			if (!@$file['name'] || !@$file['path']) continue;
			$part = join("\r\n", array(
				'--' . $boundary,
				'Content-Type: application/octet-stream; name="=?UTF-8?B?' . base64_encode($file['name']) . '?="',
				'Content-Transfer-Encoding: base64',
			)) . "\r\n\r\n";
			$part .= base64_encode(file_get_contents($file['path']));
			$part .= "\r\n\r\n";
			$attc[] = $part;
		}
		$attc = join('', $attc);
	} else {
		$attc = '';
	}

	$body = $text . $attc . '--' . $boundary . '--';

	return mail($to, $subject, $body, join("\r\n", $headers) . "\r\n");
}

function pagecontent($html) {
	$html = str_replace("[[URL]]", URL, $html);
	return $html;
}

function http_digest_parse($txt) {
    $needed_parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));
    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
    foreach ($matches as $m) {
        $data[$m[1]] = @$m[3] ? @$m[3] : @$m[4];
        unset($needed_parts[$m[1]]);
    }
    return $needed_parts ? false : $data;
}

function fGetDirSize($path){
	$dir = scandir($path);
	$s = 0;
	foreach ($dir as $item) {
		$newpath = $path . '/' . $item;
		if ($item == '.' || $item == '..') continue;
		if (is_dir($newpath)) {
			$s += fGetDirSize($newpath);
		}
		$s += filesize($newpath);
	}
	return $s;
}

function fakerandom($seed, $a, $b) {
	srand($seed);
	$res = rand($a, $b);
	srand(time());
	return $res;
}
function fakeshuffle($seed, &$arr) {
	srand($seed);
	shuffle($arr);
	srand(time());
}

function fgmap($x, $in_min, $in_max, $out_min, $out_max) {
	if ($in_min == $in_max || $out_min == $out_max) return $out_max;
	return ($x - $in_min) * ($out_max - $out_min) / ($in_max - $in_min) + $out_min;
}
