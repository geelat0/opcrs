@extends('components.app')

@section('content')
<div class="row mt-4">
    <div class="col">
        <div class="card">
            <div class="card-body">
              <h4 class="card-title">Roles</h4>
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
        <div class="card">
            <div class="card-body">

              {{-- <p class="card-description"> Add class <code>.table-bordered</code> --}}
              </p>

              <div class="table-responsive pt-3">
                <table id="roles-table"  class="table table-striped" style="width: 100%">
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
    </div>
</div>
    @include('role_page.create')
    @include('role_page.edit')
    @include('role_page.view')
@endsection


{{-- JS of Pages --}}
@section('components.specific_page_scripts')

<script>
    $(document).ready(function() {


        var table;

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
                });
            }
        });

        table = $('#roles-table').DataTable({
            responsive: true,
            processing: false,
            serverSide: true,
            pageLength: 30,
            lengthChange: false,
            paging: false,
            ordering: false,
            search: true,
            scrollY: 400,
            select: {
                style: 'single'
            },
             ajax: {
                url: '{{ route('role.list') }}',
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
                //     action: function ( e, dt, node, config ) {
                //         dt.ajax.reload();
                //     }
                // },
                {
                    text: 'Add',
                    className: 'btn btn-success user_btn',
                    action: function (e, dt, node, config) {
                        $('#createRoleModal').modal('show');

                        $('#createRoleForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            showLoader();
                            // Handle form submission, e.g., via AJAX
                            var formData = $(this).serialize();
                            $.ajax({
                                url: '{{ route('role.store') }}',
                                method: 'POST',
                                data: formData,
                                success: function(response) {
                                    if (response.success) {
                                        $('#createRoleModal').modal('hide');
                                        hideLoader();
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: response.message,
                                                showConfirmButton: true,
                                            })
                                            table.ajax.reload();
                                            table.buttons('.user_btn').disable(); // Disable all user buttons
                                            table.buttons('.btn-success').enable(); // Enable only the "Add" button
                                        }
                                        else{

                                            var errors = response.errors;
                                            Object.keys(errors).forEach(function(key) {
                                                var inputField = $('#createRoleForm [name=' + key + ']');
                                                inputField.addClass('is-invalid');
                                                $('#createRoleForm #' + key + 'Error').text(errors[key][0]);
                                            });
                                            hideLoader();
                                            table.buttons('.user_btn').disable(); // Disable all user buttons
                                            table.buttons('.btn-success').enable(); // Enable only the "Add" button
                                        }
                                },
                                error: function(xhr) {
                                    hideLoader();
                                    // Handle error
                                    console.log(xhr.responseText);
                                    table.buttons('.user_btn').disable(); // Disable all user buttons
                                    table.buttons('.btn-success').enable(); // Enable only the "Add" button
                                }
                            });
                        });
                    }
                },
                {
                    text: 'Edit',
                    className: 'btn btn-info user_btn',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        $('#editRoleModal').modal('show');

                        var selectedData = dt.row({ selected: true }).data();
                        $('#edit_role_id').val(selectedData.id);
                        $('#edit_name').val(selectedData.name);
                        $('#edit_status').val(selectedData.status);

                        $('#editRoleForm').off('submit').on('submit', function(e) {
                                e.preventDefault();
                                showLoader();
                                var formData = $(this).serialize();
                                $.ajax({
                                    url: '{{ route('role.update') }}',
                                    method: 'POST',
                                    data: formData,
                                    success: function(response) {
                                        if (response.success) {
                                            $('#editRoleModal').modal('hide');
                                            hideLoader();
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: response.message,
                                                showConfirmButton: true,
                                            })

                                            table.ajax.reload();
                                            table.buttons('.user_btn').disable(); // Disable all user buttons
                                            table.buttons('.btn-success').enable(); // Enable only the "Add" button

                                        } else {
                                            var errors = response.errors;
                                            Object.keys(errors).forEach(function(key) {
                                                var inputField = $('#editRoleForm [name=' + key + ']');
                                                inputField.addClass('is-invalid');
                                                $('#editRoleForm #' + key + 'Error').text(errors[key][0]);
                                            });
                                            hideLoader();
                                            table.buttons('.user_btn').disable(); // Disable all user buttons
                                            table.buttons('.btn-success').enable(); // Enable only the "Add" button
                                        }
                                    },
                                    error: function(xhr) {
                                        hideLoader();
                                        console.log(xhr.responseText);
                                        table.buttons('.user_btn').disable(); // Disable all user buttons
                                        table.buttons('.btn-success').enable(); // Enable only the "Add" button
                                    }
                            });
                        });
                    }
                },
                {
                    text: 'View',
                    className: 'btn btn-warning user_btn',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        //alert('View Activated!');

                        var selectedData = dt.row({ selected: true }).data();
                        $('#view_role_id').val(selectedData.id);
                        $('#view_name').val(selectedData.name);
                        $('#view_status').val(selectedData.status);
                        $('#viewRoleModal').modal('show');

                    }
                },
                {
                    text: 'Delete',
                    className: 'btn btn-danger user_btn',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        //alert('Delete Activated!');

                        var selectedUserId = table.row({ selected: true }).data().id; // Assuming you have selected a user row

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
                                    url: '{{ route('role.destroy') }}',
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
                                                'User has been deleted.',
                                                'success'
                                            );
                                            table.ajax.reload();
                                            table.buttons('.user_btn').disable(); // Disable all user buttons
                                            table.buttons('.btn-success').enable(); // Enable only the "Add" button
                                        } else {
                                            var errorMessage = '';
                                            Object.keys(response.errors).forEach(function(key) {
                                                errorMessage += response.errors[key][0] + '<br>';
                                            });
                                            hideLoader();
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Deletion Failed',
                                                html: response.errors
                                            });
                                            table.buttons('.user_btn').disable(); // Disable all user buttons
                                            table.buttons('.btn-success').enable(); // Enable only the "Add" button
                                        }
                                    },
                                    error: function(xhr) {
                                        hideLoader();
                                        console.log(xhr.responseText);
                                        table.buttons('.user_btn').disable(); // Disable all user buttons
                                        table.buttons('.btn-success').enable(); // Enable only the "Add" button
                                    }
                                });
                            }
                        });
                    }
                },


            ],

            columns: [
                { data: 'id', name: 'id', title: 'ID', visible: false },
                { data: 'name', name: 'name', title: 'Name' },
                { data: 'status', name: 'status', title: 'Status' },
                { data: 'created_by', name: 'created_by', title: 'Created By' },
                { data: 'created_at', name: 'created_at', title: 'Created At' },
            ],

            language: {
                emptyTable: "No data found",
                search: "", // Remove "Search:" label
                searchPlaceholder: "Search..." // Set placeholder text
            },

            dom: '<"d-flex justify-content-between flex-wrap"B>rtip',
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

        // table.buttons().container().appendTo('#roles-table_wrapper .col-md-6:eq(0)');
        table.buttons().container().appendTo('#table-buttons');

        table.on('select deselect', function() {
            var selectedRows = table.rows({ selected: true }).count();
            table.buttons(['.btn-warning', '.btn-info', '.btn-danger']).enable(selectedRows > 0);
        });

        $('#createRoleModal, #editRoleModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset(); // Reset form fields
            $(this).find('.is-invalid').removeClass('is-invalid'); // Remove validation error classes
            $(this).find('.invalid-feedback').text(''); // Clear error messages
        });


    });


    $(document).ready(function() {
        $.ajax({
            url: '{{ route('get.role') }}',
            type: 'GET',
            success: function(data) {
                var rolesDropdown = $('#rolesDropdown');
                var editRolesDropdown = $('#edit_role');
                var viewRolesDropdown = $('#view_role');
                data.forEach(function(role) {
                    rolesDropdown.append('<option value="' + role.id + '">' + role.name + '</option>');
                    editRolesDropdown.append('<option value="' + role.id + '">' + role.name + '</option>');
                    viewRolesDropdown.append('<option value="' + role.id + '">' + role.name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching roles:', error);
            }
        });
    });


</script>
@endsection
