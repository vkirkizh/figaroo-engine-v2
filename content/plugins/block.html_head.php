<?php

function smarty_block_html_head($params, $content, &$smarty) {
	if (is_null($content)) return;
	$text = '';
	$text .= '<head>' . "\n";
	$text .= '<meta charset="UTF-8">' . "\n";
	$text .= '<title>' . Page::get()->title() . '</title>' . "\n";
	if (!Page::get()->httpError() && Page::get()->getIndexing()) {
		$text .= '<meta name="robots" content="index,follow" />' . "\n";
	} else {
		$text .= '<meta name="robots" content="noindex,nofollow" />' . "\n";
	}
	if (Page::get()->keywords()) $text .= '<meta name="keywords" content="' . Page::get()->keywords() . '" />' . "\n";
	if (Page::get()->description()) $text .= '<meta name="description" content="' . Page::get()->description() . '" />' . "\n";
	$text .= $content . "\n";
	$text .= '<link rel="index" href="' . URL . '" title="' . htmlsec(Settings::get()->main->title) . '" />' . "\n";
	if (!Page::get()->httpError()) {
		$text .= '<link rel="canonical" href="' . Page::get()->vpathStr() . '" />' . "\n";
	}
	$text .= '<link rel="icon" href="' . URL . 'favicon.ico" type="image/x-icon" />' . "\n";
	$text .= '<link rel="apple-touch-icon" href="' . App::get()->tmplImgUrl() . 'apple-touch-icon.png">' . "\n";
	$text .= '<FIGAROO:CSS>' . "\n";
	$text .= '<FIGAROO:HEAD_JS>' . "\n";
	$text .= '</head>' . "\n";
	return $text;
}
