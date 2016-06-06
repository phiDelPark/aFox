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

			var cursorPos = $i.prop('selectionStart');
			var v = $i.val();
			var textBefore = v.substring(0, cursorPos);
			var textAfter = v.substring(cursorPos, v.length);
			$i.val(textBefore + text + textAfter);
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

			var sel, range, w = $i[0].contentWindow;
			if (w) {
				var isie = false;
				if (w.getSelection) {
					sel = w.getSelection();
				} else if (w.document.getSelection) {
					sel = w.document.getSelection();
				} else {
					var selection = w.document.selection && w.document.selection.createRange();
					if (selection.text) {
						isie = true;
						sel = selection;
					}
					return false;
				}
				if (isie) {
					sel.pasteHTML(text);
				} else {
					if (sel.getRangeAt && sel.rangeCount) {
						var el = w.document.createElement("div");
						el.innerHTML = text;
						range = sel.getRangeAt(0);
						range.deleteContents();
						range.insertNode(el);
					} else {
						var html = $i.contents().find('body').html();
						$i.contents().find('body').html(html + text);
					}
				}
			}
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
			e.originalEvent.dataTransfer.setData("TEXT", JSON.stringify({
				'type': $i.attr('data-type'),
				'srl': $i.attr('data-srl'),
				'title': $i.attr('title')
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

				$('<i class="file-item" draggable="true" title="' + (ismt ? title : '') + '" data-type="' + type + '" data-index="' + i + '" data-path="' + path + '">')
					.text(ismt ? '' : title)
					.appendTo($c)
					.on('dragstart', function(e) {
						var $i = $(this);
						e.originalEvent.dataTransfer.setData("TEXT", JSON.stringify({
							'type': $i.attr('data-type'),
							'index': $i.attr('data-index'),
							'title': (ismt ? $i.attr('title') : $i.text()),
							'path': $i.attr('data-path')
						}));
					});
			});
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
