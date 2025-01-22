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
                                <div class="table-responsive">
                                    <table class="table text-nowrap">
                                        <thead>
                                            <tr>
                                                <th scope="col">Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <tr>
                                                    <td>Transaction ID: {{ $transaction->trx }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Sender Name: {{ $transaction->sender_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Amount: ₦{{ number_format($transaction->amount, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <p>Receipt</p>
                                                        <img src="{{ asset('storage/' . $transaction->receipt_path) }}" alt="Receipt" style="max-width: 300px;">
                                                    </td>
                                                </tr>
                                                @if($transaction->status === 2)
                                                <tr>
                                                    <td>
                                                        <form action="{{ route('admin.transactions.manual.transactions.approve', $transaction) }}" method="POST">
                                                            @csrf
                                                            <button class="btn btn-primary" type="submit">Approve Transaction</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h2>Reject Transaction</h2>
                                                        <form action="{{ route('admin.transactions.manual.transactions.disapprove', $transaction) }}" method="POST">
                                                            @csrf
                                                            <label for="rejection_reason">Reason for Rejection:</label>
                                                            <textarea id="rejection_reason" class="form-control form-control-lg" name="rejection_reason" required></textarea>
                                                            <br>
                                                            <button class="btn btn-danger" type="submit" name="submit">Reject Transaction</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End:: row-1 -->
        </div>
    </div>

@include('admin.partials.footer')
