@include('user.partials.head')

@include('user.partials.navbar')

<div class="bg-white board pb-5 pt-14 mx-1 lg:ml-32 lg:mr-24 md:ml-12 md:mr-6 mb-8" style="margin-top: 70px;">

    <div class="md:text-4xl text-2xl sm:text-center mb-7 px-3">
        <span class="text-blue-900 font-bold">Package</span> :
        <span class="text-blue-900 font-medium">@if (Auth::user()->account_type == "1") Customer @elseif (Auth::user()->account_type == "2") Reseller @else Agent @endif</span>&nbsp; &nbsp;
        <span class="text-blue-900 font-bold">KYC</span> :
        <span class="text-blue-900 font-medium">@if (Auth::user()->is_kyc_verified === 1) Verified @else Not Verified @endif</span>
      </div>

      <div class="mb-12 cursor-pointer">
        <ul class="flex flex-wrap justify-center md:gap-5 gap-1">
          @if (Auth::user()->is_kyc_verified !== 1)
          <li
            class="bg-blue-990 text-white sm:text-27 text-base sm:font-bold sm:py-6 sm:px-8 py-4 px-4"
            style="border-radius: 11.996px"
          >
            <a href="{{ route('user.profile.kyc.show') }}">Verify KYC</a>
          </li>
          @endif
        </ul>
      </div>

    <div>
        <ul
          class="grid sm:grid-cols-4 grid-cols-1 justify-center gap-6 cursor-pointer tab mx-10"
        >
          <li
            class="tablinks text-xl font-medium rounded border-1 border-solid border-black py-5 px-7 text-center"
            style="
              background: #0357EE;
              color: #000000;
              .tab li:hover {
                background-color: #C0D1EE;
              }
            "
            onclick="openCity(event, 'diamondbank')"
          >
            Moniepoint
          </li>
          <li
            class="tablinks text-xl font-medium rounded border-1 border-solid border-black py-5 px-7 text-center active"
            style="
              background: #0357EE;
              color: #000000;
              .tab li:hover {
                background-color: #C0D1EE;
              }
            "
            onclick="openCity(event, 'accessbank')"
          >
            Wema
          </li>
          <li
            class="tablinks text-xl font-medium rounded border-1 border-solid border-black py-5 px-7 text-center"
            style="background: #AE328E; color: #000000"
            onclick="openCity(event, 'gtbank')"
          >
            Sterling Bank
          </li>
        </ul>

        <div class=" text-white mb-16 tabcontent" style=" background-color: #0357EE;" id="accessbank">
          <div class="flex flex-wrap sm:pl-10 py-9">

            <div class="font-medium sm:text-3xl text-xl pl-6 space-y-2">
              <p>Account Number: {{ optional($wema)->account_number }}</p>
              <p>Account Name: - {{ optional($wema)->account_name }}</p>
              <p>Bank Name: Wema Bank</p>
              @if(auth()->guard('web')->user()->account_type === 1)
              <p>No Charge</p>
              @else
              <p>- 1% Charge</p>
              @endif
              @if(auth()->guard('web')->user()->is_kyc_verified !== 1)
              <a href="{{ route('user.profile.kyc.show') }}" rel="_nofollow" class="button-54" role="button">Generate Account</a>
              @endif
            </div>
          </div>

          <div
            class="flex flex-wrap justify-between bg-white text-red-990 px-10"
          >
            <p class="sm:text-6xl text-4xl font-extrabold pr-3">₦{{ number_format(Auth::user()->balance, 2) }}</p>
            <p class="text-xl font-medium pt-4">Wallet Balance</p>
          </div>

          <div class="pl-10 py-5">
            <p class="font-medium">AUTOMATED BANK TRANSFER</p>
            <p class="font-medium">
              Make transfer to this account to fund your wallet Instantly! <br>
              You can save this account as beneficiary to topup anytime!
            </p>
          </div>
        </div>

        <div class="text-white mb-16 tabcontent" id="gtbank" style="background-color: #AE328E;">
          <div class="flex flex-wrap sm:pl-10 py-9">

            <div class="font-medium sm:text-3xl text-xl pl-6 space-y-2">
              <p>Account Number: {{ optional($sterling)->account_number }}</p>
              <p>Account Name: - {{ optional($sterling)->account_name }}</p>
              <p>Bank Name: Sterling Bank</p>
              @if(auth()->guard('web')->user()->account_type === 1)
              <p>No Charge</p>
              @else
              <p>- 1% Charge</p>
              @endif
              @if(auth()->guard('web')->user()->is_kyc_verified !== 1)
              <a href="{{ route('user.profile.kyc.show') }}" rel="_nofollow" class="button-54" role="button">Generate Account</a>
              @endif
            </div>
          </div>

          <div
            class="flex flex-wrap justify-between bg-white text-red-990 px-10"
          >
            <p class="sm:text-6xl text-4xl font-extrabold pr-3">₦{{ number_format(Auth::user()->balance, 2) }}</p>
            <p class="text-xl font-medium pt-4">Wallet Balance</p>
          </div>

          <div class="pl-10 py-5">
            <p class="font-medium">AUTOMATED BANK TRANSFER</p>
            <p class="font-medium">
              Make transfer to this account to fund your wallet Instantly! <br>
              You can save this account as beneficiary to topup anytime!
            </p>
          </div>
        </div>

        <div class=" text-white mb-16 tabcontent" style=" background-color: #0357EE;" id="diamondbank">
          <div class="flex flex-wrap sm:pl-10 py-9">

            <div class="font-medium sm:text-3xl text-xl pl-6 space-y-2">
              <p>Account Number: {{ optional($moniepoint)->account_number }}</p>
              <p>Account Name: - {{ optional($moniepoint)->account_name }}</p>
              <p>Bank Name: Moniepoint Microfinance Bank</p>
              @if(auth()->guard('web')->user()->account_type === 1)
              <p>No Charge</p>
              @else
              <p>- 1% Charge</p>
              @endif
              @if(auth()->guard('web')->user()->is_kyc_verified !== 1)
              <a href="{{ route('user.profile.kyc.show') }}" rel="_nofollow" class="button-54" role="button">Generate Account</a>
              @endif
            </div>
          </div>

          <div
            class="flex flex-wrap justify-between bg-white text-red-990 px-10"
          >
            <p class="sm:text-6xl text-4xl font-extrabold pr-3">₦{{ number_format(Auth::user()->balance, 2) }}</p>
            <p class="text-xl font-medium pt-4">Wallet Balance</p>
          </div>

          <div class="pl-10 py-5">
            <p class="font-medium">AUTOMATED BANK TRANSFER</p>
            <p class="font-medium">
              Make transfer to this account to fund your wallet Instantly! <br>
              You can save this account as beneficiary to topup anytime!
            </p>
          </div>
        </div>

      </div>

</div>

    <script>
        function openCity(evt, cityName) {
          var i, tabcontent, tablinks;
          tabcontent = document.getElementsByClassName("tabcontent");
          for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
          }
          tablinks = document.getElementsByClassName("tablinks");
          for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
          }
          document.getElementById(cityName).style.display = "block";
          evt.currentTarget.className += " active";
        }
    </script>

@include('user.partials.footer')
