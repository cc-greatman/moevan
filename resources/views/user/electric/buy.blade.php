@include('user.partials.head')

@include('user.partials.navbar')

      <!-- HTML Markup for Modal -->
      <div class="container" id="cookiesPopup">
        <div class="cookiesContent">
          <button class="close" id="confirmNo" type="button">✖</button>
            <img src="https://cdn-icons-png.flaticon.com/512/1047/1047711.png" alt="cookies-img" />
            <p>Are you sure you want to buy <strong><span id="networkName"></span> <span id="modeName"></span> ₦<span id="amountNumber"></span> for <span id="phoneNumber"></span></strong>?</p>
          <button class="accept" id="confirmYes" type="submit">Yes, Proceed!</button>
        </div>
      </div>

<div>
      <div class="text-center text-4xl text-blue-990 font-bold mt-12 mb-8">Electricity Bill Payment </div>

      <div class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 pl-3 pr-3 mx-6 sm:mx-0 md:pl-12 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-16">

    <form id="bitForm" action="{{ route('user.utility.electric.perform') }}" method="post">
        @csrf
        <div>
          <div class="space-y-4 mb-5">
            <p><label for="">Disco name</label></p>
            <select class="place-style rounded-sm h-14 w-full lg:w-3/4" name="disco-name" id="disco-name" aria-label="Default select example">
                <option selected="" disabled>-------</option>
                <option value="abuja-electric">AEDC - Abuja Electric</option>
                <option value="kano-electric">KEDCO - Kano Electric</option>
                <option value="ikeja-electric">IKEDC - Ikeja Electric</option>
                <option value="portharcourt-electric">PHED - Portharcourt Electric</option>
                <option value="eko-electric">EKEDC - Eko Electric</option>
                <option value="kaduna-electric">KAEDCO - Kaduna Electric</option>
                <option value="jos-electric">JED - Jos Electric</option>
                <option value="ibadan-electric">IBEDC - Ibadan Electric</option>
                <option value="enugu-electric">EEDC - Enugu Electric</option>
                <option value="aba-electric">ABA - Aba Electric</option>
                <option value="benin-electric">BEDC - Benin Electric</option>
            </select>
          </div>

          <div class="space-y-4 mb-5">
            <p><label for="">Meter Type</label></p>
            <select class="place-style rounded-sm h-14 w-full lg:w-3/4" name="meter-type" id="meter-type" aria-label="Default select example">
                <option selected="" disabled>-------</option>
                <option value="prepaid">PREPAID</option>
                <option value="postpaid">POSTPAID</option>

            </select>
          </div>

          <div class="space-y-4 mb-5">
            <p><label for="">Meter number</label></p>
            <input type="text" placeholder=""  name="meter-number" id="meter-number" class="place-style rounded-lg h-14 w-full lg:w-3/4" />
            <br>
            <span id="meter-number-feedback"></span>
          </div>

          <div class="space-y-4 mb-5">
            <p><label for="">Customer Phone Number</label></p>
            <input type="text" placeholder="" name="phone" class="place-style rounded-lg h-14 w-full lg:w-3/4" maxlength="11" />
          </div>

          <div class="space-y-4 mb-5">
                    <p><label for="amount">Amount</label></p>
                    <input type="text" placeholder="Amount" name="amount" id="amount" value="{{ old('amount') }}" class="place-style rounded-sm h-14 w-full lg:w-3/4" />
                </div>

                <div class="space-y-4 mb-5">
                    <p><label for="amountToPay">Amount to Pay (@if(Auth::user()->account_type == 1) -0.5% @else -1% @endif)</label></p>
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
                        var discountedAmount = accountType === 1 ? amount * 0.005 : amount * 0.01;

                        // Calculate the amount to pay
                        var amountToPay = amount - discountedAmount;

                        // Update the "Amount to Pay" input field with the calculated amount
                        amountToPayInput.value = amountToPay.toFixed(2); // Display the amount with two decimal places
                    });
                </script>

        </div>

        <div class="">
            <button onclick="showConfirmation(event)" class="bg-blue-990 text-white text-2xl font-bold rounded-lg px-20 py-6 cursor-pointer">Buy Now</button>
         </div>
         <button id="submitButton" type="submit" name="submit" style="display: none;"></button>
    </form>

    </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
                // $(document).ready(function () {
                //     // Event listener for Disco Name select change
                //     $('#disco-name').change(function () {
                //         // Get the selected serviceID
                //         var serviceName = $(this).val();

                //         $('#meter-type').change(function () {
                //             // Get the selected meter-type
                //             var meterType = $(this).val();

                //             // Event listener for Meter Number input blur
                //             $('#meter-number').off('blur').blur(function () {
                //                 // Continue with the existing code for meter number verification
                //                 var meterNumber = $(this).val();
                //                 $('#meter-number-feedback').text('').removeClass('text-danger');

                //                 // Make the API request to verify the meter number
                //                 $.ajax({
                //                     url: 'https://api-service.vtpass.com/api/merchant-verify',
                //                     method: 'POST',
                //                     headers: {
                //                         'api-key': 'b382ea284d8c05da64a2fc6a1002b783',
                //                         'secret-key': 'SK_42642f8ea1f5e158890b5dfd9dfcac87ee314158597',
                //                         'Content-Type': 'application/json', // Set the content type if required
                //                     },
                //                     data: JSON.stringify({
                //                         billersCode: meterNumber,
                //                         serviceID: serviceName,
                //                         type: meterType, // Adjust the type based on your needs
                //                     }),
                //                     success: function (response) {
                //                         // Handle the successful response
                //                         if (response.code === '000' && response.content) {
                //                             // Set the feedback with Customer Name and Address or District
                //                             var feedbackText = 'Customer Name: ' + response.content.Customer_Name + '<br>';

                //                             if (response.content.Address) {
                //                                 feedbackText += 'Address: ' + response.content.Address;
                //                             } else if (response.content.District) {
                //                                 feedbackText += 'District: ' + response.content.District;
                //                             } else if (response.content.Business_Unit) {
                //                                 feedbackText += 'Business Unit: ' + response.content.Business_Unit;
                //                             } else {
                //                                 feedbackText += 'Address information not available';
                //                             }

                //                             $('#meter-number-feedback').html(feedbackText).addClass('text-success');
                //                         } else {
                //                             // Set the feedback with the API response content
                //                             $('#meter-number-feedback').text('Meter number validation failed: ' + response.content).addClass('text-danger');
                //                         }
                //                     },
                //                     error: function (xhr, status, error) {
                //                         // Handle the error
                //                         $('#meter-number-feedback').text('Error: ' + error).addClass('text-danger');
                //                     }
                //                 });
                //             });
                //         })
                //     });
                // });
            </script>
    <!-- JavaScript Function for Confirmation -->
    <script>
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

          var networkName = document.getElementById('disco-name').options[document.getElementById('disco-name').selectedIndex].text;
          var modeName = document.getElementById('meter-type').options[document.getElementById('meter-type').selectedIndex].text;
          var phoneNumber = document.getElementById('meter-number').value;
          var amountNumber = document.getElementById('amount').value;
          var formattedAmount = parseFloat(amountNumber).toLocaleString();
          document.getElementById('amountNumber').innerText = formattedAmount;
          document.getElementById('modeName').innerText = modeName;
          document.getElementById('networkName').innerText = networkName;
          document.getElementById('phoneNumber').innerText = phoneNumber;

          // Show modal
          document.getElementById('cookiesPopup').style.display = 'block'; // Show modal
        }
    </script>


@include('user.partials.footer')
