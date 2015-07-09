+(function(){
	events.on(window, 'load', function(){
		var ds = lilium.getCookie('clips-datastore');
		if(ds) {
			ds = decodeURIComponent(ds);
			ds = JSON.parse(ds);
			window.datastore = new lilium.ds.DataStore(ds);
		}
		else {
			window.datastore = new lilium.ds.DataStore();
		}
	});
})();
