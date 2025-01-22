document.addEventListener('DOMContentLoaded', function () {
    // Event listener for network change
    document.getElementById('network').addEventListener('change', function () {
        // Get selected network value
        var selectedNetwork = this.value;

        // Hide/show DataType container based on network
        var dataTypeContainer = document.getElementById('dataTypeContainer');
        if (selectedNetwork === '1') {
            dataTypeContainer.style.display = 'block';
        } else {
            dataTypeContainer.style.display = 'none';
        }

        // Fetch and populate Data Types if network is MTN
        if (selectedNetwork === '1') {
            fetchDataTypes();
        }

        // Fetch and populate Data Plans based on selected network
        fetchDataPlans(selectedNetwork);
    });

    // Event listener for data type change (only applicable for MTN)
    document.getElementById('dataType').addEventListener('change', function () {
        // Fetch and populate Data Plans based on selected data type
        fetchDataPlans('1', this.value);
    });

    // Function to fetch and populate Data Types
    function fetchDataTypes() {
        // Assuming you have a route to fetch data types (e.g., /user/data/types)
        fetch("{{ route('user.data.types') }}")
            .then(response => response.json())
            .then(dataTypes => {
                var dataTypeSelect = document.getElementById('dataType');
                dataTypeSelect.innerHTML = ''; // Clear existing options

                dataTypes.forEach(dataType => {
                    var option = document.createElement('option');
                    option.value = dataType.id;
                    option.text = dataType.name;
                    dataTypeSelect.appendChild(option);
                });
            });
    }

    // Function to fetch and populate Data Plans
    function fetchDataPlans(network, dataType = null) {
        // Assuming you have a named route for fetching data plans (e.g., 'user.data.plans')
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

                dataPlans.forEach(dataPlan => {
                    var option = document.createElement('option');
                    var networkName = dataPlan.network ? dataPlan.network.name : 'Unknown Network';
                    var dataTypeName = dataPlan.data_type.name;

                    option.value = dataPlan.data_id;
                    option.text = dataTypeName + ' - ' + dataPlan.size + ' - ₦' + dataPlan.price + ' @ ' + dataPlan.validity + ' days';
                    planSelect.appendChild(option);
                });

                // Event listener for plan selection
                planSelect.addEventListener('change', function () {
                    // Find the selected dataPlan based on the value
                    var selectedPlan = dataPlans.find(plan => plan.data_id == this.value);

                    // Update the value of the amount input
                    amountInput.value = selectedPlan ? selectedPlan.price : '';
                });
            });
    }
});
