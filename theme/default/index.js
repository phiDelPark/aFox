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
		const themeSwitcher = document.querySelector('#bd-theme')

		if (!themeSwitcher) return

		const themeSwitcherText = document.querySelector('#bd-theme-text')
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

		window.current_content = document.querySelector('.current_content');
		window.current_content?.querySelectorAll('img[src]')
			?.forEach(el => {
				const src = el.getAttribute('src')
				el.setAttribute('src', './common/img/blank.png');
				el.setAttribute('scroll-src', src)
			});
	})

	window.addEventListener('scroll', () => {
		window.current_content?.querySelectorAll('img[scroll-src]')
			?.forEach(el => {
				if(el.offsetTop < (window.scrollY + window.innerHeight + 50)){
					const src = el.getAttribute('scroll-src')
					el.setAttribute('src', src)
					el.removeAttribute('scroll-src')
				}
			});
	})

	window.addEventListener('load', () => {
		window.dispatchEvent(new Event('scroll', {bubbles: true, cancelable: false}))
		document.querySelector('#afoxPageLoading')?.fadeOut(el => el.remove())

		document.querySelector('main.container')
			?.querySelectorAll('[exec-ajax]')
				?.forEach(el => {
					el.addEventListener('click', e => {
						e.preventDefault()
						e.stopPropagation()
						const
							ajax = el.getAttribute('exec-ajax'),
							action = ajax.substring(0, ajax.indexOf('&')).split('.')
						let
							usuccess = el.getAttribute('success-url'),
							uerror = el.getAttribute('error-url')

						const data = {
							module: action[0],
							act: action[1],
							...ajax.getQuery(),
						}
						exec_ajax(data)
							.then((data) => {
								if(usuccess) {
									usuccess = usuccess.split('#')
									location.href = usuccess[0]
									if(usuccess[1]) set_cookie('location.hash', usuccess[1], 1)
								}
							})
							.catch((error) => {
								console.log(error)
								alert(error.message)
								if(uerror) location.href = uerror
							})
					})
				});
		const location_hash = get_cookie('location.hash', true)
		if(location_hash) location.hash = location_hash
	})
  })()