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

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="bi-house-door-fill" viewBox="0 0 16 16">
    <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/>
  </symbol>
  <symbol id="bi-check2" viewBox="0 0 16 16">
    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
  </symbol>
  <symbol id="bi-circle-half" viewBox="0 0 16 16">
    <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
  </symbol>
  <symbol id="bi-moon-stars-fill" viewBox="0 0 16 16">
    <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
    <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
  </symbol>
  <symbol id="bi-sun-fill" viewBox="0 0 16 16">
    <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
  </symbol>
  <symbol id="bi-cart" viewBox="0 0 16 16">
    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
  <symbol id="bi-aperture" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="10"/>
    <path d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83m13.79-4l-5.74 9.94"/>
  </symbol>
  <symbol id="bi-person-bounding-box" viewBox="0 0 16 16">
    <path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5"/>
    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
  </symbol>
  <symbol id="bi-person-fill" viewBox="0 0 16 16">
    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
  </symbol>
  <symbol id="bi-search" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
    <circle cx="10.5" cy="10.5" r="7.5"/>
    <path d="M21 21l-5.2-5.2"/>
  </symbol>
  <symbol id="bi-envelope" viewBox="0 0 16 16">
    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
  </symbol>
  <symbol id="bi-power" viewBox="0 0 16 16">
    <path d="M7.5 1v7h1V1z"/>
    <path d="M3 8.812a5 5 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812"/>
  </symbol>
  <symbol id="bi-trash" viewBox="0 0 16 16">
    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
  </symbol>
</svg>

<div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
  <button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center"
          id="bd-theme"
          type="button"
          aria-expanded="false"
          data-bs-toggle="dropdown"
          aria-label="Toggle theme (auto)">
    <svg class="bi my-1 theme-icon-active" width="1em" height="1em"><use href="#bi-circle-half"></use></svg>
    <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
  </button>
  <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
    <li>
      <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
        <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#bi-sun-fill"></use></svg>
        Light
        <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#bi-check2"></use></svg>
      </button>
    </li>
    <li>
      <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
        <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#bi-moon-stars-fill"></use></svg>
        Dark
        <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#bi-check2"></use></svg>
      </button>
    </li>
    <li>
      <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
        <svg class="bi me-2 opacity-50 theme-icon"><use href="#bi-circle-half"></use></svg>
        Auto
        <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#bi-check2"></use></svg>
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
        <a class="link-secondary me-1" href="#" aria-label="Search"><svg class="bi" aria-hidden="true" width="1.2em" height="1.2em"><title>Search</title><use xlink:href="#bi-search"/></svg></a>
<?php if(empty($_MEMBER)){ ?>
        <a class="btn p-0" href="<?php echo getUrl('', 'member', 'signIn')?>" aria-label="SignIn"><svg class="bi" aria-hidden="true" width="1.6em" height="1.6em"><title>Sign In</title><use xlink:href="#bi-person-fill"/></svg></a>
<?php }else{ ?>
        <a class="btn p-0" href="#" aria-label="Member" data-bs-toggle="dropdown" aria-expanded="false"><svg class="bi" aria-hidden="true" width="1.6em" height="1.6em"><title><?php echo $_MEMBER['mb_nick']?></title><use xlink:href="#bi-person-bounding-box"/></svg></a>
        <ul class="dropdown-menu">
					<?php if(isManager(__MID__)) { ?>
					<li><a class="dropdown-item" href="<?php echo _AF_URL_?>?admin" target="_blank"><svg class="bi" aria-hidden="true"><title>Site setup</title><use xlink:href="#bi-person-fill"/></svg> <?php echo $_MEMBER['mb_nick']?></a></li>
					<?php } else { ?>
					<li><a class="dropdown-item" href="<?php echo getUrl('','member','signUp') ?>"><svg class="bi" aria-hidden="true"><title>Member info</title><use xlink:href="#bi-person-fill"/></svg> <?php echo $_MEMBER['mb_nick']?></a></li>
					<?php } ?>
					<li><hr class="dropdown-divider"></li>
					<li><a class="dropdown-item" href="<?php echo getUrl('','member','inbox') ?>"><svg class="bi" aria-hidden="true"><use xlink:href="#bi-envelope"/></svg> <?php echo getLang('Inbox')?></a></li>
					<li><a class="dropdown-item" href="<?php echo getUrl('','member','trash') ?>"><svg class="bi" aria-hidden="true"><use xlink:href="#bi-trash"/></svg> <?php echo getLang('Recycle_bin')?></a></li>
					<li><hr class="dropdown-divider"></li>
					<li><a class="dropdown-item" href="<?php echo getUrl('', 'module', 'member', 'act', 'signOut')?>"><svg class="bi" aria-hidden="true"><use xlink:href="#bi-power"/></svg> <?php echo getLang('Logout')?></a></li>
				</ul>
<?php } ?>
      </div>
    </div>
  </header>

  <nav class="navbar navbar-expand-md py-0 mb-4 border-bottom">
    <div class="container-fluid">
			<a class="navbar-brand" href="<?php echo getUrl('')?>" aria-label="Goto the main page"><svg class="bi" aria-hidden="true"><use xlink:href="#bi-house-door-fill"/></svg></a>
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
</div>

