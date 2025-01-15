<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" >
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="firstName" class="required">First Name</label>
                                <input type="text" class="form-control capitalize" name="first_name" id="firstName" aria-describedby="">
                                <div class="invalid-feedback" id="first_nameError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="lastName" class="required">Last Name</label>
                                <input type="text" class="form-control capitalize" id="lastName" name="last_name" aria-describedby="">
                                <div class="invalid-feedback" id="last_nameError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="middleName" class="required">Middle Name</label>
                                <input type="text" class="form-control capitalize" id="middleName" name="middle_name">
                                <div class="invalid-feedback" id="middle_nameError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="suffix">Suffix</label>
                                <input type="text" class="form-control capitalize" id="suffix" name="suffix" aria-describedby="">
                            </div>

                            <div class="form-group mb-3">
                                <label for="rolesDropdown" class="required">Role</label>
                                <select id="rolesDropdown" class="form-select" name="role_id">
                                    <option value="">Select a Role...</option>
                                </select>
                                <div class="invalid-feedback" id="role_idError"></div>
                            </div>


                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="required">Email</label>
                                <input type="text" class="form-control" id="email" aria-describedby="" name="email" placeholder="@gmail.com">
                                <div class="invalid-feedback" id="emailError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="contactNumber" class="required">Mobile Number</label>
                                <input type="text" class="form-control" id="contactNumber" name="mobile_number" placeholder="09+">
                                <div class="invalid-feedback" id="mobile_numberError"></div>
                            </div>
                            <div class="form-group mb-3" style="">
                                <label for="position" class="required">Position</label>
                                <input type="text" class="form-control capitalize" id="position" name="position" aria-describedby="">
                                <div class="invalid-feedback" id="positionError"></div>
                            </div>
                            <div class="form-group mb-3" style="">
                                <label for="province" class="required">Province</label>
                                <select class="form-select capitalize" id="province" name="province">
                                    <option value="">Select a Province...</option>
                                    <option value="Albay">Albay</option>
                                    <option value="Camarines Norte">Camarines Norte</option>
                                    <option value="Camarines Sur">Camarines Sur</option>
                                    <option value="Catanduanes">Catanduanes</option>
                                    <option value="Masbate">Masbate</option>
                                    <option value="Sorsogon">Sorsogon</option>
                                </select>
                                <div class="invalid-feedback" id="provinceError"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="create_division_id" class="required">Division</label>
                                <select id="create_division_id" class="division-select form-select" name="division_id[]" multiple="multiple">
                                </select>
                                <div class="invalid-feedback" id="division_idError "></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
