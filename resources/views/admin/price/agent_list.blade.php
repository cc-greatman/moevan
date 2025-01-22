@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

<div class="main-content app-content">
    <div class="container-fluid">

        <!-- PAGE-HEADER -->
        <div class="page-header">
          <h1 class="page-title my-auto">Agent Lists</h1>
          @include('user.messages.success_alert')
        </div>
        <!-- PAGE-HEADER END -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            Agent Data Price Edit
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Username</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($agents as $agent)
                                    <tr>
                                        <th scope="row">{{ $agent->id }}</th>
                                        <td>{{ $agent->username }}</td>
                                        <td>{{ $agent->email }}</td>
                                        <td>
                                            <a href="{{ route('admin.price.agent.edit', $agent->id) }}" class="btn btn-primary mb-1">Edit Data Prices</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center">
                        {{ $agents->links('vendor.pagination.default') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('admin.partials.footer')
