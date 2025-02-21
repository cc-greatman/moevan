@include('user.partials.head')

@include('user.partials.navbar')

    <!-- HTML Markup for Modal -->
    <div class="container" id="cookiesPopup">
      <div class="cookiesContent">
        <button class="close" id="confirmNo" type="button">✖</button>
        <img src="https://cdn-icons-png.flaticon.com/512/1047/1047711.png" alt="cookies-img" />
        <p>Are you sure you want to continue to buy <strong><span id="networkName"></span> <span id="planDetails"></span> for <span id="phoneNumber"></span></strong>?</p>
        <!-- Additional message element -->
        <p id="additionalMessage" style="display: none; color: red;">MAKE SURE YOU ARE NOT OWING AIRTEL AIRTIME/DATA</p>
        <button class="accept" id="confirmYes" type="submit">Yes, Proceed!</button>
      </div>
    </div>

    <div>
      <div class="text-center text-4xl text-blue-990 font-bold mt-12 mb-8">
        Buy Data
      </div>

      <div
        class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 pl-3 pr-3 mx-6 sm:mx-0 md:pl-12 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-16"
      >
        <form id="bitForm" method="post" action="{{ route('user.data.buy.perform') }}">
            @csrf
            <div>
              <div class="space-y-4 mb-5">
                <p><label for="">Network</label></p>
                <select class="place-style rounded-sm h-14 w-full lg:w-3/4" name="network_id" id="network" aria-label="Default select example">
                        <option selected="" disabled>-------</option>
                        <option value="1">MTN</option>
                        <option value="4">AIRTEL</option>
                        <option value="2">GLO</option>
                        <option value="3">9MOBILE</option>
                </select>
              </div>

              <div class="space-y-4 mb-5" style="display: none;" id="dataTypeContainer">
                <p><label for="">Data type</label></p>
                <select class="place-style rounded-sm h-14 w-full lg:w-3/4" name="dataType" id="dataType" aria-label="Default select example">
                        <option selected="" disabled>-------</option>
                </select>
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Data Plans</label></p>
                <select class="place-style rounded-sm h-14 w-full lg:w-3/4" name="plan" id="plan" aria-label="Default select example">
                        <option selected="" disabled>-------</option>
                </select>
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Mobile number</label></p>
                <input type="text" class="place-style rounded-sm h-14 w-full lg:w-3/4" name="phone" value="{{ old('phone') }}" id="phone" placeholder="07012234556" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" />
              </div>

              <div class="space-y-4 mb-5">
                <p><label for="">Amount</label></p>
                <input type="text" placeholder="Amount" id="amount" name="amount" class="place-style rounded-sm h-14 w-full lg:w-3/4"
                    readonly/>
             </div>
            </div>

            <div class="pt-10 m-4">
              <button onclick="showConfirmation(event)" class="bg-blue-990 text-white text-2xl font-bold rounded-lg md:px-20 md:py-6 px-8 py-3 cursor-pointer"
                >Buy Now</button>
            </div>
            <button id="submitButton" name="submit" type="submit" style="display: none;"></button>
        </form>
      </div>
    </div>

    <div class="md:mt-72 mt:44">
      <div class="text-center text-2xl text-blue-990 font-bold mt-12 mb-8">
        Codes for Data Balance
      </div>

      <div
        class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 mx-6 sm:mx-0 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-11"
      >
        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl">MTN: Send 2 to 323</p>
        </div>

        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl">Airtel *323#</p>
        </div>

        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl">Glo *323#</p>
        </div>

        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl">9mobile *323#</p>
        </div>
      </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Event listener for network change
            document.getElementById('network').addEventListener('change', function () {
                var selectedNetwork = this.value;

                // Show DataType container regardless of the network
                var dataTypeContainer = document.getElementById('dataTypeContainer');
                dataTypeContainer.style.display = 'block';

                // Fetch and populate Data Types if network has available data types
                if (selectedNetwork) {
                    fetchDataTypes(selectedNetwork);
                }

                // Clear Data Plan options
                clearPlanOptions();
            });

            // Event listener for data type change
            document.getElementById('dataType').addEventListener('change', function () {
                clearPlanOptions();
                fetchDataPlans(document.getElementById('network').value, this.value);
            });

            // Function to fetch and populate Data Types
            function fetchDataTypes(network) {
                var url = "{{ route('user.data.types') }}?network_id=" + network;

                fetch(url)
                    .then(response => response.json())
                    .then(dataTypes => {
                        var dataTypeSelect = document.getElementById('dataType');
                        dataTypeSelect.innerHTML = ''; // Clear existing options

                        var defaultOption = document.createElement('option');
                        defaultOption.value = "";
                        defaultOption.text = "Select Data Type";
                        dataTypeSelect.appendChild(defaultOption);

                        if (dataTypes.length > 0) {
                            dataTypes.forEach(dataType => {
                                var option = document.createElement('option');
                                option.value = dataType.id;
                                option.text = dataType.name;
                                dataTypeSelect.appendChild(option);
                            });
                        } else {
                            dataTypeSelect.innerHTML = '<option value="">No Data Types Available</option>';
                        }
                    });
            }

            // Function to fetch and populate Data Plans
            function fetchDataPlans(network, dataType = null) {
                var url = "{{ route('user.data.plans') }}?network_id=" + network;

                if (dataType !== null) {
                    url += '&dataType=' + dataType;
                }

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(dataPlans => {
                        var planSelect = document.getElementById('plan');
                        var amountInput = document.getElementById('amount');
                        planSelect.innerHTML = ''; // Clear existing options

                        var defaultOption = document.createElement('option');
                        defaultOption.value = "";
                        defaultOption.text = "Select Data Plan";
                        planSelect.appendChild(defaultOption);

                        dataPlans.forEach(dataPlan => {
                            var option = document.createElement('option');

                            var priceToShow = '';
                            switch (<?= Auth::user()->account_type ?>) {
                                case 1:
                                    priceToShow = dataPlan.price;
                                    break;
                                case 2:
                                    priceToShow = dataPlan.r_price;
                                    break;
                                case 3:
                                    priceToShow = dataPlan.a_price;
                                    break;
                            }

                            option.value = dataPlan.data_id;
                            option.text = dataPlan.size + ' - ₦' + priceToShow + ' @ ' + dataPlan.validity + ' days';
                            planSelect.appendChild(option);
                        });

                        planSelect.addEventListener('change', function () {
                            var selectedPlan = dataPlans.find(plan => plan.data_id == this.value);

                            switch (<?= Auth::user()->account_type ?>) {
                                case 1:
                                    amountInput.value = selectedPlan ? selectedPlan.price : '';
                                    break;
                                case 2:
                                    amountInput.value = selectedPlan ? selectedPlan.r_price : '';
                                    break;
                                case 3:
                                    amountInput.value = selectedPlan ? selectedPlan.a_price : '';
                                    break;
                            }

                            amountInput.readOnly = true;
                        });
                    });
            }

            function clearPlanOptions() {
                var planSelect = document.getElementById('plan');
                var amountInput = document.getElementById('amount');
                planSelect.innerHTML = '';

                var defaultOption = document.createElement('option');
                defaultOption.value = "";
                defaultOption.text = "Select Data Plan";
                planSelect.appendChild(defaultOption);

                amountInput.value = '';
                amountInput.readOnly = false;
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal by default

            // Handle "Yes" button click
            document.getElementById("confirmYes").addEventListener("click", function() {
                console.log("Yes button clicked"); // Debugging
                var submitButton = document.getElementById("submitButton");
                if (submitButton) {
                    submitButton.click();
                } else {
                    console.log('Submit button not found');
                }
                document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal after action
            });

            // Handle "No" button click
            document.getElementById("confirmNo").addEventListener("click", function() {
                console.log("No button clicked"); // Debugging
                document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal
            });

            function showConfirmation(event) {
                event.preventDefault(); // Prevent default form submission

                console.log("showConfirmation triggered"); // Debugging

                var networkName = document.getElementById('network').options[document.getElementById('network').selectedIndex].text;
                var planDetails = document.getElementById('plan').options[document.getElementById('plan').selectedIndex].text;
                var phoneNumber = document.getElementById('phone').value;
                var selectedNetworkId = document.getElementById('network').value;
                var selectedDataType = document.getElementById('dataType').value;
                var additionalMessage = document.getElementById('additionalMessage');

                document.getElementById('networkName').innerText = networkName;
                document.getElementById('planDetails').innerText = planDetails;
                document.getElementById('phoneNumber').innerText = phoneNumber;

                // Show additional message only for specific network_id and dataType values
                /*if (selectedNetworkId === "4" && (selectedDataType === "7" || selectedDataType === "4")) {
                        additionalMessage.style.display = 'block';
                        console.log("Additional message displayed"); // Debugging
                    } else {
                        additionalMessage.style.display = 'none';
                    }*/

                document.getElementById('cookiesPopup').style.display = 'block'; // Show modal
                console.log("Popup displayed"); // Debugging
            }

            // Ensure this function is correctly linked to the button's onclick
            document.querySelector("button[onclick='showConfirmation(event)']").addEventListener('click', showConfirmation);
        });
    </script>


@include('user.partials.footer')
