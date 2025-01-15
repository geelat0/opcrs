@extends('components.app')

@section('content')
<div class="container">
    <h1 class="mt-4">Dashboard</h1>
    <p>Welcome to your dashboard, {{ $user->user_name }}</p>

    <div class="row mb-4 justify-content-center">
        <div class="col-lg-10 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                    <input type="text" id="start_date" class="form-control" placeholder="Start Date">
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                    <input type="text" id="end_date" class="form-control" placeholder="End Date">
                </div>
                <div class="col-lg-2 col-md-4 col-sm-12 mb-2">
                    <select id="month" class="form-select">
                        <option value="">Select Month</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-12 mb-2">
                    <select id="year" class="form-select">
                        <option value="">Select Year</option>
                        @for ($i = date('Y'); $i >= 2000; $i--)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-12 mb-2">
                    <button class="btn btn-primary w-100" id="filterBtn">Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="card-title">Users</h2>
                            <h3 id="userCount">{{ $userCount }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
                @if(Auth::user()->role->name === 'SuperAdmin' || Auth::user()->role->name === 'Admin')
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="/user" class="text-primary">View Details</a>
                    <a href="/user" class="text-primary"><i class="fas fa-arrow-circle-right text-primary"></i></a>
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="card-title">Roles</h2>
                            <h3 id="roleCount">{{ $roleCount }}</h3>
                        </div>
                        <div>
                            <i class="fas fa-user-tag fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
                @if(Auth::user()->role->name === 'SuperAdmin' || Auth::user()->role->name === 'Admin')
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <a href="/roles" class="text-success">View Details</a>
                    <a href="/roles" class="text-success"><i class="fas fa-arrow-circle-right text-success"></i></a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('components.specific_page_scripts')
{{-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}
<script>
$(function() {
    // $("#start_date").datepicker();
    // $("#end_date").datepicker();

    // $('#filterBtn').on('click', function() {
    //     var startDate = $('#start_date').val();
    //     var endDate = $('#end_date').val();
    //     var month = $('#month').val();
    //     var year = $('#year').val();

    //     $.ajax({
    //         url: '{{ route("dashboard.filter") }}',
    //         method: 'GET',
    //         data: {
    //             start_date: startDate,
    //             end_date: endDate,
    //             month: month,
    //             year: year
    //         },
    //         success: function(data) {
    //             $('#userCount').text(data.userCount);
    //             $('#roleCount').text(data.roleCount);
    //         }
    //     });
    // });
});
</script>
@endsection
