$(function(){
	$('.pinet_banner').each(function(){
		var self = $(this);
		var src = $(this).attr('src');
		var strategy = self.attr('strategy');
		var i = $(document.createElement('img'));
		i.load(function(){
			var img = $(this);
			var css = {};
			css.height = img.height();
			css.background = 'url(' + src + ') center center no-repeat';

			switch(strategy) {
				case 'top':
					css['background-size'] = '100%';
					css.background = 'url(' + src + ') center top no-repeat';
					break;
				case 'bottom':
					css['background-size'] = '100%';
					css.background = 'url(' + src + ') center bottom no-repeat';
					break;
				case 'center':
					css['background-size'] = '100%';
					break;
			}
			self.css(css);
			img.remove();
		});
		i.attr('src', src);
		i.css('display', 'none');
		$(this).append(i);
	});
})
