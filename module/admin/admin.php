<?php

if(!defined('__AFOX__')) exit();

if(empty($_MEMBER)) goUrl(_AF_URL_, getLang('msg_not_permitted'));
// 관리자의 아이피, 브라우저와 다르다면 세션을 끊고 관리자에게 메일을 보낸다.
$admin_key = md5($_MEMBER['mb_regdate'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
if (get_session('ss_mb_key') !== $admin_key) {
	session_destroy();
	// TODO 관리자에게 쪽지 보낸다.
	goUrl(_AF_URL_, getLang('msg_not_permitted'));
}

@include_once _AF_LANGS_PATH_ . 'module_' . _AF_LANG_ . '.php';
@include_once _AF_LANGS_PATH_ . 'admin_' . _AF_LANG_ . '.php';

$_MENU_ICON = ['dashbd'=>'dashboard', 'theme'=>'home', 'menu'=>'menu-hamburger', 'member'=>'user', 'content'=>'list-alt', 'page'=>'list-alt', 'board'=>'list-alt', 'document'=>'list-alt', 'comment'=>'list-alt', 'file'=>'list-alt', 'trash'=>'trash', 'module'=>'th-large', 'addon'=>'random', 'widget'=>'import', 'setup'=>'cog'];

$admin = empty($_DATA['admin']) ? 'dashbd' :  $_DATA['admin'];

?>

<div id="wrapper">

		<!-- Navigation -->
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo _AF_URL_ . '?admin' ?>">aFox BD Admin <small>ver <?php echo _AF_VERSION_?></small></a>
			</div>
			<!-- Top Menu Items -->
			<ul class="nav navbar-right top-nav">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_MEMBER['mb_nick']?> <b class="caret"></b></a>
					<ul class="dropdown-menu pull-right">
						<li><a href="#"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i> <?php echo getLang('inbox')?></a></li>
						<li><a href="<?php echo _AF_URL_ ?>?module=member&disp=signUp"><i class="glyphicon glyphicon-cog" aria-hidden="true"></i> <?php echo getLang('setup')?></a></li>
						<li class="divider"></li>
						<li><a href="#" data-exec-ajax="member.logOut" data-ajax-param="success_return_url,<?php echo getUrl('')?>"><i class="glyphicon glyphicon-off" aria-hidden="true"></i> <?php echo getLang('logout')?></a></li>
					</ul>
				</li>
			</ul>
			<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
			<div class="collapse navbar-collapse navbar-ex1-collapse">
				<ul class="nav navbar-nav side-nav">

					<li role="presentation"<?php echo empty($_DATA['admin']) ? ' class="active"': '' ?>><a href="./?admin"><i class="glyphicon glyphicon-dashboard" aria-hidden="true"></i> <?php echo getLang('menu_name_dashbd')?></a></li>
					<li role="presentation"<?php echo $_DATA['admin'] == 'theme' ? ' class="active"': '' ?>><a href="./?admin=theme"><i class="glyphicon glyphicon-home" aria-hidden="true"></i> <?php echo getLang('menu_name_theme')?></a></li>
					<li role="presentation"<?php echo $_DATA['admin'] == 'menu' ? ' class="active"': '' ?>><a href="./?admin=menu"><i class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></i> <?php echo getLang('menu_name_menu')?></a></li>
					<li role="presentation"<?php echo $_DATA['admin'] == 'member' ? ' class="active"': '' ?>><a href="./?admin=member"><i class="glyphicon glyphicon-user" aria-hidden="true"></i> <?php echo getLang('menu_name_member')?></a></li>
					<li role="presentation"><a href="#" data-toggle="collapse" data-target="#content"><i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i> <?php echo getLang('menu_name_content')?></a>
						<ul id="content" class="collapse<?php echo in_array($_DATA['admin'], ['page','board','document','comment','file','trash'])?' in':''?>">
							<li<?php echo $_DATA['admin'] == 'page' ? ' class="active"': '' ?>>
								<a href="./?admin=page"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_page')?></a>
							</li>
							<li<?php echo $_DATA['admin'] == 'board' ? ' class="active"': '' ?>>
								<a href="./?admin=board"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_board')?></a>
							</li>
							<li<?php echo $_DATA['admin'] == 'document' ? ' class="active"': '' ?>>
								<a href="./?admin=document"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_document')?></a>
							</li>
							<li<?php echo $_DATA['admin'] == 'comment' ? ' class="active"': '' ?>>
								<a href="./?admin=comment"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_comment')?></a>
							</li>
							<li<?php echo $_DATA['admin'] == 'file' ? ' class="active"': '' ?>>
								<a href="./?admin=file"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_file')?></a>
							</li>
							<li<?php echo $_DATA['admin'] == 'trash' ? ' class="active"': '' ?>>
								<a href="./?admin=trash"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_trash')?></a>
							</li>
						</ul>
					</li>
					<li role="presentation"<?php echo $_DATA['admin'] == 'visit' ? ' class="active"': '' ?>><a href="./?admin=visit"><i class="glyphicon glyphicon-globe" aria-hidden="true"></i> <?php echo getLang('menu_name_visit')?></a></li>
					<li role="presentation"<?php echo $_DATA['admin'] == 'module' ? ' class="active"': '' ?>><a href="./?admin=module"><i class="glyphicon glyphicon-th-large" aria-hidden="true"></i> <?php echo getLang('menu_name_module')?></a></li>
					<li role="presentation"<?php echo $_DATA['admin'] == 'addon' ? ' class="active"': '' ?>><a href="./?admin=addon"><i class="glyphicon glyphicon-random" aria-hidden="true"></i> <?php echo getLang('menu_name_addon')?></a></li>
					<li role="presentation"<?php echo $_DATA['admin'] == 'widget' ? ' class="active"': '' ?>><a href="./?admin=widget"><i class="glyphicon glyphicon-import" aria-hidden="true"></i> <?php echo getLang('menu_name_widget')?></a></li>
					<li role="presentation"<?php echo $_DATA['admin'] == 'setup' ? ' class="active"': '' ?>><a href="./?admin=setup"><i class="glyphicon glyphicon-cog" aria-hidden="true"></i> <?php echo getLang('menu_name_setup')?></a></li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</nav>

		<div id="page-wrapper">

			<div id="<?php echo (empty($_DATA['mid']) ? 'ADM_DEFAULT_MODULE' : 'ADM_CUSTOM_MODULE') ?>" class="container-fluid">

				<?php if(empty($_DATA['mid'])) { ?>
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">
							<?php echo getLang('menu_name_'.$admin)?>
						</h1>
						<ol class="breadcrumb">
							<li class="active">
								<i class="glyphicon glyphicon-<?php echo $_MENU_ICON[$admin]?>" aria-hidden="true"></i> <?php echo getLang('menu_desc_'.$admin)?>
							</li>
						</ol>
					</div>
				</div>
				<?php } ?>

				<!-- /.row -->

				<?php
					if(is_array($err = get_error())) echo showMessage($err['message'], $err['error']);
					require_once _AF_ADMIN_PATH_ . $admin . '.php';
				?>

			</div>
			<!-- /.container-fluid -->

		</div>
		<!-- /#page-wrapper -->

	</div>
	<!-- /#wrapper -->


<script>
	$_LANG['document'] = "<?php echo getLang('document')?>";
	$_LANG['page'] = "<?php echo getLang('page')?>";
	$_LANG['board'] = "<?php echo getLang('board')?>";
	$_LANG['menu'] = "<?php echo getLang('menu')?>";
	$_LANG['addon'] = "<?php echo getLang('addon')?>";
	$_LANG['theme'] = "<?php echo getLang('theme')?>";
	$_LANG['confirm_select_empty'] = "<?php echo getLang('confirm_select_empty')?>";
	$_LANG['confirm_select_delete'] = "<?php echo getLang('confirm_select_delete')?>";
</script>
<?php
/* End of file admin.php */
/* Location: ./module/admin/admin.php */