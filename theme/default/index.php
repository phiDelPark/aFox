<?php if(!defined('__AFOX__')) exit();
addJSLang(['alert','confirm']);
$menus = getSiteMenu();
?>
<ul class="bd-circles"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
<?php if(!empty($_THEME['use_loader'])) { ?>
<div id="loading_page" aria-label="Loading..."><noscript><style>#loading_page{display:none}</style></noscript></div>
<?php } ?>
<div class="mode-toggle" onclick="this.classList.toggle('open')">
  <div class="button" data-bs-theme-value="light"></div>
  <div class="button" onclick="location.hash='locationMap'"></div>
  <div class="button" onclick="location.href='#'"></div>
</div>
<header class="container">
	<div class="border-bottom lh-1 p-1">
		<div class="row flex-nowrap justify-content-between align-items-end">
			<div class="col-4 mb-1">
				<a class="link-secondary" href="#">Subscribe</a>
			</div>
			<div class="col-4 text-center">
				<h1 class="header-logo text-body-emphasis mb-0"><?php echo $_CFG['title']?></h1>
			</div>
			<div class="col-4 d-flex justify-content-end align-items-end">
				<label class="link-secondary icon-link-hover me-2" style="cursor:pointer" href="#" for="searchExForm" aria-label="Search"><svg class="bi m-1" aria-hidden="true"><title>Search</title><use href="./theme/default/bi-icons.svg#search"/></svg></label>
<?php if(empty($_MEMBER)){ ?>
				<a class="btn p-0" style="line-height:normal" href="<?php echo getUrl('', 'member', 'signIn')?>" aria-label="SignIn"><svg class="bi bi-lg" aria-hidden="true"><title>Sign In</title><use href="./theme/default/bi-icons.svg#person-fill"/></svg></a>
<?php }else{ ?>
				<a class="btn p-0" style="line-height:normal" href="#" aria-label="Member" data-bs-toggle="dropdown" aria-expanded="false"><svg class="bi bi-lg" aria-hidden="true"><title><?php echo $_MEMBER['mb_nick']?></title><use href="./theme/default/bi-icons.svg#person-bounding-box"/></svg></a>
				<ul class="dropdown-menu">
					<?php if(isManager(_MID_)) { ?>
					<li><a class="dropdown-item" href="<?php echo _AF_URL_?>?admin" target="_blank"><svg class="bi" aria-hidden="true"><title>Site setup</title><use href="./theme/default/bi-icons.svg#person-fill"/></svg> <?php echo $_MEMBER['mb_nick']?></a></li>
					<?php } else { ?>
					<li><a class="dropdown-item" href="<?php echo getUrl('','member','signUp') ?>"><svg class="bi" aria-hidden="true"><title>Member info</title><use href="./theme/default/bi-icons.svg#person-fill"/></svg> <?php echo $_MEMBER['mb_nick']?></a></li>
					<?php } ?>
					<li><hr class="dropdown-divider"></li>
					<li><a class="dropdown-item" href="<?php echo getUrl('','member','inbox') ?>"><svg class="bi" aria-hidden="true"><use href="./theme/default/bi-icons.svg#envelope"/></svg> <?php echo getLang('Inbox')?></a></li>
					<li><a class="dropdown-item" href="<?php echo getUrl('','member','trash') ?>"><svg class="bi" aria-hidden="true"><use href="./theme/default/bi-icons.svg#trash"/></svg> <?php echo getLang('trash_bin')?></a></li>
					<li><hr class="dropdown-divider"></li>
					<li><a class="dropdown-item" href="<?php echo getUrl('', 'module', 'member', 'act', 'signOut')?>"><svg class="bi" aria-hidden="true"><use href="./theme/default/bi-icons.svg#power"/></svg> <?php echo getLang('Logout')?></a></li>
				</ul>
<?php } ?>
			</div>
		</div>
		<div style="position:relative">
		<input class="d-none" type="checkbox" id="searchExForm">
		<form class="<?php echo @$_GET['search']?' d-block':''?>" method="get">
        <input type="hidden" name="module" value="searchex">
		<div class="input-group input-group-sm float-end">
		<label class="input-group-text"<?php echo @$_GET['search']?' onclick="location.replace(\''.getUrl('','id',(empty($_GET['return'])||_MODULE_!='searchex'?_MID_:$_GET['return'])).'\')"':''?>>
        	<svg class="bi"><use href="./theme/default/bi-icons.svg#<?php echo @$_GET['search']?'x-lg':'search'?>"/></svg>
        </label>
		<?php if(_MODULE_=='board') { ?>
        <select class="form-control" style="max-width:50px" name="id">
          <option value="">ALL</option>
          <option value="<?php echo _MID_ ?>" selected>MID</option>
        </select>
        <?php } ?>
			<input type="text" name="search" id="searchEX" value="<?php echo @$_GET['search']?$_GET['search']:''?>" class="form-control" oninvalid="this.setCustomValidity('<?php echo getLang('search_help') ?>')" oninput="this.setCustomValidity('')" required>
			<button class="btn btn-outline-secondary" type="submit"><?php echo getLang('search') ?></button>
		</div>
		</form>
		</div>
	</div>
	<nav class="navbar navbar-expand-md border-bottom mb-4 p-0">
		<div class="container-fluid p-0">
			<a class="navbar-brand ms-2" href="<?php echo getUrl('')?>" aria-label="Goto the main page"><svg class="bi" style="vertical-align:-.2em;width:1.25em;height:1.25em"><use href="./theme/default/bi-icons.svg#house-door-fill"/></svg></a>
			<button class="navbar-toggler py-0 me-1" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="navbar-nav navbar-collapse font-gugi fs-5 collapse" id="navbarNav">
