@extends('components.app')

@section('content')
<div class="row mt-4">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div>
                </div>
                <h4 class="card-title">Organizational Outcome/PAP </h4>
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
                            <div class="input-group me-3">
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
                    <table id="org-table"  class="table table-striped" style="width: 100%">
                    <tbody>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
  @include('outcome.create')
  @include('outcome.edit')
  @include('outcome.view')


@endsection

{{-- JS of Pages --}}
@section('components.specific_page_scripts')

<script>

    $(document).ready(function() {

        $('#addOutcomeBtn').click(function () {
            const newOutcomeHtml = `
                <div id="organizational_outcome_group_${outcomeIndex}">
                    <div class="form-group mt-3" >
                        <label for="order_${outcomeIndex}" class="required">Order</label>
                        <input type="text" class="form-control capitalize" name="order[]" id="order_${outcomeIndex}" aria-describedby="">
                        <div class="invalid-feedback" id="orderError_${outcomeIndex}"></div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="organizational_outcome_${outcomeIndex}" class="required">Organization Outcome</label>
                        <input type="text" class="form-control capitalize" name="organizational_outcome[]" id="organizational_outcome_${outcomeIndex}" aria-describedby="">
                        <div class="invalid-feedback" id="organizational_outcomeError_${outcomeIndex}"></div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="category_${outcomeIndex}" class="required">Category</label>
                        <select class="form-control capitalize" name="category[]" id="category_${outcomeIndex}" >
                            <option value="Core">Core</option>
                            <option value="Non Core">Non Core</option>
                        </select>
                        <div class="invalid-feedback" id="category_${outcomeIndex}"></div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm mt-2 removeOutcomeBtn" data-index="${outcomeIndex}"><i class='bx bx-trash'></i></button>
                </div>
            `;
            $('#organizational_outcomes').append(newOutcomeHtml);
            outcomeIndex++;
        });

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

        table = $('#org-table').DataTable({
            responsive: true,
            processing: false,
            serverSide: true,
            pageLength: 10,
            lengthChange: false,
            paging: true,
            ordering: false,
            search: true,
            scrollY: 400,
            select: {
                style: 'single'
            },
            ajax: {
                url: '{{ route('org.list') }}',
                data: function(d) {
                    // Include the date range in the AJAX request
                    d.date_range = $('#date-range-picker').val();
                    // d.search = $('#search-input').val();
                },
               
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
                        $('#createOrgModal').modal('show');

                        $('#createOrgForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            showLoader();
                            // Handle form submission, e.g., via AJAX
                            var formData = $(this).serialize();
                            $.ajax({
                                url: '{{ route('org.store') }}',
                                method: 'POST',
                                data: formData,
                                success: function(response) {
                                    if (response.success) {
                                        $('#createOrgModal').modal('hide');
                                        hideLoader();
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: response.message,
                                                showConfirmButton: true,
                                            })
                                            table.ajax.reload();

                                             // Reset the form and remove repeated inputs
                                            $('#createOrgForm')[0].reset(); // Reset form fields
                                            $('#organizational_outcomes').empty(); // Clear repeated inputs
                                            outcomeIndex = 1; // Reset outcome index
                                        }
                                        else{
                                            hideLoader();
                                            const errors = response.errors;
                                            $('.invalid-feedback').html(''); // Clear any previous error messages
                                            $('.is-invalid').removeClass('is-invalid');
                                            for (let key in errors) {
                                                const keyParts = key.split('.');
                                                if (keyParts.length > 1) {
                                                    const index = keyParts[1];
                                                    const errorKey = keyParts[0];
                                                    $(`#${errorKey}_${index}`).addClass('is-invalid');
                                                    $(`#${errorKey}Error_${index}`).html(errors[key][0]).show();
                                                } else {
                                                    $(`#${key}`).addClass('is-invalid');
                                                    $(`#${key}Error`).html(errors[key][0]).show();
                                                }
                                            }
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Validation Error',
                                                html: 'Please fill out the required fields with asterisk',

                                            });
                                        }
                                },
                                error: function(xhr) {
                                    hideLoader();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oh no!',
                                        text: 'Something went wrong.',
                                        showConfirmButton: true,
                                    });
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
                        $('#editOrgModal').modal('show');

                        var selectedData = dt.row({ selected: true }).data();
                        $('#edit_role_id').val(selectedData.id);
                        $('#edit_organizational_outcome').val(selectedData.organizational_outcome);
                        $('#edit_status').val(selectedData.status);
                        $('#edit_order').val(selectedData.order);
                        $('#edit_category').val(selectedData.category);

                        $('#editOrgForm').off('submit').on('submit', function(e) {
                                e.preventDefault();
                                showLoader();
                                var formData = $(this).serialize();
                                $.ajax({
                                    url: '{{ route('org.update') }}',
                                    method: 'POST',
                                    data: formData,
                                    success: function(response) {
                                        if (response.success) {
                                            $('#editOrgModal').modal('hide');
                                            hideLoader();
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: response.message,
                                                showConfirmButton: true,
                                            })

                                            table.ajax.reload();

                                        } else {
                                            hideLoader();
                                            const errors = response.errors;
                                            $('.invalid-feedback').html(''); // Clear any previous error messages
                                            $('.is-invalid').removeClass('is-invalid');
                                            for (let key in errors) {
                                                const keyParts = key.split('.');
                                                if (keyParts.length > 1) {
                                                    const index = keyParts[1];
                                                    const errorKey = keyParts[0];
                                                    $(`#edit_${errorKey}`).addClass('is-invalid');
                                                    $(`#${errorKey}Error`).html(errors[key][0]).show();
                                                } else {
                                                    $(`#edit_${key}`).addClass('is-invalid');
                                                    $(`#${key}Error`).html(errors[key][0]).show();
                                                }
                                            }
                                            // var errorMessage = '';
                                            // $.each(response.errors, function(index, value) {
                                            //     errorMessage += value + '<br>';
                                            // });
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Validation Error',
                                                html: errorMessage,
                                            });
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
                        $('#view_role_id').val(selectedData.id);
                        $('#view_name').val(selectedData.organizational_outcome);
                        $('#view_status').val(selectedData.status);
                        $('#viewOrgModal').modal('show');
                        $('#view_order').val(selectedData.order);
                        $('#view_category').val(selectedData.category);


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
                                // showLoader();
                                $.ajax({
                                    url: '{{ route('org.destroy') }}',
                                    method: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        id: selectedUserId
                                    },
                                    success: function(response) {
                                        // hideLoader();
                                        if (response.success) {
                                            Swal.fire(
                                                'Deleted!',
                                                'Organization Outcome has been deleted.',
                                                'success'
                                            );
                                            table.ajax.reload();
                                        } else {
                                            var errorMessage = '';
                                            Object.keys(response.errors).forEach(function(key) {
                                                errorMessage += response.errors[key][0] + '<br>';
                                            });
                                            // hideLoader();
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Deletion Failed',
                                                html: response.errors
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


            ],

            columns: [
                { data: 'id', name: 'id', title: 'ID', visible: false },
                { data: 'order', name: 'order', title: 'Order' },
                { data: 'organizational_outcome', name: 'organizational_outcome', title: 'Organizational Outcome' },
                { data: 'category', name: 'category', title: 'Category' },
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

        let outcomeIndex = 1;
       
        $(document).on('click', '.removeOutcomeBtn', function () {
            const index = $(this).data('index');
            $(`#organizational_outcome_group_${index}`).remove();
        });

        $('#createOrgModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset(); // Reset form fields
            $(this).find('.is-invalid').removeClass('is-invalid'); // Remove validation error classes
            $(this).find('.invalid-feedback').text(''); // Clear error messages
            outcomeIndex = 1; // Reset outcome index
         
        });
     

    });
</script>

@endsection

