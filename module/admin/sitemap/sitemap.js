/*
 * sitemap.js
 * @author phiDel (is4975@gmail.com)
 */

let siteMap_tempKey = -1;

function SiteMap(id) {

	const container = document.querySelector(id);

	let dragging = false;
	let position = {x: 0, y: 0};
	let offset = {top: 0, left: 0, right: 0, bottom: 0};
	let offsets = [];
	let dropzone = null;
	let target = null;
	let clone = null;
	let pholder = null;

	const getPidOffset = (el, offsetParent) => {

		let top = 0,
			left = 0;

		while (el && el != offsetParent) {
			top += el.offsetTop;
			left += el.offsetLeft;
			el = el.offsetParent;
		}

		return {
			top: top,
			left: left
		};
	}

	const setPidHolder = (info, yPos) => {

		if (Math.abs(info.top - yPos) <= 3) {
			pholder.style.top = (info.top - 3) + 'px';
			pholder.style.height = '2px';
			return 'before';
		} else if (Math.abs(info.bottom - yPos) <= 3) {
			pholder.style.top = (info.bottom - 3) + 'px';
			pholder.style.height = '2px';
			return 'after';
		} else {
			pholder.style.top = (info.top - 3) + 'px';
			pholder.style.height = (info.bottom - info.top) + 'px';
			return 'prepend';
		}
	}

	const documentMouseMove = e => {

		let el = e.target.tagName != 'LI' ? e.target.closest('LI') : e.target;
		if(!el || el.tagName != 'LI') return false;

		let diff, nTop, i, c, o, t;
		dropzone = null;

		diff = {
			x: position.x - e.pageX,
			y: position.y - e.pageY
		};
		nTop = offset.top - diff.y - 10;

		let h = el.querySelector('[type=text]')?.offsetHeight || 38;

		for (i = 0, c = offsets.length; i < c; i++) {
			t = nTop + h;
			o = offsets[i];

			if (i === 0 && t < o.top) t = o.top;
			if (i == c - 1 && t > o.bottom) t = o.bottom;

			if (o.top <= t && o.bottom >= t) {
				dropzone = {
					element: o.item,
					state: setPidHolder(o, t)
				};
				break;
			}
		}

		clone.style.display = 'block';
		clone.style.top = nTop + 'px';

		return false;
	};

	const documentMouseUp = e => {

		dragging = false;

		document.removeEventListener('mousemove', documentMouseMove);
		document.removeEventListener('mouseup', documentMouseUp);

		target.style.opacity = '1';
		clone.remove();
		pholder.remove();

		if (!dropzone) return false;

		let el = dropzone.element.tagName != 'LI'
			? dropzone.element.closest('LI') : dropzone.element;

		switch (dropzone.state) {
			case 'after':
				el.after(target);
				break;
			case 'before':
				el.before(target);
				break;
			case 'prepend':
				let ul_child = dropzone.element.querySelector('UL');
				if (!ul_child) {
					ul_child = document.createElement("ul");
					dropzone.element.append(ul_child);
				}
				ul_child.append(target);
				break;
		}

		let el_pkey = target.querySelector('input[name="parent_key[]"]'),
			el_pli = target.parentNode.closest('LI');
		el_pkey.value = el_pli?.querySelector('input[name="item_key[]"]').value || '0';
		return false;
	};

	container.addEventListener('mousedown', e => {

		if (['UL','INPUT','TEXTAREA'].indexOf(e.target.tagName) > -1 || e.which != 1)
			return;

		dragging = true;

		target = e.target.tagName != 'LI' ? e.target.closest('LI') : e.target;

		target.style.opacity = '.5';

		clone = target.cloneNode(true);
		clone.classList.add('draggable');
		pholder = document.createElement("li");
		pholder.classList.add('placeholder');

		const li_children = container.querySelectorAll('LI');
		const rect =  target.getBoundingClientRect();

		container.append(clone);
		container.append(pholder);

		let h = target.querySelector('[type=text]')?.offsetHeight || 38;

		offsets = [];
		li_children.forEach(el => {
			let o = getPidOffset(el, container);
			offsets.push({
				top: o.top,
				bottom: o.top + h + 8,
				item: el
			});
		});

		position = {x: e.pageX, y: e.pageY};
		offset = getPidOffset(target, container);

		clone.style.cssText = `
			display: none;
			position: absolute;
			opacity: .5;
			width: ${rect.width}px;
			height: ${rect.height}px;
			left: ${offset.left}px;
			top: ${offset.top}px;
			z-index: 100;
		`;

		pholder.style.cssText = `
			position: absolute;
			opacity: .6;
			width: ${rect.width}px;
			height: 2px;
			left: ${offset.left}px;
			top: ${offset.top}px;
			z-index: 99;
		`;

		document.addEventListener('mousemove', documentMouseMove);
		document.addEventListener('mouseup', documentMouseUp);

		return false;
	});

	container.addEventListener('mouseover', e => {
		//if (!dragging) e.target.classList.add('active');
		return false;
	});

	container.addEventListener('mouseout', e => {
		//if (!dragging) e.target.classList.remove('active');
		return false;
	});

	const els_delete = container.querySelectorAll('li input[value=delete]');
	els_delete.forEach(el => {
		el.addEventListener('click', e => {

			e.preventDefault();
			if(!confirm($_LANG['confirm_delete'].sprintf([$_LANG['menu']]))) return;
			const el2 = e.target.tagName != 'LI' ? e.target.closest('LI') : e.target;
			el2.remove();
		});
	});

	const els_setup = container.querySelectorAll('li input[value=setup]');
	els_setup.forEach(el => {
		el.addEventListener('click', e => {

			e.preventDefault();
			const el_pli = el.closest('LI'),
				el_input = el_pli.querySelector('.input'),
				el_setup = el_pli.querySelector('.setup');
			el_input.classList.toggle('d-none');
			el_setup.classList.toggle('d-none');
		});
	});

	const el_form = container.closest('FORM');
	el_form.addEventListener('submit', e => {
		try {
			const els_ck = e.target.querySelectorAll('input[type=checkbox]');
			els_ck.forEach(el => {
				if(el.checked) el.value = 1;
				el.setAttribute('type', 'hidden');
			});
			return true;
		} catch (error) {
			e.preventDefault();
			return false;
		}
	});
}

window.onload = function() {
	window.siteMapItemAdd = (th, idx) => {
		try {
			const container = document.querySelector('#siteMapRoot' + idx),
				el = document.querySelector('#siteMap_item_template li').cloneNode(true),
				el_del = el.querySelector('input[value=delete]'),
				el_set = el.querySelector('input[value=setup]'),
				el_key = el.querySelector('input[name="item_key[]"]');
			el_key.value = siteMap_tempKey--;
			el_del.addEventListener('click', e =>
			{
				e.preventDefault();
				if(!confirm($_LANG['confirm_delete'].sprintf([$_LANG['menu']]))) return;
				const el2 = e.target.tagName != 'LI' ? e.target.closest('LI') : e.target;
				el2.remove();
			});
			el_set.addEventListener('click', e =>
			{
				e.preventDefault();
				const el_pli = e.target.closest('LI'),
					el_input = el_pli.querySelector('.input'),
					el_setup = el_pli.querySelector('.setup');
				el_input.classList.toggle('d-none');
				el_setup.classList.toggle('d-none');
			});
			container.prepend(el);
		} catch (error) {

		}
		return false;
	};

	const SiteMap1 = new SiteMap('#siteMapRoot1');
	const SiteMap2 = new SiteMap('#siteMapRoot2');
}