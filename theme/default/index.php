<?php
	if(!defined('__AFOX__')) exit();

	$menus = getSiteMenu();
	$err = empty($menus['error']) ? get_error() : $menus;
?>

<?php if(!empty($err['error'])) { ?>
	<div class="auto-hide" data-timer="5">
		<h3 class="clearfix"><span class="timer-progress pull-left" data-repeat-char="&bull;"></span> <i class="fa fa-<?php echo $err['error']!=0?'warning':'exclamation-circle'?>" aria-hidden="true"></i> <?php echo $err['message']?></h3>
	</div>
<?php } ?>

<div class="container">

	<header class="bs-docs-header">
		<h1 id="logo_title"><img src="<?php echo empty($_CFG['logo']) ? _AF_THEME_URL_.'/img/logo.png' : $_CFG['logo']?>" alt="<?php echo $_CFG['title']?>" height="50"></h1>
		<div class="right">
<?php if (!empty($_MEMBER)) { ?>
			<span>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope" aria-hidden="true"></i> <b class="caret"></b></a>
				<ul class="dropdown-menu message-dropdown dropdown-menu-right">
					<li class="message-preview">
						<a href="#">
							<div class="media">
								<span class="pull-left">
									<img class="media-object" src="http://placehold.it/50x50" alt="">
								</span>
								<div class="media-body">
									<h5 class="media-heading"><strong>이름</strong>
									</h5>
									<p class="small text-muted"><i class="fa fa-clock-o" aria-hidden="true"></i> 날짜</p>
									<p>간략히</p>
								</div>
							</div>
						</a>
					</li>
					<li class="message-footer">
						<a href="#">모든 새 메세지 읽기</a>
					</li>
				</ul>
			</span>
			<span>
				<a href="#" class="dropdown-toggle login-icon" data-toggle="dropdown"><img src="<?php echo _AF_THEME_URL_?>/img/user_default.jpg" alt="<?php echo $_MEMBER['mb_nick']?>" /></a>
				<ul class="dropdown-menu dropdown-menu-right">
					<?php if(isManager($_DATA['id'])) { ?>
					<li><a href="<?php echo _AF_URL_?>?admin"><i class="fa fa-fw fa-user" aria-hidden="true"></i> <?php echo $_MEMBER['mb_nick']?></a></li>
					<?php } else { ?>
					<li class="dropdown-header"><i class="fa fa-fw fa-user" aria-hidden="true"></i> <?php echo $_MEMBER['mb_nick']?></li>
					<?php } ?>
					<li class="divider"></li>
					<li><a href="#"><i class="fa fa-trash" aria-hidden="true"></i> <?php echo getLang('recycle_bin')?></a></li>
					<li><a href="<?php echo _AF_URL_ ?>?module=member&disp=signUp"><i class="fa fa-fw fa-gear" aria-hidden="true"></i> <?php echo getLang('setup')?></a></li>
					<li class="divider"></li>
					<li><a href="#" data-exec-ajax="member.logOut" data-ajax-param="success_return_url,<?php echo escapeHtml(getUrl(''))?>"><i class="fa fa-fw fa-power-off" aria-hidden="true"></i> <?php echo getLang('logout')?></a></li>
				</ul>
			</span>
<?php } else { ?>
			<a href="#" data-toggle="modal" data-target="#loginModal"><i class="fa fa-user fa-2" aria-hidden="true"></i></a>
			<!-- Modal -->
			<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel">
				<div class="modal-dialog">
					<form class="modal-content" method="post" autocomplete="off" data-exec-ajax="member.loginCheck">
					<input type="hidden" name="success_return_url" value="<?php echo getUrl('')?>" />
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
								<a href="<?php echo _AF_URL_ ?>?module=member&disp=findAccount"><?php echo getLang('member_find_account')?></a>
							</div>
							<button type="button" class="btn btn-default" data-dismiss="modal"> <?php echo getLang('close')?></a></button>
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
				<a class="navbar-brand" href="<?php echo getUrl('')?>"><?php echo $_CFG['title']?></a>
			</div>
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-9">
				<ul class="nav navbar-nav right">

<?php
	$submenu = [];
	// getUrlQuery(pg), getUrlQuery(id)
	// $sr_only = '<span class="sr-only">(current)</span>';
	if(empty($menus['error'])){
		$url = getCurrentUrl();
		foreach ($menus['header'] as $val) {
			$mu_parent = (int) $val['mu_parent'];
			if($mu_parent > 0) {
				$submenu[$muroot][] = $val;
			} else {
				$muroot = !empty($val['md_id'])&&$val['md_id']==$_DATA['id']?'_ACTIVE_':$val['mu_srl'];
				if($muroot!='_ACTIVE_' && !empty($val['mu_link']) && strpos($url, $val['mu_link'])!==false) {$muroot='_ACTIVE_';}
				$submenu[$muroot] = [];
				echo '<li'.($muroot=='_ACTIVE_'?' class="active"':'').'><a href="'. escapeHtml($val['mu_link']) .'"'.($val['mu_new_win']==1?' target="_blank"':'').'">'. escapeHtml($val['mu_title']) .'</a></li>';
			}
		}
	}
?>
				</ul>
			</div>
		</div>
	</nav>

<?php if ($_DATA['id'] == $_CFG['start']) { ?>

	<section id="myCarousel" class="carousel slide" data-ride="carousel">
		<!-- Indicators -->
		<ol class="carousel-indicators">
			<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
			<li data-target="#myCarousel" data-slide-to="1"></li>
			<li data-target="#myCarousel" data-slide-to="2"></li>
			<li data-target="#myCarousel" data-slide-to="3"></li>
		</ol>

		<!-- Wrapper for slides -->
		<div class="carousel-inner" role="listbox">
			<div class="item active">
				<img src="http://www.w3schools.com/bootstrap/img_chania.jpg" alt="Chania">
				<div class="carousel-caption">
					<h3>Chania</h3>
					<p>The atmosphere in Chania has a touch of Florence and Venice.</p>
				</div>
			</div>

			<div class="item">
				<img src="http://www.w3schools.com/bootstrap/img_chania2.jpg" alt="Chania">
				<div class="carousel-caption">
					<h3>Chania</h3>
					<p>The atmosphere in Chania has a touch of Florence and Venice.</p>
				</div>
			</div>

			<div class="item">
				<img src="http://www.w3schools.com/bootstrap/img_flower.jpg" alt="Flower">
				<div class="carousel-caption">
					<h3>Flowers</h3>
					<p>Beatiful flowers in Kolymbari, Crete.</p>
				</div>
			</div>

			<div class="item">
				<img src="http://www.w3schools.com/bootstrap/img_flower2.jpg" alt="Flower">
				<div class="carousel-caption">
					<h3>Flowers</h3>
					<p>Beatiful flowers in Kolymbari, Crete.</p>
				</div>
			</div>
		</div>

	  <!-- Left and right controls -->
	  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
		<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
	  </a>
	  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
		<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
	  </a>
	</section>

<?php } else if(!empty($_CFG['module'])) { ?>

  <div class="header-image">
	<h3><?php echo $_CFG['module']['md_title'] ?></h3>
	<p><?php echo $_CFG['module']['md_description'] ?></p>
  </div>

<?php } else { ?>

  <div class="header-image"></div>

<?php } ?>

<?php if(!empty($submenu['_ACTIVE_'])) { ?>
	<div class="row">
		<aside class="col-md-3">
			<div class="list-group">
			  <span class="list-group-item disabled">
				Cras justo odio
			  </span>
			</div>
			<div class="list-group">
	<?php
		foreach ($submenu['_ACTIVE_'] as $val) {
			echo '<a href="'. escapeHtml($val['mu_link']) .'" class="list-group-item"'.($val['mu_new_win']==1?' target="_blank"':'').'">'. escapeHtml($val['mu_title']) .'</a>';
		}
	?>
			</div>
		</aside>
		<section class="col-md-9">

<?php } else { ?>
	<div>
		<section>
<?php } ?>
			<article class="bs-docs-body">

			<?php echo dispModuleContent()?>

			</article>
		</section>
	</div>

	<footer class="bs-docs-footer">
		<ul class="bs-docs-footer-links">
<?php

	if(empty($menus['error'])){
		foreach ($menus['footer'] as $val) {
			echo '<li><a href="'. escapeHtml($val['mu_link']) .'"'.($val['mu_new_win']==1?' target="_blank"':'').' title="'.$val['mu_desc'].'">'. escapeHtml($val['mu_title']) .'</a></li>';
		}
	}

?>
		</ul>
		<p><?php if($_CFG['footer_html']) include $_CFG['footer_html']; ?></p>
	</footer>

</div>
