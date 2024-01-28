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
		document.querySelector('#afoxPageLoading')?.fadeOut($e => $e.remove())
	})
})()