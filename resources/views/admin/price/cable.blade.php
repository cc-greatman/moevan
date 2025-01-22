@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

<!--APP-CONTENT START-->
            <div class="main-content app-content">
                <div class="container-fluid">

                    <!-- PAGE-HEADER -->
                    <div class="page-header">
                      <h1 class="page-title my-auto">Buy Cable Subscription</h1>
                      @include('user.messages.success_alert')
                    </div>
                    <!-- PAGE-HEADER END -->


                    <!-- Start:: row-1 -->
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                        Cable Sub
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.price.cable.edit.perform') }}" method="post" autocomplete="off">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="form-text" class="form-label fs-14 text-dark">Cable Name</label>
                                            <select class="form-select" id="cableName" name="cableName" aria-label="Default select example">
                                                <option selected="" disabled>-------</option>
                                                @foreach ($providers as $network)
                                                    <option value="{{ $network->name }}">{{ $network->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3" id="subscriptionPlansContainer">
                                            <label for="form-text" class="form-label fs-14 text-dark">Plans</label>
                                            <select class="form-select" id="subscriptionPlans" name="variation_code" aria-label="Default select example">
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="price" class="form-label fs-14 text-dark">Amount</label>
                                            <input type="text" class="form-control" id="amount" name="amount" placeholder="">
                                        </div>
                                        <button class="btn btn-primary" name="submit" type="submit">Edit Price</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End:: row-1 -->
                </div>
            </div>
            <!--APP-CONTENT CLOSE-->

            <script>
                var getPlansUrl = "{{ url('user/utility/cable/get') }}/";
                var merchantVerifyUrl = "https://api-service.vtpass.com/api/merchant-verify";
                var selectedCableName;
                var amountInput;

                document.getElementById('cableName').addEventListener('change', function () {
                    selectedCableName = this.value;
                    var subscriptionPlansContainer = document.getElementById('subscriptionPlansContainer');
                    var subscriptionPlansSelect = document.getElementById('subscriptionPlans');
                    amountInput = document.getElementById('amount');
                    var renewalContainer = document.getElementById('renewalContainer');
                    var renewalDropdown = document.getElementById('changeContainer');

                    // Reset the amount input
                    amountInput.value = '';
                    amountInput.removeAttribute('readonly');

                    if (selectedCableName) {
                        var url = getPlansUrl + encodeURIComponent(selectedCableName);
                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                subscriptionPlansSelect.innerHTML = '<option selected disabled>-------</option>';
                                data.plans.forEach(plan => {
                                    subscriptionPlansSelect.innerHTML += '<option value="' + plan.variation_code + '">' + plan.name + '</option>';
                                });

                                renewalContainer.style.display = selectedCableName === 'DSTV' ? 'block' : 'none';

                                // Show or hide Renewal/Bouquet Purchase dropdown based on cable name
                                renewalDropdown.style.display = selectedCableName === 'STARTIMES' ? 'none' : 'block';

                                subscriptionPlansSelect.addEventListener('change', function () {
                                    // Find the selected plan based on the value
                                    var selectedPlan = data.plans.find(plan => plan.variation_code == this.value);

                                    // Update the value and readonly attribute of the amount input
                                    amountInput.value = selectedPlan ? selectedPlan.amount : '';
                                    amountInput.readOnly = selectedPlan ? true : false;
                                });

                                subscriptionPlansContainer.style.display = 'block';
                            });
                    } else {
                        subscriptionPlansContainer.style.display = 'none';
                    }
                });

                function formatNumberWithCommas(number) {
                    return Number(number).toLocaleString();
                }

                // Event listener for Renewal/Bouquet Purchase selection
                document.getElementById('change').addEventListener('change', function () {
                    var selectedChange = this.value;
                    var renewalContainer = document.getElementById('renewalContainer');

                    if (selectedChange === 'renewal') {
                        // Run API check for renewal
                        var smartcardNumberInput = document.getElementById('form-text');
                        var smartcardNumber = smartcardNumberInput.value;

                        // Make the API request to verify the smartcard number for renewal
                        fetch(merchantVerifyUrl, {
                            method: 'POST',
                            headers: {
                                'api-key': 'b382ea284d8c05da64a2fc6a1002b783',
                                'secret-key': 'SK_42642f8ea1f5e158890b5dfd9dfcac87ee314158597',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                billersCode: smartcardNumber,
                                serviceID: selectedCableName,
                            }),
                        })
                            .then(response => response.json())
                            .then(data => {
                                // Display the Renewal information
                                var renewalContent = 'Current Bouquet: ' + data.content.Current_Bouquet + '<br>' +
                                                    'Renewal Amount: ₦' + formatNumberWithCommas(data.content.Renewal_Amount);

                                renewalContainer.innerHTML = renewalContent;

                                // Create a hidden input field dynamically
                                var hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'code';
                                hiddenInput.value = data.content.Current_Bouquet_Code;

                                // Prepend the hidden input to the form
                                var form = document.querySelector('form');
                                form.insertBefore(hiddenInput, form.firstChild);

                                // Update the value and readonly attribute of the amount input with Renewal Amount
                                amountInput.value = data.content.Renewal_Amount;
                            })
                            .catch(error => console.error('Error verifying smartcard number for renewal:', error));

                        // Show the Renewal container
                        renewalContainer.style.display = 'block';

                        // Hide the subscription plans container
                        subscriptionPlansContainer.style.display = 'none';
                    } else {
                        // Hide the Renewal container
                        renewalContainer.style.display = 'none';
                    }
                });

                document.getElementById('form-text').addEventListener('blur', function () {
                    var smartcardNumber = this.value;

                    fetch(merchantVerifyUrl, {
                        method: 'POST',
                        headers: {
                            'api-key': 'b382ea284d8c05da64a2fc6a1002b783',
                            'secret-key': 'SK_42642f8ea1f5e158890b5dfd9dfcac87ee314158597',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            billersCode: smartcardNumber,
                            serviceID: selectedCableName,
                        }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            var renewalContent = 'Current Bouquet: ' + data.content.Current_Bouquet + '<br>' +
                                                 'Renewal Amount: ₦' + formatNumberWithCommas(data.content.Renewal_Amount);

                            renewalContainer.innerHTML = renewalContent;

                            amountInput.value = data.content.Renewal_Amount;
                        });
                });
            </script>


@include('admin.partials.footer')
