@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

<div class="main-content app-content">
    <div class="container-fluid">

        <!-- PAGE-HEADER -->
        <div class="page-header">
          <h1 class="page-title my-auto">Users Referral Count</h1>
        </div>
        <!-- PAGE-HEADER END -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            Users
                        </div>
                    </div>
                    <div class="card-body">
                        @if (auth()->guard('admin')->user())
                        <form action="{{ route('admin.search.referral') }}" method="GET" class="row gy-2 gx-3 align-items-center mb-4">
                            @csrf
                            <div class="col-auto">
                                <input type="text" name="query" placeholder="Find specific User" class="form-control">
                            </div>
                            <div class="col-auto">
                                <select id="pricingType" name="search_type" class="form-select">
                                    <option selected disabled>Select Mode</option>
                                    <option value="username">Username</option>
                                    <option value="email">Email</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="submit" class="btn btn-primary">Search</button>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.user.referrals') }}" class="btn btn-danger">Reset</a>
                            </div>
                        </form>
                        @endif
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Username</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Number of Referrals</th>
                                        <th scope="col">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                    <tr>
                                        <th scope="row">{{ $user->id }}</th>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        @php
                                            $userReferralCount = \App\Models\Referral::where('referrer_id', $user->id)->count();
                                        @endphp
                                        <td>{{ $userReferralCount }}</td>
                                        <td>
                                            <form method="post" action="{{ route('admin.user.delete', $user->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" onclick="return confirm('This action cannot be undone')" class="btn btn-danger mb-1">Delete Account</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center">
                        {{ $users->links('vendor.pagination.default') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('admin.partials.footer')
