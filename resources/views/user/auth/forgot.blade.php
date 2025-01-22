@include('frontend.partials.head')

@include('frontend.partials.navbar')

    <div class="py-16">
      <div class="flex bg-white rounded-lg shadow-lg overflow-hidden mx-auto max-w-sm lg:max-w-5xl">
          <div class="hidden lg:block lg:w-1/2 bg-cover"
              style="background-image:url('../frontend/img/Mask\ group.png')">
          </div>
          <div class="bg-[#E1E8FF] w-full py-16 px-4 lg:py-20 lg:px-10 lg:w-1/2">
              @if(isset ($errors) && count($errors) > 0)
                    <div class="fixed inset-x-0 top-10 pb-2 sm:pb-5 z-50">
                        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
                            <div class="rounded-lg bg-red-600 p-2 shadow-lg sm:p-3">
                                <div class="flex flex-wrap relative items-center justify-between">
                                    <div class="flex p-2 flex-1 items-center font-medium text-white">
                                        <ul>
                                        @foreach($errors->all() as $error)
                                          <li class="text-sm font-semibold">{{ $error }}</li>
                                        @endforeach
                                        </ul>
                                    </div>
                                    <button class="absolute -top-2 -right-2 hover:text-gray-100" onclick="return this.parentNode.parentNode.parentNode.parentNode.remove();">
                                        <span class="sr-only">Dismiss</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                            class="h-6 w-6 text-white">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
              @endif
              @if(session()->has('success'))
                    <div class="fixed inset-x-0 top-10 pb-2 sm:pb-5 z-50">
                        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
                            <div class="rounded-lg bg-green-600 p-2 shadow-lg sm:p-3">
                                <div class="flex flex-wrap relative items-center justify-between">
                                    <div class="flex p-2 flex-1 items-center font-medium text-white">
                                        <p class="text-sm font-semibold">{{ session()->get('success') }}</p>
                                    </div>
                                    <button class="absolute -top-2 -right-2 hover:text-gray-100" onclick="return this.parentNode.parentNode.parentNode.parentNode.remove();">
                                        <span class="sr-only">Dismiss</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                                            class="h-6 w-6 text-white">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
              @endif
              <h2 class="text-4xl font-bold text-[#001D82] text-left">Forgot your password?, <br>We got you covered!</h2>
              <form method="post" action="{{ route("user.forgot-password.perform") }}">
                  @csrf
                <div class="mt-10">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input class=" font-semibold bg-white text-gray-700 focus:outline-blue-950 focus:shadow-outline border border-gray-300 rounded py-2 px-4 block w-full appearance-none"  value="{{ old("email") }}" name="email" placeholder="Enter registered email address" />
                    @if ($errors->has('email'))
                        <span class="text-red-700 font-semibold text-lg">{{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="mt-8 flex justify-center items-center">
                  <button class="g-recaptcha bg-[#000E3E] text-white font-bold py-3 px-8 w-2/4 rounded-full hover:bg-blue-500" type="submit" name="submit">Send Reset Link</button>
                </div>
              </form>
          </div>
      </div>
    </div>

@include('frontend.partials.footer')

