@include('user.partials.head')

@include('user.partials.navbar')

    <!-- HTML Markup for Modal-->
    <div class="container" id="cookiesPopup">
        <div class="cookiesContents">
          <img src="https://cdn-icons-png.flaticon.com/512/2014/2014825.png" alt="cookies-img" />
          <p>The Central Bank of Nigeria, released a circular recently, regarding virtual accounts issued to customers, as follows:
          </p>
          <p style="text-align: left;">
            <strong><span style="font-size: 18px;">&middot;</span> Every virtual account(Personal Wallet) must be linked with either a Bank Verification Number (BVN) or National Identification Number (NIN).</strong>
            <br>
            <br>
            <strong><span style="font-size: 18px;">&middot;</span> Ensure Your Personal Details are the same as the one linked to your BVN/NIN</strong>
          </p>
          <button class="accept" id="confirmYes" type="submit">Proceed to KYC Verification</button>
        </div>
    </div>

    @if(session()->has('error'))
                    <div class="fixed inset-x-0 top-10 pb-2 sm:pb-5 z-50">
                        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
                            <div class="rounded-lg bg-red-600 p-2 shadow-lg sm:p-3">
                                <div class="flex flex-wrap relative items-center justify-between">
                                    <div class="flex p-2 flex-1 items-center font-medium text-white">
                                        <ul>
                                          <li class="text-sm font-semibold">{{ session('error') }}</li>
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

   <div>
      <div class="text-center text-4xl text-blue-990 font-bold mt-12 mb-8">
        Profile Settings
      </div>

      <div
        class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 pl-3 pr-3 md:pl-12 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-16"
      >
        <form action="{{ route("user.profile.profile.update") }}" method="post">
            @csrf
            <div>
              <div class="space-y-4 mb-5">
                <p><label for="">First Name</label></p>
                <input
                  type="text" name="first_name" placeholder="First Name" value="{{ optional(Auth::guard('web')->user())->first_name }}" class="place-style rounded-lg h-14 w-full lg:w-3/4"
                />
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Middle Name</label></p>
                <input type="text" name="middle_name" placeholder="Middle Name" value="{{ optional(Auth::guard('web')->user())->middle_name }}" class="place-style rounded-lg h-14 w-full lg:w-3/4"
                />
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Surname</label></p>
                <input type="text" name="last_name" placeholder="Last Name" value="{{ optional(Auth::guard('web')->user())->last_name }}" class="place-style rounded-lg h-14 w-full lg:w-3/4"
                />
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Username</label></p>
                <input type="text" name="username" placeholder="Username" value="{{ optional(Auth::guard('web')->user())->username }}" class="place-style rounded-lg h-14 w-full lg:w-3/4" disabled/>
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Phone Number</label></p>
                <input type="text" placeholder="07012246557" maxlength="11" id="phone" name="phone_number" value="{{ optional(Auth::guard('web')->user())->phone }}" class="place-style rounded-lg h-14 w-full lg:w-3/4"/>
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Date of Birth</label></p>
                <input type="date"class="place-style rounded-lg h-14 w-full lg:w-3/4" id="dob" name="dob" placeholder="dd/mm/yyyy" value="{{ optional(Auth::guard('web')->user())->dob }}"/>
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Email</label></p>
                <input type="text" placeholder="xyz@exmple.com" value="{{ optional(Auth::guard('web')->user())->email }}" class="place-style rounded-lg h-14 w-full lg:w-3/4" disabled/>
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Address</label></p>
                <input type="text" id="phone" name="address" placeholder="Your Current Address" value="{{ optional(Auth::guard('web')->user())->address }}" class="place-style rounded-lg h-14 w-full lg:w-3/4"/>
              </div>
            </div>

            <div class="mt-10">
              <button type="submit" name="submit" class="bg-blue-990 text-white text-3xl font-bold rounded-lg md:px-20 px-14 md:py-6 py-3 cursor-pointer"
                >Proceed</button>
            </div>
        </form>
      </div>
   </div

       <div>
          <div class="text-center text-4xl text-blue-990 font-bold mt-12 mb-8">
            KYC Verification
          </div>

          <div
            class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 pl-3 pr-3 md:pl-12 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-16"
          >
                <div class="mt-10">
                  <a href="{{ route('user.profile.kyc.show') }}" class="bg-blue-990 text-white text-3xl font-bold rounded-lg md:px-20 px-14 md:py-6 py-3 cursor-pointer"
                    >Proceed</a>
                </div>
          </div>
       </div>

   <div>
      <div class="text-center text-4xl text-blue-990 font-bold mt-12 mb-8">
        Password Settings
      </div>

      <div
        class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 pl-3 pr-3 md:pl-12 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-16"
      >
        <form id="bitForm" method="post" action="{{ route("user.profile.password.update") }}">
            @csrf
            <div>
              <div class="space-y-4 mb-5">
                <p><label for="">Current Password</label></p>
                <input type="text" id="current_password" name="current_password" placeholder="Current Password" class="place-style rounded-lg h-14 w-full lg:w-3/4"
                />
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">New Password</label></p>
                <input type="text" id="new_password" name="new_password" placeholder="New Password" class="place-style rounded-lg h-14 w-full lg:w-3/4" />
              </div>

              <div class="space-y-4 mb-12">
                <p><label for="">Confirm Password</label></p>
                <input type="text" id="confirm_password" name="confirm_password" placeholder="Confirm Password" class="place-style rounded-lg h-14 w-full lg:w-3/4"
                />
              </div>
            </div>

            <div class="mt-10">
              <button type="submit" name="submit" class="bg-blue-990 text-white text-3xl font-bold rounded-lg md:px-20 px-14 md:py-6 py-3 cursor-pointer"
                >Proceed</button>
            </div>
        </form>
      </div>
   </div>

   <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal by default

            // Handle "Yes" button click
            document.getElementById("confirmYes").addEventListener("click", function() {
                console.log("Yes button clicked"); // Debugging
                document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal after action
            });

            // Handle "No" button click
            document.getElementById("confirmNo").addEventListener("click", function() {
                console.log("No button clicked"); // Debugging
                document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal
            });
        });
    </script>

@include('user.partials.footer')
