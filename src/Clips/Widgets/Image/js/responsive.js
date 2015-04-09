;(function ( $, window, document, undefined ) {

	var pluginName = 'responsiveImage';
	var defaults = {
		pattern: 'responsive/size/_(size)/_(img)',
		defaultImg: 'default/img.png'
	};
	
	function ResponsiveImage( element, options ) {
		this.element = element;
		this.img = $(element).attr('data-image'); // Getting the image
		this.options = $.extend( {}, defaults, options) ;
		this.defaults = defaults;
		var pattern = $(element).attr('data-pattern');
		if(pattern)
			this.options.pattern = pattern;
		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}

	ResponsiveImage.prototype.init = function () {
		var _this  = this;
		_this.first = 0;

		_this.restoreSettings();
		_this.autoSize();

		$(_this.element).load(function(){
			_this.first = 0;
		});

		$(_this.element).error(function (e) {
			if(_this.first == 0) {
				_this.first = 1;
				_this.loadDefault(_this.options.defaultImg);
			}
			else if(_this.first == 1) {
				_this.first = 2;
				_this.loadDefault(_this.defaults.defaultImg);
				console.log('can not find default img which is ' + Clips.siteUrl(_this.options.defaultImg));
			}
			else {
				//throw new Error('can not find default img which is ' + Clips.siteUrl(_this.defaults.defaultImg));
			}
		});

	};

	ResponsiveImage.prototype.restoreSettings = function(callback) {
		var _this = this;
		if ($(_this.element).attr('data-default-image')) {
			_this.options = $.extend(_this.options, {
				defaultImg: $(_this.element).attr('data-default-image')
			});
		}
		if($.isFunction(callback)) {
			callback();	
		}
	};

	ResponsiveImage.prototype.autoSize = function() {
		var _this = this;
		if(this.last_width == $(_this.element).parent().width())
			return;
		var src = S(_this.options.pattern).template({
			size: $(_this.element).parent().width(), img: _this.img
		}, '_(', ')').toString();

		if(src.indexOf('_(') < 0 && src.indexOf(')') < 0 ) {
			$(_this.element).attr('src', Clips.siteUrl(src));
		}
		this.last_width = $(_this.element).parent().width();
	};
	
	ResponsiveImage.prototype.loadDefault = function (imgpath) {
		var _this = this;
		if ($(_this.element).parent().width() > 0) {
			$(_this.element).attr('src', Clips.siteUrl(S(_this.options.pattern).template({
				size: $(_this.element).parent().width(), img: imgpath
			}, '_(', ')').toString()));			
		}
	};
	
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
