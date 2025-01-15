@extends('components.app')

@section('content')

<div class="row">
    <div class="col">

    </div>
</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Accomplishment</h4>
                {{-- <p class="card-description"> Add class <code>.table-bordered</code> --}}
                </p>
                <div class="row">
                    <div class="col">
                        <p class="d-inline-flex gap-1">
                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                <i class="mdi mdi-filter-outline"></i> Filter
                            </button>
                        </p>
                    </div>
                    <div class="col d-flex justify-content-end mb-3" >
                        <div id="pending-table-buttons"></div>
                        <div id="completed-table-buttons"></div>
                        {{-- <div id="table-buttons" class="d-flex">
                            <!-- Buttons will be appended here -->
                        </div> --}}
                    </div>
                </div>
                <div class="collapse" id="collapseExample">
                        <div class="d-flex justify-content-center mb-3">
                            <div class="input-group me-3 pending-search">
                                <input type="text" id="search-input-pending" class="form-control" placeholder="Search...">
                            </div>

                            <div class="input-group me-3 completed-search" >
                                <input type="text" id="search-input-completed" class="form-control" placeholder="Search Completed...">
                            </div>

                            <div class="input-group">
                                <input type="text" id="date-range-picker" class="form-control" placeholder="Select date range">
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col">
        <div class="nav-align-top">
            <ul class="nav nav-tabs w-100 d-flex" role="tablist">
                <li class="nav-item flex-fill">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-align-pending">Pending</button>
                </li>
                <li class="nav-item flex-fill">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-align-complete">Completed</button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="navs-top-align-pending">
                    <div class="card-datatable table-responsive pt-0">
                        <table id="pending-table"  class="datatables-basic table border-top">
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="navs-top-align-complete">
                    <div class="card-datatable table-responsive pt-0">
                        <table id="completed-table"  class="datatables-basic table border-top">
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('entries.file')


@endsection


@section('components.specific_page_scripts')

