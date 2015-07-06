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
