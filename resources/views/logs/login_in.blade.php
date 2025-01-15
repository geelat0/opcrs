@extends('components.app')

@section('content')


<div class="row mt-4">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Login History</h4>
            {{-- <p class="card-description"> Add class <code>.table-bordered</code> --}}
                <div class="row">
                    <div class="col">
                        <p class="d-inline-flex gap-1">
                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                <i class="mdi mdi-filter-outline"></i> Filter
                            </button>
                        </p>

                    </div>
                    <div class="col d-flex justify-content-end mb-3" >

                        <div id="table-buttons" class="d-flex">
                            <!-- Buttons will be appended here -->
                        </div>

                    </div>

                </div>
                <div class="collapse" id="collapseExample">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="input-group me-3">
                            <input type="text" id="search-input" class="form-control" placeholder="Search...">
                        </div>
                        <div class="input-group me-3">
                            <input type="text" id="date-range-picker" class="form-control" placeholder="Select date range">
                        </div>
                    </div>
                </div>
            </p>
          </div>
        </div>
    </div>

</div>


<div class="row mt-4">

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
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

@endsection

@section('components.specific_page_scripts')
<script>
    $(document).ready(function() {

        var file_name = 'Login History ' + new Date().toISOString().split('T')[0];

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

        var table;

        table = $('#login-table').DataTable({
            responsive: true,
            processing: false,
            serverSide: true,
            pageLength: 30,
            lengthChange: true,
            paging: false,
            ordering: false,
            search: true,
            scrollY: 400,
            // select: {
            //     style: 'single',
            // },
            ajax: {
                url: '{{ route('list') }}',
                data: function(d) {
                    // Include the date range in the AJAX request
                    d.date_range = $('#date-range-picker').val();
                    // d.search = $('#search-input').val();
                },
                // beforeSend: function() {
                //     showLoader(); // Show loader before starting the AJAX request
                // },
                // complete: function() {
                //     hideLoader(); // Hide loader after AJAX request completes
                // }
            },

            buttons: [
                // {
                //     text: 'Reload',
                //     className: 'btn btn-warning user_btn',
                //     enabled: true,
                //     action: function ( e, dt, node, config ) {
                //         dt.ajax.reload();
                //     }
                // },

                {

                    extend: 'collection',
                    text: 'Actions',
                    className: 'btn btn-primary user_btn',
                    buttons: [
                            {
                                extend: 'print',
                                text: 'Print',
                                title: '',
                                filename: function () {
                                    return file_name;
                                },

                                customize: function (win) {
                                    $(win.document.body)
                                        .css('font-size', '10pt')
                                        .prepend(
                                            '<div style="text-align: center; font-size: 12pt;"><strong>Login History</strong></div>'

                                        );

                                    $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');
                                }
                            },
                            {
                                extend: 'copyHtml5',
                                text: 'Copy',
                                title: 'Login History',
                                filename: function () {
                                    return file_name;
                                },

                            },
                            {
                                extend: 'excelHtml5',
                                text: 'Export to Excel',
                                title: 'Login History',
                                filename: function () {
                                    return file_name;
                                },

                            },
                            {
                                extend: 'pdfHtml5',
                                text: 'Export to PDF',
                                filename: function () {
                                    return file_name;
                                },
                                title: 'Login History',
                                orientation: 'portrait',
                                pageSize: 'A4',
                                exportOptions: {
                                    modifier: {
                                        page: 'current',

                                    }
                                },
                                customize: function (doc) {
                                    // Center the table on the PDF page
                                    doc.content.forEach(function (item) {
                                        if (item.table) {
                                            item.layout = 'lightHorizontalLines';
                                            item.alignment = 'center';
                                        }
                                    });
                                }
                            },
                            {
                                extend: 'csvHtml5',
                                text: 'Export to CSV',
                                title: 'Login History',
                                filename: function () {
                                    return file_name;
                                },

                            },

                    ]

                }

            ],

            columns: [
                // { data: 'id', name: 'id', title: 'ID', visible: false },
                { data: 'user', name: 'user', title: 'Name' },
                { data: 'user_name', name: 'user_name', title: 'User Name' },
                { data: 'position', name: 'position', title: 'Position' },
                { data: 'role', name: 'role', title: 'Role' },
                { data: 'status', name: 'status', title: 'Status' },
                { data: 'created_at', name: 'created_at', title: 'Date Time In' },
                // { data: 'time_in', name: 'time_in', title: 'Time in' },
            ],

            language: {
                emptyTable: "No data found",
                search: "", // Remove "Search:" label
                searchPlaceholder: "Search..." // Set placeholder text
            },

            dom: '<"d-flex justify-content-between flex-wrap"B>rtip',  // Adjust DOM layout
        });

        $('.navbar-toggler').on('click', function() {
        // Reload the DataTable
            table.ajax.reload(null, false); // false to keep the current paging
        });


        // $('#search-input').on('keyup', function() {
        //     table.ajax.reload();  // Reload the table when the search input changes
        // });

        $('#search-input').on('keyup change', function() {
            table.search(this.value).draw(); // Reload the table when the search input changes
        });

        // table.buttons().container().appendTo('#login-table_wrapper .col-md-6:eq(0)');
        table.buttons().container().appendTo('#table-buttons');

    });


</script>

@endsection
