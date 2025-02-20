@include('user.partials.head')

@include('user.partials.navbar')

    <div class="text-center text-3xl text-blue-990 font-bold mt-12 px-20 mb-10">
        Results for {{ $query }}.
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
        <li class="flex-item"><a href="{{ route('user.transactions.cable.show') }}">Cable subscription</a></li>
        <li class="flex-item"><a href="{{ route('user.transactions.wallet.show') }}">Wallet Topup</a></li>
        <li class="flex-item"><a href="{{ route('user.transactions.electric.show') }}">Electric Bill Payment</a></li>
      </ul>

      <div class="bg-white px-5 pb-7">
        <p class="text-blue-990 md:text-5xl text-3xl font-medium py-9 pl-5">
          Search Results for {{ $query }}
        </p>

        <form action="{{ route('user.data.search.transactions') }}" method="GET" class="flex flex-col md:flex-row gap-3 my-6">
            @csrf
          <div class="flex">
              <input type="text" name="query" placeholder="Find specific transaction(s)" class="w-full md:w-96 px-3 h-10 rounded-l border-2 border-gray-200 focus:outline-none focus:border-blue-700">
          </div>
          <select id="pricingType" name="search_type" class="w-full h-10 border-2 border-gray-200 focus:outline-none focus:border-blue-700 text-gray-700 rounded px-2 md:px-3 py-0 md:py-1 tracking-wider">
            <option selected disabled>Select Mode</option>
            <option value="trx">Transaction ID</option>
            <option value="network">Network</option>
            <option value="phone">Phone</option>
            <option value="date">Date</option>
            <option value="amount">Amount</option>
            <option value="status">Status</option>
          </select>
          <button type="submit" name="submit" class="bg-blue-700 text-white sm:text-sm text-xl rounded-sm px-2 md:px-6 py-2.5 md:py-1">Search</button>
          <a href="{{ route('user.transactions.data.show') }}" class="py-2.5 px-6 rounded-sm sm:text-sm text-xl font-medium text-white text-center bg-red-600">Reset</a>
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
                            Plan
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Phone
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Balance Before
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Plan Amount
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Balance After
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $dataTrx)
                    <tr class="text-lg odd:bg-white even:bg-gray-50 border-b-4 border-white">
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($dataTrx->created_at)->format('M. j, Y, g:i a') }}
                        </td>
                        <th scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900 whitespace-nowrap">
                          {{ $dataTrx->trx }}
                        </th>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">{{ optional(optional($dataTrx->dataPlan)->network)->name ?? 'Unknown Network' }} {{ optional($dataTrx->dataPlan)->size ?? 'N/A' }} @ {{ optional($dataTrx->dataPlan)->validity ?? 'N/A' }} Days</td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ $dataTrx->phone }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          ₦{{ number_format($dataTrx->pre_bal, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          ₦{{ number_format($dataTrx->amount, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          ₦{{ number_format($dataTrx->post_bal, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($dataTrx->created_at)->format('M. j, Y, g:i a') }}
                        </td>
                          @if ($dataTrx->status == '1')
                          <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            <span class="bg-green-100 text-green-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-green-400">Successful</span>
                          </td>
                          @elseif ($dataTrx->status == '2')
                              <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                                <span class="bg-orange-100 text-orange-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-orange-400">Processing</span>
                              </td>
                          @else
                              <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                                <span class="bg-red-100 text-red-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-red-400">Failed</span>
                              </td>
                          @endif
                        <td>
                            <div class="text-center">
                              <a href="{{ route('user.receipt.data.show', $dataTrx->id) }}" target="_blank" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                View Receipt
                              </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="text-lg border-b-4 border-white">
                        <td colspan="100" style="text-align: center; font-weight: 600; padding: 15px;">No Results Found!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
      </div>

</div>

@include('user.partials.footer')
