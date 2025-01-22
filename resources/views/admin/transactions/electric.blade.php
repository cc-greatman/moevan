@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

    <!--APP-CONTENT START-->
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- PAGE-HEADER -->
            <div class="page-header">
              <h1 class="page-title my-auto">Electricity Transaction History</h1>
              <div>
                    <a href="{{ route('admin.export.electric', ['filename' => 'Electric_Transactions']) }}" class="btn btn-primary mb-1">Export to CSV</a>
              </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- Data Transactions -->
                <!-- Start:: row-1 -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    Electricity Payment History
                                </div>
                            </div>
                            <div class="card-body">
                                @if (auth()->guard('admin')->user())
                                <form action="{{ route('admin.search.electric') }}" method="GET" class="row gy-2 gx-3 align-items-center mb-4">
                                    @csrf
                                    <div class="col-auto">
                                        <input type="text" name="query" placeholder="Find specific User" class="form-control">
                                    </div>
                                    <div class="col-auto">
                                        <select id="pricingType" name="search_type" class="form-select">
                                                <option selected disabled>Select Mode</option>
                                                <option value="trx">Transaction ID</option>
                                                <option value="username">User</option>
                                                <option value="meter_no">Meter Number</option>
                                                <option value="meter_type">Meter Type</option>
                                                <option value="units">Units</option>
                                                <option value="phone">Phone</option>
                                                <option value="date">Date</option>
                                                <option value="amount">Amount</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" name="submit" class="btn btn-primary">Search</button>
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ route('admin.transactions.electric.view') }}" class="btn btn-danger">Reset</a>
                                    </div>
                                </form>
                                @endif
                                <div class="table-responsive">
                                    <table class="table text-nowrap">
                                        <thead>
                                            <tr>
                                                <th scope="col">Transaction ID</th>
                                                <th scope="col">User</th>
                                                <th scope="col">Meter Number</th>
                                                <th scope="col">Meter Type</th>
                                                <th scope="col">Token</th>
                                                <th scope="col">Units</th>
                                                <th scope="col">Balance Before</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Balance After</th>
                                                <th scope="col">Customer Phone</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($electric as $electricTrx)
                                                <tr>
                                                    <td>{{ $electricTrx->trx }}</td>
                                                    <td>{{ $electricTrx->user->username }}</td>
                                                    <td>{{ $electricTrx->meter_no }}</td>
                                                    <td>{{ $electricTrx->meter_type }}</td>
                                                    <td>{{ $electricTrx->token }}</td>
                                                    <td>{{ $electricTrx->units }}</td>
                                                    <td>₦{{ number_format($electricTrx->pre_bal, 2) }}</td>
                                                    <td>₦{{ number_format($electricTrx->amount, 2) }}</td>
                                                    <td>₦{{ number_format($electricTrx->post_bal, 2) }}</td>
                                                    <td>{{ $electricTrx->phone }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($electricTrx->created_at)->format('M. j, Y, g:i a') }}
                                                    </td>
                                                    @if ($electricTrx->status == '1')
                                                        <td><span class="badge bg-outline-success">Completed</span></td>
                                                    @elseif ($electricTrx->status == '2')
                                                        <td><span class="badge bg-outline-warning">Processing</span></td>
                                                    @else
                                                        <td><span class="badge bg-outline-danger">Failed</span></td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center">
                                {{ $electric->links('vendor.pagination.default') }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End:: row-1 -->
        </div>
    </div>

@include('admin.partials.footer')
