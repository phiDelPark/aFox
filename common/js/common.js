/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 */
if (typeof bootstrap === 'undefined') {
	if (!document.getElementById('defBootstrapJS'))
		parent.location.replace(request_uri + '?cdnerr=bootstrap')
	throw new Error('aFox\'s JavaScript requires Bootstrap 5')
}

const $_LANG = {};

(() => {
'use strict'

	Number.prototype.shortSize = function() {
		let	s = this, i = 0; while(s > 1024){s = s / 1024; i++}
		return s.toFixed(1) + (['B','K','M','G','T'].at(i) || '?')
	}

	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, "")
	}

	String.prototype.toUcFirst = function() {
		return this.charAt(0).toUpperCase()+this.slice(1).toLowerCase()
	}

	String.prototype.nl2br = function(brTag) {
		return this.replace(/\r\n|\n\r|\r|\n/g, (brTag || "<br>"))
	}

	String.prototype.sprintf = function() {
		let	s = this, a = arguments; a = (typeof a[0]=='object' ? a[0] : a)
		for (let i = 0; i < a.length; i++) {
			s = s.replace(/%([0-9]?)(s|d)/, ($0, $1, $2) => {
				return a[i].padStart(Number($1||0), $2=='d'?'0':'_')
			})
		} return s
	}

	String.prototype.stripTags = function(a) { // php.js
		// making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
		a=(((a||'')+'').toLowerCase().match(/<[a-z][a-z0-9]*>/g)||[]).join('')
		const tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
			pags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi
		return this.replace(pags, '').replace(tags, ($0, $1) => {
			return a.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
		})
	}

	String.prototype.escapeMKDW = function(f) {
		const rex = f === false ? /[\`\[\]]/g : /[\\\`\*\_\{\}\[\]\(\)\>\#\+\-\.\!]/g
		return this.replace(rex, (s) => { return '\\' + s })
	}

	String.prototype.escapeHtml = function() {
		const a = {"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#x2F;"}
		return this.replace(/[&<>"'\/]/g, (s) => { return a[s] })
	}

	String.prototype.unescapeHtml = function() {
		const a = {"&amp;":"&","&lt;":"<","&gt;":">","&quot;":'"',"&#39;":"'","&#x2F;":"/"}
		return this.replace(/&[^;]+;/g, (s) => { return a[s] })
	}

	String.prototype.rawurlencode = function() {
		return encodeURIComponent(this).replace(/[!\"'()*~]/g, c => {
			return '%' + c.charCodeAt(0).toString(16).toUpperCase();
		})
	}

	String.prototype.rawurldecode = function() {
		return decodeURIComponent(this.replace(/%(21|22|27|28|29|2A|7E)/g, c => {
			return String.fromCharCode(parseInt(c.slice(1), 16))
		}))
	}

	String.prototype.encode64 = function() {//base64+rawurlencode
		return btoa(this.rawurlencode()).replace(/[=\/]/g, c => {
			return '%' + c.charCodeAt(0).toString(16);
		})
	}

	String.prototype.decode64 = function() {//js doesn't have md5
		return atob(this.replace(/%(3d|2f)/g, c => {
			return String.fromCharCode(parseInt(c.slice(1), 16))
		})).rawurldecode()
	}

	String.prototype.getQuery = function(g) {
		let	p={}; const u = decodeURIComponent(this).replace(/&amp;/g, '&')
		u.replace(/[?&]+([^=&]+)=([^&]*)/g, (a, k, v) => {p[k] = v})
		return g ? (p[g] || '') : p
	}

	String.prototype.setQuery = function() {
		let	a = arguments; a = (typeof a[0] == 'object')?a[0]:a
		const u = decodeURIComponent(this).replace(/&amp;/g, '&')
		if (a.length === 0) return u
		let	p = (a[0] != '') ? u.getQuery() : {}, r = [], v
		for (var i = (a[0]==''?1:0); i < a.length; i += 2) p[a[i]]=a[i+1]
		for (var k in p) {
			if (!p.hasOwnProperty(k)) continue
			if (!(v = String(p[k]).trim())) continue
			r.push(k + '=' + v)
		}
		let x = u.indexOf('?'), z = (x == -1 ? u : u.slice(0, x))
		return z+((z.slice(-1)=='/')?'':'/')+(r.length>0?'?'+r.join('&'):'')
	}

	HTMLElement.prototype.fadeOut = function(callback, speed = 50) {
		let $e = this, op = 1, increment = 0.1
		$e.timer = setInterval(()=> {
			op -= increment; $e.style.opacity = op
			if (op <= 0) { clearInterval($e.timer); callback($e) }
		}, speed)
	}

	HTMLElement.prototype.fadeIn = function(callback, speed = 50) {
		let $e = this, op = 0, increment = 0.1
		$e.timer = setInterval(()=> {
			op += increment; $e.style.opacity = op
			if (op >= 1) { clearInterval($e.timer); callback($e) }
		}, speed)
	}

	HTMLFormElement.prototype.serializeArray = function(arr = []) {
		Array.prototype.slice.call(this.elements).forEach(($e) => {
			if (!$e.name || $e.disabled || ['file'].indexOf($e.type) > -1) return
			if ($e.type === 'select-multiple') {
				Array.prototype.slice.call($e.options).forEach((opt) => {
					if (!opt.selected) return
					arr.push({name: $e.name, value: opt.value})
				}); return
			}
			if (['checkbox', 'radio'].indexOf($e.type) >-1 && !$e.checked) return
			arr.push({name: $e.name, value: $e.value})
		}); return arr
	}

	window.set_cookie = function(name, value, exp = 0) { //if 0, 1 year //if -, remove
		document.cookie = name.encode64() + '=' + value.encode64() + '; path=/; '
		+ 'Domain=' + (_AF_COOKIE_DOMAIN_ || '') + '; max-age=' + (exp ? exp : 31536000)
	}

	window.get_cookie = function(name, remove = false) {
		let x, y, enname = name.encode64()
		const cookies = document.cookie.split(';')
		for(let i = 0; i < cookies.length; i++) {
			x = cookies[i].slice(0, cookies[i].indexOf('='))
			y = cookies[i].slice(cookies[i].indexOf('=') + 1)
			x = x.replace(/^\s+|\s+$/g, '')
			if(x == enname) {
				if(remove) set_cookie(name, '', -1)
				return y.rawurldecode().decode64()
			}
		}
	}

	window._AF_COOKIE_DOMAIN_ = get_cookie('_AF_COOKIE_DOMAIN_');

	window.exec_ajax = async function(body, headers = {}) {
		const options = {
			method: "POST",
			headers: {
				"X-Requested-With":"XMLHttpRequest",
				"Content-Type":"application/json",
				"Accept":"application/json, text/javascript",
				...headers,
			},
			body: JSON.stringify(body),
		}
		const res = await fetch(request_uri, options)
		const data = await res.json()
		if(!res.ok || (data?.error || 0) !== 0) throw Error(data?.message || data)
		return data
	}

	window.load_script = function(source, after, async, defer) {
		return new Promise((resolve, reject) => {
			let sc = document.createElement('script')
			const prior = after || document.getElementsByTagName('script')[0]
			sc.async = async || true; sc.defer = defer || true
			const onloadHander = (_, isAbort) => {
			if (isAbort || !sc.readyState || /loaded|complete/.test(sc.readyState)){
				sc.onload = null; sc.onreadystatechange = null; sc = undefined
				if (isAbort) { reject() } else { resolve() }
			}}
			sc.onload = onloadHander
			sc.onreadystatechange = onloadHander
			sc.src = source
			prior.parentNode.insertBefore(sc, prior.nextSibling)
		})
	}

	window.pop_win = function(url, width, height, id) {
		const popwin = window.open( url, (id || 'afox_popup'),
			'width=' + (width || '700') + ',height=' + (height || '500') +
			',top=50,left=50,scrollbars=yes,toolbar=no,menubar=no,location=no'
		); popwin.focus()
		return popwin
	}
})()