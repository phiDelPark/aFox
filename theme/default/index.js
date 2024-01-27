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

	const showActiveTheme = (theme, focus = false) => {
		const themeSwitcher = document.querySelector('#theme-color-modes')

		if (!themeSwitcher) return

		const themeSwitcherText = document.querySelector('#theme-color-modes-text')
		const activeThemeIcon = document.querySelector('.theme-icon-active use')
		const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
		const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href')

		document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
			element.classList.remove('active')
			element.setAttribute('aria-pressed', 'false')
		})

		btnToActive.classList.add('active')
		btnToActive.setAttribute('aria-pressed', 'true')
		activeThemeIcon.setAttribute('href', svgOfActiveBtn)
		const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`
		themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)

		if (focus) {
			themeSwitcher.focus()
		}
	}

	window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
		const storedTheme = getStoredTheme()
		if (storedTheme !== 'light' && storedTheme !== 'dark') {
			setTheme(getPreferredTheme())
		}
	})

	window.addEventListener('DOMContentLoaded', () => {
		showActiveTheme(getPreferredTheme())

		document.querySelectorAll('[data-bs-theme-value]')
			.forEach(toggle => {
				toggle.addEventListener('click', () => {
					const theme = toggle.getAttribute('data-bs-theme-value')
					setStoredTheme(theme)
					setTheme(theme)
					showActiveTheme(theme, true)
				})
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
		const content_modal = document.querySelector('#themeContentModal')
		content_modal.addEventListener('show.bs.modal', (e) => {
			const title = e.target.querySelector('.modal-title'),
				body = e.target.querySelector('.modal-body'),
				action = e.relatedTarget.dataset.ajaxAction.split('.'),
				responses = e.relatedTarget.dataset.ajaxResponses.split(',')
			const data = {
				module: action[0],
				act: action[1],
				response_tags: responses,
				...e.relatedTarget.href.getQuery(),
			}
			exec_ajax(data)
				.then((data) => {
					title.innerHTML = data['wr_title']
					body.innerHTML = data['wr_content']
				}).catch((error) => {
					console.log(error)
					body.innerHTML = error.message
				})
		})
		content_modal.addEventListener('hide.bs.modal', (e) => {
			e.target.querySelector('.modal-title').innerHTML = ''
			e.target.querySelector('.modal-body').innerHTML = ''
		})

		const location_hash = get_cookie('location.hash', true)
		if(location_hash) location.hash = location_hash
	})
  })()