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
        <li class="flex-item"><a href="{{ route('user.transactions.cable.show') }}">Cable subscription</a></li>
        <li class="flex-item"><a href="{{ route('user.transactions.wallet.show') }}">Wallet Topup</a></li>
        <li class="flex-item"><a href="{{ route('user.transactions.electric.show') }}">Electric Bill Payment</a></li>
      </ul>



      <div class="bg-white px-5 pb-7">
        <p class="text-blue-990 md:text-5xl text-3xl font-medium py-9 pl-5">
          Wallet Transactions History
        </p>

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
                            Balance Before
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Amount
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Balance After
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Type
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Mode
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wallet as $trx)
                    <tr class="text-lg border-b-4 border-white">
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ \Carbon\Carbon::parse($trx->created_at)->format('M. j, Y, g:i a') }}
                        </td>
                        <th scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900 whitespace-nowrap">
                          {{ $trx->trx }}
                        </th>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            ₦{{ number_format($trx->pre_bal, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            ₦{{ number_format($trx->amount, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            ₦{{ number_format($trx->post_bal, 2) }}
                        </td>
                        @if ($trx->type === '+')
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          <span class="bg-green-100 text-green-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-green-400">Credit</span>
                        </td>
                        @else
                            <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                              <span class="bg-red-100 text-red-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-red-400">Debit</span>
                            </td>
                        @endif
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            {{ $trx->mode }}
                        </td>
                        @if ($trx->status == '1')
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          <span class="bg-green-100 text-green-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-green-400">Successful</span>
                        </td>
                        @elseif ($trx->status == '2')
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
        {{ $wallet->links() }}
      </div>

    @if($data)
      <div class="bg-white px-5 pb-7">
        <p class="text-blue-990 md:text-5xl text-3xl font-medium py-9 pl-5">
          Data History
        </p>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-nowrap text-xl text-left rtl:text-right text-gray-500 ">
                <thead class="text-lg text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Transaction ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Plan
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
                            Number
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                          Status
                      </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $dataTrx)
                    <tr class="text-lg odd:bg-white even:bg-gray-50 border-b-4 border-white">
                        <th scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900 whitespace-nowrap">
                          {{ $dataTrx->trx }}
                        </th>
                        @php
                        $plan = $dataTrx->dataPlan->network->name.' '.$dataTrx->dataPlan->size.' @ '.$dataTrx->dataPlan->validity.'Days';
                        @endphp
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ $plan }}
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
                            {{ $dataTrx->phone }}
                        </td>
                        <td scope="row" class="px-6 py- bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ \Carbon\Carbon::parse($dataTrx->created_at)->format('M. j, Y, g:i a') }}
                        </td>
                        @if (Auth::guard('web')->user()->account_type == '1')
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          <span class="bg-green-100 text-green-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-green-400">Successful</span>
                        </td>
                        @else
                            <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                              {{ $dataTrx->response }}
                            </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $data->links() }}
      </div>
    @endif

    @if($voice)
      <div class="bg-white px-5 pb-7">
        <p class="text-blue-990 md:text-5xl text-3xl font-medium py-9 pl-5">
          Airtime History
        </p>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-nowrap text-xl text-left rtl:text-right text-gray-500 ">
                <thead class="text-lg text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Transaction ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Network
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
                            Number
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                          Status
                      </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($voice as $voiceTrx)
                    <tr class="text-lg border-b-4 border-white">
                        <th scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900 whitespace-nowrap">
                          {{ $voiceTrx->trx }}
                        </th>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ $voiceTrx->network->name.' '.$voiceTrx->type }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          ₦{{ number_format($voiceTrx->pre_bal, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          ₦{{ number_format($voiceTrx->amount, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          ₦{{ number_format($voiceTrx->post_bal, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            {{ $voiceTrx->phone }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ \Carbon\Carbon::parse($voiceTrx->created_at)->format('M. j, Y, g:i a') }}
                        </td>
                        @if ($voiceTrx->status == '1')
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          <span class="bg-green-100 text-green-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-green-400">Successful</span>
                        </td>
                        @elseif ($voiceTrx->status == '2')
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
            {{ $voice->links() }}
      </div>
    @endif

    @if($electric)
      <div class="bg-white px-5 pb-7">
        <p class="text-blue-990 md:text-5xl text-3xl font-medium py-9 pl-5">
          Electricity Payment History
        </p>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-nowrap text-xl text-left rtl:text-right text-gray-500 ">
                <thead class="text-lg text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Transaction ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Token
                        </th>
                        <th scope="col" class="px-6 py-3">
                            units
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Meter Number
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Meter Type
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
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($electric as $electricTrx)
                    <tr class="text-lg border-b-4 border-white">
                        <th scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900 whitespace-nowrap">
                          {{ $electricTrx->trx }}
                        </th>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ $electricTrx->token }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ $electricTrx->units }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            {{ $electricTrx->meter_no }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            {{ $electricTrx->meter_type }}
                        </td>
                         <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            ₦{{ number_format($electricTrx->pre_bal, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            ₦{{ number_format($electricTrx->amount, 2) }}
                        </td>
                         <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            ₦{{ number_format($electricTrx->post_bal, 2) }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                            {{ $electricTrx->phone }}
                        </td>
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ \Carbon\Carbon::parse($electricTrx->created_at)->format('M. j, Y, g:i a') }}
                        </td>
                        @if ($electricTrx->status == '1')
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          <span class="bg-green-100 text-green-600 text-lg font-medium me-2 px-2.5 py-0.5 rounded border border-green-400">Successful</span>
                        </td>
                        @elseif ($electricTrx->status == '2')
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
            {{ $electric->links() }}
      </div>
    @endif

    @if($cable)
      <div class="bg-white px-5 pb-7">
        <p class="text-blue-990 md:text-5xl text-3xl font-medium py-9 pl-5">
          TV Subscription Payment History
        </p>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-nowrap text-xl text-left rtl:text-right text-gray-500 ">
                <thead class="text-lg text-gray-700 uppercase bg-gray-50">
                    <tr>
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
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cable as $cableTrx)
                    <tr class="text-lg odd:bg-white even:bg-gray-50 border-b-4 border-white">
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
                        <td scope="row" class="px-6 py-4 bg-gray-300 border-r-4 border-white font-bold text-gray-900">
                          {{ \Carbon\Carbon::parse($cableTrx->created_at)->format('M. j, Y, g:i a') }}
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
    @endif
    </div>

@include('user.partials.footer')
