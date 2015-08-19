
//====================================
// Initializing Clips JS Base
//====================================
if(typeof Clips == 'undefined')
	Clips = {};
Clips.base = '{{base}}';
Clips.site = '{{site}}';
Clips.siteUrl = function(url) {
	if(url.indexOf('/') == 0)
		url = url.substring(1);
	return Clips.site + url;
}
Clips.staticUrl = function(url) {
	if(url) {
		if(url.indexOf('/') == 0)
			url = url.substring(1);
	}
	else {
		url = '';
	}
	return Clips.base + '' + url;
}
