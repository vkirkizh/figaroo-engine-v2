function gE(id) {return document.getElementById(id);}

function putSiteMess(text, type) {
	var mess = gE('info_mess');
	if (!mess) return alert(text);
	mess.style.display = '';
	mess.className = type == _SUCCESS_ ? 'some-message' : 'error-message';
	mess.innerHTML = text;
}
function hideSiteMess() {
	var mess = gE('info_mess');
	if (!mess) return;
	mess.style.display = 'none';
	mess.innerHTML = '';
}

$('#session_key').val(session_key);

$('.fg-time').each(function(){
	var time = $(this).data('time'),
		format = $(this).data('format');
	$(this).text(fgDate(format, time));
});

(function(d){
	function c(k){return(d.cookie.match('(^|; )'+k+'=([^;]*)')||0)[2];}
	var ua = navigator.userAgent,
		ismobile = / mobile/i.test(ua),
		mgecko = !!( / gecko/i.test(ua) && / firefox\//i.test(ua)),
		wasmobile = c('wasmobile') === "was",
		desktopvp = 'user-scalable=yes, width=1000, minimum-width=1000',
		el;
	if (ismobile && !wasmobile) {
		d.cookie = "wasmobile=was";
	}
	else if (!ismobile && wasmobile) {
		if (mgecko) {
			el = d.createElement('meta');
			el.setAttribute('content', desktopvp);
			el.setAttribute('name', 'viewport');
			d.getElementsByTagName('head')[0].appendChild(el);
		}else{
			d.getElementsByName('viewport')[0].setAttribute('content', desktopvp);
		}
	}
}(document));

function preg_quote(str) {
	return str.replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
}

function sleep(ms) {
	var start = new Date().getTime(), expire = start + ms;
	while (new Date().getTime() < expire) { }
	return;
}

function ucfirst(str) {
	var f = str.charAt(0).toUpperCase();
	return f + str.substr(1, str.length-1);
}

function preg_match_all(regex, haystack) {
	var globalRegex = new RegExp(regex, 'ig');
	var globalMatch = haystack.match(globalRegex);
	matchArray = new Array();
	for (var i in globalMatch) {
		nonGlobalRegex = new RegExp(regex);
		nonGlobalMatch = globalMatch[i].match(nonGlobalRegex);
		matchArray.push(nonGlobalMatch[1]);
	}
	return matchArray;
}
