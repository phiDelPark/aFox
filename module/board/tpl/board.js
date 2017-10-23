/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

(function($) {
	'use strict';

	var input_password = '<form action="%s" class="input-password" method="post" autocomplete="off"><div class="form-inline"><div>%s</div><input class="form-control" name="mb_password" type="password" placeholder="%s" style="width:150px" required> <button class="btn btn-primary" type="submit">%s</button> <button class="btn btn-default" type="button">%s</button></div></form>';
	var confirm_action = '<form action="%s" class="input-group" method="post" autocomplete="off"><div><div>%s</div><button class="btn btn-primary" type="submit">%s</button> <button class="btn btn-default" type="button">%s</button></div></form>';

	$('[data-exec-act]').click(function() {
		var data = {},
			$i = $(this),
			act = $i.attr('data-exec-act'),
			args = $i.attr('data-act-param');

		if (!args) return false;
		args = args.split(',');

		for (var i = 0, n = args.length; i < n; i += 2) {
			data[args[i]] = args[i + 1];
		}

		var $arp = $i.closest('#bdReply'),
			$rp = $i.closest('.reply-item'),
			passform = $i.attr('data-act-password') || '';

		$arp.find('form.right').remove().end().find('.reply-item>.right').show();

		var $f = $arp.find('[data-exec-ajax="board.updateComment"]').clone().hide(),
			editor = $f.find('.af_editor_rp_content').afEditor();

		$f.find('.form-group').show();
		$f.find('[disabled="disabled"]').removeAttr('disabled');

		var getCommentCallfunc = function(status, data, xhr) {
			if (status == 'success') {
				$('<input type="hidden" name="rp_srl" value="">').prependTo($f);
				var $rc = $rp.find('>.right');
				if (data['mb_srl'] === '0') {
					var $mbinfo = $f.find('.area-mbinfo');
					$mbinfo.find('[name="mb_nick"],[name="mb_password"]').attr('required', 'required').end().removeClass('hide');
					$f.find('.af-editor-toolbar,.af-editor-content>textarea,.af-editor-content>iframe').removeAttr('readonly');
				}
				$f[0].dataImport(data);
				$f.addClass('right')
					.find('.close').show().click(
						function() {
							$rc.show('fast', function() {
								$f.remove();
							});
						}
					)
					.end().insertAfter($rc.hide('slow')).show('slow');
				$f.find('.btn-success').removeClass('btn-success').addClass('btn-info');
				if (data['rp_type'] == 2) editor.switch(true);
				$rp[0].scrollIntoView(true);
				return false;
			}
		};

		if (act == 'board.deleteComment' || (act == 'board.getComment' && passform)) {
			var $ipu, url = encodeURIComponent(current_url.setQuery('rp', ''));
			if ($rp.find('>>.inside_massage_box').length > 0) {
				return false;
			}
			if (passform) {
				if (act == 'board.getComment') {
					$ipu = $(input_password.sprintf('', $_LANG['request_input'].sprintf($_LANG['password']), $_LANG['password'], $_LANG['ok'], $_LANG['close']));
					$ipu.on('success.exec.ajax', function(e, data, xhr) {
						e.preventDefault();
						getCommentCallfunc('success', data, xhr);
						$(this).remove();
					});
				} else {
					$ipu = $(input_password.sprintf('', $_LANG['confirm_select_delete'].sprintf($_LANG['comment']), $_LANG['password'], $_LANG['ok'], $_LANG['close']));
				}
			} else {
				$ipu = $(confirm_action.sprintf('', $_LANG['confirm_select_delete'].sprintf($_LANG['comment']), $_LANG['yes'], $_LANG['no']));
			}
			$ipu.on('error.exec.ajax', function(e, msg, xhr) {
				e.preventDefault();
				$(e.currentTarget).find('>div>div').css('color', 'red').html($_LANG['error'] + ': ' + msg);
			}).hide().addClass('inside_massage_box').prependTo($rp.find('>.right')).fadeIn('slow');
			$ipu.find('button.btn-default').click(function() {
				$ipu.fadeOut('slow', function() {
					$(this).remove();
				});
			}).focus();
			$ipu.attr('data-exec-ajax', act);
			$ipu.prepend('<input type="hidden" name="rp_srl" value="' + data['rp_srl'] + '">');
			$ipu.prepend('<input type="hidden" name="success_return_url" value="' + url + '">');
			$ipu.addClass('inside_massage_box').prependTo($rp.find('>.right')).fadeIn('slow', function() {
				var $mb_password = $(this).find('[name="mb_password"]');
				if ($mb_password.length > 0) {
					$mb_password.focus();
				}
			});

		} else if (act == 'board.getComment' || act == 'board.updateComment') {

			if (act == 'board.getComment') {
				exec_ajax(act, data, getCommentCallfunc);
			} else {
				editor.switch(false);
				$('<input type="hidden" name="rp_parent" value="">').prependTo($f);
				$f[0].dataImport({
					'rp_type': 1,
					'rp_secret': 0,
					'rp_content': '',
					'rp_parent': data['rp_parent']
				});
				$f.addClass('right').insertAfter($rp.find('>.right')).show('slow');
			}
		}

		$f.on('success.exec.ajax', function(e, data, xhr) {
			e.preventDefault();
			if (data['redirect_url']) {
				parent.location.replace(data['redirect_url'].setQuery('rp', data['rp_srl']));
			}
		});

		return false;
	});

	$('[data-exec-ajax="board.updateDocument"],[data-exec-ajax="board.updateComment"]')
		.on('success.exec.ajax', function(e, data, xhr) {
			e.preventDefault();
			var isd = $(this).attr('data-exec-ajax') == 'board.updateDocument';
			if (data['redirect_url']) {
				parent.location.replace(data['redirect_url'].setQuery(
					isd ? 'srl' : 'rp', data[isd ? 'wr_srl' : 'rp_srl']
				));
			}
		});

	$(document).on('change.af.editor.toolbar', '.af-editor-toolbar', function(e, tar, old, val) {
		var $e = $(this).closest('.af-editor-group');
		if ((tar == 'wr_type' || tar == 'rp_type') && $e.length == 1) $e.data('af.editor').switch(val === '2');
	});

	$('.list-table tr[data-hot-track]').click(function() {
		var $i = $('.wr_title a', this),
			href = $i.attr('href');
		if (href == '#' && $i.attr('data-target') == '#passwordBoxModal') {
			$($i.attr('data-target')).modal('show', $i);
		} else {
			location.href = href;
		}
		return false;
	});

	$('#passwordBoxModal', '#bdList').on('show.bs.modal', function(e) {
		if (typeof e.relatedTarget == 'undefined') {
			e.preventDefault();
			return false;
		}

		var $modal = $(this),
			$form = $modal.find('form'),
			$relatedTarget = $(e.relatedTarget),
			srl = $relatedTarget.attr('data-srl') || '',
			param = ($relatedTarget.attr('data-param') || '').split(','),
			url = current_url;

		$form
			.find('.modal-body>p.error').remove().end()
			.find('.modal-body>p').show().end()
			.find('input[name="mb_password"]').val('');

		if (srl && param.length > 1) {
			url = url.setQuery(param);
			$form
				.on('submit', function() {
					// 체크가 성공하면 이동
					if ($form.data('check success') || false) {
						return true;
					}
					var data = {},
						response_tags = ['mb_password'];
					data['wr_srl'] = srl;
					data['mb_password'] = $modal.find('input[name="mb_password"]').val();
					exec_ajax('board.checkpassword', data, function(status, data, xhr) {
						$form.data('check success', status === 'success');
						if (status === 'success') {
							$form.submit();
							return false;
						} else if (status === 'error') {
							var $err = $form.find('.modal-body>p.error');
							if ($err.length === 0) {
								$err = $('<p class="error" style="color:red">');
								$form.find('.modal-body>p').hide().after($err);
							}
							$err.html($_LANG['error'] + ': ' + data);
							return false;
						}
					}, response_tags);
					return false;
				}).attr('action', url);
		}
	}).on('shown.bs.modal', function(e) {
		$(this).find('[name="mb_password"]').focus();
	});

	$(window).on('load', function() {
		var into = $('a.active[id^=reply_]')[0];
		if (into) into.scrollIntoView(true);
	});

})(jQuery);
