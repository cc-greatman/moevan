@include('user.partials.head')

@include('user.partials.navbar')

<div>
      <!-- HTML Markup for Modal -->
      <div class="container" id="cookiesPopup">
        <div class="cookiesContent">
          <button class="close" id="confirmNo" type="button">✖</button>
            <img src="https://cdn-icons-png.flaticon.com/512/1047/1047711.png" alt="cookies-img" />
            <p>Are you sure you want to continue to buy <strong><span id="networkName"></span> ₦<span id="amountNumber"></span> for <span id="phoneNumber"></span></strong>?</p>
          <button class="accept" id="confirmYes" type="submit">Yes, Proceed!</button>
        </div>
      </div>

      <div class="text-center text-4xl text-blue-990 font-bold mt-12 mb-8">
        Airtime Top-Up
      </div>

      <div
        class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 pl-3 pr-3 mx-6 sm:mx-0 md:pl-12 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-16">
        <form id="bitForm" method="POST" action="{{ route('user.airtime.buy.process') }}">
            @csrf
            <div>
              <div class="space-y-4 mb-5">
                <p><label for="">Network</label></p>
                <select class="place-style rounded-sm h-14 w-full lg:w-3/4" name="network_id" id="network" aria-label="Default select example">
                    <option selected="" disabled>-------</option>
                    @foreach ($networks as $line)
                        <option value="{{ $line->network_id }}">{{ $line->name }}</option>
                    @endforeach
                </select>
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Mobile number</label></p>
                <input
                  type="text" class="place-style rounded-sm h-14 w-full lg:w-3/4" name="phone" value="{{ old('phone') }}" id="phone" placeholder="07012234556" maxlength="11"
                />
              </div>

              <div class="space-y-4 mb-5">
                    <p><label for="amount">Amount</label></p>
                    <input type="text" placeholder="Amount" name="amount" id="amount" value="{{ old('amount') }}" class="place-style rounded-sm h-14 w-full lg:w-3/4" />
                </div>

                <div class="space-y-4 mb-5">
                    <p><label for="amountToPay">Amount to Pay (@if(Auth::user()->account_type == 1) -1% @elseif(Auth::user()->account_type == 2) -2% @elseif(Auth::user()->account_type == 3) -2.5% @endif)</label></p>
                    <input type="text" placeholder="Amount to Pay" name="amountToPay" id="amountToPay" class="place-style rounded-sm h-14 w-full lg:w-3/4" readonly/>
                </div>

                <script>
                    // Get references to the input fields
                    var amountInput = document.getElementById('amount');
                    var amountToPayInput = document.getElementById('amountToPay');

                    // Add event listener to the "Amount" input field
                    amountInput.addEventListener('input', function() {
                        var amount = parseFloat(amountInput.value); // Parse the input value as a float
                        var accountType = {{ Auth::user()->account_type }}; // Get the user's account type

                       // Calculate the discounted amount based on the account type
                        var discountedAmount;
                        if (accountType === 1) {
                            discountedAmount = amount * 0.01;
                        } else if (accountType === 2) {
                            discountedAmount = amount * 0.02;
                        } else if (accountType === 3) {
                            discountedAmount = amount * 0.025;
                        } else {
                            discountedAmount = 0; // Handle other account types here if needed
                        }

                        // Calculate the amount to pay
                        var amountToPay = amount - discountedAmount;

                        // Update the "Amount to Pay" input field with the calculated amount
                        amountToPayInput.value = amountToPay.toFixed(2); // Display the amount with two decimal places
                    });
                </script>

            </div>

            <div class="">
              <button onclick="showConfirmation(event)" class="bg-blue-990 text-white text-2xl font-bold rounded-lg md:px-20 md:py-6 px-8 py-3 cursor-pointer">Buy Now</button>
            </div>
            <button id="submitButton" type="submit" name="submit" style="display: none;"></button>
        </form>
      </div>
    </div>
    
    <div class="md:mt-72 mt:44">
      <div class="text-center text-2xl text-blue-990 font-bold mt-12 mb-8">
        Codes for Airtime Balance
      </div>

      <div
        class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 mx-6 sm:mx-0 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-11"
      >
        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl">MTN *310#</p>
        </div>

        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl">Airtel *310#</p>
        </div>

        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl">Glo *310#</p>
        </div>

        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl">9mobile *310#</p>
        </div>
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

        function showConfirmation(event) {
            event.preventDefault(); // Prevent default form submission

            var networkName = document.getElementById('network').options[document.getElementById('network').selectedIndex].text;
            var phoneNumber = document.getElementById('phone').value;
            var amountNumber = document.getElementById('amount').value;
            var formattedAmount = parseFloat(amountNumber).toLocaleString();
            document.getElementById('amountNumber').innerText = formattedAmount;
            document.getElementById('networkName').innerText = networkName;
            document.getElementById('phoneNumber').innerText = phoneNumber;

            // Show modal
            document.getElementById('cookiesPopup').style.display = 'block'; // Show modal
        }

    </script>

@include('user.partials.footer')
