App = new function(_url) {
	this.jsUrl = URL + 'js/';
	this.cacheUrl = URL + 'cache/';
	this.tmplUrl =    _url;
	this.tmplImgUrl = _url + 'img/';
	this.tmplCssUrl = _url + 'css/';
	this.tmplJsUrl =  _url + 'js/';
}(tmplUrl);
