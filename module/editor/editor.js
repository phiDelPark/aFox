/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 */

function afEditor(ID, options) {
	console.log(options);

	this.html = options.html;
	this.required = options.required;
	this.readonly = options.readonly;
	this.isHtmlmode = false;

	const
		editor = document.querySelector('#' + ID),
		textarea = editor.querySelector('[name=' + options.name + ']'),
		iframe = document.createElement('iframe');

	const
		typebar = editor.querySelectorAll('#editorTypebar [data-target]'),
		toolbar = editor.querySelectorAll('#editorToolbar button'),
		files = editor.querySelectorAll('[name="upload_files[]"]'),
		updFiles = editor.querySelector('#uploadFiles'),
		updedFiles = editor.querySelectorAll('#uploadedFiles img');

	const modeSwitch = (is_html) => {
		this.isHtmlmode = textarea.classList.contains('d-none');
		if (is_html && !this.isHtmlmode) {
			iframe.style.height = textarea.clientHeight + "px";
			iframe.contentWindow.document.body.innerHTML = textarea.value;
			textarea.classList.add('d-none');
			iframe.classList.remove('d-none');
		} else if (!is_html && this.isHtmlmode) {
			textarea.style.height = iframe.clientHeight + "px";
			textarea.value = iframe.contentWindow.document.body.innerHTML;
			iframe.classList.add('d-none');
			textarea.classList.remove('d-none');
		}
		this.isHtmlmode = textarea.classList.contains('d-none');
	}

	const getSelection = (html) => {
		const w = iframe.contentWindow;
		this.isHtmlmode ? w.document.body.focus() : textarea.focus();

		if (this.isHtmlmode) {
			if (w.getSelection) {
				return w.getSelection();
			} else if (w.document.getSelection) {
				return w.document.getSelection();
			}
		} else {
			return {
				start: textarea.selectionStart,
				end: textarea.selectionEnd,
				value: textarea.value.slice(
					textarea.selectionStart,
					textarea.selectionEnd
				)
			};
		}
	};

	const pasteHtml = (html) => {
		const selection = getSelection();

		if (this.isHtmlmode) {
			let range = selection.getRangeAt(0);
			if (range) {
				const el = iframe.contentWindow.document.createElement("div");
				el.appendChild(range.cloneContents());
				el.innerHTML = html.replace(/%s/, el.innerHTML);
				range.deleteContents();
				range.insertNode(el.firstElementChild);
				range.setStart(selection.focusNode, selection.focusOffset);
				range.setEnd(selection.anchorNode, selection.anchorOffset);
			}
		} else {
			html = html.replace(/%s/, selection.value ? selection.value : '...');
			const
				txtBefore = textarea.value.slice(0, selection.start),
				txtAfter = textarea.value.slice(
					(textarea.value.length - selection.end) * -1
				);
			textarea.value = txtBefore + html + txtAfter;
		}
	}

	const clickTypebar = (e) => {
		const tid = e.target.getAttribute('data-target'),
			elt = editor.querySelector('[name=' + tid + ']');
		elt.value = e.target.getAttribute('data-value');
		typebar.forEach(i => i.classList.remove('checked'));
		e.target.classList.add('checked');
		modeSwitch(e.target.innerText === 'HTML');
	}

	const clickToolbar = (e) => {
		const htmlToCmd = {
				'bold':'**%s**',
				'italic':'*%s*',
				'underline':'<u>%s</u>',
				'strikethrough':'~~%s~~',
				'insertorderedlist':"\n"+'1. '+'%s'+"\n"+'2. ',
				'header':'<h2>%s</h2>',
				'indent':'<blockquote>%s</blockquote>',
				'codeblock':'<pre><code>%s</code></pre>',
				'highlight':'<code>%s</code>'
			};

		let	cmd = e.target.closest('button');
		cmd = cmd.getAttribute('aria-label').toLowerCase();

		switch (cmd) {
			case 'bold':
			case 'italic':
			case 'underline':
			case 'strikethrough':
			case 'insertorderedlist':
				if (this.isHtmlmode) {
					iframe.contentWindow.document.execCommand(cmd, false, null);
				} else {
					pasteHtml(htmlToCmd[cmd]);
				}
				break;
			case 'highlight':
				pasteHtml(this.isHtmlmode ? htmlToCmd[cmd] : '`%s`');
				break;
			default:
				pasteHtml(htmlToCmd[cmd]);
				break;
		}

		this.isHtmlmode ? iframe.contentWindow.document.body.focus() : textarea.focus();
	}

	iframe.classList.add('form-control', 'shadow-ring', 'resizable', 'p-0', 'd-none');
	iframe.addEventListener('load', e => {
		const s_head = `<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
		<link rel="stylesheet" href="${request_uri}module/editor/editor.min.css">`;
		iframe.style.resize = 'vertical';
		iframe.contentWindow.document.head.innerHTML = s_head;
		iframe.contentWindow.document.designMode = this.readonly ? 'off' : 'on';
		iframe.contentWindow.document.body.innerHTML = textarea.value;
		iframe.contentWindow.document.body.addEventListener('drop', ee => {
			ee.preventDefault();
			pasteHtml(ee.dataTransfer.getData('text') || '');
		});
	});

	textarea.parentNode.insertBefore(iframe, textarea);
	if(this.readonly) textarea.readOnly = true;

	updedFiles.forEach(el => {
		el.addEventListener('dragstart', e => {
			if(/<[img][^>]+[class]=[\\"\']+image\//g.test(el.outerHTML)) {
				e.dataTransfer.setData('text', el.outerHTML);
			} else {
				const html = el.outerHTML.replace(
					/^(<[img]+\ssrc)([^>]+title=[\\"\']+)([^>\\"\']+)([^>]+)srcset=[\\"\']+[^>\\"\']+([^>]+>)$/g,
					'<a href' + "$2$3$4" + 'target="_blank' + "$5$3" + '</a>'
				);
				console.log(html);
				e.dataTransfer.setData('text', html);
			}
		});
	});

	files.forEach(el => {
		el.addEventListener('change', (e) => {
			window.trigger('beforeunload'); // trigger is in afox.common.js
			while (updFiles.firstChild) updFiles.removeChild(updFiles.lastChild);
			// todo 후에 files 를 새로 고침 안하고 계속 넣어 보내기 하자
			const elURL = (window.URL || window.webkitURL);
			Object.keys(e.target.files).map(key => {
				let span = document.createElement('span'),
					name = e.target.files[key].name.escapeHtml(),
					size = e.target.files[key].size.shortSize(),
					type = e.target.files[key].type.escapeHtml(),
					url = elURL.createObjectURL(e.target.files[key]);
				window.uploadFiles.push(url);
				const index = window.uploadFiles.length - 1;
				if(type.slice(0, 5) != 'image') type = type + '" srcset="./module/editor/bi-binary.svg'
				let html = `="${url}" title="${name} (${size})" alt="${index}.${name}"`;
				span.innerHTML = '<image class="' + type + '" src' + html + '>'
					+ (e.target.files.length === 1 ? '<span>' + el.value + '</span>' : '');
				html = type.slice(0, 5) == 'image'
					? '<image src' + html + '>' : `<a href${html}>${name} (${size})</a>`;
				span.addEventListener('dragstart', ee => {
					ee.dataTransfer.setData('text', html);
				});
				updFiles.append(span);
			});
		});
	});

	toolbar.forEach(el => el.addEventListener('click', clickToolbar));
	typebar.forEach(el => el.addEventListener('click', clickTypebar));
	typebar.forEach(el => el.addEventListener('keydown', e => {
		if (e.which == 32 && e.shiftKey !== true) { //"Space"
			e.preventDefault();
			e.target.click();
		}
	}));

	editor.closest('FORM').addEventListener('submit', e => {
		try {
			if (this.isHtmlmode) {
				textarea.value = iframe.contentWindow.document.body.innerHTML;
			}
			if (this.required && !textarea.value) {
				this.isHtmlmode
					? iframe.contentWindow.document.body.focus() : textarea.focus();
				throw new Error('Please enter the content.');
			}
		} catch (error) {
			e.stopPropagation();
			e.preventDefault();
			console.error(error);
			alert(error);
			return false;
		}
		return true;
	});

	if(this.html) modeSwitch(true);
}

window.uploadFiles = [];
window.onbeforeunload = e => {
	let url; // 파일 사용 후 메모리 제거
	const elURL = (window.URL || window.webkitURL);
	while (window.uploadFiles.length > 0) {
		url = window.uploadFiles.pop();
		elURL.revokeObjectURL(url);
		console.error(url);
	}
}