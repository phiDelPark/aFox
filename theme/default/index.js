/*!
 * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
 * Copyright 2011-2023 The Bootstrap Authors
 * Licensed under the Creative Commons Attribution 3.0 Unported License.
 */

(() => {
'use strict'
	const getStoredTheme = () => localStorage.getItem('theme')
	const setStoredTheme = theme => localStorage.setItem('theme', theme)
	const prefersScheme = '(prefers-color-scheme: dark)'

	const getPreferredTheme = () => {
		const storedTheme = getStoredTheme()
		if (storedTheme) {
			return storedTheme
		}

		return window.matchMedia(prefersScheme).matches ? 'dark' : 'light'
	}

	const setTheme = theme => {
		if (theme === 'auto' && window.matchMedia(prefersScheme).matches) {
			theme = 'dark'
		}
		setStoredTheme(theme)
		document.documentElement.setAttribute('data-bs-theme', theme)
	}

	setTheme(getPreferredTheme())

	const showActiveTheme = (theme) => {
		const switcher = document.querySelector('[data-bs-theme-value]')
		if (!switcher) return
		switcher.setAttribute('data-bs-theme-value', theme == 'dark' ? 'dark' : 'light')
	}

	window.matchMedia(prefersScheme).addEventListener('change', () => {
		const storedTheme = getStoredTheme()
		if (storedTheme !== 'light' && storedTheme !== 'dark') {
			setTheme(getPreferredTheme())
		}
	})

	window.addEventListener('DOMContentLoaded', () => {
		showActiveTheme(getPreferredTheme())

		document.querySelector('[data-bs-theme-value]')
			?.addEventListener('click', (e) => {
				let theme = e.target.getAttribute('data-bs-theme-value')
					theme = theme == 'dark' ? 'light' : 'dark'
				setTheme(theme)
				showActiveTheme(theme)
			})

		const $qlink = document.querySelector('#quickLink li')
		if($qlink){
			const $h1 = document.querySelectorAll('.current_content *:is(h1,h2,h3)')
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
		const b = t == 'confirm' ? '<button class="btn btn-primary" data-bs-whatever="ok">'+$_LANG['ok']+'</button>' : ''
		const html = '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h1 class="modal-title fs-5">'
		+$_LANG[t]+'</h1></div><div class="modal-body">'+s+'</div><div class="modal-footer">'+b+'<button class="btn btn-secondary" data-bs-dismiss="modal">'
		+$_LANG[t=='confirm'?'cancel':'close']+'</button></div></div></div>'
		return new Promise((resolve) => {
			const modal = document.createElement('DIV')
			modal.classList.add('modal', 'alert', 'fade')
			modal.setAttribute('tabindex', '-1')
			modal.setAttribute('data-bs-backdrop', 'static')
			modal.innerHTML = html
			document.body.insertBefore(modal, document.body.firstChild)
			const myModal = new bootstrap.Modal(modal, {})
			modal.addEventListener('hidden.bs.modal', e => e.target.remove())
			modal.querySelector('[data-bs-whatever],[data-bs-dismiss=modal]')
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
		let arr = v.split('|'); s += '<hr class="m-1 border-0">'
		const t = arr.length > 1 ? 'radio' : 'checkbox'
		if(arr.length < 2) arr = v.split(',')
		if(arr.length > 1){
			arr.forEach((a,i) => {
				s += '&nbsp;<label><input type="'+t+'" value="'+a+'"> '+a+'</label>&nbsp;'
			})
		}else s += '<input type="text" value="'+v+'" style="width:100%">'
		return window.alert(s, 'confirm')
	}
})()