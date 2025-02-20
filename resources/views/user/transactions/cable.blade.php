@include('user.partials.head')

@include('user.partials.navbar')

    <div class="text-center text-3xl text-blue-990 font-bold mt-12 px-20 mb-10">
      Your Account History And Activities.
    </div>

    <div
      class="bg-gray-300 shadow-gray-600 shadow-md space-y-5 pt-7 lg:ml-32 md:ml-12 lg:mr-32 md:mr-12 mb-10 pb-20"
    >
      <div
        class="flex justify-center text-4xl text-blue-950 font-medium pl-3 pr-3 md:pl-24 md:pr-14"
      >
        FILTER
      </div>

      <ul
        class="flex-container text-blue-950 text-xl font-medium md:mr-14 pl-3 pr-3 md:pl-24 md:pr-14 pb-12"
      >
        <li class="flex-item"><a href="{{ route('user.transactions.airtime.show') }}">Airtime payment</a></li>
        <li class="flex-item"><a href="{{ route('user.transactions.data.show') }}">Data plan</a></li>
        <li class="flex-item"><a href="{{ route('user.transactions.wallet.show') }}">Wallet Topup</a></li>
        <li class="flex-item"><a href="{{ route('user.transactions.electric.show') }}">Electric Bill Payment</a></li>
      </ul>

      <div class="bg-white px-5 pb-7">
        <p class="text-blue-990 md:text-5xl text-3xl font-medium py-9 pl-5">
          TV Subscription Payment History
        </p>

        <form action="{{ route('user.utility.cable.search.transactions') }}" method="GET" class="flex flex-col md:flex-row gap-3 my-6">
            @csrf
          <div class="flex">
              <input type="text" name="query" placeholder="Find specific transaction(s)" class="w-full md:w-96 px-3 h-10 rounded-l border-2 border-gray-200 focus:outline-none focus:border-blue-700">
          </div>
          <select id="pricingType" name="search_type" class="w-full h-10 border-2 border-gray-200 focus:outline-none focus:border-blue-700 text-gray-700 rounded px-2 md:px-3 py-0 md:py-1 tracking-wider">
            <option selected disabled>Select Mode</option>
            <option value="trx">Transaction ID</option>
            <option value="date">Date</option>
            <option value="amount">Amount</option>
          </select>
          <button type="submit" name="submit" class="bg-blue-700 text-white sm:text-sm text-xl rounded-sm px-2 md:px-6 py-2.5 md:py-1">Search</button>
          <a href="{{ route('user.transactions.cable.show') }}" class="py-2.5 px-6 rounded-sm sm:text-sm text-xl font-medium text-white text-center bg-red-600">Reset</a>
        </form>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-nowrap text-xl text-left rtl:text-right text-gray-500 ">
                <thead class="text-lg text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Transaction ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            TV Provider
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Package
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Type
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Smartcard/IUC Number
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Balance Before
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Amount
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Balance After
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Customer Phone
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cable as $cableTrx)
                    <tr class="text-lg odd:bg-white even:bg-gray-50 border-b-4 border-white">
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ \Carbon\Carbon::parse($cableTrx->created_at)->format('M. j, Y, g:i a') }}
                        </td>
                        <th scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900 whitespace-nowrap">
                          {{ $cableTrx->trx }}
                        </th>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ $cableTrx->providers->name }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                         {{ strtoupper($cableTrx->package) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            {{ $cableTrx->type }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            {{ $cableTrx->billers_code }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            ₦{{ number_format($cableTrx->pre_bal, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            ₦{{ number_format($cableTrx->amount, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            ₦{{ number_format($cableTrx->post_bal, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            {{ $cableTrx->phone }}
                        </td>
                        @if ($cableTrx->status == '1')
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          <span class="bg-green-100 text-green-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-green-400">Successful</span>
                        </td>
                        @elseif ($cableTrx->status == '2')
                            <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                              <span class="bg-orange-100 text-orange-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-orange-400">Processing</span>
                            </td>
                        @else
                            <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                              <span class="bg-red-100 text-red-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-red-400">Failed</span>
                            </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
            {{ $cable->links() }}
      </div>
    </div>

@include('user.partials.footer')
