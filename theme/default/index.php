<?php
	if(!defined('__AFOX__')) exit();

	$menus = getSiteMenu();

	$mainmenu = [];
	$submenu = [];
	if(empty($menus['error'])) {
		$url = getCurrentUrl();
		foreach ($menus['header'] as $val) {
			$is_active = !empty($val['md_id'])&&$val['md_id']==$_DATA['id']?'_ACTIVE_':$val['mu_srl'];
			if($is_active!='_ACTIVE_' && !empty($val['mu_link']) && strpos($url, $val['mu_link'])!==false) {$is_active='_ACTIVE_';}
			if((int) $val['mu_parent'] > 0) {
				if($is_active == '_ACTIVE_') {
					// 활성화시 키값 교체
					if(!empty($submenu[$muroot])){
						$submenu['_ACTIVE_'] = $submenu[$muroot];
						unset($submenu[$muroot]);
					}
					$mainmenu['_ACTIVE_'] = $mainmenu[$muroot];
					unset($mainmenu[$muroot]);
					$muroot = '_ACTIVE_';
					$val['_ACTIVE_'] = 1;
				}
				$submenu[$muroot][] = $val;
			} else {
				$muroot = $is_active;
				$mainmenu[$muroot] = $val;
			}
		}

	}

?>

<?php if($err = get_error()) { ?>
	<div class="auto-hide" data-timer="5">
		<h3 class="clearfix"><span class="timer-progress pull-left" data-repeat-char="&bull;"></span> <i class="fa fa-warning" aria-hidden="true"></i> <?php echo $err['message']?></h3>
	</div>
<?php } ?>

<div class="container">

	<header class="bs-docs-header">
		<h1 id="logo_title"><img src="<?php echo empty($_CFG['logo']) ? _AF_THEME_URL_.'img/logo.png' : $_CFG['logo']?>" alt="<?php echo escapeHtml($_CFG['title'])?>" height="50"></h1>
		<div class="right">
<?php if (!empty($_MEMBER)) {
	$notes = getDBList(_AF_NOTE_TABLE_, ['mb_srl'=>$_MEMBER['mb_srl'],'nt_read_date'=>'0000-00-00 00:00:00'], '', 1, 5);
?>
			<?php if(empty($notes['error']) && $notes['total_count'] > 0){ ?>
			<span>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope" aria-hidden="true"></i> <b class="caret"></b></a>
				<ul class="dropdown-menu message-dropdown dropdown-menu-right">
					<?php foreach ($notes['data'] as $val) {
						$_icon = $val['nt_sender'].'/profile_image.png';
						if(file_exists(_AF_MEMBER_DATA_.$_icon)) {
							$_icon = _AF_URL_ . 'data/member/' . $_icon;
						} else {
							$_icon = _AF_URL_ .'module/board/tpl/user_default.jpg';
						}
					?>
					<li class="message-preview">
						<a href="<?php echo getUrl('','module','member','disp','inbox','srl', $val['nt_srl']) ?>">
							<div class="media">
								<span class="pull-left">
									<img class="media-object" src="<?php echo $_icon ?>">
								</span>
								<div class="media-body">
									<h5 class="media-heading"><strong><?php echo escapeHtml($val['nt_sender_nick']) ?></strong></h5>
									<p class="small text-muted"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo date('Y/m/d', strtotime($val['nt_send_date'])) ?></p>
									<p><?php echo cutstr(strip_tags($val['nt_content']),35) ?></p>
								</div>
							</div>
						</a>
					</li>
					<?php } ?>
					<li class="message-footer">
						<a href="#" data-exec-ajax="member.readAllNotes" data-ajax-param="success_return_url,<?php echo getUrl()?>">Mark all messages as read</a>
					</li>
				</ul>
			</span>
			<?php } ?>
			<span>
			<?php
				$_icon = $_MEMBER['mb_srl'].'/profile_image.png';
				if(file_exists(_AF_MEMBER_DATA_.$_icon)) {
					$_icon = _AF_URL_ . 'data/member/' . $_icon;
				} else {
					$_icon = _AF_URL_ .'module/board/tpl/user_default.jpg';
				}
			?>
				<a href="#" class="dropdown-toggle login-icon" data-toggle="dropdown"><img src="<?php echo $_icon ?>" alt="<?php echo escapeHtml($_MEMBER['mb_nick'])?>" /></a>
				<ul class="dropdown-menu dropdown-menu-right">
					<?php if(isManager($_DATA['id'])) { ?>
					<li><a href="<?php echo _AF_URL_?>?admin"><i class="fa fa-fw fa-user" aria-hidden="true"></i> <?php echo $_MEMBER['mb_nick']?></a></li>
					<?php } else { ?>
					<li class="dropdown-header"><i class="fa fa-fw fa-user" aria-hidden="true"></i> <?php echo $_MEMBER['mb_nick']?></li>
					<?php } ?>
					<li class="divider"></li>
					<li><a href="<?php echo getUrl('','module','member','disp','inbox') ?>"><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo getLang('inbox')?></a></li>
					<li><a href="<?php echo getUrl('','module','member','disp','trash') ?>"><i class="fa fa-trash" aria-hidden="true"></i> <?php echo getLang('recycle_bin')?></a></li>
					<li><a href="<?php echo _AF_URL_ ?>?module=member&disp=signUp"><i class="fa fa-fw fa-gear" aria-hidden="true"></i> <?php echo getLang('setup')?></a></li>
					<li class="divider"></li>
					<li><a href="#" data-exec-ajax="member.logOut" data-ajax-param="success_return_url,<?php echo getUrl('')?>"><i class="fa fa-fw fa-power-off" aria-hidden="true"></i> <?php echo getLang('logout')?></a></li>
				</ul>
			</span>
<?php } else { ?>
			<a href="#" data-toggle="modal" data-target="#loginModal"><i class="fa fa-user fa-2" aria-hidden="true"></i></a>
			<!-- Modal -->
			<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel">
				<div class="modal-dialog">
					<form class="modal-content" method="post" autocomplete="off" data-exec-ajax="member.loginCheck">
					<input type="hidden" name="success_return_url" value="<?php echo getUrl()?>" />
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="loginModalLabel"><?php echo getLang('login')?></h4>
						</div>
						<div class="modal-body">
								<div class="form-group">
									<input type="text" class="form-control" name="mb_id" maxlength="20" placeholder="<?php echo getLang('id')?>" required /> <span class="sr-only"><?php echo getLang('id')?></span>
								</div>
								<div class="form-group">
									<input type="password" class="form-control" name="mb_password" placeholder="<?php echo getLang('password')?>" required /> <span class="sr-only"><?php echo getLang('password')?></span>
								</div>
								<div class="checkbox">
									<label><input type="checkbox" name="auto_login" /> <?php echo getLang('auto_login')?></label>
								</div>
						</div>
						<div class="modal-footer">
							<div class="pull-left">
								<a href="<?php echo _AF_URL_ ?>?module=member&disp=signUp"><strong><?php echo getLang('member_signup')?></strong></a> /
								<a href="<?php echo _AF_URL_ ?>?module=member&disp=findAccount"><?php echo getLang('member_find')?></a>
							</div>
							<?php if(!__MOBILE__) { ?><button type="button" class="btn btn-default" data-dismiss="modal"> <?php echo getLang('close')?></a></button><?php } ?>
							<button type="submit" class="btn btn-primary"><?php echo getLang('login')?></button>
						</div>
					</form>
				</div>
			</div>
<?php } ?>
		</div>
	</header>

	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-9" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo getUrl('')?>"><?php echo escapeHtml($_CFG['title'])?></a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-9">
				<ul class="nav navbar-nav right">

