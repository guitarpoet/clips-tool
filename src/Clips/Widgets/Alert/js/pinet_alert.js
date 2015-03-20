;(function ( $, window, document, undefined ) {

	var pluginName = 'message',
		defaults = {
			propertyName: "value"
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
		
		$(this.element).find('[alert-for="close"]').on('click', function(){
			_this.close();
		});
		
	};
	
	Plugin.prototype.close = function() {
		alert('close');
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