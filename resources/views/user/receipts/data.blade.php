@include('user.partials.head')

@include('user.partials.navbar')
<style>
    @media screen and (max-width: 765px) {
        .text {
            font-size: 12px;
        }
    }
</style>

<!-- Invoice -->
<div class="max-w-[85rem] px-4 sm:px-6 lg:px-8 mx-auto my-4 sm:my-10">
  <div class="sm:w-11/12 lg:w-3/4 mx-auto">
        <nav class="flex my-8" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
              <a href="{{ route('user.dashboard') }}" class="text inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                  <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                </svg>
                Dashboard
              </a>
            </li>
            <li>
              <div class="flex items-center">
                <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                </svg>
                <a href="{{ route('user.transactions.data.show') }}" class="text ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">Data Txns</a>
              </div>
            </li>
            <li aria-current="page">
              <div class="flex items-center">
                <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                </svg>
                <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">Receipt</span>
              </div>
            </li>
          </ol>
        </nav>
    <!-- Card -->
    <div id="invoice-to-print" class="flex flex-col p-4 sm:p-10 bg-white shadow-md rounded-xl">
      <!-- Grid -->
      <div class="flex justify-between">
        <div>
          <img src="{{ asset("../frontend/img/logo/moeblue2.png") }}" alt="" class="rounded w-44 h-12" />
        </div>
        <!-- Col -->

        <div class="text-end">
          <h2 class="text-2xl md:text-3xl font-semibold text-gray-800">Invoice #</h2>
          <span class="mt-1 block text-gray-500">{{ $data->trx }}</span>

          <address class="mt-4 not-italic text-gray-800">
            <a href="https://moevan.com">moevan.com</a><br>
            <a href="mailto:support@moevan.com">support@moevan.com</a><br>
            <a href="tel:+2349027205636">+234 902 720 5635</a><br>
          </address>
        </div>
        <!-- Col -->
      </div>
      <!-- End Grid -->

      <!-- Grid -->
      <div class="grid sm:grid-cols-2 gap-3 mt-8 mb-8">
        <div>
          <h3 class="text-lg font-semibold text-gray-800">Bill to:</h3>
          <h3 class="text-lg font-semibold text-gray-800">{{ $user->username }}</h3>
        </div>
        <!-- Col -->

        <div class="sm:text-end space-y-2">
          <!-- Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">
            <dl class="grid sm:grid-cols-5 gap-x-3">
              <dt class="col-span-3 text-[14px] font-semibold text-gray-800">Invoice date:</dt>
              <dd class="col-span-2 text-[14px] text-gray-500">{{ \Carbon\Carbon::parse($data->created_at)->format('M. j, Y, g:i a') }}</dd>
            </dl>
            <dl class="grid sm:grid-cols-5 gap-x-3">
              <dt class="col-span-3 text-[14px] font-semibold text-gray-800">Status:</dt>
              <dd class="col-span-2  text-[14px] text-start @if($data->status == '1') text-green-800 font-medium me-2 rounded @elseif($data->status =='2')  text-yellow-800 font-medium me-2 rounded @else text-red-800 font-medium me-2 rounded @endif"> @if($data->status == '1') Successful @elseif($data->status == '2') Pending  @else Failed @endif</dd>
            </dl>
          </div>
          <!-- End Grid -->
        </div>
        <!-- Col -->
      </div>
      <!-- End Grid -->

      <!-- Table -->
      <div class="mt-6">
            <div class="border border-gray-200 p-4 rounded-lg space-y-4">
                <!-- Table Header -->
                <div class="hidden sm:grid sm:grid-cols-2">
                  <div class="text-xs font-medium text-gray-500 uppercase">Summary</div>
                </div>
            
                <div class="hidden sm:block border-b border-gray-200"></div>
            
                @php
                  $plan = $data->dataPlan->network->name.' '.$data->dataPlan->size.' @ '.$data->dataPlan->validity.'Days';
                @endphp
            
                <!-- Row 1: Product -->
                <div class="grid grid-cols-2 gap-2 py-4">
                  <div>
                    <p class="text font-medium text-gray-800">Product</p>
                  </div>
                  <div class="text-end">
                    <p class="text text-gray-800">{{ $plan }}</p>
                  </div>
                </div>
                
                <div class="grid grid-cols-2 gap-2 py-4">
                  <div>
                    <p class="text font-medium text-gray-800">Phone</p>
                  </div>
                  <div class="text-end">
                    <p class="text text-gray-800">{{ $data->phone }}</p>
                  </div>
                </div>
            
                <div class="sm:hidden border-b border-gray-200"></div>
            
                <!-- Row 2: Response -->
                <div class="grid grid-cols-2 gap-2 py-4">
                  <div>
                    <p class="text font-medium text-gray-800">Response</p>
                  </div>
                  <div class="text-end">
                    <p class="text text-gray-800">{!! $data->response !!}</p>
                  </div>
                </div>
            
                <div class="sm:hidden border-b border-gray-200"></div>
            
                <!-- Row 3: Amount -->
                <div class="grid grid-cols-2 gap-2 py-4">
                  <div>
                    <p class="text font-medium text-gray-800">Amount Paid</p>
                  </div>
                  <div class="text-end">
                    <p class="text text-gray-800">₦{{ number_format($data->amount, 2) }}</p>
                  </div>
                </div>
            </div>
      </div>
      <!-- End Table -->


      <!-- Flex -->
      <div class="mt-8 flex sm:justify-end">
        <div class="w-full max-w-2xl sm:text-end space-y-2">
          <!-- Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">
            <dl class="grid sm:grid-cols-5 gap-x-3">
              <dt class="col-span-3 font-semibold text-gray-800">Total:</dt>
              <dd class="col-span-2 text-gray-500">₦{{ number_format($data->amount, 2) }}</dd>
            </dl>
          </div>
          <!-- End Grid -->
        </div>
      </div>
      <!-- End Flex -->

      <div class="mt-8 sm:mt-12">
        <h4 class="text-lg font-semibold text-gray-800">Thank you!</h4>
        <p class="text text-gray-500">If you have any questions concerning this invoice, use the following contact information:</p>
        <div class="mt-2">
          <p class="text block text-sm font-medium text-gray-800"><a href="mailto:support@moevan.com">support@moevan.com</a></p>
          <p class="text block text-sm font-medium text-gray-800"><a href="tel:+2349027205636">+234 902 720 5635</a></p>
        </div>
      </div>

      <p class="text mt-5 text-sm text-gray-500">© 2024 Moevan.</p>
    </div>
    <!-- End Card -->

    <!-- Buttons -->
    <div class="mt-6 flex justify-end gap-x-3">
      <a class="text py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-gray-50" href="javascript:void(0);" onclick="downloadAsImage()">
      <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
        Invoice PNG
      </a>
      <a class="text py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#001D82] text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" href="javascript:void(0);" onclick="printInvoice()">
        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Print
      </a>
    </div>
    <!-- End Buttons -->
  </div>
