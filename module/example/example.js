+function ($) {
  'use strict';

	$('#ADM_CUSTOM_MODULE')
		.on('success.exec.ajax', '[data-exec-ajax][data-modal-target]', function(e, data, xhr){
			e.preventDefault();

			var $i = $(this),
				$m = $($i.attr('data-modal-target')).clone(),
				$f = $m.find('form'),
				act = $i.attr('data-exec-ajax'),
				arr = $i.attr('data-ajax-param').split(',');

			$f.find('[id]').each(function(){$(this).attr('id',$(this).attr('id')+'_0');}).end()
				.find('label[for]').each(function(){$(this).attr('for',$(this).attr('for')+'_0');}).end()
				.attr('data-exec-ajax', act.replace(/^([a-z]+).get/,'$1.update'));

			$f[0].dataImport(data);

			$m.find('[name=new_md_id]').attr('name','').attr('disabled','disabled').val(data[type]).end()
				.on('hidden.bs.modal', function(){$m.remove();})
				.modal("show");
		});

}(jQuery);