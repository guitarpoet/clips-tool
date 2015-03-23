;(function ( $, window, document, undefined ) {

	var pluginName = 'message',
		defaults = {
			propertyName: 'value',
			fadeEffect: 'hide'
		};
	
	function Plugin( element, options ) {
		this.element = element;

		this.options = $.extend( {}, defaults, options) ;

		this._defaults = defaults;
		this._name = pluginName;

		this.init();
	}

	Plugin.prototype.init = function () {

		var _this = this;
		
		$(this.element).find('[alert-for="close"]').on('click', function(e){
			_this.close();
			$(this).trigger('alert.close', [$(_this.element)]);
		});
		
	};
	
	Plugin.prototype.close = function() {
		var _this = this;
		var fadeEffect = '';
		var args = [];
		
		if($.isArray(_this.options.fadeEffect) && _this.options.fadeEffect.length > 1) {
			fadeEffect = _this.options.fadeEffect[0];
			args = _this.options.fadeEffect[1];
		}
		else {
			fadeEffect = _this.options.fadeEffect;
		}
		
		$(_this.element)[fadeEffect](args);
	}
	
	$.fn[pluginName] = function ( options ) {
		return this.each(function () {
			if (!$.data(this, 'plugin_' + pluginName)) {
				$.data(this, 'plugin_' + pluginName,
					new Plugin( this, options ));
			}
		});
	}

})( jQuery, window, document );