</div>
<!-- End Invoice -->

<!-- Modal and Print Handling Scripts -->
<script>
    function printInvoice() {
        // Get the content of the invoice
        const printContent = document.getElementById('invoice-to-print').innerHTML;
        
        // Create a new iframe
        const printWindow = window.open('', '', 'width=800,height=600');
        
        // Write the content into the iframe's document
        printWindow.document.write(`
          <html>
            <head>
              <title>Print Invoice</title>
              <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"><\/script>
              <style>
                @media print {
                  body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: #f8f8f8;
                  }
                  .max-w-[85rem] {
                    max-width: 100%;
                  }
                  .bg-white {
                    background-color: white;
                  }
                  /* Add any additional print-specific styles */
                }
              </style>
            </head>
            <body>
              ${printContent}
            </body>
          </html>
        `);
    
        // Close the document to ensure it is fully loaded
        printWindow.document.close();
    
        // Trigger the print function once the content is loaded
        printWindow.onload = function() {
          printWindow.focus(); // Ensure the window is focused before printing
          printWindow.print(); // Trigger the print
          printWindow.close(); // Close the print window after printing
        };
    }
    
    // Download Invoice as Image
    function downloadAsImage() {
        
        const element = document.getElementById('invoice-to-print');
        
        // Use html2canvas to capture the div content
        html2canvas(element).then(function(canvas) {
          // Create a link element to trigger download
          const link = document.createElement('a');
          link.download = 'invoice.png';
          link.href = canvas.toDataURL('image/png');
          
          // Trigger the download
          link.click();
        });
    }
</script>

@include('user.partials.footer')
