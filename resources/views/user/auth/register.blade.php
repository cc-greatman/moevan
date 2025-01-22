@include('frontend.partials.head')

@include('frontend.partials.navbar')

    <div class="py-16">
      <div class="flex bg-white rounded-lg shadow-lg overflow-hidden mx-4 sm:mx-auto max-w-sm lg:max-w-5xl">
          <div class="hidden lg:block lg:w-1/2 bg-cover"
              style="background-image:url('../frontend/img/emmanuel-ikwuegbu-81fRHbVliQI-unsplash\ 1.png')">
          </div>
          <div class="bg-[#E1E8FF] w-full p-6 sm:p-14 lg:w-1/2">
              <div class="flex flex-row flex-wrap gap-2">
                <h2 class="text-2xl sm:text-3xl font-bold text-[#001D82] text-left">Hey, We are glad you
                chose</h2>
                <img src="{{ asset("../frontend/img/logo/moeblue1.png") }}" alt="" class="rounded w-44 h-10" />
              </div>
              <div class="mt-10 flex items-center justify-between">
                  <span class="border-gray-500 border-b w-1/5 lg:w-1/4"></span>
                  <a href="#" class="text-xs text-[#000E3E] font-bold text-center text-gray-500 uppercase">Let's Get Started</a>
                  <span class="border-gray-500 border-b w-1/5 lg:w-1/4"></span>
              </div>
              <div class="uppercase grid grid-cols-2 gap-4">
                <a href="{{ route("user.login.google") }}" class="bg-white flex items-center justify-center mt-4 text-white rounded-lg shadow-md hover:bg-blue-300">
                  <div class="px-3 py-3">
                      <svg class="h-6 w-6" viewBox="0 0 40 40">
                          <path
                              d="M36.3425 16.7358H35V16.6667H20V23.3333H29.4192C28.045 27.2142 24.3525 30 20 30C14.4775 30 10 25.5225 10 20C10 14.4775 14.4775 9.99999 20 9.99999C22.5492 9.99999 24.8683 10.9617 26.6342 12.5325L31.3483 7.81833C28.3717 5.04416 24.39 3.33333 20 3.33333C10.7958 3.33333 3.33335 10.7958 3.33335 20C3.33335 29.2042 10.7958 36.6667 20 36.6667C29.2042 36.6667 36.6667 29.2042 36.6667 20C36.6667 18.8825 36.5517 17.7917 36.3425 16.7358Z"
                              fill="#FFC107" />
                          <path
                              d="M5.25497 12.2425L10.7308 16.2583C12.2125 12.59 15.8008 9.99999 20 9.99999C22.5491 9.99999 24.8683 10.9617 26.6341 12.5325L31.3483 7.81833C28.3716 5.04416 24.39 3.33333 20 3.33333C13.5983 3.33333 8.04663 6.94749 5.25497 12.2425Z"
                              fill="#FF3D00" />
                          <path
                              d="M20 36.6667C24.305 36.6667 28.2167 35.0192 31.1742 32.34L26.0159 27.975C24.3425 29.2425 22.2625 30 20 30C15.665 30 11.9842 27.2359 10.5975 23.3784L5.16254 27.5659C7.92087 32.9634 13.5225 36.6667 20 36.6667Z"
                              fill="#4CAF50" />
                          <path
                              d="M36.3425 16.7358H35V16.6667H20V23.3333H29.4192C28.7592 25.1975 27.56 26.805 26.0133 27.9758C26.0142 27.975 26.015 27.975 26.0158 27.9742L31.1742 32.3392C30.8092 32.6708 36.6667 28.3333 36.6667 20C36.6667 18.8825 36.5517 17.7917 36.3425 16.7358Z"
                              fill="#1976D2" />
                      </svg>
                  </div>
                  <h1 class="px-3 py-3 w-5/6 text-left hover:text-white text-gray-600 font-bold">Google</h1>
                </a>
                <a href="{{ route("user.login.facebook") }}" class="bg-white flex items-center justify-center mt-4 text-white rounded-lg shadow-md hover:bg-blue-300">
                  <div class="px-3 py-3">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 48 48">
                      <path fill="#039be5" d="M24 5A19 19 0 1 0 24 43A19 19 0 1 0 24 5Z"></path><path fill="#fff" d="M26.572,29.036h4.917l0.772-4.995h-5.69v-2.73c0-2.075,0.678-3.915,2.619-3.915h3.119v-4.359c-0.548-0.074-1.707-0.236-3.897-0.236c-4.573,0-7.254,2.415-7.254,7.917v3.323h-4.701v4.995h4.701v13.729C22.089,42.905,23.032,43,24,43c0.875,0,1.729-0.08,2.572-0.194V29.036z"></path>
                      </svg>
                  </div>
                  <h1 class="px-3 py-3 w-5/6 text-left hover:text-white text-gray-600 font-bold">FaceBook</h1>
                </a>
              </div>
              <form id="bitForm" method="post" action="{{ route('user.register.perform') }}">
                  @csrf
                  <div class="grid grid-cols-2 gap-4 mt-8">
                    <div class="mt-4">
                      <label class="block text-gray-700 text-sm font-bold mb-2">First Name</label>
                      <input class=" font-semibold bg-white text-gray-700 focus:outline-blue-950 focus:shadow-outline border border-gray-300 rounded py-2 px-4 block w-full appearance-none" name="first_name" type="text" placeholder="Given Name" />
                      @if ($errors->has('first_name'))
                        <span class="text-red-700 font-semibold text-lg">{{ $errors->first('first_name') }}</span>
                      @endif
                    </div>
                    <div class="mt-4">
                      <label class="block text-gray-700 text-sm font-bold mb-2">Surname</label>
                      <input class=" font-semibold bg-white text-gray-700 focus:outline-blue-950 focus:shadow-outline border border-gray-300 rounded py-2 px-4 block w-full appearance-none" name="last_name" type="text" placeholder="Surname" />
                      @if ($errors->has('last_name'))
                        <span class="text-red-700 font-semibold text-lg">{{ $errors->first('last_name') }}</span>
                      @endif
                    </div>
                  </div>
                  <div class="mt-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input class=" font-semibold bg-white text-gray-700 focus:outline-blue-950 focus:shadow-outline border border-gray-300 rounded py-2 px-4 block w-full appearance-none" name="username" value="{{ old('username') }}" type="text" placeholder="Username" />
                    @if ($errors->has('username'))
                        <span class="text-red-700 font-semibold text-lg">{{ $errors->first('username') }}</span>
                    @endif
                  </div>
                  <div class="mt-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Mobile Phone</label>
                    <input class="font-semibold bg-white text-gray-700 focus:outline-blue-950 focus:shadow-outline border border-gray-300 rounded py-2 px-4 block w-full appearance-none" type="text" maxlength="11" name="phone" value="{{ old('phone') }}" placeholder="08167768998" required/>
                    @if ($errors->has('phone'))
                        <span class="text-red-700 font-semibold text-lg">{{ $errors->first('phone') }}</span>
                    @endif
                  </div>
                  <div class="mt-4">
                      <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                      <input class="font-semibold bg-white text-gray-700 focus:outline-blue-950 focus:shadow-outline border border-gray-300 rounded py-2 px-4 block w-full appearance-none" name="email" value="{{ old('email') }}" type="email" placeholder="mail@example.com" />
                      @if ($errors->has('email'))
                        <span class="text-red-700 font-semibold text-lg">{{ $errors->first('email') }}</span>
                    @endif
                  </div>
                  <div class="mt-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Referral Code (Optional)</label>
                    <input class="font-semibold bg-white text-gray-700 focus:outline-blue-950 focus:shadow-outline border border-gray-300 rounded py-2 px-4 block w-full appearance-none" name="referral_code" value="{{ session('referrer') }}" type="text" placeholder="moevandev" />
                    @if ($errors->has('referral_code'))
                        <span class="text-red-700 font-semibold text-lg">{{ $errors->first('referral_code') }}</span>
                    @endif
                  </div>
                  <div class="mt-4">
                      <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                      <div class="flex flex-row">
                        <input class="bg-white text-gray-700 focus:outline-blue-950 focus:shadow-outline border border-gray-300 rounded rounded-r-none py-2 px-4 block w-full appearance-none" id="password" name="password" type="password" placeholder="Enter a password" />
                        <button aria-label="button" class="w-9 h-11 bg-blue-300 rounded rounded-l-none" onclick="createpassword('password',this)" type="button" id="button-addon21"><i class="ri-eye-off-line text-white align-middle"></i></button>
                      </div>
                      @if ($errors->has('password'))
                        <span class="text-red-700 font-semibold text-lg">{{ $errors->first('password') }}</span>
                      @endif
                  </div>
                  <div class="mt-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                    <div class="flex flex-row">
                      <input class="bg-white text-gray-700 focus:outline-blue-950 focus:shadow-outline border border-gray-300 rounded rounded-r-none py-2 px-4 block w-full appearance-none" id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm your password" />
                      <button aria-label="button" class="w-9 h-11 bg-blue-300 rounded rounded-l-none" onclick="createpassword('password_confirmation',this)" type="button" id="button-addon21"><i class="ri-eye-off-line text-white align-middle"></i></button>
                    </div>
                    @if ($errors->has('password_confirmation'))
                        <span class="text-red-700 font-semibold text-lg">{{ $errors->first('password_confirmation') }}</span>
                    @endif
                  </div>
                  <div class="mt-8 flex justify-center items-center">
                      <button type="submit" name="submit" class="bg-[#000E3E] text-white font-bold py-3 px-8 w-2/4 rounded-full hover:bg-blue-500">Register</button>
                  </div>
              </form>
              <div class="mt-6 flex gap-2 items-center justify-around">
                    <a href="{{ route('user.login.show') }}" class="text-xs font-bold text-blue-500 hover:text-[#000E3E] p-0 m-0 uppercase"><span class="text-[#000E3E]">Already Registered? </span>Login</a>
              </div>
          </div>
      </div>
    </div>

@include('frontend.partials.footer')