<script>
    $(document).ready(function() {

        var table;
        var completed_table;

        flatpickr("#date-range-picker", {
            mode: "range",
            dateFormat: "m/d/Y",
            onChange: function(selectedDates, dateStr, instance) {
                // Check if both start and end dates are selected
                if (selectedDates.length === 2) {
                    // Check if the end date is earlier than or equal to the start date
                    if (selectedDates[1] <= selectedDates[0]) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: 'Please select a valid date range.',
                        });
                    } else {
                        // Reload the tables if a valid range is selected
                        table.ajax.reload(null, false);
                        completed_table.ajax.reload(null, false);
                    }
                }
            },
            // Add clear button
            onReady: function(selectedDates, dateStr, instance) {
                // Create a "Clear" button
                const clearButton = document.createElement("button");
                clearButton.innerHTML = "Clear";
                clearButton.classList.add("clear-btn");

                // Append the button to the flatpickr calendar
                instance.calendarContainer.appendChild(clearButton);

                // Add event listener to clear the date and reload the tables
                clearButton.addEventListener("click", function() {
                    instance.clear(); // Clear the date range
                    table.ajax.reload(null, false); // Reload the tables
                    completed_table.ajax.reload(null, false);
                });
            }
        });


        table = $('#pending-table').DataTable({
            responsive: true,
            processing: false,
            serverSide: true,
            pageLength: 30,
            lengthChange: false,
            paging: false,
            ordering: false,
            searching: true,
            scrollY: 400,
            select: {
                style: 'single'
            },
             ajax: {
                url: '{{ route('entries.list') }}',
                data: function(d) {
                    // Include the date range in the AJAX request
                    d.date_range = $('#date-range-picker').val();
                    // d.search = $('#search-input').val();
                },
            },
            buttons: [

                {
                    text: 'Add Accomplishment',
                    enabled: false,
                    className: 'btn btn-success user_btn',
                    action: function (e, dt, node, config) {
                        var selectedData = dt.row({ selected: true }).data();

                        if (selectedData && selectedData.id) {

                            window.location.href = `/accomplishment_create?id=${selectedData.id}`;
                            console.log(selectedData.id);
                        } else {
                            alert('No item selected or invalid ID.');
                        }
                    }
                },

            ],

            columns: [
                { data: 'id', name: 'id', title: 'ID', visible: false },
                // { data: 'org_id', name: 'org_id', title: 'Organizational Outcome' },
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

            // rowGroup: {
            //     dataSrc: 'org_id'
            // },

            language: {
                emptyTable: "No data found",
                search: "", // Remove "Search:" label
                searchPlaceholder: "Search..." // Set placeholder text
            },

            dom: '<"d-flex justify-content-between flex-wrap"B>rtip',
            createdRow: function(row, data, dataIndex) {
                $(row).find('td:eq(1)').addClass('wrap-text');
            },
        });

        $('#search-input-pending').on('keyup change', function() {
            table.search(this.value).draw(); // Use this value to search the pending table
            completed_table.search(this.value).draw();
        });

        completed_table = $('#completed-table').DataTable({
            responsive: true,
            processing: false,
            serverSide: true,
            pageLength: 30,
            lengthChange: false,
            paging: false,
            searching: true,
            ordering: false,
            scrollY: 400,
            select: {
                style: 'single'
            },
             ajax: {
                url: '{{ route('entries.completed_list') }}',
                data: function(d) {
                    // Include the date range in the AJAX request
                    d.date_range = $('#date-range-picker').val();
                    // d.search = $('#search-input').val();
                },
            },
            buttons: [
                // {
                //     text: 'Edit',
                //     className: 'btn btn-info user_btn',
                //     enabled: false,
                //     action: function (e, dt, node, config) {

                //         var selectedData = dt.row({ selected: true }).data();

                //         if (selectedData && selectedData.id) {

                //             window.location.href = `/entries_edit?id=${selectedData.id}`;
                //             console.log(selectedData.id);
                //         } else {
                //             alert('No item selected or invalid ID.');
                //         }

                //     }
                // },
                {
                    text: 'View',
                    className: 'btn btn-warning user_btn',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        //alert('View Activated!');

                        var selectedData = dt.row({ selected: true }).data();

                        if (selectedData && selectedData.id) {

                            window.location.href = `/accomplishment_view?id=${selectedData.id}`;
                            console.log(selectedData.id);
                        } else {
                            alert('No item selected or invalid ID.');
                        }
                    }
                },

                @if(in_array(Auth::user()->role->name, ['SuperAdmin']))
                {
                    text: 'Delete',
                    className: 'btn btn-danger user_btn',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        //alert('Delete Activated!');

                        var selectedUserId = completed_table.row({ selected: true }).data().id;
                        console.log(selectedUserId);

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                showLoader();
                                $.ajax({
                                    url: '{{ route('entries.destroy') }}',
                                    method: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        id: selectedUserId
                                    },
                                    success: function(response) {
                                        hideLoader();
                                        if (response.success) {
                                            Swal.fire(
                                                'Deleted!',
                                                'Entry has been deleted.',
                                                'success'
                                            );
                                            completed_table.ajax.reload();
                                        } else {
                                            var errorMessage = '';
                                            Object.keys(response.errors).forEach(function(key) {
                                                errorMessage += response.errors[key][0] + '<br>';
                                            });
                                            hideLoader();
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Deletion Failed',
                                                html: errorMessage
                                            });
                                        }
                                    },
                                    error: function(xhr) {
                                        hideLoader();
                                        console.log(xhr.responseText);
                                    }
                                });
                            }
                        });
                    }
                },
                @endif

            ],

            columns: [
                { data: 'id', name: 'id', title: 'ID', visible: false },
                // { data: 'org_id', name: 'org_id', title: 'Organizational Outcome' },
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

            // rowGroup: {
            //     dataSrc: 'org_id'
            // },

            language: {
                emptyTable: "No data found",
                search: "", // Remove "Search:" label
                searchPlaceholder: "Search..." // Set placeholder text
            },

            dom: '<"d-flex justify-content-between flex-wrap"B>rtip',
            createdRow: function(row, data, dataIndex) {
                $(row).find('td:eq(1)').addClass('wrap-text');
            },

        });

        $('#search-input-completed').on('keyup change', function() {
            completed_table.search(this.value).draw(); // Use this value to search the completed table
        });


        $('.navbar-toggler').on('click', function() {

            table.ajax.reload(null, false);
            completed_table.ajax.reload(null, false);
        });

        $('.nav-link').on('click', function() {

            table.ajax.reload(null, false);
            completed_table.ajax.reload(null, false);
        });

        $('#search-input').on('keyup', function() {
            table.ajax.reload();
            completed_table.ajax.reload();
        });

        // table.buttons().container().appendTo('#roles-table_wrapper .col-md-6:eq(0)');
        // completed_table.buttons().container().appendTo('#roles-table_wrapper .col-md-6:eq(0)');

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            let target = $(e.target).attr('data-bs-target'); // Get the active tab

            if (target === '#navs-top-align-pending') {
                $('#pending-table-buttons').show();
                $('#completed-table-buttons').hide();
                table.buttons().disable();
                // completed_table.buttons().disable();
            } else if (target === '#navs-top-align-complete') {
                $('#pending-table-buttons').hide();
                $('#completed-table-buttons').show();
                // table.buttons().disable();
                completed_table.buttons().disable();
            }
        });

        table.buttons().container().appendTo('#pending-table-buttons');
        completed_table.buttons().container().appendTo('#completed-table-buttons');

        // Hide completed buttons by default
        $('#completed-table-buttons').hide();
        $('#collapseExample .completed-search').hide();


        // table.buttons().container().appendTo('#table-buttons');
        // completed_table.buttons().container().appendTo('#table-buttons');

        table.on('select deselect', function() {
            var selectedRows = table.rows({ selected: true }).count();
            table.buttons(['.btn-success', '.btn-warning', '.btn-info', '.btn-danger']).enable(selectedRows > 0);
        });

        completed_table.on('select deselect', function() {
            var selectedRows = completed_table.rows({ selected: true }).count();
            completed_table.buttons([ '.btn-warning', '.btn-info', '.btn-danger']).enable(selectedRows > 0);
        });

        function getFileExtension(filename) {
            return filename.split('.').pop().toLowerCase();
        }
    });

    $(document).on('click', '.file-preview', function(e) {
        e.preventDefault();
        var base64File = $(this).data('file');
        var fileUrl = 'data:application/pdf;base64,' + base64File;
        $('#filePreview').attr('src', fileUrl);
        $('#fileModal').modal('show');
    });


</script>

@endsection
