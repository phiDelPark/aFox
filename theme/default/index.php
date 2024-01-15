<?php
	if(!defined('__AFOX__')) exit();
  addJSLang(['error']);

	$menus = getSiteMenu();
	$mainmenu = [];
	$submenu = [];
	$muroot = '';
	$submenuactivetitle = '';

	if(empty($menus['error'])){
		$url = getUrl();
		foreach($menus['header'] as $val){
			$is_active = (!empty($val['md_id']) && $val['md_id'] == __MID__) ? '_ACTIVE_' : $val['mu_srl'];
			if($is_active!='_ACTIVE_' && $val['mu_link'] && strpos($url, $val['mu_link'])!==false) $is_active='_ACTIVE_';
			if((int) $val['mu_parent'] > 0){
				if($is_active == '_ACTIVE_'){
					// 활성화시 키값 교체
					if(!empty($submenu[$muroot])){
						$submenu['_ACTIVE_'] = $submenu[$muroot];
						unset($submenu[$muroot]);
					}
					$mainmenu['_ACTIVE_'] = $mainmenu[$muroot];
					unset($mainmenu[$muroot]);
					$muroot = '_ACTIVE_';
					$val['_ACTIVE_'] = 1;
					$submenuactivetitle = $val['mu_title'];
				}
				$submenu[$muroot][] = $val;
			} else {
				$muroot = $is_active;
				$mainmenu[$muroot] = $val;
			}
		}
	}
  $is_submenu = !empty($submenu['_ACTIVE_'])&&count($submenu['_ACTIVE_'])>0;
?>

<div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
  <button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center"
          id="bd-theme"
          type="button"
          aria-expanded="false"
          data-bs-toggle="dropdown"
          aria-label="Toggle theme (auto)">
    <svg class="bi my-1 theme-icon-active"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#circle-half"></use></svg>
    <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
  </button>
  <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
    <li>
      <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
        <svg class="bi me-2 opacity-50 theme-icon"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#sun-fill"></use></svg>
        Light
        <svg class="bi ms-auto d-none"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#check2"></use></svg>
      </button>
    </li>
    <li>
      <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
        <svg class="bi me-2 opacity-50 theme-icon"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#moon-stars-fill"></use></svg>
        Dark
        <svg class="bi ms-auto d-none"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#check2"></use></svg>
      </button>
    </li>
    <li>
      <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
        <svg class="bi me-2 opacity-50 theme-icon"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#circle-half"></use></svg>
        Auto
        <svg class="bi ms-auto d-none"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#check2"></use></svg>
      </button>
    </li>
  </ul>
</div>

<div class="container">
  <header class="border-bottom lh-1 py-2">
    <div class="row flex-nowrap justify-content-between align-items-center">
      <div class="col-4">
        <a class="link-secondary" href="#">Subscribe</a>
      </div>
      <div class="col-4 text-center">
        <h1 class="text-body-emphasis mb-0">Afox</h1>
      </div>
      <div class="col-4 d-flex justify-content-end align-items-center">
        <a class="link-secondary me-1 mt-1" href="#" aria-label="Search"><svg class="bi" aria-hidden="true" style="width:1.4em;height:1.4em"><title>Search</title><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#search"/></svg></a>
<?php if(empty($_MEMBER)){ ?>
        <a class="btn p-0" style="line-height:normal" href="<?php echo getUrl('', 'member', 'signIn')?>" aria-label="SignIn"><svg class="bi bi-lg" aria-hidden="true"><title>Sign In</title><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#person-fill"/></svg></a>
<?php }else{ ?>
        <a class="btn p-0" style="line-height:normal" href="#" aria-label="Member" data-bs-toggle="dropdown" aria-expanded="false"><svg class="bi bi-lg" aria-hidden="true"><title><?php echo $_MEMBER['mb_nick']?></title><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#person-bounding-box"/></svg></a>
        <ul class="dropdown-menu">
					<?php if(isManager(__MID__)) { ?>
					<li><a class="dropdown-item" href="<?php echo _AF_URL_?>?admin" target="_blank"><svg class="bi" aria-hidden="true"><title>Site setup</title><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#person-fill"/></svg> <?php echo $_MEMBER['mb_nick']?></a></li>
					<?php } else { ?>
					<li><a class="dropdown-item" href="<?php echo getUrl('','member','signUp') ?>"><svg class="bi" aria-hidden="true"><title>Member info</title><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#person-fill"/></svg> <?php echo $_MEMBER['mb_nick']?></a></li>
					<?php } ?>
					<li><hr class="dropdown-divider"></li>
					<li><a class="dropdown-item" href="<?php echo getUrl('','member','inbox') ?>"><svg class="bi" aria-hidden="true"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#envelope"/></svg> <?php echo getLang('Inbox')?></a></li>
					<li><a class="dropdown-item" href="<?php echo getUrl('','member','trash') ?>"><svg class="bi" aria-hidden="true"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#trash"/></svg> <?php echo getLang('Recycle_bin')?></a></li>
					<li><hr class="dropdown-divider"></li>
					<li><a class="dropdown-item" href="<?php echo getUrl('', 'module', 'member', 'act', 'signOut')?>"><svg class="bi" aria-hidden="true"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#power"/></svg> <?php echo getLang('Logout')?></a></li>
				</ul>
<?php } ?>
      </div>
    </div>
  </header>

  <nav class="navbar navbar-expand-md py-0 mb-4 border-bottom">
    <div class="container-fluid">
			<a class="navbar-brand" href="<?php echo getUrl('')?>" aria-label="Goto the main page"><svg class="bi"><use xlink:href="<?php echo _AF_THEME_URL_ ?>bi-icons.svg#house-door-fill"/></svg></a>
      <button class="navbar-toggler py-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
