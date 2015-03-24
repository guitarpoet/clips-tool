if(typeof Clips == 'undefined')
	Clips = {};

Clips.layout = function (container, options, callback) {
	// Load the layout rules first
	Clips.rules.load('Widgets/Layout/rules/layout.rules');

	var default_options = {
		vgap: 0,
		hgap: 0,
		itemClass: 'box',
		layout: ['layout', 'waterfall', "left"]
	};

	// Override the options using default options
	options = $.extend(default_options, options)

	// Asserting the base informations
	Clips.rules.assert(['vgap', options.vgap]);
	Clips.rules.assert(['hgap', options.hgap]);
	Clips.rules.assert(['total-width', $(container).width()]);
	Clips.rules.assert(options.layout);
	Clips.rules.filter(options.itemClass);

	$(container + ' .' + options.itemClass).each(function(i){
		var data = ['b'];
		var item = $(this);
		data.push(i);
		data.push(item.width());
		data.push(item.height());
		data.push(parseInt(item.css("margin-left").replace(/[^-\d\.]/g, '')));
		data.push(parseInt(item.css("margin-right").replace(/[^-\d\.]/g, '')));
		data.push(parseInt(item.css("margin-top").replace(/[^-\d\.]/g, '')));
		data.push(parseInt(item.css("margin-bottom").replace(/[^-\d\.]/g, '')));
		Clips.rules.assert(data);
	});
	Clips.rules.run(function(data){
		$(container).addClass('abs');
		var boxes = $(container + ' .' + options.itemClass);
		var height = 0;
		$(data).each(function(i){ // Iterationg boxes
			var h = this.y + this.height + this['margin-top'] + this['margin-bottom'];
			if(h > height)
				height = h;
			boxes.eq(this.index).css('left', this.x).css('top', this.y);
		});
		$(container).height(height);
		if(callback)
			callback(data);
	});
}
