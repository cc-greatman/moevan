@include('user.partials.head')

@include('user.partials.navbar')

<div class="md:mt-72 mt:44">
      <div class="text-center text-2xl text-blue-990 font-bold mt-12 mb-8">
        Meter Token Number
      </div>

      <div
        class="bg-gray-300 text-blue-950 font-medium text-xl shadow-gray-600 shadow-md space-y-5 pt-7 mx-6 sm:mx-0 lg:ml-40 md:ml-14 lg:mr-40 md:mr-14 mb-10 pb-11"
      >
        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl"><strong>Token</strong>: {{ $data['token'] }}</p>
        </div>

        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl"><strong>Meter Number</strong>: {{ $data['meterNumber'] }}</p>
        </div>

        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl"><strong>Meter Type</strong>: {{ $data['meterType'] }}</p>
        </div>

        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl"><strong>Units</strong>: {{ $data['units'] ?? null }}</p>
        </div>

        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl"><strong>Amount</strong>: {{ $data['amount'] }}</p>
        </div>
        
        <div class="bg-white md:py-8 py-6">
          <p class="md:ml-10 ml-5 md:text-4xl text-xl"><strong>Phone</strong>: {{ $data['phone'] }}</p>
        </div>
        <div class="pt-10 m-4">
              <a href="{{ route('user.dashboard') }}" class="bg-blue-990 text-white text-2xl font-bold rounded-lg md:px-20 md:py-6 px-8 py-3 cursor-pointer"
                >Close</a>
        </div>
      </div>
    </div>

@include('user.partials.footer')
