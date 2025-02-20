@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

    <!--APP-CONTENT START-->
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- PAGE-HEADER -->
            <div class="page-header">
              <h1 class="page-title my-auto">Data Transaction History - Results for {{ $query }}</h1>
              <div>
                    <a href="{{ route('admin.export.data', ['filename' => 'Data_Transactions']) }}" class="btn btn-primary mb-1">Export to CSV</a>
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
                                    Results for {{ $query }}
                                </div>
                            </div>
                            <div class="card-body">
                                @if (auth()->guard('admin')->user())
                                <form action="{{ route('admin.search.data') }}" method="GET" class="row gy-2 gx-3 align-items-center mb-4">
                                    @csrf
                                    <div class="col-auto">
                                        <input type="text" name="query" placeholder="Find specific transaction(s)" class="form-control">
                                    </div>
                                    <div class="col-auto">
                                        <select id="pricingType" name="search_type" class="form-select">
                                            <option selected disabled>Select Mode</option>
                                            <option value="trx">Transaction ID</option>
                                            <option value="username">User</option>
                                            <option value="provider">Provider</option>
                                            <option value="datatype">Datatype</option>
                                            <option value="network">Network</option>
                                            <option value="phone">Phone</option>
                                            <option value="date">Date</option>
                                            <option value="amount">Amount</option>
                                            <option value="status">Status</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" name="submit" class="btn btn-primary">Search</button>
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ route('admin.transactions.data.view') }}" class="btn btn-danger">Reset</a>
                                    </div>
                                </form>
                                @endif
                                <div class="table-responsive">
                                    <table class="table text-nowrap">
                                        <thead>
                                            <tr>
                                                <th scope="col">Transaction ID</th>
                                                <th scope="col">User</th>
                                                <th scope="col">Provider</th>
                                                <th scope="col">Network</th>
                                                <th scope="col">Data Type</th>
                                                <th scope="col">Plan</th>
                                                <th scope="col">Balance Before</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Balance After</th>
                                                <th scope="col">Number</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Refund</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($transactions as $dataTrx)
                                                <tr>
                                                    <td>{{ $dataTrx->trx }}</td>
                                                    @php
                                                        $plan = $dataTrx->dataPlan->size.' @ '.$dataTrx->dataPlan->validity.'Days';
                                                    @endphp
                                                    <td>{{ optional($dataTrx->user)->username }}</td>
                                                    <td>{{ $dataTrx->provider }}</td>
                                                    <td>{{ optional($dataTrx->dataPlan)->size ?? 'N/A' }} @ {{ optional($dataTrx->dataPlan)->validity ?? 'N/A' }} Days</td>
                                                    <td>{{ optional(optional($dataTrx->dataPlan)->network)->name ?? 'Unknown Network' }}</td>
                                                    <td>{{ optional(optional($dataTrx->dataPlan)->dataType)->name ?? 'Unknown Type' }}</td>
                                                    <td>₦{{ number_format($dataTrx->pre_bal, 2) }}</td>
                                                    <td>₦{{ number_format($dataTrx->amount, 2) }}</td>
                                                    <td>₦{{ number_format($dataTrx->post_bal, 2) }}</td>
                                                    <td>{{ $dataTrx->phone }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($dataTrx->created_at)->format('M. j, Y, g:i a') }}
                                                    </td>
                                                    <td>{{ $dataTrx->response }}</td>
                                                    <td>
                                                        <form>
                                                            @csrf
                                                            <button type="submit" formmethod="post" formaction="{{ route('admin.data.refund', $dataTrx->id) }}" class="btn btn-danger mb-1">
                                                            Refund User
                                                        </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr colspan="100%">No Transactions found for "{{ $query }}"</tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center">
                                {{ $transactions->links('vendor.pagination.default') }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End:: row-1 -->
        </div>
    </div>

@include('admin.partials.footer')
