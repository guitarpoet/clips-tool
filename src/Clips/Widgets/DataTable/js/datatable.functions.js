function datatable_action_column(data, type, row, meta) {
	var col = meta.settings.aoColumns[meta.col];
	var actionUrl = col.action;
	var refer = col.refer;
	console.info('The refer for col ' + col.data + ' is ' + refer);
	if(refer) {
		if(actionUrl)
			return "<a data-id='" + row[refer] + "' href='"+ actionUrl + "/" + row[refer] +"'>"+ data +"</a>"
		else
			return "<a class='no_text_decoration' data-id='" + row[refer] + "'>"+ data +"</a>"
	}
	return "<a href='"+ actionUrl + "/" + data +"'>"+ data +"</a>"
}

$(".datatable").on('init.dt', function(){
    $(this).each(function(){
        var self = $(this);
        
        self.find("tbody").selectable({
            delay: 1
        });

        self.on('click', 'tr', function(){
            $(this).addClass('ui-selected').siblings().removeClass('ui-selected');
        });
    });
});


