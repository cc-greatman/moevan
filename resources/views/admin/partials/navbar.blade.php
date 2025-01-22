<div class="page">
    <!-- app-header -->
    <header class="app-header">

       <!-- Start::main-header-container -->
       <div class="main-header-container container-fluid">

           <!-- Start::header-content-left -->
           <div class="header-content-left">

               <!-- Start::header-element -->
               <div class="header-element">
                   <div class="horizontal-logo">
                       <a href="index.html" class="header-logo">
                           <img src="{{ asset("../backend/images/brand-logos/desktop-dark.png") }}" alt="logo" class="desktop-dark">
                       </a>
                   </div>
               </div>
               <!-- End::header-element -->

               <!-- Start::header-element -->
               <div class="header-element">
                   <!-- Start::header-link -->
                   <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a>
                   <!-- End::header-link -->
               </div>
               <!-- End::header-element -->

           </div>
           <!-- End::header-content-left -->

           <!-- Start::header-content-right -->
           <div class="header-content-right">

               <!-- Start::header-element -->
               <div class="header-element header-theme-mode">
                   <!-- Start::header-link|layout-setting -->
                   <a aria-label="anchor" href="javascript:void(0);" class="header-link layout-setting">
                       <span class="light-layout">
                           <!-- Start::header-link-icon -->
                       <i class="fe fe-moon header-link-icon"></i>
                           <!-- End::header-link-icon -->
                       </span>
                       <span class="dark-layout">
                           <!-- Start::header-link-icon -->
                       <i class="fe fe-sun header-link-icon"></i>
                           <!-- End::header-link-icon -->
                       </span>
                   </a>
                   <!-- End::header-link|layout-setting -->
               </div>
               <!-- End::header-element -->

               <!-- Start::header-element -->
               <div class="header-element main-profile-user">
                   <!-- Start::header-link|dropdown-toggle -->
                   <a href="#" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                       <div class="d-flex align-items-center">
                           <div class="me-xxl-2 me-0">
                               <img src="{{ asset("/backend/images/faces/9.jpg") }}" alt="img" width="32" height="32" class="rounded-circle">
                           </div>
                           <div class="d-xxl-block d-none my-auto">
                               <h6 class="fw-semibold mb-0 lh-1 fs-14">{{ Auth::guard('admin')->user()->username }}</h6>
                               <span class="op-7 fw-normal d-block fs-11 text-muted"></span>
                           </div>
                       </div>
                   </a>
                   <!-- End::header-link|dropdown-toggle -->
                   <ul class="main-header-dropdown dropdown-menu pt-0 header-profile-dropdown dropdown-menu-end" aria-labelledby="mainHeaderProfile">
                       <li class="drop-heading d-xxl-none d-block">
                            <div class="text-center">
                               <h5 class="text-dark mb-0 fs-14 fw-semibold">{{ Auth::guard('admin')->user()->username }}</h5>
                           </div>
                       </li>
                       <li class="dropdown-item"><a class="d-flex w-100" href="{{ route('user.profile.show') }}"><i class="fe fe-user fs-18 me-2 text-primary"></i>Profile</a></li>
                       <li class="dropdown-item"><a class="d-flex w-100" href="{{ route('admin.logout.perform') }}"><i class="fe fe-info fs-18 me-2 text-primary"></i>Log Out</a></li>
                   </ul>
               </div>
               <!-- End::header-element -->
           </div>
           <!-- End::header-content-right -->

       </div>
       <!-- End::main-header-container -->

   </header>
   <!-- /app-header -->
