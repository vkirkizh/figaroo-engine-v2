<?php

require_once('./kernel/configs/system.php');
require_once('./kernel/kernel-main.php');

$path = preg_replace("#^" . preg_quote(URL, "#") . "#uis", "", PROTOCOL . '://' . $_SERVER['SERVER_NAME'] . urldecode($_SERVER['REQUEST_URI']));

$base = dirname($path) . '/';
$filename = basename($path);

if (0
	|| preg_match("#^(.*?)(?:\.v[0-9a-z]+)?\.(css)$#uis", $filename, $m)
	|| preg_match("#^(.*?)(?:\.v[0-9a-z]+)?\.(js)$#uis", $filename, $m)
) {
	$name = @$m[1];
	$ext = @$m[2];
} else {
	die('Access denied.');
}

$file = DIR . $base . $name . '.' . $ext;
$gzip = CACHE_DIR . 'static/' . md5($base . $name) . '.' . $ext . '.gz';

if (!file_exists($file)) {
	httpStatus('404 Not Found');
	die('File not found.');
}

switch ($ext) {
	case 'css': $mime = 'text/css'; break;
	case 'js': $mime = 'text/javascript'; break;
	default: $mime = 'text/plain'; break;
}

$filemtime = filemtime($file);

if (gzipEnabled()) {
	$output = $gzip;
	if (FIGAROO_DEBUGGING || !file_exists($gzip) || $filemtime > filemtime($gzip)) {
		file_put_contents($gzip, gzencode(file_get_contents($file), 9, FORCE_GZIP));
	}
	header('Content-Encoding: gzip');
}
else {
	$output = $file;
}

header('Content-Length: ' . filesize($output));

header('Content-Type: ' . $mime . '; charset=UTF-8');

if (FIGAROO_DEBUGGING) {
	httpCache(false);
} else {
	httpCache(true, 31536000);
}

$fileetag = md5(file_get_contents($file));
header('ETag: ' . $fileetag);
if (@$_SERVER['HTTP_IF_NONE_MATCH']) {
	$if_none_match = strtotime($_SERVER['HTTP_IF_NONE_MATCH']);
	if ($if_none_match && $if_none_match != $fileetag) {
		header("HTTP/1.1 304 Not Modified");
		header('Content-Length: 0');
		die();
	}
}

header('Last-Modified: ' . gmdate('r', $filemtime) . ' GMT');
if (@$_SERVER['HTTP_IF_MODIFIED_SINCE']) {
	$if_modified_since = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
	if ($if_modified_since && $if_modified_since >= $filemtime) {
		header("HTTP/1.1 304 Not Modified");
		header('Content-Length: 0');
		die();
	}
}

header('Vary: User-Agent,Nogzip,Accept-Encoding');
header('Connection: close');

readfile($output);
