+(function(){
	events.on(window, 'load', function(){
		if(window.data)
			window.datastore = new lilium.ds.DataStore(data);
		else
			window.datastore = new lilium.ds.DataStore();
	});
})();
