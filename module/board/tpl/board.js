/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

+function ($) {
  'use strict';

	$('[data-exec-act]').click(function() {
		var data = {},
			$i = $(this),
			act = $i.attr('data-exec-act'),
			args = $i.attr('data-act-param');

		if(!args) return false;
		args = args.split(',');

		for (var i = 0, n = args.length; i < n; i+=2) {
			data[args[i]] = args[i+1];
		}

		if(act == 'board.getComment' || act == 'board.updateComment') {

			var $arp = $i.closest('#board_reply');
			$arp.find('form.right').remove().end().find('.reply-item>.right').show();
			var $f = $arp.find('[data-exec-ajax="board.updateComment"]').clone().hide(),
				$rp = $i.closest('.reply-item');

			$f.find('.form-group').show();
			$f.find('[disabled="disabled"]').removeAttr('disabled');

			var editor = $f.find('.af_editor_rp_content').afEditor();

			if(act == 'board.getComment'){
				var nombsrl = $i.attr('data-act-option') || '';
				if(nombsrl) {
					var r = prompt($_LANG['warn_input'].sprintf([$_LANG['password']]), '');
					if (r == null) return false;
					data['mb_password'] = r;
				}
				exec_ajax(act, data, function(status, data, xhr) {
					if(status == 'success') {
						$('<input type="hidden" name="rp_srl" value="">').prependTo($f);
						var $rc = $rp.find('>.right');
						$f[0].dataImport(data);
						$f.addClass('right')
						.find('.close').show().click(
								function() {
									$rc.show('fast', function(){
										$f.remove();
									});
								}
							)
						.end().insertAfter($rc.hide('slow')).show('slow');
						$f.find('.btn-success').removeClass('btn-success').addClass('btn-info');
						if(data['rp_type'] == 2) editor.switch(true);
						return false;
					}
				});
			} else {
				editor.switch(false);
				$('<input type="hidden" name="rp_parent" value="">').prependTo($f);
				$f[0].dataImport({'rp_type':1,'rp_secret':0,'rp_content':'','rp_parent':data['rp_parent']});
				$f.addClass('right').insertAfter($rp.find('>.right')).show('slow');
			}

			$f.on('success.exec.ajax', function(e, data, xhr){
				e.preventDefault();
				if(data['redirect_url']) {
					parent.location.replace(data['redirect_url'].setQuery('rp',data['rp_srl']));
				}
			});

		} else if(act == 'board.deleteComment'){
			var nombsrl = $i.attr('data-act-option') || '';
			if(nombsrl) {
				var r = prompt($_LANG['warn_input'].sprintf([$_LANG['password']]), '');
				if (r == null) return false;
				data['mb_password'] = r;
			} else {
				if (!confirm($_LANG['confirm_select_delete'].sprintf([$_LANG['comment']]))) return false;
			}
			exec_ajax(act, data, function(status, data, xhr){
				switch(status) {
					case 'error':
						break;
					case 'success':
							parent.location.replace(current_url.setQuery('rp',''));
							return false;
						break;
				}
			});
		}

		return false;
	});

	$('[data-exec-ajax="board.updateDocument"],[data-exec-ajax="board.updateComment"]')
		.on('success.exec.ajax', function(e, data, xhr){
			e.preventDefault();
			var isd = $(this).attr('data-exec-ajax') == 'board.updateDocument';
			if(data['redirect_url']) {
				parent.location.replace(data['redirect_url'].setQuery(
					isd?'srl':'rp', data[isd?'wr_srl':'rp_srl']
				));
			}
		});

	$(document).on('change.af.editor.toolbar', '.af-editor-toolbar', function(e, tar, old, val){
		var $e = $(this).closest('.af-editor-group');
		if((tar == 'wr_type' || tar == 'rp_type') && $e.length == 1) $e.data('af.editor').switch(val==='2');
	});

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

	$('.list-table tr[data-hot-track]').click(function() {
		var href = $('.wr_title a', this).attr('href');
		location.href = href;
		return false;
	});

	$(window).on('load', function () {
		var into = $('a.active[id^=reply_]')[0];
		if(into) into.scrollIntoView(true);
	});

}(jQuery);