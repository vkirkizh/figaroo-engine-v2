<?php

function smarty_block_html_body($params, $content, &$smarty) {
	if (is_null($content)) return;
	$text  = '<body';
	foreach ($params as $k => $v) {
		$text .= ' ' . $k . '="' . htmlsec($v) . '"';
	}
	$text .= '>' . "\n";
	$text .= $content . "\n";
	if (Settings::get()->external->googleAnalytics) {
		$id = htmlsec(Settings::get()->external->googleAnalytics);
		$text .= '<script type="text/javascript">';
		$text .= '(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){';
		$text .= '(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),';
		$text .= 'm=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)';
		$text .= '})(window,document,"script","//www.google-analytics.com/analytics.js","ga");';
		$text .= 'ga("create","' . $id . '","auto");';
		$text .= 'ga("send","pageview");';
		$text .= '</script>';
		$text .= "\n";
	}
	if (Settings::get()->external->yandexMetrika) {
		$id = intval(Settings::get()->external->yandexMetrika);
		$text .= '<script type="text/javascript">var yaParams = {};</script>';
		$text .= '<script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter' . $id . ' = new Ya.Metrika({id:' . $id . ', enableAll: true, webvisor:true, params:window.yaParams||{ }}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script>';
		$text .= '<noscript><div><img src="//mc.yandex.ru/watch/' . $id . '" style="position:absolute; left:-9999px;" alt="" /></div></noscript>';
		$text .= "\n";
	}
	$text .= '</body>' . "\n";
	$text = preg_replace("/(\r?\n\r?)+/", "$1", $text);
	return $text;
}
