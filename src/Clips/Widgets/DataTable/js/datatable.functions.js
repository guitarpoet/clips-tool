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
