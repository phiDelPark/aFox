/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */

(function($) {
	'use strict';

	function dropFile(e, $i) {
		var text, data = JSON.parse(e.originalEvent.dataTransfer.getData("TEXT") || '{}'),
			title = data['title'],
			srl = data['srl'] || data['index'],
			url = ((data['srl'] || false) ? (request_uri + '?file=' + srl) : 'af-editor-tmpfile=' + srl),
			type = (data['type'].split('/')[0] || 'binary');

		if ($i[0].tagName == 'TEXTAREA') {

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

			pasteTxtWithSel(text, '', $i);
			$i.focus();
		} else {

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

			pasteTxtWithSel(text, '', $i);
			$i.contents().find('body').focus();
		}
	}

	function pasteTxtWithSel(sTxt, eTxt, $i) {
		var range;

		if ($i[0].tagName == 'TEXTAREA') {

			var startPos = $i.prop('selectionStart'),
				endPos = $i.prop('selectionEnd'),
				v = $i.val(),
				txtBefore = v.substring(0, startPos),
				txtAfter = v.substring(startPos + (endPos - startPos), v.length),
				selTxt = v.substring(startPos, endPos);
			$i.val(txtBefore + sTxt + selTxt + eTxt + txtAfter);

			endPos = endPos + sTxt.length + eTxt.length;
			if ($i[0].setSelectionRange) {
				$i[0].setSelectionRange(startPos, endPos);
			} else if ($i[0].createTextRange) {
				range = $i[0].createTextRange();
				range.collapse(true);
				range.moveEnd('character', endPos);
				range.moveStart('character', startPos);
				range.select();
			}

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
					sel.pasteHTML(sTxt + sel.text + eTxt);
				} else if (sel.getRangeAt && sel.rangeCount) {
					range = sel.getRangeAt(0);
					var el = w.document.createElement("div");
					el.appendChild(range.cloneContents());
					el = $(sTxt + el.innerHTML + eTxt)[0];
					range.deleteContents();
					range.insertNode(el);
				}
				/**
				 else {
					var $body = $i.contents().find('body'),
						html = $body.html();
					$body.html(html + sTxt + eTxt);
				}
				**/
			}

		}
	}

	function textareaExecCmd(cmd, val, $i) {
		var sTxt = '',
			eTxt = '';

		switch (cmd) {
			case 'bold':
				sTxt = '**';
				eTxt = '**';
				break;
			case 'italic':
				sTxt = '`';
				eTxt = '`';
				break;
			case 'underline':
				sTxt = '~~';
				eTxt = '~~';
				break;
			case 'header':
				sTxt = "\n" + '### ';
				break;
			case 'insertorderedlist':
				sTxt = "\n" + '1. ';
				eTxt = "\n" + '2. ';
				break;
			case 'indent':
				sTxt = "\n" + '> ';
				break;
			case 'codeblock':
				sTxt = "\n" + '```' + "\n";
				eTxt = "\n" + '```' + "\n";
				break;
			case 'link':
				sTxt = '<' + val + '>';
				eTxt = '';
				break;
			case 'video':
				sTxt = '[youtube](' + val + ')';
				eTxt = '';
				break;
		}

		if (sTxt || eTxt) {
			pasteTxtWithSel(sTxt, eTxt, $i);
		}

		$i.focus();
	}

	function iFrameExecCmd(cmd, val, $i) {
		var doc,
			sTxt = '',
			eTxt = '';

		switch (cmd) {
			case 'bold':
			case 'italic':
			case 'underline':
			case 'insertorderedlist':
				doc = $i[0].contentWindow.document;
				doc.execCommand(cmd, false, val);
				break;
			case 'header':
				sTxt = '<h3>';
				eTxt = '</h3>';
				pasteTxtWithSel(sTxt, eTxt, $i);
				break;
			case 'indent':
				sTxt = '<blockquote>';
				eTxt = '</blockquote>';
				pasteTxtWithSel(sTxt, eTxt, $i);
				break;
			case 'codeblock':
				sTxt = '<pre><code>';
				eTxt = '</code></pre>';
				pasteTxtWithSel(sTxt, eTxt, $i);
				break;
			case 'link':
				sTxt = '<a href="' + val + '" target="_blank">' + val + '</a>';
				eTxt = '';
				pasteTxtWithSel(sTxt, eTxt, $i);
				break;
			case 'video':
				// 보안을 위해 iframe 사용 금지 (미디어 관리자 에드온을 사용하자)
				//var pattern = /(https?:\/\/[a-z\.]*youtub?e?)\.(com|be)(\/embed\/|\/watch\?v\=|\/)([a-zA-Z0-9]+)/i;
				//sTxt = '<iframe src="' + val.replace(pattern, "https://www.youtube.com/embed/$4") + '" frameborder="0" allowfullscreen></iframe>';
				sTxt = '<div>[youtube](' + val.escapeHtml() + ')</div>';
				eTxt = '';
				pasteTxtWithSel(sTxt, eTxt, $i);
				break;
		}

		$i.contents().find('body').focus();
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
			title = title.substring(title.length - 12, title.length);
			$i.tooltip({
				html: 1,
				container: 'body',
				title: function() {
					return '<img src="' + url + '" style="width:80px;height:80px"><div style="width:80px;overflow:hidden;white-space:nowrap;">' + title + '</div>';
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
				$txtarea = $this.$textarea,
				$iframe = $this.$element.find('iframe'),
				type = $i.attr('data-type');
			$i.blur();
			if (type == 'link' || type == 'video') return false;
			if ($txtarea.is(':visible') && $txtarea.length > 0) {
				textareaExecCmd(type, null, $txtarea);
			} else if ($iframe.length > 0) {
				iFrameExecCmd(type, null, $iframe);
			}
		}).on('click', '.af-editor-uploaded-list>.file-item', function() {
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
		}).on('dragstart', '.af-editor-uploaded-list>.file-item', function(e) {
			var $i = $(this);
			if ($i.data && $i.data('bs.tooltip')) $i.tooltip('hide'); // 드래그 시작시 툴팁 감추기
			e.originalEvent.dataTransfer.setData("TEXT", JSON.stringify({
				'type': $i.attr('data-type'),
				'srl': $i.attr('data-srl'),
				'title': $i.attr('data-title') || $i.attr('title')
			}));
		}).on('dragover', '.af-editor-area>textarea', function(e) {
			e.preventDefault();
		}).on('drop', '.af-editor-area>textarea', function(e) {
			e.preventDefault();
			dropFile(e, $(this));
		}).on('insert.af.fileupload', '.fileupload-group', function(e, files) {
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
		}).on('delete.af.fileupload', '.fileupload-group', function(e, files) {
			e.preventDefault();
			var $c = $(this).find('.file-caption'),
				$g = $c.closest('.fileupload-group');
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
			content: '<div class="input-group"><input type="text" class="form-control" style="width:150px"><a href="#" class="btn btn-default input-group-addon">OK</a></div>'
		}).click(function() {
			$(this).popover('toggle');
		}).on('inserted.bs.popover', function() {
			var $i = $(this),
				$popover = $i.data("bs.popover").tip().find('.popover-content'),
				type = $i.attr('data-type') || '';
			$popover.find('input').attr('placeholder', type == 'video' ? 'YouTube' : 'Link');
			$popover.find('a').click(function() {
				var url = $(this).prev().val() || '',
					$txtarea = $this.$textarea,
					$iframe = $this.$element.find('iframe');
				if ($txtarea.is(':visible') && $txtarea.length > 0) {
					textareaExecCmd(type, url, $txtarea);
				} else if ($iframe.length > 0) {
					iFrameExecCmd(type, url, $iframe);
				}
				$i.popover('hide');
				return false;
			});
		});

		this.$element.find('.fileupload-group .file-item').each(function() {
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
		var $txtarea = this.$textarea,
			$iframe = this.$element.find('iframe'),
			height = $txtarea.css('height') || '',
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
								dropFile(e, $_i);
							});
						$_i.contents().find("head").html('<link rel="stylesheet" href="' + request_uri + 'module/editor/editor.min.css">');
						$_i.contents().find('body').html($txtarea.val());
						if (!readonly) $_i.contents()[0].designMode = 'on';
						//$_i[0].contentWindow.document.designMode = 'on';
					}).insertAfter($txtarea.hide()).end();

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
				text = $txtarea.val();
			}

			$txtarea.css('height', height).val(text).show();
			if (readonly) {
				$txtarea.attr('readonly', 'readonly');
			} else {
				$txtarea.removeAttr('readonly');
			}
		}

	};

	AfEditor.prototype.toggle = function() {
		this.switch(this.$textarea.is(':visible'));
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
