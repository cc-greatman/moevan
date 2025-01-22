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
                           Reseller Data Switch
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Manual Disburse</th>
                                        <th scope="col">Main Switch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reseller as $data)
                                    <tr>
                                        <th scope="row">{{ $data->id }}</th>
                                        <td>Reseller Special Data</td>
                                        <td>
                                            @if($data->status == '1')
                                            <a href="{{ route('admin.setting.reseller.switch.off') }}" class="btn btn-danger mb-1">Turn Manual off</a>
                                            @else
                                            <a href="{{ route('admin.setting.reseller.switch.on') }}" class="btn btn-success mb-1">Turn Manual on</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->active == '1')
                                            <a href="{{ route('admin.setting.agent.switch.off') }}" class="btn btn-danger mb-1">Turn Main off</a>
                                            @else
                                            <a href="{{ route('admin.setting.agent.switch.on') }}" class="btn btn-success mb-1">Turn Main on</a>
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
                           Agent Data Switch
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Manual Disburse</th>
                                        <th scope="col">Main Switch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($agent as $data)
                                    <tr>
                                        <th scope="row">{{ $data->id }}</th>
                                        <td>Agent Special Data</td>
                                        <td>
                                            @if($data->status == '1')
                                            <form method="post" action="{{ route('admin.setting.status.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-danger mb-1">Turn Manual Off</button>
                                            </form>
                                            @else
                                            <form method="post" action="{{ route('admin.setting.status.switch.off', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-success mb-1">Turn Manual On</button>
                                            </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->active == '1')
                                            <form method="post" action="{{ route('admin.setting.active.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-danger mb-1">Turn Main Off</button>
                                            </form>
                                            @else
                                             <form method="post" action="{{ route('admin.setting.active.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-success mb-1">Turn Main On</button>
                                            </form>
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
                           MTN Data Switch
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Manual Disburse</th>
                                        <th scope="col">Main Switch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mtn as $data)
                                    <tr>
                                        <th scope="row">{{ $data->id }}</th>
                                        <td>{{ optional($data->dataType)->name }}</td>
                                        <td>
                                            @if($data->status == '1')
                                            <form method="post" action="{{ route('admin.setting.status.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-danger mb-1">Turn Manual Off</button>
                                            </form>
                                            @else
                                            <form method="post" action="{{ route('admin.setting.status.switch.off', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-success mb-1">Turn Manual On</button>
                                            </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->active == '1')
                                            <form method="post" action="{{ route('admin.setting.active.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-danger mb-1">Turn Main Off</button>
                                            </form>
                                            @else
                                            <form method="post" action="{{ route('admin.setting.active.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-success mb-1">Turn Main On</button>
                                            </form>
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
                           GLO Data Switch
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Manual Disburse</th>
                                        <th scope="col">Main Switch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($glo as $data)
                                    <tr>
                                        <th scope="row">{{ $data->id }}</th>
                                        <td>{{ optional($data->dataType)->name }}</td>
                                        <td>
                                            @if($data->status == '1')
                                            <form method="post" action="{{ route('admin.setting.status.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-danger mb-1">Turn Manual Off</button>
                                            </form>
                                            @else
                                            <form method="post" action="{{ route('admin.setting.status.switch.off', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-success mb-1">Turn Manual On</button>
                                            </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->active == '1')
                                            <form method="post" action="{{ route('admin.setting.active.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-danger mb-1">Turn Main Off</button>
                                            </form>
                                            @else
                                            <form method="post" action="{{ route('admin.setting.active.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-success mb-1">Turn Main On</button>
                                            </form>
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
                           9MOBILE Data Switch
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Manual Disburse</th>
                                        <th scope="col">Main Switch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($etisalat as $data)
                                    <tr>
                                        <th scope="row">{{ $data->id }}</th>
                                        <td>{{ optional($data->dataType)->name }}</td>
                                        <td>
                                            @if($data->status == '1')
                                            <form method="post" action="{{ route('admin.setting.status.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-danger mb-1">Turn Manual Off</button>
                                            </form>
                                            @else
                                            <form method="post" action="{{ route('admin.setting.status.switch.off', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-success mb-1">Turn Manual On</button>
                                            </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->active == '1')
                                            <form method="post" action="{{ route('admin.setting.active.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-danger mb-1">Turn Main Off</button>
                                            </form>
                                            @else
                                            <form method="post" action="{{ route('admin.setting.active.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-success mb-1">Turn Main On</button>
                                            </form>
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
                           AIRTEL Data Switch
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Manual Disburse</th>
                                        <th scope="col">Main Switch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($airtel as $data)
                                    <tr>
                                        <th scope="row">{{ $data->id }}</th>
                                        <td>{{ optional($data->dataType)->name }}</td>
                                        <td>
                                            @if($data->status == '1')
                                            <form method="post" action="{{ route('admin.setting.status.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-danger mb-1">Turn Manual Off</button>
                                            </form>
                                            @else
                                            <form method="post" action="{{ route('admin.setting.status.switch.off', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-success mb-1">Turn Manual On</button>
                                            </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->active == '1')
                                            <form method="post" action="{{ route('admin.setting.active.switch.on', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-danger mb-1">Turn Main Off</button>
                                            </form>
                                            @else
                                            <form method="post" action="{{ route('admin.setting.active.switch.off', $data->id) }}">
                                                @csrf
                                                <button name="submit" type="submit" class="btn btn-success mb-1">Turn Main On</button>
                                            </form>
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
