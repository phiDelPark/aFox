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
			url = ((data['srl'] || false) ? (request_uri + '?file=' + srl) : 'af-editor-tmpfile=' + srl),
			type = (data['type'].split('/')[0] || 'binary');

		if (!$i.is(':visible') || $i.length === 0) {
			$i = th.$element.find('iframe');

			title = title.escapeHtml();

			if (data['path'] || false) {
				url = data['path'] + '" data-af-editor-tmpfile="' + srl;
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
					text = '<a href="' + url + '"><code>' + title + '</code></a>';
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
		var title = $i.attr('title');
		$i.removeAttr('title');
		$i.attr('data-title', title);

		if ($i.attr('data-type').substring(0, 5) !== 'image') {
			$i.tooltip({
				container: 'body'
			});
		} else {
			var url = $i.attr('data-srl') || false ? request_uri + '?file=' + $i.attr('data-srl') : $i.attr('data-path');
			title = title.substring(title.length - 17, title.length);
			$i.tooltip({
				html: 1,
				container: 'body',
				title: function() {
					return '<img src="' + url + '" style="width:100px;height:100px"><div style="width:100px;overflow:hidden;white-space:nowrap;">' + title + '</div>';
				}
			});
		}
	}

	// AFEDITOR CLASS DEFINITION
	// ==========================

	function AfEditor(element, options) {
		this.$element = $(element);
		this.$textarea = $(element).find('textarea').show();
		this.options = $.extend({}, AfEditor.DEFAULTS, options);

		var $this = this;

		this.$element.closest('form').on('submit', function() {
			var text = $this.$textarea.val(),
				$iframe = $this.$element.find('iframe');

			if ($iframe.length) {
				text = $iframe.contents().find('body').html();
				$this.$textarea.val(text);
			}

			if ($this.options.required && !text) {
				alert($this.options.required);
				return false;
			}
		});

		this.$element.on('click', '.af-editor-toolbar>.pull-right', function(e) {
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
		}).on('keydown', '.af-editor-toolbar>.pull-right>span[tabindex=0]', function(e) {
			if (e.which == 13 || e.which == 32) {
				e.preventDefault();
				$(this).click();
			}
		}).on('click', '.af-statebar-area>.btn-group>button[tabindex=-1]', function(e, i) {
			var $i = $(this),
				$txtara = $this.$textarea,
				$iframe = $this.$element.find('iframe'),
				type = $i.attr('data-type');
			$i.blur();
			if (type == 'link' || type == 'components') return false;
			$this.exec(type, null);
		}).on('click', '.af-editor-uploaded>.file-item', function() {
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
		}).on('dragstart', '.af-editor-uploaded>.file-item', function(e) {
			var $i = $(this);
			if ($i.data && $i.data('bs.tooltip')) $i.tooltip('hide'); // 드래그 시작시 툴팁 감추기
			e.originalEvent.dataTransfer.setData("TEXT", JSON.stringify({
				'type': $i.attr('data-type'),
				'srl': $i.attr('data-srl'),
				'title': $i.attr('data-title') || $i.attr('title')
			}));
		}).on('dragover', '.af-editor-content>textarea', function(e) {
			e.preventDefault();
		}).on('drop', '.af-editor-content>textarea', function(e) {
			e.preventDefault();
			dropFile(e, $this);
		}).on('insert.af.uploader', '.uploader-group', function(e, files) {
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
					.on('dragstart', function(e) {
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
		}).on('delete.af.uploader', '.uploader-group', function(e, files) {
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
		}).on('show.bs.popover', function() {
			$(this).data("bs.popover").tip().css({
				'max-width': '500px',
				'font-size': '12px'
			});
		});

		this.$element.find('.af-statebar-area [data-toggle="popover"]').popover({
			html: 1,
			trigger: 'manual',
			placement: 'top',
			content: '<div class="input-group" style="min-width:150px"></div>'
		}).on('inserted.bs.popover', function() {
			var $i = $(this),
				$popover = $i.data("bs.popover").tip().find('.popover-content'),
				type = $i.attr('data-type') || '';
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
					.html('<input type="text" class="form-control" style="width:150px" placeholder="Link"><a href="#" class="btn btn-default input-group-addon">OK</a>')
					.find('a').click(function() {
						var url = $(this).prev().val() || '';
						$this.exec(type, url);
						return false;
					});
			}
		}).on('shown.bs.popover', function() {
			var $i = $(this),
				$ipu = $i.data("bs.popover").tip().find('.popover-content').find('input');
			if ($ipu.length > 0) {
				$ipu.focus().select();
			} else {
				$ipu = $i;
			}
			$ipu.on('blur', function() {
				$i.popover('hide');
			});
		}).on('click', function() {
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
					.on('load', function() {
						var $_i = $(this);
						$_i.contents().find('body')
							.on('dragover',
								function(e) {
									e.preventDefault();
								}
							).on('drop', function(e) {
								e.preventDefault();
								dropFile(e, $this);
							});
						$_i.contents().find("head").html('<link rel="stylesheet" href="' + request_uri + 'module/editor/editor.min.css">');
						$_i.contents().find('body').html($txtara.val());
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

	AfEditor.prototype.paste = function(text, fm) {
		var $i = this.$textarea,
			range;

		fm = (fm !== false);

		if (!$i.is(':visible') || $i.length === 0) {
			$i = this.$element.find('iframe');
		}

		if ($i[0].tagName == 'TEXTAREA') {

			var startPos = $i.prop('selectionStart'),
				endPos = $i.prop('selectionEnd'),
				v = $i.val(),
				txtBefore = v.substring(0, startPos),
				txtAfter = v.substring(startPos + (endPos - startPos), v.length),
				selTxt = v.substring(startPos, endPos);

			if (fm) text = text.replace(/%s/, selTxt);
			$i.val(txtBefore + text + txtAfter);

			endPos = endPos + text.length - selTxt.length;
			if ($i[0].setSelectionRange) {
				$i[0].setSelectionRange(startPos, endPos);
			} else if ($i[0].createTextRange) {
				range = $i[0].createTextRange();
				range.collapse(true);
				range.moveEnd('character', endPos);
				range.moveStart('character', startPos);
				range.select();
			}

			$i.focus();

		} else {

			var sel, isie = false,
				w = $i[0].contentWindow;
			if (w) {
				if (w.getSelection) {
					sel = w.getSelection();
				} else if (w.document.getSelection) {
					sel = w.document.getSelection();
				} else {
					sel = w.document.selection && w.document.selection.createRange();
					if (sel.text) {
						isie = true;
					} else {
						return false;
					}
				}
				if (isie) {
					if (fm) text = text.replace(/%s/, sel.text);
					sel.pasteHTML(text);
				} else if (sel.getRangeAt && sel.rangeCount) {
					range = sel.getRangeAt(0);
					var el = w.document.createElement("div");
					el.appendChild(range.cloneContents());
					if (fm) text = text.replace(/%s/, el.innerText);
					range.deleteContents();
					range.insertNode($(text)[0]);
				}
				/**
				 else {
					var $body = $i.contents().find('body'),
						html = $body.html();
					$body.html(html + text.replace(/%s/, ''));
				}
				**/
			}

			$i.contents().find('body').focus();
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
			case 'strikeThrough':
			case 'insertorderedlist':
				if ($i[0].tagName == 'TEXTAREA') {
					switch (cmd) {
						case 'bold':
							this.paste('**%s**');
							break;
						case 'italic':
							this.paste('`%s`');
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
			case 'header':
				this.paste('<h3>%s</h3>');
				break;
			case 'indent':
				this.paste('<blockquote>%s</blockquote>');
				break;
			case 'codeblock':
				this.paste('<pre><code>%s</code></pre>');
				break;
			case 'link':
				var pattern = /https?:\/\/([a-z\.]*youtub?e?)\.(com|be)(\/embed\/|\/watch\?v\=|\/)([^\?\&]+)(.*)/i,
					t = value.getQuery('t') || value.getQuery('start');
				if (pattern.test(value)) {
					text = '<img class="afox_widget" widget="youtube" src="' + value.replace(pattern, "https://img.youtube.com/vi/$4/mqdefault.jpg\" width=\"560\" height=\"315\" vid=\"$4") + '"' + (t ? ' time="' + t + '"' : '') + '>' + "\n";
				} else {
					text = '<a href="' + value + '" target="_blank">' + value + '</a>';
					if ($i[0].tagName == 'TEXTAREA') text = '<' + value + '>';
				}
				this.paste(text, false);
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
