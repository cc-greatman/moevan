<!-- Start::app-sidebar -->
<aside class="app-sidebar sticky" id="sidebar">

    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header">
        <a href="{{ route('admin.show.dashboard') }}" class="header-logo">
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
                    <a href="{{ route('admin.show.dashboard') }}" class="side-menu__item">
                        <i class="fe fe-home side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>
                <!-- End::slide -->

                @if(auth()->guard('admin')->user()->account_type == 1)
                <!-- Start::slide -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="fe fe-users side-menu__icon"></i>
                        <span class="side-menu__label">Manage Users</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">Manage Users</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route("admin.user.topup") }}" class="side-menu__item">Fund Users</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route("admin.user.debit") }}" class="side-menu__item">Debit Users</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route("admin.user.referrals") }}" class="side-menu__item">User Referrals</a>
                        </li>
                    </ul>
                </li>
                <!-- End::slide -->

                 <!-- Start::slide -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="fe fe-settings side-menu__icon"></i>
                        <span class="side-menu__label">Settings</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">Site Settings</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route("admin.setting.data") }}" class="side-menu__item">Data Status</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route("admin.setting.rolling") }}" class="side-menu__item">Messages</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route("admin.setting.general") }}" class="side-menu__item">General Switch</a>
                        </li>
                    </ul>
                </li>
                <!-- End::slide -->

                <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <i class="fe fe-edit-3 side-menu__icon"></i>
                                <span class="side-menu__label">Price Edit</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide side-menu__label1">
                                    <a href="javascript:void(0);">Price Edit</a>
                                </li>
                                <li class="slide has-sub">
                                    <a href="javascript:void(0);" class="side-menu__item">Customers
                                        <i class="fe fe-chevron-right side-menu__angle"></i></a>
                                    <ul class="slide-menu child2" style="display: block; box-sizing: border-box;">
                                        <li class="slide">
                                            <a href="{{ route("admin.price.data.edit") }}" class="side-menu__item">Data</a>
                                        </li>
                                        <li class="slide">
                                            <a href="{{ route("admin.price.cable.edit") }}" class="side-menu__item">Cable Subscriptions</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="slide has-sub">
                                    <a href="javascript:void(0);" class="side-menu__item">Agents
                                        <i class="fe fe-chevron-right side-menu__angle"></i></a>
                                    <ul class="slide-menu child2" style="display: block; box-sizing: border-box;">
                                        <li class="slide">
                                            <a href="{{ route('admin.price.specific.data.edit') }}" class="side-menu__item">General Data</a>
                                        </li>
                                        <li class="slide">
                                            <a href="{{ route('admin.price.agent.list') }}" class="side-menu__item">Specific Data</a>
                                        </li>
                                        <li class="slide">
                                            <a href="{{ route("admin.price.cable.edit") }}" class="side-menu__item">Cable Subscriptions</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="slide has-sub">
                                    <a href="javascript:void(0);" class="side-menu__item">Resellers
                                        <i class="fe fe-chevron-right side-menu__angle"></i></a>
                                    <ul class="slide-menu child2" style="display: block; box-sizing: border-box;">
                                        <li class="slide">
                                            <a href="{{ route("admin.price.data.edit.reseller") }}" class="side-menu__item">Data</a>
                                        </li>
                                        <li class="slide">
                                            <a href="{{ route("admin.price.cable.edit") }}" class="side-menu__item">Cable Subscriptions</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                </li>
                @endif


                <!-- Start::slide
                <li class="slide">
                    <a href="" class="side-menu__item">
                        <i class="fe fe-wifi side-menu__icon"></i>
                        <span class="side-menu__label">Send Data</span>
                    </a>
                </li>
                 End::slide

                 Start::slide
                <li class="slide">
                    <a href="" class="side-menu__item">
                        <i class="fe fe-phone-call side-menu__icon"></i>
                        <span class="side-menu__label">Send Airtime</span>
                    </a>
                </li>
                End::slide

                Start::slide
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
                            <a href="javascript:void(0);" class="side-menu__item">Bill Payment</a>
                        </li>
                        <li class="slide">
                            <a href="javascript:void(0);" class="side-menu__item">Cable Subscription</a>
                        </li>
                    </ul>
                </li>
                 End::slide -->

                <!-- Start::slide -->
                <li class="slide has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="las la-chart-bar side-menu__icon"></i>
                        <span class="side-menu__label">Transactions</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1 mega-menu">
                        <li class="slide side-menu__label1">
                            <a href="javascript:void(0)">Transactions</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('admin.transactions.wallet.view') }}" class="side-menu__item">Wallet Transactions</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('admin.transactions.manual.transactions') }}" class="side-menu__item">Manual Fundings</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('admin.transactions.airtime.view') }}" class="side-menu__item">Airtime Transactions</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('admin.transactions.data.view') }}" class="side-menu__item">Data Transactions</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('admin.transactions.cable.view') }}" class="side-menu__item">Cable Transactions</a>
                        </li>
                        <li class="slide">
                            <a href="{{ route('admin.transactions.electric.view') }}" class="side-menu__item">Electricity Transactions</a>
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
