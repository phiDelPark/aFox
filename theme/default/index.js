/*!
 * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
 * Copyright 2011-2023 The Bootstrap Authors
 * Licensed under the Creative Commons Attribution 3.0 Unported License.
 */

(() => {
'use strict'
	const getStoredTheme = () => localStorage.getItem('theme')
	const setStoredTheme = theme => localStorage.setItem('theme', theme)

	const getPreferredTheme = () => {
		const storedTheme = getStoredTheme()
		if (storedTheme) {
		return storedTheme
	}

		return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
	}

	const setTheme = theme => {
		if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
			document.documentElement.setAttribute('data-bs-theme', 'dark')
		} else {
			document.documentElement.setAttribute('data-bs-theme', theme)
		}
	}

	setTheme(getPreferredTheme())

	const showActiveTheme = (theme) => {
		const switcher = document.querySelector('[data-bs-theme-value]')
		if (!switcher) return
		switcher.setAttribute('data-bs-theme-value', theme == 'dark' ? 'dark' : 'light')
	}

	window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
		const storedTheme = getStoredTheme()
		if (storedTheme !== 'light' && storedTheme !== 'dark') {
			setTheme(getPreferredTheme())
		}
	})

	window.addEventListener('DOMContentLoaded', () => {
		showActiveTheme(getPreferredTheme())

		document.querySelector('[data-bs-theme-value]')
			.addEventListener('click', (e) => {
				let theme = e.target.getAttribute('data-bs-theme-value')
					theme = theme == 'dark' ? 'light' : 'dark'
				setStoredTheme(theme)
				setTheme(theme)
				showActiveTheme(theme)
			})

		const $qlink = document.querySelector('#quickLink li')
		if($qlink){
			const $h1 = document.querySelectorAll('.current_content h1,.current_content h2')
			for (let idx = $h1.length - 1; idx > -1 ; idx--) {
				if(!$h1[idx].innerText || ($h1.length - idx) > 22) continue
				const $li = document.createElement('LI'); $li.innerHTML = $qlink.innerHTML
				const $lia = $li.querySelector('a'), $lisvg = $lia.querySelector('svg')
				$h1[idx].id = 'quickLink_' + idx; $lia.href = '#' + $h1[idx].id
				$lia.innerHTML = $lisvg.outerHTML + $h1[idx].innerText; $qlink.after($li)
			}
		}
	})

	window.addEventListener('load', () => {
		document.querySelector('#loading_page')?.fadeOut($e => $e.remove())
	})

	window.alert = function(s, t = 'alert') {
		console.log(s);
		const b = t == 'confirm' ? '<button type="button" class="btn btn-primary" data-bs-whatever="ok">'+$_LANG['ok']+'</button>' : ''
		const html = '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">\
		<h1 class="modal-title fs-5">'+$_LANG[t]+'</h1></div><div class="modal-body">'+s+'</div>\
		<div class="modal-footer">'+b+'<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'+$_LANG[t == 'confirm' ? 'cancel' : 'close']+'</button></div></div></div>'
		return new Promise((resolve) => {
			const modal = document.createElement('DIV')
			modal.classList.add('modal', 'fade')
			modal.setAttribute('tabindex', '-1')
			modal.setAttribute('aria-hidden', 'true')
			modal.setAttribute('data-bs-backdrop', 'static')
			modal.innerHTML = html
			document.body.insertBefore(modal, document.body.firstChild)
			const myModal = new bootstrap.Modal(modal, {})
			modal.addEventListener('hidden.bs.modal', e => e.target.remove())
			modal.querySelector('[data-bs-whatever]')
				?.addEventListener('click', _ => {
					let vals = '';
					modal.querySelectorAll('[class=modal-body] input')
						.forEach(el=>{
							if(el.checked) vals += ',' + el.value
						})
					resolve(vals?.slice(1))
					myModal.hide()
				})
			myModal.show()
		})
	}
	window.confirm = function(s) {
		return window.alert(s, 'confirm')
	}
	window.prompt = function(s, v) {
		v = v.split('|'); s += '<hr class="m-1 border-0">'
		const t = v.length > 1 ? 'checkbox' : 'radio'
		if(v.length < 2) v = v.split(',')
		if(v.length > 1){
			v.forEach((a,i) => {
				s += '<label><input type="'+t+'" value="'+a+'"> '+a+'</label> '
			})
		}else s += '<input type="text" value="'+v+'">'
		return window.alert(s, 'confirm')
	}
})()