<?php
if(!defined('__AFOX__')) exit();

$menus = getSiteMenu();
if (!empty($menus['error'])) messageBox($menus['message'],$menus['error'], false);
?>

<link rel="stylesheet" href="<?php echo _AF_URL_ ?>module/admin/sitemap/sitemap.css">
<script src="<?php echo _AF_URL_ ?>module/admin/sitemap/sitemap.js"></script>

<div id="siteMap">
<div class="row menu">
	<div class="col-lg-12">

		<h5><i class="glyphicon glyphicon-indent-left" aria-hidden="true"></i> <strong><?php echo getLang('main_menu')?></strong></h5>
		<div class="panel panel-default">
			<div class="panel-body clearfix">

				<header class="pull-left">
				<div class="text-center"><input type="image" src="<?php echo _AF_URL_ ?>module/admin/sitemap/icon_add.png" value="+" onclick="return siteMapItemAdd(1);" style="cursor:pointer"><br><?php echo getLang('insert')?></div>
				<div class="text-center" style="margin-top:20px"><input type="image" src="<?php echo _AF_URL_ ?>module/admin/sitemap/icon_save.png" value="S" onclick="return siteMapItemSave(1);" style="cursor:pointer"><br><?php echo getLang('save')?></div>
				</header>

				<form action="<?php echo _AF_URL_ . '?admin' ?>" method="post" autocomplete="off" data-type="1">
					<input type="hidden" name="success_return_url" value="<?php echo getUrl('', 'admin', 'menu') ?>">
					<input type="hidden" name="error_return_url" value="<?php echo getUrl('', 'admin', 'menu') ?>">
					<input type="hidden" name="act" value="updateMenu">
					<input type="hidden" name="mu_type" value="0">
					<ul id="siteMapRoot1">

				<?php

				if(empty($menus['error'])){
					$placeholder = getLang('%s %s', ['category', 'title']);
					$is_parent = $depth = 0;
					$deps = [0];
					$li = '';
					foreach ($menus['header'] as $key => $value) {

						if($is_parent < $value['mu_parent']) {
							$depth++;
							echo "<ul>";
							$is_parent = $value['mu_parent'];
						}else if($is_parent > $value['mu_parent']) {
							$tmp = $depth - isset($deps[$value['mu_parent']]) ? $deps[$value['mu_parent']] : 0;
							echo "</li>" . str_repeat("</ul></li>", $depth - $tmp);
							$depth = $tmp;
							$is_parent = $value['mu_parent'];
						} else {
							echo $li;
							$li = "</li>";
						}

						$deps[$value['mu_srl']] = $depth + 1;

						echo '<li class="form-inline sitemap-item">'."\n";
						echo '<input type="hidden" name="parent_key[]" value="'. $value['mu_parent'] .'" class="_parent_key">'."\n";
						echo '<input type="hidden" name="item_key[]" value="'. $value['mu_srl'] .'" class="_item_key">'."\n";
						echo '<input type="hidden" name="desc_key[]" value="'. escapeHtml($value['mu_description']) .'" class="_desc_key">'."\n";
						echo '<input type="hidden" name="collapse_key[]" value="'. $value['mu_collapse'] .'" class="_collapse_key">'."\n";
						echo '<input type="hidden" name="new_win_key[]" value="'. $value['mu_new_win'] .'" class="_new_win_key">'."\n";
						echo '<input type="text" name="item_title[]" placeholder="' . $placeholder . '" value="'. escapeHtml($value['mu_title']) .'" class="form-control input-sm">'."\n";
						echo '<input type="text" name="item_link[]" placeholder="' . getLang('%s (or %s)',['id','link']) . '" value="'. (empty($value['md_id'])?escapeHtml($value['mu_link']):$value['md_id']) .'" class="form-control input-sm">'."\n";
						echo '<span class="side"><input type="image" src="'._AF_URL_.'module/admin/sitemap/icon_tool.png" onclick="return false" data-toggle="modal" data-target=".bs-admin-modal-lg"> <input type="image" src="'._AF_URL_.'module/admin/sitemap/icon_delete.png" value="delete" onclick="return siteMapItemDelete(this);"></span>'."\n";

					}

					echo str_repeat("</li></ul>", $depth) . $li;
				}

				?>

					</ul>
				</form>

			</div>
		</div>
	</div>
</div>

