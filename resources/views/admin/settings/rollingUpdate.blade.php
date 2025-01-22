@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

<!--APP-CONTENT START-->
            <div class="main-content app-content">
                <div class="container-fluid">

                    <!-- PAGE-HEADER -->
                    <div class="page-header">
                      <h1 class="page-title my-auto">Site Setings Update</h1>
                    </div>
                    <!-- PAGE-HEADER END -->


                    <!-- Start:: row-1 -->
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                       Message Edit
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.setting.rolling.update', $settings->id) }}" method="post">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="form-password" class="form-label fs-14 text-dark">Message</label>
                                            <textarea rows="4" name="message" class="form-control" id="form-password" value="" placeholder="">{{ $settings->rolling_message }}</textarea>
                                        </div>
                                        <button class="btn btn-success" name="submit" type="submit">Update</button>
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
