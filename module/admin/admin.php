<?php
if(!defined('__AFOX__')) exit();

$_MENU_ICON = ['default'=>'dashboard', 'theme'=>'home', 'menu'=>'menu-hamburger', 'member'=>'user', 'content'=>'list-alt', 'page'=>'list-alt', 'board'=>'list-alt', 'document'=>'list-alt', 'comment'=>'list-alt', 'file'=>'list-alt', 'trash'=>'trash', 'module'=>'th-large', 'addon'=>'random', 'widget'=>'import', 'setup'=>'cog', 'visit'=>'globe'];
$admin = empty($_POST['disp']) ? 'default' :  $_POST['disp'];
$is_admin = isAdmin();
?>

<!-- top navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
	<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="offcanvasExample">
	  <span class="navbar-toggler-icon" data-bs-target="#sidebar"></span>
	</button>
	<a class="navbar-brand me-auto ms-lg-0 ms-3 text-uppercase fw-bold" href="#">ADMINISTRATION</a>
	<div class="collapse navbar-collapse" style="flex:none">
	  <ul class="navbar-nav">
		<li class="nav-item dropdown">
		  <a class="nav-link dropdown-toggle ms-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
		  	<?php echo $_MEMBER['mb_nick']?>
		  </a>
		  <ul class="dropdown-menu dropdown-menu-end">
		 	<li><a class="dropdown-item" href="./?admin=clearcache"><svg class="bi" aria-hidden="true"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#recycle"/></svg> <?php echo getLang('clear_cache') ?></a></li>
			<li><hr class="dropdown-divider"></li>
			<li><a class="dropdown-item" href="<?php echo getUrl('', 'module', 'member', 'act', 'signOut')?>"><svg class="bi" aria-hidden="true"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#power"/></svg> <?php echo getLang('logout') ?></a></li>
		  </ul>
		</li>
	  </ul>
	</div>
  </div>
