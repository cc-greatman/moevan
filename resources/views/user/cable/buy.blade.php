@include('user.partials.head')
@include('user.partials.navbar')

<div class="container" id="cookiesPopup" style="display: none;">
  <div class="cookiesContent">
    <button class="close" id="confirmNo" type="button">✖</button>
    <img src="https://cdn-icons-png.flaticon.com/512/1047/1047711.png" alt="cookies-img" />
    <p>Are you sure you want to continue to buy <strong><span id="networkName"></span> <span id="planDetails"></span> @ ₦<span id="amountNumber"></span></strong>?</p>
    <button class="accept" id="confirmYes" type="button">Yes, Proceed!</button>
  </div>
</div>

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

<div>
  <div class="text-center text-4xl text-blue-990 font-bold mt-12 mb-8">Cable Subscription Payment</div>
  <div class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 pl-3 pr-3 md:pl-12 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-16">
    
    <form action="{{ route('user.utility.cable.perform') }}" method="post" autocomplete="off">
      @csrf
      <input type="hidden" name="user" value="{{ Auth::guard('web')->user()->id }}">

      <!-- Cable Name Selection -->
      <div class="space-y-4 mb-5">
        <p><label for="cableName">Cable Name</label></p>
        <select class="place-style rounded-sm h-14 w-full lg:w-3/4" name="cableName" id="cableName" aria-label="Default select example">
            <option selected disabled>-------</option>
            @foreach ($providers as $network)
                <option value="{{ $network->name }}">{{ $network->name }}</option>
            @endforeach
        </select>
      </div>
      
      <!-- Smartcard / IUC Number Field -->
      <div class="space-y-4 mb-5">
        <p><label for="billerCode">Smartcard / IUC Number</label></p>
        <input type="text" id="billerCode" name="billersCode" class="place-style rounded-lg h-14 w-full lg:w-3/4" maxlength="11" />
      </div>
      
      <!-- Customer Name (auto-loads based on Smart Card Validation) -->
      <div class="space-y-4 mb-5" id="customerNameContainer">
        <p><label for="customer_name">Name</label></p>
        <input type="text" value="" name="customer" id="customer" class="place-style rounded-lg h-14 w-full lg:w-3/4" style="color:green;" readonly/>
        
        <!-- Spinner element (hidden by default) -->
        <div id="spinner" style="display: none;">
            <img src="https://cdn-icons-png.flaticon.com/512/189/189792.png" alt="Loading..." style="width: 20px; height: 20px;" />
            <span>Loading customer name...</span>
        </div>
      </div>
      
      <!-- Subscription Plans (auto-loads based on Cable Name) -->
      <div class="space-y-4 mb-5" id="subscriptionPlansContainer" style="display: none;">
        <p><label for="subscriptionPlans">Plans</label></p>
        <select class="place-style rounded-sm h-14 w-full lg:w-3/4" name="variation_code" id="subscriptionPlans" aria-label="Default select example">
        </select>
      </div>

      <!-- Phone and Amount -->
      <div class="space-y-4 mb-5">
        <p><label for="phone">Phone</label></p>
        <input type="text" name="phone" id="phone" class="place-style rounded-lg h-14 w-full lg:w-3/4" maxlength="11" />
      </div>
      
      <div class="space-y-4 mb-5">
        <p><label for="amount">Amount</label></p>
        <input type="text" value="0" name="amount" id="amount" class="place-style rounded-sm h-14 w-full lg:w-3/4" readonly/>
      </div>
      
      <!-- Discount Information -->
      <div class="space-y-4 mb-5">
        <p><label for="amountToPay">Discount of (@if(Auth::user()->account_type == 1) -0.5% @else -1% @endif)</label></p>
      </div>

      <!-- Submit Button -->
      <div>
        <button onclick="showConfirmation(event)" class="bg-blue-990 text-white text-2xl font-bold rounded-lg px-20 py-6 cursor-pointer">Buy Now</button>
      </div>
      <button type="submit" id="submitButton" name="submit" style="display: none;"></button>
    </form>  
  </div>
</div>

