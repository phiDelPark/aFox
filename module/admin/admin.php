<?php
if(!defined('__AFOX__')) exit();

$_MENU_ICON = ['default'=>'dashboard', 'theme'=>'home', 'menu'=>'menu-hamburger', 'member'=>'user', 'content'=>'list-alt', 'page'=>'list-alt', 'board'=>'list-alt', 'document'=>'list-alt', 'comment'=>'list-alt', 'file'=>'list-alt', 'trash'=>'trash', 'module'=>'th-large', 'addon'=>'random', 'widget'=>'import', 'setup'=>'cog', 'visit'=>'globe'];
$admin = empty($_DATA['disp']) ? 'default' :  $_DATA['disp'];
$is_admin = isAdmin();
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
				<a class="navbar-brand" href="<?php echo _AF_URL_ . '?admin' ?>">aFox Admin <small>ver <?php echo _AF_VERSION_?></small></a>
			</div>
			<!-- Top Menu Items -->
			<ul class="nav navbar-right top-nav">
				<li><a href="#" data-exec-ajax="admin.clearCache" data-ajax-param="success_return_url,<?php echo urlencode(getUrl())?>"><?php echo getLang('clear_cache')?></a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_MEMBER['mb_nick']?> <strong class="caret"></strong></a>
					<ul class="dropdown-menu pull-right">
						<li><a href="<?php echo getUrl('','module','member','disp','inbox')?>"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i> <?php echo getLang('inbox')?></a></li>
						<li class="divider"></li>
						<li><a href="#" data-exec-ajax="member.logOut" data-ajax-param="success_return_url,<?php echo urlencode(getUrl(''))?>"><i class="glyphicon glyphicon-off" aria-hidden="true"></i> <?php echo getLang('logout')?></a></li>
					</ul>
				</li>
			</ul>
			<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
			<div class="collapse navbar-collapse navbar-ex1-collapse">
				<ul class="nav navbar-nav side-nav">
					<?php if($is_admin) { ?>
					<li role="presentation"<?php echo empty($_DATA['disp']) ? ' class="active"': '' ?>><a href="./?admin"><i class="glyphicon glyphicon-dashboard" aria-hidden="true"></i> <?php echo getLang('menu_name_dashbd')?></a></li>
					<li role="presentation"<?php echo $_DATA['disp'] == 'theme' ? ' class="active"': '' ?>><a href="./?admin=theme"><i class="glyphicon glyphicon-home" aria-hidden="true"></i> <?php echo getLang('menu_name_theme')?></a></li>
					<li role="presentation"<?php echo $_DATA['disp'] == 'menu' ? ' class="active"': '' ?>><a href="./?admin=menu"><i class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></i> <?php echo getLang('menu_name_menu')?></a></li>
					<li role="presentation"<?php echo $_DATA['disp'] == 'member' ? ' class="active"': '' ?>><a href="./?admin=member"><i class="glyphicon glyphicon-user" aria-hidden="true"></i> <?php echo getLang('menu_name_member')?></a></li>
					<?php } ?>
					<li role="presentation"><a href="#" data-toggle="collapse" data-target="#content" onclick="return false"><i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i> <?php echo getLang('menu_name_content')?></a>
						<ul id="content" class="collapse<?php echo (!$is_admin||in_array($_DATA['disp'], ['page','board','document','comment','file','trash']))?' in':''?>">
							<?php if($is_admin) { ?>
							<li<?php echo $_DATA['disp'] == 'page' ? ' class="active"': '' ?>>
								<a href="./?admin=page"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_page')?></a>
							</li>
							<li<?php echo $_DATA['disp'] == 'board' ? ' class="active"': '' ?>>
								<a href="./?admin=board"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_board')?></a>
							</li>
							<?php } ?>
							<li<?php echo $_DATA['disp'] == 'document' ? ' class="active"': '' ?>>
								<a href="./?admin=document"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_document')?></a>
							</li>
							<li<?php echo $_DATA['disp'] == 'comment' ? ' class="active"': '' ?>>
								<a href="./?admin=comment"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_comment')?></a>
							</li>
							<li<?php echo $_DATA['disp'] == 'file' ? ' class="active"': '' ?>>
								<a href="./?admin=file"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_file')?></a>
							</li>
							<?php if($is_admin) { ?>
							<li<?php echo $_DATA['disp'] == 'trash' ? ' class="active"': '' ?>>
								<a href="./?admin=trash"><i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i> <?php echo getLang('menu_name_trash')?></a>
							</li>
							<?php } ?>
						</ul>
					</li>
					<?php if($is_admin) { ?>
					<li role="presentation"<?php echo $_DATA['disp'] == 'visit' ? ' class="active"': '' ?>><a href="./?admin=visit"><i class="glyphicon glyphicon-globe" aria-hidden="true"></i> <?php echo getLang('menu_name_visit')?></a></li>
					<?php } ?>
					<li role="presentation"<?php echo $_DATA['disp'] == 'module' ? ' class="active"': '' ?>><a href="./?admin=module"><i class="glyphicon glyphicon-th-large" aria-hidden="true"></i> <?php echo getLang('menu_name_module')?></a></li>
					<?php if($is_admin) { ?>
					<li role="presentation"<?php echo $_DATA['disp'] == 'addon' ? ' class="active"': '' ?>><a href="./?admin=addon"><i class="glyphicon glyphicon-random" aria-hidden="true"></i> <?php echo getLang('menu_name_addon')?></a></li>
					<li role="presentation"<?php echo $_DATA['disp'] == 'widget' ? ' class="active"': '' ?>><a href="./?admin=widget"><i class="glyphicon glyphicon-import" aria-hidden="true"></i> <?php echo getLang('menu_name_widget')?></a></li>
					<li role="presentation"<?php echo $_DATA['disp'] == 'setup' ? ' class="active"': '' ?>><a href="./?admin=setup"><i class="glyphicon glyphicon-cog" aria-hidden="true"></i> <?php echo getLang('menu_name_setup')?></a></li>
					<?php } ?>
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
				<!-- /.row -->
				<?php }
					if (!$is_admin && !in_array($admin,['default','document','comment','file','module'])) {
						messageBox(getLang('error_permitted'), 4501, false);
					} else {
						if (is_array($err = get_error())) messageBox($err['message'], $err['error'], false);
						require_once _AF_ADMIN_PATH_ . $admin . '.php';
					}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
	</div>
	<!-- /#wrapper -->
<?php
/* End of file admin.php */
/* Location: ./module/admin/admin.php */