<?php
	foreach ($mainmenu as $key => $val) {
		echo '<li'.($key==='_ACTIVE_'?' class="active"':'').'><a href="'. escapeHtml($val['mu_link']) .'"'.($val['mu_new_win']==1?' target="_blank"':'').'>'. escapeHtml($val['mu_title']) .'</a></li>';
	}
?>
				</ul>
			</div>
		</div>
	</nav>

<?php if ($_DATA['id'] == $_CFG['start']) { ?>
	<section id="myCarousel" class="carousel slide" data-ride="carousel">
	  <div class="carousel-inner">
		<?php for ($i=1; $i < 4; $i++) {  ?>
			<div class="item<?php echo $i===1 ? ' active':''?>">
			  <img src="<?php echo _AF_THEME_URL_ ?>img/slide-0<?php echo $i ?>.jpg" alt="">
			  <div class="container">
				<div class="carousel-caption">
				  <?php if(!empty($_THEME['carousel_item_'.$i])) echo $_THEME['carousel_item_'.$i] ?>
				</div>
			  </div>
			</div>
		<?php } ?>
	  </div>
	  <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
	  <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
	</section>

<?php } else if(!empty($_CFG['md_id'])) { ?>

  <div class="header-image <?php echo strtolower($_DATA['module']) ?>">
	<h3><?php echo $_CFG['md_title'] ?></h3>
	<p><?php echo $_CFG['md_description'] ?></p>
  </div>

<?php } else { ?>

  <div class="header-image"></div>

<?php } ?>

<?php if(!empty($submenu['_ACTIVE_']) && count($submenu['_ACTIVE_'])>0) { ?>
	<div class="bs-docs-body row">
		<aside class="col-md-3">
			<div class="list-group">
			  <span class="list-group-item disabled">
				<?php echo $mainmenu['_ACTIVE_']['mu_title'] ?>
			  </span>
			</div>
			<div class="list-group">
	<?php
		foreach ($submenu['_ACTIVE_'] as $key => $val) {
			echo '<a href="'. escapeHtml($val['mu_link']) .'" class="list-group-item'.(empty($val['_ACTIVE_'])?'':' active').'"'.($val['mu_new_win']==1?' target="_blank"':'').'>'. escapeHtml($val['mu_title']) .'</a>';
		}
	?>
			</div>
		</aside>
		<section class="col-md-9">

<?php } else { ?>
	<div class="bs-docs-body">
		<section>
<?php } ?>
			<article>
			<?php echo dispModuleContent()?>
			</article>
		</section>
	</div>

	<footer class="bs-docs-footer">
		<ul class="bs-docs-footer-links">
		<li style="visibility:hidden"></li>
<?php

	if(empty($menus['error'])){
		foreach ($menus['footer'] as $val) {
			echo '<li><a href="'. escapeHtml($val['mu_link']) .'"'.($val['mu_new_win']==1?' target="_blank"':'').' title="'.escapeHtml($val['mu_description']).'">'. escapeHtml($val['mu_title']) .'</a></li>';
		}
	}

?>
		</ul>
		<p><?php if(!empty($_THEME['footer_html'])) echo $_THEME['footer_html']; ?></p>
	</footer>

</div>
