babel.transform.load = function (url, callback, opts, hold) {
  if (opts === undefined) opts = {};
  opts.modules = "ignore";
  opts.filename = opts.filename || url;

  var xhr = new XMLHttpRequest();
  xhr.open("GET", url, true);
  if ("overrideMimeType" in xhr) xhr.overrideMimeType("text/plain");

  xhr.onreadystatechange = function () {
    if (xhr.readyState !== 4) return;

    var status = xhr.status;
    if (status === 0 || status === 200) {
      var param = [xhr.responseText, opts];
      if (!hold) babel.transform.run.apply(babel.transform, param);
      if (callback) callback(param);
    } else {
      throw new Error("Could not load " + url);
    }
  };

  xhr.send(null);
};

imports = function(name, module) {
}

function functionName(fun) {
	var ret = fun.toString();
	ret = ret.substr('function '.length);
	ret = ret.substr(0, ret.indexOf('('));
	return ret;
}

exports = function(widgets, module) {
	if(!module)
		module = 'widgets';
	window[module] = window[module] || {};

	if(Object.prototype.toString.call(widgets) === '[object Array]' ) {
		for(var i = 0;i < widgets.length; i++) {
			window[module][functionName(widgets[i])] = widgets[i];
		}
	}
	else {
		if(typeof widgets === 'function') {
			window[module][functionName(widgets)] = widgets;
		}
	}
}
