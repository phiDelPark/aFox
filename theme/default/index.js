/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

(function($) {
	'use strict';

	$(window).on('load', function() {
		var loginformhtml = '<form action="%s" method="post" autocomplete="off"><div class="form-group"><input type="text" class="form-control" name="mb_id" minlength="2" maxlength="20" placeholder="%s" required></div><div class="form-group"><input type="password" class="form-control" name="mb_password" placeholder="%s" required></div><div class="captcha-group" style="display:none"></div><label class="checkbox" tabindex="0"><input type="checkbox" name="auto_login" value="1"><span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span> %s</label><p></p><button type="submit" style="display:none">button</button></form>';
		var captchahtml = ('<div class="captcha-group clearfix"><div class="form-group pull-left"><img src="%s" alt="CAPTCHA code" width="162" height="77"></div><div class="form-group pull-right"><input type="text" class="form-control" placeholder="%s" name="captcha_code" required></div></div>').sprintf('./common/img/loader.gif', $_LANG['captcha_code']);

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

		$('aside[role="menu"]:eq(0)').each(function() {
			var $this = $(this);
			$this.find('a.hidden-md').offOn('click', function() {
				var $lst = $this.find('.list-group.hidden-xs');
				if ($lst.length > 0) {
					$lst.addClass('collapse')
						.removeClass('hidden-xs hidden-sm')
						.animate({
							height: "toggle"
						}, function() {
							$(this).addClass('in').removeAttr('style');
						});
				} else {
					$this.find('.list-group.collapse.in')
						.animate({
							height: "toggle"
						}, function() {
							$(this).addClass('hidden-xs hidden-sm')
								.removeClass('collapse in')
								.removeAttr('style');
						});
				}
			});
		});

		$('#af_md_config').each(function() {
			var $this = $(this),
				$btn = $this.find('.btn-toggle');
			$btn.find('.glyphicon-ok').offOn('click', function() {
				if (confirm($_LANG['confirm_save'].sprintf($_LANG['setup'])) === true) {
					$this.find('form[data-exec-ajax]:eq(0)').submit();
				}
			});
			$btn.find('.glyphicon-cog').offOn('click', function() {
				var w1 = $this.find('.config-area').width(),
					w2 = $this.find('#config_menus').width();
				$this.find('.config-area').animate({
					width: w1 != 0 ? 0 : w2
				});
			});
			$this.find('#config_menus li>a:first-child').offOn('click', function() {
				var $el = $(this).parent().find('>div');
				if ($el.css('display') == 'none') {
					$this.find('#config_menus li>div').hide();
					$el.animate({
						height: "toggle"
					});
				}
				return false;
			});
		});

		$('[aria-labelledby="afPageLoader"]').fadeOut("slow");
	});

})(jQuery);
