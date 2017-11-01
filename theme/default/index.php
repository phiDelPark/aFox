<?php
	if(!defined('__AFOX__')) exit();

	addJSLang(['error','id','password','login','auto_login','member_signup','member_find']);

	$menus = getSiteMenu();
	$mainmenu = [];
	$submenu = [];

	if(empty($menus['error'])) {
		$url = getUrl();
		foreach ($menus['header'] as $val) {
			$is_active = !empty($val['md_id'])&&$val['md_id']==__MID__?'_ACTIVE_':$val['mu_srl'];
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

<?php if(!empty($_THEME['use_loader'])) { ?>
<div aria-labelledby="afPageLoader">
	<span id="afPageLoader" class="sr-only">
		Please Wait, Loading...
	</span>
</div>
<?php } ?>

<div class="container">

	<header class="bs-docs-header">
		<h1 class="logo-title" role="banner" aria-label="<?php echo escapeHtml($_CFG['title'])?>"><img src="<?php echo empty($_CFG['logo']) ? _AF_THEME_URL_.'img/logo.png' : $_CFG['logo']?>" alt="<?php echo escapeHtml($_CFG['title'])?>" height="50"></h1>
		<div class="right">
			<span role="search" aria-label="Search"><a class="collapsed"  data-toggle="collapse" href="#nav-collapse3" aria-expanded="false" aria-controls="nav-collapse3"><i class="glyphicon glyphicon-search" aria-hidden="true"></i></a></span>
<?php if (!empty($_MEMBER)) {
	$notes = getDBList(_AF_NOTE_TABLE_, ['mb_srl'=>$_MEMBER['mb_srl'],'nt_read_date'=>'0000-00-00 00:00:00'], '', 1, 5);
?>
			<?php if(empty($notes['error']) && $notes['total_count'] > 0){ ?>
			<span>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i> <strong class="caret"></strong></a>
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
									<p class="small text-muted"><i class="glyphicon glyphicon-time" aria-hidden="true"></i> <?php echo date('Y/m/d', strtotime($val['nt_send_date'])) ?></p>
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
				$_icon = _AF_URL_ . (empty($_MEMBER['mb_icon']) ? 'module/board/tpl/user_default.jpg' : 'data/member/' . $_MEMBER['mb_srl'].'/profile_image.png');
			?>
				<a href="#" class="dropdown-toggle login-icon" data-toggle="dropdown"><img src="<?php echo $_icon ?>" alt="<?php echo escapeHtml($_MEMBER['mb_nick'])?>" /></a>
				<ul class="dropdown-menu dropdown-menu-right">
					<?php if(isManager(__MID__)) { ?>
					<li><a href="<?php echo _AF_URL_?>?admin" target="_blank"><i class="glyphicon glyphicon-user" aria-hidden="true"></i> <?php echo $_MEMBER['mb_nick']?></a></li>
					<?php } else { ?>
					<li class="dropdown-header"><i class="glyphicon glyphicon-user" aria-hidden="true"></i> <?php echo $_MEMBER['mb_nick']?></li>
					<?php } ?>
					<li class="divider"></li>
					<li><a href="<?php echo getUrl('','module','member','disp','inbox') ?>"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i> <?php echo getLang('Inbox')?></a></li>
					<li><a href="<?php echo getUrl('','module','member','disp','trash') ?>"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> <?php echo getLang('Recycle_bin')?></a></li>
					<li><a href="<?php echo _AF_URL_ ?>?module=member&disp=signUp"><i class="glyphicon glyphicon-cog" aria-hidden="true"></i> <?php echo getLang('Setup')?></a></li>
					<li class="divider"></li>
					<li><a href="#" data-exec-ajax="member.logOut" data-ajax-param="success_return_url,<?php echo getUrl('')?>"><i class="glyphicon glyphicon-off" aria-hidden="true"></i> <?php echo getLang('Logout')?></a></li>
				</ul>
			</span>
<?php } else { ?>
			<a href="#loginForm"><i class="glyphicon glyphicon-user fs-2x" aria-hidden="true"></i></a>
<?php } ?>
		</div>
	</header>

	<nav class="navbar navbar-inverse" role="navigation" aria-label="Site Navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#af-navbar-collapse-9" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo getUrl('')?>"><i class="glyphicon glyphicon-home" aria-hidden="true"></i></a>
			</div>
			<div class="collapse navbar-collapse" id="af-navbar-collapse-9">
				<ul class="nav navbar-nav right">
<?php
	foreach ($mainmenu as $key => $val) {
		echo '<li'.($key==='_ACTIVE_'?' class="active"':'').'><a href="'. escapeHtml($val['mu_link']) .'"'.($val['mu_new_win']==='1'?' target="_blank"':'').'>'. escapeHtml($val['mu_title']) .'</a></li>';
	}
?>
				</ul>
				<div class="collapse nav navbar-nav nav-collapse slide-down" id="nav-collapse3">
				<form class="navbar-form navbar-right" role="search" action="<?php echo getUrl('') ?>" method="get">
					<div class="form-group">
						<input type="hidden" name="id" value="search">
						<input type="text" name="combine" class="form-control" placeholder="<?php echo getLang('Search_text')?>" required>
					</div>
					<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> <?php echo getLang('Search')?></button>
				</form>
				</div>
			</div>
		</div>
	</nav>

<?php if (__MID__ == $_CFG['start']) { ?>
	<section id="myCarousel" class="carousel slide" data-ride="carousel" role="complementary" aria-label="Site Carousel">
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
<?php } else { ?>
<div class="header-image">
	<?php if(__MODULE__) {
		$md_key = ucfirst(__MODULE__);
	?>
		<h3 role="heading" aria-labelledby="md<?php echo $md_key ?>Title">
			<span id="md<?php echo $md_key ?>Title"><?php echo $_CFG['md_title'] ?></span>
		</h3>
		<p role="description"><?php echo $_CFG['md_description'] ?></p>
	<?php } ?>
</div>
<?php } ?>

<?php
$tmp = 'col-md-9 ';
if(!empty($submenu['_ACTIVE_']) && count($submenu['_ACTIVE_'])>0) { ?>
	<div class="bs-docs-body row">
		<aside class="col-md-3" role="menu" aria-label="Content Menu">
			<div class="list-group">
			  <span class="list-group-item disabled">
				<?php echo $mainmenu['_ACTIVE_']['mu_title'] ?>
			  </span>
			</div>
			<div class="list-group">
	<?php
		foreach ($submenu['_ACTIVE_'] as $key => $val) {
			echo '<a href="'. escapeHtml($val['mu_link']) .'" class="list-group-item'.(empty($val['_ACTIVE_'])?'':' active').'"'.($val['mu_new_win']==='1'?' target="_blank"':'').'>'. escapeHtml($val['mu_title']) .'</a>';
		}
	?>
			</div>
		</aside>
<?php } else {
$tmp = '';
?>
	<div class="bs-docs-body">
<?php } ?>
		<section class="<?php echo $tmp.__MODULE__?>" role="main" aria-label="Site Contents">
			<article>
			<?php if($error = get_error()) { messageBox($error['message'], 3); } ?>
			<?php displayModule()?>
			</article>
		</section>
	</div>

	<footer class="bs-docs-footer" role="contentinfo" aria-label="About Site">
		<ul class="bs-docs-footer-links">
		<li style="visibility:hidden"></li>
<?php

	if(empty($menus['error'])){
		foreach ($menus['footer'] as $val) {
			echo '<li><a href="'. escapeHtml($val['mu_link']) .'"'.($val['mu_new_win']==='1'?' target="_blank"':'').' title="'.escapeHtml($val['mu_description']).'">'. escapeHtml($val['mu_title']) .'</a></li>';
		}
	}

?>
		</ul>
		<p><?php if(!empty($_THEME['footer_html'])) echo $_THEME['footer_html']; ?></p>
	</footer>

</div>
