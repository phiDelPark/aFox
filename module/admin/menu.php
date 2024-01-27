<?php
if(!defined('__AFOX__')) exit();
$menus = getSiteMenu();
if (!empty($menus['error'])) messageBox($menus['message'],$menus['error'], false);
$placeholder = getLang('%s %s', ['category', 'title']);
?>

<link rel="stylesheet" href="<?php echo _AF_URL_ ?>module/admin/sitemap/sitemap.min.css">
<script src="<?php echo _AF_URL_ ?>module/admin/sitemap/sitemap.min.js"></script>

<div id="siteMap">
	<h5 class="pb-2 mb-4 border-bottom"><?php echo getLang('main_menu')?></h5>
	<div class="mb-5">
		<form action="<?php echo _AF_URL_ . '?admin' ?>" method="post" autocomplete="off">
			<header class="float-start">
			<div class="text-center"><input type="button" width="24" height="24" onclick="return siteMapItemAdd(this, 1)"><br><?php echo getLang('insert')?></div>
			<div class="text-center" style="margin-top:20px"><input type="submit" width="24" height="24"><br><?php echo getLang('save')?></div>
			</header>
			<div>
			<input type="hidden" name="success_url" value="<?php echo getUrl('', 'admin', 'menu') ?>">
			<input type="hidden" name="error_url" value="<?php echo getUrl('', 'admin', 'menu') ?>">
			<input type="hidden" name="act" value="updateMenu">
			<input type="hidden" name="mu_type" value="0">
			<ul id="siteMapRoot1" class="ms-5 p-2 border rounded" style="min-height:300px">

<?php
		if(empty($menus['error'])){
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

				echo '<li class="sitemap-item">'."\n";
				echo '<span class="side"><input type="button" width="24" height="24" value="setup">'."\n";
				echo '<input type="button" width="24" height="24" width="24" height="24" value="delete"></span>'."\n";
				echo '<input type="hidden" name="parent_key[]" value="'. $value['mu_parent'] .'">'."\n";
				echo '<input type="hidden" name="item_key[]" value="'. $value['mu_srl'] .'">'."\n";
				echo '<span class="indent setup d-none"><input type="text" name="desc_key[]" value="'. escapeHtml($value['mu_description']) .'" class="form-control" placeholder="'.getLang('menu_desc').'">'."\n";
				echo '<input type="checkbox" name="collapse_key[]"'.(empty($value['mu_collapse'])?'':' checked').'>'.getLang('collapse')."\n";
				echo '<input type="checkbox" name="new_win_key[]"'.(empty($value['mu_new_win'])?'':' checked').'>'.getLang('new_window').'</span>'."\n";
				echo '<span class="indent input"><input type="text" name="item_title[]" placeholder="' . $placeholder . '" value="'. escapeHtml($value['mu_title']) .'" class="form-control">'."\n";
				echo '<input type="text" name="item_link[]" placeholder="' . getLang('%s (or %s)',['id','link']) . '" value="'. (empty($value['md_id'])?escapeHtml($value['mu_link']):$value['md_id']) .'" class="form-control"></span>'."\n";
			}

			echo str_repeat("</li></ul>", $depth) . $li;
		}
?>

			</ul>
			</div>
		</form>
	</div>

	<h5 class="pb-2 mb-4 border-bottom"><?php echo getLang('foot_menu')?></h5>
	<div class="mb-5">
		<form action="<?php echo _AF_URL_ . '?admin' ?>" method="post" autocomplete="off">
			<header class="float-start">
			<div class="text-center"><input type="button" width="24" height="24" onclick="return siteMapItemAdd(this, 2)"><br><?php echo getLang('insert')?></div>
			<div class="text-center" style="margin-top:20px"><input type="submit" width="24" height="24"><br><?php echo getLang('save')?></div>
			</header>
			<div>
			<input type="hidden" name="success_url" value="<?php echo getUrl('', 'admin', 'menu') ?>">
			<input type="hidden" name="error_url" value="<?php echo getUrl('', 'admin', 'menu') ?>">
			<input type="hidden" name="act" value="updateMenu">
			<input type="hidden" name="mu_type" value="1">
			<ul id="siteMapRoot2" class="ms-5 p-2 border rounded" style="min-height:300px">

<?php
		if(empty($menus['error'])){
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

				echo '<li class="sitemap-item">'."\n";
				echo '<span class="side"><input type="button" width="24" height="24" value="setup">'."\n";
				echo '<input type="button" width="24" height="24" width="24" height="24" value="delete"></span>'."\n";
				echo '<input type="hidden" name="parent_key[]" value="'. $value['mu_parent'] .'">'."\n";
				echo '<input type="hidden" name="item_key[]" value="'. $value['mu_srl'] .'">'."\n";
				echo '<span class="indent setup d-none"><input type="text" name="desc_key[]" value="'. escapeHtml($value['mu_description']) .'" class="form-control" placeholder="'.getLang('menu_desc').'">'."\n";
				echo '<input type="checkbox" name="collapse_key[]"'.(empty($value['mu_collapse'])?'':' checked').'>'.getLang('collapse')."\n";
				echo '<input type="checkbox" name="new_win_key[]"'.(empty($value['mu_new_win'])?'':' checked').'>'.getLang('new_window').'</span>'."\n";
				echo '<span class="indent input"><input type="text" name="item_title[]" placeholder="' . $placeholder . '" value="'. escapeHtml($value['mu_title']) .'" class="form-control">'."\n";
				echo '<input type="text" name="item_link[]" placeholder="' . getLang('%s (or %s)',['id','link']) . '" value="'. (empty($value['md_id'])?escapeHtml($value['mu_link']):$value['md_id']) .'" class="form-control"></span>'."\n";
			}

			echo str_repeat("</li></ul>", $depth) . $li;
		}
?>

			</ul>
			</div>
		</form>
	</div>
</div>

<ul id="siteMap_item_template" class="d-none">
	<li class="sitemap-item">
		<span class="side"><input type="button" width="24" height="24" value="setup">
		<input type="button" width="24" height="24" value="delete"></span>
		<input type="hidden" name="parent_key[]">
		<input type="hidden" name="item_key[]">
		<span class="indent setup d-none">
			<input type="text" name="desc_key[]" class="form-control" placeholder="<?php echo getLang('menu_desc')?>">
			<input type="checkbox" name="collapse_key[]"><?php echo getLang('collapse')?>
			<input type="checkbox" name="new_win_key[]"><?php echo getLang('new_window')?>
		</span>
		<span class="indent input">
			<input type="text" name="item_title[]" placeholder="<?php echo $placeholder?>" class="form-control">
			<input type="text" name="item_link[]" placeholder="<?php echo getLang('%s (or %s)',['id','link'])?>" class="form-control">
		</span>
	</li>
</ul>

<?php
/* End of file menu.php */
/* Location: ./module/admin/menu.php */
