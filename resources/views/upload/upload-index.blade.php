@extends('components.app')

@section('content')

<!-- File Modal -->
<div class="modal fade" id="fileModal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileModalLabel">File Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- File preview area -->
                <iframe id="filePreview" src="" style="width: 100%; height: 500px;" frameborder="0"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col">
        <div class="card">
            <div class="card-body">
              <h4 class="card-title">Upload</h4>
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

{{-- Nav Tabs and Datatable Container --}}
<div class="col-md">
    <div class="nav-align-top">
        
        <ul class="nav nav-pills nav-fill d-flex flex-nowrap overflow-auto mb-4 mt-4 gap-2" id="ul-scroll" role="tablist">
           {{-- nav-item --}}
        </ul>

        <div class="tab-content mt-2">
            <div class="tab-pane fade show active" id="navs-pills-justified" role="tabpanel">
                <p class="text-secondary" id="office-title"></p>
                <div class="table-responsive pt-3">
                    <table id="uploaded-table"  class="table table-striped" style="width: 100%">
                      <tbody>
                      </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
</div>

{{-- include --}}
@include('upload.create')
@include('upload.edit')

@endsection

@section('components.specific_page_scripts')

<script>

    $(document).ready(function() {

        let selectedStartDate, selectedEndDate, selectedYear;

        function navitem() {
            $.ajax({
                url: '{{ route('categories.get') }}',
                method: 'GET',
                success: function (categories) {
                    const ulScroll = document.getElementById('ul-scroll');
                    ulScroll.innerHTML = ''; // Clear existing items to avoid duplicates

                    const colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8'];

                    categories.forEach((category, index) => {
                        const li = document.createElement('li');
                        li.classList.add('nav-item');

                        const button = document.createElement('button');
                        button.type = 'button';
                        button.classList.add('btn', 'office-btn');
                        button.style.color = '#007bff';
                        button.dataset.route = '{{ route('upload.list') }}';
                        button.role = 'tab';
                        button.ariaSelected = index === 0 ? 'true' : 'false';
                        button.textContent = `${selectedYear || new Date().getFullYear()} ${category.category_name}`;

                        button.style.color = colors[index % colors.length]; // Loop through colors


                        if (index === 0) {
                            button.classList.add('active');
                        }

                        li.appendChild(button);
                        ulScroll.appendChild(li);
                    });

                    // Bind click event after dynamically adding buttons
                    bindOfficeButtonEvents();

                     // Trigger the first category button if available
                    const firstButton = ulScroll.querySelector('.office-btn');
                    if (firstButton) {
                        firstButton.click();
                    }
                }
            });
        }

        function bindOfficeButtonEvents() {
            // Handle button clicks for dynamic routes
            $('.office-btn').off('click').on('click', function (e) {
                e.preventDefault();

                $(".office-btn").removeClass("active");
                $(this).addClass("active");

                const officeTitle = $(this).text().trim().replace(/[^a-zA-Z\s]/g, '').trim();
                const route = $(this).data('route');

                // Update table data
                table.ajax.url(route).load();

                // Update office title
                $('#office-title').text(officeTitle);
            });
        }

        // Call navitem to initialize
        navitem();

        // Update the currently active button's title
        const activeButton = $(".office-btn.active").text().trim().replace(/[^a-zA-Z\s]/g, '').trim();
        $('#office-title').text(activeButton);

        $.ajax({
            url: '{{ route('upload.getCategoryUploads') }}',
            type: 'GET',
            success: function(data) {
                var catDropdown = $('.category-select');
                var editCatDropdown = $('.edit-category-select');
                data.forEach(function(item) {
                    catDropdown.append('<option value="' + item.id + '">' + item.category_name + '</option>');
                    editCatDropdown.append('<option value="' + item.id + '">' + item.category_name + '</option>');

                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching roles:', error);
            }
        });

        var table;

        flatpickr("#date-range-picker", {
            mode: "range",
            dateFormat: "m/d/Y",
            onChange: function(selectedDates, dateStr, instance) {
                // Check if both start and end dates are selected
                if (selectedDates.length === 2) {
                    const startDate = selectedDates[0];
                    const endDate = selectedDates[1];
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

                        // Update the month and year display
                        const startMonth = startDate.toLocaleString('default', { month: 'short' });
                        const endMonth = endDate.toLocaleString('default', { month: 'short' });
                        const startYear = startDate.getFullYear();
                        const endYear = endDate.getFullYear();

                        if (startYear === endYear) {
                            selectedYear = startYear;
                            console.log(selectedYear);
                        } else {
                            selectedYear = `${startYear} - ${endYear}`;
                        }
                         // Update nav items with the selected year
                        navitem();
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
                    selectedYear = new Date().getFullYear(); // Reset to current year
                    navitem(); // Update nav items with the current year
                });
            }
        });

        table = $('#uploaded-table').DataTable({
            responsive: true,
            processing: false,
            serverSide: true,
            pageLength: 10,
            paging: true,
            lengthChange: false,
            ordering: false,
            search: true,
            scrollY: 400,
            select: {
                style: 'single'
            },
            ajax: {
                url: '{{ route('upload.list') }}',
                data: function(d) {
                    // Include the date range in the AJAX request
                    d.date_range = $('#date-range-picker').val();
                    d.upload_category = $(".office-btn.active").text().trim().replace(/[^a-zA-Z\s]/g, '').trim();
                    
                },
            },
            buttons: [
                {
                    text: 'Add',
                    className: 'btn btn-success user_btn',
                    action: function (e, dt, node, config) {
                        $('#uploadModal').modal('show');

                        $('#submitUploadForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            showLoader();

                            var formData = new FormData(this);

                            // Re-disable the select field if needed

                            $.ajax({
                                url: '{{ route('upload.store') }}',
                                type: 'POST',
                                data: formData,
                                contentType: false,
                                processData: false,
                                success: function(response) {
                                    hideLoader();
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: response.message,
                                            showConfirmButton: true,
                                        });

                                        $('#submitUploadForm')[0].reset(); // Reset the form
                                        $('#uploadModal').modal('hide'); // Hide the modal
                                        table.ajax.reload();
                                    }
                                },
                                error: function(xhr) {
                                    hideLoader();

                                    if (xhr.status === 422) {
                                        const errors = xhr.responseJSON.errors;

                                        // Clear previous errors and remove red borders
                                        $('.form-control').removeClass('is-invalid');
                                        $('.invalid-feedback').html('').hide();

                                        // Loop through the errors and update fields
                                        for (let key in errors) {
                                            if (errors.hasOwnProperty(key)) {
                                                const keyParts = key.split('.');
                                                let fieldName = keyParts[0];

                                                if (keyParts.length > 1) {
                                                    const index = keyParts[1];
                                                    fieldName = `${fieldName}_${index}`;
                                                }

                                                // Apply red border and show error message
                                                $(`#${fieldName}`).addClass('is-invalid');
                                                $(`#${fieldName}Error`).html(errors[key].join('<br>')).show();
                                            }
                                        }

                                        // Show SweetAlert with a summary of errors
                                        let errorMessages = '';
                                        for (let key in errors) {
                                            if (errors.hasOwnProperty(key)) {
                                                errorMessages += `<strong>${key}</strong>: ${errors[key].join('<br>')}<br>`;
                                            }
                                        }

                                        if (errorMessages) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Validation Errors!',
                                                showConfirmButton: true,
                                            });
                                        }
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oh no!',
                                            text: 'Something went wrong.',
                                            showConfirmButton: true,
                                        });
                                    }
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
                        $('#editUploadModal').modal('show');

                        var selectedData = dt.row({ selected: true }).data();
                        console.log(selectedData.category_id);
                        $('#edit_id').val(selectedData.id);
                        $('#edit_category').val(selectedData.category_id).change();
                       
                        $('#updateUploadForm').off('submit').on('submit', function(e) {
                            e.preventDefault();
                            showLoader();

                            var formData = new FormData(this);

                            // Re-disable the select field if needed
                            $.ajax({
                                url: '{{ route('upload.update') }}',
                                type: 'POST',
                                data: formData,
                                contentType: false,
                                processData: false,
                                success: function(response) {
                                    hideLoader();
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: response.message,
                                            showConfirmButton: true,
                                        });

                                        $('#updateUploadForm')[0].reset(); // Reset the form
                                        $('#editUploadModal').modal('hide'); // Hide the modal
                                        table.ajax.reload();
                                    }
                                },
                                error: function(xhr) {
                                    hideLoader();

                                    if (xhr.status === 422) {
                                        const errors = xhr.responseJSON.errors;

                                        // Clear previous errors and remove red borders
                                        $('.form-control').removeClass('is-invalid');
                                        $('.invalid-feedback').html('').hide();

                                        // Loop through the errors and update fields
                                        for (let key in errors) {
                                            if (errors.hasOwnProperty(key)) {
                                                const keyParts = key.split('.');
                                                let fieldName = keyParts[0];

                                                if (keyParts.length > 1) {
                                                    const index = keyParts[1];
                                                    fieldName = `${fieldName}_${index}`;
                                                }

                                                // Apply red border and show error message
                                                $(`#${fieldName}`).addClass('is-invalid');
                                                $(`#${fieldName}Error`).html(errors[key].join('<br>')).show();
                                            }
                                        }

                                        // Show SweetAlert with a summary of errors
                                        let errorMessages = '';
                                        for (let key in errors) {
                                            if (errors.hasOwnProperty(key)) {
                                                errorMessages += `<strong>${key}</strong>: ${errors[key].join('<br>')}<br>`;
                                            }
                                        }

                                        if (errorMessages) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Validation Errors!',
                                                showConfirmButton: true,
                                            });
                                        }
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oh no!',
                                            text: 'Something went wrong.',
                                            showConfirmButton: true,
                                        });
                                    }
                                }

                            });
                        });
                    }
                },
               @if( Auth::user()->role->name === 'SuperAdmin')
                {
                    text: 'Delete',
                    className: 'btn btn-danger user_btn',
                    enabled: false,
                    action: function (e, dt, node, config) {
                        //alert('Delete Activated!');

                        var selectedId = table.row({ selected: true }).data().id; // Assuming you have selected a user row

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
                                    url: '{{ route('upload.destroy') }}',
                                    method: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        id: selectedId
                                    },
                                    success: function(response) {
                                        hideLoader();
                                       
                                            Swal.fire(
                                                'Deleted!',
                                                'User has been deleted.',
                                                'success'
                                            );
                                            table.ajax.reload();
                                       
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
                @if( Auth::user()->role->name === 'SuperAdmin')
                {
                    text: 'Upload Logs',
                    className: 'btn btn-warning user_btn',
                    action: function (e, dt, node, config) {
                        window.location.href = '{{ route('upload.uploadLogs') }}';
                    }
                }
                @endif
            ],

            columns: [
                { data: 'id', name: 'id', title: 'ID', visible: false },
                { data: 'content', name: 'content', title: 'Content', visible: false },
                { data: 'code', name: 'code', title: 'File Code' },

                {
                    data: 'file',
                    name: 'file',
                    title: 'File',
                    render: function(data, type, row) {
                        if (data) {
    
                            return `
                                <a href="#" class="file"
                                data-file="${row.content}"
                                data-name="${data}"
                                data-id = "${row.id}">
                                    <i class="bx bx-file"></i> ${data};
                                </a>`

                            
                           ;
                        } else {
                            return 'No File';
                        }
                    },
                    orderable: false,
                    searchable: false
                },         
                { data: 'upload_category_id', name: 'upload_category_id', title: 'Category' },
                { data: 'category_id', name: 'category_id', title: 'Category', visible: false, },
                { data: 'month', name: 'month', title: 'Month' },
                { data: 'created_by', name: 'created_by', title: 'Created By' },
                { data: 'updated_by', name: 'updated_by', title: 'Updated By' },
                { data: 'created_at', name: 'created_at', title: 'Created At' },
                { data: 'updated_at', name: 'updated_at', title: 'Updated At' },
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


        $('#search-input').on('keyup change', function() {
            table.search(this.value).draw(); // Reload the table when the search input changes
        });

        table.buttons().container().appendTo('#roles-table_wrapper .col-md-6:eq(0)');
        table.buttons().container().appendTo('#table-buttons');

        table.on('select deselect', function() {
            var selectedRows = table.rows({ selected: true }).count();
            table.buttons(['.btn-info', '.btn-danger']).enable(selectedRows > 0);
        });

        $('#uploadModal, #editRoleModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset(); // Reset form fields
            $(this).find('.is-invalid').removeClass('is-invalid'); // Remove validation error classes
            $(this).find('.invalid-feedback').text(''); // Clear error messages
        });


        $(document).on('click', '.file', function(e) {
            var name = $(this).data('name')
            var file = $(this).data('file')
            var id = $(this).data('id')
            
            if (name.endsWith('.pdf')) {

                e.preventDefault();
                var base64File = $(this).data('file');
                var fileUrl = 'data:application/pdf;base64,' + base64File;
                $('#filePreview').attr('src', fileUrl);
                $('#fileModal').modal('show');
               
                } else {

                    e.preventDefault();
                    var downloadUrl = `/upload/download/${id}`;
                    
                    // Create a temporary anchor element
                    var link = document.createElement('a');
                    link.href = downloadUrl;
                    link.target = '_blank'; // Open in a new tab/window for safety
                    link.download = name;  // Suggest a filename for download
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link); // Clean up the element
            
                }
        });

    });


</script>

@endsection