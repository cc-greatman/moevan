@include('user.partials.head')

@include('user.partials.navbar')

   <!-- HTML Markup for Modal -->
    <div class="container" id="cookiesPopup">
        <div class="cookiesContent">
          <button class="close" id="confirmNo" type="button">✖</button>
            <img src="https://cdn-icons-png.flaticon.com/512/1047/1047711.png" alt="cookies-img" />
            <p>Are you sure you want to withdraw <strong>₦<span id="amountNumber"></span> to Wallet Balance</strong>?</p>
          <button class="accept" id="confirmYes" type="submit">Yes, Proceed!</button>
        </div>
    </div>

   <div>
      <div class="text-center text-4xl text-blue-990 font-bold mt-12 mb-8">
        Withdraw Referral Bonus
      </div>

      <div
        class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 pl-3 pr-3 md:pl-12 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-16"
      >
        <form method="post" action="{{ route("user.profile.bonus.show") }}">
            @csrf
            <input type="hidden" name="user" value="{{ Auth::guard('web')->user()->id }}"/>
            <div>
              <div class="space-y-4 mb-5">
                <p><label for="">Referral Balance</label></p>
                <input type="text" id="referral_bonus" name="referral_bonus" value="₦{{ number_format(Auth::guard('web')->user()->referral_balance, 2) }}" class="place-style rounded-lg h-14 w-full lg:w-3/4"
                />
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Amount to Withdraw</label></p>
                <input type="text" id="amount" name="amount" placeholder="Amount" class="place-style rounded-lg h-14 w-full lg:w-3/4" />
              </div>
            </div>

            <div class="mt-10">
              <button onclick="showConfirmation(event)" class="bg-blue-990 text-white text-3xl font-bold rounded-lg md:px-20 px-14 md:py-6 py-3 cursor-pointer"
                >Withdraw</button>
            </div>
            <button type="submit" id="submitButton" name="submit" style="display: none;"></button>
        </form>
      </div>
   </div>

   <!-- JavaScript Function for Confirmation -->
    <script>
        // JavaScript Function for Confirmation
        document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal by default

        // Handle "Yes" button click
        document.getElementById("confirmYes").addEventListener("click", function() {
            // Get modal elements
            var modal = document.getElementById('cookiesPopup');

            // Close the modal
            modal.style.display = 'none'; // Hide modal

            var submitButton = document.getElementById("submitButton");
            if (submitButton) {
                submitButton.click();
            } else {
                console.log('Submit button not found');
            }
        });

        // Handle "No" button click
        document.getElementById("confirmNo").addEventListener("click", function() {
            // Close the modal
            document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal
        });

        document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal by default

        function showConfirmation(event) {
          event.preventDefault(); // Prevent default form submission


          var amountNumber = document.getElementById('amount').value;
          var formattedAmount = parseFloat(amountNumber).toLocaleString();
          document.getElementById('amountNumber').innerText = formattedAmount;

          // Show modal
          document.getElementById('cookiesPopup').style.display = 'block'; // Show modal
        }
    </script>


@include('user.partials.footer')
