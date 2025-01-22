@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

<div class="main-content app-content">
    <div class="container-fluid">


        <!-- PAGE-HEADER -->
        <div class="page-header">
          <h1 class="page-title my-auto">Dashboard</h1>
        </div>
        <!-- PAGE-HEADER END -->
        
        @if(auth()->guard('admin')->user()->account_type == 1)
        <!-- ROW-1 -->
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xxl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="fw-normal">Total Users</h6>
                                <h2 class="mb-0 text-dark fw-semibold">{{ number_format($totalUsers) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xxl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="fw-normal">Total Amount Credited For The Month</h6>
                                <h2 class="mb-0 text-dark fw-semibold">₦{{ number_format($totalCredit, 2) }}</h2>
                            </div>
                            <div class="ms-auto">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xxl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="fw-normal">Total Amount Debited for the Month</h6>
                                <h2 class="mb-0 text-dark fw-semibold">₦{{ number_format($totalDebit, 2) }}</h2>
                            </div>
                            <div class="ms-auto">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xxl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="fw-normal">Total Amount Remaining for the Month</h6>
                                <h2 class="mb-0 text-dark fw-semibold">₦{{ number_format($totalRem, 2) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ROW-1 END -->
        @endif
        
    </div>
</div>

@include('admin.partials.footer')