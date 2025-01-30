@include('user.partials.head')

@include('user.partials.navbar')

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

      <div class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 pl-3 pr-3 mx-6 sm:mx-0 md:pl-12 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-16" style="margin-top: 50px;">
        <p class="text-xl md:text-xl font-medium" style="color: rgb(255, 0, 0) !important;">Fund your wallet through the following methods <br>
           ✅ 0818170354 - (Moevan Enterprise - GTBANK) <br>
           ✅ 8064324511 - (Chukwuma Moses Chukwunazaekpere- OPAY)
        </p>
        <p class="text-xl md:text-xl font-medium">
           1. MINIMUM DEPOSIT AMOUNT FOR THIS SERVICE IS NGN 100. <br>
           2. You can transfer to GTB using any bank <br> 3. Just make sure your bank generates sectional ID or Reference number BEFORE TRANSFER OF FUNDS. <br> 4. OPAY to OPAY only please 🙏 br
        </p>
        <form id="bitForm" method="POST" action="{{ route('user.top-up.fund.manual.perform') }}" enctype="multipart/form-data">
            @csrf
            <div>

              <div class="space-y-4 mb-5">
                <p><label for="">Select Bank to Pay to</label></p>
                <select type="text" class="place-style rounded-sm h-14 w-full lg:w-3/4" name="bank" id="bank" onchange="updateAccountNumber()">
                  <option selected="" disabled>-------</option>
                  <option value="GTB">GTB - Moevan Enterprise</option>
                  <option value="OPAY">OPAY - Chukwuma Moses</option>
                </select>
              </div>

                <!-- Container for displaying the account number -->
                <div id="accountNumberContainer" class="hidden space-y-2 mb-5">
                    <p><label for="amount">Account Number</label></p>
                    <input type="text" id="accountNumber" name="accountNo" class="place-style rounded-sm h-14 w-full lg:w-3/4" readonly/>
                    <button onclick="copyAccountNumber()" class="bg-blue-990 rounded-lg text-white px-4 py-2 rounded-md hover:bg-blue-600"> Copy </button>
                </div>

              <div class="space-y-4 mb-5">
                    <p><label for="amount">Amount</label></p>
                    <input type="text" placeholder="Amount Sent to Bank" name="amount" id="amount" class="place-style rounded-sm h-14 w-full lg:w-3/4" />
              </div>

              <div class="space-y-4 mb-5">
                    <p><label for="amount">Session ID or Reference Number</label></p>
                    <input type="text" placeholder="Session ID" name="reference" id="reference" class="place-style rounded-sm h-14 w-full lg:w-3/4" />
              </div>

            </div>

            <div class="">
              <button name="submit" type="submit" class="bg-blue-990 text-white text-2xl font-bold rounded-lg md:px-20 md:py-6 px-8 py-3 cursor-pointer">Confirm Top-Up</button>
            </div>
        </form>
      </div>
    </div>

    <script>
        // Map of bank names to account numbers
        const bankAccountNumbers = {
          GTB: "0818170354",
          OPAY: "8064324511",
        };

        // Update the account number when a bank is selected
        function updateAccountNumber() {
          const bankSelect = document.getElementById("bank");
          const accountNumberContainer = document.getElementById(
            "accountNumberContainer"
          );
          const accountNumberInput = document.getElementById("accountNumber");

          const selectedBank = bankSelect.value;

          // If a valid bank is selected, update the account number
          if (bankAccountNumbers[selectedBank]) {
            accountNumberInput.value = bankAccountNumbers[selectedBank];
            accountNumberContainer.classList.remove("hidden");
          } else {
            accountNumberContainer.classList.add("hidden");
          }
        }

        // Copy the account number to clipboard
        function copyAccountNumber() {
          const accountNumberInput = document.getElementById("accountNumber");
          accountNumberInput.select();
          document.execCommand("copy");

          // Show a temporary alert
          alert("Account number copied to clipboard!");
        }
      </script>

      <style>
        .hidden {
            display: none;
        }
      </style>

@include('user.partials.footer')
