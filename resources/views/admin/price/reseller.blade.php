@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

<!--APP-CONTENT START-->
            <div class="main-content app-content">
                <div class="container-fluid">
                    <!-- PAGE-HEADER -->
                    <div class="page-header">
                      <h1 class="page-title my-auto">Data Price for Resellers Edit</h1>
                      @include('user.messages.success_alert')
                    </div>
                    <!-- PAGE-HEADER END -->


                    <!-- Start:: row-1 -->
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                        Data Plan Price Edit
                                    </div>
                                </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.price.data.edit.reseller.perform') }}" method="post">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="form-text" class="form-label fs-14 text-dark">Select Network</label>
                                                <select class="form-select" id="network" name="network_id" aria-label="Default select example">
                                                    <option selected="" disabled>-------
                                                    </option>
                                                    @foreach ($networks as $line)
                                                        <option value="{{ $line->network_id }}">{{ $line->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3" style="display: none;" id="dataTypeContainer">
                                                <label for="form-text" class="form-label fs-14 text-dark">Data Type</label>
                                                <select class="form-select" id="dataType" name="dataType" aria-label="Default select example">
                                                    <option selected="" disabled>-------</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="form-text" class="form-label fs-14 text-dark">Select Plan</label>
                                                <select class="form-select" name="plan" id="plan" aria-label="Default select example">
                                                    <option selected="" disabled>-------</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="form-password" class="form-label fs-14 text-dark">Amount</label>
                                                <input type="text" class="form-control" id="amount" name="amount" placeholder="">
                                            </div>
                                            <button class="btn btn-primary" type="submit" name="submit">Edit Price</button>
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
    document.addEventListener('DOMContentLoaded', function () {
    // Event listener for network change
    document.getElementById('network').addEventListener('change', function () {
        // Get selected network value
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
        // Clear Data Plan options
        clearPlanOptions();

        // Fetch and populate Data Plans based on selected data type
        fetchDataPlans(document.getElementById('network').value, this.value);
    });

    // Function to fetch and populate Data Types
    function fetchDataTypes(network) {
        // Assuming you have a route to fetch data types (e.g., /user/data/types)
        var url = "{{ route('admin.data.types.reseller') }}?network_id=" + network;

        fetch(url)
            .then(response => response.json())
            .then(dataTypes => {
                var dataTypeSelect = document.getElementById('dataType');
                dataTypeSelect.innerHTML = ''; // Clear existing options

                // Add "Select Data Type" option
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
                    // Handle case where no data types are available
                    dataTypeSelect.innerHTML = '<option value="">No Data Types Available</option>';
                }
            });
    }

    // Function to fetch and populate Data Plans
    function fetchDataPlans(network, dataType = null) {
        // Assuming you have a named route for fetching data plans (e.g., 'user.data.plans')
        var url = "{{ route('admin.data.plans') }}?network_id=" + network;

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

                // Add "Select Data Plan" option
                var defaultOption = document.createElement('option');
                defaultOption.value = "";
                defaultOption.text = "Select Data Plan";
                planSelect.appendChild(defaultOption);

                dataPlans.forEach(dataPlan => {
                    var option = document.createElement('option');
                    var networkName = dataPlan.network ? dataPlan.network.name : 'Unknown Network';
                    var dataTypeName = dataPlan.data_type.name;

                    option.value = dataPlan.data_id;
                    option.text = dataTypeName + ' - ' + dataPlan.size + ' - ₦' + dataPlan.r_price + ' @ ' + dataPlan.validity + ' days';
                    planSelect.appendChild(option);
                });

                // Event listener for plan selection
                planSelect.addEventListener('change', function () {
                    // Find the selected dataPlan based on the value
                    var selectedPlan = dataPlans.find(plan => plan.data_id == this.value);

                    // Update the value of the amount input
                    amountInput.value = selectedPlan ? selectedPlan.r_price : '';

                    // Make the amount input non-editable
                });
            });
    }

    // Function to clear Data Plan options and reset amount input
    function clearPlanOptions() {
        var planSelect = document.getElementById('plan');
        var amountInput = document.getElementById('amount');
        planSelect.innerHTML = ''; // Clear existing options

        // Add "Select Data Plan" option
        var defaultOption = document.createElement('option');
        defaultOption.value = "";
        defaultOption.text = "Select Data Plan";
        planSelect.appendChild(defaultOption);

        // Clear amount input value and make it editable
        amountInput.value = '';
        amountInput.readOnly = false;
    }
});

</script>

@include('admin.partials.footer')

