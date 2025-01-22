@include('admin.auth.partials.head')

    <div class="autentication-bg" style="background: linear-gradient(to right, #000E3E 100%, #001D82 100%)">

        <div class="container-lg">
            <div class="row justify-content-center authentication authentication-basic align-items-center h-100">
                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12">
                    <div class="my-4 d-flex justify-content-center">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset("backend/images/brand-logos/desktop-dark.png") }}" alt="logo">
                        </a>
                    </div>
                    <div class="card custom-card">
                        <div class="card-body p-5">
                            @include('user.messages.error-lists')
                            <p class="h5 fw-semibold mb-2 text-center">Sign In</p>
                            <p class="mb-4 text-muted op-7 fw-normal text-center">Admin Login!</p>
                            <form action="{{ route('admin.login.perform') }}" method="post">
                                @csrf
                                <div class="row gy-3">
                                    <div class="col-xl-12">
                                        <label for="signin-username" class="form-label text-default">Username</label>
                                        <input type="text" value="{{ old("username") }}" name="username" class="form-control form-control-lg"  placeholder="Username">
                                    </div>
                                    <div class="col-xl-12 mb-2">
                                        <label for="signin-password" class="form-label text-default d-block">Password</label>
                                        <div class="input-group">
                                            <input type="password" name="password" class="form-control form-control-lg" id="password" placeholder="Password">
                                            <button class="btn btn-light" type="button" onclick="createpassword('password',this)" id="button-addon2"><i class="ri-eye-off-line align-middle"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-xl-12 d-grid mt-2">
                                        <button type="submit" name="submit" class="btn btn-lg btn-primary">Sign In</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('admin.auth.partials.footer')