<div class="row menu">
	<div class="col-lg-12">
		<h5><i class="glyphicon glyphicon-indent-left" aria-hidden="true"></i> <strong><?php echo getLang('foot_menu')?></strong></h5>
		<div class="panel panel-default">
			<div class="panel-body clearfix">

				<header class="pull-left">
				<div class="text-center"><input type="image" src="<?php echo _AF_URL_ ?>module/admin/sitemap/icon_add.png" value="+" onclick="return siteMapItemAdd(2);" style="cursor:pointer"><br><?php echo getLang('insert')?></div>
				<div class="text-center" style="margin-top:20px"><input type="image" src="<?php echo _AF_URL_ ?>module/admin/sitemap/icon_save.png" value="S" onclick="return siteMapItemSave(2);" style="cursor:pointer"><br><?php echo getLang('save')?></div>
				</header>

				<form action="<?php echo _AF_URL_ . '?admin' ?>" method="post" autocomplete="off" data-type="2">
					<input type="hidden" name="success_return_url" value="<?php echo getUrl('', 'admin', 'menu') ?>">
					<input type="hidden" name="error_return_url" value="<?php echo getUrl('', 'admin', 'menu') ?>">
					<input type="hidden" name="act" value="updateMenu">
					<input type="hidden" name="mu_type" value="1">
					<ul id="siteMapRoot2">

				<?php

				if(empty($menus['error'])){
					$placeholder = getLang('%s %s', ['category', 'title']);
					$is_parent = $depth = 0;
					$deps = [0];
					$li = '';

					foreach ($menus['footer'] as $key => $value) {

						if($is_parent < $value['mu_parent']) {
							$depth++;
							echo "<ul>";
							$is_parent = $value['mu_parent'];
						}else if($is_parent > $value['mu_parent']) {
							$tmp = $depth - isset($deps[$value['mu_parent']]) ? $deps[$value['mu_parent']] : 0;
							echo "</li>" . str_repeat("</ul></li>", $depth - $tmp);
							$depth = $tmp;
							$is_parent = $value['mu_parent'];
						} else {
							echo $li;
							$li = "</li>";
						}

						$deps[$value['mu_srl']] = $depth + 1;

						echo '<li class="form-inline sitemap-item">'."\n";
						echo '<input type="hidden" name="parent_key[]" value="'. $value['mu_parent'] .'" class="_parent_key">'."\n";
						echo '<input type="hidden" name="item_key[]" value="'. $value['mu_srl'] .'" class="_item_key">'."\n";
						echo '<input type="hidden" name="desc_key[]" value="'. escapeHtml($value['mu_description']) .'" class="_desc_key">'."\n";
						echo '<input type="hidden" name="collapse_key[]" value="'. $value['mu_collapse'] .'" class="_collapse_key">'."\n";
						echo '<input type="hidden" name="new_win_key[]" value="'. $value['mu_new_win'] .'" class="_new_win_key">'."\n";
						echo '<input type="text" name="item_title[]" placeholder="' . $placeholder . '" value="'. escapeHtml($value['mu_title']) .'" class="form-control input-sm">'."\n";
						echo '<input type="text" name="item_link[]" placeholder="' . getLang('%s (or %s)',['id','link']) . '" value="'. (empty($value['md_id'])?escapeHtml($value['mu_link']):$value['md_id']) .'" class="form-control input-sm">'."\n";
						echo '<span class="side"><input type="image" src="'._AF_URL_.'module/admin/sitemap/icon_tool.png" onclick="return false" data-toggle="modal" data-target=".bs-admin-modal-lg"> <input type="image" src="'._AF_URL_.'module/admin/sitemap/icon_delete.png" value="delete" onclick="return siteMapItemDelete(this);"></span>'."\n";

					}

					echo str_repeat("</li></ul>", $depth) . $li;
				}

				?>

					</ul>
				</form>

			</div>
		</div>
	</div>
</div>
</div>


<div id="admin_menu_modal" class="modal fade bs-admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="adminMenuModalTitle">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="adminMenuModalTitle"><?php echo getLang('menu')?></h4>
	  </div>
	  <div class="modal-body">
		<div class="form-group" style="margin-top:20px">
			<label style="display:block"><?php echo getLang('option')?></label>
			<label class="checkbox btn inline" tabindex="0">
				<input type="checkbox" value="1" id="sitemap_mu_collapse">
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				<span><?php echo getLang('collapse')?></span>
			</label>
			<label class="checkbox btn inline" tabindex="0">
				<input type="checkbox" value="1" id="sitemap_mu_new_window">
				<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
				<span><?php echo getLang('new_open_window')?></span>
			</label>
		</div>
		<div class="form-group">
			<label for="sitemap_mu_description"><?php echo getLang('menu_desc')?></label>
			<input type="text" class="form-control" id="sitemap_mu_description" maxlength="255">
			<p class="help-block"><?php echo getLang('desc_menu_desc')?></p>
		</div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo getLang('close')?></button>
		<button type="button" class="btn btn-success"><i class="glyphicon glyphicon-ok" aria-hidden="true"></i> <?php echo getLang('save')?></button>
	  </div>
	</div>
  </div>
</div>


<ul id="siteMap_item_template" class="hide">
	<li class="form-inline sitemap-item">
		<button type="button" class="moveTo">Move to</button>
		<input type="hidden" name="parent_key[]" value="0" class="_parent_key">
		<input type="hidden" name="item_key[]" value="0" class="_item_key">
		<input type="hidden" name="desc_key[]" value="" class="_desc_key">
		<input type="hidden" name="collapse_key[]" value="0" class="_collapse_key">
		<input type="hidden" name="new_win_key[]" value="0" class="_new_win_key">
		<input type="text" name="item_title[]" placeholder="<?php echo getLang('%s %s', ['category', 'title'])?>" value="" class="form-control input-sm">
		<input type="text" name="item_link[]" placeholder="<?php echo getLang('%s (or %s)',['id','link'])?>" value="" class="form-control input-sm">
		<span class="side">
			<input type="image" src="<?php echo _AF_URL_ ?>module/admin/sitemap/icon_tool.png" onclick="return false" data-toggle="modal" data-target=".bs-admin-modal-lg">
			<input type="image" src="<?php echo _AF_URL_ ?>module/admin/sitemap/icon_delete.png" value="delete" onclick="return siteMapItemDelete(this);">
		</span>
	</li>
</ul>

<?php
/* End of file menu.php */
/* Location: ./module/admin/menu.php */