<script>
    function showConfirmation(event) {
        event.preventDefault();
        const networkName = document.getElementById('cableName').options[document.getElementById('cableName').selectedIndex].text;
        const smartcardNumber = document.getElementById('billerCode').value;
        const subscriptionPlans = document.getElementById('subscriptionPlans');
        const selectedPlan = subscriptionPlans.options[subscriptionPlans.selectedIndex];

        if (!selectedPlan || selectedPlan.disabled) {
            alert("Please select a plan to continue.");
            return;
        }
        if (!smartcardNumber) {
            alert("Please enter your Smartcard / IUC Number.");
            return;
        }

        const planDetails = selectedPlan.text;
        const amountNumber = document.getElementById('amount').value;
        document.getElementById('amountNumber').innerText = parseFloat(amountNumber).toLocaleString();
        document.getElementById('networkName').innerText = networkName;
        document.getElementById('planDetails').innerText = planDetails;
        
        document.getElementById('cookiesPopup').style.display = 'block';

        document.getElementById("confirmYes").addEventListener("click", function() {
            document.getElementById('cookiesPopup').style.display = 'none';
            document.getElementById("submitButton").click();
        });
        document.getElementById("confirmNo").addEventListener("click", function() {
            document.getElementById('cookiesPopup').style.display = 'none';
        });
    }
    
    document.addEventListener('DOMContentLoaded', function () {
    // Clear form fields on load
    document.getElementById('billerCode').value = '';
    document.getElementById('amount').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('customer').value = '';
    document.querySelector('.bg-blue-990').addEventListener('click', showConfirmation);

    document.getElementById('cableName').selectedIndex = 0;
    document.getElementById('subscriptionPlans').innerHTML = '<option selected disabled>-------</option>';
    document.getElementById('subscriptionPlansContainer').style.display = 'none';

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const getPlansUrl = "{{ url('user/utility/cable/get') }}/";
    const spinner = document.getElementById('spinner');
    const amountInput = document.getElementById('amount');
    const customerInput = document.getElementById('customer');

    // Helper function to fetch customer data
    function fetchCustomerData(cableType, smartCardNo) {
        return fetch("{{ url('/user/utility/cable/validate-smartcard') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ cableType, smartCardNo })
        })
        .then(response => response.json());
    }

    // Cable name selection event listener
    document.getElementById('cableName').addEventListener('change', function () {
        const selectedCableName = this.value;
        const subscriptionPlansContainer = document.getElementById('subscriptionPlansContainer');
        const subscriptionPlansSelect = document.getElementById('subscriptionPlans');
        amountInput.value = '';  // Reset amount field

        if (selectedCableName) {
            fetch(getPlansUrl + encodeURIComponent(selectedCableName))
                .then(response => response.json())
                .then(data => {
                    subscriptionPlansSelect.innerHTML = '<option selected disabled>-------</option>';
                    data.plans.forEach(plan => {
                        subscriptionPlansSelect.innerHTML += '<option value="' + plan.variation_code + '">' + plan.name + '</option>';
                    });
                    subscriptionPlansContainer.style.display = 'block';

                    subscriptionPlansSelect.addEventListener('change', function () {
                        const selectedPlan = data.plans.find(plan => plan.variation_code === this.value);
                        amountInput.value = selectedPlan ? selectedPlan.amount : '';
                        amountInput.readOnly = !!selectedPlan;
                    });
                });
        } else {
            subscriptionPlansContainer.style.display = 'none';
        }
    });

    // Smartcard validation event listener
    document.getElementById('billerCode').addEventListener('blur', function () {
        const smartCardNo = this.value;
        const cableType = document.getElementById('cableName').value;

        if (smartCardNo && cableType) {
            spinner.style.display = 'inline-flex';
            customerInput.value = ''; // Clear customer field while loading

            fetchCustomerData(cableType, smartCardNo)
                .then(data => {
                    spinner.style.display = 'none';

                    if (data.status && data.code === 200) {
                        customerInput.value = data.data.validate.customerName;
                    } else if (data.code === 424 && data.data.message === "Session Expired") {
                        alert("An error occurred while validating the smartcard. Please try again.");
                        customerInput.value = '';
                    } else {
                        alert('Invalid Smartcard Number.');
                        customerInput.value = '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    spinner.style.display = 'none';
                });
        }
    });
});
</script>

@include('user.partials.footer')