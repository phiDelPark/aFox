/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 */

function afoxEditor(ID, OPTIONS) {
	this.htmlMode = false
	this.required = OPTIONS.required
	this.readonly = OPTIONS.readonly
	this.theme = window.localStorage.getItem('theme')

	const
		editor = document.querySelector('#editor' + ID.toUcFirst()),
		iframe = document.createElement('iframe'),
		textarea = editor.querySelector('[name=' + ID + ']')

	const
		typebar = editor.querySelectorAll('#editorTypebar [data-target]'),
		toolbar = editor.querySelectorAll('#editorToolbar button'),
		files = editor.querySelectorAll('[name="upload_files[]"]'),
		updFiles = editor.querySelector('#uploadFiles'),
		updedFiles = editor.querySelectorAll('#uploadedFiles img')

	const modeSwitch = (is_html) => {
		this.htmlMode = textarea.classList.contains('d-none')
		if (is_html && !this.htmlMode) {
			iframe.style.height = textarea.offsetHeight + "px"
			iframe.contentWindow.document.body.innerHTML = textarea.value
			textarea.classList.add('d-none')
			iframe.classList.remove('d-none')
			if(this.required) textarea.removeAttribute('required')
		} else if (!is_html && this.htmlMode) {
			textarea.style.height = iframe.offsetHeight + "px"
			textarea.value = iframe.contentWindow.document.body.innerHTML
			iframe.classList.add('d-none')
			textarea.classList.remove('d-none')
			if(this.required) textarea.setAttribute('required','')
		}
		this.htmlMode = textarea.classList.contains('d-none')
	}

	this.querySelector = (selectors) => {
		if(!this.htmlMode) {
			iframe.contentWindow.document.body.innerHTML = textarea.value
		}
		return iframe.contentWindow.document.body.querySelector(selectors)
	}

	this.querySelectorAll = (selectors) => {
		if(!this.htmlMode) {
			iframe.contentWindow.document.body.innerHTML = textarea.value
		}
		return iframe.contentWindow.document.body.querySelectorAll(selectors)
	}

	this.getSelection = () => {
		const w = iframe.contentWindow
		this.htmlMode ? w.document.body.focus() : textarea.focus()

		if (this.htmlMode) {
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

		if (this.htmlMode) {
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
			const nend = (textarea.value.length - selection.end) * -1;
			html = html.replace(/%s/, selection.value ? selection.value : '...')
			const
				txtBefore = textarea.value.slice(0, selection.start),
				txtAfter = nend ? textarea.value.slice(nend) : ''
			textarea.value = txtBefore + html + txtAfter
		}
	}

	iframe.classList.add('form-control', 'resizable', 'p-0', 'd-none')
	iframe.addEventListener('load', ee => {
		const s_head = `<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
		<link rel="stylesheet" href="${request_uri}module/editor/editor.min.css">`
		iframe.style.resize = 'vertical'
		if(this.theme) iframe.contentWindow.document.documentElement.setAttribute('data-bs-theme', this.theme)
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
		const regexp = /(?:src|title|alt)="([^"]+)"/gi
		const a = [...e.target.outerHTML.matchAll(regexp)]
		html = a[2][1].slice(0,5).toLowerCase() == 'image'
			? '<img src="%s" title="%s" alt="%s">' : '<a href="%s" title="%s">%s</a>'
		e.dataTransfer.setData('text', html.sprintf(a[0][1], a[2][1], a[1][1]))
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
			let html = `="${url}" title="blob-${index}" alt="${name} (${size})"`,
				image = type.slice(0, 5) == 'image'
			span.innerHTML = '<image src' + html + (image?'':' srcset="./module/editor/bi-binary.svg"') + '>'
				+ (e.target.files.length === 1 ? '<span>' + e.target.value + '</span>' : '')
			html = image ? '<image src' + html + '>' : `<a href${html}>${name} (${size})</a>`
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
				'bold':this.htmlMode ? '<strong>%s</strong>' : '**%s**',
				'italic':this.htmlMode ? '<em>%s</em>' : '*%s*',
				'underline':'<u>%s</u>',
				'strikethrough':this.htmlMode ? '<strike>%s</strike>' : '~~%s~~',
				'insertorderedlist':this.htmlMode ? '<ol><li>%s</li></ol>' : "\n"+'1. '+'%s'+"\n"+'2. ',
				'header':this.htmlMode ? '<h3>%s</h3>' : '## %s',
				'highlight':this.htmlMode ? '<code>%s</code>' : '`%s`',
				'indent':this.htmlMode ? '<blockquote>%s</blockquote>' : '> %s',
				'codeblock':this.htmlMode ? '<pre><code>%s</code></pre>' : '```'+"\n"+'%s'+"\n"+'```'
			}

		const cmd = e.target.closest('button').getAttribute('aria-label').toLowerCase()

		switch (cmd) {
			case 'components':
				break
			default:
				this.pasteHtml(htmlToCmd[cmd])
				break
		}

		this.htmlMode ? iframe.contentWindow.document.body.focus() : textarea.focus()
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

		this.htmlMode ? iframe.contentWindow.document.body.focus() : textarea.focus()
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
			if (this.htmlMode) {
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
					this.htmlMode
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

	if(OPTIONS.html) modeSwitch(true)
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