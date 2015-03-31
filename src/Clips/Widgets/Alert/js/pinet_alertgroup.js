;(function ( $, window, document, undefined ) {

	var pluginName = 'alertgroup',
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

	function updateItemPosition(owlcarousel) {
		owlcarousel.find('.owlcarousel-item').each(function(i) {
			var self = $(this);
			self.find('.alert').attr('targetposition', i);
		} );
	}	
	
	Plugin.prototype.init = function () {

		var _this = this;

		var owl = $(this.element);

		owl.owlCarousel({
			singleItem: true,
			afterInit: function(owlcarousel) {
				updateItemPosition(owlcarousel);
			}
		});

		var owlApi = owl.data('owlCarousel');

		owl.on('alert.close', function(e, item){
			var targetPosition = item.attr('targetposition');
			owlApi.removeItem(targetPosition);
			owlApi.jumpTo(targetPosition);
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