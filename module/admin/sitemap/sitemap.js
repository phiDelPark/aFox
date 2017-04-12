/*
 * sitemap.js for BoardDX
 * @author NHN (developers@xpressengine.com) site map
 * @optimizer phiDel (xe.phidel@gmail.com)
 */

jQuery(function($) {
	var siteMap_tempKey = -1;

	var
		pid_dragging = false,
		$pidPholder = $('<li class="placeholder">');

	$('#siteMap form')
		.on({
			'mousedown.st': function(event) {
				var $this, $uls, $ul, width, height, offset, position, offsets, i, dropzone, wrapper = '';

				if ($(event.target).is('input,textarea') || event.which != 1) return;

				pid_dragging = true;

				$this = $(this);
				height = $this.height();
				width = $this.width();
				$uls = $this.parentsUntil(this).filter('ul');
				$ul = $uls.eq(-1);

				$ul.css('position', 'relative');

				position = {
					x: event.pageX,
					y: event.pageY
				};
				offset = getPidOffset(this, $ul.get(0));

				$clone = $this.clone(true).attr('target', true);

				for (i = $uls.length - 1; i; i--) {
					$clone = $clone.wrap('<li><ul /></li>').parent().parent();
				}

				// get offsets of all list-item elements
				offsets = [];
				$ul.find('li').each(function(idx) {
					if ($this[0] === this || $this.has(this).length) return true;

					var o = getPidOffset(this, $ul.get(0));
					offsets.push({
						top: o.top,
						bottom: o.top + 32,
						item: this
					});
				});

				// Remove unnecessary elements from the clone, set class name and styles.
				// Append it to the list
				$clone
					.find('.side,input').remove().end()
					.addClass('draggable')
					.css({
						position: 'absolute',
						opacity: '.6',
						width: width,
						height: height,
						left: offset.left,
						top: offset.top,
						zIndex: 100
					})
					.appendTo($ul.eq(0));

				// Set a place holder
				$pidPholder
					.css({
						position: 'absolute',
						opacity: '.6',
						width: width,
						height: '5px',
						left: offset.left,
						top: offset.top,
						zIndex: 99
					})
					.appendTo($ul.eq(0));

				$this.css('opacity', '.6');

				$(document)
					.off('mousemove.st mouseup.st')
					.on('mousemove.st', function(event) {
						var diff, nTop, item, i, c, o, t;

						dropzone = null;

						diff = {
							x: position.x - event.pageX,
							y: position.y - event.pageY
						};
						nTop = offset.top - diff.y;

						for (i = 0, c = offsets.length; i < c; i++) {
							t = nTop;
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

						$clone.css({
							top: nTop
						});
					})
					.on('mouseup.st', function(event) {
						var $dropzone, $li;

						pid_dragging = false;

						$(document).off('mousemove.st mouseup.st');
						$this.css('opacity', '');
						$clone.remove();
						$pidPholder.remove();

						// dummy list item for animation
						$li = $('<li />').height($this.height());

						if (!dropzone) return;
						$dropzone = $(dropzone.element);

						$this.before($li);

						if (dropzone.state == 'prepend') {
							if (!$dropzone.find('>ul').length) $dropzone.find('>.side').after('<ul>');
							$dropzone.find('>ul').prepend($this.hide());
						} else {
							$dropzone[dropzone.state]($this.hide());
						}

						$this.slideDown(100, function() {
							$this.removeClass('active');
						});
						$li.slideUp(100, function() {
							var $par = $li.parent();
							$li.remove();
							if (!$par.children('li').length) $par.remove();
						});

						// trigger 'dropped.st' event
						$this.trigger('dropped.st');
					});

				return false;
			},
			'mouseover.st': function() {
				if (!pid_dragging) $(this).addClass('active');
				return false;
			},
			'mouseout.st': function() {
				if (!pid_dragging) $(this).removeClass('active');
				return false;
			}
		},'li:not(.placeholder)')
		.find('li')
		.prepend('<button type="button" class="moveTo">Move to</button>')
		//.append('<span class="vr"></span><span class="hr"></span>')
		.end();

	function getPidOffset(elem, offsetParent) {
		var top = 0,
			left = 0;

		while (elem && elem != offsetParent) {
			top += elem.offsetTop;
			left += elem.offsetLeft;

			elem = elem.offsetParent;
		}

		return {
			top: top,
			left: left
		};
	}

	function setPidHolder(info, yPos) {
		if (Math.abs(info.top - yPos) <= 3) {
			$pidPholder.css({
				top: info.top - 3,
				height: '5px'
			});
			return 'before';
		} else if (Math.abs(info.bottom - yPos) <= 3) {
			$pidPholder.css({
				top: info.bottom - 3,
				height: '5px'
			});
			return 'after';
		} else {
			$pidPholder.css({
				top: info.top + 3,
				height: '27px'
			});
			return 'prepend';
		}
	}

	$.siteMapItemSave = window.siteMapItemSave = function(i){
		$('#siteMap form[data-type=' + i + ']').submit();
		return false;
	};

	$.siteMapItemAdd = window.siteMapItemAdd = function(i){
		$($('#siteMap_item_template').html())
			.find('input._item_key').val(siteMap_tempKey--).end()
			.prependTo('#siteMapRoot' + i);
		return false;
	};

	$.siteMapItemDelete = window.siteMapItemDelete = function(th){
		if(!confirm($_LANG['confirm_select_delete'].sprintf([$_LANG['menu']]))) return false;
		$(th).closest('li').remove();
		return false;
	};

	$('#siteMap form').on('dropped.st', 'li:not(.placeholder)', function() {
		var $this = $(this), $pkey, is_child;
		$pkey = $this.find('>input._parent_key');
		is_child = $this.parent('ul').parent('li').length;
		if (is_child) {
			$pkey.val($this.parent('ul').parent('li').find('>input._item_key').val());
		} else {
			$pkey.val('0');
		}
	});

	$('#ADM_DEFAULT_MODULE #admin_menu_modal').on('show.bs.modal', function(e) {
		var $this = $(this),
			$target = $(e.relatedTarget),
			$p = $target.closest('.sitemap-item');

		var kd = $p.find('._desc_key').val(),
			kc = $p.find('._collapse_key').val(),
			kw = $p.find('._new_win_key').val();

		$this.find('#sitemap_mu_description').val(kd);
		var smc = $this.find('#sitemap_mu_collapse').val(kc).parent(),
			smw = $this.find('#sitemap_mu_new_window').val(kw).parent();

		if(kc==1) {smc.addClass('on');} else {smc.removeClass('on');}
		if(kw==1) {smw.addClass('on');} else {smw.removeClass('on');}

		$this.find('button.btn-success').off('click').one('click', function(e){
			$p.find('._desc_key').val($this.find('#sitemap_mu_description').val());
			$p.find('._collapse_key').val($this.find('#sitemap_mu_collapse').val());
			$p.find('._new_win_key').val($this.find('#sitemap_mu_new_window').val());
			$this.modal('hide');
			$target.attr('src', request_uri + 'module/admin/sitemap/icon_refresh.png').addClass('gly-normal-right-spin');
		});
	});
});
