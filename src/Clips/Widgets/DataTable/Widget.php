<?php namespace Clips\Widgets\DataTable; in_array(__FILE__, get_included_files()) or exit("No direct script access allowed");

use Clips\WidgetV2;

/**
 * The datatable widget
 */
class Widget extends WidgetV2 {
	protected function doInit() {
		$js = <<<TEXT
	window.UrlManager = {};
	
	window.UrlManager.serialize = function(obj) {
	  var str = [];
	  for(var p in obj)
	    if (obj.hasOwnProperty(p)) {
	      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
	    }
	  return str.join("&");
	}
		
	
	$('.datatable').each(function(){
		$(this).on('init.dt', function(){
			var self = $(this);
	
			var datatable_settings = DatatableSettings[self.attr('name')];
	
			if(datatable_settings.selectType != 'single') {
				self.find("tbody").selectable({
					delay: 1
				});			
			}
	
			self.on('click', 'tr', function(){
				$(this).addClass('ui-selected').siblings().removeClass('ui-selected');
			});
	
			if($.isFunction($.fn.selectBoxIt)){
				self.parents('.dataTables_wrapper').find('select:not([data-no-selectBoxIt])').each(function(){
					$(this).selectBoxIt({});
				});
			}
	
	
			$('[datatable-for]').each(function(){
				var self = $(this);
				var datatable = {};
				datatable.length = 0;
	
				if(self.attr('datatable-for')) {
					datatable = $('.datatable[name='+self.attr('datatable-for')+']');
				}
	
				if(datatable.length > 0) {
	
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
					else if (type && type == 'search') {
						var filterDom = self;
						if(self[0].nodeName.toLowerCase() != 'input') {
						    filterDom = self.find('input');
						}
						var dt = datatable.DataTable();
						filterDom.on('keyup', function(){
							dt.search(this.value).draw();
						});
					}
					else if(type && type == 'length') {
						var selectDom = self;
						if(self[0].nodeName.toLowerCase() != 'select') {
						    selectDom = self.find('select');
						}
						var dt = datatable.DataTable();
						selectDom.on('change', function(){
							dt.page.len(this.value).draw();
						});
					}
					else if(type && type == 'order') {
						var selectDom = self;
						if(self[0].nodeName.toLowerCase() != 'select') {
						    selectDom = self.find('select');
						}
						var dt = datatable.DataTable();
						selectDom.on('change', function(){
							dt.order([this.value, 'asc']).draw();
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
					// end action switch
	
				}
	
	
			});
		});
	});

TEXT;
		\Clips\context('jquery_init', $js, true);
	}
}
