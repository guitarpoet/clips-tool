function datatable_action_column(data, type, row, meta) {
	var func = $('#' + meta.settings.sTableId).attr('name') + '_datatable_action';
	var col = meta.settings.aoColumns[meta.col];
	var actionUrl = col.action;
	if(typeof window[func] == 'function') {
		return window[func].call(window, data, type, row, col, meta)
	}
	var refer = col.refer;
	if(refer) {
		if(actionUrl)
			return "<a data-id='" + row[refer] + "' href='"+ Clips.siteUrl(actionUrl) + "/" + row[refer] +"'>"+ data +"</a>"
		else
			return "<a class='no_text_decoration' data-id='" + row[refer] + "'>"+ data +"</a>"
	}
	return "<a href='"+ Clips.siteUrl(actionUrl) + "/" + data +"'>"+ data +"</a>"
}

(function(){

    window.DataTableManager = {};

    var options = {
        key: 'datatable_select_'
    };

    function restoreSavedStates(datatable) {
        var savedStates = window.DatatableManager.getState(datatable);
        if(savedStates) {
            datatable.states = savedStates;
        }
        else {
            datatable.states = {};
        }
    }

    window.DataTableManager.getState = function(datatable) {
        var key = options.key;
        key += window.location.pathname;
        return JSON.parse(store.get(key));
    };

    window.DataTableManager.saveState = function(datatable, state) {
        var key = options.key;
        key += window.location.pathname;
        store.set(key, JSON.stringify(state));
    };

    window.DataTableManager.init = function(datatable) {

        restoreSavedStates(datatable);

        var psl = false;
        if(psl) {
            datatable.find("li").not('li.listview_item_template').each(function(i){
                var itemId = parseInt($(this).attr('itemId'));
                if ($.inArray(itemId, psl) > -1) {
                    $(this).addClass('ui-selected selected');
                }
            })
        }
        datatable.off('selectableselected').on('selectableselected', function(event, ui){
            datatable.states.selectedItems = $(ui.selected);
        });
        datatable.off('selectableunselected').on('selectableunselected', function(event, ui){
        })
    };

    window.DataTableManager.getSelectedItems = function(datatable) {
        return datatable.find('tbody').find('tr.ui-selected');
    };

    window.DataTableManager.getPrimaryKeyColumn = function(datatable, settings) {
        var primaryKey = [];
        var columns = settings.aoColumns;

        $.each(columns, function(index, col){
            if(col.hasOwnProperty('primary') && col.primary) {
                primaryKey.push(col);
            }
        });

        if(primaryKey.length == 0) {
            return columns[0];
        }

        return primaryKey[0];
    };

    window.DataTableManager.getPrimaryKeyColumnKey = function(datatable, settings) {
        var column = window.DataTableManager.getPrimaryKeyColumn(datatable, settings);
        return column.data;
    };

    window.DataTableManager.getPrimaryKeyColumnIndex = function(datatable, settings) {
        var column = window.DataTableManager.getPrimaryKeyColumn(datatable, settings);
        return column.idx;
    };

    window.DataTableManager.getSelectedItemsPrimaryKeys = function(datatable, settings) {
        var pk = window.DataTableManager.getPrimaryKeyColumnIndex(datatable, settings);
        var selectedItems = datatable.find('tr.ui-selected');
        var ctr = datatable.find('tr');
        var keys = [];
//				var data = datatable.DataTable().column(pk).data();

        if (selectedItems.length > 0) {
            selectedItems.each(function (i) {
                var self = $(this);
                var ctd = datatable.find('tr.ui-selected').eq(i).find('td').eq(pk);
                var dtd = datatable.DataTable().cell(ctd).data();

                if (dtd) {
                    keys.push(dtd);
                }
            });
        }

        if (keys.length > 0) {
            return keys;
        }

        return false;
    };

})();