<?php
		$active_menu = -1;
		foreach($menus['header'] as $key => $val){
			if(!$val['mu_parent']){
				if($active_menu < 0 && $val['active']) $active_menu = $key;
				echo '<a class="nav-link'.($val['active']?' active':'').'" href="'. escapeHTML($val['mu_link']) .'"'.($val['mu_new_win']==='1'?' target="_blank"':'').'>'. escapeHTML($val['mu_title']) .'</a>';
			}
		}
		$sub_menus = $active_menu>-1&&!empty($menus['header'][$active_menu+1])&&$menus['header'][$active_menu+1]['mu_parent'];
?>
			</div>
		</div>
	</nav>
	<div id="carouselExampleCaptions" class="carousel slide mb-5">
<?php if(_MID_ == 'welcome'){ ?>
		<div class="carousel-indicators">
			<button data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
			<button data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
			<button data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
		</div>
		<div class="carousel-inner rounded">
			<div class="carousel-item active">
				<img src="./theme/default/img/slide-01.jpg" class="d-block w-100" alt="...">
				<div class="carousel-caption d-none d-md-block">
					<?php echo $_THEME['carousel_item_1'] ?>
				</div>
			</div>
			<div class="carousel-item">
				<img src="./theme/default/img/slide-02.jpg" class="d-block w-100" alt="...">
				<div class="carousel-caption d-none d-md-block">
					<?php echo $_THEME['carousel_item_2'] ?>
				</div>
			</div>
			<div class="carousel-item">
				<img src="./theme/default/img/slide-03.jpg" class="d-block w-100" alt="...">
				<div class="carousel-caption d-none d-md-block">
					<?php echo $_THEME['carousel_item_3'] ?>
				</div>
			</div>
		</div>
		<button class="carousel-control-prev" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="visually-hidden">Previous</span>
		</button>
		<button class="carousel-control-next" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="visually-hidden">Next</span>
		</button>
<?php }else{ ?>
		<div class="carousel-inner rounded">
			<div class="carousel-item active">
				<img src="./theme/default/img/header_<?php echo _MODULE_=='board'?'board':'page' ?>.jpg" class="d-block w-100" alt="...">
				<div class="carousel-caption d-none d-md-block">
					<h2>&nbsp;</h2>
					<h5><?php echo escapeHTML(@$_CFG['md_about'])?></h5>
				</div>
			</div>
		</div>
<?php } ?>
	</div>
</header>
<main class="container">
	<div class="row g-5 mb-4">
	<article class="<?php echo $sub_menus ? 'col-lg-9 order-lg-1 ' : ''?>mt-4" aria-label="Site Contents">
<?php
	if($error = get_error()) { messageBox($error['message'], $error['error']); }
	displayModule();
?>
	</article>
<?php if($sub_menus){ ?>
	<aside class="col-lg-3 mt-4">
		<div class="position-sticky" style="top:2rem">
			<h2 class="pb-2"><?php echo empty($menus['header'][$active_menu]['mu_title'])?'Categories':$menus['header'][$active_menu]['mu_title']?></h2>
			<ol class="list-unstyled">
	<?php for ($i=$active_menu+1,$n=count($menus['header']); $i < $n; $i++) { $val = $menus['header'][$i]; if(!$val['mu_parent']) break;
		echo '<li><a href="'. escapeHTML($val['mu_link']) .'" class="d-flex flex-column flex-lg-row gap-3 py-3 link-body-emphasis text-decoration-none border-top'.($val['active']?' active':'').'"'.($val['mu_new_win']==='1'?' target="_blank"':'').'>';
	?>
					<svg class="bd-placeholder-img" width="100%" height="41" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="gray"></rect></svg>
					<div class="col-lg-8">
						<h6 class="mb-0"><?php echo escapeHTML($val['mu_title'])?></h6>
						<small class="text-body-secondary"><?php echo escapeHTML($val['mu_about'])?></small>
					</div>
				</a></li>
	<?php } ?>
			</ol>
			<ol id="quickLink" class="list-unstyled">
				<li class="d-none"><a class="d-block icon-link-hover text-truncate" href="#"><svg class="bi me-1"><use href="./theme/default/bi-icons.svg#hash"/></svg>Scroll To Top</a></li>
			</ol>
	</aside>
<?php } ?>
	</div>
</main>
<footer id="locationMap" class="p-4 text-center text-body-secondary bg-body-tertiary" aria-label="About Site">
	<ul class="list-unstyled"><li style="visibility:hidden"></li>
<?php
	if(!empty($menus['footer'])){
		foreach ($menus['footer'] as $val) {
			echo '<li class="d-inline mx-2"><a href="'. escapeHTML($val['mu_link']) .'"'.($val['mu_new_win']==='1'?' target="_blank"':'').' title="'.escapeHTML($val['mu_about']).'">'. escapeHTML($val['mu_title']) .'</a></li>';
		}
	}
?>
		</ul>
		<p class="mb-0"><?php if(!empty($_THEME['footer_html'])) echo $_THEME['footer_html']; ?></p>
</footer>