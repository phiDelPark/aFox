/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

(function($) {
	'use strict';

	$(window).load(function() {
		var loginformhtml = '<form action="%s" method="post" autocomplete="off"><div class="form-group"><input type="text" class="form-control" name="mb_id" maxlength="20" placeholder="%s" pattern="^[a-zA-Z]+\\w{2,}$" required></div><div class="form-group"><input type="password" class="form-control" name="mb_password" placeholder="%s" required></div><div class="captcha-group" style="display:none"></div><label class="checkbox" tabindex="0"><input type="checkbox" name="auto_login" value="1"><span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span> %s</label><p></p><button type="submit" style="display:none">button</button></form>';
		var captchahtml = ('<div class="captcha-group clearfix"><div class="form-group pull-left"><img src="%s" alt="CAPTCHA code" width="162" height="77"></div><div class="form-group pull-right"><input type="text" class="form-control" placeholder="%s" name="captcha_code" required></div></div>').sprintf('./common/img/loader2.gif', $_LANG['captcha_code']);

		function af_get_captcha($form) {
			exec_ajax('member.getCaptcha', {}, function(status, data, xhr) {
				if (status === 'success') {
					$form.find('>.captcha-group img').attr('src', './lib/' + data['src']);
					return false;
				} else if (status === 'error') {
					$form.find('>p').html($_LANG['error'] + ': ' + data['message']).css('color', 'red');
					return false;
				}
			});
		}

		$('a[href="#loginForm"]').offOn('click', function() {
			var is_captcha = ($(this).attr('captcha') || '0') == '1';
			loginformhtml = loginformhtml.sprintf(current_url, $_LANG['id'], $_LANG['password'], $_LANG['auto_login']);
			msg_box(loginformhtml, $_LANG['login'], ['question', ['cancel', 'OK']], function(key, $body) {
				if (key == 'show') {
					var $m = $body.closest('[aria-labelledby="afMsgBox"]');
					$m.find('.modal-header .glyphicon').removeClass().addClass('glyphicon glyphicon-user');
					$m.find('.modal-footer [data-key="ok"]').text($_LANG['login']);
					$m.find('.modal-footer').prepend('<div class="pull-left"><a href="' + request_uri + '?module=member&disp=signUp"><strong>' + $_LANG['member_signup'] + '</strong></a> / <a href="' + request_uri + '?module=member&disp=findAccount">' + $_LANG['member_find'] + '</a></div>');
					if (is_captcha) {
						af_get_captcha($body.find('form'));
						var $auth = $body.find('form').find('>.captcha-group');
						$auth.html(captchahtml).show();
					}
				} else if (key == 'ok') {
					var $form = $body.find('form');
					$form
						.offOn('submit', function() {
							var data = {};
							data['mb_id'] = $form.find('input[name="mb_id"]').val();
							data['mb_password'] = $form.find('input[name="mb_password"]').val();
							data['auto_login'] = $form.find('input[name="auto_login"]').is(':checked');
							if ($form.find('input[name="captcha_code"]').length > 0) {
								data['captcha_code'] = $form.find('input[name="captcha_code"]').val();
							}
							exec_ajax('member.loginCheck', data, function(status, data, xhr) {
								if (status === 'success') {
									location.href = $form.attr('action');
									return false;
								} else if (status === 'error') {
									if (data['error'] == '4001') {
										af_get_captcha($form);
										var $auth = $form.find('>.captcha-group');
										$auth.html(captchahtml).show();
									}
									$form.find('>p').html($_LANG['error'] + ': ' + data['message']).css('color', 'red');
									$form.find('input[name="captcha_code"]').val('');
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

		$('[aria-labelledby="afPageLoader"]').fadeOut("slow");
	});

})(jQuery);
