<div class="page-main-header">
  <div class="main-header-right row m-0">
    <div class="main-header-left">
    <!-- public/assets/images/logo/mwalimu2.png -->
    <div class="logo-wrapper">
    <a href="<?php echo e(route('home')); ?>">
        <img class="img-fluid" src="<?php echo e(asset('assets/images/logo/mwalimu2.png')); ?>" alt="" style="width: 120px; height: 50px;">
    </a>
</div>
      <div class="dark-logo-wrapper"><a href="<?php echo e(route('home')); ?>"><img class="img-fluid" src="<?php echo e(asset('assets/images/logo/pep.png')); ?>" alt=""></a></div>
      <div class="toggle-sidebar"><i class="status_toggle middle" data-feather="align-center" id="sidebar-toggle">    </i></div>
    </div>
    <div class="left-menu-header col">
      <ul>
        <li>
          <div class="form-inline search-form">
            <div class="search-bg">
            
          </div>
          </div>
          
        </li>
      </ul>
    </div>
    <div class="nav-right col pull-right right-menu p-0">
      <ul class="nav-menus">
        <li><a class="text-dark" href="#!" onclick="javascript:toggleFullScreen()"><i data-feather="maximize"></i></a></li>
        
        <li class="onhover-dropdown p-0">
          <a class="btn btn-primary-light" href="<?php echo e(route('logout')); ?>"><i data-feather="log-out"></i>Log out</a>
        </li>
      </ul>
    </div>
    <div class="d-lg-none mobile-toggle pull-right w-auto"><i data-feather="more-horizontal"></i></div>
  </div>
</div>


 



<?php /**PATH /var/www/html/laravel/resources/views/layouts/admin/partials/header.blade.php ENDPATH**/ ?>