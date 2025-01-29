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
            style="background: #AE328E; color: #000000"
          >
          <a href="{{ route('user.top-up.fund.show') }}">Automated Funding</a>
          </li>
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
        </ul>
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
