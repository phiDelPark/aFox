/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

(function($) {
	'use strict';

	$(window).load(function() {
		//window.alert = function (message) { msg_box(message); };

		$('a[href="#loginForm"]').on('click', function() {
			var html = '<form action="%s" method="post" autocomplete="off"><div class="form-group"><input type="text" class="form-control" name="mb_id" maxlength="20" placeholder="%s" pattern="^[a-zA-Z]+\\w{2,}$" required> <span class="sr-only">%s</span></div><div class="form-group"><input type="password" class="form-control" name="mb_password" placeholder="%s" required /> <span class="sr-only">%s</span></div><label class="checkbox" tabindex="0"><input type="checkbox" name="auto_login" value="1"><span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span> %s</label><p></p><button type="submit" style="display:none">button</button></form>';

			msg_box(html.sprintf(current_url, $_LANG['id'], $_LANG['id'], $_LANG['password'], $_LANG['password'], $_LANG['auto_login']), $_LANG['login'], ['question', ['cancel', 'OK']], function(key, $body) {
				if (key == 'show') {
					var $m = $body.closest('#afMessageBox');
					$m.find('.modal-header .glyphicon').removeClass().addClass('glyphicon glyphicon-user');
					$m.find('.modal-footer [data-key="ok"]').text($_LANG['login']);
					$m.find('.modal-footer').prepend('<div class="pull-left"><a href="' + request_uri + '?module=member&disp=signUp"><strong>' + $_LANG['member_signup'] + '</strong></a> / <a href="' + request_uri + '?module=member&disp=findAccount">' + $_LANG['member_find'] + '</a></div>');
				} else if (key == 'ok') {
					var $form = $body.find('form');
					// 이벤트 중복 실행 방지
					$form.off('submit');
					$form
						.on('submit', function() {
							var data = {};
							data['mb_id'] = $form.find('input[name="mb_id"]').val();
							data['mb_password'] = $form.find('input[name="mb_password"]').val();
							data['auto_login'] = $form.find('input[name="auto_login"]').is(':checked');
							exec_ajax('member.loginCheck', data, function(status, data, xhr) {
								if (status === 'success') {
									location.href = $form.attr('action');
									return false;
								} else if (status === 'error') {
									$form.find('>p').html($_LANG['error'] + ': ' + data).css('color', 'red');
									$form.find('input[name="mb_password"]').val('');
									$form.find('input[name="mb_id"]').focus();
									return false;
								}
							});
							return false;
						});
					$form.find('button[type="submit"]').click();
				} else {
					return true;
				}
				return false;
			});

			return false;
		});

		$("#bsPreLoader").fadeOut("slow");
	});

})(jQuery);
