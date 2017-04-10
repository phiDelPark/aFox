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

var $_LANG = [];

(function($) {
	'use strict';
	/*
		var version = $.fn.jquery.split(' ')[0].split('.');
		if ((version[0] < 2 && version[1] < 9) || (version[0] == 1 && version[1] == 9 && version[2] < 1) || (version[0] > 2)) {
			throw new Error('aFox\'s JavaScript requires jQuery version 1.9.1 or higher, but lower than version 3');
		}
	*/

	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, "");
	};

	String.prototype.toUcFirst = function() {
		var s = this.toLowerCase();
		return s.charAt(0).toUpperCase() + s.slice(1);
	};

	String.prototype.sprintf = function() {
		var s = this;
		for (var i = 0, n = arguments.length; i < n; i++) {
			s = s.replace(/%(s|d)/, arguments[i]);
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

	String.prototype.setQuery = function() {
		var s = this,
			args = arguments,
			n = args.length;
		if (n === 0) return s;
		if (args.length === 1 && typeof args[0] === 'object') {
			args = args[0];
			n = args.length;
		}
		var idx = s.indexOf('?'),
			uri = s.replace(/#$/, ''),
			qrs = {},
			qls = [],
			v;
		if (idx != -1 && args[0] !== '') {
			uri.substr(idx + 1, s.length)
				.replace(/([^=]+)=([^&]*)(&|$)/g,
					function(all, key, val) {
						qrs[key] = val;
					}
				);
		}
		uri = s.substr(0, idx);
		for (var i = (args[0] === '' ? 1 : 0); i < n; i += 2) {
			qrs[args[i]] = args[i + 1];
		}
		for (var k in qrs) {
			if (!qrs.hasOwnProperty(k)) continue;
			if (!(v = String(qrs[k]).trim())) continue;
			qls.push(k + '=' + decodeURI(v));
		}
		return uri + (qls.length > 0 ? '?' + qls.join('&') : '');
	};

	Number.prototype.shortFileSize = function() {
		var size = this,
			tail = 'byte';
		if (size > 1024) {
			size = Math.ceil(size / 1024);
			tail = 'kb';
		}
		if (size > 1024) {
			size = Math.ceil(size / 1024);
			tail = 'mb';
		}
		if (size > 1024) {
			size = Math.ceil(size / 1024);
			tail = 'gb';
		}
		return size + tail;
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
					value = $item.map(function() {
						return this.value;
					}).get();
					name = name.slice(0, -2);
				}
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
					if ($target.hasClass('fileupload-group')) {
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
					if (data[key] == 1) {
						$item.attr('checked', 'checked');
					} else {
						$item.removeAttr('checked');
					}
				} else if ($item.is(':radio')) {
					$item.filter(function() {
						return this.value === data[key];
					}).attr('checked', 'checked');
				} else {
					if ($item.attr('type') == 'hidden') {
						$item.val(data[key]);
						$target = $item.parent();
						if ($target.hasClass('radio-group')) {
							$target.find('.radio').removeClass('active');
							$target.find('.radio[data-value="' + data[key] + '"]').addClass('active');
						} else if ($target.hasClass('switch-group')) {
							var von = $target.find('.switch-handle-on').attr('data-value') || 1;
							if (data[key] == von) $target.addClass('on');
							else $target.removeClass('on');
						}
					} else {
						$item.val(data[key] == '0' ? '' : data[key]);
					}
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
				html = '<div class="form-group has-feedback" style="margin-bottom:5px"><div class="af-editor-uploaded-list fileupload-group file-list form-control" style="margin-top:10px">';
				$.map(data['files'], function(v, i) {
					html += '<i class="file-item" draggable="true" title="' + v.mf_name.escapeHtml() + ' (' + Number(v.mf_size).shortFileSize() + ')" data-type="' + v.mf_type + '" data-srl="' + v.mf_srl + '"></i>';
				});
				html += '</div><span class="glyphicon glyphicon-question-sign form-control-feedback" style="pointer-events:auto;cursor:pointer" tabindex="0"></span></div>';
				$uploads.closest('.af-editor-upload-button').prepend(html);
			}
		}
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

		if (!waiting_message) waiting_message = '';
		var $waiting = $('<div class="af_waiting_message alert alert-warning" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' + waiting_message + '<div class="progress"><div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" style="width:100%"></div></div></div>');
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
					if ($.isFunction(callback)) {
						callback('complete', status, xhr);
					} else {
						$i.trigger('complete.exec.ajax', [status, xhr]);
					}
					$waiting.remove();
					$i.data('actioning', false);
				},
				success: function(data, status, xhr) {
					var ev, err, msg, cancel = false;
					if (typeof data == 'string') {
						err = -1;
						msg = xhr.responseText.replace(/<[^>]+>/g, '');
					} else {
						err = (data['error'] && data['error'] != '0') ? data['error'] : 0;
						msg = err ? data['message'] : '';
					}
					if ($.isFunction(callback)) {
						cancel = callback(err ? 'error' : 'success', err ? msg : data, xhr) === false;
					} else {
						ev = $.Event(err ? 'error.exec.ajax' : 'success.exec.ajax');
						$i.trigger(ev, err ? [msg, xhr] : [data, xhr]);
						cancel = ev.isDefaultPrevented();
					}
					if (cancel) {
						$waiting.remove();
						$i.data('actioning', false);
						return;
					}
					if (err) alert(msg.replace(/<br[\s|\/]*>/g, "\n"));
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
					if ($.isFunction(callback)) {
						cancel = callback('error', msg, xhr) === false;
					} else {
						var ev = $.Event('error.exec.ajax');
						$i.trigger(ev, [msg, xhr]);
						cancel = ev.isDefaultPrevented();
					}
					if (cancel) {
						$waiting.remove();
						$i.data('actioning', false);
						return;
					}
					alert(msg.replace(/<br[\s|\/]*>/g, "\n"));
					if (data['redirect_url']) parent.location.replace(data['redirect_url']);
				}
			});
		} catch (e) {
			alert(e);
			$waiting.remove();
			$i.data('actioning', false);
		}
	};

	$(document).on('submit', 'form[data-exec-ajax]', function(e) {
		e.preventDefault();
		exec_ajax(this);
	}).on('click', '[data-exec-ajax][data-ajax-param]', function(e) {
		e.preventDefault();
		exec_ajax(this);
	});

	// <div class="fileupload-group" placeholder="File">
	// 	<div class="input-group">
	// 		<div class="file-caption form-control"></div>
	// 		<div class="btn btn-primary btn-file">
	// 			<i class="glyphicon glyphicon-folder-open">Browse…</i>
	// 			<input name="upload_file" type="file">
	// 		</div>
	// 	</div>
	// </div>
	$(document).on('change', '.fileupload-group input:file', function(e) {
		var $i = $(this),
			$g = $i.closest('.fileupload-group'),
			$c = $g.find('.file-caption'),
			ismt = $i[0].hasAttribute('multiple');
		if (ismt) $g.addClass('file-list');
		var ev = $.Event('insert.af.fileupload');
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
	}).on('click', '.fileupload-group .file-caption', function(e) {
		var $c = $(this),
			$ci = $c.find('.file-item'),
			$g = $c.closest('.fileupload-group'),
			$i = $g.find('input:file'),
			plac = $g.attr('placeholder') || '';
		if ($ci.length > 0) {
			var ev = $.Event('delete.af.fileupload');
			$g.trigger(ev, [$i.prop("files")]);
			if (ev.isDefaultPrevented()) return;
			$i.val('');
			$('<input type="hidden" name="remove_files[]" value="' + $i.attr('name') + '">').appendTo($c.text(plac));
		}
	}).on('keydown', '.fileupload-group .file-caption', function(e) {
		if (e.which == 13 || e.which == 32) {
			e.preventDefault();
			$(this).click();
		}
	}).on('keydown', '.fileupload-group .btn-file', function(e) {
		if (e.which == 13 || e.which == 32) {
			e.preventDefault();
			$(this).find('input:file').click();
		}
	});


	// <div class="radio-group">
	// 	<input type="hidden" name="pg_type" value="0">
	// 	<div class="radio-control radio-xs">
	// 		<span class="radio active" data-value="0">MKDW</span>
	// 		<span class="radio" data-value="1">TEXT</span>
	// 		<span class="radio" data-value="2">HTML</span>
	// 	</div>
	// </div>
	$(document).on('click', '.radio-control', function(e) {
		var $i = $(e.target),
			$p = $i.closest('.radio-group');
		if ($i[0].hasAttribute("readonly") || $i[0].hasAttribute("disabled")) return;
		if ($i.hasClass('radio')) {
			var v = $i.attr('data-value') || '0';
			$p.find('input:hidden').val(v).end().find('.radio').removeClass('active');
			$i.addClass('active');
			$p.trigger('changed.af.radio', [v]);
		}
	}).on('keydown', '.radio-control .radio', function(e) {
		if (e.which == 13 || e.which == 32) {
			e.preventDefault();
			$(this).click();
		}
	});


	// <div class="switch-group">
	// 	<input type="hidden" name="use_ssl" value="0">
	// 	<div class="switch-control switch-xs">
	// 		<span class="switch switch-handle-on" data-value="1">use</span>
	// 		<span class="switch switch-label">ssl</span>
	// 		<span class="switch switch-handle-off" data-value="0">notuse</span>
	// 	</div>
	// </div>
	$(document).on('click', '.switch-group', function(e) {
		var $i = $(this);
		if ($i[0].hasAttribute("readonly") || $i[0].hasAttribute("disabled")) return;
		if ($i.data('actioning')) return;
		$i.data('actioning', true);
		var on = $i.hasClass('on'),
			von = $i.find('.switch-handle-on').attr('data-value') || 1,
			vof = $i.find('.switch-handle-off').attr('data-value') || 0;
		var ev = $.Event('change.af.switch');
		$i.trigger(ev, [on, (on ? von : vof)]);
		if (ev.isDefaultPrevented()) {
			$i.data('actioning', false);
			return;
		}
		$i.find(".switch-control").animate({
			left: on ? '-=100' : '+=100',
		}, 500, function() {
			$(this).css('left', '');
			$i.toggleClass('on')
				.find('input:hidden').val(!on ? von : vof).end()
				.trigger('changed.af.switch', [!on, (!on ? von : vof)])
				.data('actioning', false);
		});
	}).on('keydown', '.switch-group', function(e) {
		if (e.which == 13 || e.which == 32) {
			e.preventDefault();
			$(this).click();
		}
	});

	// 글자 수를 byte로 체크하기 위해
	$(document).on('keyup', 'input[maxbyte],textarea[maxbyte]', function(e) {
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
	$(window).on('load', function() {
		$('.fileupload-group input:file').each(function() {
			var $i = $(this),
				$g = $i.closest('.fileupload-group'),
				$c = $g.find('.file-caption'),
				plac = $g.attr('placeholder') || '';
			if (!$c.find('.file-item').length && plac) $c.text(plac);
			if (!$c[0].hasAttribute('tabindex')) $c.attr('tabindex', '0');
			if (!$i[0].hasAttribute('tabindex')) {
				$i.parent().attr('tabindex', '0');
				$i.attr('tabindex', '-1');
			}
		});
		$('.radio-group input:hidden').each(function() {
			var $i = $(this),
				$g = $i.closest('.radio-group'),
				v = $i.val();
			$g.find('.radio').each(function() {
				$(this).removeClass('active');
				if (!this.hasAttribute('tabindex')) $(this).attr('tabindex', '0');
			});
			$g.find('.radio[data-value="' + v + '"]').addClass('active');
		});
		$('.switch-group input:hidden').each(function() {
			var $i = $(this),
				$g = $i.closest('.switch-group'),
				von = $g.find('.switch-handle-on').attr('data-value') || 1;
			$g.removeClass('on').addClass(($i.val() || 0) == von ? 'on' : '');
			if (!$g[0].hasAttribute('tabindex')) $g.attr('tabindex', '0');
		});
	});

})(jQuery);
