<?php namespace CLips\Widgets\Datepicker; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Widget extends \Clips\Widget {
    protected function doInit() {
        $js = <<<TEXT
function strToObj(str){
	var json = (new Function("return " + str))();
	return json;
}

function dataToObj(str) {
	var jsonstr = str.trim().replace(/\{/, '');
	jsonstr = '{ ' + jsonstr.substr(0, jsonstr.length - 1) + ' }';
	return strToObj(jsonstr);
}

$('[data-role*=date]').each(function(){
	var options = {};
	if($(this).attr('data-date-options')) {
		options = dataToObj($(this).attr('data-date-options'));
	}
	var opt = $.extend({}, options);
	$(this).datetimepicker(opt);
});

$('[data-role*=month]').each(function(){
	$(this).datetimepicker({
		"viewMode": "months"
	});
});

$('[data-role*=year]').each(function(){
	$(this).datetimepicker({
		"viewMode": "years"
	});
});

$('[data-role*=day]').each(function(){
	$(this).datetimepicker({
		"viewMode": "days"
	});
});

$('[data-role*=dategroup]').each(function(){
	if ($(this).attr('data-for')) {
		var self = $(this);
		var datetimepickerApplySelector = 'input[name=' + $(this).attr('data-for') + ']';
		$(datetimepickerApplySelector).on("dp.change",function (e) {
			self.data("DateTimePicker").minDate(e.date);
		});
		self.on("dp.change",function (e) {
			$(datetimepickerApplySelector).data("DateTimePicker").maxDate(e.date);
		});
	}
});
TEXT;
        \Clips\context('jquery_init', $js, true);
    }
}
