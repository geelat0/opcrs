@extends('components.app')

@section('content')
    
    @if(auth()->user()->can('manage_accomplishments') && Auth::user()->role->name !== 'SuperAdmin')
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card" style="width: 100%; margin-bottom: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Pending Entries For {{\Carbon\Carbon::create()->month($targetMonth)->format('F')}}</h4>
                                <h3 id="entriesCount" class=" mt-1" style="color: {{ $entriesCount == 0 ? 'blue' : '#ff1500' }};">
                                    {{ $entriesCount ?? 0 }}
                                </h3>
                            </div>
                            <div>
                                <i class="bx bxs-bell-ring dash-icon" style="color: {{ $entriesCount == 0 ? 'blue' : '#ff1500' }};"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card" style="width: 100%; margin-bottom: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Completed Entries For {{\Carbon\Carbon::create()->month($targetMonth)->format('F')}}</h4>
                                <h3 id="CompleteEntriesCount" class="text-primary mt-1">
                                    {{ $CompleteEntriesCount }}
                                </h3>
                            </div>
                            <div>
                                <i class="bx bx-task text-primary dash-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        {{-- <h3 class="card-title">Pending Entries</h3> --}}

                    {{-- <p class="card-description"> Add class <code>.table-bordered</code> --}}
                    </p>
                    <div class="table-responsive pt-3">
                        <table id="pending-table"  class="table table-striped" style="width: 100%">
                        <tbody>
                        </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(Auth::user()->role->name === 'SuperAdmin')
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card" style="width: 100%; margin-bottom: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title">Roles</h3>
                                <h3 id="roleCount">{{ $roleCount }}</h3>
                            </div>
                            <div>
                                <i class="bx bxs-purchase-tag text-success dash-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card" style="width: 100%; margin-bottom: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title">Users</h3>
                                <h3 id="userCount">{{ $userCount }}</h3>
                            </div>
                            <div>
                                <i class="bx bxs-user text-primary dash-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card" style="width: 100%; margin-bottom: 1rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title">Login</h3>
                                <h3 id="loggedInUsersCount">{{ $loggedInUsersCount ?  $loggedInUsersCount : 0}}</h3>
                            </div>
                            <div>
                                <i class='bx bx-history text-warning dash-icon'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Who's login now?</h3>
                    {{-- <p class="card-description"> Add class <code>.table-bordered</code> --}}
                    </p>
                    <div class="table-responsive pt-3">
                        <table id="login-table"  class="table table-striped" style="width: 100%">
                        <tbody>
                        </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('components.specific_page_scripts')
