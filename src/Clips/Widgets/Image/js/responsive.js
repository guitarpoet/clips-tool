;(function ( $, window, document, undefined ) {

	var pluginName = 'responsiveImage';
	var defaults = {
		pattern: 'responsive/size/_(size)/_(img)'
	};
	
	function ResponsiveImage( element, options ) {
		this.element = element;
		this.img = $(element).attr('data-image'); // Getting the image
		this.options = $.extend( {}, defaults, options) ;
		var pattern = $(element).attr('data-pattern');
		if(pattern)
			this.options.pattern = pattern;
		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}

	ResponsiveImage.prototype.init = function () {
		this.autoSize();
	};

	ResponsiveImage.prototype.autoSize = function() {
		var _this = this;
		if(this.last_width == $(_this.element).parent().width())
			return;
		$(_this.element).attr('src', S(_this.options.pattern).template({
			size: $(_this.element).parent().width(), img: _this.img
		}, '_(', ')').toString());
		this.last_width = $(_this.element).parent().width();
	}
	
	$.fn[pluginName] = function ( options ) {
		var result = this.each(function () {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName,
					new ResponsiveImage( this, options ));
			}
		});

		var _this = this;
		$(window).resizeEnd(function(){
			_this.each(function() {
				var plugin = $.data(this, 'plugin_' + pluginName);
				if(plugin)
					plugin.autoSize();
			});
		});
		return result;
	}

})( jQuery, window, document );
