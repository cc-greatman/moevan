@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

<!--APP-CONTENT START-->
            <div class="main-content app-content">
                <div class="container-fluid">

                    <!-- PAGE-HEADER -->
                    <div class="page-header">
                      <h1 class="page-title my-auto">{{ $user->username }}'s Account Debit</h1>
                    </div>
                    <!-- PAGE-HEADER END -->


                    <!-- Start:: row-1 -->
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                       {{ $user->username }} Wallet Debit
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.user.debit.perform', $user->id) }}" method="post">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="form-password" class="form-label fs-14 text-dark">Amount</label>
                                            <input type="text" name="amount" class="form-control" id="form-password" placeholder="">
                                        </div>
                                        <button class="btn btn-danger" name="submit" type="submit">Debit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End:: row-1 -->
                </div>
            </div>
            <!--APP-CONTENT CLOSE-->

@include('admin.partials.footer')
