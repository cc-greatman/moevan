@include('user.partials.head')

@include('user.partials.navbar')

    <!-- HTML Markup for Modal-->
    <div class="container" id="cookiesPopup">
        <div class="cookiesContents">
          <button class="close" id="confirmNo" type="button" style="margin-left: 240px;">✖ <br> <span>No Skip!</span></button>
          <img src="https://cdn-icons-png.flaticon.com/512/2014/2014825.png" alt="cookies-img" />
          <p>The Central Bank of Nigeria, released a circular recently, regarding virtual accounts issued to customers, as follows:
            <br>
            <strong><span style="font-size: 18px;">&middot;</span> Every virtual account(Personal Wallet) must be linked with either a Bank Verification Number (BVN) or National Identification Number (NIN).</strong>
            <br>
            <br>
            <strong><span style="font-size: 18px;">&middot;</span> Ensure Your Personal Details are the same as the one linked to your BVN/NIN</strong>
          </p>
          <button class="accept" id="confirmYes" type="submit">Proceed to KYC Verification</button>
        </div>
    </div>

   <div>
      <div class="text-center text-4xl text-blue-990 font-bold mt-12 mb-8">
        User Verification
      </div>

      <div class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 pl-3 pr-3 md:pl-12 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-16">
        <form action="{{ route("user.profile.kyc.perform") }}" method="post">
            @csrf
            <div>
                <div class="space-y-4 mb-5">
                    <p><label for="">ID Type</label></p>
                    <select class="place-style rounded-sm h-14 w-full lg:w-3/4" name="id_type" id="id_type" aria-label="Default select example" required>
                            <option selected="" disabled>-------</option>
                            <option value="nin">National Identification Number(NIN)</option>
                            <option value="bvn">Bank Verification Number</option>
                    </select>
                </div>

              <div class="space-y-4 mb-5">
                <p><label for="">ID Number</label></p>
                <input type="text" name="id_number" placeholder="Enter your BVN/NIN" class="place-style rounded-lg h-14 w-full lg:w-3/4" required/>
              </div>
            </div>

            <div class="mt-10">
              <button type="submit" name="submit" class="bg-blue-990 text-white text-3xl font-bold rounded-lg md:px-20 px-14 md:py-6 py-3 cursor-pointer"
                >Proceed</button>
            </div>
        </form>
      </div>
   </div>

   <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.getElementById('cookiesPopup').style.display = 'block'; // Hide modal by default

            // Handle "Yes" button click
            document.getElementById("confirmYes").addEventListener("click", function() {
                console.log("Yes button clicked"); // Debugging
                document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal after action
            });

            // Handle "No" button click
            document.getElementById("confirmNo").addEventListener("click", function() {
                console.log("No button clicked"); // Debugging
                document.getElementById('cookiesPopup').style.display = 'none'; // Hide modal
            });
        });
    </script>

@include('user.partials.footer')