<script>
    $(function() {

        function fetchDashboardData() {
            $.ajax({
                url: '{{ route('fetch.dashboard.data') }}',
                method: 'GET',
                success: function(data) {
                    $('#userCount').text(data.userCount);
                    $('#roleCount').text(data.roleCount);
                    $('#entriesCount').text(data.entriesCount);
                    $('#loggedInUsersCount').text(data.loggedInUsersCount);
                    $('#CompleteEntriesCount').text(data.CompleteEntriesCount);
                }
            });
        }

        // Fetch dashboard data on page load
        fetchDashboardData();
        setInterval(fetchDashboardData, 7500);

        $('#filterIcon').click(function() {
            $('#filtersSection').toggleClass('hidden');
        });
        // Initialize Flatpickr date range picker
        flatpickr("#flatpickr-range", {
            mode: "range",
            dateFormat: "Y-m-d",
        });

        // Set current month and year as the selected values
        var now = new Date();
        var currentMonth = now.getMonth() + 1; // getMonth() returns month from 0-11
        var currentYear = now.getFullYear();

        $('#month').val(currentMonth);
        $('#year').val(currentYear);

        // Handle the filter button click
        $('#filterBtn').on('click', function() {
            var dateRange = $('#flatpickr-range').val();
            var [startDate, endDate] = dateRange ? dateRange.split(' to ') : [null, null];
            var month = $('#month').val();
            var year = $('#year').val();
            showLoader();

            $.ajax({
                url: '{{ route("dashboard.filter") }}',
                method: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate,
                    month: month,
                    year: year
                },
                success: function(data) {
                    hideLoader();
                    $('#userCount').text(data.userCount);
                    $('#roleCount').text(data.roleCount);
                    $('#entriesCount').text(data.entriesCount);
                }
            });
        });

        var table;
        table = $('#pending-table').DataTable({
            responsive: true,
            processing: false,
            serverSide: true,
            pageLength: 30,
            lengthChange: false,
            paging: false,
            ordering: false,
            scrollY: 400,
            // select: {
            //     style: 'single'
            // },
             ajax: {
                url: '{{ route('entries.list') }}',
                data: function(d) {
                    // Include the date range in the AJAX request
                    d.date_range = $('#date-range-picker').val();
                    d.search = $('#search-input').val();
                },
            },
            buttons: [

            ],

            columns: [
                { data: 'id', name: 'id', title: 'ID', visible: false },
                { data: 'indicator_id', name: 'indicator_id', title: 'Indicator'},
                // { data: 'accomplishment', name: 'accomplishment', title: 'Accomplishment'},
                { data: 'months', name: 'months', title: 'Month', className: 'wrap-text' },
                { data: 'year', name: 'year', title: 'Year', className: 'wrap-text' },

                {
                    data: 'file',
                    name: 'file',
                    title: 'File',
                    render: function(data, type, row) {
                        if (data) {
                            // Assuming 'data' is the Base64 encoded string
                            return `
                                <a href="#" class="file-preview"
                                data-file="${data}"
                                data-toggle="modal"
                                data-target="#fileModal">
                                    <i class="bx bx-file"></i> Preview
                                </a>`;
                        } else {
                            return 'No File';
                        }
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'responsible_user', name: 'responsible_user', title: 'Responsible User' },
                { data: 'status', name: 'status', title: 'Status' },
                { data: 'created_at', name: 'created_at', title: 'Created At' },
                { data: 'created_by', name: 'created_by', title: 'Created By' },
            ],

            language: {
                emptyTable: "No data found",
                search: "", // Remove "Search:" label
                searchPlaceholder: "Search..." // Set placeholder text
            },

            dom: '<"d-flex justify-content-between flex-wrap"B>rtip',
            createdRow: function(row, data, dataIndex) {
                $(row).find('td:eq(1)').addClass('wrap-text');
            }
        });

    });
</script>

<script>

</script>

<script>
    var table;
    table = $('#login-table').DataTable({
        responsive: true,
        processing: false,
        serverSide: true,
        pageLength: 30,
        lengthChange: true,
        paging: false,
        ordering: false,
        scrollY: 400,
        // select: {
        //     style: 'single',
        // },
        ajax: {
            url: '{{ route('Loginlist') }}',
            data: function(d) {
                // Include the date range in the AJAX request
                d.date_range = $('#flatpickr-range').val();
                d.search = $('#search-input').val();
            },
        },

        columns: [
            // { data: 'id', name: 'id', title: 'ID', visible: false },
            { data: 'user', name: 'user', title: 'Name' },
            { data: 'user_name', name: 'user_name', title: 'User Name' },
            { data: 'position', name: 'position', title: 'Position' },
            { data: 'role', name: 'role', title: 'Role' },
            // { data: 'last_activity', name: 'last_activity', title: 'last_activity' },
            // { data: 'time_in', name: 'time_in', title: 'Time in' },
        ],

        language: {
            emptyTable: "No data found",
            search: "", // Remove "Search:" label
            searchPlaceholder: "Search..." // Set placeholder text
        },

        dom: '<"d-flex justify-content-between flex-wrap">rtip',  // Adjust DOM layout
    });

    function reloadTable() {
        table.ajax.reload(null, false); // Reloads the table data without resetting pagination
    }

    // Reload table every 1 minute (60000 milliseconds)
    setInterval(reloadTable, 7500);
</script>
@endsection
