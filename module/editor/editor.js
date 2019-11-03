/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

(function($) {
	'use strict';

	function dropFile(e, th) {
		var $i = th.$textarea,
			text,
			data = JSON.parse(e.originalEvent.dataTransfer.getData("TEXT") || '{}'),
			title = data['title'],
			srl = data['srl'] || data['index'],
			url = ((data['srl'] || false) ? (request_uri + '?file=' + srl) : 'afox-editor-tmpfile=' + srl),
			type = (data['type'].split('/')[0] || 'binary');

		if (!$i.is(':visible') || $i.length === 0) {
			$i = th.$element.find('iframe');

			title = title.escapeHtml();

			if (data['path'] || false) {
				url = data['path'] + '" afox-editor-tmpfile="' + srl;
			} else url = url.escapeHtml();

			switch (type) {
				case 'image':
					text = '<img src="' + url + '" alt="' + title + '">';
					break;
				case 'video':
				case 'audio':
					text = '<' + type + ' width="100%" controls>';
					text += '<source src="' + url + '" type="' + data['type'] + '">';
					text += 'Your browser does not support the audio element.</' + type + '>';
					break;
				default:
					text = '<code><a href="' + url + '">' + title + '</a></code>';
					break;
			}

			th.paste(text, false);

		} else {

			title = title.escapeMKDW(false);

			switch (type) {
				case 'image':
					text = '![' + title + '](' + url + ')';
					break;
				case 'video':
				case 'audio':
					text = '[' + title + '](' + url + ' "_' + data['type'] + '_")';
					break;
				default:
					text = '[`' + title + '`](' + url + ')';
					break;
			}

			th.paste(text, false);
		}
	}

	function setImageTooltip($i) {
		var title = $i.attr('data-title') || $i.attr('title');
		$i.removeAttr('title');
		$i.attr('data-title', title);

		var size = '';
		title = title.replace(/(.+)(\([a-zA-Z0-9.]+\)$)/g, function(match, p1, p2) {
			size = p2;
			return p1.trim();
		}).escapeHtml();

		title = '<div>' + title + '</div><div>' + size + '</div>';

		if ($i.attr('data-type').substring(0, 5) === 'image') {
			var url = $i.attr('data-srl') || false ? request_uri + '?file=' + $i.attr('data-srl') : $i.attr('data-path');
			title = '<img src="' + url + '" style="width:100px;height:100px"><div style="width:100px;overflow:hidden;white-space:nowrap">' + title + '</div>';
		}

		$i.tooltip({
			html: 1,
			container: 'body',
			title: function() {
				return title;
			}
		});
	}

	// AFEDITOR CLASS DEFINITION
	// ==========================

	function AfEditor(element, options) {
		this.$element = $(element);
		this.$textarea = $(element).find('textarea').show();
		this.options = $.extend({}, AfEditor.DEFAULTS, options);

		var $this = this,
			$balloon;

		// 풍선 메세지 뛰우기 위해서...
		if (this.options.required) {
			$balloon = $('<form style="position:absolute;left:0;top:0;height:1px!important;overflow:hidden!important;border:0!important;opacity:0!important" onsubmit="return false"><input type="text" required><input type="submit"></form>');
			$balloon.appendTo('BODY');
		}

		this.$element.closest('form').offOn('submit', function() {
			var text = $this.$textarea.val(),
				$iframe = $this.$element.find('iframe');

			if ($iframe.length) {
				text = $iframe.contents().find('body').html();
				$this.$textarea.val(text);
			}

			if ($this.options.required && !text) {
				//alert($this.options.required);
				var x = $iframe.length ? $iframe.offset() : $this.$textarea.offset();
				$balloon.css({
					'left': x.left,
					'top': x.top
				}).find('[type="submit"]').click();
				return false;
			}
		});

		this.$element.offOn('click', '.af-editor-toolbar>.pull-right', function(e) {
			var $g = $(this),
				$i = $(e.target);
			if ($g.parent()[0].hasAttribute('readonly')) return;

			if ($i.hasClass('glyphicon')) $i = $i.parent();

			var type = $i.attr('data-type') || '',
				target = $i.attr('data-target') || '',
				val = $i.attr('data-value') || '';
			if (!type || !val || !target) return;

			var old = $g.find('input[name="' + target + '"]').val(),
				ev = $.Event('change.af.editor.toolbar');
			if (type === 'checkbox') val = $i.find('.glyphicon').hasClass('glyphicon-check') ? '' : '1';

			$g.parent().trigger(ev, [target, old, val]);
			if (ev.isDefaultPrevented()) return;

			if (type === 'checkbox') {
				$i.find('.glyphicon')
					.addClass('glyphicon-' + (val === '1' ? 'check' : 'unchecked'))
					.removeClass('glyphicon-' + (val === '1' ? 'unchecked' : 'check'));
			} else {
				$g.find('[data-type="' + type + '"]').each(function() {
					$(this).find('.glyphicon-check').addClass('glyphicon-unchecked').removeClass('glyphicon-check');
				});
				$g.find('[data-type="' + type + '"][data-value="' + val + '"]').find('.glyphicon-unchecked')
					.addClass('glyphicon-check').removeClass('glyphicon-unchecked');
			}

			$g.find('input[name="' + target + '"]').val(val);
			$g.parent().trigger('changed.af.editor.toolbar', [target, old, val]);
		}).offOn('keydown', '.af-editor-toolbar>.pull-right>span[tabindex=0]', function(e) {
			if (e.which == 13 || e.which == 32) {
				e.preventDefault();
				$(this).click();
			}
		}).offOn('click', '.af-statebar-area>.btn-group>button[tabindex=-1]', function(e, i) {
			var $i = $(this),
				$txtara = $this.$textarea,
				$iframe = $this.$element.find('iframe'),
				type = $i.attr('aria-label');
			$i.blur();
			if (type == 'link' || type == 'components') return false;
			$this.exec(type, null);
		}).offOn('click', '.af-editor-uploaded>.file-item', function() {
			var $i = $(this),
				srl = $i.attr('data-srl'),
				$rm = $('[name="remove_files[]"][value="' + srl + '"]');

			if ($rm.length) {
				$rm.remove();
				$i.css('color', '').attr('title', $i.attr('title').substr(9));
			} else {
				$('<input type="hidden" name="remove_files[]" value="' + srl + '">').insertAfter($i);
				$i.css('color', 'hotpink').attr('title', '(Remove) ' + $i.attr('title'));
			}
		}).offOn('dragstart', '.af-editor-uploaded>.file-item', function(e) {
			var $i = $(this);
			if ($i.data && $i.data('bs.tooltip')) $i.tooltip('hide'); // 드래그 시작시 툴팁 감추기
			e.originalEvent.dataTransfer.setData("TEXT", JSON.stringify({
				'type': $i.attr('data-type'),
				'srl': $i.attr('data-srl'),
				'title': $i.attr('data-title') || $i.attr('title')
			}));
		}).offOn('dragover', '.af-editor-content>textarea', function(e) {
			e.preventDefault();
		}).offOn('drop', '.af-editor-content>textarea', function(e) {
			e.preventDefault();
			dropFile(e, $this);
		}).offOn('insert.af.uploader', '.uploader-group', function(e, files) {
			e.preventDefault();
			var $c = $(this).find('.file-caption'),
				ismt = $(this).find('input:file')[0].hasAttribute('multiple');

			$c.html('');
			$.map(files, function(val, i) {
				var type = val.type.escapeHtml(),
					size = val.size.shortFileSize(),
					title = val.name.escapeHtml() + ' (' + size + ')',
					path = (window.URL || window.webkitURL).createObjectURL(val);

				var $item = $('<i class="file-item" draggable="true" title="' + (ismt ? title : '') + '" data-type="' + type + '" data-index="' + i + '" data-path="' + path + '">');
				$item.text(ismt ? '' : title)
					.appendTo($c)
					.offOn('dragstart', function(e) {
						var $i = $(this);
						if ($i.data && $i.data('bs.tooltip')) $i.tooltip('hide'); // 드래그 시작시 툴팁 감추기
						e.originalEvent.dataTransfer.setData("TEXT", JSON.stringify({
							'type': $i.attr('data-type'),
							'index': $i.attr('data-index'),
							'title': (ismt ? ($i.attr('data-title') || $i.attr('title')) : $i.text()),
							'path': $i.attr('data-path')
						}));
					});

				setImageTooltip($item);
			});
		}).offOn('delete.af.uploader', '.uploader-group', function(e, files) {
			e.preventDefault();
			var $c = $(this).find('.file-caption'),
				$g = $c.closest('.uploader-group');
			$c.find('.file-item').tooltip('destroy').end().text($g.attr('placeholder') || '');
			$g.find('input:file').val('');
		});

		this.$element.find('.form-control-feedback').popover({
			html: 1,
			trigger: 'focus',
			placement: 'top',
			title: $_LANG['help_editor_attach_title'] || '<strong>첨부파일 사용법</strong>',
			content: $_LANG['help_editor_attach_content'] || '본문에 파일을 보여주려면 아이콘을 잡고 끌어 본문 위로 옮기면 됩니다.<br><br>클릭시엔 삭제 모드가 토글됩니다.'
		}).offOn('show.bs.popover', function() {
			$(this).data("bs.popover").tip().css({
				'max-width': '500px',
				'font-size': '12px'
			});
		});

		this.$element.find('.af-statebar-area [data-toggle="popover"]').popover({
			html: 1,
			trigger: 'manual',
			placement: 'top',
			content: '<div class="input-group mw-20"></div>'
		}).offOn('inserted.bs.popover', function() {
			var $i = $(this),
				$popover = $i.data("bs.popover").tip().find('.popover-content'),
				type = $i.attr('aria-label') || '';
			if (type == 'components') {
				if (AF_EDITOR_COMPONENTS) {
					var lk = '<div class="list-group" style="margin:0">';
					for (var i in AF_EDITOR_COMPONENTS) {
						lk = lk + '<a href="#" class="list-group-item" data-name="' + AF_EDITOR_COMPONENTS[i][0] + '">' + AF_EDITOR_COMPONENTS[i][1] + '</a>';
					}
					$popover
						.attr('style', 'padding:0')
						.find('.input-group')
						.html(lk + '</div>')
						.find('a').click(function() {
							var name = $(this).attr('data-name');
							pop_win(request_uri + 'module/editor/component.php?n=' + name + '&k=' + $this.options.name, null, null, 'af_editor_components');
							return false;
						});
				}
			} else {
				$popover
					.find('.input-group')
					.html('<input type="text" class="form-control" placeholder="Link"><a href="#" class="btn btn-default input-group-addon">OK</a>')
					.find('a').click(function() {
						var url = $(this).prev().val() || '';
						$this.exec(type, url);
						return false;
					});
			}
		}).offOn('shown.bs.popover', function() {
			var $i = $(this),
				$ipu = $i.data("bs.popover").tip().find('.popover-content').find('input');
			if ($ipu.length > 0) {
				$ipu.focus().select();
			} else {
				$ipu = $i;
			}
			$ipu.offOn('blur', function() {
				$i.popover('hide');
			});
		}).offOn('click', function() {
			$(this).popover('toggle');
		});

		this.$element.find('.uploader-group .file-item').each(function() {
			setImageTooltip($(this));
		});

		var $iframe = this.$element.find('iframe');
		if ($iframe.length) $iframe.remove();
		if (this.options.html) this.switch(true);
	}

	AfEditor.VERSION = '1.0.0';

	AfEditor.DEFAULTS = {
		required: false,
		readonly: false,
		html: false
	};

	AfEditor.prototype.switch = function(swc) {
		var $this = this,
			$txtara = this.$textarea,
			$iframe = this.$element.find('iframe'),
			height = $txtara.css('height') || '',
			readonly = this.options.readonly;

		if (swc) {
			if (!$iframe.length) {
				$iframe = $('<iframe class="form-control vresize">')
					.offOn('load', function() {
						var $_i = $(this);
						$_i.contents().find('body')
							.offOn('dragover',
								function(e) {
									e.preventDefault();
								}
							).offOn('drop', function(e) {
								e.preventDefault();
								dropFile(e, $this);
							});
						$_i.contents().find("head").html('<link rel="stylesheet" href="' + request_uri + 'module/editor/editor.min.css">');
						$_i.contents().find('body').html($txtara.val());
						/*
						$_i.contents().offOn('keydown', function(e) {
							if (e.which == 13 && e.shiftKey !== true) {
								$this.paste('<br>', false);
								return false;
							}
						});
						*/
						if (!readonly) $_i.contents()[0].designMode = 'on';
						//$_i[0].contentWindow.document.designMode = 'on';
					}).insertAfter($txtara.hide()).end();

				if (readonly) {
					$iframe.attr('readonly', 'readonly');
				} else {
					$iframe.removeAttr('readonly');
				}
			}

			$iframe.css('height', height);
		} else {
			var text;
			if ($iframe.length) {
				height = $iframe.css('height');
				text = $iframe.contents().find('body').html();
				$iframe.remove();
			} else {
				text = $txtara.val();
			}

			$txtara.css('height', height).val(text).show();
			if (readonly) {
				$txtara.attr('readonly', 'readonly');
			} else {
				$txtara.removeAttr('readonly');
			}
		}
	};

	AfEditor.prototype.toggle = function() {
		this.switch(this.$textarea.is(':visible'));
	};

	AfEditor.prototype.selection = function() {
		var $i = this.$textarea,
			ret = {};

		if (!$i.is(':visible') || $i.length === 0) {
			$i = this.$element.find('iframe');
			$i.contents().find('body').focus();

			var sel, isie = false,
				w = $i[0].contentWindow;

			if (w) {
				if (w.getSelection) {
					sel = w.getSelection();
				} else if (w.document.getSelection) {
					sel = w.document.getSelection();
				}
				/** else {
									sel = w.document.selection && w.document.selection.createRange();
									if (sel.text) { isie = true; } else { return null; }
								} **/
				ret['target'] = $i;
				ret['window'] = w;
				ret['selection'] = sel;
			}
			/** else {
							var $body = $i.contents().find('body'),
								html = $body.html();
							$body.html(html + text.replace(/%s/, ''));
						} **/
		} else {
			$i.focus();
			ret['target'] = $i;
			ret['start'] = $i.prop('selectionStart');
			ret['end'] = $i.prop('selectionEnd');
			ret['text'] = $i.val().substring(ret['start'], ret['end']);
		}

		return ret;
	};

	AfEditor.prototype.paste = function(text, fm) {
		var s = this.selection(),
			range;

		if (s.target[0].tagName == 'TEXTAREA') {
			if (fm !== false)
				text = text.replace(/%s/, s.text ? s.text : (fm || ''));

			var v = s.target.val(),
				txtBefore = v.substring(0, s.start),
				txtAfter = v.substring(s.start + (s.end - s.start), v.length);

			s.target.val(txtBefore + text + txtAfter);

			s.end = s.end + text.length - s.text.length;
			if (s.target[0].setSelectionRange) {
				s.target[0].setSelectionRange(s.start, s.end);
			} else if (s.target[0].createTextRange) {
				range = s.target[0].createTextRange();
				range.collapse(true);
				range.moveEnd('character', s.end);
				range.moveStart('character', s.start);
				range.select();
			}

			s.target.focus();
		} else {
			range = s.selection.getRangeAt(0);

			if (range) {
				var v = '', el = s.window.document.createElement("div");
				el.appendChild(range.cloneContents());
				range.deleteContents();
				if (fm !== false) {
					if (el.innerText) {
						v = el.innerHTML.replace(/(\n|\r)/g, '').replace(/<(br|hr|\/div|\/li|\/?ul|\/?ol|\/?dl|\/th|\/td|\/?tr|table|\/?blockquote|\/?pre|\/?p|\/?h[1-9])(\s[^>]*>|>)/gi, "\n<$1>");
						v = $('<div>' + v + '</div>').text().replace(/</g, '&lt;').replace(/>/g, '&gt;');
					} else {
						v = fm || '';
					}
				}
				range.insertNode($(text.replace(/%s/, v))[0]);
				if (fm === false) {
					range.setStart(s.selection.focusNode, s.selection.focusOffset);
					range.setEnd(s.selection.anchorNode, s.selection.anchorOffset);
				}
			}

			s.target.contents().find('body').focus();
		}
	};

	AfEditor.prototype.exec = function(cmd, value) {
		var $i = this.$textarea,
			doc,
			text;

		if (!$i.is(':visible') || $i.length === 0) {
			$i = this.$element.find('iframe');
		}

		switch (cmd) {
			case 'bold':
			case 'italic':
			case 'underline':
			case 'strikeThrough':
			case 'insertorderedlist':
				if ($i[0].tagName == 'TEXTAREA') {
					switch (cmd) {
						case 'bold':
							this.paste('**%s**');
							break;
						case 'italic':
							this.paste('*%s*');
							break;
						case 'underline':
							this.paste('<u>%s</u>');
							break;
						case 'strikeThrough':
							this.paste('~~%s~~');
							break;
						case 'insertorderedlist':
							this.paste("\n" + '1. ' + '%s' + "\n" + '2. ');
							break;
					}
				} else {
					doc = $i[0].contentWindow.document;
					doc.execCommand(cmd, false, value);
				}
				break;
			case 'highlight':
				if ($i[0].tagName == 'TEXTAREA') {
					this.paste('`%s`');
				} else {
					this.paste('<code>%s</code>', '...');
				}
				break;
			case 'header':
				this.paste('<h2>%s</h2>', '...');
				break;
			case 'indent':
				this.paste('<blockquote>%s</blockquote>', '...');
				break;
			case 'codeblock':
				this.paste('<pre><code>%s' + "\n" + '</code></pre>');
				break;
			case 'link':
				value = decodeURIComponent(value).replace(/&amp;/g, '&');
				var pattern = /https?:\/\/([a-z\.]*youtub?e?)\.(com|be)(\/embed\/|\/watch\?v\=|\/)([^\?\&]+)(.*)/i,
					t = value.getQuery('t') || value.getQuery('start');
				if (pattern.test(value)) {
					text = '<img widget="youtube" src="' + value.replace(pattern, "https://img.youtube.com/vi/$4/mqdefault.jpg\" width=\"560\" height=\"315\" vid=\"$4") + '"' + (t ? ' time="' + t + '"' : '') + '>' + "\n";
					this.paste(text, false);
				} else {
					if (!value) value = '#';
					text = '<a href="' + value + '" target="_blank">%s</a>';
					if ($i[0].tagName == 'TEXTAREA') text = '<' + value + '>';
					this.paste(text, value);
				}
				break;
		}

		if ($i[0].tagName == 'TEXTAREA') {
			$i.focus();
		} else {
			$i.contents().find('body').focus();
		}
	};

	// AFEDITOR PLUGIN DEFINITION
	// ===========================

	function Plugin(option) {
		return this.each(function() {
				var $this = $(this),
					data = $this.data('af.editor'),
					options = typeof option == 'object' && option;

				if (!data) $this.data('af.editor', (data = new AfEditor(this, options)));
				if (typeof option == 'string') data[option]();
			})
			.data('af.editor');
	}

	var old = $.fn.afEditor;

	$.fn.afEditor = Plugin;
	$.fn.afEditor.Constructor = AfEditor;


	// AFEDITOR NO CONFLICT
	// =====================

	$.fn.afEditor.noConflict = function() {
		$.fn.afEditor = old;
		return this;
	};

})(jQuery);