<?php
    foreach($mainmenu as $key => $val){
      echo '<li class="nav-item"><a class="nav-link'.($key==='_ACTIVE_'?' active':'').'" href="'. escapeHtml($val['mu_link']) .'"'.($val['mu_new_win']==='1'?' target="_blank"':'').'>'. escapeHtml($val['mu_title']) .'</a></li>';
    }
?>
        </ul>
      </div>
    </div>
  </nav>

  <div id="carouselExampleCaptions" class="carousel slide mb-5">

<?php if(!empty($_DATA['id']) && $_DATA['id'] == 'welcome'){ ?>
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner rounded">
      <div class="carousel-item active">
        <img src="<?php echo _AF_THEME_URL_ ?>img/slide-01.jpg" class="d-block w-100" alt="...">
        <div class="carousel-caption d-none d-md-block">
          <h5 class="fw-bold">First slide label</h5>
          <p>Some representative placeholder content for the first slide.</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="<?php echo _AF_THEME_URL_ ?>/img/slide-02.jpg" class="d-block w-100" alt="...">
        <div class="carousel-caption d-none d-md-block">
          <h5 class="fw-bold">Second slide label</h5>
          <p>Some representative placeholder content for the second slide.</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="<?php echo _AF_THEME_URL_ ?>/img/slide-03.jpg" class="d-block w-100" alt="...">
        <div class="carousel-caption d-none d-md-block">
          <h5 class="fw-bold">Third slide label</h5>
          <p>Some representative placeholder content for the third slide.</p>
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>

<?php }else{ ?>

    <div class="carousel-inner rounded">
      <div class="carousel-item active">
        <img src="<?php echo _AF_THEME_URL_ ?>img/header_<?php echo !empty($_DATA['module'])&&$_DATA['module']=='board'?'board':'page' ?>.jpg" class="d-block w-100" alt="...">
        <div class="carousel-caption d-none d-md-block">
          <h5 class="fw-bold"><?php echo $_CFG['md_title']?></h5>
          <p><?php echo $_CFG['md_description']?></p>
        </div>
      </div>
    </div>

<?php } ?>
  </div>

</div>

<main class="container">

  <div class="row g-5 mb-5">
    <div<?php echo $is_submenu ? ' class="col-md-10"' : ''?>>
      <article aria-label="Site Contents">
<?php
  if($error = get_error()) { messageBox($error['message'], $error['error']); }
  displayModule();
?>
      </article>
    </div>

<?php
	if($is_submenu){
		if(!$submenuactivetitle) $submenuactivetitle=$mainmenu['_ACTIVE_']['mu_title'];
?>

    <div class="col-md-2">
      <div class="position-sticky" style="top: 2rem;">
        <div>
          <h3 class="pb-3 mb-2 fst-italic border-bottom"><?php echo $mainmenu['_ACTIVE_']['mu_title']?></h3>
          <ol class="list-unstyled">
<?php
		foreach ($submenu['_ACTIVE_'] as $key => $val) {
			echo '<li class="p-2"><a href="'. escapeHtml($val['mu_link']) .'" class="icon-link gap-1 icon-link-hover d-block'.(empty($val['_ACTIVE_'])?'':' active').'"'.($val['mu_new_win']==='1'?' target="_blank"':'').'><svg class="bi"><use xlink:href="'._AF_THEME_URL_.'bi-icons.svg#dot"/></svg> '. escapeHtml($val['mu_title']) .'</a></li>';
		}
?>
          </ol>
        </div>
      </div>
    </div>
  </div>

<?php } ?>

</main>

<footer class="pt-2 pb-5 text-center text-body-secondary bg-body-tertiary" aria-label="About Site">
  <ul class="list-unstyled"><li style="visibility:hidden"></li>
<?php
	if(empty($menus['error'])){
		foreach ($menus['footer'] as $val) {
			echo '<li class="d-inline mx-2"><a href="'. escapeHtml($val['mu_link']) .'"'.($val['mu_new_win']==='1'?' target="_blank"':'').' title="'.escapeHtml($val['mu_description']).'">'. escapeHtml($val['mu_title']) .'</a></li>';
		}
	}
?>
		</ul>
		<p class="mb-0"><?php if(!empty($_THEME['footer_html'])) echo $_THEME['footer_html']; ?></p>
</footer>