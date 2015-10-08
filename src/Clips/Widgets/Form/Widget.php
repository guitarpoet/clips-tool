<?php namespace Clips\Widgets\Form; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\WidgetV2;

class Widget extends WidgetV2 {
    protected function doInit() {
        $js = <<<TEXT
	//====================================
	// Initializing Form
	//====================================
	$('input,select,textarea').not('[type=submit]').jqBootstrapValidation();
	if($.isFunction($.fn.selectBoxIt)){
		$('form select:not([data-no-select-box-it])').each(function(){
			$(this).selectBoxIt({
				autoWidth: false
			});
		});
	}
	$('[form-for]').each(function(){
		var self = $(this);
		var forname = self.attr('form-for');
	    var form = $('form[name='+forname+']');

		if(form.length > 0) {
	        var type = self.attr("action");
			var url = self.attr("href");
			if(!type || type == 'ajax') {
				if (!url) {
					var uri = self.attr('uri');
					if(!uri) {
	//							return false;
					}
					else {
						url = Clips.siteUrl(uri);
					}
				}
			}

			if(type && type == 'ajax') {
	//		    ajax
			}
			else {
				self.on('click', function(e){
					e.preventDefault();
					form.submit();
				});
			}
		}
	});
TEXT;
        \Clips\context('jquery_init', $js, true);
    }
}
