/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 */

function afoxEditor(ID, options) {
	this.html = options.html
	this.required = options.required
	this.readonly = options.readonly
	this.isHtmlmode = false

	const
		editor = document.querySelector('#' + ID),
		iframe = document.createElement('iframe'),
		textarea = editor.querySelector('[name=' + options.name + ']')

	const
		typebar = editor.querySelectorAll('#editorTypebar [data-target]'),
		toolbar = editor.querySelectorAll('#editorToolbar button'),
		files = editor.querySelectorAll('[name="upload_files[]"]'),
		updFiles = editor.querySelector('#uploadFiles'),
		updedFiles = editor.querySelectorAll('#uploadedFiles img')

	const modeSwitch = (is_html) => {
		this.isHtmlmode = textarea.classList.contains('d-none')
		if (is_html && !this.isHtmlmode) {
			iframe.style.height = textarea.offsetHeight + "px"
			iframe.contentWindow.document.body.innerHTML = textarea.value
			textarea.classList.add('d-none')
			iframe.classList.remove('d-none')
			if(this.required) textarea.removeAttribute('required')
		} else if (!is_html && this.isHtmlmode) {
			textarea.style.height = iframe.offsetHeight + "px"
			textarea.value = iframe.contentWindow.document.body.innerHTML
			iframe.classList.add('d-none')
			textarea.classList.remove('d-none')
			if(this.required) textarea.setAttribute('required','')
		}
		this.isHtmlmode = textarea.classList.contains('d-none')
	}

	this.querySelector = (selectors) => {
		if(!this.isHtmlmode) {
			iframe.contentWindow.document.body.innerHTML = textarea.value
		}
		return iframe.contentWindow.document.body.querySelector(selectors)
	}

	this.querySelectorAll = (selectors) => {
		if(!this.isHtmlmode) {
			iframe.contentWindow.document.body.innerHTML = textarea.value
		}
		return iframe.contentWindow.document.body.querySelectorAll(selectors)
	}

	this.getSelection = () => {
		const w = iframe.contentWindow
		this.isHtmlmode ? w.document.body.focus() : textarea.focus()

		if (this.isHtmlmode) {
			if (w.getSelection) {
				return w.getSelection()
			} else if (w.document.getSelection) {
				return w.document.getSelection()
			}
		} else {
			return {
				start: textarea.selectionStart,
				end: textarea.selectionEnd,
				value: textarea.value.slice(
					textarea.selectionStart,
					textarea.selectionEnd
				)
			}
		}
	}

	this.pasteHtml = (html) => {
		const selection = this.getSelection()

		if (this.isHtmlmode) {
			let range = selection.getRangeAt(0)
			if (range) {
				const el = iframe.contentWindow.document.createElement("div")
				el.appendChild(range.cloneContents())
				el.innerHTML = html.replace(/%s/, el.innerHTML ? el.innerHTML : '...')
				range.deleteContents()
				range.insertNode(el.firstElementChild)
				range.setStart(selection.focusNode, selection.focusOffset)
				range.setEnd(selection.anchorNode, selection.anchorOffset)
			}
		} else {
			html = html.replace(/%s/, selection.value ? selection.value : '...')
			const
				txtBefore = textarea.value.slice(0, selection.start),
				txtAfter = textarea.value.slice(
					(textarea.value.length - selection.end) * -1
				)
			textarea.value = txtBefore + html + txtAfter
		}
	}

	iframe.classList.add('form-control', 'resizable', 'p-0', 'd-none')
	iframe.addEventListener('load', ee => {
		const s_head = `<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
		<link rel="stylesheet" href="${request_uri}module/editor/editor.min.css">`
		iframe.style.resize = 'vertical'
		iframe.contentWindow.document.head.innerHTML = s_head
		iframe.contentWindow.document.designMode = this.readonly ? 'off' : 'on'
		iframe.contentWindow.document.body.innerHTML = textarea.value
		iframe.contentWindow.document.body.addEventListener('drop', e => {
			e.preventDefault()
			this.pasteHtml(e.dataTransfer.getData('text') || '')
		})
		iframe.contentWindow.document.body.addEventListener('focus', e => iframe.classList.add('focused'))
		iframe.contentWindow.document.body.addEventListener('blur', e => iframe.classList.remove('focused','is-invalid'))
	})

	textarea.parentNode.insertBefore(iframe, textarea)
	if(this.readonly) textarea.readOnly = true

	const clickRemoveFile = (e) => {
		const srl = e.target.getAttribute('src').getQuery('file')
		const el_del = editor.querySelector('[name="remove_files[]"][value="' + srl + '"]')
		if (el_del) {
			el_del.remove()
			//e.target.style.filter = 'invert(0)'
			//e.target.style.webkitFilter = 'invert(0)'
		} else {
			const input = document.createElement('input')
			input.setAttribute('type', 'hidden')
			input.setAttribute('name', 'remove_files[]')
			input.setAttribute('value', srl)
			e.target.parentNode.insertBefore(input, e.target)
			//e.target.style.filter = 'invert(100%)'
			//e.target.style.webkitFilter = 'invert(100%)'
		}
	}

	const dragStartFile = (e) => {
		const srl = e.target.getAttribute('src').getQuery('file')
		if (editor.querySelector('[name="remove_files[]"][value="' + srl + '"]')) {
			e.preventDefault()
			e.stopPropagation()
			return false
		}
		if(/<[img][^>]+[class]=[\\"\']+image\//g.test(e.target.outerHTML)) {
			e.dataTransfer.setData('text', e.target.outerHTML)
		} else {
			const html = e.target.outerHTML.replace(
				/^(<[img]+\ssrc)([^>]+title=[\\"\']+)([^>\\"\']+)([^>]+)srcset=[\\"\']+[^>\\"\']+([^>]+>)$/g,
				'<a href' + "$2$3$4" + 'target="_file' + "$5$3" + '</a>'
			)
			e.dataTransfer.setData('text', html)
		}
	}

	updedFiles.forEach(el => {
		el.addEventListener('click', clickRemoveFile)
		el.addEventListener('dragstart', dragStartFile)
	})

	const changeUploadFile = (e) => {
		window.dispatchEvent(new Event('beforeunload', {bubbles: true, cancelable: false}))
		while (updFiles.firstChild) updFiles.removeChild(updFiles.lastChild)
		// todo 후에 files 를 새로 고침 안하고 계속 넣어 보내기 하자
		const elURL = (window.URL || window.webkitURL)
		Object.keys(e.target.files).map(key => {
			let span = document.createElement('span'),
				name = e.target.files[key].name.escapeHTML(),
				type = e.target.files[key].type.escapeHTML(),
				size = e.target.files[key].size.shortFileSize(),
				url = elURL.createObjectURL(e.target.files[key])
			window.uploadFiles.push(url)
			const index = window.uploadFiles.length - 1
			if(type.slice(0, 5) != 'image') type = type + '" srcset="./module/editor/bi-binary.svg'
			let html = `="${url}" title="${name} (${size})" alt="${name}" target="${index}"`
			span.innerHTML = '<image class="' + type + '" src' + html + '>'
				+ (e.target.files.length === 1 ? '<span>' + e.target.value + '</span>' : '')
			html = type.slice(0, 5) == 'image'
				? '<image src' + html + '>' : `<a href${html}>${name} (${size})</a>`
			span.addEventListener('dragstart', ee => {
				ee.dataTransfer.setData('text', html)
			})
			updFiles.append(span)
		})
	}

	files.forEach(el => el.addEventListener('change', changeUploadFile))

	const clickToolbar = (e) => {
		e.preventDefault()
		e.stopPropagation()

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
			}

		const cmd = e.target.closest('button').getAttribute('aria-label').toLowerCase()

		switch (cmd) {
			case 'bold':
			case 'italic':
			case 'underline':
			case 'strikethrough':
			case 'insertorderedlist':
				if (this.isHtmlmode) {
					iframe.contentWindow.document.execCommand(cmd, false, null)
				} else {
					this.pasteHtml(htmlToCmd[cmd])
				}
				break
			case 'highlight':
				this.pasteHtml(this.isHtmlmode ? htmlToCmd[cmd] : '`%s`')
				break
			case 'components':
				break
			default:
				this.pasteHtml(htmlToCmd[cmd])
				break
		}

		this.isHtmlmode ? iframe.contentWindow.document.body.focus() : textarea.focus()
	}

	const clickTypebar = (e) => {
		e.preventDefault()
		e.stopPropagation()

		const
			elt = editor.querySelector('[name=' + e.target.dataset.target + ']'),
			val = e.target.dataset.value

		if(val == 'true'){
			elt.value = elt.value == val ? 'false' : 'true'
			if(elt.value == 'true') e.target.classList.add('checked')
			else  e.target.classList.remove('checked')
		} else{
			typebar.forEach(i =>{
				if(i.getAttribute('data-value') != 'true')
					i.classList.remove('checked')
			})
			elt.value = val
			e.target.classList.add('checked')

			modeSwitch(e.target.innerText === 'HTML')
		}

		this.isHtmlmode ? iframe.contentWindow.document.body.focus() : textarea.focus()
	}

	toolbar.forEach(el => el.addEventListener('click', clickToolbar))
	typebar.forEach(el => el.addEventListener('click', clickTypebar))
	typebar.forEach(el => el.addEventListener('keydown', e => {
		if (e.which == 32 && e.shiftKey !== true) { //"Space"
			e.preventDefault()
			e.stopPropagation()
			e.target.click()
		}
	}))

	const form = editor.closest('FORM')

	const check_groups = form.querySelectorAll('.checkbox-group.required');
	check_groups.forEach(el => el.addEventListener('change', _=> el.classList.remove('is-invalid')))

	if(form.hasAttribute('needvalidate')) form.setAttribute('novalidate', '')
	form.addEventListener('submit', e => {
		try {
			if (this.isHtmlmode) {
				textarea.value = iframe.contentWindow.document.body.innerHTML
			}
			check_groups.forEach(el => {
				if(el.querySelectorAll('[type=checkbox]:checked')?.length === 0){
					el.classList.add('is-invalid')
					e.preventDefault()
					e.stopPropagation()
				}
			})
			content_validity = !this.required || textarea.value
			if(e.currentTarget.hasAttribute('needvalidate')){
				if (!content_validity || !e.currentTarget.checkValidity()) {
					e.preventDefault()
					e.stopPropagation()
					if(!content_validity) iframe.classList.add('is-invalid')
				}
				e.currentTarget.classList.add('was-validated')
			} else {
				if (!content_validity) {
					this.isHtmlmode
						? iframe.contentWindow.document.body.focus() : textarea.focus()
					throw new Error(this.required)
				}
			}
		} catch (error) {
			e.preventDefault()
			e.stopPropagation()
			console.log(error)
			alert(error)
		}
	}, false)

	if(this.html) modeSwitch(true)
}

(function() {
	'use strict'
	window.uploadFiles = []
	window.addEventListener('beforeunload', e => {
		let url // remove files from spent memory
		const elURL = (window.URL || window.webkitURL)
		while (window.uploadFiles.length > 0) {
			url = window.uploadFiles.pop()
			elURL.revokeObjectURL(url)
			//console.error(url)
		}
	})
})()