/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 */
if (typeof bootstrap === 'undefined') {
	if (!document.getElementById('defBootstrapJS'))
		parent.location.replace(request_uri + '?cdnerr=bootstrap')
	throw new Error('aFox\'s JavaScript requires Bootstrap 5')
}

const $_LANG = {}

Number.prototype.shortSize = function() {
	const t = ['B', 'K', 'M', 'G', 'T']
	let	s = this, i = 0
	for (let n = 4; i < n; i++) {
		if (s <= 1024) break
		s = s / 1024
	}
	return s.toFixed(1) + t[i]
}

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g, "")
}

String.prototype.toUcFirst = function() {
	let s = this.toLowerCase()
	return s.charAt(0).toUpperCase() + s.slice(1)
}

String.prototype.sprintf = function() {
	let s = this, a = arguments
	a = (a.length === 1 && typeof a[0] === 'object') ? a[0] : a
	for (let i = 0, n = a.length; i < n; i++) {
		s = s.replace(/%([0-9]?)(s|d)/, function(x, y, z) {
			let out = a[i] + ''
			if (Number(y || 0) > 0) {
				let t1 = z == 'd' ? '0' : '_'
				while (out.length < y) out = t1 + out
			}
			return out
		})
	}
	return s
}

String.prototype.nl2br = function(breakTag) {
	return this.replace(/\r\n|\n\r|\r|\n/g, (breakTag || "<br>"))
}

