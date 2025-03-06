@include('user.partials.head')

@include('user.partials.navbar')

    @if($popup->active === 1)
        @if(session()->has('fresh_login'))
            <!-- HTML Markup for Modal -->
            <div class="container" id="cookiesPopup" style="display: none;"> <!-- Modal hidden by default -->
                <div class="cookiesContent">
                    <button class="close" id="confirmNo" type="button">✖</button>
                    <img src="https://cdn-icons-png.flaticon.com/512/2014/2014825.png" alt="cookies-img" />
                    <p style="text-align: left;">{!! $message->rolling_message !!}</p>
                    <button id="confirmClose" type="submit" class="accept">Thank You!</button>
                    <br>
                    <button id="confirmYes" type="submit" class="decline" style="margin-top: 5px; font-size: 12px; font-weight: bold;">No, Skip!</button>
                </div>
            </div>

            {{-- Forget the session key after displaying the modal --}}
            {{ session()->forget('fresh_login') }}
        @endif
    @endif

<div
      class="flex flex-wrap justify-between md:pt-24 pt-11 pb-56 text-white"
      style="background-image: url(../frontend/img/Rectangle\ 2.png)"
    >
      <div class="ml-8 md:mb-16 mb-6">
        <p class="text-3xl md:text-5xl font-bold mb-4 tracking-wide">
          {{ Auth::user()->username }}, welcome!
        </p>

        <p class="text-xl md:text-3xl font-medium">
          WANT FREE MONEY💰?<br><br>
          THEN REFER AND ALWAYS GET 1% OF YOUR REFERRALS FUNDING AFTER FUNDING<br><br>
          YOU ALSO GET 25% WHEN EVER YOUR REFERRAL UPGRADES AFTER UPGRADES
        </p>
      </div>
    </div>

    <div
      class="bg-white board pb-5 pt-14 mx-1 lg:ml-32 lg:mr-24 md:ml-12 md:mr-6 mb-8"
    >
      <!-- md:ml-48 md:mr-32 -->
      <div
        class="pb-8 pl-10 flex-shrink-0 text-blue-900 font-lexend font-medium md:text-4xl text-3xl"
      >
        <?php
            date_default_timezone_set('Africa/Lagos'); // Set your timezone here
            $time = date("H"); // Get current hour in 24-hour format

            // Greeting based on time of the day
            if ($time >= 5 && $time < 12) {
                echo "Good Morning!";
            } elseif ($time >= 12 && $time < 18) {
                echo "Good Afternoon!";
            } elseif ($time >= 18 && $time < 22) {
                echo "Good Evening!";
            } else {
                echo "Good Night!";
            }
        ?>
      </div>

      <div class="w-full h-0.5 mb-6" style="background: #c3c3c3"></div>

      <div class="md:text-4xl text-2xl sm:text-center mb-7 px-3">
        <span class="text-blue-900 font-bold">Package</span> :
        <span class="text-blue-900 font-medium">@if (Auth::user()->account_type == "1") Customer @elseif (Auth::user()->account_type == "2") Reseller @else Agent @endif</span>&nbsp; &nbsp;
        <span class="text-blue-900 font-bold">KYC</span> :
        <span class="text-blue-900 font-medium">@if (Auth::user()->is_kyc_verified === 1) Verified @else Not Verified @endif</span>
      </div>

      <div class="mb-12 cursor-pointer">
        <ul class="flex flex-wrap justify-center md:gap-5 gap-1">
          <li
            class="bg-blue-990 text-white sm:text-27 text-base sm:font-bold sm:py-6 sm:px-8 py-4 px-4"
            style="border-radius: 11.996px"
          >
            Referral Code : {{ $referral_code }}
          </li>
          @if (Auth::user()->is_kyc_verified !== 1)
          <li
            class="bg-blue-990 text-white sm:text-27 text-base sm:font-bold sm:py-6 sm:px-8 py-4 px-4"
            style="border-radius: 11.996px"
          >
            <a href="{{ route('user.profile.kyc.show') }}">Verify KYC</a>
          </li>
          @endif
          <span id="referralLink" style="text-decoration: underline; display: none;">{{ Auth::user()->referral_link }}</span>
          <button class="copy-btn bg-blue-990 text-white sm:text-27 text-base sm:font-bold sm:py-6 sm:px-8 py-4 px-4" style="border-radius: 11.996px" data-copy-target="referralLink">
            Copy Referral Link
          </button>
        </ul>
      </div>

      <div>
        <ul
          class="grid sm:grid-cols-4 grid-cols-1 justify-center gap-6 cursor-pointer tab mx-10"
        >
          <li
            class="tablinks text-xl font-medium rounded border-1 border-solid border-black py-5 px-7 text-center"
            style="
              background-color: #fff000;
              color: #000;
              .tab li:hover {
                background-color: #fff000;
              }
            "
          ><a href="{{ route('user.top-up.fund.manual.show') }}">Automated Manual Funding</a>
          </li>
          <li class="tablinks text-xl font-medium rounded border-1 border-solid border-black py-5 px-7 text-center" style="background: #AE328E; color: #000000">
            <a href="{{ route('user.top-up.fund.show') }}">Automated Funding</a>
          </li>
        </ul>

        <div class="flex flex-wrap items-center justify-center gap-4" style="margin-top: 25px;">
          <div
            class="flex bg-white pr-20"
            style="
              border-radius: 2px;

              box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25);
            "
          >
            <p class="px-3 py-3">
              <img
                src="../frontend/img/🦆 icon _menu 1_.png"
                class="bg-blue-990 py-3 px-3"
              />
            </p>
            <p class="text-blue-900 flex items-center font-medium text-xl">
              <a href="{{ route('user.transactions.general.show') }}">Transactions</a>
            </p>
          </div>

          <div
            class="flex bg-white pr-8"
            style="
              border-radius: 2px;

              box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25);
            "
          >
            <p class="px-3 py-3">
              <img
                src="../frontend/img/🦆 icon _menu 1_.png"
                class="bg-blue-990 py-3 px-3"
              />
            </p>
            <p class="text-blue-900 flex items-center font-medium text-xl">
              <a href="{{ route('user.transactions.wallet.show') }}">Wallet summary</a>
            </p>
          </div>

          <div
            class="flex bg-white pr-8"
            style="
              border-radius: 2px;

              box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25);
            "
          >
            <p class="px-3 py-3">
              <img
                src="../frontend/img/🦆 icon _menu 1_.png"
                class="bg-blue-990 py-3 px-3"
              />
            </p>
            <p class="text-blue-900 flex items-center font-medium text-xl">
              <a href="{{ route('user.transactions.data.show') }}">Data Transactions</a>
            </p>
          </div>

          <div
            class="flex bg-white pr-8"
            style="
              border-radius: 2px;

              box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25);
            "
          >
            <p class="px-3 py-3">
              <img
                src="../frontend/img/🦆 icon _menu 1_.png"
                class="bg-blue-990 py-3 px-3"
              />
            </p>
            <p class="text-blue-900 flex items-center font-medium text-xl">
              <a href="{{ route('user.transactions.airtime.show') }}">Airtime Transactions</a>
            </p>
          </div>

          @if (Auth::user()->account_type == "1")
            <form action="{{ route('user.profile.account.upgrade') }}" method="post">
                @csrf
                <input type="hidden" name="user" value="{{ Auth::user()->id }}">
                <div class="flex bg-white pr-2" style="border-radius: 2px; box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25); ">
                    <p class="px-3 py-3">
                      <img
                        src="../frontend/img/🦆 icon _menu 1_.png"
                        class="bg-blue-990 py-3 px-3"
                      />
                    </p>
                    <div class="flex items-center">
                      <button type="submit" name="submit" class="text-blue-900 font-medium text-xl">
                        Upgrade to Reseller <br />
                        ₦2,000
                      </button>
                    </div>
                </div>
            </form>
          @elseif (Auth::user()->account_type == "2")
            <form action="{{ route('user.profile.account.upgrade') }}" method="post">
                @csrf
                <input type="hidden" name="user" value="{{ Auth::user()->id }}">
                <div class="flex bg-white pr-2" style="border-radius: 2px; box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25); ">
                    <p class="">
                      <img
                        src="../frontend/img/🦆 icon _menu 1_.png"
                        class="bg-blue-990 py-3 px-3"
                      />
                    </p>
                    <div class="flex items-center">
                      <button type="submit" name="submit" style="padding: 0; margin: 0;" class="text-blue-900 font-medium text-xl">
                        Upgrade to Agent ₦5,000
                      </button>
                    </div>
                </div>
            </form>
        @endif
        </div>

      </div>
    </div>

    <div class="flex justify-center mb-8">
      <p class="flex items-center text-box rounded py-4">
        <marquee>
          {{ $settings->rolling_message }}
        </marquee>
      </p>
    </div>

    <div
      class="flex justify-centerbg-white rounded mx-2 lg:ml-40 lg:mr-24 md:ml-12 md:mr-6 mb-8"
      style="box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25)"
    >
      <p class="flex items-center px-3 py-3">
        <img src="../frontend/img/Group.png" class="bg-blue-990 py-4 px-4" />
        <span class="text-blue-900 font-bold text-2xl pb-4 pl-3" style="padding-right: 10px;"><a href="{{ route('user.profile.bonus.show') }}">My Total Referrals</a><span class="text-blue-900 font-bold text-2xl px-3 pl-3">{{ $userReferralCount }}</span></span>

         </div>
      </p>
    </div>

    <div
      class="flex justify-centerbg-white rounded mx-2 lg:ml-40 lg:mr-24 md:ml-12 md:mr-6 mb-8"
      style="box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25)"
    >
      <p class="flex items-center px-3 py-3">
        <img src="../frontend/img/Group.png" class="bg-blue-990 py-4 px-4" />
        <span class="text-blue-900 font-bold text-2xl pb-4 pl-3" style="padding-right: 10px;"><a href="{{ route('user.profile.bonus.show') }}">My Total Referral Bonus</a><span class="text-blue-900 font-bold text-2xl px-3 pl-3">₦{{ number_format(Auth::guard('web')->user()->referral_balance, 2) }}</span></span>

         </div>
      </p>
    </div>

    <div
      class="bg-white board-two pt-14 mx-1 lg:ml-36 lg:mr-24 md:ml-12 md:mr-6"
    >
      <div
        class="flex flex-wrap md:flex-nowrap justify-between lg:gap-14 md:gap-8 gap-4"
      >
        <div class="md:pl-14 pl-2 space-y-11 mr-3">
          <div class="bg-blue-999 py-4  text-center ">
            <span class="text-white text-2xl font-bold"
              ><a href="{{ route('user.transactions.general.show') }}">Transactions</a></span
            >
          </div>

          <ul
            class="text-blue-999 font-medium text-xl pl-5 pt-5 pb-20 "
            style="box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25)"
          >
              @foreach($notifications as $msg)
                <li class="flex gap-2">
                  <span
                    class="bg-blue-990 px-2 py-2 mt-1.5 rounded-full w-0 h-0"
                  ></span>
                  <span>{{ $msg->details }} </span>
                </li>
                <hr>
              @endforeach
          </ul>

          <div class="bg-blue-999 text-white text-center px-2 py-5 ">
            <p class="font-bold text-2xl pb-3">FAQs</p>
            <p class="font-medium text-xl">
              Please go through them to have a better knowledge of this platform
            </p>
          </div>
          <div class ="bg-blue-999 text-white text-center px-2 py-5 ">
            <p class="font-bold text-2xl pb-3">Support Team</p>
            <p class="font-medium text-xl pb-5">
              Have anything to say to us? Please contact our Support Team on
              Whatsapp
            </p>
            <a href="https://chat.whatsapp.com/Io850EGG8AaC0J6TdU7yic" target="_blank"><p style="margin-top: 30px;"
              class="font-bold text-xl bg-green-600 inline-block px-14 py-3 rounded"
            >
              WA Community
            </p></a>
            <a href="https://wa.link/3xvhjb" target="_blank"><p  style="margin-top: 20px;"
              class="font-bold text-2xl bg-green-600 inline-block px-14 py-3 rounded"
            >
              Send a DM
            </p></a>
          </div>
        </div>

        <div class="md:px-12 md:py-9 mx-2 w-full">
          <div class="grid grid-cols-2 lg:gap-8 md:gap-8 gap-4">
            <a href="{{ route('user.airtime.show') }}">
                <div
              class="bg-blue-999 flex flex-col justify-between"
              style="box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25)"
            >
                <p class="bg-blue-999 mx-auto flex items-center pt-4">
                <img
                  src="../frontend/img/dashboard-img/🦆 icon _mobile programming_.png"
                  class="bg-blue-999 pb-3 px-9 sm:px-0"
                />
              </p>
              <p class="text-center bg-white text-blue-999 font-medium py-3">
                Airtime Top-up
              </p>
            </div>
            </a>

            <a href="{{ route('user.data.show') }}">
                <div
              class="bg-blue-999 flex flex-col justify-between"
              style="box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25)"
            >
              <p class="bg-blue-999 mx-auto flex items-center pt-4">
                <img src="../frontend/img/dashboard-img/Group (1).png" class="bg-blue-999 px-9 sm:px-0" style="padding-bottom: 45px;" />
              </p>
              <p class="text-center bg-white text-blue-999 font-medium py-3">
                Data Top-up
              </p>
            </div>
            </a>

            <a href="javascript:void(0);">
                <div
              class="bg-blue-999 flex flex-col justify-between"
              style="box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25)"
            >
                <p class="bg-blue-999 mx-auto flex items-center pt-4">
                <img src="../frontend/img/dashboard-img/Group (2).png" class="bg-blue-999 px-9 sm:px-0" />
              </p>
              <p class="text-center bg-white text-blue-999 font-medium py-3">
                Airtime To Cash <br>
                (Coming Soon)
              </p>
            </div>
            </a>

            <a href="{{ route('user.utility.electric.show') }}">
                <div
              class="bg-blue-999 flex flex-col justify-between"
              style="box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25)"
            >
                <p class="bg-blue-999 mx-auto flex items-center pt-4">
                <img src="../frontend/img/dashboard-img/Flash On.png" class="bg-blue-999 px-9 sm:px-0" />
              </p>
              <p class="text-center bg-white text-blue-999 font-medium py-3">
                Electricity Bill
              </p>

            </div>
            </a>

            <a href="{{ route('user.utility.cable.show') }}">
                <div
              class="bg-blue-999 flex flex-col justify-between"
              style="box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25)"
            >
                <p class="bg-blue-999 mx-auto flex items-center pt-4">
                <img src="../frontend/img/dashboard-img/TV (2).png" class="bg-blue-999 px-9 sm:px-0" />
              </p>
              <p class="text-center bg-white text-blue-999 font-medium py-3">
                Cable Payments
              </p>

            </div>
            </a>

            <a href="javascript: void(0);">
                <div
              class="bg-blue-999 flex flex-col justify-between"
              style="box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25)"
            >
                <p class="bg-blue-999 mx-auto flex items-center pt-4">
                <img src="../frontend/img/dashboard-img/School.png" class="bg-blue-999 px-9 sm:px-0" />
              </p>
              <p class="text-center bg-white text-blue-999 font-medium py-3">
                Result checker <br>
                (Coming Soon)
              </p>

            </div>
            </a>

            <a href="javascript:void(0);">
                <div
              class="bg-blue-999 flex flex-col justify-between"
              style="box-shadow: 0px 0px 5px 3px rgba(0, 0, 0, 0.25)"
            >
                <p class="bg-blue-999 mx-auto flex items-center pt-4">
                <img
                  src="../frontend/img/dashboard-img/Gmail Logo.png"
                  class="bg-blue-999 px-9 sm:px-0"
                />
              </p>
              <p class="text-center bg-white text-blue-999 font-medium py-3">
                Bulk SMS <br>
                (Coming Soon)
              </p>

            </div>
            </a>
          </div>
        </div>
      </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.getElementById('cookiesPopup').style.display = 'block'; // Hide modal by default

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

            // Handle "No" button click
            document.getElementById("confirmClose").addEventListener("click", function() {
                console.log("No button clicked"); // Debugging
                document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal
            });
        });
    </script>

    <script>
        document.querySelectorAll('.copy-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var targetId = button.getAttribute('data-copy-target');
                var content = document.getElementById(targetId);

                var isHidden = content.style.display === 'none';
                if (isHidden) {
                    content.style.display = 'inline'; // Temporarily show hidden content
                }

                var range = document.createRange();
                range.selectNode(content);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
                document.execCommand('copy');
                window.getSelection().removeAllRanges();

                if (isHidden) {
                    content.style.display = 'none'; // Hide the content again
                }

                alert('Copied to clipboard!');
            });
        });
    </script>

@include('user.partials.footer')
