/*!
 * aFox (https://github.com/phiDelPark/aFox)
 * Copyright 2016 afox, Inc.
 */

(function($) {
	'use strict';

	$('#ledger_write_modal')
	.on('show.bs.modal', function(e) {
		var $i = $(this),
			$f = $i.find('form'),
			$p = $i.find('.modal-body');

		var srl = $(e.relatedTarget).attr('data-ledger-srl');
		$i.find('.modal-title').html(srl?$_LANG['edit']:$_LANG['new']);

		var deleteDocumentCallfunc = function(status, data, xhr) {
			if (status == 'success') {
				location.reload();
				return false;
			} else if (status == 'error') {
				alert(data['message']);
			}
		};

		var initLedgerItemForm = function(index, value){
			var $el = $p.find('#id-ev-items'),
				$tg = $p.find('#ledger-item-panel');
			$el.attr('data-item-index', '-1');
			$el.attr('data-item-amount', '0');
			$tg.css('margin','0px');
			$tg.css('padding','15px');
			//$tg.width($el.width() - 31);
			$tg.height($el.height() - 24);
			$tg.find('[id=ev_item_caption]').val('');
			$tg.find('[id=ev_item_count]').val('1');
			$tg.find('[id=ev_item_unit]').val('');
			$tg.find('[id=ev_item_price]').val('0');
			$tg.find('[id=ev_item_tax]').val('0');
			$tg.find('[id=ev_item_info]').val('');
		};

		var LedgerItemLIdblclick = function(index, value){
			initLedgerItemForm();
			var $el = $p.find('#id-ev-items'),
				$tg = $p.find('#ledger-item-panel');
			var json = decodeURIComponent(value),
				jObj = JSON.parse(json.nl2br('\\\\n'));
			$tg.find('[id=ev_item_caption]').val(jObj.caption);
			$tg.find('[id=ev_item_count]').val(jObj.count);
			$tg.find('[id=ev_item_unit]').val(jObj.unit);
			$tg.find('[id=ev_item_price]').val(jObj.price);
			$tg.find('[id=ev_item_tax]').val(jObj.tax);
			$tg.find('[id=ev_item_info]').val(jObj.info.unescapeHtml().replace(/\\n/g, "\n"));
			$tg.find('#itemdelete').show();
			$el.attr('data-item-index', index);
			$el.attr('data-item-amount', (Number(jObj.price) + Number(jObj.tax)) * Number(jObj.count));
			$tg.show();
		};

		$f.attr('data-exec-ajax', 'ledger.getdocumentform')
			.find('input[name=ev_srl]').val(srl).end()
			.find('#delete-ledger-document')
			.click(function(){
				e.preventDefault();
				var data = {},
					$i = $(this),
					srl = $f.find('input[name=ev_srl]').val();
				data['ev_srl'] = srl;
				exec_ajax('ledger.deleteDocument', data, deleteDocumentCallfunc);
			}).end()
			.offOn('error.exec.ajax', function(e, error, xhr){
				e.preventDefault();
				$p.html('<div class="alert alert-danger">' + error['message'] + '</div>');
			})
			.offOn('success.exec.ajax', function(e, data, xhr){
				e.preventDefault();
				var act = $(e.target).attr('data-exec-ajax');
				if(act == 'ledger.updatedocument' && data['redirect_url']) {
					parent.location.replace(data['redirect_url']);
				} else {
					$(e.target).attr('data-exec-ajax', 'ledger.updatedocument');
					if(typeof(data['tpl']) != 'undefined') {
						$p.html(data['tpl']);
						var $el = $p.find('#id-ev-items'),
							$tg = $p.find('#ledger-item-panel');
						$p.find('[data-toggle=ledger-item-form]')
						.click(function(e){
							e.preventDefault();
							initLedgerItemForm();
							$p.find('#itemdelete').hide();
							$tg.show();
						});
						$tg.find('#itemclose')
						.click(function(e){
							e.preventDefault();
							$tg.hide();
						});
						$p.find('#iteminsert')
						.click(function(e){
							e.preventDefault();
							var caption = $p.find('[id=ev_item_caption]').val().trim(),
								amount = Number($el.attr('data-item-amount')),
								price = Number($p.find('[id=ev_item_price]').val()),
								tax = Number($p.find('[id=ev_item_tax]').val()),
								count = Number($p.find('[id=ev_item_count]').val()),
								unit = $p.find('[id=ev_item_unit]').val(),
								info = $p.find('[id=ev_item_info]').val().trim().replace(/\\/g, '/').escapeHtml();

							var value = '{"caption":"' + caption + '",';
							value = value + '"count":"' + count + '",';
							value = value + '"unit":"' + unit + '",';
							value = value + '"price":"' + price + '",';
							value = value + '"tax":"' + tax + '",';
							value = encodeURIComponent((value + '"info":"' + info + '"}').nl2br('\\\\n'));

							if(caption){
								var idx = $el.attr('data-item-index'),
									$am = $p.find('[name=ev_amount]');
								caption = '<div>' + caption + '</div><div class="right">' + ((price + tax) * count) + '</div>'
								if(idx > -1) {
									$el.find('li').eq(idx).html(caption);
									$el.find('li').eq(idx).attr('data-value', value);
								} else{
									var $tmp = $('<li data-value="'+ value +'">'+ caption +'</li>');
									$tmp.dblclick(function(e){
										e.preventDefault();
										LedgerItemLIdblclick($(this).index(), $(this).attr('data-value'));
										return false;
									});
									$el.append($tmp);
								}
								$am.val((Number($am.val()) - amount) + ((price + tax) * count));
								$tg.hide();
							}else{
								alert($_LANG['request_item_name']);
								$p.find('[id=ev_item_caption]').focus();
							}
						});
						$p.find('#itemdelete')
						.click(function(e){
							e.preventDefault();
							var amount = Number($el.attr('data-item-amount')),
								idx = $el.attr('data-item-index'),
								$am = $p.find('[name=ev_amount]');
							if(idx > -1){
								$el.find('li').eq(idx).remove();
								$am.val(Number($am.val()) - amount);
							}
							$tg.hide();
						});
						$el.find('li').dblclick(function(e){
							e.preventDefault();
							LedgerItemLIdblclick($(this).index(), $(this).attr('data-value'));
							return false;
						});
					}
				}
			});

		$f.submit(function(){
			var items = '';
			$(this).find('#id-ev-items li[data-value]').each(function(){
				items = items + ',' + $(this).attr('data-value');
			});
			$(this).find('[name=ev_items]').val(items.substring(1));
		});

		$(document).trigger($.Event('submit', { target: $f[0] }));
	})
	.on('shown.bs.modal', function(){
		var $p = $(this);
		$p.find('#id-ev-items').height(
			$p.find('#ledger-form').height() - $p.find('#ledger-item-form .form-group').eq(0).height() - 42
		);
	})
	.on('hidden.bs.modal', function(){
		$(this).find('.modal-body').html('');
	});

	$('.breadcrumb select').change(function(){
		window.location = $(this).val();
	});

    $.datepicker.setDefaults({
        dateFormat: 'yy년 mm월 dd일',
        prevText: '이전 달',
        nextText: '다음 달',
        monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
        monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
        dayNames: ['일', '월', '화', '수', '목', '금', '토'],
        dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
        dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
        showMonthAfterYear: true,
        yearSuffix: '년'
    });

	$(window)
		.on('load', function() {
		})

})(jQuery);
