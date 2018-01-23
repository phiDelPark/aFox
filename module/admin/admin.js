/*!
 * aFox (http://afox.kr)
 * Copyright 2016 afox, Inc.
 */
 var AF_EDITOR_WR_CONTENT;
 var AF_EDITOR_PG_CONTENT;
 var AF_EDITOR_RP_CONTENT;

+function ($) {
  'use strict';

	$('#ADM_DEFAULT_MODULE')
	.on('success.exec.ajax', '[data-exec-ajax][data-modal-target]', function(e, data, xhr){
		e.preventDefault();

		var $i = $(this),
			$m = $($i.attr('data-modal-target')).clone(),
			$f = $m.find('form'),
			act = $i.attr('data-exec-ajax'),
			arr = $i.attr('data-ajax-param').split(','),
			tp = arr[0];

		$f.find('[id]').each(function(){$(this).attr('id',$(this).attr('id')+'_0');}).end()
			.find('label[for]').each(function(){$(this).attr('for',$(this).attr('for')+'_0');}).end()
			.find('[name=new_mb_password]').val('').removeAttr('required').end()
			.find('[name=verify_mb_password]').val('').removeAttr('required').end()
			.find('.modal-footer > button.hide').removeClass('hide').end()
			.attr('data-exec-ajax', act.replace(/^([a-z]+).get/,'$1.update'));

		if(tp == 'mb_id') {
			data['new_mb_rank'] = data['mb_rank'] == 's' ? '2' : (data['mb_rank'] == 'm' ? '1' : '0');
		} else if (tp == 'wr_srl') {
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

		if(act=='admin.getBoard' && data['use_type'] > 6) {
			data['use_default_type'] = data['use_type'];
			data['use_type'] = '0';
		}

		var func_success = function () {
			$f[0].dataImport(data);

			if(act=='admin.getBoard' && typeof(data['md_extra']['keys']) != 'undefined') {
				var ext_arr = $.map(data['md_extra']['keys'], function(v) {
					return v;
				});
				$f.find('[name=md_extra_keys]').val(ext_arr.join());
			} else if(act=='admin.getFile') {
				var type = (data['mf_type'].split('/')[0] || 'binary');
				if(type == 'image') {
					$f.find('.imagebox > img').attr('src', request_uri + '?file=' + data['mf_srl']);
					$f.find('.imagebox').show();
				} else {
					$f.find('.imagebox').hide();
					$f.find('.imagebox > img').attr('src', '');
				}
			} else if(act=='member.getMember' && data['mb_rank'] == 's') {
				$f.find('[name="new_mb_rank"]').parent().hide();
				$f.find('[name="new_mb_rank"][value="2"]').parent().show();
			}

			$f.find('[data-act-change]').offOn('click', function() {
				var $i = $(this),
					$f = $i.closest('form'),
					act = $i.attr('data-act-change'),
					arr = $i.attr('data-add-param')||'',
					data = $f[0].dataExport();

				if(arr) {
					arr = arr.split(',');
					for (var i = 0, n = arr.length; i < n; i+=2) {
						data[arr[i]] = arr[i+1];
					}
				}

				if(act == 'admin.deleteBoard'||act == 'page.deletePage'||act == 'board.deleteDocument') {
					var tmp = act == 'admin.deleteBoard' ? 'board' : (act == 'page.deletePage'?'page':'document');
					if (!confirm($_LANG['confirm_select_'+(tmp=='document'&&(!data['is_empty']||data['is_empty']!=='1')?'move':'delete')].sprintf([$_LANG[tmp]]))) return false;
				} else if(act == 'board.deleteComment'||act == 'admin.deleteFile') {
					var tmp = act == 'board.deleteComment' ? 'comment' : 'file';
					if (!confirm($_LANG['confirm_select_delete'].sprintf([$_LANG[tmp]]))) return false;
				}

				exec_ajax(act, data);
				return false;
			});

			$f.find('button.document_goto').offOn('click', function() {
				var $i = $(this),
					$f = $i.closest('form'),
					mf = $f.find('input[name=mf_target]').val()||'',
					wr = $f.find('input[name=wr_srl]').val()||mf,
					rp = $f.find('input[name=rp_srl]').val()||'';
				if(!wr) return false;
				window.open(request_uri+'?srl='+wr+(rp?'#reply_'+rp:''), '_blank');
			});

			var $editor = $f.find('.af-editor-group');
			if ($editor.length>0) {
				var AF_EDITOR = $editor.afEditor({'name':(act=='page.getPage'?'pg':(act=='board.getComment'?'rp':'wr'))+'_content'});
				if (act=='page.getPage') {
					AF_EDITOR_PG_CONTENT = AF_EDITOR;
				} else if (act=='board.getComment') {
					AF_EDITOR_RP_CONTENT = AF_EDITOR;
				} else {
					AF_EDITOR_WR_CONTENT = AF_EDITOR;
				}
				var ishtml = ((act=='page.getPage'&&data['pg_type']=='2')
							||(act=='board.getDocument'&&data['wr_type']=='2')
							||(act=='board.getComment'&&data['rp_type']=='2'));
				$m.offOn('shown.bs.modal', function(){$editor.data('af.editor').switch(ishtml);});
			}

			$m.find('[name=new_'+tp+']').attr('name','').attr('disabled','disabled').val(data[tp]).end()
				.offOn('hidden.bs.modal', function(){$m.remove();})
				.modal("show");
		};

		if(act=='board.getDocument' || act=='page.getPage') {
			exec_ajax('admin.getFilelist', {
				'md_id': data['md_id'],
				'mf_target': act=='page.getPage' ? '1' : data['wr_srl']
			}, function(status, files, xhr){
				switch(status) {
					case 'error':
					break;
					case 'success':
						if(files && files.length > 0) {
							data['files'] = [];
							data['files'] = $.merge(data['files'], files);
						}
						func_success();
						return false;
					break;
				}
			});
		} else {
			func_success();
		}
	});

	$('#ADM_DEFAULT_MODULE,#ADM_CUSTOM_MODULE')
	.on('click', '[data-toggle="modal.clone"]', function(){
		var $m = $($(this).attr('data-target')).clone();
		$m.offOn('hidden.bs.modal', function(){$m.remove();}).modal("show");
		var $editor = $m.find('.af-editor-group');
		if($editor.length>0) $editor.afEditor({});
	});

	$('#ADM_DEFAULT_MODULE')
	.on('click', '[data-empty-addon],[data-empty-theme]', function(){
		var key = this.hasAttribute('data-empty-addon') ? 'addon': 'theme';
		if (!confirm($_LANG['confirm_select_empty'].sprintf([$_LANG[key]]))) return false;
		var $i = $(this),
			id = $i.attr('data-empty-'+key),
			data = {};
		data[(key=='addon'?'ao':'th') + '_id'] = id;
		exec_ajax('admin.delete'+key.toUcFirst()+'Config', data, function(status, data, xhr){
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
	.on('show.bs.modal', '#admin_addon_modal,#admin_theme_modal,#admin_widget_modal', function(e) {
		var $i = $(this),
			$f = $i.find('form'),
			$p = $i.find('.modal-body'),
			key = $i.attr('id') == 'admin_addon_modal' ? 'addon': ($i.attr('id') == 'admin_theme_modal' ? 'theme' : 'widget');

		var id = $(e.relatedTarget).attr('data-'+key+'-id');

		$f.attr('data-exec-ajax', 'admin.get'+key.toUcFirst()+'Form')
			.find('input[name='+(key=='addon'?'ao':(key=='theme'?'th':'wg'))+'_id]').val(id).end()
			.offOn('error.exec.ajax', function(e, error, xhr){
				e.preventDefault();
				$p.html('<div class="alert alert-danger">' + error['message'] + '</div>');
			})
			.offOn('success.exec.ajax', function(e, data, xhr){
				e.preventDefault();
				var act = $(e.target).attr('data-exec-ajax');
				if(act == 'admin.update'+key.toUcFirst()+'Config' && data['redirect_url']) {
					parent.location.replace(data['redirect_url']);
				} else {
					$(e.target).attr('data-exec-ajax', 'admin.update'+key.toUcFirst()+'Config');
					if(typeof(data['tpl']) != 'undefined') {
						$p.html(data['tpl']);
					}
				}
			});

		$(document).trigger($.Event('submit', { target: $f[0] }));
	})
	.on('hidden.bs.modal', '#admin_addon_modal,#admin_theme_modal', function(){
		$(this).find('.modal-body').html('');
	});

	$('#ADM_DEFAULT_MODULE').find('a[href="#DataManageAction"]')
	.on('click', function() {
		var $a = $(this).closest('table');
		$a.find('.th_title').hide();
		$a.find('.data_controler').show();
		$a.find('.data_selecter').show();
		$a.find('.data_all_selecter').offOn('click', function() {
			$a.find('.data_selecter').prop('checked', $(this).is(':checked')) ;
		});
		return false;
	});

	$(document)
	.on('change.af.editor.toolbar', '.af-editor-toolbar', function(e, tar, old, val){
		var $e = $(this).closest('.af-editor-group');
		if((tar == 'pg_type' || tar == 'wr_type' || tar == 'rp_type') && $e.length == 1) $e.data('af.editor').switch(val==='2');
	})

}(jQuery);
