<!-- Start::app-sidebar -->
<aside class="app-sidebar sticky" id="sidebar">

    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
        <a href="{{ route('user.dashboard') }}" class="header-logo">
            <img src="{{ asset("/backend/images/brand-logos/desktop-white.png") }}" alt="logo">
        </a>
    </div>
    <!-- End::main-sidebar-header -->

    <!-- Start::main-sidebar -->
    <div class="main-sidebar" id="sidebar-scroll">

        <!-- Start::nav -->
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path> </svg>
            </div>
            <ul class="main-menu">
                <!-- Start::slide__category -->
                <li class="slide__category"><span class="category-name">Menu</span></li>
                <!-- End::slide__category -->

                <!-- Start::slide -->
                <li class="slide">
                    <a href="{{ route("user.dashboard") }}" class="side-menu__item">
                        <i class="fe fe-home side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>
                <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide">
                    <a href="{{ route('user.data.show') }}" class="side-menu__item">
                        <i class="fe fe-wifi side-menu__icon"></i>
                        <span class="side-menu__label">Buy Data</span>
                    </a>
                </li>
                <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide">
                    <a href="{{ route('user.airtime.show') }}" class="side-menu__item">
                        <i class="fe fe-phone-call side-menu__icon"></i>
                        <span class="side-menu__label">Buy Airtime</span>
                    </a>
                </li>
                <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="las la-lightbulb side-menu__icon"></i>
                        <span class="side-menu__label">Utilites Payment</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1 mega-menu">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">Utilities Payment</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('user.utility.electric.show') }}" class="side-menu__item">Bill Payment</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('user.utility.cable.show') }}" class="side-menu__item">Cable Subscription</a>
                        </li>
                    </ul>
                </li>
                <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="fe fe-credit-card side-menu__icon"></i>
                        <span class="side-menu__label">Wallet Top-Up</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">Wallet Top-Up</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('user.top-up.monnify.show') }}" class="side-menu__item">Instant Deposit</a>
                        </li>
                    </ul>
                </li>
                <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide">
                    <a href="{{ route("user.pricing.view") }}" class="side-menu__item">
                        <i class="las la-search-dollar side-menu__icon"></i>
                        <span class="side-menu__label">Pricing</span>
                    </a>
                </li>
                <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="fe fe-users side-menu__icon"></i>
                        <span class="side-menu__label">Account</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">Account</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route("user.transactions.general.show") }}" class="side-menu__item">History</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route("user.profile.show") }}" class="side-menu__item">Settings</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route("user.dashboard") }}" class="side-menu__item">Developers</a>
                        </li>
                    </ul>
                </li>
                <!-- End::slide -->
            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path> </svg></div>
        </nav>
        <!-- End::nav -->

    </div>
    <!-- End::main-sidebar -->

</aside>
<!-- End::app-sidebar -->
