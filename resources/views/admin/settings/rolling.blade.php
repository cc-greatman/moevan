@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

<div class="main-content app-content">
    <div class="container-fluid">

        <!-- PAGE-HEADER -->
        <div class="page-header">
          <h1 class="page-title my-auto">Messages Edit</h1>
        </div>
        <!-- PAGE-HEADER END -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            Site Settings
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Type</th>
                                        <th scope="col">Message</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings as $setting)
                                    <tr>
                                        @if($setting->id == 1)
                                        <td>Rolling Message</td>
                                        @elseif ($setting->id == 2)
                                        <td>Dashboard Popup</td>
                                        @elseif ($setting->id == 3)
                                        <td>9mobile Price List</td>
                                        @elseif ($setting->id == 4)
                                        <td>Airtel Price List</td>
                                        @elseif ($setting->id == 5)
                                        <td>Glo Price List</td>
                                        @elseif ($setting->id == 6)
                                        <td>MTN Price List</td>
                                        @endif
                                        <td>{{ \Illuminate\Support\Str::limit($setting->rolling_message, 50, '...') }}</td>
                                        <td>
                                            <a href="{{ route('admin.setting.rolling.view', $setting->id) }}" class="btn btn-success mb-1">Edit Message</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('admin.partials.footer')
