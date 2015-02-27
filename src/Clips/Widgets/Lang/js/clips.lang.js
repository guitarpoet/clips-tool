if(typeof Clips == 'undefined')
	Clips = {};

Clips.lang = {
	message: function() {
		var a = Array.prototype.slice.call(arguments);
		var tpl = a.shift();
		if(Clips.bundle && Clips.bundle[tpl]) {
			tpl = Clips.bundle[tpl];
		}
		return sprintf(tpl, a);
	}
};
