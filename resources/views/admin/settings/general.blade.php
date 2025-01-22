@include('admin.partials.head')

@include('admin.partials.navbar')

@include('admin.partials.sidebar')

<div class="main-content app-content">
    <div class="container-fluid">

        <!-- PAGE-HEADER -->
        <div class="page-header">
          <h1 class="page-title my-auto">Site Settings</h1>
          @include('user.messages.success_alert')
        </div>
        <!-- PAGE-HEADER END -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                           Popup Switch
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Main Switch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($popup as $data)
                                    <tr>
                                        <td>{{ $data->name }}</td>
                                        <td>
                                            @if($data->active == '1')
                                            <a href="{{ route('admin.setting.popup.main') }}" class="btn btn-danger mb-1">Turn Main off</a>
                                            @else
                                            <a href="{{ route('admin.setting.popup.main') }}" class="btn btn-success mb-1">Turn Main on</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                           Airtime Switch
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Manual Disburse</th>
                                        <th scope="col">Main Switch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($airtime as $data)
                                    <tr>
                                        <td>{{ $data->name }}</td>
                                        <td>
                                            @if($data->status == '1')
                                            <a href="{{ route('admin.setting.airtime.manual') }}" class="btn btn-danger mb-1">Turn Manual off</a>
                                            @else
                                            <a href="{{ route('admin.setting.airtime.manual') }}" class="btn btn-success mb-1">Turn Manual on</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->active == '1')
                                            <a href="{{ route('admin.setting.airtime.main') }}" class="btn btn-danger mb-1">Turn Main off</a>
                                            @else
                                            <a href="{{ route('admin.setting.airtime.main') }}" class="btn btn-success mb-1">Turn Main on</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                           Cable Switch
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Manual Disburse</th>
                                        <th scope="col">Main Switch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cable as $data)
                                    <tr>
                                        <td>{{ $data->name }}</td>
                                        <td>
                                            @if($data->status == '1')
                                            <a href="{{ route('admin.setting.cable.manual') }}" class="btn btn-danger mb-1">Turn Manual off</a>
                                            @else
                                            <a href="{{ route('admin.setting.cable.manual') }}" class="btn btn-success mb-1">Turn Manual on</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->active == '1')
                                            <a href="{{ route('admin.setting.cable.main') }}" class="btn btn-danger mb-1">Turn Main off</a>
                                            @else
                                            <a href="{{ route('admin.setting.cable.main') }}" class="btn btn-success mb-1">Turn Main on</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                           Electricity Switch
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Manual Disburse</th>
                                        <th scope="col">Main Switch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($electricity as $data)
                                    <tr>
                                        <td>{{ $data->name }}</td>
                                        <td>
                                            @if($data->status == '1')
                                            <a href="{{ route('admin.setting.electricity.manual') }}" class="btn btn-danger mb-1">Turn Manual off</a>
                                            @else
                                            <a href="{{ route('admin.setting.electricity.manual') }}" class="btn btn-success mb-1">Turn Manual on</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->active == '1')
                                            <a href="{{ route('admin.setting.electricity.main') }}" class="btn btn-danger mb-1">Turn Main off</a>
                                            @else
                                            <a href="{{ route('admin.setting.electricity.main') }}" class="btn btn-success mb-1">Turn Main on</a>
                                            @endif
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
