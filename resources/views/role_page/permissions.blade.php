@extends('components.app')

@section('content')
<div class="container">
    <form id="permissionsForm">
        @csrf
        <div class="row mt-4">
            <div class="col">
                <div class="form-group">
                    <label for="roleSelect">Select Role</label>
                    <select id="roleSelect" name="role_id" class="form-control">

                        @foreach($roles as $roleItem)
                            <option value="{{$roleItem->id}}"
                                {{ $role->id == $roleItem->id ? 'selected' : '' }}>
                                {{ $roleItem->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col mt-4">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save Permissions</button>
                    @if(Auth::user()->role->name === 'SuperAdmin')
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#basicModal">
                        Add Permissions
                      </button>
                      @endif
                </div>

            </div>
        </div>

        <div class="row mt-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll"> Select All
                        </th>
                        <th>Permission Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td>
                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->id }}">
                            </td>
                            <td>{{ $permission->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>


    {{-- MODAL FOR ADDING PERMISSIONS --}}
    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <form id="SavePermissionForm">
            <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel1">Create New Permission</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="row">
                    <div class="col mb-6">
                      <label for="name" class="form-label">Permission Name</label>
                      <input type="text" id="name" name="name" class="form-control" placeholder="Enter Name">
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
              </div>
        </form>
        </div>
      </div>
</div>
@endsection

@section('components.specific_page_scripts')
<script>
    $(document).ready(function() {
        // Function to fetch permissions for the selected role
        function fetchPermissions(roleId) {
            $.ajax({
                url: '{{ route("roles.permissions.fetch") }}',
                method: 'GET',
                data: { role_id: roleId },
                success: function(response) {
                    if (response.success) {
                        $('.permission-checkbox').prop('checked', false);
                        response.permissions.forEach(function(permissionId) {
                            $('input.permission-checkbox[value="' + permissionId + '"]').prop('checked', true);
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while fetching permissions. Please try again.',
                        showConfirmButton: true,
                    });
                }
            });
        }

        // Fetch permissions when a role is selected
        $('#roleSelect').change(function() {
            var selectedRoleId = $(this).val();
            fetchPermissions(selectedRoleId);
        });

        // Initial fetch of permissions based on the selected role
        fetchPermissions($('#roleSelect').val());

        // Handle form submission
        $('#permissionsForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: '{{ route("roles.permissions.update") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: true,
                        });
                    } else {
                        var errors = response.errors;
                        Object.keys(errors).forEach(function(key) {
                            var inputField = $('#permissionsForm [name=' + key + ']');
                            inputField.addClass('is-invalid');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errors[key][0],
                                showConfirmButton: true,
                            });
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while updating permissions. Please try again.',
                        showConfirmButton: true,
                    });
                }
            });
        });

        // Select/Deselect all checkboxes
        $('#selectAll').click(function() {
            $('.permission-checkbox').prop('checked', this.checked);
        });

        // Update "Select All" checkbox based on individual checkbox selection
        $('.permission-checkbox').change(function() {
            if ($('.permission-checkbox:checked').length === $('.permission-checkbox').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });


        // Handle Saving Permission form submission
        $('#SavePermissionForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            showLoader();
            $.ajax({
                url: '{{ route("roles.permissions.store") }}',
                method: 'POST',
                data: {
                name: $('#name').val(),
                _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#basicModal').modal('hide');
                        hideLoader();
                        window.location.href = '/permissions';
                    if (response.success) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: true,
                        });
                    } else {
                        var errors = response.errors;
                        Object.keys(errors).forEach(function(key) {
                            var inputField = $('#SavePermissionForm [name=' + key + ']');
                            inputField.addClass('is-invalid');
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errors[key][0],
                                showConfirmButton: true,
                            });
                        });
                    }
                },
                error: function(xhr) {
                    $('#basicModal').modal('hide');
                        hideLoader();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while updating permissions. Please try again.',
                        showConfirmButton: true,
                    });
                }
            });
        });
    });
</script>
@endsection
