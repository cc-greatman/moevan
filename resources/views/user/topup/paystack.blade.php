@include('user.partials.head')

@include('user.partials.navbar')

@include('user.partials.sidebar')

<!--APP-CONTENT START-->
            <div class="main-content app-content">
                <div class="container-fluid">

                    <!-- PAGE-HEADER -->
                    <div class="page-header">
                      <h1 class="page-title my-auto">Wallet Top-Up</h1>
                    </div>
                    <!-- PAGE-HEADER END -->


                    <!-- Start:: row-1 -->
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                        Fund with Paystack
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Amount</label>
                                        <input type="text" class="form-control" id="form-password" placeholder="">
                                    </div>
                                    <button class="btn btn-primary" type="submit">Top Up</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End:: row-1 -->
                </div>
            </div>
            <!--APP-CONTENT CLOSE-->

@include('user.partials.footer')
