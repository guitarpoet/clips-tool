<?php namespace CLips\Widgets\Datepicker; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Widget extends \Clips\Widget {
    protected function doInit() {
        $js = <<<TEXT
        $('[data-role=date]').each(function(){
            $(this).datetimepicker({
            });
        });

        $('[data-role=month]').each(function(){
            $(this).datetimepicker({
            	"viewMode": "months"
            });
        });

        $('[data-role=year]').each(function(){
            $(this).datetimepicker({
            	"viewMode": "years"
            });
        });

        $('[data-role=day]').each(function(){
            $(this).datetimepicker({
            	"viewMode": "days"
            });
        });

        $('[data-role*=dategroup]').each(function(){
        	alert(1);
            if ($(this).attr('data-for')) {
                var self = $(this);
                var datetimepickerApplySelector = '#datetimepicker-' + $(this).attr('data-for');
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
