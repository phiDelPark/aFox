/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

if (typeof jQuery === 'undefined') {
	if (!document.getElementById('def-jQuery-JS')) parent.location.replace(request_uri + '?cdnerr=jquery');
	throw new Error('aFox\'s JavaScript requires jQuery');
} else if (typeof $('body').alert === 'undefined') {
	if (!document.getElementById('def-Bootstrap-JS')) parent.location.replace(request_uri + '?cdnerr=bootstrap');
	throw new Error('aFox\'s JavaScript requires Bootstrap');
}

var $_LANG = {};

(function($) {
	'use strict';
	/*
		var version = $.fn.jquery.split(' ')[0].split('.');
		if ((version[0] < 2 && version[1] < 9) || (version[0] == 1 && version[1] == 9 && version[2] < 1) || (version[0] > 2)) {
			throw new Error('aFox\'s JavaScript requires jQuery version 1.9.1 or higher, but lower than version 3');
		}
	*/

	jQuery.fn.offOn = function(types, selector, data, fn) {
		return this
			.off(types, (typeof selector === 'function') ? undefined : selector)
			.on(types, selector, data, fn);
	};

	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, "");
	};

	String.prototype.toUcFirst = function() {
		var s = this.toLowerCase();
		return s.charAt(0).toUpperCase() + s.slice(1);
	};

	String.prototype.sprintf = function() {
		var s = this,
			a = arguments;
		a = (a.length === 1 && typeof a[0] === 'object') ? a[0] : a;
		for (var i = 0, n = a.length; i < n; i++) {
			s = s.replace(/%([0-9]?)(s|d)/, function(x, y, z) {
				var out = a[i] + '';
				if (Number(y || 0) > 0) {
					var t1 = z == 'd' ? '0' : '_';
					while (out.length < y) out = t1 + out;
				}
				return out;
			});
		}
		return s;
	};

	// php.js
	String.prototype.stripTags = function(allowed) {
		allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
		var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
			commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
		return this.replace(commentsAndPhpTags, '').replace(tags, function($0, $1) {
			return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
		});
	};

	String.prototype.escapeMKDW = function(full) {
		var rex = full === false ? /[\`\[\]]/g : /[\\\`\*\_\{\}\[\]\(\)\>\#\+\-\.\!]/g;
		return this.replace(rex, function(s) {
			return '\\' + s;
		});
	};

	String.prototype.escapeHtml = function() {
		var es = {
			"&": "&amp;",
			"<": "&lt;",
			">": "&gt;",
			'"': '&quot;',
			"'": '&#39;',
			"/": '&#x2F;'
		};
		return this.replace(/[&<>"'\/]/g, function(s) {
			return es[s];
		});
	};

	String.prototype.getQuery = function(s) {
		var u = decodeURIComponent(this).replace(/&amp;/g, '&'),
			p = {};
		u.replace(/[?&]+([^=&]+)=([^&]*)/g, function(a, k, v) {
			p[k] = v;
		});
		return s ? (p[s] || '') : p;
	};

	String.prototype.setQuery = function() {
		var u = decodeURIComponent(this).replace(/&amp;/g, '&'),
			a = arguments,
			n = a.length;
		if (n === 0) return u;
		if (typeof a[0] === 'object') {
			a = a[0];
			n = a.length;
		}
		var p = (a[0] !== '') ? u.getQuery() : {},
			r = [],
			v;
		for (var i = (a[0] === '' ? 1 : 0); i < n; i += 2) {
			p[a[i]] = a[i + 1];
		}
		for (var k in p) {
			if (!p.hasOwnProperty(k)) continue;
			if (!(v = String(p[k]).trim())) continue;
			r.push(k + '=' + v);
		}
		var x = u.indexOf('?');
		u = (x == -1 ? u : u.substr(0, x));
		if (u.substr(-1, 1) != '/') u = u + '/';
		return u + (r.length > 0 ? '?' + r.join('&') : '');
	};

	Number.prototype.shortFileSize = function() {
		var s = this,
			t = ['B', 'KB', 'MB', 'GB'],
			i = 0;
		for (var n = 4; i < n; i++) {
			if (s <= 1024) break;
			s = s / 1024;
		}
		return s.toFixed(1) + t[i];
	};

	Number.prototype.withCommas = function() {
		var n = String(this),
			minus = n.indexOf('-') === 0;
		n = n.replace(/[^0-9]/g, '').split("").reverse().join("").replace(/(.{3})/g, "$1,").split("").reverse().join("");
		return (minus ? '-' : '') + ((n.substring(0, 1) == ",") ? n.substring(1, n.length) : n);
	};

	HTMLFormElement.prototype.dataExport = function() {
		var name, value, data = {},
			arrs = $(this).serializeArray();
		for (var v in arrs) {
			name = arrs[v]['name'];
			value = arrs[v]['value'];
			if (name.substring(name.length - 2) === '[]') {
				var $item = $('[name="' + name + '"]');
				if ($item.eq(0).is(':checkbox') || $item.eq(0).is(':radio')) {
					$item = $('[name="' + name + '"]:checked');
				}
				value = $item.map(function() {
					return this.value;
				}).get();
				name = name.slice(0, -2);
			}
			data[name] = value;
		}
		return data;
	};

	HTMLFormElement.prototype.dataImport = function(data) {
		var $item, $target, html;
		for (var key in data) {
			html = '';
			$item = $(this).find('[name=' + key + ']');
			if ($item.is("input")) {
				if ($item.is(':file')) {
					$target = $item.parent().parent().parent();
					if ($target.hasClass('uploader-group')) {
						$target.find('input:file').val('');
						if (data[key]) {
							if ($.isArray(data[key])) {
								$target.addClass('file-list').find('input:file').attr('multiple', '1');
								for (var v in data[key]) {
									html += '<i class="file-item" title="' + v['title'] + '" data-type="' + v['type'] + '"></i>';
								}
							} else {
								html = '<i class="file-item">' + data[key] + '</i>';
							}
						}
						$target.find('.file-caption').html(html ? html : ($target.attr('placeholder') || ''));
					}
				} else if ($item.is(':checkbox')) {
					$item[0].checked = data[key] == 1;
				} else if ($item.is(':radio')) {
					$item.filter(function() {
						return this.value === data[key];
					}).attr('checked', 'checked');
				} else {
					$item.val(data[key] == '0' ? '' : data[key]);
				}
			} else if ($item.is("select") || $item.is("textarea")) {
				$item.val(data[key]);
			}
		}

		//에디터가 있을시에
		var $editor = $(this).find('.af-editor-group');
		if ($editor.length > 0) {
			$editor.find('.af-editor-toolbar input:hidden').each(function() {
				var $i = $(this),
					$p = $i.closest('.af-editor-toolbar'),
					target = $i.attr('name'),
					value = $i.val();
				$p.find('[data-target="' + target + '"]').find('.glyphicon').addClass('glyphicon-unchecked').removeClass('glyphicon-check');
				$p.find('[data-target="' + target + '"][data-value="' + value + '"]').find('.glyphicon').addClass('glyphicon-check').removeClass('glyphicon-unchecked');
			});
			//업로드된 파일
			var $uploads = $editor.find('[name="upload_files[]"]');
			if ($uploads.length > 0 && $.isArray(data['files']) && data['files'].length > 0) {
				html = '<div class="form-group has-feedback" style="margin-bottom:5px"><div class="af-editor-uploaded uploader-group file-list form-control" style="margin-top:10px">';
				$.map(data['files'], function(v, i) {
					html += '<i class="file-item" draggable="true" title="' + v.mf_name.escapeHtml() + ' (' + Number(v.mf_size).shortFileSize() + ')" data-type="' + v.mf_type + '" data-srl="' + v.mf_srl + '"></i>';
				});
				html += '</div><span class="glyphicon glyphicon-question-sign form-control-feedback" style="pointer-events:auto;cursor:pointer" tabindex="0"></span></div>';
				$uploads.closest('.af-editor-uploader').prepend(html);
			}
		}
	};

	// ?et_cookie 함수에서 md5, base64 를 적용하려면 jquery.md5.min.js 를 로드해야합니다.
	// 참고로 php 에서 사용되는 ?et_cookie 함수는 md5, base64 를 기본 적용하기에 서로 상호작용 하려면 jquery.md5.min.js 를 로드해야합니다.
	$.set_cookie = window.set_cookie = function(name, value, expire) {
		if (typeof $.md5 !== 'undefined') name = $.md5(name);
		if (typeof $.base64 !== 'undefined') value = $.base64.encode(value);
		if (expire) {
			var date = new Date();
			date.setTime(date.getTime() + (expire * 24 * 60 * 60 * 1000));
			expire = "; expires=" + date.toGMTString();
		} else {
			expire = '';
		}
		document.cookie = name + "=" + encodeURIComponent(value) + expire + "; path=/";
	};
	$.get_cookie = window.get_cookie = function(name) {
		if (typeof $.md5 !== 'undefined') name = $.md5(name);
		var pair = document.cookie.match(new RegExp(name + '=([^;]+)'));
		var value = !!pair ? decodeURIComponent(pair[1]) : null;
		if (value !== null && typeof $.base64 !== 'undefined') value = $.base64.decode(value);
		return value;
	};

	$.msg_box = window.msg_box = function(text, caption, type, callback) {
		var html = '<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="afMsgBox" aria-hidden="true" style="z-index:99999"><div class="modal-dialog"><div class="modal-content"><div class="modal-header" id="afMsgBox"><i class="glyphicon glyphicon-%s" aria-hidden="true"></i> %s</div><div class="modal-body"></div><div class="modal-footer">%s</div></div></div></div>',
			icons = {
				'info': 'info-sign',
				'question': 'question-sign',
				'asterisk': 'asterisk',
				'warning': 'exclamation-sign'
			},
			buttons = {
				'ok': $_LANG['ok'] || 'Ok',
				'cancel': $_LANG['cancel'] || 'Cancel',
				'yes': $_LANG['yes'] || 'Yes',
				'no': $_LANG['no'] || 'No'
			},
			btnTmp = '<button type="button" class="btn btn-default%s" data-key="%s">%s</button>',
			button = btnTmp.sprintf(' btn-primary" data-dismiss="modal', 'ok', buttons['ok']);
		if (!caption) caption = ($('title').text().split('-') || ['Alert'])[0];
		if (type && type[1]) {
			button = '';
			for (var i in type[1]) {
				var k = type[1][i].toLowerCase();
				button = button + btnTmp.sprintf((k.toUpperCase() === type[1][i] ? ' btn-primary' : ''), k, buttons[k]);
			}
		}
		html = html.sprintf(icons[type ? type[0] : 'warning'], caption, button);
		var $focus = $(':focus');
		// modal-body 는 따로 입력해야 치환이 정상적으로 됨
		var $modal = $(html).find('.modal-body:eq(0)').html(text).end();
		$modal.prependTo('body')
			.offOn('show.bs.modal', function(e) {
				if ($.isFunction(callback)) {
					callback('show', $modal.find('.modal-body'));
				}
			})
			.offOn('hide.bs.modal', function(e) {
				if ($.isFunction(callback)) {
					callback('hide', $modal.find('.modal-body'));
				}
			})
			.offOn('shown.bs.modal', function(e) {
				$(this).find('.modal-body input[type!="hidden"]:eq(0)').focus();
			})
			.offOn('hidden.bs.modal', function(e) {
				$(this).remove();
				$focus.focus();
			})
			.offOn('keydown', function(e) {
				if (e.which == 13 && e.target.tagName != 'TEXTAREA') {
					e.preventDefault();
					$(this).find('.modal-footer>button.btn-primary').click();
				}
			})
			.modal('show')
			.find('.modal-footer>button')
			.offOn('click', function(e) {
				if ($.isFunction(callback)) {
					if (callback($(this).attr('data-key'), $modal.find('.modal-body'))) {
						$modal.modal('hide');
					} else {
						return false;
					}
				}
			});
		return false;
	};

	$.pop_win = window.pop_win = function(url, w, h, id) {
		var popwin = window.open(url, (id || 'af_popup'), 'width=' + (w || '700') + ',height=' + (h || '500') + ',top=50,left=50,scrollbars=yes,toolbar=no,menubar=no,location=no');
		popwin.focus();
		return popwin;
	};

	$.exec_ajax = window.exec_ajax = function(self, param, callback, responses) {
		var $i = $(typeof self === 'string' ? 'BODY' : self),
			act = ((typeof self === 'string' ? self : $i.attr('data-exec-ajax')) || '').split('.');

		if (act.length != 2) return;
		if ($i.data('actioning')) return;
		$i.data('actioning', true);

		var isform = self.tagName == 'FORM',
			multipart = ($i.attr('enctype') || '') == 'multipart/form-data',
			data = param || {};

		var $waiting = $('<div class="af_waiting_message alert alert-warning" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' + (($_LANG && $_LANG['calling_server']) ? $_LANG['calling_server'] : 'Please wait...') + '<div class="progress"><div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" style="width:100%"></div></div></div>');
		$waiting.hide().appendTo('body').fadeIn(1500);


		if (typeof(responses) == "undefined" || responses.length < 1) {
			responses = [];
		}

		if (isform && multipart) {
			data = new FormData($i[0]);
			data.append('module', act[0]);
			data.append('act', act[1]);
			data.append('response_tags', responses);
		} else {
			if (isform) {
				data = $i[0].dataExport();
			} else {
				var arr = ($i.attr('data-ajax-param') || '').split(',');
				if (arr.length > 1) {
					for (var i = 0, n = arr.length; i < n; i += 2) data[arr[i]] = arr[i + 1];
				}
			}
			$.extend(data, {
				module: act[0],
				act: act[1],
				response_tags: responses
			});
			data = JSON.stringify(data);
		}

		try {
			$.ajax({
				type: "POST",
				url: request_uri,
				dataType: multipart ? false : "json",
				contentType: multipart ? false : "application/json",
				processData: multipart ? false : true,
				data: data,
				beforeSend: function(xhr, settings) {
					var cancel = false;
					if ($.isFunction(callback)) {
						cancel = callback('before', settings, xhr) === false;
					} else {
						var ev = $.Event('before.exec.ajax');
						$i.trigger(ev, [settings, xhr]);
						cancel = ev.isDefaultPrevented();
					}
					if (cancel) {
						$waiting.remove();
						$i.data('actioning', false);
						return false;
					}
				},
				complete: function(xhr, status) {
					$i.data('actioning', false);

					if ($.isFunction(callback)) {
						callback('complete', status, xhr);
					} else {
						$i.trigger('complete.exec.ajax', [status, xhr]);
					}
					$waiting.remove();
				},
				success: function(data, status, xhr) {
					var ev, cancel = false;
					if (typeof data == 'string') {
						data = [];
						data['error'] = '1';
						data['message'] = xhr.responseText.replace(/<[^>]+>/g, '');
					} else {
						if (!data['error']) data['error'] = '0';
						if (!data['message']) data['message'] = '';
					}

					$i.data('actioning', false);
					var err = data['error'] !== '0';

					if ($.isFunction(callback)) {
						cancel = callback(err ? 'error' : 'success', data, xhr) === false;
					} else {
						ev = $.Event(err ? 'error.exec.ajax' : 'success.exec.ajax');
						$i.trigger(ev, [data, xhr]);
						cancel = ev.isDefaultPrevented();
					}
					if (cancel) return;
					if (err) alert(data['message'].replace(/<br[\s|\/]*>/g, "\n"));
					if (data['redirect_url']) parent.location.replace(data['redirect_url']);
				},
				error: function(xhr, status, error) {
					var msg = status,
						cancel = false;
					if (status == 'parsererror') {
						msg = 'The result is not valid JSON :\n--------------------------------\n';
						if (xhr.responseText !== "") msg += xhr.responseText.replace(/<[^>]+>/g, '');
						console.log(msg);
						msg = 'parsererror';
					} else msg = error;

					var data = [];
					data['error'] = '1';
					data['message'] = msg;

					$i.data('actioning', false);

					if ($.isFunction(callback)) {
						cancel = callback('error', data, xhr) === false;
					} else {
						var ev = $.Event('error.exec.ajax');
						$i.trigger(ev, [data, xhr]);
						cancel = ev.isDefaultPrevented();
					}
					if (cancel) return;
					alert(data['message'].replace(/<br[\s|\/]*>/g, "\n"));
					if (data['redirect_url']) parent.location.replace(data['redirect_url']);
				}
			});
		} catch (e) {
			alert(e);
			$waiting.remove();
			$i.data('actioning', false);
		}
	};

	$(document)
		.on('click', '[data-msg-box]', function(e) {
			var $this = $(this);
			if ($this.data('clicked') === true) return true;
			e.stopImmediatePropagation();
			var type = $this.attr('data-msg-box') || 'warning',
				title = $this.attr('data-title') || $this.attr('title') || '';
			msg_box(title, '', [type, (type == 'question') ? ['OK', 'cancel'] : ['OK']], function(key) {
				if (type == 'question' && key == 'ok') {
					$this.data('clicked', true);
					var event = document.createEvent("MouseEvents");
					event.initEvent("click", false, true);
					$this[0].dispatchEvent(event);
				}
				return true;
			});
			return false;
		})
		.on('click', '[data-exec-ajax][data-ajax-param]', function(e) {
			var $this = $(this),
				$i = $(e.target);
			if ($i.is('[data-except-ajax]')) return true;
			var msg = $this.attr('data-ajax-confirm') || '';
			e.preventDefault();
			if (msg != '') {
				msg_box(msg, '', ['question', ['OK', 'cancel']], function(key) {
					if (key == 'ok') exec_ajax($this);
					return true;
				});
			} else {
				exec_ajax(this);
			}
			return false;
		})
		.on('submit', 'form[data-exec-ajax]', function(e) {
			var msg = $(this).attr('data-ajax-confirm') || '';
			e.preventDefault();
			if (msg != '') {
				msg_box(msg, '', ['question', ['OK', 'cancel']], function(key) {
					if (key == 'ok') exec_ajax($this);
					return true;
				});
			} else {
				exec_ajax(this);
			}
			return false;
		});

	// <div class="uploader-group" placeholder="File">
	// 	<div class="input-group">
	// 		<div class="file-caption form-control"></div>
	// 		<div class="btn btn-primary btn-file">
	// 			<i class="glyphicon glyphicon-folder-open">Browse…</i>
	// 			<input name="upload_file" type="file">
	// 		</div>
	// 	</div>
	// </div>
	$(document)
		.on('change', '.uploader-group input:file', function(e) {
			var $i = $(this),
				$g = $i.closest('.uploader-group'),
				$c = $g.find('.file-caption'),
				ismt = $i[0].hasAttribute('multiple');
			if (ismt) $g.addClass('file-list');
			var ev = $.Event('insert.af.uploader');
			$g.trigger(ev, [$i.prop("files")]);
			if (ev.isDefaultPrevented()) return;
			$c.html('');
			$.map($i.prop("files"), function(val, i) {
				var type = (val.type.split('/')[0] || 'binary').escapeHtml(),
					size = val.size.shortFileSize(),
					title = val.name.escapeHtml() + ' (' + size + ')';
				$('<i class="file-item" title="' + (ismt ? title : '') + '" data-type="' + type + '" data-index="' + i + '">')
					.html(ismt ? '' : title)
					.appendTo($c);
			});
		})
		.on('click', '.uploader-group .file-caption', function(e) {
			var $c = $(this),
				$ci = $c.find('.file-item'),
				$g = $c.closest('.uploader-group'),
				$i = $g.find('input:file'),
				plac = $g.attr('placeholder') || '';
			if ($ci.length > 0) {
				var ev = $.Event('delete.af.uploader');
				$g.trigger(ev, [$i.prop("files")]);
				if (ev.isDefaultPrevented()) return;
				$i.val('');
				$('<input type="hidden" name="remove_files[]" value="' + $i.attr('name') + '">').appendTo($c.text(plac));
			}
		})
		.on('keydown', '.uploader-group .file-caption', function(e) {
			if (e.which == 13 || e.which == 32) {
				e.preventDefault();
				$(this).click();
			}
		})
		.on('keydown', '.uploader-group .btn-file', function(e) {
			if (e.which == 13 || e.which == 32) {
				e.preventDefault();
				$(this).find('input:file').click();
			}
		})
		.on('uploader:repair', '.uploader-group', function(e) {
			var $g = $(this),
				$i = $g.find('input:hidden'),
				$c = $g.find('.file-caption'),
				plac = $g.attr('placeholder') || '';
			if (!$c.find('.file-item').length && plac) $c.text(plac);
			if ($c[0] && !$c[0].hasAttribute('tabindex')) $c.attr('tabindex', '0');
			if ($i[0] && !$i[0].hasAttribute('tabindex')) {
				$i.parent().attr('tabindex', '0');
				$i.attr('tabindex', '-1');
			}
		});

	// 에이폭스 체크박스 스페이스 바 누르면 클릭
	$(document)
		.on('keydown', 'label.checkbox,label.radio', function(e) {
			if (e.which == 13 || e.which == 32) {
				e.preventDefault();
				$(this).find('>input').click();
			}
		});

	// 글자 수를 byte로 체크하기 위해
	$(document)
		.on('keyup', 'input[maxbyte],textarea[maxbyte]', function(e) {
			var $i = $(this),
				max = $i.attr('maxbyte') || 0;
			if (isNaN(max)) return;
			var b = 0,
				r = '',
				val = $i.val();
			for (var i = 0, c; !isNaN(c = val.charCodeAt(i)); i++) {
				b += c < 128 ? 1 : (c < 2048 ? 2 : (c < 3936256 ? 3 : 4));
				if (b > max) break;
				r += String.fromCharCode(c);
			}
			$i.val(r);
		});

	// ... load
	$(window)
		.on('load', function() {
			$('.uploader-group').trigger('uploader:repair');
		});

})(jQuery);
