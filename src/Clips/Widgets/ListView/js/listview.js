/**
 * jQuery List view
 *
 * jQuery list view is a plugin to add powerful list view control using jQuery.
 *
 * The list view has two type, static and auto.
 *
 * The static type list view, the width of the list item is static, won't change when
 * the window's size is changed, the margin of the list item is calculated
 *
 * The dynamic type list view, the width of the list item is dynamic, can be changed when
 * the windows size is changed.
 *
 * @version 0.0.1
 * @author Jack <guitarpoet@gmail.com>
 * @license BSD
 * @date Wed Feb 25 21:38:02 2015
 */
(function($){
	function template_string(node, data) {
		var template = (typeof node == 'string')? node: $('<div/>').append($(node).clone()).html();
		var ret = S(template).template(data, '_(', ')').toString();
		return ret;
	}

	array_remove = function(array, from, to) {
    	var rest = array.slice((to || from) + 1 || array.length);
    	array.length = from < 0 ? array.length + from : from;
    	return array.push.apply(array, rest);
	};

	$.fn.listview = function(args) {
		var defaults = {
			listtype: 'dynamic', // The list type, can be auto and static
			columns_count: -1, // The columns of the list. This is required for list type static, default -1 means auto
			ajax: '', // The service url for the list view items
			gap: 10, // The gap for all the list items, usefule for dynamic, default is 10px
			vgap: 10, // The vertical gap for all the list items
			min_gap: 10, // The min gap for all the list item, this is used for static list view
			pg_ratio: 1, // The padding and gap ratio, this is work for static list view, for static list view, the padding and the gap is calculated, default is 1, the gap and the padding is the same
			processing: true,
			selectType: 'multi',
			joins: [],
			stateSave: true,
			serverSide: true,
			margin_right: 0,
			margin_bottom: 0,
			info: '',
			columns: [],
			select_options: [5,10,20,50],
			selectable_prefix: 'select',
			template: '<li class="item listview_item_template">_(content)</li>',
			pagination_template: '<div class="listview_paginate"></div>',
			pagination_previous_template: '<a class="paginate_button previous" aria-controls="listview"><i class="glyphicon glyphicon-backward"></i></a>',
			pagination_button_template: '<a class="paginate_button" data-dt-idx="_(index)" tabindex="0" aria-controls="listview">_(index)</a>',
			pagination_button_group_template: '<span></span>',
			pagination_next_template: '<a class="paginate_button next" aria-controls="listview"><i class="glyphicon glyphicon-forward"></i></a>',
			length_select_template: '<div class="listview_length">Show<label><select class="" name="listview_length" aria-controls="datatable"></select></label>entries</div>',
			fliter_template: '<div class="listview_filter">Search<label><input class="" type="search" placeholder="" aria-controls="datatable"></input></label></div>',
			wrap: '<div class="dataTables_wrapper listview_wrapper"/>',
			order_box: '<select id="listview_orderbox" class="listview_orderbox"></select>',
			order_dir_box: '<select id="listview_order" class="listview_orderbox"><option id="asc">ASC</option><option id="desc">DESC</option></select>'
		};

		var settings = $.extend(true, {}, defaults, args);
		var self = $(this);

		settings.col_objs = [];

		$.each(settings.columns, function(i, item) {
			settings.col_objs.push(new $.fn.listview.Column(item));
		});

		function restoreSavedStates(list) {
			var savedStates = getState(list);
			if(savedStates) {
				list.states = savedStates;
				list.start = savedStates.start;
				list.pageLength = savedStates.length;
				if(savedStates.search && savedStates.search.value)
					list.search_value = savedStates.search.value;
				if(savedStates.order && savedStates.order[0].column) {
					list.orderColumn = savedStates.order[0].column;
					list.orderDir = savedStates.order[0].dir;
				}
			}
		}

		function getState(list) {
			var key = 'listview_';
			key += window.location.pathname;
			return JSON.parse(store.get(key));
		}

		function saveState(list, state) {
			var key = 'listview_';
			key += window.location.pathname;
			store.set(key, JSON.stringify(state));
		}

		function requestData(list) {
			if(settings.ajax != '') {
				// data, name, orderable, regex, searchable, value
				if(list.draw) {
					list.draw++;
				}
				else
					list.draw = 1;

				if(!list.start) {
					list.start = 0;
				}

				if(settings.pageLength) {
					list.pageLength = settings.pageLength;
				}

				if(!list.pageLength) {
					list.pageLength = 10;
				}

				var cols = [];
				$.each(settings.col_objs, function(i,item) {
					cols.push(item.to_query());
				});

				var orderColumn = 0;
				if(list.orderColumn) {
					orderColumn = parseInt(list.orderColumn);
				}

				var orderDir = 'ASC';
				if(list.orderDir) {
					orderDir = list.orderDir;
				}

				var option = list.states || {};
				var listview_option = $.extend(true, option, {
					draw : list.draw,
					columns : cols,
					start : list.start,
					length : list.pageLength,
					order : [{
						column : orderColumn,
						dir : orderDir
					}],
					search : {
						regex: false
					},
					selectedItems : []
				});

				if(list.search_value || list.search_value == '') {
					listview_option.search.value = list.search_value
				}

				$.getJSON(settings.ajax, listview_option, function(data) {
					p = calculatePagination(data.start, data.length, data.recordsFiltered);
					list.states = listview_option;
					makeItems(list, data);
					selectItems(list, p.current);
					layoutItems(list); // Layout the list first
					saveState(list, listview_option);
					self.trigger('list.loaded', [list, data]);
				});
			}
		}

		function makeItems(list, data) {
			var listData = data.data;
			if(listData) {
				list.children('li').not('li.listview_item_template').remove(); // Clear last items
				var template = list.children('li.listview_item_template');
				if(!template.length) {
					template = settings.template;
				}
				else {
					template = template.get(0);
				}
				$.each(listData, function(i, e) {
					var li = $(template_string(template, e)).removeClass('listview_item_template');
					li.attr('itemId',e.users_id);
                    li.trigger('list.item.load', [e]);
                    li.data('itemdata', e);
                    list.append(li);
				});
				makePagination(list, data);
			}
		}

		function selectItems(list) {
			var psl = list.states.selectedItems;
			if(psl) {
				list.find("li").not('li.listview_item_template').each(function(i){
					var itemId = parseInt($(this).attr('itemId'));
					if ($.inArray(itemId, psl) > -1) {
						$(this).addClass('ui-selected selected');
					};
				})
			}
			list.off('selectableselected').on('selectableselected', function(event, ui){
				var itemId = parseInt($(ui.selected).attr('itemId'));
				if($.isNumeric(itemId)) {
					console.log(itemId);
					var index = $.inArray(itemId, psl);
					if(index < 0) {
						list.states.selectedItems.push(itemId);
						saveState(list, list.states);
					}
				}
			})
			list.off('selectableunselected').on('selectableunselected', function(event, ui){
				var itemId = parseInt($(ui.unselected).attr('itemId'));
				if($.isNumeric(itemId)) {
					var index = $.inArray(itemId, psl);
					if(index > -1) {
						list.states.selectedItems.splice(index,1);
						saveState(list, list.states);
					}
				}
			})
		}

		function getSelectedNums(list, index) {
			var nums = []
			list.find('li').not('li.listview_item_template').each(function(i){
				var itemId = parseInt($(this).attr('itemId'));

				if($(this).hasClass('ui-selected')) {
					nums.push(parseInt(itemId));
				}

			})
			return nums;
		}

		function getUnSelectedNums(list, index) {
			var nums = []
			list.find('li').not('li.listview_item_template').each(function(i){
				var itemId = parseInt($(this).attr('itemId'));

				if(!$(this).hasClass('ui-selected')) {
					nums.push(parseInt(itemId));
				}

			})
			return nums;
		}

		function calculatePagination(start, length, records) {
			var p = {};
			p.current = start / length + 1;
			p.total = Math.ceil(records / length);
			return p;
		}

		function updatePaginationButtons(list, data) {
			// Remove all the paginate buttons
			var bg = list.parent().find('.listview_pagination span');
			bg.children().remove();

			var lf = list.find('.pagination_button_template');
			var pbt = lf.length? lf.get(0): settings.pagination_button_template;
			var records = data.recordsFiltered;
			var length = data.length;
			var start = data.start;

			var p = calculatePagination(start, length, records);
			var trunk = p.toal > 7;

			var first = $(template_string(pbt, {index: 1}));
			var last = $(template_string(pbt, {index: p.total}));

			if(p.total < 7) {
				for(var i = 1; i <= p.total; i++) {
					var item = $(template_string(pbt, {index: i}));
					if(i == p.current)
						item.addClass('current');
					bg.append(item);
				}
			}else{
				if(p.current == 1)
					first.addClass('current');
				bg.append(first);

				if(p.current < 5) {
					for(var i = 2; i <= 5; i++) {
						var item = $(template_string(pbt, {index: i}));
						if(i == p.current)
							item.addClass('current');
						bg.append(item);
					}
					bg.append('<span>...</span>');
				}
				else if(p.current > p.total - 4) {
					bg.append('<span>...</span>');
					for(var i = p.total - 4; i <= p.total - 1; i++) {
						var item = $(template_string(pbt, {index: i}));
						if(i == p.current)
							item.addClass('current');
						bg.append(item);
					}
				}
				else {
					bg.append('<span>...</span>');
					for(var i = p.current - 1; i <= p.current + 1; i++) {
						var item = $(template_string(pbt, {index: i}));
						if(i == p.current)
							item.addClass('current');
						bg.append(item);
					}
					bg.append('<span>...</span>');
				}
				if(p.total != 1) {
					if(p.current == p.total) {
						last.addClass('current');
					}
					bg.append(last);
				}
			}


			bg.off('click').on('click', 'a', function() {
				list.start = (parseInt($(this).text()) - 1) * list.pageLength;
				requestData(list);
			});

			bg.parent().off('click');

			if(p.current > 1) {
				bg.parent().find('a.previous').removeClass('disabled');
				bg.parent().on('click', 'a.previous', function(){
					list.start = (p.current - 2) * list.pageLength;
					requestData(list);
				});
			}else{
				bg.parent().find('a.previous').addClass('disabled');
			}

			if(p.current < p.total) {
				bg.parent().find('a.next').removeClass('disabled');
				bg.parent().on('click', 'a.next', function(){
					list.start = p.current * list.pageLength;
					requestData(list);
				});
			}else{
				bg.parent().find('a.next').addClass('disabled');
			}
		}

		function createPagination(list, data) {
			var lc = list.children('.listview_pagination_template');
			var lpt = lc.length? lc.get(0): settings.pagination_template;

			var lf = list.find('.pagination_previous_template');
			var pbt = lf.length? lf.get(0): settings.pagination_previous_template;

			lf = list.find('.pagination_next_template');
			var nbt = lf.length? lf.get(0): settings.pagination_next_template;

			lf = list.find('.listview_pagination_button_group_template');
			var bgt = lf.length? lf.get(0): settings.pagination_button_group_template;

			var pagination = $(lpt).addClass('listview_pagination').removeClass('listview_pagination_template');
			pagination.append($(pbt).removeClass('pagination_previous_template'));
			var a = $(bgt);
			a.children().remove();
			pagination.append(a.removeClass('listview_pagination_button_group_template'));
			pagination.append($(bgt).removeClass('listview_pagination_button_group_template').children('a').remove());
			pagination.append($(nbt).removeClass('pagination_next_template'));
			list.parent().append(pagination);
		}

		function createItemlengthbox(list) {
			var lengthSelect = $(settings.length_select_template);
			for (var i = 0; i < settings.select_options.length; i++) {
				var str = '<option>'+settings.select_options[i]+'</option>';
				if (list.pageLength == settings.select_options[i]) {
					str = '<option selected>'+settings.select_options[i]+'</option>';
				};
				lengthSelect.find('select').append(str);
			};

			list.parent().prepend(lengthSelect);

			lengthSelect.find('select').on('change', function(){
				list.pageLength = parseInt(lengthSelect.find('select').val());
				requestData(list);
			})
		}

		function createFliterbox(list) {
			var search = $(settings.fliter_template);
			if(list.search_value) {
				search.find('input').val(list.search_value);
			}
			list.parent().prepend(search);

			search.find('input').on('keyup',function(){
				list.search_value = $(this).val();
				requestData(list);
			});
		}

		function makePagination(list, data) {
			if(list.parent().children('.listview_pagination').length == 0) {
				createPagination(list, data);
			}
			updatePaginationButtons(list, data);
		}

		function setSelectablePlugin(list) {
			list.selectable({
				delay: 85,
				start: function(event, ui) {
					$(this).find("li").each(function(){
						$(this).removeClass('selected');
					})
				},
				selected: function(event, ui) {
					$(this).find("li").each(function() {
						if($(this).hasClass('ui-selected'))
							$(this).addClass('selected');
						else
							$(this).removeClass('selected');
					})
				}
			});
		}

		function layoutItems(list) {
			var box = {};
			box.pl = list.css('padding-left').replace('px','');
			box.pr = list.css('padding-right').replace('px', '');
			box.width = list.width();
			box.vgap = settings.vgap;

			if(!list.hasClass('listview')) {
				list.addClass('listview');
			}

			list.children('li').each(function(index,item) { // Add the list view item class to every li
				if(!$(item).hasClass('listview_item')) {
					$(item).addClass('listview_item');
				}
			});

			if(settings.listtype == 'static' || settings.columns_count == -1) { // Setting columns to -1 triggers static list view
				// box.iw = list.children('li').width(); // Get the item width, only support all the item has the same width in current version(0.0.1)
				// box.mg = settings.min_gap;
				// box.r = settings.pg_ratio;
				// // Now let's calcaulte how many columns can we have
				// box.columns = Math.floor((box.width - 2 * box.mg * box.r + box.mg) / (box.iw + box.mg));
				// box.gap = Math.floor((box.width - box.iw * box.columns) / (2 * box.r + box.columns - 1));
				// box.pl = box.gap * box.r;
				// box.pr = box.gap * box.r;
				// list.css('padding-left', box.pl);
				// list.css('padding-right', box.pr);
				// list.children('li').not('.listview_item_template').each(function(index, item) {
				// 	if((index + 1) % box.columns != 0 || box.columns == 1) { //
				// 		$(item).css('margin-right', box.gap);
				// 	}
				// 	$(item).css('margin-bottom', box.vgap);
				// });
				var listview_items =  list.children("li").filter('.listview_item').not('.listview_item_template');
				var mr =  parseInt(listview_items.eq(0).css("margin-right")) || 0;
				var mb = parseInt(listview_items.eq(0).css("margin-bottom")) || 0;
				var cols = Math.floor((list.width() + mr) /  (listview_items.eq(0).width() + mr) );
				layout(listview_items, cols, mb, mr);
			}
			else {
				box.w = box.width - box.pl - box.pr; // The container width
				box.gap = settings.gap; // The gaps between items
				box.columns = settings.columns_count;
				var item_width = (box.w + box.gap) / box.columns - box.gap;
				list.children('li').width(item_width);
				list.children('li').not('.listview_item_template').each(function(index, item) {
					if((index + 1) % box.columns != 0 || box.columns == 1) { // If this is the end of the row
						$(item).css('margin-right', box.gap);
					}
					$(item).css('margin-bottom', box.vgap);
				});
			}
		}

		function createOrderbox(list) {
			var orderBox = $(settings.order_box);
			var orderDirBox= $(settings.order_dir_box);
			var order, orderDir;
			if(list.states && list.states.order) {
				order = list.states.order[0].column;
				orderDir = list.states.order[0].dir;
			}
			$.each(settings.columns, function(i, col){
				if(col.orderable) {
					var option = $('<option></option>');
					option.text(col.title);
					option.attr('value', i);
					option.attr('data', col.data);
					if(order && order == i) {
						option.attr('selected','');
					}
					orderBox.append(option);
				}
			});
			orderDirBox.find('option').each(function(i, dir) {
				if (orderDir && orderDir == $(dir).val()) {
					$(dir).attr('selected','');
				}
			})
			orderBox.change(function(){
				list.orderColumn = orderBox.val();
				list.orderDir = orderDirBox.val();
				requestData(list);
			});
			orderDirBox.change(function(){
				list.orderColumn = orderBox.val();
				list.orderDir = orderDirBox.val();
				requestData(list);
			});
			list.parent().prepend(orderDirBox);
			list.parent().prepend(orderBox);
		}

		var Api = function(list){
			_this = this;
			_this.list = list;
		};
		Api.prototype.search = function(value){
			_this.list.search_value = value;
			requestData(this.list);
		}

		Api.prototype.save = function(itemId) {
			var psl = _this.list.states.selectedItems;
			if($.isNumeric(itemId)) {
				var index = $.inArray(itemId, psl);
				if(index < 0) {
					_this.list.states.selectedItems.push(itemId);
					saveState(_this.list, _this.list.states);
				}
			}
		}

		this.each(function() {
			var list = $(this);
			restoreSavedStates(list); // Restore the states at first
			list.wrap(settings.wrap); // Added the list wrap
			requestData(list);	// Requesting the data for the listview
			createFliterbox(list); // Create the filterbox for listview
			createOrderbox(list);
			createItemlengthbox(list);	 // Create the length choice box
			setSelectablePlugin(list); // Initilize the selectable function for listview
			self.data('api', new Api(list));
			// Getting the list's basic informations
			$(window).resize(function(){ // If the size of the list has been changed, relayout the items
				layoutItems(list);
			});
		});
	}

	$.fn.listview.Column = function(cellType, className, contentPadding, data, defaultContent, name, orderable, searchable, title, type, visible, width, dataCol) {
		if(typeof cellType === 'object') {
			this.cellType = cellType.cellType;
			this.className = cellType.className;
			this.contentPadding = cellType.contentPadding;
			this.data = cellType.data;
			this.defaultContent = cellType.defaultContent;
			this.name = cellType.name;
			this.orderable = cellType.orderable;
			this.searchable = cellType.searchable;
			this.title = cellType.title;
			this.type = cellType.type;
			this.visible = cellType.visible;
			this.width = cellType.width;
			this.dataCol = cellType.dataCol;
		}
		else {
			this.cellType = cellType;
			this.className = className;
			this.contentPadding = contentPadding;
			this.data = data;
			this.defaultContent = defaultContent;
			this.name = name;
			this.orderable = orderable;
			this.searchable = searchable;
			this.title = title;
			this.type = type;
			this.visible = visible;
			this.width = width;
			this.dataCol = dataCol;
		}
	}

	$.fn.listview.Column.prototype = {
		to_query: function() {
			return {
				regex: false,
				data: this.data,
				orderable: this.orderable,
				searchable: this.searchable,
				value: '',
				name: name
			};
		}
	}
})(jQuery);