String.prototype.stripTags = function(allowed) { // php.js
	// making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
	allowed = (((allowed || "") + "")
		.toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('')
	const tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
		cmtPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi
	return this.replace(cmtPhpTags, '').replace(tags, function($0, $1) {
		return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
	})
}

String.prototype.escapeMKDW = function(full) {
	const rex = full === false ? /[\`\[\]]/g : /[\\\`\*\_\{\}\[\]\(\)\>\#\+\-\.\!]/g
	return this.replace(rex, function(s) { return '\\' + s })
}

String.prototype.escapeHtml = function() {
	const es = {"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#x2F;"}
	return this.replace(/[&<>"'\/]/g, function(s) { return es[s] })
}

String.prototype.unescapeHtml = function() {
	const es = {"&amp;":"&","&lt;":"<","&gt;":">","&quot;":'"',"&#39;":"'","&#x2F;":"/"}
	return this.replace(/&[^;]+;/g, function(s) { return es[s] })
}

String.prototype.getQuery = function(s) {
	let	p = {}
	const u = decodeURIComponent(this).replace(/&amp;/g, '&')
	u.replace(/[?&]+([^=&]+)=([^&]*)/g, function(a, k, v){p[k] = v})
	return s ? (p[s] || '') : p
}

String.prototype.setQuery = function() {
	let	a = arguments, n = a.length
	const u = decodeURIComponent(this).replace(/&amp;/g, '&')
	if (n === 0) return u
	if (typeof a[0] === 'object') {
		a = a[0]
		n = a.length
	}
	let p = (a[0] !== '') ? u.getQuery() : {}, r = [], v
	for (var i = (a[0] === '' ? 1 : 0); i < n; i += 2) {
		p[a[i]] = a[i + 1]
	}
	for (var k in p) {
		if (!p.hasOwnProperty(k)) continue
		if (!(v = String(p[k]).trim())) continue
		r.push(k + '=' + v)
	}
	let x = u.indexOf('?'), z = (x == -1 ? u : u.substr(0, x))
	if (z.substr(-1, 1) != '/') z = z + '/'
	return z + (r.length > 0 ? '?' + r.join('&') : '')
}

HTMLFormElement.prototype.serializeArray = function() {
	let arr = []
	Array.prototype.slice.call(this.elements).forEach(function($e) {
		if (!$e.name || $e.disabled || ['file', 'reset', 'submit', 'button'].indexOf($e.type) > -1)
			return
		if ($e.type === 'select-multiple') {
			Array.prototype.slice.call($e.options).forEach(function(option) {
				if (!option.selected) return
				arr.push({name: $e.name, value: option.value})
			})
			return
		}
		if (['checkbox', 'radio'].indexOf($e.type) >-1 && !$e.checked) return
		arr.push({name: $e.name, value: $e.value})
	})
	return arr
}

HTMLFormElement.prototype.dataExport = function() {
	let name, value, data = {}, arrs = this.serializeArray()
	for (let v in arrs) {
		name = arrs[v]['name']
		value = arrs[v]['value']
		if (name.substring(name.length - 2) === '[]') {
			name = name.slice(0, -2)
			if(!Array.isArray(data[name])) data[name] = []
			data[name][data[name].length] = value
		} else {
			data[name] = value
		}
	}
	return data
}

HTMLFormElement.prototype.dataImport = function(data) {
	let $e, $target, html
	for (let name in data) {
		$e = this.elements[name]
		if (!$e || !$e.name) continue
		if ($e.tagName == 'INPUT') {
			if ($e.type == 'file') {



// todo file 만나면 처리




			} else if ($e.type == 'checkbox') $e.checked = data[name] == 1
			else if ($e.type == 'radio') {
				if($e = $e.find(el => el.value === data[name])) $e.checked = true
			} else $e.value = data[name] == '0' ? '' : data[name]
		} else if (['select', 'textarea'].indexOf($e.type) >-1) $e.value = data[name]
	}
}

HTMLElement.prototype.fadeOut = function(callback, speed = 50) {
	let el = this, op = 1, increment = 0.1
	el.timer = setInterval(()=> {
		op -= increment
		el.style.opacity = op
		if (op <= 0) {
			clearInterval(el.timer)
			callback(el)
		}
	}, speed)
}

HTMLElement.prototype.fadeIn = function(callback, speed = 50) {
	let el = this, op = 0, increment = 0.1
	el.timer = setInterval(()=> {
		op += increment
		el.style.opacity = op
		if (op >= 1) {
			clearInterval(el.timer)
			callback(el)
		}
	}, speed)
}

window.setCookie = function(name, value, days) {
	const exdate = new Date();
	exdate.setDate(exdate.getDate() + days)
	document.cookie = name + '=' + encodeURIComponent(value)
		+ ((days == null) ? '' : '; expires=' + exdate.toUTCString())
}

window.getCookie = function(name, remove = false) {
  let x, y, val = document.cookie.split(';')
  for (let i = 0; i < val.length; i++) {
    x = val[i].slice(0, val[i].indexOf('='))
    y = val[i].slice(val[i].indexOf('=') + 1)
    x = x.replace(/^\s+|\s+$/g, '')
    if (x == name){
			if(remove) setCookie(name, '', -1)
			return decodeURIComponent(y)
		}
  }
}

window.exec_ajax = async function(body, headers = {}) {
	const options = {
		method: "POST",
		headers: {"Content-Type":"application/json",...headers,},
		body: JSON.stringify(body),
	}
	const res = await fetch(request_uri, options)
	const data = await res.json()
	if(!res.ok || (data?.error || 0) !== 0) throw Error(data?.message || data)
	return data
}

window.load_script = function(source, after, async, defer) {
	return new Promise((resolve, reject) => {
		let script = document.createElement('script')
		const prior = after || document.getElementsByTagName('script')[0]

		script.async = async || true
		script.defer = defer || true

		function onloadHander(_, isAbort) {
		if (isAbort || !script.readyState || /loaded|complete/.test(script.readyState)) {
			script.onload = null
			script.onreadystatechange = null
			script = undefined
			if (isAbort) { reject() } else { resolve() }
		}
		}

		script.onload = onloadHander
		script.onreadystatechange = onloadHander
		script.src = source
		prior.parentNode.insertBefore(script, prior.nextSibling)
	})
}

window.pop_win = function(url, width, height, id) {
	const popwin = window.open(
		url, (id || 'afox_popup'),
		'width=' + (width || '700') + ',height=' + (height || '500') +
		',top=50,left=50,scrollbars=yes,toolbar=no,menubar=no,location=no'
	)
	popwin.focus()
	return popwin
}