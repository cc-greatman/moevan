@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

    <!--APP-CONTENT START-->
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- PAGE-HEADER -->
            <div class="page-header">
              <h1 class="page-title my-auto">Manual Funding Transactions History</h1>
              <div>
                    <a href="{{ route('admin.export.wallet', ['filename' => 'Wallet_Transactions']) }}" class="btn btn-primary mb-1">Export to CSV</a>
              </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- Airtime Transactions -->
                <!-- Start:: row-1 -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header justify-content-between">
                                <div class="card-title">
                                    Manual Transactions History
                                </div>
                            </div>
                            <div class="card-body">
                                @if (auth()->guard('admin')->user())
                                <form action="{{ route('admin.search.wallet') }}" method="GET" class="row gy-2 gx-3 align-items-center mb-4">
                                    @csrf
                                    <div class="col-auto">
                                        <input type="text" name="query" placeholder="Find specific User" class="form-control">
                                    </div>
                                    <div class="col-auto">
                                        <select id="pricingType" name="search_type" class="form-select">
                                            <option selected disabled>Select Mode</option>
                                            <option value="trx">Transaction ID</option>
                                            <option value="username">User</option>
                                            <option value="mode">Mode</option>
                                            <option value="type">Type</option>
                                            <option value="date">Date</option>
                                            <option value="amount">Amount</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" name="submit" class="btn btn-primary">Search</button>
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ route('admin.transactions.manual.transactions') }}" class="btn btn-danger">Reset</a>
                                    </div>
                                </form>
                                @endif
                                <div class="table-responsive">
                                    <table class="table text-nowrap">
                                        <thead>
                                            <tr>
                                                <th scope="col">Transaction ID</th>
                                                <th scope="col">User</th>
                                                <th scope="col">Balance Before</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Balance After</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($wallet as $trx)
                                                <tr>
                                                    <td>{{ $trx->trx }}</td>
                                                    <td>{{ optional($trx->user)->username }}</td>
                                                    <td>₦{{ number_format($trx->pre_bal, 2) }}</td>
                                                    <td>₦{{ number_format($trx->amount, 2) }}</td>
                                                    <td>₦{{ number_format($trx->post_bal, 2) }}</td>
                                                    <td><span class="badge bg-outline-success">Credit</span></td>
                                                    <td>{{ \Carbon\Carbon::parse($trx->created_at)->format('M. j, Y, g:i a') }}</td>
                                                    @if ($trx->status == '1')
                                                        <td><span class="badge bg-outline-success">Successful</span></td>
                                                    @elseif ($trx->status == '2')
                                                        <td><span class="badge bg-outline-warning">Pending</span></td>
                                                    @else
                                                        <td><span class="badge bg-outline-danger">Failed</span></td>
                                                    @endif
                                                    <td>
                                                        <a class="btn btn-primary" href="{{ route('admin.transactions.manual.transactions.show', $trx) }}">View</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center">
                                {{ $wallet->links('vendor.pagination.default') }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End:: row-1 -->
        </div>
    </div>

@include('admin.partials.footer')