</nav>
<!-- top navigation bar -->
<!-- offcanvas -->
<div class="offcanvas sidebar-nav bg-dark" tabindex="-1" id="sidebar">
  <div class="offcanvas-body p-0">
	<nav class="navbar-dark">
	  <ul class="navbar-nav">
		<li>
		  <div class="text-muted small fw-bold text-uppercase px-3">
			CORE
		  </div>
		</li>
		<li>
		  <a href="./?admin" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'default' ? ' active': '' ?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#speedometer2"/></svg>
			<span><?php echo getLang('menu_name_dashbd')?></a></span>
		  </a>
		</li>
		<li class="my-3"><hr class="navbar-divider" /></li>
		<li>
		  <div class="text-muted small fw-bold text-uppercase px-3">
			Interface
		  </div>
		</li>
		<li>
		  <a href="./?admin=theme" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'theme' ? ' active': '' ?>">
		  <svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#house-door"/></svg>
			<span><?php echo getLang('menu_name_theme')?></span>
		  </a>
		</li>
		<li>
		  <a href="./?admin=menu" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'menu' ? ' active': '' ?>">
		  	<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#menu-button"/></svg>
			<span><?php echo getLang('menu_name_menu')?></span>
		  </a>
		</li>
		<li class="my-3"><hr class="navbar-divider" /></li>
		<li>
		  <div class="text-muted small fw-bold text-uppercase px-3">
		  Contents
		  </div>
		</li>
		<li>
			<a href="./?admin=page" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'page' ? ' active': '' ?>">
				<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#book"/></svg>
				<span><?php echo getLang('menu_name_page')?></span>
			</a>
		</li>
		<li>
			<a href="./?admin=board" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'board' ? ' active': '' ?>">
				<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#collection"/></svg>
				<span><?php echo getLang('menu_name_board')?></span>
			</a>
		</li>
		<?php $tmp=(in_array($_POST['disp'], ['document','comment','file','trash']))?>
		<li>
		  <a class="nav-link px-3 pb-0 sidebar-link" data-bs-toggle="collapse" href="#layouts" aria-expanded="<?php echo $tmp?'true':'false'?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#box"/></svg>
			<span style="margin-left:4px"><?php echo getLang('menu_name_content')?></span>
			<span class="ms-auto">
			  <span class="right-icon">
			  <svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#chevron-down"/></svg>
			  </span>
			</span>
		  </a>
		  <div class="collapse<?php echo $tmp?' show':''?>" id="layouts">
			<ul class="navbar-nav ps-3">
			  <li>
				<a href="./?admin=document" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'document' ? ' active': '' ?>">
					<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#chat-right-text-fill"/></svg>
					<span><?php echo getLang('menu_name_document')?></span>
				</a>
			  </li>
			  <li>
				<a href="./?admin=comment" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'comment' ? ' active': '' ?>">
					<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#chat-right-quote-fill"/></svg>
					<span><?php echo getLang('menu_name_comment')?></span>
				</a>
			  </li>
			  <li>
				<a href="./?admin=file" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'file' ? ' active': '' ?>">
					<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#floppy-fill"/></svg>
					<span><?php echo getLang('menu_name_file')?></span>
				</a>
			  </li>
			  <li>
				<a href="./?admin=trash" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'trash' ? ' active': '' ?>">
					<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#trash-fill"/></svg>
					<span><?php echo getLang('menu_name_trash')?></span>
				</a>
			  </li>
			</ul>
		  </div>
		</li>
		<li class="my-3"><hr class="navbar-divider" /></li>
		<li>
		  <div class="text-muted small fw-bold text-uppercase px-3">
			Addons
		  </div>
		</li>
		<li>
		  <a href="./?admin=module" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'module' ? ' active': '' ?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#grid"/></svg>
			<span><?php echo getLang('menu_name_module')?></span>
		  </a>
		</li>
		<li>
		  <a href="./?admin=addon" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'addon' ? ' active': '' ?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#plugin"/></svg>
			<span><?php echo getLang('menu_name_addon')?></span>
		  </a>
		</li>
		<li>
		  <a href="./?admin=widget" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'widget' ? ' active': '' ?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#puzzle"/></svg>
			<span><?php echo getLang('menu_name_widget')?></span>
		  </a>
		</li>
		<li>
		  <a href="./?admin=visit" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'visit' ? ' active': '' ?>">
		  <svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#airplane"/></svg>
			<span><?php echo getLang('menu_name_visit')?></span>
		  </a>
		</li>
		<li class="my-3"><hr class="navbar-divider" /></li>
		<li>
		  <div class="text-muted small fw-bold text-uppercase px-3">
		  Settings
		  </div>
		</li>
		<li>
		  <a href="./?admin=member" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'member' ? ' active': '' ?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#person"/></svg>
			<span><?php echo getLang('menu_name_member')?></span>
		  </a>
		</li>
		<li>
		  <a href="./?admin=setup" class="nav-link px-3 pb-0<?php echo $_POST['disp'] == 'setup' ? ' active': '' ?>">
		  	<svg class="bi me-2" width="1em" height="1em"><use href="<?php echo _AF_URL_?>module/admin/bi-icons.svg#gear"/></svg>
			<span><?php echo getLang('menu_name_setup')?></span>
		  </a>
		</li>
	  </ul>
	</nav>
  </div>
</div>
<!-- offcanvas -->
<main class="my-4 p-1 pt-5">

<?php if($_POST['disp'] != 'default') { ?>

	<div class="mx-2 mb-4">
		<h3><?php echo getLang('menu_name_'.$admin)?></h3>
		<hr class="navbar-divider" />
		<small class="d-inline-flex w-100 px-2 py-1 fw-semibold text-secondary-emphasis bg-secondary-subtle border border-secondary-subtle rounded-1"><?php echo getLang('menu_desc_'.$admin)?></small>
	</div>

<?php } ?>
	<section class="container-fluid">
<?php
		if (!$is_admin && !in_array($admin,['default'])) {
			messageBox(getLang('error_permitted'), 4501, false);
		} else {
			if (is_array($err = get_error())) messageBox($err['message'], $err['error'], false);
			if(!empty($_POST['mid'])){
				$_POST['md_id'] = $_POST['mid'];
				@include_once _AF_MODULES_PATH_ . $admin . '/lang/' . _AF_LANG_ . '.php';
				require_once _AF_MODULES_PATH_ . $admin . '/setup.php';
			} else if(!empty($_POST['th_id']) || !empty($_POST['md_id']) || !empty($_POST['ao_id']) || !empty($_POST['wg_id'])){
				require_once _AF_ADMIN_PATH_ . 'form/' . (
					!empty($_POST['th_id'])	? 'themeform'
					: (!empty($_POST['md_id']) ? 'moduleform'
						: (!empty($_POST['ao_id']) ? 'addonform'
							: 'widgetform'))) . '.php';
			} else {
				require_once _AF_ADMIN_PATH_ . $admin . '.php';
			}
		}
?>
	</section>
</main>
<?php
/* End of file admin.php */
/* Location: ./module/admin/admin.php */
