/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

+function ($) {
  'use strict';

	$('.auto-hide').each(function() {
		var $i = $(this),
			stime = ($i.attr('data-timer') || '5') - 1,
			$prg = $i.find('.timer-progress') || 0,
			srpt = $prg.attr('data-repeat-char') || '*',
			time = stime,
			timerId = 0;

			var _repeat_char= function() {
				var r = srpt.repeat(time);
				r = r + '<u>' + srpt + '</u>' + srpt.repeat(stime-time);
				return r;
			};

			if($prg) $prg.html(_repeat_char());
			timerId = setInterval(function(){
				if(time < 1){
					clearInterval(timerId);
					$i.animate({
						bottom: '-=70'
					}, 1000, function() {
						$(this).remove();
					});
				} else {
					time--;
					if($prg) $prg.html(_repeat_char());
				}
			}, 1000);
	});

}(jQuery);