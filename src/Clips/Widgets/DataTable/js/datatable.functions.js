function datatable_action_column(data, type, row, meta) {
	var col = meta.settings.aoColumns[meta.col];
	var actionUrl = col.action;
	var refer = col.refer;
	console.info('The refer for col ' + col.data + ' is ' + refer);
	if(refer) {
		if(actionUrl)
			return "<a data-id='" + row[refer] + "' href='"+ Clips.siteUrl(actionUrl) + "/" + row[refer] +"'>"+ data +"</a>"
		else
			return "<a class='no_text_decoration' data-id='" + row[refer] + "'>"+ data +"</a>"
	}
	return "<a href='"+ CLips.siteUrl(actionUrl) + "/" + data +"'>"+ data +"</a>"
}




