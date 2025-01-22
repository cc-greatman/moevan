
<body class="overflow-x-hidden">
    <div class="flex justify-between md:px-28 px-8 py-9 bg-blue-990">
      <div class="w-32 lg:w-44">
          <a href="{{ route('home') }}"><img src="{{ asset("../frontend/img/Logo2.png") }}" alt="" /></a>
      </div>
      <div>
        <ul
          class="menus flex space-x-6 text-white text-xl font-medium font-sans leading-8"
        >
        <li></li>
          <li><a href="{{ route('home') }}">HOME</a></li>
          @if(Route::is('user.home'))
               <li><a href="#" class="scroll-link" data-id="about">ABOUT US</a></li>
               <li><a href="#" class="scroll-link" data-id="services">SERVICES</a></li>
          @elseif(!Route::is('user.home'))
                <li><a href="{{ route('home') }}#about">ABOUT US</a></li>
                <li><a href="{{ route('home') }}#services">SERVICES</a></li>
          @endif
          @if (Auth::guard('web')->user())
                <li><a href="{{ route('user.dashboard') }}">DASHBOARD</a></li>
                <li><a href="{{ route('user.logout') }}">LOGOUT</a></li>
          @else
                <li><a href="{{ route('user.register.show') }}">REGISTER</a></li>
                <li><a href="{{ route('user.login.show') }}">SIGN IN</a></li>
          @endif

        </ul>
      </div>
      <div class="bars hidden"><i class="fa fa-bars"></i></div>
    </div>
