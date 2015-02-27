;(function($){
    if($.isFunction($.fn.selectBoxIt)) {
        $("select:not([data-no-selectBoxIt])").each(function(){
            $(this).selectBoxIt();
        });
    }
})(jQuery);

