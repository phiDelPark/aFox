/*!
 * aFox Board v3.3.6 (http://afox.kr)
 * Copyright 2015-2016 afox, Inc.
 */

+function ($) {
  'use strict';

	$('#ADM_DEFAULT_MODULE')
	.on('success.exec.ajax', '[data-exec-ajax][data-modal-target]', function(e, data, xhr){
		e.preventDefault();
		var $i = $(this),
			$m = $($i.attr('data-modal-target')).clone(),
			act = $i.attr('data-exec-ajax'),
			arr = $i.attr('data-ajax-param').split(','),
			type = arr[0],
			$f = $m.find('form');

		$f.find('[id]').each(function(){$(this).attr('id',$(this).attr('id')+'_0');}).end()
			.find('label[for]').each(function(){$(this).attr('for',$(this).attr('for')+'_0');}).end()
			.find('[name=new_mb_password]').val('').removeAttr('required').end()
			.find('[name=verify_mb_password]').val('').removeAttr('required').end()
			.attr('data-exec-ajax', act.replace(/^([a-z]+).get/,'$1.update'));

		if(type == 'mb_id') {
			data['new_mb_rank'] = data['mb_rank'] == 's' ? 2 : (data['mb_rank'] == 'm' ? 1 : 0);
		} else if (type == 'wr_srl') {
			if($i.attr('data-modal-target') == '#trash_modal') {
				$f.attr('data-exec-ajax', act.replace(/^([a-z]+).get/,'$1.restore'));
			}

			var $sel = $f.find('select[name=wr_category]') || 0,
				cats = (data['md_category'] || '').split(',');

			if($sel && cats.length > 0 && cats[0]) {
				for (var i in cats) {
					$('<option value="'+cats[i]+'">'+cats[i]+'</option>').appendTo($sel);
				}
				$sel.val(data['wr_category']);
				$sel.parent().attr('required','required').show();
			}
		}

		$f[0].dataImport(data);

		$f.find('[data-act-change]').on('click', function() {
			var $i = $(this),
				$f = $i.closest('form'),
				act = $i.attr('data-act-change'),
				arr = $i.attr('data-add-param')||'',
				data = $f[0].dataExport();

			if(act == 'admin.deleteBoard'||act == 'page.deletePage'||act == 'board.deleteDocument') {
				var tmp = $_LANG[act == 'admin.deleteBoard' ? 'board' : (act == 'page.deletePage'?'page':'document')];
				if (!confirm($_LANG['confirm_select_delete'].sprintf([tmp]))) return false;
			}

			if(arr) {
				arr = arr.split(',');
				for (var i = 0, n = arr.length; i < n; i+=2) {
					data[arr[i]] = arr[i+1];
				}
			}
			exec_ajax(act, data);
			return false;
		});

		var $editor = $f.find('.af-editor-group');
		if($editor.length>0) {
			$editor.afEditor({});
			var ishtml = ((act=='page.getPage'&&data['pg_type']=='2')
						||(act=='board.getDocument'&&data['wr_type']=='2')
						||(act=='board.getComment'&&data['rp_type']=='2'));
			$m.on('shown.bs.modal', function(){$editor.data('af.editor').switch(ishtml);});
		}

		$m.find('[name=new_'+type+']').attr('name','').attr('disabled','disabled').val(data[type]).end()
			.on('hidden.bs.modal', function(){$m.remove();})
			.modal("show");
	});

	$('#ADM_DEFAULT_MODULE')
	.on('click', '[data-toggle="modal.clone"]', function(){
		var $m = $($(this).attr('data-target')).clone();
		$m.on('hidden.bs.modal', function(){$m.remove();}).modal("show");
		var $editor = $m.find('.af-editor-group');
		if($editor.length>0) $editor.afEditor({});
	})
	.on('click', '[data-empty-addon]', function(){
		if (!confirm($_LANG['confirm_select_empty'].sprintf([$_LANG['addon']]))) return false;
		var $i = $(this),
			id = $i.attr('data-empty-addon');
		exec_ajax('admin.deleteAddonConfig', {'ao_id': id}, function(status, data, xhr){
			switch(status) {
				case 'error':
				break;
				case 'success':
					$i.closest('tr').remove();
					return false;
				break;
			}
		});
	});

	$('#ADM_DEFAULT_MODULE')
	.on('show.bs.modal', '#admin_addon_modal', function(e) {
		var $i = $(this),
			$f = $i.find('form'),
			$p = $i.find('.modal-body'),
			id = $(e.relatedTarget).attr('data-addon-id');

		$f.attr('data-exec-ajax', 'admin.getAddonForm')
			.find('input[name=ao_id]').val(id).end()
			.on('error.exec.ajax', function(e, error, xhr){
				e.preventDefault();
				$p.html('<div class="alert alert-danger">' + error + '</div>');
			})
			.on('success.exec.ajax', function(e, data, xhr){
				e.preventDefault();
				if(data['act'] == 'updateAddonConfig' && data['redirect_url']) {
					parent.location.replace(data['redirect_url']);
				}
				$f.attr('data-exec-ajax', 'admin.updateAddonConfig');
				if(typeof(data['tpl']) != 'undefined') $p.html(data['tpl']);
			});

		$(document).trigger($.Event('submit', { target: $f[0] }));
	}).on('hidden.bs.modal', '#admin_addon_modal', function(){
		$(this).find('.modal-body').html('');
	});

	$(document).on('change.af.editor.toolbar', '.af-editor-toolbar', function(e, tar, old, val){
		var $e = $(this).closest('.af-editor-group');
		if((tar == 'pg_type' || tar == 'wr_type' || tar == 'rp_type') && $e.length == 1) $e.data('af.editor').switch(val==='2');
	})

}(jQuery);