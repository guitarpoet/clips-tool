<?php namespace Clips\Widgets\DataTable; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

/**
 * The datatable widget
 */
class Widget extends \Clips\Widget {
	protected function doInit() {
		$js = <<<TEXT
$('.datatable').each(function(){
	$(this).on('init.dt', function(){
		var self = $(this);

		self.find("tbody").selectable({
			delay: 1
		});

		self.on('click', 'tr', function(){
			$(this).addClass('ui-selected').siblings().removeClass('ui-selected');
		});

		if($.isFunction($.fn.selectBoxIt)){
			self.parents('.dataTables_wrapper').find('select:not([data-no-selectBoxIt])').each(function(){
				$(this).selectBoxIt({});
			});
		}
	});
});

window.UrlManager = {};

window.UrlManager.serialize = function(obj) {
  var str = [];
  for(var p in obj)
    if (obj.hasOwnProperty(p)) {
      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
  return str.join("&");
}

$('[role="datatable-action"], button[type=ajax], .action').each(function(){
	var self = $(this);
	var forname = self.attr('for');
    var datatable = $('.datatable[name='+forname+']');
    var type = self.attr("type");
	var url = self.attr("href");
	if (!url) {
		var uri = self.attr('uri');
		if(!uri) {
			return false;
		}
		else {
			url = Clips.siteUrl(uri);
		}
	}

	if(type && type == 'ajax') {
		self.on('click', function(e){
			e.preventDefault();
			var settings = datatable.DataTable.settings[0];
			var pks = window.DataTableManager.getSelectedItemsPrimaryKeys(datatable, settings);
		    if(pks) {
			    $.ajax({
			        type: "POST",
			        url: url,
			        data: {
			            ids: pks
			        },
			        dataType: "json"
			    }).success(function(data){
			        datatable.DataTable().draw();
			    });
		    }
		});
	}
	else {
		self.on('click', function(e){
			e.preventDefault();
			var settings = datatable.DataTable.settings[0];
			var pks = window.DataTableManager.getSelectedItemsPrimaryKeys(datatable, settings);
			if(pks) {
		//		var params = '?' + window.UrlManager.serialize({
		//			id: pks[0]
		//		});
				window.location.href = url + '/' + pks[0];
			}
		});
	}
});
TEXT;
		\Clips\context('jquery_init', $js, true);
	}
}