<main class="container">

  <div class="row mb-2">
    <div class="col-md-6">
      <div class="row g-0 border rounded overflow-hidden mb-4 shadow-sm position-relative">
        <div class="col p-4 position-static">
          <strong class="d-inline-block mb-2 text-primary-emphasis">World</strong>
          <h3 class="mb-0">Featured post</h3>
          <div class="mb-1 text-body-secondary">Nov 12</div>
          <p class="card-text mb-auto">This is a wider card with supporting text below as a natural lead-in to additional content.</p>
          <a href="#" class="icon-link gap-1 icon-link-hover stretched-link">
            Continue reading
            <svg class="bi"><use xlink:href="#bi-aperture"/></svg>
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="row g-0 border rounded overflow-hidden mb-4 shadow-sm position-relative">
        <div class="col p-4 position-static">
          <strong class="d-inline-block mb-2 text-success-emphasis">Design</strong>
          <h3 class="mb-0">Post title</h3>
          <div class="mb-1 text-body-secondary">Nov 11</div>
          <p class="mb-auto">This is a wider card with supporting text below as a natural lead-in to additional content.</p>
          <a href="#" class="icon-link gap-1 icon-link-hover stretched-link">
            Continue reading
            <svg class="bi"><use xlink:href="#bi-cart"/></svg>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-5 mb-5">
    <div<?php echo $is_submenu ? ' class="col-md-8"' : ''?>>
      <article osaria-label="Site Contents">
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

    <div class="col-md-4">
      <div class="position-sticky" style="top: 2rem;">
        <div class="p-4 mb-3 bg-body-tertiary rounded">
          <h4 class="fst-italic">About</h4>
          <p class="mb-0">Customize this section to tell your visitors a little bit about your publication, writers, content, or something else entirely. Totally up to you.</p>
        </div>

        <div>
          <h4 class="fst-italic">Recent posts</h4>
          <ul class="list-unstyled">
            <li>
              <a class="d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center py-3 link-body-emphasis text-decoration-none border-top" href="#">
                <svg class="bd-placeholder-img" width="100%" height="96" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="#777"/></svg>
                <div class="col-lg-8">
                  <h6 class="mb-0">Example blog post title</h6>
                  <small class="text-body-secondary">January 15, 2023</small>
                </div>
              </a>
            </li>
            <li>
              <a class="d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center py-3 link-body-emphasis text-decoration-none border-top" href="#">
                <svg class="bd-placeholder-img" width="100%" height="96" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="#777"/></svg>
                <div class="col-lg-8">
                  <h6 class="mb-0">This is another blog post title</h6>
                  <small class="text-body-secondary">January 14, 2023</small>
                </div>
              </a>
            </li>
            <li>
              <a class="d-flex flex-column flex-lg-row gap-3 align-items-start align-items-lg-center py-3 link-body-emphasis text-decoration-none border-top" href="#">
                <svg class="bd-placeholder-img" width="100%" height="96" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="#777"/></svg>
                <div class="col-lg-8">
                  <h6 class="mb-0">Longer blog post title: This one has multiple lines!</h6>
                  <small class="text-body-secondary">January 13, 2023</small>
                </div>
              </a>
            </li>
          </ul>
        </div>

        <div class="p-4">
          <h4 class="fst-italic">Archives</h4>
          <ol class="list-unstyled mb-0">
            <li><a href="#">March 2021</a></li>
          </ol>
        </div>

        <div class="p-4">
          <h4 class="fst-italic">Elsewhere</h4>
          <ol class="list-unstyled">
            <li><a href="#">GitHub</a></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

<?php } ?>

</main>

<footer class="pt-2 pb-5 text-center text-body-secondary bg-body-tertiary" aria-label="About Site">
  <ul><li style="visibility:hidden"></li>
<?php
	if(empty($menus['error'])){
		foreach ($menus['footer'] as $val) {
			echo '<li><a href="'. escapeHtml($val['mu_link']) .'"'.($val['mu_new_win']==='1'?' target="_blank"':'').' title="'.escapeHtml($val['mu_description']).'">'. escapeHtml($val['mu_title']) .'</a></li>';
		}
	}
?>
		</ul>
		<p class="mb-0"><?php if(!empty($_THEME['footer_html'])) echo $_THEME['footer_html']; ?></p>
</footer>
<script src="<?php echo _AF_THEME_URL_ ?>colormodes.min.js"></script>