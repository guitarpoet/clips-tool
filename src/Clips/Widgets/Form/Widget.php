<?php namespace Clips\Widgets\Form; in_array(__FILE__, get_included_files()) or exit("No direct sript access allowed");

class Widget extends \Clips\Widget {
    protected function doInit() {
        $js = <<<TEXT
            $('input,select,textarea').not('[type=submit]').jqBootstrapValidation();
            if($.isFunction($.fn.selectBoxIt)){
                $('select:not([data-no-selectBoxIt])').each(function(){
                    $(this).selectBoxIt({});
                });
            }
TEXT;
        \Clips\context('jquery_init', $js, true);
    }
}
