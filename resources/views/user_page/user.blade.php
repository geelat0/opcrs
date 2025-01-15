@extends('components.app')

@section('content')
<div class="row mt-4">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">User Management</h4>
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
                <div class="table-responsive pt-3">
                    <table id="users-table" class="table table-striped" style="width: 100%">
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user_page.create')
@include('user_page.edit')
@include('user_page.view')
@endsection

@section('components.specific_page_scripts')
<script>
    $(document).ready(function() {

        var table;
        // START DATATABLES

        // flatpickr("#date-range-picker", {
        //     mode: "range",
        //     dateFormat: "m/d/Y",
        //     onChange: function(selectedDates, dateStr, instance) {
        //         // Check if both start and end dates are selected
        //         if (selectedDates.length === 2) {
        //             table.ajax.reload(null, false);
        //         }
        //     }
        // });

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

        table = $('#users-table').DataTable({
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
                style: 'single',
            },
            ajax: {
                url: '{{ route('user.list') }}',
                data: function(d) {
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
                    text: 'Add',
                    className: 'btn btn-success user_btn',
                    enabled: true,
                    action: function (e, dt, node, config) {
                        $('#create_division_id').val(null).change();
                        $('#createUserModal').modal('show');
                        $('#createUserForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            showLoader();
                            var formData = $(this).serialize();

                            $.ajax({
                                url: '{{ route('users.store') }}',
                                method: 'POST',
                                data: formData,
                                success: function(response) {
                                    if (response.success) {
                                        $('#createUserModal').modal('hide');
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
                                                var inputField = $('#createUserForm [name=' + key + ']');
                                                inputField.addClass('is-invalid');
                                                $('#createUserForm #' + key + 'Error').text(errors[key][0]);
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
                    text: 'Edit',
                    className: 'btn btn-info user_btn',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        $('#editUserModal').modal('show');

                        var selectedData = dt.row({ selected: true }).data();
                        $('#edit_user_id').val(selectedData.id);
                        $('#edit_firtsname').val(selectedData.first_name);
                        $('#edit_lastname').val(selectedData.last_name);
                        $('#edit_middlesname').val(selectedData.middle_name);
                        $('#edit_contactNumber').val(selectedData.mobile_number);
                        $('#edit_suffix').val(selectedData.suffix);
                        $('#edit_email').val(selectedData.email);
                        $('#edit_position').val(selectedData.position);
                        $('#edit_province').val(selectedData.province);
                        $('#edit_role').val(selectedData.role_id).change();

                        $('#edit_division_id').val(null).change();

                        $.ajax({
                            url: '{{ route('getDivision')}}',
                            method: 'GET',
                            data: {
                                id: selectedData.id
                            },
                            success: function(response) {
                                if(response.success) {
                                    var divisionIds = response.divisions;

                                    $.each(divisionIds, function(index, division) {
                                        var newOption = new Option(division.division_name, division.id, true, true);
                                        $('#edit_division_id').append(newOption);
                                    });

                                    $('#edit_division_id').trigger('change');
                                    table.buttons('.user_btn').disable(); // Disable all user buttons
                                    table.buttons('.btn-success').enable(); // Enable only the "Add" button
                                } else {
                                    console.error('Failed to fetch divisions');
                                    table.buttons('.user_btn').disable(); // Disable all user buttons
                                    table.buttons('.btn-success').enable(); // Enable only the "Add" button
                                }
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                                table.buttons('.user_btn').disable(); // Disable all user buttons
                                table.buttons('.btn-success').enable(); // Enable only the "Add" button
                            }
                        });

                        $('#editUserForm').off('submit').on('submit', function(e) {
                                e.preventDefault();
                                showLoader();
                                var formData = $(this).serialize();
                                $.ajax({
                                    url: '{{ route('users.update') }}',
                                    method: 'POST',
                                    data: formData,
                                    success: function(response) {

                                        if (response.success) {
                                            $('#editUserModal').modal('hide');
                                            hideLoader();

                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: response.message,
                                                showConfirmButton: true,
                                            })

                                            table.ajax.reload();

                                        } else {
                                            var errors = response.errors;
                                            Object.keys(errors).forEach(function(key) {
                                                var sanitizedKey = key.replace('[]', '');
                                                var inputField = $('#editUserForm [name=' + key + ']');
                                                inputField.addClass('is-invalid');
                                                $('#editUserForm #' + sanitizedKey + 'Error').text(errors[key][0]);
                                            });
                                            hideLoader();
                                        }
                                    },
                                    error: function(xhr) {
                                        hideLoader();
                                        console.log(xhr.responseText);
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
                        $('#viewt_user_id').val(selectedData.id);
                        $('#view_firtsname').val(selectedData.first_name);
                        $('#view_lastname').val(selectedData.last_name);
                        $('#view_middlesname').val(selectedData.middle_name);
                        $('#view_contactNumber').val(selectedData.mobile_number);
                        $('#view_suffix').val(selectedData.suffix);
                        $('#view_email').val(selectedData.email);
                        $('#view_position').val(selectedData.position);
                        $('#view_province').val(selectedData.province);
                        $('#view_role').val(selectedData.role_id).change();

                        $('#view_division_id').val(null).change();

                        $.ajax({
                            url: '{{ route('getDivision')}}',
                            method: 'GET',
                            data: {
                                id: selectedData.id
                            },
                            success: function(response) {
                                if(response.success) {
                                    var divisionIds = response.divisions;

                                    $.each(divisionIds, function(index, division) {
                                        var newOption = new Option(division.division_name, division.id, true, true);
                                        $('#view_division_id').append(newOption);
                                    });

                                    $('#view_division_id').trigger('change');
                                } else {
                                    console.error('Failed to fetch divisions');
                                }
                            },
                            error: function(xhr) {
                                console.error(xhr.responseText);
                            }
                        });

                        $('#viewUserModal').modal('show');

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
                                    url: '{{ route('users.destroy') }}',
                                    method: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        id: selectedUserId
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            hideLoader();
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
                {
                    extend: 'collection',
                    text: 'Actions',
                    enabled: false,
                    className: 'btn btn-primary user_btn',
                    buttons:
                    [
                        {
                            text: 'Change Status',
                            action: function(e, dt, node, config) {
                                var selectedData = dt.row({ selected: true }).data();
                                Swal.fire({
                                    title: 'Change Status',
                                    input: 'select',
                                    inputOptions: {
                                        'Active': 'Active',
                                        'Inactive': 'Inactive',
                                        'Blocked': 'Blocked',
                                    },
                                    inputPlaceholder: selectedData.status,
                                    showCancelButton: true,
                                    inputValidator: (value) => {
                                        return new Promise((resolve) => {
                                            if (value) {
                                                resolve();
                                            } else {
                                                resolve('You need to select a status');
                                            }
                                        });
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        showLoader();
                                        $.ajax({
                                            url: '{{ route('users.change-status') }}',
                                            method: 'POST',
                                            data: {
                                                id: selectedData.id,
                                                status: result.value,
                                                _token: '{{ csrf_token() }}'
                                            },
                                            success: function(response) {
                                                hideLoader();
                                                if (response.success) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Success!',
                                                        text: response.message,
                                                        showConfirmButton: true
                                                    });
                                                    table.ajax.reload();
                                                } else {
                                                    hideLoader();
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error!',
                                                        text: response.message,
                                                        showConfirmButton: true
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
                        // {
                        //     text: 'Disabled 2FA',
                        //     action: function(e, dt, node, config) {
                        //         var selectedData = dt.row({ selected: true }).data();
                        //         showLoader();

                        //         $.ajax({
                        //             url: '{{ route('twofaDisabled') }}',
                        //             method: 'POST',
                        //             data: {
                        //                 id: selectedData.id,
                        //                 _token: '{{ csrf_token() }}'
                        //             },
                        //             success: function(response) {
                        //                 if (response.success) {
                        //                     hideLoader();

                        //                     Swal.fire({
                        //                         icon: 'success',
                        //                         title: 'Success!',
                        //                         text: response.message,
                        //                         showConfirmButton: true,
                        //                     })

                        //                 } else {
                        //                     Swal.fire({
                        //                         icon: 'error',
                        //                         title: 'Error!',
                        //                         text: response.message,
                        //                         showConfirmButton: true,
                        //                     })
                        //                     hideLoader();
                        //                 }
                        //             },
                        //             error: function(xhr) {
                        //                 hideLoader();
                        //                 console.log(xhr.responseText);
                        //             }
                        //         });
                        //         console.log(selectedData.id);
                        //     }


                        // },
                        {
                            text: 'Temporary Password',
                            action: function (e, dt, node, config) {

                                var selectedData = dt.row({ selected: true }).data();
                                showLoader();

                                $.ajax({
                                    url: '{{ route('users.temp-password') }}',
                                    method: 'POST',
                                    data: {
                                        id: selectedData.id,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            hideLoader();

                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: response.message,
                                                showConfirmButton: true,
                                            })

                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error!',
                                                text: response.message,
                                                showConfirmButton: true,
                                            })
                                            hideLoader();
                                        }
                                    },
                                    error: function(xhr) {
                                        hideLoader();
                                        console.log(xhr.responseText);
                                    }
                            });
                                console.log(selectedData.id);

                            }
                        },
                        @if(Auth::user()->role->name === 'SuperAdmin')
                        {
                            text: 'Proxy Login',
                            action: function (e, dt, node, config) {

                                var selectedData = dt.row({ selected: true }).data();
                                showLoader();

                                $.ajax({
                                    url: '{{ route('users.gen-proxy') }}',
                                    method: 'POST',
                                    data: {
                                        id: selectedData.id,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            hideLoader();
                                            window.location.href = response.redirect;

                                        } else {
                                            var errors = response.errors;
                                            Object.keys(errors).forEach(function(key) {
                                                var inputField = $('#editUserForm [name=' + key + ']');
                                                inputField.addClass('is-invalid');
                                                $('#editUserForm #' + key + 'Error').text(errors[key][0]);
                                            });
                                            hideLoader();
                                        }
                                    },
                                    error: function(xhr) {
                                        hideLoader();
                                        console.log(xhr.responseText);
                                    }
                            });
                                console.log(selectedData.id);

                            }
                        },
                        @endif
                    ]
                }
            ],

            columns: [
                { data: 'id', name: 'id', title: 'ID', visible: false },
                { data: 'user_name', name: 'user_name', title: 'User Name' },
                { data: 'name', name: 'name', title: 'Name' },
                { data: 'email', name: 'email', title: 'Email' },
                { data: 'position', name: 'position', title: 'Position' },
                { data: 'province', name: 'province', title: 'Province' },
                { data: 'role', name: 'role', title: 'Role' },
                // { data: 'division_id', name: 'division_id', title: 'Division' },
                { data: 'status', name: 'status', title: 'Status' },
                { data: 'created_by', name: 'created_by', title: 'Created By' },
                { data: 'created_at', name: 'created_at', title: 'Created At' },
            ],

            language: {
                emptyTable: "No data found",
                search: "",
                searchPlaceholder: "Search..."
            },

            dom: '<"d-flex justify-content-between flex-wrap"B>rtip',
        });

        // END DATATABLES

        $('.navbar-toggler').on('click', function() {
        // Reload the DataTable
            table.ajax.reload(null, false); // false to keep the current paging
        });


        // table.buttons().container().appendTo('#users-table_wrapper .col-md-6:eq(0)');
        table.buttons().container().appendTo('#table-buttons');

        // $('#search-input').on('keyup', function() {
        //     table.ajax.reload();  // Reload the table when the search input changes
        // });

        $('#search-input').on('keyup change', function() {
            table.search(this.value).draw(); // Reload the table when the search input changes
        });

        table.on('select deselect', function() {
            var selectedRows = table.rows({ selected: true }).count();
            table.buttons(['.btn-warning', '.btn-info', '.btn-danger', '.btn-primary']).enable(selectedRows > 0);
        });

        $('#createUserModal, #editUserModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset(); // Reset form fields
            $(this).find('.is-invalid').removeClass('is-invalid'); // Remove validation error classes
            $(this).find('.invalid-feedback').text(''); // Clear error messages
        });


        // Handle input and select fields for create and edit user forms
        $('#createUserForm, #editUserForm').find('input, select').on('keyup change', function() {
            $(this).removeClass('is-invalid');

            // Remove [] from name attribute for error ID
            var errorId = $(this).attr('name').replace('[]', '') + 'Error';
            $('#' + errorId).text('');
        });

        // Handle Select2 fields specifically
        $('#createUserForm, #editUserForm').find('select[name="division_id[]"]').on('select2:select', function() {
            $(this).removeClass('is-invalid');

            // Remove [] from name attribute for error ID
            var errorId = $(this).attr('name').replace('[]', '') + 'Error';
            $('#' + errorId).text('');
        });



        function initializeDivisionSelect() {
            $('.division-select').select2({
                placeholder: 'Select an Option',
                allowClear: true,
                ajax: {
                    url: '{{ route('indicator.getDivision') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term // search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    id: item.id,
                                    text: item.division_name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        }

        initializeDivisionSelect();

        //  START FETCH ROLES FOR SELECT

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
